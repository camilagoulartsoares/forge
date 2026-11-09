# Exercício: estado compartilhado com Pinia

Use uma store quando várias telas realmente precisarem dos mesmos dados.
Para a prática, considere uma lista e um resumo de tarefas que devem refletir
a mesma alteração. Não há Pinia instalado nesta pasta.

## Separação proposta

| Parte | Conteúdo do exercício |
| --- | --- |
| State | Coleção de tarefas e estado da operação |
| Getter | Quantidade de tarefas pendentes derivada da coleção |
| Action | Coordenar atualização e tratamento da resposta da API |
| Estado local | Texto ainda não enviado de um formulário específico |

Não armazene simultaneamente uma contagem mutável e a lista que a determina:
derive o resumo para evitar inconsistência. Ao extrair estado reativo da store,
consulte `storeToRefs` em vez de perder a ligação reativa por desestruturação simples.

## Verificações

Confirme que concluir uma tarefa atualiza lista e resumo sem sincronização
manual duplicada. Em uma falha de gravação, mantenha o estado anterior ou
desfaça uma alteração otimista, conforme a política documentada.
Crie uma instância de Pinia independente por teste para evitar vazamento de
estado. Ao trocar a identidade autenticada, limpe dados que pertencem à sessão anterior.

Referência: [Pinia: Defining a Store](https://pinia.vuejs.org/core-concepts/).
