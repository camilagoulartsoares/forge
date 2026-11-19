# Introdução ao PHP

## Objetivo

Descrever fundamentos da linguagem PHP.

## Conceitos abordados

- Sintaxe básica
- Tipos e controle de fluxo

## Pré-requisitos

PHP instalado localmente.

## Como executar

Na raiz do repositorio, execute o exemplo versionado:

```sh
php -l 01-fundamentos/php/introducao/exemplos/calcular-total.php
php 01-fundamentos/php/introducao/exemplos/executar.php
```

O primeiro comando verifica a sintaxe. O segundo deve imprimir `Total: 60`.

## Exemplos

Veja a [regra de total](exemplos/calcular-total.php) e o [programa executavel](exemplos/executar.php).

O exemplo trabalha com valores inteiros; não representa cálculo financeiro
com frações. Experimente quantidade zero e confirme total zero. Um argumento
negativo deve provocar `InvalidArgumentException` em vez de produzir um total inválido.
Produtos acima de `PHP_INT_MAX` lançam `OverflowException` antes da multiplicação,
evitando conversão implícita para ponto flutuante e falha no tipo de retorno.

## Exercícios

Veja a pasta `exercicios`.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.

## Leitura JSON sem ambiguidades

O [decoder estrito](exemplos/json-estrito.php) usa `json_decode` para validar
a gramatica, UTF-8 e profundidade; em seguida rejeita nomes de propriedades
duplicados (inclusive escapes equivalentes) e numeros convertidos para infinito.
Objetos distintos podem repetir os mesmos nomes. O leitor HTTP e o adaptador
de pedidos usam essa funcao para nao aceitar dados diferentes das fixtures.
Execute `php 01-fundamentos/php/introducao/exemplos/json-estrito-test.php`.

Os [casos de aceitacao JSON compartilhados](../testes-automatizados/exemplos/casos-json.json)
sao executados pelos decoders PHP, JavaScript e Python. Incluem escapes,
Unicode, duplicidades por objeto, nomes especiais e expoentes fora do intervalo.
Essa matriz verifica o subconjunto interoperavel usado nos exemplos, sem prometer
identidade de representacao para numeros arbitrariamente grandes.

## Tipagem depende do chamador

`declare(strict_types=1)` nao torna uma funcao estrita para todos os consumidores:
a chamada segue a configuracao do arquivo que a realiza. O
[exemplo de chamada coerciva](exemplos/chamada-coerciva.php) usa intencionalmente
`strict_types=0`: strings numericas chegam como inteiros ao calculo. O
[teste comparativo](exemplos/tipagem-test.php) mostra que a mesma entrada e
rejeitada a partir de um chamador estrito, enquanto as regras contra negativos
continuam valendo nos dois casos. Nao confunda essa conversao com validacao de
entrada HTTP. Execute `php 01-fundamentos/php/introducao/exemplos/tipagem-test.php`.
