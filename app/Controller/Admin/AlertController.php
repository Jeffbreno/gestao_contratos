<?php

namespace App\Controller\Admin;

use App\Utils\View;

class AlertController
{

    /**
     * Método responsável por retornar uma mensagem de sucesso
     *
     */
    public static function getSuccess($message): string
    {
        return View::render('pages/alert/status', [
            'tipo' => 'success',
            'message' => $message
        ]);
    }

    /**
     * Método responsável por retornar uma mensagem de erro
     *
     */
    public static function getError($message): string
    {
        return View::render('pages/alert/status', [
            'tipo' => 'danger',
            'message' => $message
        ]);
    }

    /**
     * Método responsável por retornar uma mensagem de erro
     *
     */
    public static function getWarning($message): string
    {
        return View::render('pages/alert/status', [
            'tipo' => 'warning',
            'message' => $message
        ]);
    }
}
