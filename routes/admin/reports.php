<?php

use App\Http\Response;
use App\Controller\Admin;

// Rota para acessar o relatório de contratos
$obRouter->get('/reports', [
    'middlewares' => [
        'required-admin-login'
    ],
    function ($request) {
        return new Response(200, Admin\ReportController::getContractsReport($request));
    }
]);
