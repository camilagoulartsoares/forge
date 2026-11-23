-- Etapa de verificacao antes de tornar categoria obrigatoria.
-- Uma lista vazia indica ausencia de pendencias neste snapshot; nao bloqueia novas escritas.
SELECT id, titulo
FROM tarefas
WHERE categoria IS NULL
ORDER BY id;
