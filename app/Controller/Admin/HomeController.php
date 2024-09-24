<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;

class HomeController extends PageController
{

    /**
     * Método responsável por renderizar a view de home do painel
     *
     */
    public static function getHome(Request $request): string
    {
        #CONTEÚDO DA HOME
        $content = View::render('pages/home/index', [
            'titulo' => 'Início',
            'welcome' => '<p>Seja bem-vindo(a) ao painel administrativo</p>
            <p>Utilize o menu a direita para navegar</p>'
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Home', $content, 'home');
    }
}
