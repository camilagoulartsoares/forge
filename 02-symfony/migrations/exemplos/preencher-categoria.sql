-- O chamador escolhe explicitamente a categoria de cada tarefa.
-- Nao sobrescreve uma classificacao ja informada pela aplicacao nova.
-- NULL vira entrada vazia para ser rejeitada pelo CHECK, nao um falso preenchimento.
UPDATE tarefas
SET categoria = COALESCE(:categoria, '')
WHERE id = :id AND categoria IS NULL;
