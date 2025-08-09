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
    $url = $request->query('url');

    if (! $url) {
        return '<form method="GET">
                  <label>Enter a PUBLIC URL to preview:</label><br>
                  <input type="text" name="url" style="width:360px;" placeholder="https://example.com">
                  <button type="submit">Preview</button>
                </form>';
    }

    if (! filter_var($url, FILTER_VALIDATE_URL)) {
        return response('Invalid URL.', 422);
    }
    $parts = parse_url($url);
    $scheme = $parts['scheme'] ?? '';
    $host = $parts['host'] ?? '';
    if (! in_array($scheme, ['http', 'https'], true) || ! $host) {
        return response('Only http/https allowed.', 422);
    }

    $ips = @gethostbynamel($host) ?: [@gethostbyname($host)];
    foreach ($ips as $ip) {
        if (! filter_var($ip, FILTER_VALIDATE_IP)) {
            return response('Blocked: invalid IP.', 403);
        }
        $isPublic = filter_var($ip, FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        if ($ip === '127.0.0.1' || $ip === '::1' || ! $isPublic) {
            return response('Blocked: non-public destination.', 403);
        }
    }

    try {
        $resp = Http::timeout(8)
            ->withHeaders(['User-Agent' => 'SafePreview/1.0'])
            ->withOptions(['allow_redirects' => false])
            ->get($url);

        $type = $resp->header('Content-Type') ?? '';
        if (! str_contains($type, 'text/html') && ! str_contains($type, 'image/')) {
            return response('Content type not allowed.', 415);
        }

        return '<h3>Safe Preview of '.e($url).':</h3><pre>'
            .e(substr($resp->body(), 0, 800))
            .'</pre>';
    } catch (\Throwable $e) {
        return response('Fetch failed: '.$e->getMessage(), 502);
    }
});
