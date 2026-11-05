# Planejamento de migrations

## Exercício: tornar um campo obrigatório

Considere uma tabela local de tarefas que já contém registros e receberá
uma nova categoria obrigatória. Planeje a evolução antes de alterar o esquema:

1. Inspecione o esquema e os dados fictícios existentes.
2. Acrescente o campo inicialmente opcional, mantendo compatibilidade com o código anterior.
3. Preencha os registros antigos com uma regra de negócio explícita e verificável.
4. Atualize a aplicação para sempre gravar uma categoria válida.
5. Confirme a ausência de valores nulos antes de impor a restrição obrigatória.

Separe as etapas quando a compatibilidade entre versões da aplicação exigir.
Não escolha um valor padrão que esconda informação desconhecida sem registrar essa decisão.

## Verificações locais

- Aplicar em banco vazio e em banco com dados de exemplo.
- Conferir contagens e valores antes e depois da atualização.
- Testar a aplicação com o esquema intermediário e com o esquema final.
- Ler o SQL gerado antes de executar e verificar possíveis bloqueios.
- Registrar como recuperar dados se uma etapa falhar.

Não assuma que uma transação desfaz toda alteração de esquema. No MySQL,
diversos comandos DDL provocam commit implícito. Uma migration inversa também
não recupera automaticamente valores descartados. Use somente banco local
descartável durante a prática e documente o resultado da recuperação testada.

Referência: [MySQL 8.4: Statements That Cause an Implicit Commit](https://dev.mysql.com/doc/refman/8.4/en/implicit-commit.html).
