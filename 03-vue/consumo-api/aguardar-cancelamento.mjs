export async function aguardarComCancelamento(promessa, signal) {
  if (!signal) return promessa;
  let cancelar;
  const cancelamento = new Promise((_resolver, rejeitar) => {
    cancelar = () => rejeitar(signal.reason);
    signal.addEventListener('abort', cancelar, { once: true });
    if (signal.aborted) cancelar();
  });
  try {
    return await Promise.race([cancelamento, promessa]);
  } finally {
    signal.removeEventListener('abort', cancelar);
  }
}
