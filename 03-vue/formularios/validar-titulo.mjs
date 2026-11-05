export function validarTitulo(valor) {
  if (typeof valor !== 'string') {
    return { valido: false, erro: 'Informe um titulo textual.' };
  }

  const titulo = valor.trim();
  if (titulo.length === 0) {
    return { valido: false, erro: 'Titulo obrigatorio.' };
  }
  if (titulo.length > 120) {
    return { valido: false, erro: 'Titulo deve ter ate 120 unidades UTF-16.' };
  }

  return { valido: true, titulo };
}
