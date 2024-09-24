<?php

use App\Controller\Api;
use App\Http\Response;

//ROTA HOME
$obRouter->get('/api/v1', [
    'middlewares' => [
        'api'
    ],
    function ($request) {
        return new Response(200, Api\ApiController::getDetails($request), 'application/json');
    }
]);
