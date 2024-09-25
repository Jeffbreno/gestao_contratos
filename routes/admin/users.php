<?php

use App\Http\Response;
use App\Controller\Admin;

//ROTA DA LISTAGEM DE USUÁRIOS
$obRouter->get('/users', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\UsersController::getUsers($request));
    }
]);

//ROTA DE CADASTRO DE USUÁRIO
$obRouter->get('/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\UsersController::getNewUsers($request));
    }
]);

//ROTA DE CADASTRO DE USUÁRIO
$obRouter->post('/users/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\UsersController::setNewUsers($request));
    }
]);

//ROTA DE EDIÇÃO DE USUÁRIO
$obRouter->get('/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\UsersController::getEditUsers($request, $id));
    }
]);


//ROTA DE EDIÇÃO DE USUÁRIO
$obRouter->post('/users/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\UsersController::setEditUsers($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE USUÁRIO
$obRouter->get('/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\UsersController::getDeleteUsers($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE USUÁRIO
$obRouter->post('/users/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\UsersController::setDeleteUsers($request, $id));
    }
]);
