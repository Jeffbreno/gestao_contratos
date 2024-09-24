<?php

namespace App\Controller\Api;

use App\Http\Request;

class ApiController
{

    public static function getDetails(Request $request): array
    {
        return [
            'nome' => 'API - JB',
            'version' => '1.0.0',
            'author' => 'Jefferson Lopes',
            'email' => 'jeffbreno@gmail.com'
        ];
    }
}
