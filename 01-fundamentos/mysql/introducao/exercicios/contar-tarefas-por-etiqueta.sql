-- COUNT da chave associada preserva zero para etiquetas sem tarefas.
SELECT e.id, e.nome, COUNT(te.tarefa_id) AS quantidade
FROM etiquetas AS e
LEFT JOIN tarefa_etiqueta AS te ON te.etiqueta_id = e.id
GROUP BY e.id, e.nome
ORDER BY e.id;
