# Exercício: serviço de conclusão de tarefa

Defina uma operação `concluirTarefa(id)` que pode ser chamada novamente sem
alternar a tarefa de volta para pendente. Esse contrato expressa o estado
desejado, evitando uma operação de alternância quando houver repetição.

## Regras propostas

| Situação | Resultado esperado |
| --- | --- |
| Tarefa pendente | Passa a concluída e é salva |
| Tarefa já concluída | Retorna o estado atual sem nova gravação |
| Identificador não encontrado | Falha de recurso ausente |
| Persistência indisponível | Falha propagada, sem informar sucesso |

Receba o repositório pelo construtor. O serviço coordena a consulta e a
mudança; não lê Request nem escolhe status HTTP. O adaptador de entrada
traduz as falhas conhecidas para seu contrato de resposta.

## Como verificar

Use um repositório fake que conte gravações. Execute a operação duas vezes
sobre a mesma tarefa e confirme uma única gravação no cenário sequencial.
Verifique que uma falha de infraestrutura não é confundida com ausência.

Essa simulação não prova idempotência sob concorrência ou falha parcial de
uma operação distribuída. Se acrescentar notificações ou outros efeitos,
defina como evitar sua duplicação e teste os pontos de interrupção.

Este é um roteiro de aplicação; não há serviço Symfony instalado nesta pasta.
Compare com a [implementação em PHP puro](concluir-tarefa.php) e execute
`php 02-symfony/services/concluir-tarefa-test.php`. A entidade imutável evita
alterar o objeto consultado quando a gravação falha; o exemplo não implementa
controle de concorrência nem persistência real.
Para aplicar a política de propriedade, use `executarAutorizada` com a identidade
obtida no servidor. Tarefas sem proprietário ou de outro usuário são recusadas
antes de alterar o estado, inclusive quando já concluídas. O método `executar`
continua como exemplo isolado de caso de uso sem autorização.
Ambos conferem o identificador retornado pelo repositório. Uma implementação
que devolva outra tarefa provoca `UnexpectedValueException` antes de gravar,
evitando modificar o recurso errado por uma falha de contrato do adaptador.

O [adaptador em memoria](repositorio-memoria.php) implementa a mesma porta
usada pelo servico. Aceita tarefas imutaveis como carga inicial e rejeita IDs
duplicados. Execute `php 02-symfony/services/repositorio-memoria-test.php`.
