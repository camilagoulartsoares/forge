# Testes Automatizados em PHP

## Objetivo

Praticar testes automatizados e sua organização.

## Conceitos abordados

- Pirâmide de testes
- PHPUnit

## Pré-requisitos

Fundamentos de PHP.

## Como executar

Não há PHPUnit instalado. Os testes didáticos usam verificações explícitas
que lançam exceção em caso de falha, sem depender da configuração de `assert`.
Na raiz do repositório, execute:

```sh
php 01-fundamentos/php/testes-automatizados/exemplos/total-test.php
```

O sucesso imprime `OK: total` e retorna código zero; uma falha interrompe a execução.
O [teste de total](exemplos/total-test.php) cobre cálculo, zeros, negativos e tipos.

## Exemplos

## Matriz de casos

Para uma operação de estoque que aceite apenas quantidades positivas:

| Cenário | Ação | Resultado esperado |
| --- | --- | --- |
| Saldo inicial | Criar estoque | Saldo zero |
| Adição válida | Adicionar três unidades | Saldo três |
| Acumulação | Adicionar duas e depois três | Saldo cinco |
| Limite inválido | Adicionar zero | Exceção; saldo preservado |
| Número inválido | Adicionar uma quantidade negativa | Exceção; saldo preservado |
| Isolamento | Adicionar em apenas uma de duas instâncias | Outra instância permanece zerada |

Use o [exemplo de POO](../poo/README.md) como objeto do exercício. Em cada
teste, prepare uma instância nova, execute uma ação e verifique o resultado.
Nos casos de erro, verifique também que o estado não foi alterado. Evite
dependência da ordem de execução e de serviços externos para essa regra.

## Exercícios

Veja a pasta `exercicios`.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
