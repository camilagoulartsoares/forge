# Transformação de listas

[filtrar-tarefas.mjs](filtrar-tarefas.mjs) separa a seleção de tarefas da interface.
Recebe um array de tarefas com `concluida` booleano e aceita `todas`, `pendentes`
ou `concluidas`. O contrato pressupõe itens previamente validados.

```sh
node --test 03-vue/fundamentos/filtrar-tarefas.test.mjs
```

Execute da raiz com Node.js 18 ou superior. O filtro produz um array novo,
mantém a ordem e compartilha os objetos originais; não faz cópia profunda.
Em Vue, pode ser chamado em um valor computado, mantendo a lista original
como fonte de dados. Os testes congelam a entrada para detectar mutações acidentais.

## Ordenação com critério explícito

[ordenar-tarefas.mjs](ordenar-tarefas.mjs) permite escolher `titulo` ou `data`,
sem alterar o array original. Empates usam `id` numérico único como desempate.
O título é comparado em português, sem distinguir acentos ou maiúsculas.
Para o critério de data, o contrato exige `criadaEm` previamente validada e
normalizada no formato UTC `YYYY-MM-DDTHH:mm:ss.sssZ`, permitindo comparação textual.
Não passe datas locais, inválidas ou com fusos diferentes sem normalização.

## Pesquisa textual

[pesquisar-tarefas.mjs](pesquisar-tarefas.mjs) procura todos os termos no título,
ignorando acentos e maiúsculas e preservando os dados originais. Pontuação é
literal, sem interpretar expressões regulares. Uma consulta vazia retorna uma
cópia da lista. O contrato pressupõe tarefas com títulos textuais válidos.
Execute `node --test 03-vue/fundamentos/pesquisar-tarefas.test.mjs` na raiz.

`concluirTarefa` em [concluir-tarefa.mjs](concluir-tarefa.mjs) atualiza pelo id,
mantendo a origem intacta e referências dos itens não alterados. Repetir uma
conclusão não alterna o estado nem cria outra lista. Execute
`node --test 03-vue/fundamentos/concluir-tarefa.test.mjs` para conferir o contrato.

```sh
node --test 03-vue/fundamentos/ordenar-tarefas.test.mjs
```

## Composicao da lista

[selecionarTarefas](selecionar-tarefas.mjs) combina `estado`, `termo` e `criterio`
e devolve `{ itens, total, encontradas }`. A entrada segue o contrato da API;
para ordenar por `data`, inclua `criadaEm` em UTC ISO com milissegundos.
[paginarTarefas](paginar-tarefas.mjs) recebe as mesmas opcoes mais `pagina`
(base 1) e `tamanho` (1 a 100). Pesquisa e filtros precedem a paginacao;
paginas fora do resultado devolvem uma lista vazia.

[adicionarTarefa](adicionar-tarefa.mjs) recebe lista, ID e titulo, normaliza o
titulo e cria um item pendente. [importarTarefas](importar-tarefas.mjs) recebe
lista e texto JSON e rejeita todo o lote se houver um item invalido ou ID repetido.
Essas funcoes retornam novas listas e nao persistem dados nem geram IDs de servidor.
