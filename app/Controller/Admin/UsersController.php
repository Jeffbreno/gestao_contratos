<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\User as EntityUser;

class UsersController extends PageController
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
                return AlertController::getSuccess('Usuário criado com sucesso!');
            case 'updated':
                return AlertController::getSuccess('Usuário atualizado com sucesso!');
            case 'deleted':
                return AlertController::getSuccess('Usuário excluído com sucesso!');
            case 'duplicated':
                return AlertController::getError('E-mail do usuário já existe!');
            case 'errorSenha':
                return AlertController::getError('A senha deve conter no mínimo 6 caracteres');
        }
    }

    /** 
     * Método responsável por obter a renderização dos itens de usuarios para a página
     * 
     */
    private static function getUserItems(Request $request, &$obPagination): string
    {
        //USUÁRIOS
        $resultItems = '';

        // RESULTADO DA PÁGINA
        $queryTestmonies = EntityUser::orderBy('id', 'desc')->get();

        //GET DA PAGINA ATUAL 
        $queryParams = $request->getQueryParams();

        $currentPage = $queryParams['page'] ?? 1;

        //Seta e Retorna intens por página
        $obPagination = PageController::setPaginator($request, $queryTestmonies, 5);

        // $Pagination = Page::getLinkPages($request, $queryParams, $result);

        foreach ($obPagination as $users) {
            $resultItems .= View::render('pages/users/item', [
                'id' => $users->id,
                'nome' => $users->nome,
                'login' => $users->login,
                'email' => $users->email
            ]);
        }

        //RETORNA OS USUÁRIOS
        return $resultItems;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     *
     */
    public static function getUsers(Request $request): string
    {
        #CONTEÚDO DA HOME DE USUÁRIOS
        $content = View::render('pages/users/index', [
            'itens' => self::getUserItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request),
            'descricao' => 'Lista de usuários cadastrados'
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Usuários', $content, 'users');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     *
     */
    public static function getNewUsers(Request $request): string
    {
        #CONTEÚDO DA HOME DE USUÁRIOS
        $content = View::render('pages/users/form', [
            'nome' => null,
            'email' => null,
            'login' => null,
            'senha' => null,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Cadastrar usuário', $content, 'users');
    }

    public static function setNewUsers($request): string
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $login = $postVars['login'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDAR EMAIL DO USUÁRIO
        $obUser =  EntityUser::getByEmail($email);

        if ($obUser instanceof EntityUser) {
            return $request->getRouter()->redirect('/users/new?status=duplicated');
        }

        $obUser = new EntityUser;
        $obUser->nome = $nome;
        $obUser->login = $login;
        $obUser->email = $email;
        $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);

        $obUser->save();

        return $request->getRouter()->redirect('/users/' . $obUser->id . '/edit?status=created');
        //RETORNA A PAGINA DE USUÁRIOS
        //return self::getusers($request);
    }

    /**
     * Método responsável por retornar o fomulário de edição de um depoimento
     *
     */
    public static function getEditUsers(Request $request, int $id): string
    {
        $obUser = EntityUser::getById($id);

        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/users');
        }

        #CONTEÚDO DA HOME DE USUÁRIOS
        $content = View::render('pages/users/form', [
            'title' => 'Editar usuário',
            'nome' => $obUser->nome,
            'login'=> $obUser->login,
            'email' => $obUser->email,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Editar usuário', $content, 'users');
    }

    public static function setEditUsers(Request $request, int $id)
    {
        $obUser = EntityUser::getById($id);

        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/users');
        }

        //DADOS DO POST
        $postVars = $request->getPostVars();
        $nome = $postVars['nome'] ?? '';
        $email = $postVars['email'] ?? '';
        $senha = $postVars['senha'] ?? '';

        //VALIDAR EMAIL DO USUÁRIO
        $obUserEmail =  EntityUser::getByEmail($email);

        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id) {
            return $request->getRouter()->redirect('/users/' . $id . 'edit?status=duplicated');
        }

        #ATUALIZA A INSTANCIA
        $obUser->nome = $nome;
        $obUser->email = $email;

        #VERIFICAR SE SENHA FOI DIGITADA
        if (!empty($senha)) {
            if (strlen($senha) <= 5) {
                return $request->getRouter()->redirect('/users/' . $obUser->id . '/edit?status=errorSenha');
            }
            $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        }

        $obUser->update();

        return $request->getRouter()->redirect('/users/' . $obUser->id . '/edit?status=updated');
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     *
     */
    public static function getDeleteUsers(Request $request, int $id): string
    {
        $obUser = EntityUser::getById($id);

        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/users');
        }

        #CONTEÚDO DA HOME DE USUÁRIOS
        $content = View::render('pages/users/delete', [
            'title' => 'Excluir usuário',
            'nome' => $obUser->nome,
            'email' => $obUser->email,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Excluir registro', $content, 'users');
    }

    /**
     * Método responsavel por excluir um depoimento
     *
     */
    public static function setDeleteUsers(Request $request, int $id)
    {
        $obUser = EntityUser::getById($id);

        if (!$obUser instanceof EntityUser) {
            $request->getRouter()->redirect('/users');
        }

        #EXCLUI O REGISTRO
        $obUser->delete();

        return $request->getRouter()->redirect('/users?status=deleted');
    }
}
