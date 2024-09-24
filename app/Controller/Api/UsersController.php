<?php

namespace App\Controller\Api;

use App\Http\Request;
use App\Model\Entity\User as EntityUser;
use Exception;

class UsersController extends ApiController
{

    /** 
     * Método responsável por obter a renderização dos itens de depoimentos para a página
     * 
     */
    private static function getUserItems(Request $request, &$obPagination): array
    {
        //DEPOIMENTOS
        $resultItems = [];

        // RESULTADO DA PÁGINA
        $queryUsers = EntityUser::orderBy('id', 'desc')->get();

        //GET DA PAGINA ATUAL 
        $queryParams = $request->getQueryParams();

        $currentPage = $queryParams['page'] ?? 1;

        //Retorna link para paginação
        $obPagination = PaginationController::getPagination($request, $queryUsers, 5, $currentPage);

        // $Pagination = Page::getLinkPages($request, $queryParams, $result);

        foreach ($obPagination as $users) {
            $resultItems[] = [
                'id' => (int)$users->id,
                'nome' => $users->nome,
                'email' => $users->email
            ];
        }

        //RETORNA OS DEPOIMENTOS
        return $resultItems;
    }

    /**
     * Método responsável por retornar os depoimentos
     *
     */
    public static function getUsers(Request $request): array
    {
        return [
            'usuarios' => self::getUserItems($request, $obPagination),
            'paginacao' => PaginationController::getPage($request, $obPagination)
        ];
    }

    /**
     * Método responsável por retornar os depoimentos
     *
     */
    public static function getUser(Request $request, int $id): array
    {
        /**
         * Trabalhando com váriavel tipada, esse erro seria tratado se o campo fosse mixed
         * {
         * #Valida se é um ID valido
         * if (!is_numeric($id)) {
         *    throw new Exception("O registro '" . $id . "' informado não é valido", 400);
         *   }
         * }
         */

        #Busca depoimento
        $obUser = EntityUser::getById($id);
        #Valida se o depoimento existe
        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuário " . $id . " não foi encontrado", 404);
        }

        #Retorna os detalhes do depoimento
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * Método responsável por retornar usuário atualmente conectado
     */

    public static function getCurrentUser(Request $request): array
    {
        #USUÁRIO ATUAL
        $obUser = $request->user;

        #RETORNA OS DETALHES DO DEPOIMENTO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     *
     */
    public static function setNewUser(Request $request): array
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();

        #VALIDAR SE EXISTE EMAIL JÁ CADASTRADO
        $obUserEmail =  EntityUser::getByEmail($postVars['email']);

        if ($obUserEmail instanceof EntityUser) {
            throw new Exception("Email '" . $postVars['email'] . "' já cadastrado na base de dados", 400);
        }

        #VALIDAR CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) or !isset($postVars['email']) or !isset($postVars['senha'])) {
            throw new Exception("Os campos 'nome' , 'email' e 'senha' são obrigatórios", 400);
        }

        #NOVO DEPOIMENTO
        $obUser = new EntityUser;
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);

        $obUser->save();

        #RETORNA OS DELHES DO DEPOIMENTO CADASTRADO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }

    public static function setEditUser(Request $request, int $id): array
    {
        $obUser = EntityUser::getById($id);

        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuário '" . $id . "' não foi encontrado", 400);
        }

        //DADOS DO POST
        $postVars = $request->getPostVars();

        //VALIDAR EMAIL DO USUÁRIO
        $obUserEmail =  EntityUser::getByEmail($postVars['email']);

        if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $id) {
            throw new Exception("Email '" . $postVars['email'] . "' já cadastrado na base de dados", 400);
        }

        #ATUALIZA O USUÁRIO
        $obUser->nome = $postVars['nome'];
        $obUser->email = $postVars['email'];

        #VERIFICAR SE SENHA FOI DIGITADA
        if (!empty($senha)) {
            if (strlen($senha) <= 5) {
                throw new Exception("Campo senha deve conter no mínimo 6 digitos", 400);
            }
            $obUser->senha = password_hash($senha, PASSWORD_DEFAULT);
        }

        $obUser->update();

        #RETORNA OS DELHES DO DEPOIMENTO ATUALIZADO
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'email' => $obUser->email
        ];
    }
    /**
     * Método responsavel por excluir um depoimento
     *
     */
    public static function setDeleteUser(Request $request, int $id): array
    {
        $obUser = EntityUser::getById($id);

        if (!$obUser instanceof EntityUser) {
            throw new Exception("O usuário " . $id . " não doi encontrado", 404);
        }

        #IMPEDE A EXECUÇÃO DO PROPRIO CADASTRO
        if ($obUser->id == $request->user->id) {
            throw new Exception("O usuário não pode excluir cadastro atualmente conectado", 404);
        }

        #EXCLUI O REGISTRO
        $obUser->delete();

        #RETORNA O SUCESSO DA EXLUSÃO
        return [
            'success' => true
        ];
    }
}
