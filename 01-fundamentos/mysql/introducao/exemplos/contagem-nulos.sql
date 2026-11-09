-- Resultado: (3, 2, 2). Zero conta como valor conhecido; NULL nao.
SELECT COUNT(*) AS linhas, COUNT(quantidade) AS conhecidos, SUM(quantidade) AS soma
FROM (
    SELECT NULL AS quantidade
    UNION ALL
    SELECT 0
    UNION ALL
    SELECT 2
) AS amostras;
