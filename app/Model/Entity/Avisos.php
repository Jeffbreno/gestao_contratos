<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

class Avisos extends Model
{
    protected $table = 'avisos';
    protected $fillable = ['titulo', 'mensagem', 'dt_cadastro', 'dt_alteracao', 'status'];
    public $timestamps = false;
}
