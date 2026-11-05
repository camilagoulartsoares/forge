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
