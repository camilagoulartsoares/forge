# Padrões de Escrita

## Objetivo

Estudar legibilidade, consistência e PSR-12.

## Conceitos abordados

- PSR-12
- Qualidade de código

## Pré-requisitos

Fundamentos de PHP.

## Como executar

Para um arquivo PHP do estudo, execute `php -l caminho/do/arquivo.php` para
conferir a sintaxe. Esse comando não valida PSR-12 nem comportamento.
Ainda não há ferramenta de estilo configurada neste repositório.

## Exemplos

Ao revisar código PHP, confira:

- Indentação de quatro espaços, conforme o `.editorconfig` do repositório.
- Uma responsabilidade clara por função e nomes que expressem a intenção.
- Comparações estritas (`===` e `!==`) quando o tipo fizer parte da regra.
- Tipos de parâmetros e retorno compatíveis com o contrato da função.
- Ausência de `?>` no final de arquivos que contenham somente PHP.

Uma mudança de comparação flexível para estrita pode alterar resultados;
verifique entradas como `0`, `'0'`, `null` e `false` antes de refatorar.
Formatação consistente e correção funcional devem ser verificadas separadamente.

## Exercícios

Registre exercícios planejados.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências utilizadas.
