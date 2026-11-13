import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';
import { filtrarTarefas } from './filtrar-tarefas.mjs';
import { pesquisarTarefas } from './pesquisar-tarefas.mjs';
import { ordenarTarefas } from './ordenar-tarefas.mjs';

// Ordem deliberada: validar, filtrar, pesquisar e ordenar sem mutar a origem.
export function selecionarTarefas(tarefas, { estado = 'todas', termo = '', criterio = 'titulo' } = {}) {
  validarTarefas(tarefas);
  const filtradas = pesquisarTarefas(filtrarTarefas(tarefas, estado), termo);
  return { itens: ordenarTarefas(filtradas, criterio), total: tarefas.length, encontradas: filtradas.length };
}
