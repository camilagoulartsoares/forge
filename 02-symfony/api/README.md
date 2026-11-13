# Contrato didático de uma API de tarefas

Este documento especifica um exercício; não há servidor ou endpoint
implementado nesta pasta. Use dados fictícios e defina o contrato antes do controller.

## Consulta

`GET /tarefas` retorna `200` com `Content-Type: application/json` e um array:

```json
[{ "id": 1, "titulo": "Estudar PHP", "concluida": false }]
```

Sem registros, o corpo deve ser `[]`. Cada `id` é um inteiro positivo seguro
para JavaScript. `titulo` é textual e não vazio; `concluida` é booleano.
Esse formato pode ser consumido pelo [exemplo JavaScript](../../03-vue/consumo-api/README.md).

## Criação

`POST /tarefas` recebe `{"titulo":"Estudar PHP"}`. No exercício, remova espaços
das bordas e aceite de 1 a 120 unidades UTF-16, contando como o validador frontend.
Não confunda essa medida com bytes ou caracteres visuais ao implementar em PHP.
Uma criação válida retorna `201`, o recurso criado e `Location: /tarefas/1`.

| Situação | Status definido para o exercício |
| --- | --- |
| JSON malformado | 400 |
| Tipo de conteúdo não suportado | 415 |
| Título ausente, vazio ou acima do limite | 422 |
| Falha inesperada | 500, sem detalhes internos |

Para erros de entrada, padronize um corpo como
`{"erro":"entrada_invalida","campos":{"titulo":"Titulo obrigatorio."}}`.
Teste status, cabeçalhos e corpo; não use `200` com uma mensagem de erro.
Essa política de erros é uma escolha do exercício, não uma regra universal de API.

O [validador PHP](validar-titulo.php) implementa o contrato de título sem
extensões mbstring ou iconv. Execute `php 02-symfony/api/titulo-test.php` na raiz.
Ele valida UTF-8, reproduz os espaços removidos por `String.trim` e conta
caracteres fora do plano básico como duas unidades UTF-16.

O [mapeador de erros](resposta-erro.php) produz status, cabeçalhos e corpo
para um adaptador HTTP. Falhas inesperadas recebem um corpo fixo sem mensagens
internas; erros tipados de acesso e ausência usam 403 e 404. Execute
`php 02-symfony/api/resposta-erro-test.php`.
