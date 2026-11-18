-- O chamador escolhe explicitamente a categoria de cada tarefa.
-- Nao sobrescreve uma classificacao ja informada pela aplicacao nova.
UPDATE tarefas
SET categoria = :categoria
WHERE id = :id AND categoria IS NULL;
