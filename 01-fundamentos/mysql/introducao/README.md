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

Veja a pasta `exercicios`.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
