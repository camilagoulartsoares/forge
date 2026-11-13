-- Vincule :etiqueta_id pelo driver; nunca concatene entrada na consulta.
SELECT t.id, t.titulo, t.concluida
FROM tarefa_etiqueta AS te
INNER JOIN tarefas AS t ON t.id = te.tarefa_id
WHERE te.etiqueta_id = :etiqueta_id
ORDER BY t.id;
