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

test('executa os mesmos casos JSON usados no PHP e Python', async () => {
  const { readFile } = await import('node:fs/promises');
  const casos = JSON.parse(await readFile(new URL('../../01-fundamentos/php/testes-automatizados/exemplos/casos-json.json', import.meta.url), 'utf8'));
  for (const caso of casos) {
    if (caso.valido) assert.doesNotThrow(() => decodificarJson(caso.texto), caso.nome);
    else assert.throws(() => decodificarJson(caso.texto), SyntaxError, caso.nome);
  }
});

test('strings extensas com escapes nao confundem nomes nem delimitadores', () => {
  const texto = String.raw`\\\"{}[],:` .repeat(10000);
  const chave = 'chave' + texto;
  const objeto = { [chave]: texto, filhos: [{ [chave]: 'outro objeto' }] };
  assert.deepEqual(decodificarJson(JSON.stringify(objeto)), objeto);
  const nomeCodificado = JSON.stringify(chave);
  assert.throws(() => decodificarJson(`{${nomeCodificado}:1,${nomeCodificado}:2}`), SyntaxError);
});
