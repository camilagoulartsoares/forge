# Contribuindo com o Forge

## Organização e conteúdo

Mantenha cada conteúdo na área correta, com nomes em minúsculas e separados por hífen. Documente estudos no respectivo `README.md`, incluindo objetivo, referências, dúvidas e como executar quando aplicável.

Adicione somente conteúdo real e útil ao projeto. Não crie commits, arquivos ou branches apenas para testar permissões do Git. Antes de remover ou substituir conteúdo existente, confira seu histórico e confirme que a alteração faz parte da tarefa.

## Preparação do trabalho

Antes de editar:

1. Confirme o repositório, o remoto e a branch de destino.
2. Execute `git status --short --branch` e preserve qualquer alteração local.
3. Execute `git fetch origin` e compare a branch local com a remota.
4. Atualize uma branch limpa apenas por fast-forward, por exemplo, `git switch main` seguido de `git merge --ff-only origin/main`.
5. Se houver divergência, conflito ou alteração local que possa ser afetada, pare e investigue; não use reset destrutivo nem sobrescreva trabalho alheio.

Para trabalho isolado, use uma branch com nome descritivo relacionado à tarefa, como `study/php-introducao`, `docs/fluxo-git` ou `fix/configuracao-testes`. Não use branches temporárias de teste para publicar conteúdo do projeto.

## Alterações e commits

Faça alterações pequenas e coerentes. Atualize a documentação relacionada, execute os testes pertinentes e revise `git diff` e `git diff --check` antes de criar commits.

Use Conventional Commits com mensagens que descrevam o conteúdo efetivamente alterado, por exemplo:

- `feat: adiciona exercício de orientação a objetos`
- `docs: explica o fluxo seguro de publicação`
- `test: adiciona testes com PHPUnit`
- `refactor: reorganiza exemplos de PHP`
- `fix: corrige configuração de teste`
- `chore: atualiza estrutura do repositório`

Não agrupe mudanças independentes em um commit artificial nem use uma mensagem que não corresponda ao diff.

## Publicação

Antes de publicar, faça novo `git fetch origin` e confirme que a branch de destino não avançou desde a atualização local. Publique somente no remoto e na branch previstos para a tarefa.

O push direto para `origin/main` só é apropriado quando a política e as proteções do repositório o permitem. Nesse caso, use um push normal e fast-forward. Se houver rejeição, proteção de branch, conflito ou divergência, pare e informe o motivo; não use `--force`, não contorne proteções e não substitua automaticamente a publicação por outra branch ou Pull Request sem autorização.

Depois do push, confira o SHA remoto com `git ls-remote origin refs/heads/main` e confirme que ele corresponde ao commit publicado. Não considere a publicação concluída enquanto essa verificação não for bem-sucedida.

## Qualidade e segurança

- Execute os testes disponíveis e informe quando não houver testes aplicáveis.
- Não inclua credenciais, dados pessoais, código interno ou outros materiais confidenciais.
- Arquivos `.env` e segredos devem permanecer fora do Git.
- Não faça merge, rebase ou limpeza de alterações de terceiros sem autorização.
