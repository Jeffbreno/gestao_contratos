<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Closure;
use Exception;

class Queue
{

    /**
     * Mapear middleware
     *
     */
    private static array $map;

    /**
     * Mapeamento de middlewares que serão carregados em todas as rotas
     *
     */
    private static array $default;

    /**
     * Fila de middleware a serem executados
     *
     */
    private array $middlewares;

    /**
     * função de execução do controlador
     *
     */
    private Closure $controller;

    /**
     * Argumentos da função do controlador
     *
     */
    private array $controllerArgs;

    /**
     * Método responsavél por contruir a classe de fila de middlewares
     *
     */
    public function __construct(array $middlewares, Closure $controller, array $controllerArgs)
    {
        $this->middlewares = array_merge(self::$default, $middlewares);
        $this->controller = $controller;
        $this->controllerArgs = $controllerArgs;
    }

    /**
     * Método responsável por definir o mapeamento de middlewares
     *
     */
    public static function setMap(array $map): void
    {
        self::$map = $map;
    }

    /**
     * Método responsável por definir o mapeamento de middlewares padrões
     *
     */
    public static function setDefault(array $default): void
    {
        self::$default = $default;
    }

    /**
     * Método responsável por executar o próximo nível da fila de middlewares
     *
     */
    public function next(Request $request): Response
    {
        //VERIFICA SE A FILA ESTA VAZIA
        if (empty($this->middlewares)) return call_user_func_array($this->controller, $this->controllerArgs);

        //MIDDLEWARE
        $middleware = array_shift($this->middlewares);
        //VERIFICA O MAPEAMENTO 
        if (!isset(self::$map[$middleware])) {
            throw new Exception("Problema ao processar o middleware da requisição", 500);
        }

        //NEXT
        $queue = $this;
        $next = function ($request) use ($queue) {
            return $queue->next($request);
        };

        //EXECUTA O MIDDLEWARE
        return (new self::$map[$middleware])->handle($request, $next);
    }
}
