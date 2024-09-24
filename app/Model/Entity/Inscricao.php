<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

class Inscricao extends Model
{
    protected $table = 'usuarios_inscritos';
    protected $fillable = [
        'nome',
        'nome_responsavel',
        'email',
        'genero',
        'cpf',
        'categoria',
        'dt_nascimento',
        'celular',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'distancia',
        'camisa',
        'equipe',
        'dt_cadastro',
        'dt_alteracao'

    ];
    public $timestamps = false;
}
