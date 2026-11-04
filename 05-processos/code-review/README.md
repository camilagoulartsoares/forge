# Code Review

## Objetivo

Revisar correção, clareza e riscos de uma mudança, oferecendo comentários
que ajudem o autor a decidir e agir.

## Como revisar

1. Leia o objetivo e os critérios de aceitação antes do diff.
2. Confira se a mudança resolve o cenário descrito e preserva os demais contratos.
3. Procure entradas inválidas, efeitos colaterais e falhas que ficaram sem tratamento.
4. Confira se as verificações exercitam o comportamento alterado e se os resultados foram informados.
5. Verifique instruções e exemplos afetados; execute a reprodução local quando aplicável.

Identifique o arquivo e o comportamento ao comentar. Diferencie um bloqueio
de correção, uma dúvida e uma sugestão opcional. Explique o efeito concreto:
"Com uma lista vazia, o acesso ao primeiro item falha; podemos tratar esse caso?"
é mais acionável do que "melhorar validação".

## Antes de concluir

Confirme quais pontos foram resolvidos e quais continuam pendentes. Quando
houver nova versão do diff, revise os trechos alterados e seus efeitos antes
de aprovar. Use o [checklist de Merge Request](../../01-fundamentos/git/merge-requests/checklist.md)
para conferir os itens da entrega.
