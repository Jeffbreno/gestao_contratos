<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\User;
use Exception;
use Firebase\JWT\JWT;

class AuthController extends ApiController
{

    /**
     * Método reponsável por gerar um token JWT
     */
    public static function generateToken(Request $request): array
    {
        $postVars = $request->getPostVars();

        #VALIDA OS CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['email']) or !isset($postVars['senha'])) {
            throw new Exception("Os campos 'email' e 'senha' são obrigatórios", 400);
        }

        #BUSCA USUÁRIO POR EMAIL
        $obUser = User::getByEmail($postVars['email']);
        if (
            !$obUser instanceof User or
            !password_verify($postVars['senha'], $obUser->senha)
        ) {
            throw new Exception("O usuário ou senha são inválidos", 400);
        }

        #PLAYOAD
        $playload = [
            'email' => $obUser->email
        ];

        $jwt = JWT::encode($playload, $_ENV['JWT_KEY'], 'HS256');

        #RETORNA O TOKEN GERADO
        return [
            'token' => $jwt
        ];
    }
}
