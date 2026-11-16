import { validarTarefas } from './validar-tarefas.mjs';

export class ErroHttp extends Error {
  constructor(status) {
    super(`Falha HTTP: ${status}`);
    this.name = 'ErroHttp';
    this.status = status;
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
  const resposta = await requisitar(url, { headers: { Accept: 'application/json' }, signal });
  signal?.throwIfAborted();
  if (!resposta.ok) {
    throw new ErroHttp(resposta.status);
  }

  const tarefas = await resposta.json();
  signal?.throwIfAborted();
  return validarTarefas(tarefas);
}
