# Exercício de autorização por recurso

Autenticação estabelece a identidade; autorização determina quais ações essa
identidade pode executar. Estar autenticado não concede acesso a toda tarefa.

## Cenário fictício

Considere usuários A e B e tarefas pertencentes a cada um. Defina no backend
uma regra que permita consultar e alterar somente tarefas próprias:

| Situação | Resultado esperado |
| --- | --- |
| A consulta uma tarefa de A | Permitido |
| A tenta alterar uma tarefa de B | Negado; tarefa preservada |
| A troca o identificador na URL para o de B | Negado |
| A informa no corpo um proprietário diferente | Não transfere propriedade |
| Pessoa sem autenticação tenta alterar | Negado |
| A consulta a listagem | Apenas os recursos autorizados são retornados |

Obtenha a identidade do contexto autenticado no servidor. Não confie em
`usuarioId` recebido do navegador para decidir quem está agindo. Verifique
a permissão em cada operação; esconder um botão não protege o endpoint.

## Verificação

Use identidades fictícias nos testes de integração e confira também a
ausência de alteração após a recusa. Defina respostas consistentes que não
revelem recursos de terceiros. Comece negando operações sem permissão explícita.
Este roteiro não implementa autenticação nem configura um sistema em produção.

A [política PHP](autorizacao.php) permite testar identidade e propriedade sem
framework: `php 02-symfony/security/autorizacao-test.php`. A listagem rejeita
registros sem proprietário inteiro válido e reindexa a saída. O chamador deve
obter a identidade da autenticação no servidor; o helper não autentica ninguém
nem protege automaticamente endpoints que não o utilizam.

Referência: [OWASP: Authorization Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authorization_Cheat_Sheet.html).
