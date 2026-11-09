# Exercício: item de tarefa com contrato explícito

Em um projeto Vue de estudos, extraia um componente de item a partir da lista.
Ele recebe uma tarefa por prop e comunica a intenção de alterar o estado por
evento. Esta pasta contém o roteiro, sem aplicação ou compilador Vue instalado.

## Contrato proposto

| Entrada ou saída | Significado |
| --- | --- |
| Prop `tarefa` | Identificador, título e estado atual, controlados pelo pai |
| Evento `alterar-conclusao` | Identificador e próximo estado booleano desejado |
| Prop `ocupado` | Operação em andamento; impede novo envio duplicado |

O componente filho não deve alterar diretamente o objeto recebido. O pai
decide como atualizar ou persistir os dados. Eventos de componentes precisam
ser tratados no ponto apropriado da árvore; não presuma propagação como no DOM.

## Critérios de aceitação

Acione o controle por teclado e confirme um evento com o identificador correto.
Verifique também tarefa concluída, pendente, título longo e estado ocupado.
A prop original deve permanecer intacta até o pai fornecer a versão atualizada.
Use um controle nativo acessível e uma chave estável na lista, nunca o índice
quando a ordem puder mudar. Documente o nome e o payload dos eventos.

Referência: [Vue Component Events](https://vuejs.org/guide/components/events.html).
