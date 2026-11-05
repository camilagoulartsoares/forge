-- Resultado: (2, sem email). String vazia nao e NULL.
SELECT id, COALESCE(email, 'sem email') AS contato
FROM (
    SELECT 1 AS id, '' AS email
    UNION ALL
    SELECT 2, NULL
    UNION ALL
    SELECT 3, 'exemplo@example.invalid'
) AS contatos
WHERE email IS NULL
ORDER BY id;
