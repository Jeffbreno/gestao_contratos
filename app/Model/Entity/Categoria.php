<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $fillable = [
        'titulo',
        'link_pagamento'

    ];
    public $timestamps = true;
    const CREATED_AT = 'dt_cadastro';
    const UPDATED_AT = 'dt_alteracao';

    /**
     * Método responsável por retornar busca por ID da categoria
     *
     */
    public static function getById(int $id): mixed
    {
        return self::find($id);
    }

    /**
     * Método reponsavel por buscar link da categoria
     */
    public static function getByCategoria(string $link): mixed
    {
        #BUSCA USUÁRIO POR EMAIL
        return Categoria::where('link_pagamento', $link)->first();
    }
}
