import { buscarTarefas } from '../consumo-api/buscar-tarefas.mjs';

export function criarConsultaTarefas(buscar = buscarTarefas) {
  let estado = Object.freeze({ dados: Object.freeze([]), erro: null, carregando: false });
  let versao = 0;
  let controlador;
  let descartada = false;

  function cancelar() {
    versao += 1;
    controlador?.abort();
    controlador = undefined;
    estado = Object.freeze({ ...estado, carregando: false });
  }

  async function recarregar(url) {
    if (descartada) throw new Error('Consulta ja descartada.');
    const atual = ++versao;
    controlador?.abort();
    const controller = new AbortController();
    controlador = controller;
    estado = Object.freeze({ ...estado, erro: null, carregando: true });
    try {
      const dados = await buscar(url, undefined, { signal: controller.signal });
      if (atual === versao && !controller.signal.aborted) {
        estado = Object.freeze({ ...estado, dados: Object.freeze(dados.map((t) => Object.freeze({ ...t }))) });
      }
    } catch (erro) {
      if (atual === versao && !controller.signal.aborted) {
        estado = Object.freeze({ ...estado, erro });
      }
    } finally {
      if (atual === versao) estado = Object.freeze({ ...estado, carregando: false });
    }
  }

  return {
    get estado() { return estado; },
    recarregar,
    cancelar,
    descartar() { cancelar(); descartada = true; },
  };
}
