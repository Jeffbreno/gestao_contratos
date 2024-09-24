<?php

use App\Http\Response;
use App\Controller\Admin;

//ROTA DA LISTAGEM DE DEPOIMENTOS
$obRouter->get('/bancos', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\BancosController::getBancos($request));
    }
]);

//ROTA DA LISTAGEM DE DEPOIMENTOS
$obRouter->get('/bancos/visualizar/{id}', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($id) {
        return new Response(200, Admin\BancosController::getModal($id));
    }
]);

//ROTA DE CADASTRO DE DEPOIMENTOS
$obRouter->get('/bancos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\BancosController::getNewBanco($request));
    }
]);

//ROTA DE CADASTRO DE DEPOIMENTOS
$obRouter->post('/bancos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\BancosController::setNewBanco($request));
    }
]);

//ROTA DE EDIÇÃO DE DEPOIMENTOS
$obRouter->get('/bancos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\BancosController::getEditBanco($request, $id));
    }
]);


//ROTA DE EDIÇÃO DE DEPOIMENTOS
$obRouter->post('/bancos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\BancosController::setEditBanco($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS
$obRouter->get('/bancos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\BancosController::getDeleteBanco($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS
$obRouter->post('/bancos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\BancosController::setDeleteBanco($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS
$obRouter->get('/page/bancos/{id}/pagamento', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\BancosController::setStatusPag($request, $id));
    }
]);
