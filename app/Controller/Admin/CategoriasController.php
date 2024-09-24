<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Categoria as EntityCategoria;

class CategoriasController extends PageController
{

    /**
     * Método reponsável por retornar mensagem de status
     *
     */
    private static function getStatus(Request $request)
    {
        #QUERY PARAMS
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams['status']))
            return '';

        #MENSAGEM DE STATUS
        switch ($queryParams['status']) {
            case 'created':
                return AlertController::getSuccess('Categoria criada com sucesso!');
            case 'updated':
                return AlertController::getSuccess('Categoria atualizada com sucesso!');
            case 'deleted':
                return AlertController::getSuccess('Categoria excluído com sucesso!');
            case 'duplicated':
                return AlertController::getError('Link informado consta em cadastro!');
        }
    }

    /** 
     * Método responsável por obter a renderização dos itens de usuarios para a página
     * 
     */
    private static function getCategoriasItems(Request $request, &$obPagination): string
    {
        //USUÁRIOS
        $resultItems = '';

        // RESULTADO DA PÁGINA
        $queryTestmonies = EntityCategoria::where('status', '=', 'A')->orderBy('id', 'desc')->get();

        //GET DA PAGINA ATUAL 
        $queryParams = $request->getQueryParams();

        $currentPage = $queryParams['page'] ?? 1;

        //Seta e Retorna intens por página
        $obPagination = PageController::setPaginator($request, $queryTestmonies, 5);

        foreach ($obPagination as $categorias) {
            $resultItems .= View::render('admin/categorias/item', [
                'id' => $categorias->id,
                'titulo' => $categorias->titulo,
                'link_pagamento' => $categorias->link_pagamento,
            ]);
        }

        //RETORNA OS USUÁRIOS
        return $resultItems;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     *
     */
    public static function getCategorias(Request $request): string
    {
        #CONTEÚDO DA HOME DE USUÁRIOS
        $content = View::render('admin/categorias/index', [
            'itens' => self::getCategoriasItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request),
            'descricao' => 'Lista de links de compras por categoria'
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Links', $content, 'categorias');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     *
     */
    public static function getNewCategorias(Request $request): string
    {
        #CONTEÚDO DA HOME DE USUÁRIOS
        $content = View::render('admin/categorias/form', [
            'titulo' => null,
            'link_pagamento' => null,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Cadastrar Links', $content, 'categorias');
    }

    public static function setNewCategorias($request): string
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();
        $titulo = $postVars['titulo'] ?? '';
        $link = $postVars['link_pagamento'] ?? '';

        //VALIDAR EMAIL DO USUÁRIO
        $obCategoria = EntityCategoria::getByCategoria($link);

        if ($obCategoria instanceof EntityCategoria) {
            return $request->getRouter()->redirect('/admin/categorias/new?status=duplicated');
        }

        $obCategoria = new EntityCategoria;
        $obCategoria->titulo = $titulo;
        $obCategoria->link_pagamento = $link;

        $obCategoria->save();

        return $request->getRouter()->redirect('/admin/categorias?status=created');
    }

    /**
     * Método responsável por retornar o fomulário de edição de um depoimento
     *
     */
    public static function getEditCategorias(Request $request, int $id): string
    {
        $obCategoria = EntityCategoria::getById($id);

        if (!$obCategoria instanceof EntityCategoria) {
            $request->getRouter()->redirect('/admin/categorias');
        }

        #CONTEÚDO DA HOME DE USUÁRIOS
        $content = View::render('admin/categorias/form', [
            'title' => 'Editar usuário',
            'titulo' => $obCategoria->titulo,
            'link_pagamento' => $obCategoria->link_pagamento,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Editar Link', $content, 'categorias');
    }

    public static function setEditCategorias(Request $request, int $id)
    {
        // Obtem a categoria pelo ID
        $obCategoria = EntityCategoria::getById($id);

        // Verifica se a categoria não existe
        if (!$obCategoria instanceof EntityCategoria) {
            $request->getRouter()->redirect('/admin/categorias');
        }

        // Dados do POST
        $postVars = $request->getPostVars();
        $titulo = $postVars['titulo'] ?? '';
        $link = $postVars['link_pagamento'] ?? '';

        // Valida o link
        $obCategoriaLink = EntityCategoria::getByCategoria($link);

        if ($obCategoriaLink instanceof EntityCategoria && $obCategoriaLink->id != $id) {
            return $request->getRouter()->redirect('/admin/categorias/' . $id . '/edit?status=duplicated');
        }

         // Atualiza a instância da categoria
        $obCategoria->titulo = $titulo;
        $obCategoria->link_pagamento = $link;

        // Atualiza no banco de dados
        $obCategoria->update();

        return $request->getRouter()->redirect('/admin/categorias?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     *
     */
    public static function getDeleteCategorias(Request $request, int $id): string
    {
        $obCategoria = EntityCategoria::getById($id);

        if (!$obCategoria instanceof EntityCategoria) {
            $request->getRouter()->redirect('/admin/categorias');
        }

        #CONTEÚDO DA HOME DE USUÁRIOS
        $content = View::render('admin/categorias/delete', [
            'title' => 'Excluir Link',
            'titulo' => $obCategoria->nome,
            'link_pagamento' => $obCategoria->link_pagamento,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Excluir registro', $content, 'categorias');
    }

    /**
     * Método responsavel por excluir um depoimento
     *
     */
    public static function setDeleteCategorias(Request $request, int $id)
    {
        $obCategoria = EntityCategoria::getById($id);

        if (!$obCategoria instanceof EntityCategoria) {
            $request->getRouter()->redirect('/admin/categorias');
        }

        $obCategoria->status = 'E';

        #EXCLUI O REGISTRO // Atuliza status para inativo
        $obCategoria->update();

        return $request->getRouter()->redirect('/admin/categorias?status=deleted');
    }
}
