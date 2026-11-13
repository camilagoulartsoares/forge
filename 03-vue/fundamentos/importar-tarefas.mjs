import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';

// Nenhuma escrita ocorre antes de validar o lote inteiro, inclusive conflitos com a origem.
export function importarTarefas(tarefas, json) {
  if (typeof json !== 'string') throw new TypeError('Importacao exige texto JSON.');
  validarTarefas(tarefas);
  const novas = validarTarefas(JSON.parse(json));
  validarTarefas([...tarefas, ...novas]);
  return [...tarefas, ...novas.map(tarefa => ({ ...tarefa }))];
}
