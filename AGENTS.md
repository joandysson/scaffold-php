# AGENTS.md

## Propósito
Guia para o agente executar tarefas de forma consistente em "Scaffold PHP".

## Passos obrigatórios
1. Leia `.github/copilot-instructions.md` para entender as regras de estilo.
2. Antes de qualquer commit:
   - Execute `composer phpcs`, `composer phpstan`, `composer audit` e `composer test`.
   - Caso algum comando falhe por falta de dependências, registre o fato no resumo do PR.
3. Utilize o namespace `App\` (PSR-4) ao criar novos arquivos PHP.
4. Coloque novos controladores em `app/Controller` e registre rotas em `routes/web.php`.
5. Limite cada linha de código a 120 caracteres.
6. Mensagens de commit em inglês, no imperativo e com até 72 caracteres.
7. Utilize branches seguindo os prefixos:
   - `feature/<nome>` para novas funcionalidades.
   - `fix/<nome>` para correções.
   - `release/<versão>` para preparações de versão.
   - `hotfix/<nome>` para correções urgentes.

## Estrutura do PR
- Explique em poucas linhas o que foi alterado e por quê.
- Inclua o resultado dos testes.
- Utilize citações de trechos relevantes do código (com caminhos e linhas) para justificar as mudanças.
