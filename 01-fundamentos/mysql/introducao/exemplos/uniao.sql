-- Resultado: Ana, Bruno. UNION elimina linhas repetidas da projecao.
SELECT nome
FROM (
    SELECT 'Ana' AS nome
    UNION
    SELECT 'Ana'
    UNION
    SELECT 'Bruno'
) AS nomes
ORDER BY nome;
