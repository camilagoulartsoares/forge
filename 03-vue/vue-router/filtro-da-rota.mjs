import { estadoValido } from '../fundamentos/estados.mjs';

export function lerFiltroDaRota(query) {
  const estado = query != null && Object.hasOwn(query, 'estado') ? query.estado : undefined;
  return estadoValido(estado) ? estado : 'todas';
}

export function atualizarFiltroDaRota(query, estado) {
  if (!estadoValido(estado)) {
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
