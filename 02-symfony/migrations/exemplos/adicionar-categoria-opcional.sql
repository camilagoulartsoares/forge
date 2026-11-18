-- Etapa de expansao: execute uma vez em banco local descartavel.
ALTER TABLE tarefas ADD COLUMN categoria VARCHAR(120) DEFAULT NULL
    CHECK (categoria IS NULL OR length(trim(categoria)) > 0);
