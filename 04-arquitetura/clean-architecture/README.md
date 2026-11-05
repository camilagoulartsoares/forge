# Clean Architecture

## Regra de dependência

No modelo de Robert C. Martin, as dependências de código apontam para as
políticas internas. Regras de negócio não precisam conhecer detalhes da web
ou do banco. O fluxo de execução pode atravessar fronteiras usando contratos.

## Exercício de fronteiras

Escolha um caso de uso de cadastro e classifique suas responsabilidades:

| Responsabilidade | Local sugerido no exercício |
| --- | --- |
| Regra que rejeita um estado inválido | Entidade ou regra de domínio |
| Coordenação do cadastro | Caso de uso |
| Tradução de HTTP para dados de entrada | Adaptador de entrada |
| Tradução de dados persistidos para objetos internos | Adaptador de persistência |

Esses nomes orientam a análise; não exigem quatro diretórios ou uma interface
para cada classe. Para um exercício pequeno, mantenha apenas as separações
necessárias para testar a regra sem infraestrutura.

## Como avaliar

Liste os imports da regra e do caso de uso. Identifique dependências de HTTP,
ORM ou formato de armazenamento que atravessaram a fronteira. Proponha uma
mudança e descreva como verificaria que o comportamento continua o mesmo.

Referência: [The Clean Architecture, Robert C. Martin](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html).
