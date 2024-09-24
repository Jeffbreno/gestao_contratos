<?php

namespace App\Utils\Cache;

use Closure;

class File
{

    /**Método reponsável por retornar o caminho até o arquivo de cache */
    private static function getFilePath(string $hash): string
    {
        $dir = $_ENV['CACHE_DIR'];

        #VERIFICA A EXISTÊNCIA DO DIRETÓRIO
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        #RETORNA O CAMINHO ATÉ O ARQUIVO
        return $dir . '/' . $hash;
    }
    /**
     * Métpdp reponsável por guardar informações no cache
     */
    private static function storageCache(string $hash, mixed $content): bool
    {
        #SERIALIZA O RETORNO
        $serialize = serialize($content);

        #OBTEM O CAMINHO ATÉ O ARQUIVO DE CACHE
        $cacheFile = self::getFilePath($hash);

        #GRAVA AS INFORMAÇÕES NO ARQUIVO
        return file_put_contents($cacheFile, $serialize);
    }

    /**
     * Método reponsável por retornar o conteúdo gravado no cache
     */
    private static function getContentCache(string $hash, int $expiration): mixed
    {
        #OBTÉM O CAMINHO DO ARQUIVO
        $cacheFile = self::getFilePath($hash);

        #VERIFICA A EXISTÊNCIA DO ARQUIVO
        if (!file_exists(($cacheFile))) {
            return false;
        }

        #VALIDA A EXPIRAÇÃO DO CACHE
        $createTime = filectime($cacheFile);
        $diffTime = time() - $createTime;

        if ($diffTime > $expiration) {
            return false;
        }

        #RETORNA O DAO REAL
        $serialize = file_get_contents($cacheFile);
       return unserialize($serialize);
    }
    /**
     * Método responsável por obter uma informação do cache
     */
    public static function getCache(string $hash, int $expiration, Closure $function): mixed
    {
        #VERIFICA O CONTEÚDO GRAVADO
        if ($contant = self::getContentCache($hash, $expiration)) {
            return $contant;
        }

        #EXECUÇÃO DA FUNÇÃO
        $content = $function();


        #GRAVA O RETORNO NO CACHE
        self::storageCache($hash, $content);

        //RETORNA O CONTEÚDO
        return $content;
    }
}
