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

```sh
node --test 03-vue/fundamentos/ordenar-tarefas.test.mjs
```
