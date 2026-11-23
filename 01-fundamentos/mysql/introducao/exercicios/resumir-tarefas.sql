-- Resumo global: cada tarefa conta uma vez, independentemente de suas etiquetas.
SELECT COUNT(*) AS total,
       COALESCE(SUM(CASE WHEN concluida = 0 THEN 1 ELSE 0 END), 0) AS pendentes,
       COALESCE(SUM(CASE WHEN concluida = 1 THEN 1 ELSE 0 END), 0) AS concluidas
FROM tarefas;
