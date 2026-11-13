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
Após a prática, compare com a [solução em PHP puro](exemplos/pedidos.php).
Execute `php 04-arquitetura/arquitetura-hexagonal/exemplos/pedidos-test.php`
na raiz. Os testes cobrem validação antes da gravação, duplicidade e propagação
de falha do adaptador. O armazenamento é volátil e não oferece concorrência de banco.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.

O exemplo agora separa [entidade](exemplos/Pedido.php),
[porta](exemplos/ArmazenamentoPedidos.php),
[caso de uso](exemplos/RegistrarPedido.php) e
[adaptador](exemplos/PedidosEmMemoria.php). A fachada `pedidos.php` continua
compativel com os exemplos anteriores; outros adaptadores dependem apenas da porta.

O segundo adaptador, [PedidosEmJson](exemplos/PedidosEmJson.php), pode ser
injetado em `RegistrarPedido` sem mudar o dominio. Recebe um caminho em um
diretorio local existente. Usa lock separado e substituicao por arquivo temporario
no mesmo diretorio; rejeita dados corrompidos em vez de sobrescreve-los.
Todos os escritores devem usar esse adaptador. Nao oferece transacoes entre
varios arquivos nem garantias para sistemas de arquivos de rede. Execute
`php 04-arquitetura/arquitetura-hexagonal/exemplos/pedidos-json-test.php`.
