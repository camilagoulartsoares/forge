-- Consulta didatica sem criar tabelas ou modificar dados.
-- Resultado: (1, Ana, Livro), (2, Bruno, NULL).
SELECT clientes.id, clientes.nome, pedidos.produto
FROM (
    SELECT 1 AS id, 'Ana' AS nome
    UNION ALL
    SELECT 2, 'Bruno'
) AS clientes
LEFT JOIN (
    SELECT 1 AS cliente_id, 'Livro' AS produto
) AS pedidos ON pedidos.cliente_id = clientes.id
ORDER BY clientes.id;
