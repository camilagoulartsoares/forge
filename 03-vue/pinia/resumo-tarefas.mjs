import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';

// Getter puro: nao armazena um segundo estado mutavel para as contagens.
export function resumirTarefas(tarefas) {
  validarTarefas(tarefas);
  const concluidas = tarefas.reduce((total, tarefa) => total + Number(tarefa.concluida), 0);
  return { total: tarefas.length, pendentes: tarefas.length - concluidas, concluidas };
}
