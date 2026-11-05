-- Resultado: (Sul, 2, 50). HAVING filtra depois de agrupar.
SELECT regiao, COUNT(*) AS quantidade, SUM(valor) AS total
FROM (
    SELECT 'Sul' AS regiao, 20 AS valor
    UNION ALL
    SELECT 'Sul', 30
    UNION ALL
    SELECT 'Norte', 10
) AS vendas
GROUP BY regiao
HAVING COUNT(*) >= 2
ORDER BY regiao;
