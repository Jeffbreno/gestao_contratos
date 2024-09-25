<?php

use App\Http\Response;
use App\Controller\Admin;

// ROTA DA LISTAGEM DE CONVÊNIOS
$obRouter->get('/convenios', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ConveniosController::getConvenios($request));
    }
]);


// ROTA DE CADASTRO DE CONVÊNIOS (FORMULÁRIO)
$obRouter->get('/convenios/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ConveniosController::getNewConvenio($request));
    }
]);

// ROTA DE CADASTRO DE CONVÊNIOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/convenios/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ConveniosController::setNewConvenio($request));
    }
]);

// ROTA DE EDIÇÃO DE CONVÊNIOS (FORMULÁRIO)
$obRouter->get('/convenios/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ConveniosController::getEditConvenio($request, $id));
    }
]);

// ROTA DE EDIÇÃO DE CONVÊNIOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/convenios/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ConveniosController::setEditConvenio($request, $id));
    }
]);

// ROTA DE EXCLUSÃO DE CONVÊNIOS (FORMULÁRIO)
$obRouter->get('/convenios/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ConveniosController::getDeleteConvenio($request, $id));
    }
]);

// ROTA DE EXCLUSÃO DE CONVÊNIOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/convenios/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ConveniosController::setDeleteConvenio($request, $id));
    }
]);
