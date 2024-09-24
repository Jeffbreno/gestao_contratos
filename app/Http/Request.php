<?php

namespace App\Http;

use App\Model\Entity\User;

class Request
{
    // public User $user;
    /**
     * Instancia do Router
     * 
     */
    private Router $router;
    /**
     * Método http da requisição
     * 
     */
    private string $httpMethod;

    /**
     * Uri da página
     *
     */
    private string $uri;

    /**
     * Parametros da URL ($_GET)
     *
     */
    private array $queryParams;

    /**
     * Váriaveis recebidas do POST da página
     *
     */
    private array $postVars;

    /**
     * Cabeçalho da requisição
     *
     */
    private array $headers;

    /**
     * Para declarar variaveis dinamicas
     */
    private array $propriedades;

    public function __construct($router)
    {
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
        $this->setPostVars();
    }

    /**
     * A função mágica __set() permite adicionar comportamento personalizado ao definir propriedades dinâmicas
     * 
     */
    public function __set($nome, $valor): void
    {
        $this->propriedades[$nome] = $valor;
    }

    /**
     * Quando você tenta acessar uma propriedade inacessível, o PHP chama automaticamente o método __get() se ele estiver definido na classe
     * 
     */
    public function __get($nome): mixed
    {
        if (isset($this->propriedades[$nome])) {
            return $this->propriedades[$nome];
        } else {
            return null;
        }
    }

    /**
     * Método reponsável por definir as variáveis do POST
     */
    private function setPostVars()
    {
        #VERIFICA O MÉTODO DA REQUEST
        if ($this->httpMethod == 'GET') return false;

        #POST PADRÃO
        $this->postVars = $_POST ?? [];

        #POST JSON
        $inputRaw = file_get_contents('php://input');
        $this->postVars = (strlen($inputRaw) and empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;
    }

    /**
     * Método reponsável por definir a URI
     */
    private function setUri(): void
    {
        //URI COMPLETA (COM GETS)
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        //REMOVE GETS DA URI
        $xURI = explode('?', $this->uri);
        $this->uri = $xURI[0];
    }

    /**
     * Método responsável por retornar a instancia de Router
     * 
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * Método responsável por retornar o método HTTP da requisição
     *
     */
    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    /**
     * Método responsável por retornar a URI da requisição
     *
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Método responsável por retornar os HEADERS da requisição
     *
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Método responsável por retornar os parâmetros da URL da requisição
     *
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;;
    }

    /**
     * Método responsável por retornar as variáveis POST da requisição
     *
     */
    public function getPostVars(): array
    {
        return $this->postVars;
    }
}
