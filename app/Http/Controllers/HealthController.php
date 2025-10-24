<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class HealthController extends Controller
{
    public function index(): JsonResponse
    {
        $checks = ['db' => false, 'redis' => false, 'openMeteo' => false];

        try { DB::select('SELECT 1'); $checks['db'] = true; } catch (\Throwable $e) {}
        try { Cache::put('meteora:ping', 'pong', 5); $checks['redis'] = Cache::get('meteora:ping') === 'pong'; } catch (\Throwable $e) {}
        try {
            $base = config('services.open_meteo.base', 'https://api.open-meteo.com/v1');
            $resp = Http::timeout(5)->get($base.'/forecast', [
                'latitude'=>0,'longitude'=>0,'current_weather'=>true
            ]);
            $checks['openMeteo'] = $resp->ok();
        } catch (\Throwable $e) {}

        return response()->json([
            'status' => ($checks['db'] && $checks['redis']),
            'checks' => $checks
        ]);
    }
}
