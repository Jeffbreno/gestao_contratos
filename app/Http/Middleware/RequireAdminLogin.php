<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Session\Admin\Login as SessionAdminLogin;
use Closure;

class RequireAdminLogin
{

    /**
     * Método reponsável por executar o middleware
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        #VERIFICA SE O USUÁRIO ESTÁ LOGADO
        if (!SessionAdminLogin::isLogged()) {
            $request->getRouter()->redirect('/');
        }
        #CONTINUA A EXECUÇÃO
        return $next($request);
    }
}
