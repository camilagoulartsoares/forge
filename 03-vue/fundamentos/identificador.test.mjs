import test from 'node:test';
import assert from 'node:assert/strict';
import { identificadorValido } from './identificador.mjs';

test('identificadores aceitam somente inteiros positivos seguros, sem coercao', () => {
  for (const valor of [1, Number.MAX_SAFE_INTEGER]) assert.equal(identificadorValido(valor), true);
  for (const valor of [0, -1, 1.5, NaN, Infinity, '1', 1n, null, Number.MAX_SAFE_INTEGER + 1]) {
    assert.equal(identificadorValido(valor), false);
  }
});
