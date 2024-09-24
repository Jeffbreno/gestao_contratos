<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'usuarios';
    //CAMPOS USADOS NA TABELA DE DADOS
    protected $fillable = ['nome', 'email', 'senha', 'id_alteracao'];
    public $timestamps = true;
    const CREATED_AT = 'dt_cadastro';
    const UPDATED_AT = 'dt_alteracao';
    /**
     * Método responsável por retornar busca por ID de usuario
     *
     */
    public static function getById(int $id): mixed
    {
        return self::find($id);
    }

    /**
     * Método reponsavel por buscar usuário pleo email
     */
    public static function getByEmail(string $email): mixed
    {
        #BUSCA USUÁRIO POR EMAIL
        return User::where('email', $email)->first();
    }
}
