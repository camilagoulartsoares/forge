# Verificação da documentação

## Todas as verificações

Com Python 3.10+, Git, PHP 7.4+ e Node.js 18+, execute `python tools/check.py`
na raiz. O comando roda o verificador Markdown, os testes Python/SQL, a sintaxe
de todos os arquivos PHP, as suítes PHP e os testes JavaScript. Não instala
dependências nem exige banco ou acesso à rede. Interrompe na primeira falha
e retorna um código diferente de zero; runtime ausente não conta como sucesso.
Os caminhos são resolvidos a partir do script, permitindo executá-lo por
caminho absoluto a partir de outro diretório.

Para trabalhar em uma área isolada, use `--only docs`, `--only python`,
`--only php` ou `--only js`. Somente os runtimes da área selecionada são exigidos.
Uma execução parcial não substitui o comando completo antes de publicar código.
Todos os módulos `.mjs` também passam por `node --check`, mesmo quando ainda
não são importados por uma suíte de testes.
Use `--timeout 30` para limitar cada comando a 30 segundos. Um timeout retorna
`124` e interrompe a sequência; valores não positivos, infinitos ou NaN são recusados.
Sem a opção, o tempo de execução não recebe limite adicional.
A execução completa também confere `git diff --check` e a área de preparação
com `git diff --cached --check`, antes de rodar as suítes. Assim, erros de
whitespace nas mudanças locais não passam despercebidos por testes funcionais.

## Somente documentação

Requer Python 3.10 ou superior e Git. Na raiz do repositório:

```sh
python tools/check_docs.py
python -m unittest discover -s tools -p "test_*.py"
```

O comando lê documentos Markdown versionados e novos arquivos não ignorados.
Verifica UTF-8, fechamento de blocos cercados por crases ou tils e existência
dos destinos de links e imagens locais escritos na forma `[texto](caminho)`.
Blocos de código e código inline não são tratados como links.

Links externos não são acessados. Âncoras, links por referência e sintaxe
Markdown com parênteses aninhados no destino precisam de revisão manual.
Links fora do repositório são rejeitados para manter o material portável.
O código de saída é zero em caso de sucesso e diferente de zero em caso de erro.
Falhas de conteúdo retornam `1`; problemas operacionais, como Git ausente ou
`--root` fora de um repositório, retornam `2` com diagnóstico no stderr.
Os testes usam diretórios temporários e cobrem falhas de links, UTF-8,
blocos aninhados, imagens e o código de saída da interface de linha de comando.
