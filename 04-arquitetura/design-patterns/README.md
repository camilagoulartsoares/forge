# Design Patterns: escolha de estratégias

## Problema do exercício

Uma lista de tarefas pode ser ordenada por título ou por data de criação.
O chamador precisa escolher o critério sem duplicar o fluxo de listagem.
Esse cenário permite estudar Strategy: representar uma política variável
por um contrato que o contexto utiliza.

## Proposta de prática

1. Defina o contrato de comparação para duas tarefas.
2. Implemente os critérios por título e por data separadamente.
3. Passe o critério ao fluxo de ordenação, por função ou objeto.
4. Verifique lista vazia, um item, empates e inversão da ordem.
5. Defina um desempate por identificador para tornar o resultado previsível.

Registre se a entrada será modificada ou se uma nova lista será retornada.
Escolha explicitamente como tratar datas ausentes e títulos equivalentes.
Essas decisões fazem parte do contrato, não do nome do padrão.

## Quando simplificar

Se houver um único critério pequeno, uma função direta pode bastar. Compare
o custo de criar classes com o de passar uma função de comparação. Adote a
separação quando políticas independentes precisarem mudar ou ser testadas
separadamente, evitando adicionar estruturas sem uma necessidade demonstrada.
