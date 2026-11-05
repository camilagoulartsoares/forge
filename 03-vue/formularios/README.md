# Validação de formulário

A [função de validação](validar-titulo.mjs) é JavaScript puro, reutilizável
em um formulário Vue. Não requer instalar Vue. Na raiz, com Node.js 18 ou superior:

```sh
node --test 03-vue/formularios/validar-titulo.test.mjs
```

O contrato aceita texto, remove espaços das bordas e exige de 1 a 120 unidades
UTF-16, conforme `String.length`. Esse limite não conta caracteres visuais.
No envio, use o título normalizado somente quando `valido` for verdadeiro;
caso contrário, apresente `erro` junto ao campo e preserve o texto digitado.
O backend também deve validar a entrada, pois a validação do navegador pode ser ignorada.

## Apresentação acessível dos erros

Ao integrar o validador a um componente, associe um `label` ao campo por `for`
e `id`. Após uma tentativa de envio inválida, use `aria-invalid="true"` e
associe a mensagem com `aria-describedby`. Exemplo estático do estado inválido:

```html
<label for="titulo">Título</label>
<input id="titulo" name="titulo" aria-invalid="true" aria-describedby="titulo-erro">
<p id="titulo-erro">Informe um título para a tarefa.</p>
```

No estado válido, retire a mensagem e atualize os atributos correspondentes.
Não dependa apenas de cor para indicar o problema. Se houver vários campos,
apresente um resumo dos erros e um caminho claro até cada campo afetado.
Verifique o formulário por teclado, preservação dos valores e leitura da
mensagem com tecnologia assistiva; testes do validador não cobrem essa interação.

Referência: [W3C WAI: User Notifications](https://www.w3.org/WAI/tutorials/forms/notifications/).
