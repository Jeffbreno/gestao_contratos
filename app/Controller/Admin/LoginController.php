<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Model\Entity\User;
use App\Utils\View;
use App\Session\Admin\Login as SessionAdminLogin;

class LoginController extends PageController
{
    /**
     * Método responsável por retornar a renderização da página de login
     *
     */
    public static function getLogin(Request $request, string $errorMessage = null): string
    {
        #STATUS
        $status = !is_null($errorMessage) ? AlertController::getError($errorMessage) : '';

        #CONTEÚDO DA PÁGINA DE LOGIN
        $content = View::render('pages/login/index', [
            'status' => $status,
            'script' => '<script src="' . $_ENV['URL'] . '/resources/assets/js/validate.js"></script>'
        ]);
        #RETORNA A PÁGINA COMPLETA
        return parent::getPage('Login | Painel', $content);
    }

    /**
     * Método responsável por definir o login do usuário
     *
     */
    public static function setLogin(Request $request)
    {
        #POST VARS
        $postVars = $request->getPostVars();
        $login = $postVars['login'] ?? '';
        $senha = $postVars['senha'] ?? '';

        #BUSCA USUÁRIO POR EMAIL
        $obUser = User::getByLogin($login);

        if (!$obUser instanceof User) {
            return self::getLogin($request, 'E-mail ou senha inválidos');
        } else {
            #VERIFICA SENHA DESTE USUÁRIO
            if (!password_verify($senha, $obUser->senha))
                return self::getLogin($request, 'E-mail ou senha inválidos');
            else
                #CRIAR SESSION DE LOGIN
                SessionAdminLogin::Login($obUser);

            #REDIRECIONA O USUÁRIO PARA HOME DO ADMIN
            $request->getRouter()->redirect('/home');
        }
    }

    /**
     * Método responsável por deslogar o usuário
     *
     */
    public static function setLogout(Request $request): void
    {
        #DESTROI A SESSÃO DE LOGIN
        SessionAdminLogin::Logout();

        #REDIRECIONA O USUÁRIO PARA TELA DE LOGIN
        $request->getRouter()->redirect('/');
    }
}
