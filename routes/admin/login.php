<?php

use App\Http\Response;
use App\Controller\Admin;

//ROTA LOGIN
$obRouter->get('/', [
    'middlewares' => [
        'required-admin-logout'
    ],
    function ($request) {
        return new Response(200, Admin\LoginController::getLogin($request));
    }
]);


//ROTA LOGIN (POST)
$obRouter->get('/login', [
    
]);

//ROTA LOGOUT
$obRouter->get('/logout', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\LoginController::setLogout($request));
    }
]);
