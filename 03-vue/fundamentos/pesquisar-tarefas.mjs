const normalizar = (texto) => texto.normalize('NFD').replace(/\p{M}/gu, '').toLocaleLowerCase('pt-BR');

export function pesquisarTarefas(tarefas, consulta) {
  if (typeof consulta !== 'string') {
    throw new TypeError('Consulta deve ser textual.');
  }
  const termos = normalizar(consulta).trim().split(/\s+/).filter(Boolean);
  return tarefas.filter((tarefa) => termos.every((termo) => normalizar(tarefa.titulo).includes(termo)));
}
