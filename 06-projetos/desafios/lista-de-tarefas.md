# Desafio: lista de tarefas com estados verificáveis

Integre os exemplos já existentes em uma aplicação de estudos ou em uma
simulação de fluxo. Este documento é o enunciado; não representa uma aplicação pronta.

## Componentes disponíveis

- [Validação de título](../../03-vue/formularios/README.md).
- [Pesquisa, filtro e ordenação](../../03-vue/fundamentos/README.md).
- [Consulta de API com cancelamento](../../03-vue/consumo-api/README.md).
- [Filtro representado na rota](../../03-vue/vue-router/README.md).

## Entrega mínima

Mostre tarefas com identificadores estáveis, permita pesquisar e filtrar por
conclusão e ordene o resultado por um critério explícito. A coleção original
deve permanecer íntegra enquanto a visualização muda.

| Cenário | Critério de aceitação |
| --- | --- |
| Nenhuma tarefa cadastrada | Estado vazio compreensível |
| Pesquisa não encontra correspondência | Mensagem distinta de falha de rede |
| Filtro muda e a página é recarregada | Estado restaurado da query |
| Resposta HTTP falha | Erro e ação de nova tentativa, sem falso sucesso |
| Consulta antiga termina depois da nova | Resultado atual preservado |
| Título é só espaços | Envio recusado com mensagem associada ao campo |
| Ordem visual muda | Operação continua afetando a tarefa pelo id correto |

Use uma função de requisição fake nos testes para controlar sucesso, falha
e ordem de resolução, sem depender de uma API externa. Documente como iniciar
o exemplo, executar testes e reproduzir cada cenário. Persistência, autenticação
e publicação da aplicação ficam fora da entrega mínima; registre extensões separadamente.
