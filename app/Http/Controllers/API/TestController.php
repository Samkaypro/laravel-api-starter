<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Simple test method
     */
    public function index()
    {
        return response()->json([
            'message' => 'Test controller is working',
            'routes' => [
                'register' => '/api/v1/auth/register',
                'login' => '/api/v1/auth/login',
                'forgot_password' => '/api/v1/auth/forgot-password',
                'reset_password' => '/api/v1/auth/reset-password',
                'oauth_redirect' => '/api/v1/auth/{provider}/redirect',
                'oauth_callback' => '/api/v1/auth/{provider}/callback',
                'oauth_token' => '/api/v1/auth/{provider}/token',
            ]
        ]);
    }
}
