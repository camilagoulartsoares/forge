import { test } from 'node:test';
import assert from 'node:assert/strict';
import { validarTitulo } from './validar-titulo.mjs';

test('recusa valores ausentes e titulos em branco', () => {
  for (const valor of [null, undefined, 42, {}, '', '   ', '\n\t']) {
    assert.equal(validarTitulo(valor).valido, false);
    assert.equal(typeof validarTitulo(valor).erro, 'string');
  }
});

test('normaliza bordas e preserva espacos internos', () => {
  assert.deepEqual(validarTitulo('  Estudar PHP  hoje  '), { valido: true, titulo: 'Estudar PHP  hoje' });
});

test('aceita o limite e recusa o primeiro tamanho acima dele', () => {
  assert.equal(validarTitulo('a'.repeat(120)).valido, true);
  assert.equal(validarTitulo('a'.repeat(121)).valido, false);
});
