import { aguardarComCancelamento } from './aguardar-cancelamento.mjs';
import { validarTarefas } from './validar-tarefas.mjs';

export class ErroHttp extends Error {
  constructor(status) {
    if (!Number.isInteger(status) || status < 0 || status > 599 || (status > 0 && status < 100)) {
      throw new TypeError('Status HTTP invalido.');
    }
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
    const prazo = performance.now() + timeoutMs;
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
    const expirar = () => {
      const motivo = new DOMException('Tempo limite da consulta excedido.', 'TimeoutError');
      interromper(motivo);
      return motivo;
    };
    const verificarPrazo = () => {
      if (!controlador.signal.aborted && performance.now() >= prazo) throw expirar();
    };
    const timer = setTimeout(expirar, timeoutMs);
    try {
      const resultado = await Promise.race([interrupcao, buscarTarefas(url, requisitar, { signal: controlador.signal })]);
      verificarPrazo();
      return resultado;
    } catch (erro) {
      verificarPrazo();
      throw erro;
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
