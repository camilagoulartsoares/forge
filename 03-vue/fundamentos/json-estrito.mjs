// Gramatica pelo parser nativo; verificacoes adicionais para interoperabilidade PHP/JS.
export function decodificarJson(texto) {
  if (typeof texto !== 'string') throw new TypeError('Esperado texto JSON.');
  const resultado = JSON.parse(texto);
  const pilha = [];
  for (let posicao = 0; posicao < texto.length; posicao += 1) {
    const caractere = texto[posicao];
    if (caractere === '{' || caractere === '[') {
      pilha.push(caractere === '{' ? new Set() : null);
      if (pilha.length >= 512) throw new SyntaxError('JSON excede o limite de aninhamento.');
    } else if (caractere === '}' || caractere === ']') {
      pilha.pop();
    } else if (caractere === '"') {
      const inicio = posicao++;
      while (texto[posicao] !== '"') posicao += texto[posicao] === '\\' ? 2 : 1;
      let proximo = posicao + 1;
      while (proximo < texto.length && ' \t\r\n'.includes(texto[proximo])) proximo += 1;
      if (texto[proximo] === ':') {
        const chave = JSON.parse(texto.slice(inicio, posicao + 1));
        if (chave.startsWith('\0')) throw new SyntaxError('Chave JSON iniciada por NUL.');
        const chaves = pilha[pilha.length - 1];
        if (chaves.has(chave)) throw new SyntaxError('Chave JSON duplicada.');
        chaves.add(chave);
      }
    }
  }
  const pendentes = [resultado];
  while (pendentes.length) {
    const valor = pendentes.pop();
    if (typeof valor === 'number' && !Number.isFinite(valor)) throw new SyntaxError('Numero fora do intervalo finito.');
    if (typeof valor === 'string') {
      for (const caractere of valor) {
        const codigo = caractere.codePointAt(0);
        if (codigo >= 0xD800 && codigo <= 0xDFFF) throw new SyntaxError('Surrogate Unicode isolado.');
      }
    } else if (valor !== null && typeof valor === 'object') {
      for (const [chave, filho] of Object.entries(valor)) pendentes.push(chave, filho);
    }
  }
  return resultado;
}
