import test from 'node:test';
import assert from 'node:assert/strict';
import { lerFiltroDaUrl, atualizarFiltroDaUrl } from './parametros-url.mjs';

test('normaliza filtros ambiguos e preserva parametros repetidos sem mutar origem', () => {
  const origem = new URLSearchParams('tag=PHP&tag=JS&estado=pendentes&estado=concluidas&q=a+b');
  assert.equal(lerFiltroDaUrl(origem), 'todas');
  const nova = atualizarFiltroDaUrl(origem, 'concluidas');
  assert.deepEqual(nova.getAll('tag'), ['PHP', 'JS']);
  assert.equal(nova.get('q'), 'a b');
  assert.equal(lerFiltroDaUrl(nova), 'concluidas');
  assert.equal(origem.getAll('estado').length, 2);
  assert.equal(atualizarFiltroDaUrl(nova, 'todas').has('estado'), false);
  assert.equal(lerFiltroDaUrl(new URLSearchParams()), 'todas');
  assert.throws(() => atualizarFiltroDaUrl(origem, 'invalido'), RangeError);
});
