# Consumo de API com falhas explícitas

[buscar-tarefas.mjs](buscar-tarefas.mjs) consulta uma lista e verifica status
HTTP e estrutura do JSON. Requer `fetch` no navegador ou Node.js 18 ou superior.
O contrato exige `id` inteiro positivo seguro, `titulo` não vazio e `concluida` booleano.
Os identificadores precisam ser únicos na lista, evitando chaves ambíguas no
render de componentes e atualizações aplicadas à tarefa errada.

```sh
node --test 03-vue/consumo-api/buscar-tarefas.test.mjs
```

Execute da raiz. Os testes injetam a função de requisição e não acessam a rede.
Cobrem sucesso, vazio, HTTP 503, rede indisponível, JSON inválido e formato inesperado.
O chamador deve capturar a falha, encerrar o estado de carregamento em `finally`
e permitir nova tentativa. Uma falha não deve ser apresentada como lista vazia.

Falhas HTTP lançam `ErroHttp`, com a propriedade numérica `status`. O chamador
pode distinguir uma recusa de acesso de indisponibilidade sem analisar a mensagem.
Erros de rede, cancelamento e JSON continuam sendo propagados com sua identidade.

Para cancelar uma requisição ao desmontar um componente ou mudar a consulta,
passe `{ signal: controller.signal }` como terceiro argumento, usando um
`AbortController`. Ao usar o fetch padrão, o segundo argumento pode ser `undefined`.
O cancelamento é propagado ao chamador; ele pode distinguir o motivo e evitar
exibir uma mensagem de falha de rede para uma ação cancelada pelo usuário.
