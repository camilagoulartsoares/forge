import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';
import { validarTitulo } from '../formularios/validar-titulo.mjs';
import { identificadorValido } from './identificador.mjs';

// O identificador e fornecido pelo chamador; nao simula geracao pelo servidor.
export function adicionarTarefa(tarefas, id, titulo) {
  validarTarefas(tarefas);
  const resultado = validarTitulo(titulo);
  if (!identificadorValido(id) || !resultado.valido) throw new TypeError('Nova tarefa invalida.');
  if (tarefas.some(tarefa => tarefa.id === id)) throw new RangeError('Identificador duplicado.');
  return [...tarefas, { id, titulo: resultado.titulo, concluida: false }];
}
