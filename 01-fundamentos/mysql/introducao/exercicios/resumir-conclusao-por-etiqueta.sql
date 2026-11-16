SELECT e.id, e.nome,
       COUNT(te.tarefa_id) AS total,
       SUM(CASE WHEN t.concluida = 0 THEN 1 ELSE 0 END) AS pendentes,
       SUM(CASE WHEN t.concluida = 1 THEN 1 ELSE 0 END) AS concluidas
FROM etiquetas AS e
LEFT JOIN tarefa_etiqueta AS te ON te.etiqueta_id = e.id
LEFT JOIN tarefas AS t ON t.id = te.tarefa_id
GROUP BY e.id, e.nome
ORDER BY e.id;
