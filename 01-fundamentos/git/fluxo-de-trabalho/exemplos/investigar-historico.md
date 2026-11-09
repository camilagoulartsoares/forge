# Investigar uma mudança sem alterar arquivos

Na raiz do Forge, use uma implementação real como ponto de partida:

```sh
git log -5 --oneline -- 01-fundamentos/php/poo/exemplos/Estoque.php
git log -1 -p -- 01-fundamentos/php/poo/exemplos/Estoque.php
git show HEAD:01-fundamentos/php/poo/exemplos/Estoque.php
git blame -L 1,25 -- 01-fundamentos/php/poo/exemplos/Estoque.php
```

O primeiro comando lista commits que afetam o caminho; o segundo exibe o
patch mais recente. `git show` consulta o conteúdo versionado sem substituir
o arquivo local. `blame` indica a última alteração de cada linha, não explica
sozinho o motivo nem atribui responsabilidade por um problema.

## Exercício

Localize a introdução de uma validação e leia o commit inteiro, incluindo os
testes. Compare a intenção da mensagem com o comportamento observado no diff.
Se o arquivo tiver sido renomeado, experimente `git log --follow -- caminho`
para investigar o caminho anterior; o acompanhamento por renomeação tem limites.

Registre o hash e a evidência que sustentam sua conclusão. Não restaure uma
versão antiga apenas para ler seu conteúdo e não altere o histórico durante
essa investigação.

Referência: [Git: git-log](https://git-scm.com/docs/git-log).
