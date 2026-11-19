import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';

// O componente comunica uma intencao; o pai continua dono do estado.
export function criarIntencaoConclusao(tarefa, ocupado = false) {
  validarTarefas([tarefa]);
  if (typeof ocupado !== 'boolean') throw new TypeError('Estado ocupado deve ser booleano.');
  return ocupado ? null : { evento: 'alterar-conclusao', payload: { id: tarefa.id, concluida: !tarefa.concluida } };
}
