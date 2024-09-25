<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Contrato as EntityContrato;

class ReportController extends PageController
{
    private static function getContractsReportItems(Request $request, &$obPagination): string
    {
        $resultItems = '';

        // Consulta os dados das tabelas com os joins especificados
        $contracts = EntityContrato::join('Tb_convenio_servico', 'Tb_contrato.convenio_servico', '=', 'Tb_convenio_servico.codigo')
            ->join('Tb_convenio', 'Tb_convenio_servico.convenio', '=', 'Tb_convenio.codigo')
            ->join('Tb_banco', 'Tb_convenio.banco', '=', 'Tb_banco.codigo')
            ->select(
                'Tb_banco.nome AS nome_banco',
                'Tb_convenio.verba',
                'Tb_contrato.codigo AS codigo_contrato',
                'Tb_contrato.data_inclusao',
                'Tb_contrato.valor',
                'Tb_contrato.prazo'
            )
            ->get();

        $obPagination = PageController::setPaginator($request, $contracts, 10);

        foreach ($obPagination as $contract) {
            $resultItems .= View::render('pages/reports/item', [
                'nome_banco' => $contract->nome_banco,
                'verba' => $contract->verba,
                'codigo_contrato' => str_pad($contract->codigo_contrato , 4 , '0' , STR_PAD_LEFT),
                'data_inclusao' => date('d/m/Y', strtotime($contract->data_inclusao)),
                'valor' => number_format($contract->valor, 2, ',', '.'),
                'prazo' => $contract->prazo
            ]);
        }

        return $resultItems;
    }

    public static function getContractsReport(Request $request): string
    {
        $content = View::render('pages/reports/index', [
            'itens' => self::getContractsReportItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
        ]);

        return parent::getPainel('Relátórios', $content, 'convenios-servicos');
    }

}