<?php

namespace App\Utils;

class View
{

    /**
     * Variáveis padrões da View
     * @var array
     */
    private static $vars = [];

    /**
     * Métpdp responsável por definir os dados iniciais da classe
     * @param array $vars
     */
    public static function init($vars = [])
    {
        self::$vars = $vars;
    }

    /**
     * Método responsável por retornar o conteúdo de uma view
     * 
     */
    private static function getContentView($view): string
    {
        $file = __DIR__ . '/../../resources/view/' . $view . '.html';
        return file_exists($file) ? file_get_contents($file) : '';
    }

    /**
     * Método responsável por retornar o conteúdo renderizado de uma view
     * 
     */
    public static function render($view, $vars = []): string
    {
        //CONTEUDO DA VIEW
        $contentView = self::getContentView($view);

        //MERGE DE VARIÁVEIS DA VIEW
        $vars = array_merge(self::$vars, $vars);

        $keys = array_keys($vars);
        $keys = array_map(function ($item) {
            return '{{' . $item . '}}';
        }, $keys);

        //RETORNA O CONTEUDO REDENRIZADO
        return str_replace($keys, array_values($vars), $contentView);
    }
}
