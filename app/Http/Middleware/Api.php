<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Closure;

class Api
{
    /**
     * Método reponsável por executar o middleware
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        #ALtera o content type para json
        $request->getRouter()->setContentType('application/json');

        #executa o próximo nível do middwleware
        return $next($request);
    }
}
