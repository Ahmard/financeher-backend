<?php

namespace App\Http\Controllers;

use App\Helpers\Http\Responder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    public function __construct(
        private readonly Responder $responder,
    )
    {
    }

    public function healthCheck(): JsonResponse
    {
        try {
            // Check if we can perform a simple query
            DB::select('SELECT 1');
            return $this->responder->successMessage('Bits and bytes flowing smoothly');

        } catch (Exception $e) {
            Log::error($e);
            return $this->responder->errorMessage('Service health check failed');
        }
    }

}
