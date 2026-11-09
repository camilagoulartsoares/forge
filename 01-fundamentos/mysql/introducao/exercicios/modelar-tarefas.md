# Exercício: tarefas e etiquetas

Desenhe um modelo relacional antes de escrever o DDL. Uma tarefa pode ter
várias etiquetas e uma etiqueta pode classificar várias tarefas.

| Relação | Dados mínimos | Restrições a discutir |
| --- | --- | --- |
| tarefas | id, título, concluída | Chave primária e título obrigatório |
| etiquetas | id, nome | Chave primária e política de nomes repetidos |
| tarefa_etiqueta | tarefa_id, etiqueta_id | Chaves estrangeiras e par único |

Não grave as etiquetas como uma lista separada por vírgulas em uma coluna.
Use a relação intermediária para representar a cardinalidade e impedir
associações duplicadas com uma restrição única do par, conforme o exercício.

## Critérios de aceitação

- Uma tarefa sem etiquetas continua válida.
- Uma etiqueta pode ser usada por duas tarefas.
- A mesma associação não pode ser inserida duas vezes.
- Uma associação não pode apontar para tarefa ou etiqueta inexistente.
- A política de exclusão deve ser explícita: recusar ou remover associações,
  preservando as entidades que não deveriam ser excluídas.

Defina se nomes que diferem apenas por acento ou maiúscula são equivalentes
antes de escolher collation e unicidade. Faça os testes em banco local
descartável, incluindo operações inválidas; um diagrama não comprova que
as restrições foram criadas ou são aplicadas pelo servidor.
