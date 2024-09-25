<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Contrato as EntityContratos;
use App\Model\Entity\ConvenioServico as EntityConvenioServicos;

class ContratosController extends PageController
{
    /**
     * Método responsável por retornar mensagem de status
     */
    private static function getStatus(Request $request): string
    {
        #QUERY PARAMS
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['status']))
            return '';

        $status = explode('?', $queryParams['status'])[0];

        #MENSAGEM DE STATUS
        switch ($status) {
            case 'created':
                return AlertController::getSuccess('Contrato criado com sucesso!');
            case 'update':
                return AlertController::getSuccess('Contrato atualizado com sucesso!');
            case 'deleted':
                return AlertController::getSuccess('Contrato excluído com sucesso!');
            case 'error':
                return AlertController::getError('ERRO ao tentar atualizar os dados!');
        }

        return '';
    }

    /** 
     * Método responsável por obter a renderização dos itens de contratos para a página
     */
    private static function getContratoItems(Request $request, &$obPagination): string
    {
        // CONTRATOS
        $resultItems = '';

        // RESULTADO DA PÁGINA
        $queryContratos = EntityContratos::with('convenioServicos')->orderBy('codigo', 'desc')->get();

        // Seta e Retorna itens por página
        $obPagination = PageController::setPaginator($request, $queryContratos, 10);

        foreach ($obPagination as $contrato) {
            $resultItems .= View::render('pages/contratos/item', [
                'codigo' => $contrato->codigo,
                'prazo' => $contrato->prazo,
                'valor' => $contrato->valor,
                'data_inclusao' => date('d/m/Y', strtotime($contrato->data_inclusao)),
                'convenio_servico' => $contrato->convenioServicos->servico,
            ]);
        }

        // RETORNA OS CONTRATOS
        return $resultItems;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     */
    public static function getContratos(Request $request): string
    {
        #CONTEÚDO DA HOME DE CONTRATOS
        $content = View::render('pages/contratos/index', [
            'botaolink' => URL . '/contratos/new',
            'nomebotao' => 'Cadastrar novo contrato',
            'itens' => self::getContratoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request),
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Contratos Cadastrados', $content, 'contratos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo contrato
     */
    public static function getNewContrato(Request $request): string
    {
        $queryServicos = EntityConvenioServicos::all();

        // Verifica se os convênios foram encontrados
        if ($queryServicos->isEmpty()) {
            $request->getRouter()->redirect('/convenios-servicos/new');
        }

        // Monta as opções do select
        $optionsServicos = '';
        foreach ($queryServicos as $servicos) {
            $optionsServicos .= '<option value="' . $servicos->codigo . '">' . htmlspecialchars($servicos->servico) . '</option>';
        }

        // Renderiza o select
        $selectServicos = '
        <select class="form-select" name="convenio_servico" id="convenio_servico" required>
            <option selected value="">Selecione um Serviço</option>
            ' . $optionsServicos . '
        </select>
        ';

        #CONTEÚDO DO FORMULÁRIO
        $content = View::render('pages/contratos/form', [
            'contrato' => null,
            'data_inclusao' => null,
            'convenios_servicos' => $selectServicos,
            'status' => self::getStatus($request),
        ]);

        return parent::getPainel('Cadastrar Novo Contrato', $content, 'contratos');
    }

    public static function setNewContrato(Request $request): void
    {
        // DADOS DO POST
        $postVars = $request->getPostVars();
        $obContrato = new EntityContratos;


        $postVars['valor'] = str_replace('.', '', $postVars['valor']);
        $postVars['valor'] = str_replace(',', '.', $postVars['valor']);

        // Criar um objeto DateTime a partir da data brasileira
        $data = explode('/', $postVars['data_inclusao']);
        $postVars['data_inclusao'] = $data[2] . "-" . $data[1] . "-" . $data[0];

        // LAÇO PARA INCREMENTAR TODAS AS CHAVES
        foreach ($postVars as $key => $value) {
            $obContrato->$key = $value;
        }

        // CADASTRAR DADOS
        try {
            $obContrato->save();
            $request->getRouter()->redirect('/contratos/' . $obContrato->codigo . '/edit?status=created');
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/contratos/new?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um contrato
     */
    public static function getEditContrato(Request $request, int $id): string
    {
        $obContrato = EntityContratos::find($id);

        if (!$obContrato instanceof EntityContratos) {
            $request->getRouter()->redirect('/contratos');
        }

        $queryServicos = EntityConvenioServicos::all();

        // Gera as opções do select
        $optionsServicos = '';
        foreach ($queryServicos as $servicos) {
            $selected = ($servicos->codigo == $obContrato->convenio_servico) ? 'selected' : '';
            $optionsServicos .= '<option value="' . $servicos->codigo . '" ' . $selected . '>' . htmlspecialchars($servicos->servico) . '</option>';
        }


        // Renderiza o select
        $selectServicos = '
        <select class="form-select" name="convenio_servico" id="convenio_servico" required>
            <option value="">Selecione um serviço</option>
            ' . $optionsServicos . '
        </select>
        ';

        #CONTEÚDO DA EDIÇÃO DE CONTRATOS
        $content = View::render('pages/contratos/form', [
            'prazo' => $obContrato->prazo,
            'valor' => $obContrato->valor,
            'data_inclusao' => date('d/m/Y', strtotime($obContrato->data_inclusao)),
            'convenios_servicos' => $selectServicos,
            'status' => self::getStatus($request),
        ]);

        return parent::getPainel('Editar Contrato', $content, 'contratos');
    }

    public static function setEditContrato(Request $request, int $id): void
    {
        $obContrato = EntityContratos::find($id);
        #POST VARS
        $postVars = $request->getPostVars();

        $postVars['valor'] = str_replace('.', '', $postVars['valor']);
        $postVars['valor'] = str_replace(',', '.', $postVars['valor']);

        // Criar um objeto DateTime a partir da data brasileira
        $data = explode('/', $postVars['data_inclusao']);
        $postVars['data_inclusao'] = $data[2] . "-" . $data[1] . "-" . $data[0];


        if (!$obContrato instanceof EntityContratos) {
            $request->getRouter()->redirect('/contratos');
        }

        // LAÇO PARA INCREMENTAR TODAS AS CHAVES
        foreach ($postVars as $key => $value) {
            $obContrato->$key = $value;
        }

        // ATUALIZAR DADOS
        try {
            $obContrato->update();
            $request->getRouter()->redirect('/contratos/' . $id . '/edit?status=update');
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/contratos/' . $id . '/edit?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um contrato
     */
    public static function getDeleteContrato(Request $request, int $id): string
    {
        $obContrato = EntityContratos::find($id);

        if (!$obContrato instanceof EntityContratos) {
            $request->getRouter()->redirect('/contratos');
        }

        #CONTEÚDO DA EXCLUSÃO
        $content = View::render('pages/contratos/delete', [
            'prazo' => $obContrato->prazo,
            'status' => self::getStatus($request),
        ]);

        return parent::getPainel('Excluir Contrato', $content, 'contratos');
    }

    public static function setDeleteContrato(Request $request, int $id): void
    {
        $obContrato = EntityContratos::find($id);

        if (!$obContrato instanceof EntityContratos) {
            $request->getRouter()->redirect('/contratos');
        }

        // EXCLUIR DADOS
        try {
            $obContrato->delete();
            $request->getRouter()->redirect('/contratos?status=deleted');
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/contratos?status=error');
        }
    }
}
