# Filtro persistido na rota

O [adaptador de query](filtro-da-rota.mjs) transforma parâmetros externos em um
dos estados aceitos pelo [filtro de tarefas](../fundamentos/README.md).
Execute `node --test 03-vue/vue-router/filtro-da-rota.test.mjs` na raiz.

`lerFiltroDaRota(route.query)` aceita valores singulares conhecidos; ausências,
valores desconhecidos e parâmetros repetidos representados por arrays voltam
para `todas`. A função não confia em coerção de tipos.

Ao navegar, use `atualizarFiltroDaRota(route.query, estado)` como nova query,
preservando parâmetros de outras funcionalidades. O estado padrão é omitido
para manter uma URL canônica. Os helpers são JavaScript puro e não instalam Router.
No exercício Vue, derive a lista da rota e verifique recarregar, voltar e avançar
no navegador para evitar duas fontes de verdade divergentes.
