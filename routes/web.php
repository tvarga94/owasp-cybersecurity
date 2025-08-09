<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\SecurityReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/login', 'Auth\LoginController@login');
});

Route::get('/', function () {
    return view('welcome');
});

Route::post('/csp-report', [SecurityReportController::class, 'cspReport']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/admin/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
});

require __DIR__.'/auth.php';

Route::get('/preview-safe', function (Request $request) {
    if (app()->environment('local', 'testing')) {
        abort(403, 'Disabled on localhost for this demo.');
    }

    $url = $request->query('url');

    if (! $url) {
        return '<form method="GET">
                    <label>Enter a public URL to preview:</label><br>
                    <input type="text" name="url" style="width:360px;" placeholder="https://example.com">
                    <button type="submit">Preview</button>
                </form>';
    }

    if (! filter_var($url, FILTER_VALIDATE_URL)) {
        abort(422, 'Invalid URL.');
    }
    $parts = parse_url($url);
    if (! in_array(($parts['scheme'] ?? ''), ['http', 'https'], true)) {
        abort(422, 'Only http/https allowed.');
    }

    $host = $parts['host'] ?? '';
    $ips = [];
    foreach (dns_get_record($host, DNS_A + DNS_AAAA) ?: [] as $rec) {
        $ips[] = $rec['type'] === 'AAAA' ? $rec['ipv6'] : $rec['ip'];
    }

    if (! $ips) {
        $ips[] = gethostbyname($host);
    }

    $isPublic = function (string $ip): bool {
        if ($ip === '127.0.0.1' || $ip === '::1') {
            return false;
        }
        if (str_starts_with($ip, '10.') ||
            str_starts_with($ip, '192.168.') ||
            preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $ip) ||
            str_starts_with($ip, '169.254.') ||      // link-local
            str_starts_with(strtolower($ip), 'fe80:') // IPv6 link-local
        ) {
            return false;
        }

        return filter_var($ip, FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
    };

    foreach ($ips as $ip) {
        if (! $isPublic($ip)) {
            abort(403, 'Blocked: non-public destination.');
        }
    }

    try {
        $resp = Http::timeout(5)->withOptions([
            'allow_redirects' => ['max' => 3, 'track_redirects' => true],
        ])->get($url);

        $history = (array) $resp->header('X-Guzzle-Redirect-History', []);
        foreach ($history as $hUrl) {
            $hHost = parse_url($hUrl, PHP_URL_HOST);
            if (! $hHost) {
                abort(403, 'Blocked redirect: invalid host.');
            }
            $hIps = [];
            foreach (dns_get_record($hHost, DNS_A + DNS_AAAA) ?: [] as $rec) {
                $hIps[] = $rec['type'] === 'AAAA' ? $rec['ipv6'] : $rec['ip'];
            }
            if (! $hIps) {
                $hIps[] = gethostbyname($hHost);
            }
            foreach ($hIps as $ip) {
                if (! $isPublic($ip)) {
                    abort(403, 'Blocked: redirect to non-public host.');
                }
            }
        }

        $type = $resp->header('Content-Type') ?? '';
        if (! str_contains($type, 'text/html') && ! str_contains($type, 'image/')) {
            abort(415, 'Content type not allowed.');
        }

        return '<h3>Safe Preview of '.e($url).':</h3><pre>'
            .e(substr($resp->body(), 0, 800))
            .'</pre>';
    } catch (\Throwable $e) {
        abort(502, 'Fetch failed: '.$e->getMessage());
    }
});
