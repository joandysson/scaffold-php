### Project Scaffold PHP

Este projeto fornece uma estrutura inicial simples em PHP para aplicações que utilizam Docker. O objetivo é facilitar o início de novos projetos web com uma base limpa e organizada.

### Pre-requisitos

- Docker
- Docker Compose
- Opcional: `make` para alguns comandos auxiliares

### Tecnologias utilizadas

- PHP `8.2`
- MySQL `10`
- Adminer para gerência do banco de dados

O `BaseModel` usa uma conexão com o banco de dados que é aberta apenas quando
necessária, evitando conexões antecipadas.

### Portas dos containers

|Container|Porta|
|---------|-----|
|`PHP`|`80`|
|`Mysql`|`3306`|
|`Adminer`|`8080`|

#### Configurações de ambiente

As variáveis de ambiente estão documentadas em `.env.exemple`. Copie este arquivo para `.env` e ajuste os valores conforme sua necessidade. Entre elas, `APP_DEBUG` controla a exibição de erros e `LOG_DRIVER` define o tipo de logger utilizado (`file` ou `error_log`).

#### Exemplo de roteamento

O roteador oferece uma sintaxe simples para registrar rotas. No arquivo `routes/web.php` você pode declarar rotas apontando para controladores ou funções anônimas:

```php
use App\Config\Router\Router;

Router::get('/', 'HomeController:home');
Router::get('/blog/{id}', 'HomeController:blog');

// Agrupando rotas com um prefixo
Router::prefix('/api');
Router::get('/status', function () {
    echo 'API ok';
});
```

Para criar novos controladores basta adicioná-los em `app/Controllers` e registrar as rotas correspondentes. Os modelos ficam em `app/Models` e podem herdar de `BaseModel`.

#### Rotas de exemplo

|Método|Parâmetros|Descrição|
|---|---|---|
|`GET`|`/`|Página de exemplo.|

### Estrutura do código

```
app/        Código PHP (controllers, models, configurações)
public/     Arquivos acessíveis publicamente e views
routes/     Definição das rotas da aplicação
storage/    Arquivos gerados pela aplicação
tests/      Testes PHPUnit
docker/     Imagens e configurações do Docker
```

### Como instalar

1. Clone este repositório.
2. Copie `.env.exemple` para `.env` e configure as variáveis.
3. Execute `docker-compose up -d` para subir os containers.
4. Caso a pasta `vendor/` não exista, entre no container `app` e rode `composer install`.
5. A aplicação estará disponível em `http://localhost`.

### Comandos úteis

- `composer test` executa a suite de testes.
- `composer phpcs` roda o linter do código.
- `composer phpstan` executa a análise estática.
- `composer audit` verifica vulnerabilidades nas dependências.
- `make cron name=ExempleCron` executa uma tarefa agendada de exemplo.
- `make ci` roda todas as verificações e testes dentro do container.

### Tarefas agendadas

Tarefas de cron são registradas no arquivo `app/Config/cron.php`. Para executar
uma delas manualmente utilize:

```sh
php run-cron.php NomeDaTarefa
```

O script `cron.sh` é um facilitador que pode ser utilizado em servidores para 
agendar execuções recorrentes.
