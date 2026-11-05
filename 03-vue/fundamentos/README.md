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
