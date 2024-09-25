<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

class ConvenioServico extends Model
{
    protected $primaryKey = 'codigo';
    protected $table = 'tb_convenio_servico';
    protected $fillable = ['convenio', 'servico'];
    public $timestamps = false;

    // Relação com Tb_convenio (muitos serviços pertencem a um convênio)
    public function convenios()
    {
        return $this->belongsTo(Convenio::class, 'convenio', 'codigo');
    }

    // Relação com Tb_contrato (um serviço pode ter muitos contratos)
    public function contratos()
    {
        return $this->hasMany(Contrato::class, 'convenio_servico', 'codigo');
    }
}
