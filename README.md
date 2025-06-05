## Requisitos

* PHP 8.3 ou superior;
* MySQL 8.0 ou superior;
* Composer;

## Como rodar o projeto baixado

Duplicar o arquivo ".env.exemple" e renomear para ".env".<br>
Alterar no arquivo .env as credenciais do banco de dados.<br>
Criar o banco de dados com a COLLACTION "utf8mb4_unicode_ci".<br>
Alterar no arquivo .env o endereço da aplicação na variável de ambiente URL_ADM.<br>
Alterar no arquivo .env as credenciais do servidor para enviar email.<br>
Enviar e-mail gratuito via SMTP https://www.iagente.com.br/solicitacao-conta-smtp/origin/celke

Instalar as dependências.
```
composer install
```
Executar as migrations.
```
vendor/bin/phinx migrate -c database/phinx.php
```
Executar as seed
```
vendor/bin/phinx seed:run -c database/phinx.php
```

Acessar o projeto: [Acessar](http://localhost/tiaraju).


## Sequencia para criar o projeto
Criar o arquivo composer.json com a instrução básica.
```
composer init
```
Instalar a dependencia Monolog, biblioteca PHO que permite criar arquivo de log.
```
composer require monolog/monolog
```

Instalar a biblioteca gerenciar variáveis de ambiente
```
composer require vlucas/phpdotenv
```
Instalar a biblioteca para criar/executar migration e seed.
```
composer require robmorgan/phinx
```
Criar o arquivo "phinx.php" com as configurações e alterar as mesmas.
```
vendor/bin/phinx init -f php
```

Testar as configurações do phinx
```
vendor/bin/phinx test 
```
Criar o diretório database para adicionar o arquivo phinx
```
mkdir database/
```

Criar o diretório para as migrations.
```
mkdir database/migrations/
```

Criar a migrations.
```
vendor/bin/phinx create AdmsUsers -c database/phinx.php
```

Executar as migrations.
```
vendor/bin/phinx migrate -c database/phinx.php
```

Executar o rollback na ultima migration - caso necessário reverter as alterações realizadas
```
vendor/bin/phinx rollback -c database/phinx.php
```

Criar o diretório seeds
```
mkdir databse/seeds/
```

Criar a seed
```
vendor/bin/phinx seed:create AddAdmsUsers -c database/phinx.php
```

Executar as seed
```
vendor/bin/phinx seed:run -c database/phinx.php
```

Instalar a biblioteca para vallidar o formulario.
```
composer require "rakit/validation"
```

Instalar a biblioteca phpmailer para enviar email.
```
composer require phpmailer/phpmailer
```


## Como usar o GitHub
Baixar os arquivos do Git.
```
git clone --branch <branch_name> <repository_url> .
```

Definir as configurações do usuário.
```
git config --local user.name mendes31 Rafael Mendes
```
```
git config --local user.email raffaell_mendez@hotmail.com
```

Verificar a branch.
```
git branch 
```

Baixar as atualizações.
```
git pull
```

Adicionar todos os arquivos modificados no staging area - área de preparação.
```
git add .
```

commit representa um conjunto de alterações em um ponto específico da história do seu projeto, registra apenas as alterações adicionadas ao índice de preparação.
O comando -m permite que insira a mensagem de commit diretamente na linha de comando.
```
git commit -m "Descrição do commit"
```

Enviar os commits locais, para um repositório remoto.
```
git push <remote> <branch>
git push origin dev-master
```



## Lista de erros
001 - DBConnection.php - Erro de conexão com o banco de dados
002 - LoadPageAdm.php - Não encontrou a pagina
003 - LoadPageAdm.php - Não encontrou a controller
004 - LoadPageAdm.php - Não encontrou o metodo
005 - LoadViewService.php - Não encontrou a VIEW
.






