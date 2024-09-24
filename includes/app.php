<?php

date_default_timezone_set('America/Recife');

require __DIR__ . '/../vendor/autoload.php';

use App\Utils\View;
use Dotenv\Dotenv;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Http\Middleware\Queue as MiddlewareQueue;

#CARREGA VARIÁVEIS DE AMBIENTE
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

#Configuração do banco de dados
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

define('URL', $_ENV['URL']);

#DEFINE  VALOR PADRÃO DAS VARIÁVEIS
View::init(['URL' => URL]);

#/DEFINE O MAPEAMENTO DE MIDDLEWARES
MiddlewareQueue::setMap([
    'maintenance' => \App\Http\Middleware\Maintenance::class,
    'required-admin-logout' => \App\Http\Middleware\RequireAdminLogout::class,
    'required-admin-login' => \App\Http\Middleware\RequireAdminLogin::class,
    'api' => \App\Http\Middleware\Api::class,
    'user-auth' => \App\Http\Middleware\UserAuth::class,
    'user-jwt-auth' => \App\Http\Middleware\JWTAuth::class,
    'cache' => \App\Http\Middleware\Cache::class,
]);

#DEFINE O MAPEAMENTO DE MIDDLEWARES PADRÕES (EXUCUTADOS EM TODAS AS ROTAS)
MiddlewareQueue::setDefault([
    'maintenance'
]);
