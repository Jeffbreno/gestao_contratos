<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Convenio as EntityConvenios;
use App\Model\Entity\Banco as EntityBancos;

class ConveniosController extends PageController
{
    /**
     * Método reponsável por retornar mensagem de status
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
                return AlertController::getSuccess('Convênio criado com sucesso!');
            case 'update':
                return AlertController::getSuccess('Convênio atualizado com sucesso!');
            case 'deleted':
                return AlertController::getSuccess('Convênio excluído com sucesso!');
            case 'error':
                return AlertController::getError('ERRO ao tentar atualizar os dados!');
        }
    }

    /** 
     * Método responsável por obter a renderização dos itens de convênios para a página
     */
    private static function getConvenioItems(Request $request, &$obPagination): string
    {
        // CONVÊNIOS
        $resultItems = '';

        // RESULTADO DA PÁGINA
        // $queryConvenios = EntityConvenios::orderBy('codigo', 'desc')->get();
        $queryConvenios = EntityConvenios::with('bancos')->orderBy('codigo', 'desc')->get();

        // Seta e Retorna itens por página
        $obPagination = PageController::setPaginator($request, $queryConvenios, 10);

        foreach ($obPagination as $convenio) {
            $resultItems .= View::render('pages/convenios/item', [
                'codigo' => $convenio->codigo,
                'convenio' => $convenio->convenio,
                'verba' => $convenio->verba,
                'banco' => $convenio->bancos->nome
            ]);
        }

        // RETORNA OS CONVÊNIOS
        return $resultItems;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     */
    public static function getConvenios(Request $request): string
    {
        #CONTEÚDO DA HOME DE CONVÊNIOS
        $content = View::render('pages/convenios/index', [
            'botaolink' => URL . '/convenios/new',
            'nomebotao' => 'Cadastrar novo convênio',
            'itens' => self::getConvenioItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request),
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Convênios Cadastrados', $content, 'convenios');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo convênio
     */
    public static function getNewConvenio(Request $request): string
    {

        // Obtém todos os bancos
        $queryBancos = EntityBancos::all();

        // Verifica se os bancos foram encontrados
        if ($queryBancos->isEmpty()) {
            // Redireciona caso não haja bancos cadastrados
            $request->getRouter()->redirect('/bancos/new');
        }


        // Monta as opções do select
        $optionsBancos = '';
        foreach ($queryBancos as $banco) {
            $optionsBancos .= '<option value="' . $banco->codigo . '">' . htmlspecialchars($banco->nome) . '</option>';
        }

        // Renderiza o select
        $selectBancos = '
        <select class="form-select" name="banco" id="banco" required>
            <option selected value="">Selecione um Banco</option>
            ' . $optionsBancos . '
        </select>
        ';

        #CONTEÚDO DA HOME DE CONVÊNIOS
        $content = View::render('pages/convenios/form', [
            'convenio' => null,
            'verba' => null,
            'banco' => $selectBancos,
            'status' => self::getStatus($request),
        ]);

        return parent::getPainel('Cadastrar Novo Convênio', $content, 'convenios');
    }

    public static function setNewConvenio($request): string
    {
        // DADOS DO POST
        $postVars = $request->getPostVars();
        $obConvenio = new EntityConvenios;

        $postVars['verba'] = str_replace('.', '', $postVars['verba']);
        $postVars['verba'] = str_replace(',', '.', $postVars['verba']);

        // LAÇO PARA INCREMENTAR TODAS AS CHAVES
        foreach ($postVars as $key => $value) {
            $obConvenio->$key = $value;
        }

        // CADASTRAR DADOS
        try {
            $obConvenio->save();
            return $request->getRouter()->redirect('/convenios/' . $obConvenio->codigo . '/edit?status=created');
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/convenios/new?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de edição de um convênio
     */
    public static function getEditConvenio(Request $request, int $id): string
    {
        $obConvenio = EntityConvenios::find($id);


        if (!$obConvenio instanceof EntityConvenios) {
            $request->getRouter()->redirect('/convenios');
        }

        // Recupera todos os bancos
        $bancos = EntityBancos::all();

        // Gera as opções do select
        $optionsBancos = '';
        foreach ($bancos as $banco) {
            // Verifica se o banco atual é o que está associado ao convênio
            $selected = ($banco->codigo == $obConvenio->banco) ? 'selected' : '';
            $optionsBancos .= '<option value="' . $banco->codigo . '" ' . $selected . '>' . htmlspecialchars($banco->nome) . '</option>';
        }

        // Renderiza o select
        echo $selectBancos = '
        <select class="form-select" name="banco" id="banco" required>
            <option value="">Selecione um Banco</option>
            ' . $optionsBancos . '
        </select>
        ';

        #CONTEÚDO DA EDIÇÃO DE CONVÊNIOS
        $content = View::render('pages/convenios/form', [
            'convenio' => $obConvenio->convenio,
            'verba' => $obConvenio->verba,
            'banco' => $selectBancos,
            'status' => self::getStatus($request),
        ]);

        return parent::getPainel('Editar Convênio', $content, 'convenios');
    }

    public static function setEditConvenio(Request $request, int $id): void
    {
        $obConvenio = EntityConvenios::find($id);
        #POST VARS
        $postVars = $request->getPostVars();

        $postVars['verba'] = str_replace('.', '', $postVars['verba']);
        $postVars['verba'] = str_replace(',', '.', $postVars['verba']);

        if (!$obConvenio instanceof EntityConvenios) {
            $request->getRouter()->redirect('/convenios');
        }

        // LAÇO PARA INCREMENTAR TODAS AS CHAVES
        foreach ($postVars as $key => $value) {
            $obConvenio->$key = $value;
        }

        // ATUALIZAR DADOS
        try {
            $obConvenio->update();
            $request->getRouter()->redirect('/convenios/' . $id . '/edit?status=update');
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/convenios/' . $id . '/edit?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um convênio
     */
    public static function getDeleteConvenio(Request $request, int $id): string
    {
        $obConvenio = EntityConvenios::find($id);

        if (!$obConvenio instanceof EntityConvenios) {
            $request->getRouter()->redirect('/convenios');
        }

        #CONTEÚDO DA EXCLUSÃO DE CONVÊNIO
        $content = View::render('pages/convenios/delete', [
            'title' => 'Deletar Convênio',
            'convenio' => $obConvenio->convenio,
        ]);

        return parent::getPainel('Excluir Convênio', $content, 'convenios');
    }

    /**
     * Método responsável por excluir um convênio
     */
    public static function setDeleteConvenio(Request $request, int $id): void
    {
        $obConvenio = EntityConvenios::find($id);

        if (!$obConvenio instanceof EntityConvenios) {
            $request->getRouter()->redirect('/convenios');
        }

        // EXCLUIR CONVÊNIO
        try {
            $obConvenio->delete();
            $request->getRouter()->redirect('/convenios?status=deleted');
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/convenios?status=error');
        }
    }
}
