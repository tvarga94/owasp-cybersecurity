<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SecurityReportController extends Controller
{
    public function cspReport(Request $request): JsonResponse
    {
        Log::warning('CSP Violation:', [
            'report' => $request->getContent(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json(['status' => 'received'], 204);
    }
}
