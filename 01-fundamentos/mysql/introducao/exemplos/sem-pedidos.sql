-- Resultado: (2, Bruno). Uma referencia nula nao elimina o cliente sem pedido.
SELECT clientes.id, clientes.nome
FROM (
    SELECT 1 AS id, 'Ana' AS nome
    UNION ALL
    SELECT 2, 'Bruno'
) AS clientes
WHERE NOT EXISTS (
    SELECT 1
    FROM (
        SELECT 1 AS cliente_id
        UNION ALL
        SELECT NULL
    ) AS pedidos
    WHERE pedidos.cliente_id = clientes.id
)
ORDER BY clientes.id;
