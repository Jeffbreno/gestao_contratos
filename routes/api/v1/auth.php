<?php

use App\Controller\Api;
use App\Http\Response;

//
$obRouter->post('/api/v1/auth', [
    'middlewares' => [
        'api',
        'user-auth'
    ],
    function ($request) {
        return new Response(200, Api\AuthController::generateToken($request), 'application/json');
    }
]);
