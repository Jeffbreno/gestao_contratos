<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
    protected $primaryKey = 'codigo';
    protected $table = 'tb_convenio';
    protected $fillable = ['convenio', 'verba', 'banco'];
    public $timestamps = false;

    // Relação com Tb_banco (muitos convênios pertencem a um banco)
    public function bancos()
    {
        return $this->belongsTo(Banco::class, 'banco', 'codigo');
    }

    // Relação com Tb_convenio_servico (um convênio pode ter muitos serviços)
    public function convenioServicos()
    {
        return $this->hasMany(ConvenioServico::class, 'convenio', 'codigo');
    }
}
