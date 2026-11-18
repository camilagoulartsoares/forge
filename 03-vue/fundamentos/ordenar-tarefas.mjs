const porTitulo = new Intl.Collator('pt-BR', { sensitivity: 'base' });

const criterios = {
  titulo: (a, b) => porTitulo.compare(a.titulo, b.titulo),
  data: (a, b) => (a.criadaEm < b.criadaEm ? -1 : Number(a.criadaEm > b.criadaEm)),
};

export function ordenarTarefas(tarefas, criterio = 'titulo', direcao = 'asc') {
  if (typeof criterio !== 'string' || !Object.hasOwn(criterios, criterio)) {
    throw new RangeError('Criterio de ordenacao desconhecido.');
  }

  if (direcao !== 'asc' && direcao !== 'desc') throw new RangeError('Direcao de ordenacao desconhecida.');

  if (criterio === 'data') {
    for (const tarefa of tarefas) {
      const data = tarefa.criadaEm;
      if (typeof data !== 'string' || !/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/.test(data)
        || !Number.isFinite(Date.parse(data)) || new Date(data).toISOString() !== data) {
        throw new TypeError('Data deve usar UTC ISO valida com milissegundos.');
      }
    }
  }

  return [...tarefas].sort((a, b) => (direcao === 'desc' ? -1 : 1) * criterios[criterio](a, b) || a.id - b.id);
}
