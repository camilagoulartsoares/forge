import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';
import { validarTitulo } from '../formularios/validar-titulo.mjs';
import { identificadorValido } from './identificador.mjs';

export function renomearTarefa(tarefas, id, titulo) {
  validarTarefas(tarefas);
  const validacao = validarTitulo(titulo);
  if (!identificadorValido(id) || !validacao.valido) throw new TypeError('Renomeacao invalida.');
  const indice = tarefas.findIndex(tarefa => tarefa.id === id);
  if (indice < 0) throw new RangeError('Tarefa nao encontrada.');
  if (tarefas[indice].titulo === validacao.titulo) return tarefas;
  return tarefas.map((tarefa, posicao) => posicao === indice ? { ...tarefa, titulo: validacao.titulo } : tarefa);
}
