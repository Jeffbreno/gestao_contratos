<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use App\Model\Entity\User;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth
{
    /**
     * Método reponsável por executar o middleware
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        #REALIZA A VALIDAÇÃO DO ACESSO VIA JWT
        $this->auth($request);

        #executa o próximo nível do middwleware
        return $next($request);
    }
    /**
     * Método responsável por validar o acesso via JWT
     * 
     */
    private function auth(Request $request): Request|bool
    {
        #VERIFICA O USUÁRIO RECEBIDO
        if ($obUser = $this->getJWTAuthUser($request)) {
            $request->user = $obUser;
            return true;
        }

        #Emite o erro de senha inválida
        throw new Exception("Acesso negado", 403);
    }
    /**
     * Método responsável por retornar uma instancia de usuário autenticado
     */
    private function getJWTAuthUser(Request $request): User|bool
    {
        #HEADERS
        $headers = $request->getHeaders();
        #TOKEN PURO EM JWT
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        try {
            $decoded = (array) JWT::decode($jwt, new Key($_ENV['JWT_KEY'], 'HS256'));
        } catch (Exception $e) {
            throw new Exception("Verificação de token falhou", 403);
        }

        #EMAIL
        $email = $decoded['email'] ?? '';

        #BUSCA USUÁRIO POR EMAIL
        $obUser = User::getByEmail($email);

        #RETORNA O USUÁRIO
        return $obUser instanceof User ? $obUser : false;
    }
}
