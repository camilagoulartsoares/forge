import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';

// Exportacao do contrato publico: metadados locais nao acompanham o arquivo.
export function exportarTarefas(tarefas) {
  validarTarefas(tarefas);
  return JSON.stringify(tarefas.map(({ id, titulo, concluida }) => ({ id, titulo, concluida })), null, 2);
}
