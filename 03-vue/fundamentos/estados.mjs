export const estadosDeTarefa = Object.freeze(['todas', 'pendentes', 'concluidas']);

export const estadoValido = (valor) => typeof valor === 'string' && estadosDeTarefa.includes(valor);
