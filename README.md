# Gestão de Contratos PHP

Durante o desenvolvimento do teste, utilizei dois métodos para demonstrar meu domínio da linguagem, considerando o tempo disponibilizado:

Simulação do Sistema: Desenvolvi uma simulação simples de um sistema com tela de login e template, implementando as consultas solicitadas na tela de relatórios, localizada em ./app/Controller/Admin/ReportController.php. Para este projeto, utilizei um miniframework de minha autoria, criado há algum tempo com o objetivo de facilitar o desenvolvimento de painéis administrativos de sites. A estrutura é simples, sendo que o único componente mais avançado é a biblioteca illuminate/database, usada para gerenciar o banco de dados.

Solução Simplificada: Também criei uma solução mais direta e objetiva, conforme solicitado no teste, que está disponível na raiz do projeto, na pasta ./simplificado. Dentro dessa pasta, estão tanto o script PHP quanto os scripts SQL requeridos.

Deixei o código mais comentado possível para que fique tudo bem entendido

## Para rodar o projeto será necessário os passos a baixo

- Executar o comando "composer install" (lembrar que precisa instalar o COMPOSER na máquina) no terminao aberto na pasta do projeto;
- Renomei o arquivo .env.production para .env;
- Descomente as informações do mesmo;
- Rode o comando no terminal => php -S localhost:5000 para iniciar o serviço do pequeno sistema;
- Pegue o arquivo "gestao_contratos_db.sql" e importe em seu ambiente MySQL;
- Na raiz do projeto encontrará o script para criar o banco com nome de "gestao_contratos_db.sql";

- USUÁRIO: master, SENHA: 123456.

