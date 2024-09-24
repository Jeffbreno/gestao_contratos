<?php

namespace App\Controller\Admin;

use App\Http\Request;
use App\Utils\View;
use App\Model\Entity\Inscricao as EntityIncritos;
use App\Model\Entity\Categoria as EntityCategoria;

class InscricoesController extends PageController
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
    private static function getInscritoItems(Request $request, &$obPagination): string
    {
        //DEPOIMENTOS
        $resultItems = '';

        // RESULTADO DA PÁGINA
        $queryInscritos = EntityIncritos::where('dt_cadastro','>=','2023-12-31 00:00:00')->orderBy('id', 'desc')->get();

        //Seta e Retorna intens por página
        $obPagination = PageController::setPaginator($request, $queryInscritos, 10);

        foreach ($obPagination as $inscrito) {
            $resultItems .= View::render('admin/inscritos/item', [
                'id' => $inscrito->id,
                'nome' => $inscrito->nome,
                'email' => $inscrito->email,
                'cpf' => $inscrito->cpf,
                'status_desc' => $inscrito->status_pag === 'P' ? 'Pago' : 'Aberto',
                'status_pag' =>  $inscrito->status_pag,
                'status_pag_cor' => $inscrito->status_pag === 'P' ? 'success' : 'danger',
                'dt_cadastro' => date('d/m/Y H:i', strtotime($inscrito->dt_cadastro))
            ]);
        }

        //RETORNA OS DEPOIMENTOS
        return $resultItems;
    }

    public static function getModal($id): string
    {
        // $obInscrito = EntityIncritos::find($id);
        // $obCategoria = EntityCategoria::find($obInscrito->categoria);

        $obInscrito = EntityIncritos::join(
            'categorias',
            'categorias.id',
            '=',
            'usuarios_inscritos.categoria'
        )->where('usuarios_inscritos.id', $id)
            ->select(['categorias.titulo', 'usuarios_inscritos.*'])
            ->first();




        if ($obInscrito->genero == 'M') {
            $obInscrito->genero = 'MASCULINO';
        } else {
            $obInscrito->genero = 'FEMININO';
        }

        if ($obInscrito->status_pag == 'P') {
            $obInscrito->status_pag = 'PAGA';
        } else {
            $obInscrito->status_pag = 'ABERTA';
        }

        $obInscrito->dt_nascimento = date('d/m/Y', strtotime($obInscrito->dt_nascimento));
        $obInscrito->dt_cadastro = date('d/m/Y H:i', strtotime($obInscrito->dt_cadastro));

        return $obInscrito;
    }

    /**
     * Método responsável por renderizar a view de home do painel
     *
     */
    public static function getInscrito(Request $request): string
    {


        #CONTEÚDO DA HOME DE DEPOIMENTOS
        $content = View::render('admin/inscritos/index', [
            'botaolink' => URL . '/admin/inscritos/new',
            'nomebotao' => 'Cadastrar novo inscrito',
            'descricao' => 'Lista de inscritos cadastrados no site',
            'itens' => self::getInscritoItems($request, $obPagination),
            'pagination' => parent::getPagination($request, $obPagination),
            'status' => self::getStatus($request),
            'modal' => View::render('admin/inscritos/modal')
        ]);

        #RETORNA A PÁGINA COMPLETA
        return parent::getPainel('Lista Inscritos', $content, 'inscritos');
    }

    /**
     * Método responsável por retornar o formulário de cadastro de um novo depoimento
     *
     */
    public static function getNewInscrito(Request $request): string
    {

        $genero = '
            <option value="M">Masculino</option>
            <option value="F">Feminino</option>
            <option value="O">Não informar</option>
        ';

        $camisa = '
            <option value="PP">PP (63cm X 43cm)</option>
            <option value="P">P (65cm X 46cm)</option>
            <option value="M">M (67cm X 50cm)</option>
            <option value="G">G (69cm X 54cm)</option>
            <option value="GG">GG (71cm X 57cm)</option>
            <option value="XGG">XGG (75cm X 60cm)</option>
        ';

        $distancia = '
            <option value="5km">5 km</option>
            <option value="10km">10 km</option>
            <option value="200m">200 m (kids)</option>
        ';

        $queryCategorias = EntityCategoria::orderBy('titulo', 'asc')->get();
        $categoria = '';
        foreach ($queryCategorias as $categorias) {
            $categoria .= '<option value="' . $categorias->id . '">' . $categorias->titulo . '</option>';
        }

        #CONTEÚDO DA HOME DE DEPOIMENTOS
        $content = View::render('admin/inscritos/form', [
            'nome' => null,
            'nome_responsavel' => null,
            'email' => null,
            'genero' => $genero,
            'cpf' => null,
            'categoria' => $categoria,
            'dt_nascimento' => null,
            'celular' => null,
            'logradouro' => null,
            'numero' => null,
            'complemento' => null,
            'bairro' => null,
            'cidade' => null,
            'uf' => null,
            'distancia' => $distancia,
            'camisa' => $camisa,
            'equipe' => null,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Cadastrar Novo Inscrito', $content, 'inscritos');
    }

    public static function setNewInscrito($request): string
    {
        //DADOS DO POST
        $postVars = $request->getPostVars();
        $obInscrito = new EntityIncritos;

        if (!$obInscrito instanceof EntityIncritos) {
            $request->getRouter()->redirect('/admin/inscritos');
        }

        #POST VARS
        $postVars = $request->getPostVars();

        //LAÇO PARA INCREMENTAR TODAS AS KEY, PRECISANDO SER IGUAL COM O QUE ESTA EM BANCO
        foreach ($postVars as $key => $value) {
            $obInscrito->$key = $value;
        }

        //CADASTRAR DADOS
        try {
            $obInscrito->save();
            return $request->getRouter()->redirect('/admin/inscritos/' . $obInscrito->id . '/edit?status=update');
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/admin/inscritos/' . $obInscrito->id . '/edit?status=error');
        }
    }

    /**
     * Método responsável por retornar o fomulário de edição de um depoimento
     *
     */
    public static function getEditInscrito(Request $request, int $id): string
    {
        $obInscrito = EntityIncritos::join(
            'categorias',
            'categorias.id',
            '=',
            'usuarios_inscritos.categoria'
        )->where('usuarios_inscritos.id', $id)
            ->select(['categorias.titulo', 'usuarios_inscritos.*'])
            ->first();

        if (!$obInscrito instanceof EntityIncritos) {
            $request->getRouter()->redirect('/admin/inscritos');
        }

        $genero = '
            <option ' . ($obInscrito->genero === "M" ? "selected" : "") . ' value="M">Masculino</option>
            <option ' . ($obInscrito->genero === "F" ? "selected" : "") . ' value="F">Feminino</option>
            <option ' . ($obInscrito->genero === "O" ? "selected" : "") . ' value="O">Não informar</option>
        ';

        $camisa = '
            <option ' . ($obInscrito->camisa === "PP" ? "selected" : "") . ' value="PP">PP (63cm X 43cm)</option>
            <option ' . ($obInscrito->camisa === "P" ? "selected" : "") . ' value="P">P (65cm X 46cm)</option>
            <option ' . ($obInscrito->camisa === "M" ? "selected" : "") . ' value="M">M (67cm X 50cm)</option>
            <option ' . ($obInscrito->camisa === "G" ? "selected" : "") . ' value="G">G (69cm X 54cm)</option>
            <option ' . ($obInscrito->camisa === "GG" ? "selected" : "") . ' value="GG">GG (71cm X 57cm)</option>
            <option ' . ($obInscrito->camisa === "XGG" ? "selected" : "") . ' value="XGG">XGG (75cm X 60cm)</option>
        ';


        $distancia = '
            <option ' . ($obInscrito->distancia === "5km" ? "selected" : "") . ' value="5km">5 km</option>
            <option ' . ($obInscrito->distancia === "10km" ? "selected" : "") . ' value="10km">10 km</option>
            <option ' . ($obInscrito->distanciad === "200m" ? "selected" : "") . ' value="200m">200 m (kids)</option>
        ';

        $queryCategorias = EntityCategoria::orderBy('titulo', 'asc')->get();
        $categoria = '';
        foreach ($queryCategorias as $categorias) {
            $categoria .= '<option ' . ($obInscrito->categoria === $categorias->id ? "selected" : "") . ' value="' . $categorias->id . '">' . $categorias->titulo . '</option>';
        }

        #CONTEÚDO DA HOME DE DEPOIMENTOS
        $content = View::render('admin/inscritos/form', [
            'nome' => $obInscrito->nome,
            'nome_responsavel' => $obInscrito->nome_responsavel,
            'email' => $obInscrito->email,
            'genero' => $genero,
            'cpf' => $obInscrito->cpf,
            'categoria' => $categoria,
            'dt_nascimento' => $obInscrito->dt_nascimento,
            'celular' => $obInscrito->celular,
            'logradouro' => $obInscrito->logradouro,
            'numero' => $obInscrito->numero,
            'complemento' => $obInscrito->complemento,
            'bairro' => $obInscrito->bairro,
            'cidade' => $obInscrito->cidade,
            'uf' => $obInscrito->uf,
            'distancia' => $distancia,
            'camisa' => $camisa,
            'equipe' => $obInscrito->equipe,
            'status_pag' => $obInscrito->status_pag,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Editar inscrição', $content, 'inscritos');
    }

    public static function setEditInscrito(Request $request, int $id)
    {
        $obInscrito = EntityIncritos::find($id);

        if (!$obInscrito instanceof EntityIncritos) {
            $request->getRouter()->redirect('/admin/inscritos');
        }

        #POST VARS
        $postVars = $request->getPostVars();

        //LAÇO PARA INCREMENTAR TODAS AS KEY, PRECISANDO SER IGUAL COM O QUE ESTA EM BANCO
        foreach ($postVars as $key => $value) {
            $obInscrito->$key = $value;
        }

        //ATUALIZAR DADOS
        try {
            $obInscrito->update();
            return $request->getRouter()->redirect('/admin/inscritos/' . $id . '/edit?status=update');
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/admin/inscritos/' . $id . '/edit?status=error');
        }
    }

    /**
     * Método responsável por retornar o formulário de exclusão de um depoimento
     *
     */
    public static function getDeleteInscrito(Request $request, int $id): string
    {
        $obInscrito = EntityIncritos::find($id);

        if (!$obInscrito instanceof EntityIncritos) {
            $request->getRouter()->redirect('/admin/inscritos');
        }

        #CONTEÚDO DA HOME DE DEPOIMENTOS
        $content = View::render('admin/inscritos/delete', [
            'title' => 'Excluir Registro',
            'nome' => $obInscrito->nome,
            'email' => $obInscrito->email,
            'status' => self::getStatus($request)
        ]);
        return parent::getPainel('Excluir registro', $content, 'inscritos');
    }

    /**
     * Método responsavel por excluir um depoimento
     * @return void
     */
    public static function setDeleteInscrito(Request $request, int $id)
    {
        $obInscrito = EntityIncritos::find($id);

        if (!$obInscrito instanceof EntityIncritos) {
            $request->getRouter()->redirect('/admin/inscritos');
        }

        #QUERY PARAMS
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;

        #EXCLUI O REGISTRO
        $obInscrito->delete();

        //ATUALIZAR DADOS
        try {
            $obInscrito->delete();
            return $request->getRouter()->redirect('/admin/inscritos?status=update?pag=' . $currentPage);
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/admin/inscritos?status=error?pag=' . $currentPage);
        }
    }

    /**
     * Método responsavel por excluir um depoimento
     * @return void
     */
    public static function setStatusPag(Request $request, int $id)
    {
        $obInscrito = EntityIncritos::find($id);

        if (!$obInscrito instanceof EntityIncritos) {
            $request->getRouter()->redirect('/admin/inscritos');
        }

        #QUERY PARAMS
        $queryParams = $request->getQueryParams();
        $currentPage = $queryParams['page'] ?? 1;
        $statusPagamento = ($queryParams['status'] === 'P' ? 'A' : 'P');
        $obInscrito->status_pag = $statusPagamento;

        //ATUALIZAR DADOS
        try {
            $obInscrito->update();
            return $request->getRouter()->redirect('/admin/inscritos?status=update?pag=' . $currentPage);
        } catch (\Exception $e) {
            return $request->getRouter()->redirect('/admin/inscritos?status=error?pag=' . $currentPage);
        }
    }
}
