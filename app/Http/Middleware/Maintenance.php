<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Closure;
use Exception;

class Maintenance
{
    /**
     * Método reponsável por executar o middleware
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        //VERIFICA O ESTADO DE MANUTENÇÃO DA PÁGINA
        if ($_ENV['MAINTENANCE'] == 'true') {
            throw new Exception("Página em manutenção. Volte mais tarde", 200);
        }

        //EXECUTA O PRÓXIMO NÍVEL DO MIDDLEWARE
        return $next($request);
    }
}
