import { lerFiltroDaRota, atualizarFiltroDaRota } from './filtro-da-rota.mjs';

// Recebe e devolve URLSearchParams; o chamador continua responsavel pela navegacao.
export function lerFiltroDaUrl(parametros) {
  const valores = parametros.getAll('estado');
  return lerFiltroDaRota({ estado: valores.length === 1 ? valores[0] : valores });
}

export function atualizarFiltroDaUrl(parametros, estado) {
  const query = atualizarFiltroDaRota({}, estado);
  const resultado = new URLSearchParams(parametros);
  resultado.delete('estado');
  if (query.estado) resultado.set('estado', query.estado);
  return resultado;
}
