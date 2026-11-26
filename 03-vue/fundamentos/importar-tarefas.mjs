import { decodificarJson } from './json-estrito.mjs';
import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';

// Nenhuma escrita ocorre antes de validar o lote inteiro, inclusive conflitos com a origem.
export function importarTarefas(tarefas, json) {
  if (typeof json !== 'string') throw new TypeError('Importacao exige texto JSON.');
  validarTarefas(tarefas);
  const novas = validarTarefas(decodificarJson(json));
  // Preserva os indices ja validados, sem delegar o snapshot ao iterador da origem.
  const atuais = Array.from({ length: tarefas.length }, (_, indice) => tarefas[indice]);
  const resultado = atuais.concat(novas.map(tarefa => ({ ...tarefa })));
  return validarTarefas(resultado);
}
