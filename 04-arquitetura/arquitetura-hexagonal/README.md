# Arquitetura Hexagonal

## Objetivo

Estudar isolamento do domínio com portas e adaptadores.

## Conceitos abordados

- Ports and Adapters
- Dependências direcionadas ao domínio

## Pré-requisitos

Fundamentos de arquitetura.

## Como executar

Este roteiro é conceitual e não requer aplicação instalada. Escolha uma regra
pequena, como registrar um pedido, e desenhe o fluxo antes de implementar.

## Exemplos

Para registrar um pedido, separe as responsabilidades:

| Parte | Responsabilidade |
| --- | --- |
| Adaptador de entrada HTTP | Interpretar a requisição e chamar o caso de uso |
| Caso de uso | Coordenar a operação de registro |
| Domínio | Validar as regras do pedido |
| Porta de persistência | Definir o contrato de armazenamento exigido pelo caso de uso |
| Adaptador de persistência | Implementar o contrato usando o banco escolhido |

O domínio não deve importar o controller ou o ORM. O adaptador de banco
depende do contrato definido internamente. Na montagem da aplicação, injete
a implementação desse contrato no caso de uso.

Para verificar a separação, substitua o armazenamento por uma implementação
em memória em um teste: a regra do pedido deve continuar funcionando sem HTTP
ou banco real. Registre quais dependências precisaram mudar e por quê.

## Exercícios

Implemente o [registro de pedido](exercicios/registrar-pedido.md) com porta
de persistência e adaptador em memória, usando os critérios de aceitação propostos.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
