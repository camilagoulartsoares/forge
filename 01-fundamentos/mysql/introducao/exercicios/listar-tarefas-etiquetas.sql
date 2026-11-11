SELECT tarefas.id, tarefas.titulo, etiquetas.nome AS etiqueta
FROM tarefas
LEFT JOIN tarefa_etiqueta ON tarefa_etiqueta.tarefa_id = tarefas.id
LEFT JOIN etiquetas ON etiquetas.id = tarefa_etiqueta.etiqueta_id
ORDER BY tarefas.id, etiquetas.id;
