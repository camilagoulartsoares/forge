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

O [DDL de referência](tarefas-etiquetas.sql) usa exclusão restritiva e par único.
Execute `python -m unittest discover -s tools -p "test_task_schema.py"` para
conferir integridade em SQLite em memória, com foreign keys habilitadas.
Isso não valida MySQL: suporte a CHECK, collation e limites VARCHAR dependem
do servidor. O título ainda precisa da validação de aplicação; SQLite não
impõe o comprimento declarado em VARCHAR.
Após popular o esquema, execute a [listagem com etiquetas](listar-tarefas-etiquetas.sql).
Ela mantém tarefas sem etiquetas e retorna uma linha por associação, com
ordenação explícita por identificadores. O teste usa o esquema real em memória.
O esquema restringe os identificadores a inteiros positivos seguros para
JavaScript (`1` a `9007199254740991`), alinhando a persistência ao contrato
da API e evitando perda de precisão na representação de identificadores.

Para o caminho inverso, use [tarefas por etiqueta](tarefas-por-etiqueta.sql)
com `:etiqueta_id` vinculado pelo driver. O indice `(etiqueta_id, tarefa_id)`
cobre a leitura da associacao nessa direcao. A [contagem por etiqueta](contar-tarefas-por-etiqueta.sql)
usa `COUNT(te.tarefa_id)` para representar corretamente etiquetas com zero tarefas.
O plano e verificado em SQLite; confira tambem `EXPLAIN` no MySQL de destino.
