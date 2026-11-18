-- Cursor inicial :apos_id = 0; vincule inteiros validados pelo adaptador.
SELECT id, titulo, concluida
FROM tarefas
WHERE id > :apos_id
ORDER BY id
LIMIT :limite;
