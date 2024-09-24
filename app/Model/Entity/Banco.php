<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    protected $primaryKey = 'codigo';
    protected $table = 'tb_banco';
    protected $fillable = ['nome'];
    public $timestamps = true;
    const CREATED_AT = 'dt_cadastro';
    const UPDATED_AT = 'dt_alteracao';
}
