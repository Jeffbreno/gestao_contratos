<?php

use App\Controller\Api;
use App\Http\Response;

#ROTA HOME
$obRouter->get('/api/v1/users', [
    'middlewares' => [
        'api',
        'user-jwt-auth',
        'cache'
    ],
    function ($request) {
        return new Response(200, Api\UsersController::getUsers($request), 'application/json');
    }
]);

//ROTA DE CONSULTA DO USUÁRIO ATUAL
$obRouter->get('/api/v1/users/me', [
    'middlewares' => [
        'api',
        'user-jwt-auth'
    ],
    function ($request) {
        return new Response(200, Api\UsersController::getCurrentUser($request), 'application/json');
    }
]);

#ROTA RETORNA UM DEPOIMENTO ESPECIFICO
$obRouter->get('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'user-jwt-auth',
        'cache'
    ],
    function ($request, $id) {
        return new Response(200, Api\UsersController::getUser($request, $id), 'application/json');
    }
]);

#ROTA DE CADASTRO DE DEPOIMENTO
$obRouter->post('/api/v1/users', [
    'middlewares' => [
        'api',
        'user-jwt-auth'
    ],
    function ($request) {
        return new Response(201, Api\UsersController::setNewUser($request), 'application/json');
    }
]);

#ROTA DE ATUALIZAÇÃO DE DEPOIMENTO
$obRouter->put('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'user-jwt-auth'
    ],
    function ($request, $id) {
        return new Response(200, Api\UsersController::setEditUser($request, $id), 'application/json');
    }
]);

#ROTA DE EXCLUSÃO DE DEPOIMENTO
$obRouter->delete('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
        'user-jwt-auth'
    ],
    function ($request, $id) {
        return new Response(200, Api\UsersController::setDeleteUser($request, $id), 'application/json');
    }
]);
