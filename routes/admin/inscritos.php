<?php

use App\Http\Response;
use App\Controller\Admin;

//ROTA DA LISTAGEM DE DEPOIMENTOS
$obRouter->get('/page/inscritos', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\InscricoesController::getInscrito($request));
    }
]);

//ROTA DA LISTAGEM DE DEPOIMENTOS
$obRouter->get('/page/inscritos/visualizar/{id}', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($id) {
        return new Response(200, Admin\InscricoesController::getModal($id));
    }
]);

//ROTA DE CADASTRO DE DEPOIMENTOS
$obRouter->get('/page/inscritos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\InscricoesController::getNewInscrito($request));
    }
]);

//ROTA DE CADASTRO DE DEPOIMENTOS
$obRouter->post('/page/inscritos/new', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\InscricoesController::setNewInscrito($request));
    }
]);

//ROTA DE EDIÇÃO DE DEPOIMENTOS
$obRouter->get('/page/inscritos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\InscricoesController::getEditInscrito($request, $id));
    }
]);


//ROTA DE EDIÇÃO DE DEPOIMENTOS
$obRouter->post('/page/inscritos/{id}/edit', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\InscricoesController::setEditInscrito($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS
$obRouter->get('/page/inscritos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\InscricoesController::getDeleteInscrito($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS
$obRouter->post('/page/inscritos/{id}/delete', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\InscricoesController::setDeleteInscrito($request, $id));
    }
]);

//ROTA DE EXCLUSÃO DE DEPOIMENTOS
$obRouter->get('/page/inscritos/{id}/pagamento', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request, $id) {
        return new Response(200, Admin\InscricoesController::setStatusPag($request, $id));
    }
]);
