<?php

use App\Http\Response;
use App\Controller\Admin;

// ROTA DA LISTAGEM DE CONTRATOS
$obRouter->get('/contratos', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ContratosController::getContratos($request));
    }
]);

// ROTA DE CADASTRO DE CONTRATOS (FORMULÁRIO)
$obRouter->get('/contratos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ContratosController::getNewContrato($request));
    }
]);

// ROTA DE CADASTRO DE CONTRATOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/contratos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ContratosController::setNewContrato($request));
    }
]);

// ROTA DE EDIÇÃO DE CONTRATOS (FORMULÁRIO)
$obRouter->get('/contratos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ContratosController::getEditContrato($request, $id));
    }
]);

// ROTA DE EDIÇÃO DE CONTRATOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/contratos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ContratosController::setEditContrato($request, $id));
    }
]);

// ROTA DE EXCLUSÃO DE CONTRATOS (FORMULÁRIO)
$obRouter->get('/contratos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ContratosController::getDeleteContrato($request, $id));
    }
]);

// ROTA DE EXCLUSÃO DE CONTRATOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/contratos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ContratosController::setDeleteContrato($request, $id));
    }
]);
