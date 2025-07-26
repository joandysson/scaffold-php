# Copilot Instructions

## 1. Objetivo do repositório
O projeto “Scaffold PHP” oferece uma estrutura básica em Docker para aplicações PHP. Ele simplifica o início de novos projetos web e fornece scripts de automação para desenvolvimento, testes e tarefas agendadas.

## 2. Diretrizes de estilo e práticas
- **PHP CodeSniffer**: execute `composer phpcs` e siga as regras do arquivo `phpcs.xml`. As principais exigências são o uso de tags `<?php ?>`, arquivos em UTF-8 sem BOM e até 120 caracteres por linha.
- **PHPStan**: a análise estática está configurada no nível 5 e abrange o diretório `app`. Utilize `composer phpstan` para rodar a verificação.
- **Autoload PSR-4**: o namespace base `App\\` aponta para `app/`.
- **Testes**: utilize PHPUnit. Os testes ficam em `tests/` e podem ser executados com `composer test`.

## 3. Fluxo de trabalho
1. Levante os containers com `docker compose up -d`.
2. Rode a rotina de verificações local com `make ci`, que executa `composer phpcs`, `composer phpstan`, `composer audit` e `composer test`.
3. O GitHub Actions realiza essas mesmas etapas em pull requests.
4. Para tarefas em lote, utilize o script `cron.sh` (ex.: `make cron name=ExempleCron`).

## 4. Estrutura das pastas
```
app/        Código PHP (controllers, models, configurações)
public/     Arquivos acessíveis publicamente e views
routes/     Definição de rotas da aplicação
storage/    Arquivos gerados pela aplicação
tests/      Testes PHPUnit
docker/     Imagens e configurações do Docker
```
Controladores ficam em `app/Controllers`, modelos em `app/Models` e as rotas padrão em `routes/web.php`.

## 5. Exemplo de uso
- Copie `.env.exemple` para `.env` e ajuste as variáveis de ambiente.
- Para subir a aplicação: `docker compose up -d`.
- Exemplo de rota em `routes/web.php`: `Router::get('/', 'HomeController:home');`.

## 6. Boas práticas adicionais
- Utilize as helpers em `app/Config/functions.php` para renderizar views, manipular erros e idiomas.
- Mantenha controllers simples, seguindo o exemplo de `HomeController`.
- Ao adicionar novas funcionalidades, inclua testes em `tests/`.
