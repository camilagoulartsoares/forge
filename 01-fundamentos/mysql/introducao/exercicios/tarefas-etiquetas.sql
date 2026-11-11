-- Execute somente em banco local descartavel, sem tabelas com estes nomes.
CREATE TABLE tarefas (
    id BIGINT NOT NULL PRIMARY KEY,
    titulo VARCHAR(120) NOT NULL,
    concluida INTEGER NOT NULL DEFAULT 0,
    CHECK (length(trim(titulo)) > 0),
    CHECK (concluida IN (0, 1))
);

CREATE TABLE etiquetas (
    id BIGINT NOT NULL PRIMARY KEY,
    nome VARCHAR(120) NOT NULL UNIQUE,
    CHECK (length(trim(nome)) > 0)
);

CREATE TABLE tarefa_etiqueta (
    tarefa_id BIGINT NOT NULL,
    etiqueta_id BIGINT NOT NULL,
    PRIMARY KEY (tarefa_id, etiqueta_id),
    FOREIGN KEY (tarefa_id) REFERENCES tarefas(id) ON DELETE RESTRICT,
    FOREIGN KEY (etiqueta_id) REFERENCES etiquetas(id) ON DELETE RESTRICT
);

CREATE INDEX idx_tarefa_etiqueta_etiqueta ON tarefa_etiqueta(etiqueta_id);
