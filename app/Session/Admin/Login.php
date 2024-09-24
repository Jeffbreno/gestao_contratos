<?php

namespace App\Session\Admin;

use App\Model\Entity\User;

class Login
{
    /**
     * Método responsável por iniciar a sessão
     *
     * @return void
     */
    private static function init(): void
    {
        #VERIFICA SE A SESSÃO NÂO ESTA ATIVA
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
    }
    /**
     * Método reponsável por criar o login do usuário
     *
     */
    public static function Login(User $obUser): bool
    {
        #INICIA A SESSÃO
        self::init();

        #DEFINE A SESSÃO DO USUÁRIO
        $_SESSION['admin']['usuario'] = [
            'id' => $obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];

        #SUCESSO
        return true;
    }

    /**
     * Método responsável por verificar se o usuário está logado
     *
     */
    public static function isLogged(): bool
    {
        #INICIA A SESSÃO
        self::init();

        #RETORNA A VERIFICAÇÃO
        return isset($_SESSION['admin']['usuario']['id']);
    }

    /**
     * Método reponsável executar o logout do usuário
     *
     */
    public static function Logout(): bool
    {
        #INICIA A SESSÃO
        self::init();

        #DESLOGA O SUSUARIO
        unset($_SESSION['admin']['usuario']);

        #SUCESSO
        return true;
    }
}
