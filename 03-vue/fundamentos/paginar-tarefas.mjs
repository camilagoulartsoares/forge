import { selecionarTarefas } from './selecionar-tarefas.mjs';

export function paginarTarefas(tarefas, { pagina = 1, tamanho = 20, ...filtros } = {}) {
  if (!Number.isSafeInteger(pagina) || pagina < 1
    || !Number.isSafeInteger(tamanho) || tamanho < 1 || tamanho > 100) {
    throw new RangeError('Pagina deve ser positiva e tamanho deve estar entre 1 e 100.');
  }
  const selecao = selecionarTarefas(tarefas, filtros);
  const paginas = Math.ceil(selecao.encontradas / tamanho);
  const itens = pagina > paginas ? [] : selecao.itens.slice((pagina - 1) * tamanho, pagina * tamanho);
  return { ...selecao, itens, pagina, tamanho, paginas };
}
