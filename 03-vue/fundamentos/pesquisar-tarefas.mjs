const normalizar = (texto) => texto.normalize('NFD').replace(/\p{M}/gu, '').toLocaleLowerCase('pt-BR');

export function pesquisarTarefas(tarefas, consulta) {
  if (typeof consulta !== 'string') {
    throw new TypeError('Consulta deve ser textual.');
  }
  const termos = [...new Set(normalizar(consulta).trim().split(/\s+/).filter(Boolean))];
  if (termos.length === 0) return [...tarefas];
  return tarefas.filter((tarefa) => {
    const titulo = normalizar(tarefa.titulo);
    return termos.every((termo) => titulo.includes(termo));
  });
}
