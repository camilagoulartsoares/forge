# Fluxo de Trabalho com Git

## Objetivo

Trabalhar em alterações reais com histórico claro, preservando o trabalho local e remoto.

## Conceitos abordados

- Estado do repositório, branches locais e remotas
- Fast-forward e atualização segura
- Commits pequenos e descritivos
- Push e verificação do SHA publicado

## Pré-requisitos

Git instalado e acesso ao remoto autorizado.

## Preparação

Antes de alterar arquivos, confirme o repositório, o remoto e a branch; verifique o estado local e atualize as referências remotas:

```sh
git status --short --branch
git remote -v
git fetch origin
git branch -avv
```

Preserve alterações locais. Para atualizar `main` quando estiver limpa e apenas atrás do remoto:

```sh
git switch main
git merge --ff-only origin/main
```

Se houver divergência, alterações locais ou conflito, pare e investigue antes de editar. Não use `reset --hard` para forçar uma atualização.

## Alterações e commits

Faça somente alterações necessárias e úteis ao projeto. Não crie arquivos ou commits artificiais para testar permissões. Execute os testes pertinentes e revise o diff:

```sh
git diff --check
git diff
```

Crie commits com mensagens específicas para o conteúdo, seguindo Conventional Commits:

```sh
git add caminho/do/arquivo
git diff --cached --check
git diff --cached
git commit -m "docs: explica o fluxo seguro de publicação"
```

`git diff` mostra alterações ainda não preparadas; `git diff --cached` mostra
o conteúdo que entrará no próximo commit. Revise também o conteúdo preparado
para evitar incluir mudanças de outro trabalho. Se a verificação apontar um
problema, corrija o arquivo e execute `git add` novamente antes do commit.

Se um arquivo foi preparado por engano, retire-o da área de preparação:

```sh
git restore --staged caminho/do/arquivo
```

Esse comando mantém as edições no diretório de trabalho. Depois, confira
`git status --short` e `git diff --cached` antes de continuar.

## Publicação em `main`

Antes do push, atualize as referências e confira se `origin/main` continua no commit esperado:

```sh
git fetch origin
git status --short --branch
git rev-parse HEAD
git ls-remote origin refs/heads/main
```

Publique diretamente apenas se a política e as proteções do repositório permitirem, usando um push normal e fast-forward:

```sh
git push origin main
```

Nunca use `git push --force` em `main` nem tente contornar proteção de branch. Se o push for recusado, se o remoto tiver avançado ou se houver conflito, pare e informe o erro; não publique em outra branch e não abra Pull Request sem autorização.

Após o push, compare o SHA local com o SHA remoto:

```sh
git rev-parse HEAD
git ls-remote origin refs/heads/main
```

A publicação só está confirmada quando os SHAs correspondem. Não considere um push para outra branch como publicação em `main`.

## Exercícios

Pratique em um repositório próprio ou em uma cópia local descartável, sem criar commits de teste no repositório compartilhado.

## Referências

- [Git Book: Git Basics](https://git-scm.com/book/en/v2/Getting-Started-Git-Basics)
- [Git Documentation: git-push](https://git-scm.com/docs/git-push)
