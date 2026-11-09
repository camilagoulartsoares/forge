# Git

- [ ] Fluxo de branches
- [ ] Commits
- [ ] Merge Requests
- [ ] Resolução de conflitos
- [ ] Revisão de código

## Diagnóstico de conflitos

Quando uma operação de integração já tiver produzido conflito, comece por
`git status`: ele informa a operação em andamento e os arquivos afetados.
Use `git diff --name-only --diff-filter=U` para listar os caminhos não resolvidos.

Leia as duas versões e entenda a intenção antes de editar os marcadores
`<<<<<<<`, `=======` e `>>>>>>>`. Não escolha uma versão inteira automaticamente:
a resolução pode precisar preservar partes das duas mudanças.

Depois de resolver cada arquivo, execute as verificações pertinentes, prepare
somente os caminhos resolvidos com `git add caminho/do/arquivo` e revise
`git diff --cached`. Siga a continuação indicada por `git status` para a operação
em andamento; não inicie outra integração enquanto houver conflito pendente.

Pratique a resolução em uma cópia descartável. Consulte também o
[fluxo seguro de trabalho](fluxo-de-trabalho/README.md).
Para entender o motivo de uma mudança, pratique a
[investigação do histórico por arquivo](fluxo-de-trabalho/exemplos/investigar-historico.md).
