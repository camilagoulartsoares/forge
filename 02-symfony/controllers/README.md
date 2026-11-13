# Controller como adaptador HTTP

O controller lê a requisição, chama a operação da aplicação e produz uma
resposta. Para praticar sem misturar responsabilidades, use o
[contrato de tarefas](../api/README.md) como entrada do exercício.

## Divisão proposta

| Etapa | Responsabilidade |
| --- | --- |
| Decodificar corpo | Identificar JSON malformado e formato esperado |
| Preparar entrada | Extrair somente campos aceitos; não persistir o corpo inteiro |
| Executar operação | Delegar validação de negócio e coordenação ao serviço |
| Formar resposta | Mapear resultado ou falha conhecida para o contrato HTTP |

Não coloque a seleção de regras de negócio dentro da serialização. Retorne
um objeto de resposta com os campos públicos necessários, evitando expor
automaticamente entidades completas e suas associações.

## Exercício de teste

Substitua o serviço por um fake que registra a entrada recebida. Verifique
que JSON inválido não chama o serviço; campos extras não viram propriedades
persistidas; e o sucesso devolve somente os campos previstos no contrato.
Uma falha inesperada deve seguir o tratamento de erro da aplicação sem
transformar uma operação malsucedida em resposta de sucesso.

Este roteiro não instala Symfony. Referência: [Symfony Controllers](https://symfony.com/doc/current/controller.html).

## Adaptador executável

[lerCriacaoTarefa](ler-criacao-tarefa.php) decodifica o objeto JSON, valida o
Content-Type e o título, e devolve somente os campos permitidos. Não instala
servidor nem persiste dados. Execute `php 02-symfony/controllers/criacao-test.php`.
