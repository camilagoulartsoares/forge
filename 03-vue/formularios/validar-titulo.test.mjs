import { test } from 'node:test';
import assert from 'node:assert/strict';
import { validarTitulo } from './validar-titulo.mjs';
import { readFileSync } from 'node:fs';

test('cumpre os mesmos exemplos de contrato executados pelo PHP', () => {
  const casos = JSON.parse(readFileSync(new URL('../../01-fundamentos/php/testes-automatizados/exemplos/casos-titulo.json', import.meta.url), 'utf8'));
  for (const caso of casos) {
    const resultado = validarTitulo(caso.entrada);
    assert.equal(resultado.valido, caso.valido);
    if (caso.valido) assert.equal(resultado.titulo, caso.titulo);
  }
});

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

test('mede UTF-16 inclusive emojis e caracteres combinantes', () => {
  assert.equal(validarTitulo('😀'.repeat(60)).valido, true);
  assert.equal(validarTitulo('😀'.repeat(61)).valido, false);
  assert.equal(validarTitulo('a\u0301'.repeat(60)).valido, true);
  assert.equal(validarTitulo('a\u0301'.repeat(60) + 'a').valido, false);
});

test('normaliza espacos Unicode nas bordas antes de medir', () => {
  assert.deepEqual(validarTitulo('\u00a0Estudar\u2003'), { valido: true, titulo: 'Estudar' });
  assert.equal(validarTitulo('\u00a0\u2003').valido, false);
  assert.equal(validarTitulo(' ' + 'a'.repeat(120) + ' ').valido, true);
});

test('rejeita surrogates isolados que nao podem ser enviados como UTF-8 valido', () => {
  for (const titulo of ['\uD800', '\uDC00', 'A\uD800B', '\uDC00\uD800']) {
    assert.equal(validarTitulo(titulo).valido, false);
  }
  assert.equal(validarTitulo('\uD83D\uDE00').valido, true);
});
