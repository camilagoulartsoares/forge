# Exercício: registrar pedido sem infraestrutura

## Contrato

Implemente um caso de uso que recebe um identificador textual e uma lista
de itens. Cada item contém um código textual não vazio e uma quantidade
inteira positiva. O pedido precisa ter ao menos um item.

Defina uma porta de armazenamento com operações de consultar existência e
salvar. Para a prática, implemente um adaptador em memória. Nenhuma classe
de domínio deve importar bibliotecas de HTTP ou de banco.

## Critérios de aceitação

| Entrada ou situação | Resultado esperado |
| --- | --- |
| Identificador não vazio e um item válido | Pedido armazenado uma vez |
| Identificador vazio ou só espaços | Rejeição antes de salvar |
| Lista de itens vazia | Rejeição antes de salvar |
| Quantidade zero, negativa ou não inteira | Rejeição antes de salvar |
| Código de item vazio | Rejeição antes de salvar |
| Identificador já existente | Rejeição sem substituir o pedido anterior |
| Adaptador falha ao salvar | Falha propagada; sucesso não informado |

Nos testes, observe as chamadas à porta e o estado do adaptador. Não apenas
verifique a exceção: confirme que entradas inválidas não chegaram a salvar.

## Limite da simulação

Consultar e depois salvar em memória não resolve concorrência em um banco.
Se avançar para persistência real, documente como a unicidade será garantida
atomicamente e como o conflito será convertido para o contrato do caso de uso.
