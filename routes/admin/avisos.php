<?php

use App\Http\Response;
use App\Controller\Admin;

//ROTA DA LISTAGEM
$obRouter->get('/admin/avisos', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\AvisosController::getAviso($request));
    }
]);


//ROTA DE CADASTRO
$obRouter->get('/admin/avisos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\AvisosController::getNewAviso($request));
    }
]);

//ROTA DE CADASTRO
$obRouter->post('/admin/avisos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\AvisosController::setNewAviso($request));
    }
]);


//ROTA DE EXCLUSÃO
$obRouter->get('/admin/avisos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\AvisosController::getDeleteAviso($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DO AVISO
$obRouter->post('/admin/avisos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\AvisosController::setDeleteAviso($request, $id));
    }
]);

