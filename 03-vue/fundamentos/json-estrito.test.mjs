import test from 'node:test';
import assert from 'node:assert/strict';
import { decodificarJson } from './json-estrito.mjs';

test('valida nomes por objeto sem confundir caracteres estruturais dentro de strings', () => {
  assert.deepEqual(decodificarJson('[{"id":1},{"id":2}]'), [{ id: 1 }, { id: 2 }]);
  assert.deepEqual(decodificarJson('{"a":1,"A":2}'), { a: 1, A: 2 });
  for (const json of ['{"a":1,"a":2}', '{"a":1,"\\u0061":2}', '1e999', '"\\ud800"', '{"\\udfff":1}']) {
    assert.throws(() => decodificarJson(json), SyntaxError);
  }
  const texto = '\\'.repeat(10000) + '"{:}';
  assert.equal(decodificarJson(JSON.stringify({ texto })).texto, texto);
  assert.equal(decodificarJson('"\\ud83d\\ude00"'), '\u{1F600}');
});
