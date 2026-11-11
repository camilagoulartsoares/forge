import { validarTarefas } from './validar-tarefas.mjs';

export class ErroHttp extends Error {
  constructor(status) {
    super(`Falha HTTP: ${status}`);
    this.name = 'ErroHttp';
    this.status = status;
  }
}

export async function buscarTarefas(url, requisitar = globalThis.fetch, { signal } = {}) {
  signal?.throwIfAborted();
  const resposta = await requisitar(url, { headers: { Accept: 'application/json' }, signal });
  signal?.throwIfAborted();
  if (!resposta.ok) {
    throw new ErroHttp(resposta.status);
  }

  const tarefas = await resposta.json();
  signal?.throwIfAborted();
  return validarTarefas(tarefas);
}
