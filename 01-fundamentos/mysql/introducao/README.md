# Introdução ao MySQL

## Objetivo

Estudar consultas, modelagem básica e manipulação de dados.

## Conceitos abordados

- SQL básico
- Modelagem relacional

## Pré-requisitos

MySQL disponível localmente.

## Como executar

Adicione instruções ao incluir scripts.

## Exemplos

O exemplo somente consulta dados definidos na própria instrução, sem criar
tabelas nem modificar um banco. Execute em um cliente MySQL conectado ao
seu ambiente local de estudos:

```sql
SELECT id, nome
FROM (
    SELECT 1 AS id, 'Ana' AS nome, 1 AS ativo
    UNION ALL
    SELECT 2, 'Bruno', 0
    UNION ALL
    SELECT 3, 'Carla', 1
) AS pessoas
WHERE ativo = 1
ORDER BY id;
```

O resultado esperado contém duas linhas: `1, Ana` e `3, Carla`, nessa ordem.
Remova o filtro para observar três linhas e altere a ordenação para `id DESC`
para inverter a sequência. Sem `ORDER BY`, não dependa da ordem das linhas.

## Exercícios

### Junções

Execute [juncoes.sql](exemplos/juncoes.sql) em um cliente MySQL local.
O `LEFT JOIN` preserva Bruno mesmo sem pedido, preenchendo o produto com `NULL`.
Troque por `INNER JOIN`: apenas Ana deve aparecer. Se um cliente tiver dois
pedidos, ele aparecerá em duas linhas; a junção não elimina essa multiplicidade.

### Agregação

Execute [agregacao.sql](exemplos/agregacao.sql). O resultado deve conter
`Sul`, quantidade `2` e total `50`. Remova `HAVING` para incluir o grupo Norte.
`WHERE` filtra linhas antes do agrupamento; `HAVING` filtra os grupos formados.
As colunas selecionadas aqui são agrupadas ou agregadas, sem depender de
configurações permissivas de agrupamento do servidor.

### Valores ausentes

Execute [valores-nulos.sql](exemplos/valores-nulos.sql): somente o contato `2`
deve aparecer, com o texto `sem email`. `IS NULL` consulta ausência de valor;
`= NULL` não produz essa correspondência. `COALESCE` fornece um valor para
exibição, sem alterar os dados. O contato `1` tem string vazia e não é nulo.

Veja a pasta `exercicios`.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
