<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Contrato as EntityContrato;

class ReportController extends PageController
{

    /**
     * Método responsável por gerar o relatório de contratos e convênios
     * com informações dos bancos
     */
    private static function getContractsReportItems(Request $request, &$obPagination): string
    {
        $resultItems = '';

        // Consulta os dados das tabelas com os joins especificados
        $contracts = EntityContrato::join('tb_convenio_servico', 'tb_contrato.convenio_servico', '=', 'tb_convenio_servico.codigo')
            ->join('tb_convenio', 'tb_convenio_servico.convenio', '=', 'tb_convenio.codigo')
            ->join('tb_banco', 'tb_convenio.banco', '=', 'tb_banco.codigo')
            ->select(
                'tb_banco.nome AS nome_banco',
                'tb_convenio.verba',
                'tb_contrato.codigo AS codigo_contrato',
                'tb_contrato.data_inclusao',
                'tb_contrato.valor',
                'tb_contrato.prazo'
            )
            ->get();

        $obPagination = PageController::setPaginator($request, $contracts, 10);

        foreach ($obPagination as $contract) {
            $resultItems .= View::render('pages/reports/item', [
                'nome_banco' => $contract->nome_banco,
                'verba' => $contract->verba,
                'codigo_contrato' => str_pad($contract->codigo_contrato, 4, '0', STR_PAD_LEFT),
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
            'itens_group' => self::getSummaryReportItems($request, $obPagination),
            'pagination_group' => parent::getPagination($request, $obPagination),
        ]);

        return parent::getPainel('Relatório de Contratos', $content, 'reports');
    }

    /**
     * Método responsável por gerar um relatório sumarizado com
     * as datas de contratos mais antigos, mais novos e soma dos valores
     */
    private static function getSummaryReportItems(Request $request, &$obPagination): string
    {
        $resultItems = '';

        // Consulta sumarizada com MIN, MAX, e SUM agrupados por banco e verba
        $summary = EntityContrato::join('tb_convenio_servico', 'tb_contrato.convenio_servico', '=', 'tb_convenio_servico.codigo')
            ->join('tb_convenio', 'tb_convenio_servico.convenio', '=', 'tb_convenio.codigo')
            ->join('tb_banco', 'tb_convenio.banco', '=', 'tb_banco.codigo')
            ->select(
                'tb_banco.nome AS nome_banco',
                'tb_convenio.verba',
                \Illuminate\Database\Capsule\Manager::raw('MIN(tb_contrato.data_inclusao) AS data_contrato_mais_antigo'),
                \Illuminate\Database\Capsule\Manager::raw('MAX(tb_contrato.data_inclusao) AS data_contrato_mais_novo'),
                \Illuminate\Database\Capsule\Manager::raw('SUM(tb_contrato.valor) AS soma_valor_contratos')
            )
            ->groupBy('tb_banco.nome', 'tb_convenio.verba')
            ->orderBy('tb_banco.nome', 'asc')
            ->orderBy('tb_convenio.verba', 'asc')
            ->get();

        // Paginação
        $obPagination = PageController::setPaginator($request, $summary, 10);

        foreach ($obPagination as $item) {
            $resultItems .= View::render('pages/reports/item_group', [
                'nome_banco_group' => $item->nome_banco,
                'verba_group' => $item->verba,
                'data_contrato_mais_antigo' => date('d/m/Y', strtotime($item->data_contrato_mais_antigo)),
                'data_contrato_mais_novo' => date('d/m/Y', strtotime($item->data_contrato_mais_novo)),
                'soma_valor_contratos' => number_format($item->soma_valor_contratos, 2, ',', '.')
            ]);
        }

        return $resultItems;
    }

}