# Exercício de roteamento

Em uma aplicação Symfony de estudos, defina separadamente caminho, método
HTTP, nome da rota e requisitos dos parâmetros. Esta pasta ainda não contém
aplicação instalada nem configuração de rotas executável.

## Cenário

Planeje `GET /tarefas/nova` para apresentar um formulário e `GET /tarefas/{id}`
para consultar uma tarefa. Restrinja `id` a inteiros positivos para que `nova`
não seja interpretado como identificador. Uma rota reconhecida ainda pode
apontar para um recurso inexistente; são situações diferentes a diagnosticar.

## Verificações

| Requisição | Comportamento esperado do exercício |
| --- | --- |
| GET /tarefas/nova | Formulário, sem consultar identificador textual |
| GET /tarefas/12 | Consulta da tarefa 12 |
| GET /tarefas/abc | Caminho incompatível com o requisito |
| POST /tarefas/12 em rota somente GET | Método não permitido |

Em uma aplicação instalada, use `debug:router` e `router:match` pelo console
para inspecionar rotas e a correspondência de uma URL. Confirme também o
método no teste HTTP; conferir apenas a URL não verifica a restrição do verbo.
Gere links pelo nome da rota, evitando repetir caminhos literais na interface.

Referência: [Symfony Routing](https://symfony.com/doc/current/routing.html).

O [parser de identificadores](ler-identificador.php) converte o segmento decimal
somente depois de validar formato e limite. Rejeita zeros iniciais, sinais,
notacao cientifica e valores acima do inteiro seguro JSON ou de `PHP_INT_MAX`.
Execute `php 02-symfony/routes/identificador-test.php`.
