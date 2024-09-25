<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Banco as EntityBancos;
// use App\Model\Entity\Categoria as EntityCategoria;

class BancosController extends PageController
{
    /**
     * Método reponsável por retornar mensagem de status
     *
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
                return AlertController::getSuccess('Criado com sucesso!');
            case 'update':
                return AlertController::getSuccess('Atualizado com sucesso!');
            case 'deleted':
                return AlertController::getSuccess('Excluído com sucesso!');
            case 'error':
                return AlertController::getError('ERRO ao tentar atualizar dados!');
        }
    }

    /** 
     * Método responsável por obter a renderização dos itens de BANCOS para a página
     * 
     */
    private static function getBancoItems(Request $request, &$obPagination): string
    {
        //BANCOS
        $resultItems = '';

        // RESULTADO DA PÁGINA
        $queryBancos = EntityBancos::orderBy('codigo', 'desc')->get();

        //Seta e Retorna intens por página
        $obPagination = PageController::setPaginator($request, $queryBancos, 10);

        foreach ($obPagination as $banco) {
            $resultItems .= View::render('pages/bancos/item', [
                'codigo' => $banco->codigo,
                'nome' => $banco->nome,
                'dt_cadastro' => date('d/m/Y H:i', strtotime($banco->dt_cadastro))
            ]);
        }

        //RETORNA OS BANCOS
        return $resultItems;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     *
     */
    public static function getBancos(Request $request): string
    {
        #CONTEÚDO DA HOME DE BANCOS
        $content = View::render('pages/bancos/index', [
            'botaolink' => URL . '/bancos/new',
            'nomebotao' => 'Cadastrar novo banco',
            'itens' => self::getBancoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request),
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Bancos Cadastrados', $content, 'bancos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     *
     */
    public static function getNewBanco(Request $request): string
    {

        #CONTEÚDO DA HOME DE BANCOS
        $content = View::render('pages/bancos/form', [
            'nome' => null,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Cadastrar Novo Banco', $content, 'bancos');
    }

    public static function setNewBanco($request): string
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();
        $obBanco = new EntityBancos;

        if (!$obBanco instanceof EntityBancos) {
            $request->getRouter()->redirect('/pages/bancos');
        }

        #POST VARS
        $postVars = $request->getPostVars();

        //LAÇO PARA INCREMENTAR TODAS AS KEY, PRECISANDO SER IGUAL COM O QUE ESTA EM BANCO
        foreach ($postVars as $key => $value) {
            $obBanco->$key = $value;
        }

        //CADASTRAR DADOS
        try {
            $obBanco->save();
            return $request->getRouter()->redirect('/bancos/' . $obBanco->codigo . '/edit?status=update');
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/bancos/' . $obBanco->codigo . '/edit?status=error');
        }
    }

    /**
     * Método responsável por retornar o fomulário de edição de um depoimento
     *
     */
    public static function getEditBanco(Request $request, int $id): string
    {
        $obBanco = EntityBancos::find($id);

        if (!$obBanco instanceof EntityBancos) {
            $request->getRouter()->redirect('/bancos');
        }

        #CONTEÚDO DA HOME DE BANCOS
        $content = View::render('pages/bancos/form', [
            'nome' => $obBanco->nome,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Editar Banco', $content, 'bancos');
    }

    public static function setEditBanco(Request $request, int $id)
    {
        $obBanco = EntityBancos::find($id);

        if (!$obBanco instanceof EntityBancos) {
            $request->getRouter()->redirect('/pages/bancos');
        }

        #POST VARS
        $postVars = $request->getPostVars();

        //LAÇO PARA INCREMENTAR TODAS AS KEY, PRECISANDO SER IGUAL COM O QUE ESTA EM BANCO
        foreach ($postVars as $key => $value) {
            $obBanco->$key = $value;
        }

        //ATUALIZAR DADOS
        try {
            $obBanco->update();
            return $request->getRouter()->redirect('/bancos/' . $id . '/edit?status=update');
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/bancos/' . $id . '/edit?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     *
     */
    public static function getDeleteBanco(Request $request, int $id): string
    {
        $obBanco = EntityBancos::find($id);

        if (!$obBanco instanceof EntityBancos) {
            $request->getRouter()->redirect('/bancos');
        }

        #CONTEÚDO DA HOME DE BANCOS
        $content = View::render('pages/bancos/delete', [
            'title' => 'Excluir Registro',
            'nome' => $obBanco->nome,
        ]);
        return parent::getPainel('Excluir registro', $content, 'bancos');
    }

    /**
     * Método responsavel por excluir um depoimento
     */
    public static function setDeleteBanco(Request $request, int $id): void
    {
        $obBanco = EntityBancos::find($id);

        if (!$obBanco instanceof EntityBancos) {
            $request->getRouter()->redirect('/bancos');
        }

        #QUERY PARAMS
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;

        #EXCLUI O REGISTRO
        $obBanco->delete();

        //ATUALIZAR DADOS
        try {
            $obBanco->delete();
            $request->getRouter()->redirect('/bancos?status=update?pag=' . $currentPage);
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/bancos?status=error?pag=' . $currentPage);
        }
    }

    /**
     * Método responsavel por excluir um depoimento
     */
    public static function setStatusPag(Request $request, int $id): void
    {
        $obBanco = EntityBancos::find($id);

        if (!$obBanco instanceof EntityBancos) {
            $request->getRouter()->redirect('/bancos');
        }

        #QUERY PARAMS
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $statusPagamento = ($queryParams['status'] === 'P' ? 'A' : 'P');
        $obBanco->status_pag = $statusPagamento;

        //ATUALIZAR DADOS
        try {
            $obBanco->update();
            $request->getRouter()->redirect('/bancos?status=update?pag=' . $currentPage);
        } catch (\Exception $e) {
            $request->getRouter()->redirect('/bancos?status=error?pag=' . $currentPage);
        }
    }
}
