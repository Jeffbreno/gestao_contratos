<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Convenio as EntityConvenios;
use App\Model\Entity\ConvenioServico as EntityConvenioServico;
use App\Model\Entity\Banco as EntityBancos;

class ConveniosServicosController extends PageController
{
    /**
     * Método responsável por retornar mensagem de status
     */
    private static function getStatus(Request $request)
    {
        #QUERY PARAMS
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['status']))
            return '';

        $status = explode('?', $queryParams['status'])[0];

        #MENSAGEM DE STATUS
        switch ($status) {
            case 'created':
                return AlertController::getSuccess('Serviço criado com sucesso!');
            case 'update':
                return AlertController::getSuccess('Serviço atualizado com sucesso!');
            case 'deleted':
                return AlertController::getSuccess('Serviço excluído com sucesso!');
            case 'error':
                return AlertController::getError('ERRO ao tentar atualizar os dados!');
        }
    }

    /** 
     * Método responsável por obter a renderização dos itens de serviços para a página
     */
    private static function getConvenioServicoItems(Request $request, &$obPagination): string
    {
        // SERVIÇOS
        $resultItems = '';

        // RESULTADO DA PÁGINA
        $queryServicos = EntityConvenioServico::with('convenios')->orderBy('codigo', 'desc')->get();

        // Seta e Retorna itens por página
        $obPagination = PageController::setPaginator($request, $queryServicos, 10);

        foreach ($obPagination as $servico) {
            $resultItems .= View::render('pages/convenios_servicos/item', [
                'codigo' => $servico->codigo,
                'servico' => $servico->servico,
                'convenio' => $servico->convenios->convenio,
            ]);
        }

        // RETORNA OS SERVIÇOS
        return $resultItems;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     */
    public static function getConveniosServicos(Request $request): string
    {
        #CONTEÚDO DA HOME DE SERVIÇOS
        $content = View::render('pages/convenios_servicos/index', [
            'botaolink' => URL . '/convenios-servicos/new',
            'nomebotao' => 'Cadastrar novo serviço',
            'itens' => self::getConvenioServicoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request),
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Serviços Cadastrados', $content, 'convenios-servicos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo serviço
     */
    public static function getNewConvenioServico(Request $request): string
    {
        // Obtém todos os convênios
        $queryConvenios = EntityConvenios::all();

        // Verifica se os convênios foram encontrados
        if ($queryConvenios->isEmpty()) {
            // Redireciona caso não haja convênios cadastrados
            $request->getRouter()->redirect('/convenios/new');
        }

        // Monta as opções do select
        $optionsConvenios = '';
        foreach ($queryConvenios as $convenio) {
            $optionsConvenios .= '<option value="' . $convenio->codigo . '">' . htmlspecialchars($convenio->convenio) . '</option>';
        }

        // Renderiza o select
        $selectConvenios = '
        <select class="form-select" name="convenio" id="convenio" required>
            <option selected value="">Selecione um Convênio</option>
            ' . $optionsConvenios . '
        </select>
        ';

        #CONTEÚDO DA HOME DE SERVIÇOS
        $content = View::render('pages/convenios_servicos/form', [
            'servico' => null,
            'convenio' => $selectConvenios,
            'status' => self::getStatus($request),
        ]);

        return parent::getPainel('Cadastrar Novo Serviço', $content, 'convenios-servicos');
    }

    public static function setNewConvenioServico(Request $request): void
    {
        // DADOS DO POST
        $postVars = $request->getPostVars();
        $obServico = new EntityConvenioServico;

        // LAÇO PARA INCREMENTAR TODAS AS CHAVES
        foreach ($postVars as $key => $value) {
            $obServico->$key = $value;
        }

        // CADASTRAR DADOS
        try {
            $obServico->save();
            $request->getRouter()->redirect('/convenios-servicos/' . $obServico->codigo . '/edit?status=created');
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/convenios-servicos/new?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um serviço
     */
    public static function getEditConvenioServico(Request $request, int $id): string
    {
        $obServico = EntityConvenioServico::find($id);

        if (!$obServico instanceof EntityConvenioServico) {
            $request->getRouter()->redirect('/convenios-servicos');
        }

        // Recupera todos os convênios
        $convenios = EntityConvenios::all();

        // Gera as opções do select
        $optionsConvenios = '';
        foreach ($convenios as $convenio) {
            // Verifica se o convênio atual é o que está associado ao serviço
            $selected = ($convenio->codigo == $obServico->convenio) ? 'selected' : '';
            $optionsConvenios .= '<option value="' . $convenio->codigo . '" ' . $selected . '>' . htmlspecialchars($convenio->convenio) . '</option>';
        }

        // Renderiza o select
        $selectConvenios = '
        <select class="form-select" name="convenio" id="convenio" required>
            <option value="">Selecione um Convênio</option>
            ' . $optionsConvenios . '
        </select>
        ';

        #CONTEÚDO DA EDIÇÃO DE SERVIÇOS
        $content = View::render('pages/convenios_servicos/form', [
            'servico' => $obServico->servico,
            'convenio' => $selectConvenios,
            'status' => self::getStatus($request),
        ]);

        return parent::getPainel('Editar Serviço', $content, 'convenios-servicos');
    }

    public static function setEditConvenioServico(Request $request, int $id)
    {
        $obServico = EntityConvenioServico::find($id);
        #POST VARS
        $postVars = $request->getPostVars();

        if (!$obServico instanceof EntityConvenioServico) {
            $request->getRouter()->redirect('/convenios-servicos');
        }

        // LAÇO PARA INCREMENTAR TODAS AS CHAVES
        foreach ($postVars as $key => $value) {
            $obServico->$key = $value;
        }

        // ATUALIZAR DADOS
        try {
            $obServico->update();
            return $request->getRouter()->redirect('/convenios-servicos/' . $id . '/edit?status=update');
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/convenios-servicos/' . $id . '/edit?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um serviço
     */
    public static function getDeleteConvenioServico(Request $request, int $id): string
    {
        $obServico = EntityConvenioServico::find($id);

        if (!$obServico instanceof EntityConvenioServico) {
            $request->getRouter()->redirect('/convenios-servicos');
        }

        #CONTEÚDO DA EXCLUSÃO DE SERVIÇO
        $content = View::render('pages/convenios_servicos/delete', [
            'title' => 'Deletar Serviço',
            'servico' => $obServico->servico,
        ]);

        return parent::getPainel('Excluir Serviço', $content, 'convenios-servicos');
    }

    /**
     * Método responsável por excluir um serviço
     */
    public static function setDeleteConvenioServico(Request $request, int $id): void
    {
        $obServico = EntityConvenioServico::find($id);

        if (!$obServico instanceof EntityConvenioServico) {
            $request->getRouter()->redirect('/convenios-servicos');
        }

        // EXCLUIR SERVIÇO
        try {
            $obServico->delete();
            $request->getRouter()->redirect('/convenios-servicos?status=deleted');
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/convenios-servicos?status=error');
        }
    }
}
