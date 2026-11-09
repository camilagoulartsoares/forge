# Exercício: consulta reutilizável e concorrência

Um composable encapsula lógica com estado reativo. Para praticar, envolva o
[cliente de tarefas](../consumo-api/README.md) em uma operação que exponha dados,
erro, carregamento e uma função de recarregar. Esta pasta ainda é um roteiro.

## Regras do exercício

Cada chamada ao composable deve manter seu próprio estado e controlador de
cancelamento. Ao iniciar uma consulta nova, cancele a anterior. Ao desmontar
o componente consumidor, libere a operação pendente pelo ciclo de vida do Vue.
Não coloque um controlador mutável compartilhado no escopo global do módulo.

## Cenário de concorrência

| Passo | Resultado esperado |
| --- | --- |
| Consulta A começa | Carregamento ativo |
| Consulta B substitui A | A cancelada; B passa a ser a consulta atual |
| B termina antes de A | Dados de B exibidos |
| A termina tardiamente | Não altera dados, erro nem carregamento de B |
| Outra instância é criada | Mantém estado independente |

Além dos dados, proteja a atualização do erro e do bloco `finally`: uma
consulta antiga não pode encerrar o carregamento de uma nova. Teste a ordem
de resolução com promessas controladas, sem temporizadores ou esperas fixas.

Referência: [Vue Composables](https://vuejs.org/guide/reusability/composables.html).
