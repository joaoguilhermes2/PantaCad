# PantaCad

Sistema de cadastro dos usuarios com conexao inicial em PostgreSQL via PHP.

## Estrutura inicial

- `public/index.php`: front controller da aplicacao
- `app/Controllers`: controladores da aplicacao
- `app/Models`: regras de acesso aos dados
- `app/Views`: telas e layouts
- `core/helpers.php`: funcoes de apoio para views e redirecionamento
- `config/env.php`: carregamento do arquivo `.env`
- `config/database.php`: conexao central com `PDO`

## Configuracao

As credenciais do banco estao no arquivo `.env`.

## Como usar

Suba o projeto no Apache e acesse a pasta `public`.

Exemplo de uso em outros arquivos PHP:

```php
require_once __DIR__ . '/../config/database.php';

$pdo = database();
```
