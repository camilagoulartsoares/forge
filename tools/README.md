# Verificação da documentação

Requer Python 3.10 ou superior e Git. Na raiz do repositório:

```sh
python tools/check_docs.py
```

O comando lê documentos Markdown versionados e novos arquivos não ignorados.
Verifica UTF-8, fechamento de blocos cercados por crases ou tils e existência
dos destinos de links e imagens locais escritos na forma `[texto](caminho)`.
Blocos de código e código inline não são tratados como links.

Links externos não são acessados. Âncoras, links por referência e sintaxe
Markdown com parênteses aninhados no destino precisam de revisão manual.
Links fora do repositório são rejeitados para manter o material portável.
O código de saída é zero em caso de sucesso e diferente de zero em caso de erro.
