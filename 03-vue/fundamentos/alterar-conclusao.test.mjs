import test from 'node:test';
import assert from 'node:assert/strict';
import { alterarConclusao } from './alterar-conclusao.mjs';
import { criarIntencaoConclusao } from '../componentes/intencao-conclusao.mjs';

test('pai aplica intencao de reabertura sem mutar props e sem inverter duas vezes', () => {
  const primeira = Object.freeze({ id: 1, titulo: 'Revisar', concluida: true });
  const outra = Object.freeze({ id: 2, titulo: 'Testar', concluida: false });
  const origem = Object.freeze([primeira, outra]);
  const { payload } = criarIntencaoConclusao(primeira);
  const resultado = alterarConclusao(origem, payload.id, payload.concluida);
  assert.equal(resultado[0].concluida, false);
  assert.equal(primeira.concluida, true);
  assert.equal(resultado[1], outra);
  assert.equal(alterarConclusao(resultado, payload.id, payload.concluida), resultado);
  assert.equal(alterarConclusao(resultado, 1, true)[0].concluida, true);
});

test('recusa intencoes invalidas e alvos ausentes sem alterar a lista', () => {
  const origem = Object.freeze([{ id: 1, titulo: 'A', concluida: false }]);
  for (const valor of [null, 0, 'false', undefined]) assert.throws(() => alterarConclusao(origem, 1, valor), TypeError);
  assert.throws(() => alterarConclusao(origem, 0, false), TypeError);
  assert.throws(() => alterarConclusao(origem, 2, false), RangeError);
  assert.equal(origem[0].concluida, false);
});
