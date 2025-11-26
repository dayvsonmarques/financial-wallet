<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check()
    {
        try {
            // Verificar conexão com banco de dados
            DB::connection()->getPdo();
            
            return response()->json([
                'status' => 'healthy',
                'database' => 'connected',
                'timestamp' => now()->toIso8601String()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'database' => 'disconnected',
                'error' => $e->getMessage(),
                'timestamp' => now()->toIso8601String()
            ], 503);
        }
    }
}
