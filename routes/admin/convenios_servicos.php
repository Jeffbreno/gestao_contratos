<?php

use App\Http\Response;
use App\Controller\Admin;

// ROTA DA LISTAGEM DE SERVIÇOS
$obRouter->get('/convenios-servicos', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ConveniosServicosController::getConveniosServicos($request));
    }
]);

// ROTA DA VISUALIZAÇÃO DE UM SERVIÇO ESPECÍFICO
$obRouter->get('/convenios-servicos/visualizar/{id}', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($id) {
        return new Response(200, Admin\ConveniosServicosController::getModal($id)); // Certifique-se de implementar o método getModal se necessário
    }
]);

// ROTA DE CADASTRO DE SERVIÇOS (FORMULÁRIO)
$obRouter->get('/convenios-servicos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ConveniosServicosController::getNewConvenioServico($request));
    }
]);

// ROTA DE CADASTRO DE SERVIÇOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/convenios-servicos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ConveniosServicosController::setNewConvenioServico($request));
    }
]);

// ROTA DE EDIÇÃO DE SERVIÇOS (FORMULÁRIO)
$obRouter->get('/convenios-servicos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ConveniosServicosController::getEditConvenioServico($request, $id));
    }
]);

// ROTA DE EDIÇÃO DE SERVIÇOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/convenios-servicos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ConveniosServicosController::setEditConvenioServico($request, $id));
    }
]);

// ROTA DE EXCLUSÃO DE SERVIÇOS (FORMULÁRIO)
$obRouter->get('/convenios-servicos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ConveniosServicosController::getDeleteConvenioServico($request, $id));
    }
]);

// ROTA DE EXCLUSÃO DE SERVIÇOS (ENVIO DO FORMULÁRIO)
$obRouter->post('/convenios-servicos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\ConveniosServicosController::setDeleteConvenioServico($request, $id));
    }
]);
