# Symfony

## Checklist inicial

- [ ] Fundamentos
- [ ] [Controllers](controllers/README.md)
- [ ] [Routes](routes/README.md)
- [ ] Services
- [ ] [Dependency Injection](dependency-injection/README.md)
- [ ] [Doctrine](doctrine/README.md)
- [ ] [Migrations](migrations/README.md)
- [ ] [Security: autorização por recurso](security/README.md)
- [ ] [APIs: contrato de tarefas](api/README.md)
- [ ] Testes

## Roteiro de um primeiro fluxo

Esta área ainda não contém uma aplicação Symfony executável. Comece após
praticar [POO em PHP](../01-fundamentos/php/poo/README.md) e documente os
requisitos da versão escolhida quando criar um exemplo.

Implemente um único fluxo pequeno, como consultar uma lista de tarefas:

1. Defina a rota e o contrato de resposta, inclusive para uma lista vazia.
2. Deixe o controller traduzir a entrada e delegar a operação a um serviço.
3. Receba as dependências do serviço explicitamente, sem instanciá-las no controller.
4. Comece com dados em memória; introduza Doctrine e migrations quando precisar persistir.
5. Verifique o serviço isoladamente e o contrato HTTP em um teste de integração.

Registre o comando usado para executar a aplicação e as verificações no README
do exemplo. O checklist acima acompanha os temas, mas não indica que já foram implementados.
