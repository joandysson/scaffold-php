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

### Portas dos containers

|Container|Porta|
|---------|-----|
|`PHP`|`80`|
|`Mysql`|`3306`|
|`Adminer`|`8080`|

#### Configurações de ambiente

As variáveis de ambiente estão documentadas em `.env.exemple`. Copie este arquivo para `.env` e ajuste os valores conforme sua necessidade. Entre elas, `APP_DEBUG` controla a exibição de erros e `LOG_DRIVER` define o tipo de logger utilizado (`file` ou `error_log`).

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
