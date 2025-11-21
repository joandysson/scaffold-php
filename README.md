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

As variáveis de ambiente estão documentadas em `.env.example`. Copie este arquivo para `.env` e ajuste os valores conforme sua necessidade. A tabela a seguir explica as principais chaves:

|Variável|Descrição|
|---|---|
|`APP_URL`|Endereço base da aplicação.|
|`DB_DRIVER`, `DB_NAME`, `DB_HOST`, `DB_USER`, `DB_PASSWORD`|Configurações do banco de dados MySQL utilizado pelo `BaseModel`.|
|`APP_STAGE`|Fase do projeto (`local`, `prod`, etc.).|
|`APP_DEBUG`|Habilita a exibição de erros no navegador quando definido como `true`.|
|`LOG_DRIVER`|Define onde os logs são gravados (`file` gera arquivos em `storage/logs/`, `error_log` envia para o log do PHP).|

Há chaves extras como `SAFE_BROWSING_URL` que servem como exemplo para integrações futuras e podem ser removidas se não forem necessárias.

#### Exemplo de roteamento

O roteador oferece uma sintaxe simples para registrar rotas. No arquivo `routes/web.php` você pode declarar rotas apontando para controladores ou funções anônimas:

```php
use Config\Router\Router;

Router::get('/', 'HomeController:home');
Router::get('/blog/{id}', 'HomeController:blog');

// Agrupando rotas com um prefixo
Router::prefix('/api');
Router::get('/status', function () {
    echo 'API ok';
});
```

Para criar novos controladores basta adicioná-los em `app/Controller` e registrar as rotas correspondentes. Os modelos ficam em `app/Model` e podem herdar de `BaseModel`.

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

* `app/Controller` contém as classes responsáveis por tratar as requisições.
* `app/Model` guarda os modelos de domínio, que podem herdar de `BaseModel`.
* `public/` mantém `index.php` e templates de visualização.
* `storage/` é usado para logs e arquivos temporários.
* `docker/` concentra os arquivos de configuração das imagens utilizadas.

### Como instalar

1. Clone este repositório.
2. Execute `./start.sh` para criar o `.env`, ajustar permissões e iniciar os containers.
3. Caso a pasta `vendor/` não exista, o script executará `composer install` dentro do container `app`.
4. A aplicação estará disponível em `http://localhost`.

### Comandos úteis

- `composer test` executa a suite de testes.
- `composer phpcs` roda o linter do código.
- `composer phpstan` executa a análise estática.
- `composer audit` verifica vulnerabilidades nas dependências.
- `make cron name=ExampleCron` executa uma tarefa agendada de exemplo. Quando o nome for omitido, o script mostrará as tarefas disponíveis.
- `make ci` roda todas as verificações e testes dentro do container.

### Documentação da API

O arquivo `docs/api.json` é gerado automaticamente a partir das anotações OpenAPI presentes em `app/` e em `docs/`.
Após alterar ou adicionar endpoints, execute na raiz do projeto:

```sh
php docs/generate-api.php
```

O script utiliza o método `Generator::generate()` do swagger-php para evitar o uso do `scan()` depreciado, valida as
anotações e sobrescreve o arquivo de especificação utilizado pelo Swagger UI protegido por Basic Auth.

### Tarefas agendadas

Tarefas de cron são registradas no arquivo `config/cron.php`. Para executar
uma delas manualmente utilize:

```sh
php run-cron.php NomeDaTarefa
```

O script `cron.sh` é um facilitador que pode ser utilizado em servidores para
agendar execuções recorrentes. Edite `config/cron.php` para adicionar novas
tarefas ou ajustar a periodicidade das existentes. Logs das execuções são salvos
em `storage/logs/cron.log` quando `LOG_DRIVER` estiver configurado como `file`.

### Escrevendo testes

Os testes ficam na pasta `tests/` e utilizam PHPUnit. Para criar novos testes de
rotas é possível simular requisições definindo variáveis `$_SERVER` e chamando
`Router::run()`, como demonstrado em `RouterInjectionTest.php`. Serviços podem
ser mockados normalmente através das ferramentas do PHPUnit.

### Exemplos avançados

#### Controller e middleware

```php
use Config\Router\Router;

// Middleware simples
Router::addMiddleware(function ($request) {
    // verificar autenticação, registrar logs, etc
});

// Controller com validação
Router::post('/users', function (Config\Request\Request $req) {
    $errors = $req->validate([
        'email' => 'required|email',
        'name' => 'required'
    ]);
    if ($errors) {
        return (new Config\Response\Response())->json(['errors' => $errors], Config\Response\HttpStatus::BAD_REQUEST);
    }
    echo 'Usuário criado';
});
```

#### Tarefas de cron personalizadas

Crie classes que implementem `App\Cron\CronInterface` e registre-as em `config/cron.php`:

```php
class CleanupCron implements App\Cron\CronInterface
{
    public function run(): void
    {
        // lógica da tarefa
    }
}
```

```php
return [
    'cleanup' => CleanupCron::class,
];
```

Execute `php run-cron.php cleanup` para rodar manualmente.

#### Operações de banco de dados

Modelos podem estender `BaseModel` para usar métodos auxiliares de query:

```php
class User extends Config\Model\BaseModel
{
    public function create(array $data): int|bool
    {
        $sql = self::prepareQueryCreate($data, 'users');
        return self::save($sql, $data);
    }
}
```
