<?php

require __DIR__.'/includes/app.php';

use App\Http\Router;

$obRouter = new Router(URL);

//INCLUI AS ROTAS DE PÁGINAS
include __DIR__.'/routes/pages.php';

//INCLUI AS ROTAS DE PÁGINAS (ADMIN)
include __DIR__.'/routes/api.php';

//IMPRIME O REPONSE DA ROTA
$obRouter->run()->sendResponse();
