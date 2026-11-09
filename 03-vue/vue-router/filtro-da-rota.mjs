const estados = ['todas', 'pendentes', 'concluidas'];

export function lerFiltroDaRota(query) {
  const estado = query?.estado;
  return typeof estado === 'string' && estados.includes(estado) ? estado : 'todas';
}

export function atualizarFiltroDaRota(query, estado) {
  if (!estados.includes(estado)) {
    throw new RangeError('Filtro de rota desconhecido.');
  }
  const resultado = { ...query };
  if (estado === 'todas') {
    delete resultado.estado;
  } else {
    resultado.estado = estado;
  }
  return resultado;
}
