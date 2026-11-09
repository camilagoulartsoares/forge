# Injeção de dependências

Uma classe recebe os colaboradores de que precisa em vez de criá-los
internamente. O container pode montar esses objetos; a classe não precisa
consultar o container para executar sua regra.

## Exercício: mensagem dependente do horário

Crie um serviço que escolhe uma saudação conforme a hora atual. Primeiro,
identifique como obter a hora dentro do método torna o teste dependente
do momento em que é executado. Depois:

1. Defina um contrato de relógio que fornece o instante atual.
2. Receba o relógio no construtor do serviço de saudação.
3. No teste, passe um relógio fixo, sem alterar o horário do sistema.
4. Defina as faixas de saudação, o fuso e os limites antes de implementar.
5. Teste instantes imediatamente antes e depois de cada transição.

A implementação real de relógio pertence à montagem da aplicação. Não crie
uma condição dentro da regra apenas para descobrir se está sendo testada.

## Integração futura com Symfony

Em um projeto Symfony instalado, configure qual serviço atende ao contrato
do relógio. Quando houver várias implementações, torne essa escolha explícita.
Autowiring ajuda a conectar dependências, mas não escolhe regras de negócio
nem substitui a definição de contratos e valores de configuração.

Esta pasta contém o roteiro, sem aplicação Symfony instalada.
Referência: [Symfony: Service Container](https://symfony.com/doc/current/service_container.html).
