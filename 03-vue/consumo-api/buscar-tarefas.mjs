import { validarTarefas } from './validar-tarefas.mjs';

export class ErroHttp extends Error {
  constructor(status) {
    super(`Falha HTTP: ${status}`);
    this.name = 'ErroHttp';
    this.status = status;
  }
}

async function aguardarComCancelamento(promessa, signal) {
  if (!signal) return promessa;
  let cancelar;
  const cancelamento = new Promise((_resolver, rejeitar) => {
    cancelar = () => rejeitar(signal.reason);
    signal.addEventListener('abort', cancelar, { once: true });
    if (signal.aborted) cancelar();
  });
  try {
    return await Promise.race([promessa, cancelamento]);
  } finally {
    signal.removeEventListener('abort', cancelar);
  }
}

export async function buscarTarefas(url, requisitar = globalThis.fetch, { signal, timeoutMs } = {}) {
  signal?.throwIfAborted();
  if (timeoutMs !== undefined) {
    if (!Number.isInteger(timeoutMs) || timeoutMs <= 0 || timeoutMs > 2147483647) {
      throw new RangeError('Tempo limite deve ser um inteiro positivo em milissegundos.');
    }
    const controlador = new AbortController();
    let interromper;
    const interrupcao = new Promise((_resolver, rejeitar) => {
      interromper = motivo => {
        rejeitar(motivo);
        controlador.abort(motivo);
      };
    });
    const cancelar = () => interromper(signal.reason);
    signal?.addEventListener('abort', cancelar, { once: true });
    const timer = setTimeout(() => {
      interromper(new DOMException('Tempo limite da consulta excedido.', 'TimeoutError'));
    }, timeoutMs);
    try {
      return await Promise.race([interrupcao, buscarTarefas(url, requisitar, { signal: controlador.signal })]);
    } finally {
      clearTimeout(timer);
      signal?.removeEventListener('abort', cancelar);
    }
  }
  const resposta = await aguardarComCancelamento(requisitar(url, { headers: { Accept: 'application/json' }, signal }), signal);
  signal?.throwIfAborted();
  if (resposta === null || typeof resposta !== 'object' || typeof resposta.ok !== 'boolean') {
    throw new TypeError('Resposta HTTP invalida.');
  }
  if (!resposta.ok) {
    throw new ErroHttp(resposta.status);
  }

  const tarefas = await aguardarComCancelamento(resposta.json(), signal);
  signal?.throwIfAborted();
  return validarTarefas(tarefas);
}
