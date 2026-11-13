// Mesmo intervalo utilizado no contrato JSON e no esquema SQL.
export function identificadorValido(valor) {
  return Number.isSafeInteger(valor) && valor > 0;
}
