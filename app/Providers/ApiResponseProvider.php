<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class ApiResponseProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Response::macro('invalid', function($violations) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Body input is not valid',
                'violations' => $violations
            ], 400);
        });
    }
}
