const porTitulo = new Intl.Collator('pt-BR', { sensitivity: 'base' });

const criterios = {
  titulo: (a, b) => porTitulo.compare(a.titulo, b.titulo),
  data: (a, b) => (a.criadaEm < b.criadaEm ? -1 : Number(a.criadaEm > b.criadaEm)),
};

export function ordenarTarefas(tarefas, criterio = 'titulo') {
  if (!Object.hasOwn(criterios, criterio)) {
    throw new RangeError('Criterio de ordenacao desconhecido.');
  }

  return [...tarefas].sort((a, b) => criterios[criterio](a, b) || a.id - b.id);
}
