<?php

namespace App\Http\Middleware;

use App\Http\Request;
use App\Http\Response;
use Closure;
use App\Utils\Cache\File as CacheFile;

class Cache
{
    private function isCacheable(Request $request): bool
    {
        //VALIDAR O TEMPO DE CACHE
        if ($_ENV['CACHE_TIME'] <= 0) {
            return false;
        }

        //VALIDA O MÉTODO DA REQUISIÇÃO
        if ($request->getHttpMethod() != 'GET') {
            return false;
        }

        //VALIDA O HEADER DE CACHE
        $headers = $request->getHeaders();

        if (isset($headers['Cache-Control']) and $headers['Cache-Control'] == 'no-cache') {
            return false;
        }

        //CACHIÁVEL
        return true;
    }

    /**
     * Método responsável por retornar a hash do cache
     */
    private function getHash(Request $request): string
    {
        //URI DA ROTA
        $uri = $request->getRouter()->getUri();

        //QUERY PARAMS
        $queryParms = $request->getQueryParams();
        $uri .= !empty($queryParms) ? '?' . http_build_query($queryParms) : '';

        //REMOVE AS BARRAS E RETORNA A HASH
        return rtrim('route-' . preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/')), '-');
    }
    /**
     * Método reponsável por executar o middleware
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        //VERIFICA SE A ROTA ATUAL É CACHEÁVEL
        if (!$this->isCacheable($request)) return $next($request);

        //HASH DO CACHE
        $hash = $this->getHash($request);

        //RETORNA OS DADOS DO CACHE
        return CacheFile::getCache($hash, $_ENV['CACHE_TIME'], function () use ($request, $next) {
            return $next($request);
        });
    }
}
