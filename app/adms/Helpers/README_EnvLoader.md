# Helper EnvLoader - Carregamento de Variáveis de Ambiente

## Descrição

O `EnvLoader` é um helper que garante o carregamento correto das variáveis de ambiente (arquivo `.env`) em scripts que podem ser executados fora do fluxo principal da aplicação.

## Quando Usar

Use o `EnvLoader` quando:

1. **Scripts CLI** - Scripts executados via linha de comando
2. **Jobs/Processos em Background** - Scripts executados por cron ou agendadores
3. **Scripts de Manutenção** - Scripts de backup, limpeza, etc.
4. **Testes** - Scripts de teste que precisam das variáveis do .env
5. **Scripts Isolados** - Qualquer script que pode ser executado diretamente

## Como Usar

### Método 1: Carregamento Simples

```php
<?php
// No início do seu script
require_once __DIR__ . '/../../Helpers/EnvLoader.php';
\App\adms\Helpers\EnvLoader::load();

// Agora você pode usar $_ENV['VARIAVEL']
```

### Método 2: Carregamento com Timezone

```php
<?php
// No início do seu script
require_once __DIR__ . '/../../Helpers/EnvLoader.php';
\App\adms\Helpers\EnvLoader::loadWithTimezone();

// Carrega o .env e define o timezone automaticamente
```

### Método 3: Verificação Condicional

```php
<?php
// Verifica se o .env já foi carregado antes de carregar
if (!isset($_ENV['DB_HOST'])) {
    require_once __DIR__ . '/../../Helpers/EnvLoader.php';
    \App\adms\Helpers\EnvLoader::load();
}
```

## Exemplos Práticos

### Script CLI

```php
<?php
// app/adms/Scripts/backup_database.php

require_once __DIR__ . '/../Helpers/EnvLoader.php';
\App\adms\Helpers\EnvLoader::loadWithTimezone();

// Agora pode usar as variáveis do .env
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
// ... resto do código
```

### Script de Processamento

```php
<?php
// app/adms/Scripts/process_import.php

// Carregar .env se executado isoladamente
if (!isset($_ENV['DB_HOST'])) {
    require_once __DIR__ . '/../Helpers/EnvLoader.php';
    \App\adms\Helpers\EnvLoader::load();
}

// ... resto do código
```

## Métodos Disponíveis

### `EnvLoader::load()`

Carrega o arquivo `.env` se ainda não foi carregado.

**Retorna:** `bool` - `true` se carregado com sucesso, `false` caso contrário

### `EnvLoader::loadWithTimezone()`

Carrega o arquivo `.env` e define o timezone padrão da aplicação.

**Retorna:** `bool` - `true` se carregado com sucesso, `false` caso contrário

## Tratamento de Erros

O helper inclui tratamento de erros e logging:

- Verifica se o autoload existe
- Verifica se o arquivo `.env` existe
- Registra erros no log do PHP
- Retorna `false` em caso de falha

## Boas Práticas

1. **Sempre use o helper** em scripts que podem ser executados isoladamente
2. **Verifique o retorno** dos métodos para tratar erros
3. **Use `loadWithTimezone()`** quando precisar do timezone configurado
4. **Não execute controllers/repositórios diretamente** - sempre use um ponto de entrada que carregue o `.env`

## Arquivos Já Atualizados

Os seguintes arquivos já foram atualizados para usar o `EnvLoader`:

- `app/adms/Controllers/Services/NotificacaoTreinamentosCli.php`
- `app/adms/Controllers/receive/ProcessFileReceipts.php`
- `check_permissions.php`

## Troubleshooting

### Erro: "Undefined array key"

**Causa:** O arquivo `.env` não foi carregado.

**Solução:** Adicione o carregamento do `EnvLoader` no início do script.

### Erro: "Autoload não encontrado"

**Causa:** O script está sendo executado de um diretório incorreto.

**Solução:** Verifique se o caminho para o `vendor/autoload.php` está correto.

### Erro: "Arquivo .env não encontrado"

**Causa:** O arquivo `.env` não existe ou o caminho está incorreto.

**Solução:** Verifique se o arquivo `.env` existe na raiz do projeto. 