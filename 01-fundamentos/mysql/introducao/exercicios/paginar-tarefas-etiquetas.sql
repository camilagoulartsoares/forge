-- Limite entidades antes da juncao para nao truncar as etiquetas de uma tarefa.
SELECT pagina.id, pagina.titulo, e.nome AS etiqueta
FROM (
    SELECT id, titulo
    FROM tarefas
    WHERE id > :apos_id
    ORDER BY id
    LIMIT :limite
) AS pagina
LEFT JOIN tarefa_etiqueta AS te ON te.tarefa_id = pagina.id
LEFT JOIN etiquetas AS e ON e.id = te.etiqueta_id
ORDER BY pagina.id, e.id;
