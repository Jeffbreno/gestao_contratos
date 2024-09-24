<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Avisos as EntityAvisos;

class AvisosController extends PageController
{
    /**
     * Método reponsável por retornar mensagem de status
     *
     */
    private static function getStatus(Request $request)
    {
        #QUERY PARAMS
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['status'])) return '';

        #MENSAGEM DE STATUS
        switch ($queryParams['status']) {
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
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * 
     */
    private static function getAvisoItems(Request $request, &$obPagination): string
    {
        //DEPOIMENTOS
        $resultItems = '';

        // RESULTADO DA PÁGINA
        $queryAvisos = EntityAvisos::orderBy('id')->get();

        //Seta e Retorna intens por página
        $obPagination = PageController::setPaginator($request, $queryAvisos, 10);

        foreach ($obPagination as $aviso) {
            $resultItems .= View::render('admin/avisos/item', [
                'id' => $aviso->id,
                'titulo' => $aviso->titulo,
                'mensagem' => $aviso->mensagem,
                'dt_cadastro' => date('d/m/Y H:i', strtotime($aviso->dt_cadastro))
            ]);
        }

        //RETORNA OS DEPOIMENTOS
        return $resultItems;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     *
     */
    public static function getAviso(Request $request): string
    {

        #CONTEÚDO DA HOME DE DEPOIMENTOS
        $content = View::render('admin/avisos/index', [
            'botaolink' => URL . '/admin/avisos/new',
            'nomebotao' => 'Enviar novo aviso',
            'descricao' => 'Lista de avisos enviados',
            'itens' => self::getAvisoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request)
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Avisos', $content, 'avisos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     *
     */
    public static function getNewAviso(Request $request): string
    {

        #CONTEÚDO DA HOME DE DEPOIMENTOS
        $content = View::render('admin/avisos/form', [
            'titulo' => null,
            'mensagem' => null,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Cadastrar Novo Aviso', $content, 'avisos');
    }

    public static function setNewAviso($request): string
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();
        $obAvisos = new EntityAvisos;

        if (!$obAvisos instanceof EntityAvisos) {
            $request->getRouter()->redirect('/admin/avisos');
        }

        #POST VARS
        $postVars = $request->getPostVars();

        //LAÇO PARA INCREMENTAR TODAS AS KEY, PRECISANDO SER IGUAL COM O QUE ESTA EM BANCO
        foreach ($postVars as $key => $value) {
            $obAvisos->$key = $value;
        }

        //CADASTRAR DADOS
        try {
            $obAvisos->save();
            return $request->getRouter()->redirect('/admin/avisos/' . $obAvisos->id . '?status=update');
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/admin/avisos/' . $obAvisos->id . '?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     *
     */
    public static function getDeleteAviso(Request $request, int $id): string
    {
        $obAvisos = EntityAvisos::find($id);

        if (!$obAvisos instanceof EntityAvisos) {
            $request->getRouter()->redirect('/admin/avisos');
        }

        #CONTEÚDO DA HOME DE DEPOIMENTOS
        $content = View::render('admin/avisos/delete', [
            'title' => 'Excluir Registro',
            'titulo' => $obAvisos->titulo,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Excluir registro', $content, 'avisos');
    }

    /**
     * Método responsavel por excluir um depoimento
     * @return void
     */
    public static function setDeleteAviso(Request $request, int $id)
    {
        $obAvisos = EntityAvisos::find($id);

        if (!$obAvisos instanceof EntityAvisos) {
            $request->getRouter()->redirect('/admin/avisos');
        }

        #QUERY PARAMS
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;

        #EXCLUI O REGISTRO
        $obAvisos->delete();

        //ATUALIZAR DADOS
        try {
            $obAvisos->delete();
            return $request->getRouter()->redirect('/admin/avisos?status=update?pag=' . $currentPage);
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/admin/avisos?status=error?pag=' . $currentPage);
        }
    }
}
