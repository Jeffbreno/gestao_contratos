<?php

namespace App\Http;

class Response
{
    /**
     * Código do Status HTTP
     * 
     */
    private int $httpCode = 200;

    /**
     * Cabeçalho do Response
     * 
     */
    private array $header = [];

    /**
     * Tipo de conteúdo que está sendo retornado
     * 
     */
    private string $contentType = 'text/html';

    /**
     * Conteúdo do Response
     * 
     */
    private mixed $content;

    /**
     * Método responsável por iniciar a classe e definir  os valores
     * 
     */
    public function __construct(int $httpCode, mixed $content, string $contentType = 'text/html')
    {
        $this->httpCode = $httpCode;
        $this->content = $content;
        $this->setContentType($contentType);
    }

    /**
     * Método responsável por alterar o content type do response
     * 
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }

    /**
     * Método responsável por adicionar um registro no cabeçalho de response
     * 
     */
    public function addHeader(string $key, string $value): void
    {
        $this->header[$key] = $value;
    }

    /**
     * Método responsável por enviar os headers para o navegador
     */
    private function sendHeader(): void
    {
        //STATUS 
        http_response_code($this->httpCode);

        //ENVIAR HEADERS
        foreach ($this->header as $key => $value) {
            header($key . ': ' . $value);
        }
    }

    public function sendResponse(): void
    {
        //ENVIA OS HEADERS
        $this->sendHeader();

        //IMPRIME O CONTEUDO
        switch ($this->contentType) {
            case 'text/html':
                echo $this->content;
                break;
            case 'application/json':
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE |  JSON_UNESCAPED_SLASHES);
                break;
        }
    }
}
