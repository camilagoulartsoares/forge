-- NOT EXISTS nao multiplica linhas e continua correto com varias associacoes.
SELECT t.id, t.titulo, t.concluida
FROM tarefas AS t
WHERE NOT EXISTS (
    SELECT 1 FROM tarefa_etiqueta AS te WHERE te.tarefa_id = t.id
)
ORDER BY t.id;
