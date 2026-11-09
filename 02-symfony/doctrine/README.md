# Exercício: consultas e associações no Doctrine

Em um projeto de estudos com Doctrine instalado, modele pedidos associados
a clientes e apresente uma lista com número do pedido e nome do cliente.
Use apenas dados fictícios. Esta pasta não contém uma instalação do ORM.

## Investigar antes de otimizar

1. Crie uma amostra com vários pedidos e clientes, incluindo pedidos do mesmo cliente.
2. Execute a listagem e registre as consultas emitidas ao acessar as associações.
3. Compare com uma consulta que busca os dados necessários de forma explícita.
4. Confirme que a lista resultante mantém os mesmos dados e ordenação.

Carregar uma associação separadamente para cada resultado pode produzir
o padrão N+1. Em DQL, selecionar a entidade associada em uma junção pode
formar um fetch join, carregando os dados associados na mesma consulta.
Se a tela usa poucos campos, avalie também uma projeção em vez de hidratar tudo.

## Cuidados na comparação

Não conclua que qualquer `JOIN` elimina N+1. Observe o SQL e o que foi
selecionado. Associações de coleção podem multiplicar linhas e complicar
paginação; verifique contagens e limites com dados que realmente exerçam isso.
Registre a quantidade de consultas e o resultado antes e depois, sem adotar
um número universal como critério para toda funcionalidade.

Referência: [Doctrine DQL: joins](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html).
