# Ambiente

## Objetivo



Documentar a preparação local e as ferramentas necessárias, sem incluir credenciais.

## Conceitos abordados

- Ferramentas de desenvolvimento
- Configuração local

## Pré-requisitos

Para ler e editar os estudos, basta Git e um editor com suporte a UTF-8.
Instale os runtimes apenas conforme o tema praticado: PHP para backend,
MySQL para SQL e Node.js com npm para frontend. Composer será necessário
quando um exemplo PHP declarar dependências.

## Como executar

Na raiz do repositório, confira as ferramentas usadas no estudo escolhido:

```sh
git --version
php --version
composer --version
mysql --version
node --version
npm --version
```

Esses comandos apenas consultam versões; `mysql --version` não confirma que
o servidor está em execução. Se um comando não for encontrado, confira a
instalação e o `PATH`, depois abra um novo terminal.

Siga o README do exemplo para versões e extensões exigidas. Atualmente não
há aplicação executável, `composer.json` ou `package.json` na raiz; portanto,
não há instalação de dependências nem comando global de inicialização.

## Exemplos

Para verificar os arquivos de configuração carregados e as extensões do PHP:

```sh
php --ini
php -m
```

Use essa saída para diagnosticar diferenças entre ambientes. Não publique
caminhos internos ou outros dados privados ao compartilhar um diagnóstico.

## Exercícios

Registre exercícios planejados.

## Aprendizados

Registre aprendizados.

## Dúvidas

Registre dúvidas a revisar.

## Referências

Adicione referências públicas.
