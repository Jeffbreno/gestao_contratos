<?php

namespace App\Model\Entity;

use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    protected $primaryKey = 'codigo';
    protected $table = 'tb_contrato';
    protected $fillable = ['prazo', 'valor', 'data_inclusao', 'convenio_servico'];
    public $timestamps = false;

    // Relação com Tb_convenio_servico (muitos contratos pertencem a um serviço de convênio)
    public function convenioServicos()
    {
        return $this->belongsTo(ConvenioServico::class, 'convenio_servico', 'codigo');
    }
}

