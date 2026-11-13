import { buscarTarefas } from '../consumo-api/buscar-tarefas.mjs';
import { validarTarefas } from '../consumo-api/validar-tarefas.mjs';

export function criarConsultaTarefas(buscar = buscarTarefas) {
  let estado = Object.freeze({ dados: Object.freeze([]), erro: null, carregando: false });
  let versao = 0;
  let controlador;
  let descartada = false;
  const assinantes = new Set();

  function notificar(ouvinte, snapshot = estado) {
    try {
      ouvinte(snapshot);
    } catch (erro) {
      console.error('Erro em assinante de consulta:', erro);
    }
  }

  function publicar(proximo) {
    estado = Object.freeze(proximo);
    const snapshot = estado;
    for (const ouvinte of [...assinantes]) {
      if (estado !== snapshot) break;
      if (assinantes.has(ouvinte)) notificar(ouvinte, snapshot);
    }
  }

  function cancelar() {
    const atual = ++versao;
    const anterior = controlador;
    controlador = undefined;
    anterior?.abort();
    if (atual === versao) publicar({ ...estado, carregando: false });
  }

  async function recarregar(url) {
    if (descartada) throw new Error('Consulta ja descartada.');
    const atual = ++versao;
    const anterior = controlador;
    const controller = new AbortController();
    controlador = controller;
    anterior?.abort();
    if (atual !== versao || controller.signal.aborted) return;
    publicar({ ...estado, erro: null, carregando: true });
    if (atual !== versao || controller.signal.aborted) return;
    try {
      const dados = await buscar(url, undefined, { signal: controller.signal });
      if (atual === versao && !controller.signal.aborted) {
        validarTarefas(dados);
        publicar({ ...estado, dados: Object.freeze(dados.map((t) => Object.freeze({ ...t }))) });
      }
    } catch (erro) {
      if (atual === versao && !controller.signal.aborted) {
        publicar({ ...estado, erro });
      }
    } finally {
      if (atual === versao) publicar({ ...estado, carregando: false });
    }
  }

  return {
    get estado() { return estado; },
    recarregar,
    cancelar,
    assinar(ouvinte) {
      if (descartada) throw new Error('Consulta ja descartada.');
      if (typeof ouvinte !== 'function') throw new TypeError('Assinante deve ser uma funcao.');
      assinantes.add(ouvinte);
      notificar(ouvinte);
      return () => assinantes.delete(ouvinte);
    },
    descartar() {
      if (descartada) return;
      descartada = true;
      cancelar();
      assinantes.clear();
    },
  };
}
