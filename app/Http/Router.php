<?php

namespace App\Http;

use Closure;
use Exception;
use ReflectionFunction;
use App\Http\Middleware\Queue as MiddlewareQueue;

class Router
{
    /**
     * URL completa do projeto (raiz)
     * 
     */
    private string $url = '';

    /**
     * Prefixo de todas as rotas
     * 
     */
    private string $prefix = '';

    /**
     * Índice de rotas
     * 
     */
    private array $routes = [];

    /**
     * Instancia de Request
     * 
     */
    private Request $request;

    /**
     * Content type padrão do response
     */
    private string $contentType = 'text/html';

    /**
     * Método responsável por iniciar a classe
     * 
     */
    public function __construct(string $url)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Método responsável por alterar o valor do content type
     */
    public function setContentType(string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * Método responsável por definir o prefixo das rotas
     */
    private function setPrefix(): void
    {
        //INFORMAÇÕES DA URL ATUAL
        $parseUrl = parse_url($this->url);

        //DEFINE PREFIXO
        $this->prefix = $parseUrl['path'] ?? '';
    }

    /**
     * Método responsável por adicionar uma rota na classe
     * 
     */
    private function addRoute(string $method, string $route, array $params = []): void
    {
        //VALIDAÇÃO DOS PARÂMETROS
        foreach ($params as $key => $value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        //MIDDLEWARES DA ROTA
        $params['middlewares'] = $params['middlewares'] ?? [];

        //VARIÁVEIS DA ROTA
        $params['variables'] = [];

        //PADRÃO DE VALIDAÇÃO DAS VARIÁVEIS DAS ROTAS
        $patternVariable = '/{(.*?)}/';
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        //REMOVE BARRA DO FINAL DA ROTA
        $route = rtrim($route, '/');

        //PADRÃO DE VALIDAÇÃO DE URL
        $patternRoute = '/^' . str_replace('/', '\/', $route) . '$/';

        //ADICIONA A ROTA DENTRO DA CLASSE
        $this->routes[$patternRoute][$method] = $params;
    }
    /**
     * Método responsável por definir uma rota de GET
     *
     */
    public function get(string $route, array $params = [])
    {
        return $this->addRoute('GET', $route, $params);
    }

    /**
     * Método responsável por definir uma rota de POST
     * 
     */
    public function post(string $route, array $params = [])
    {
        return $this->addRoute('POST', $route, $params);
    }

    /**
     * Método responsável por definir uma rota de PUT
     * 
     */
    public function put($route, $params = [])
    {
        return $this->addRoute('PUT', $route, $params);
    }

    /**
     * Método responsável por definir uma rota de DELETE
     * 
     */
    public function delete(string $route, array $params = [])
    {
        return $this->addRoute('DELETE', $route, $params);
    }

    /**
     * Método responsável por retornar a URI desconsiderando o prefixo
     * 
     */
    public function getUri(): string
    {
        //URI DA REQUEST
        $uri = $this->request->getUri();

        //FATIA A URI COM PREFIXO
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];

        //RETORNA A URI  SEM PREFIXO
        return rtrim(end($xUri),'/');
    }

    /**
     * Método responsável por retornar os dados da rota atual
     * 
     */
    private function getRoute(): array
    {
        //URI
        $uri = $this->getUri();

        //METHOD
        $httpMethod = $this->request->getHttpMethod();

        //VALIDA AS ROTAS
        foreach ($this->routes as $patternRoute => $methods) {
            if (preg_match($patternRoute, $uri, $matches)) {
                //VERIFICA O MÉTODO
                if (isset($methods[$httpMethod])) {
                    //REMOVE A PRIMEIRA POSIÇÃO
                    unset($matches[0]);

                    //VARIÁVEIS PROCESSADAS
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;


                    //RETORNO DOS PARÂMETROS DA ROTA
                    return $methods[$httpMethod];
                }

                //MÉTODO NÂO PERMITIDO/DEFINIDO
                throw new Exception('Método não é permitido', 405);
            }
        }

        //URL NÃO ENCONTRADA
        throw new Exception('URL não encontrada', 404);
    }

    /**
     * Método responsável por executar a rota atual
     * 
     */
    public function run(): Response
    {
        try {
            //OBTEM A ROTA ATUAL
            $route = $this->getRoute();

            //VERIFICAR CONTROLADOR
            if (!isset($route['controller'])) {
                throw new Exception("A URL não pôde ser processada", 500);
            }

            //ARGUMENTO DA FUNÇÃO
            $args = [];

            //REFLECTION
            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            //RETORNA A EXUCUÇÃO DA FILA DE MIDDLEWARE
            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);
        } catch (Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }

    /**
     * Método responsável por retornar a mensagem de erro de acordo com o content type
     */
    private function getErrorMessage(string $message): mixed
    {
        switch ($this->contentType) {
            case 'application/json':
                return ['error' => $message];

            default:
                return $message;
        }
    }

    /**
     * Método responsável por retornar a URL atual
     *
     */
    public function getCurrentUrl(): string
    {
        return $this->url . $this->getUri();
    }

    /**
     * Método responsável por redirecionar a URL
     *
     */
    public function redirect(string $route): void
    {
        $url = $this->url . $route;

        header('location: ' . $url);
        exit;
    }
}
