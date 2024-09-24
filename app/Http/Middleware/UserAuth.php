<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Model\Entity\User;
use Closure;
use Exception;

class UserAuth
{
    /**
     * Método reponsável por executar o middleware
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        #REALIZA A VALIDAÇÃO DO ACESSO VIA BASIC AUTH
        $this->basicAuth($request);

        #executa o próximo nível do middwleware
        return $next($request);
    }
    /**
     * Método responsável por validar o acesso via HTTP BASIC AUTJ
     * 
     */
    private function basicAuth(Request $request): Request|bool
    {
        #VERIFICA O USUÁRIO RECEBIDO
        if ($obUser = $this->authUser()) {
            $request->user = $obUser;
            return true;
        }

        #Emite o erro de senha inválida
        throw new Exception("Usuário ou senha inválidos", 403);
    }
    /**
     * Método responsável por retornar uma instancia de usuário autenticado
     */
    private function authUser(): User|bool
    {
        #VERIFICA A EXISTENCIA DOS DADOS DE ACESSO
        if (!isset($_SERVER['PHP_AUTH_USER']) or !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }

        #BUSCA USUÁRIO POR EMAIL
        $obUser = User::getByEmail($_SERVER['PHP_AUTH_USER']);

        #VERIFICA INSTANCIA
        if (!$obUser instanceof User) {
            return false;
        }

        #VALIDA SENHA E RETORNA USUÁRIO
        return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;
    }
}
