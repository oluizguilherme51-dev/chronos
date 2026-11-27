# CHRONOS — Haute Horlogerie

## Sobre o projeto

O Chronos é um e-commerce de relógios de luxo desenvolvido
como projeto acadêmico do 4º bimestre do Ensino Médio
Técnico em Tecnologia da Informação do Senac Nações Unidas.

O objetivo do projeto é desenvolver uma loja virtual
funcional, aplicando os conhecimentos adquiridos durante
o curso, desde a criação da interface até a integração
com o banco de dados.

## Identidade visual

A identidade visual foi inspirada na elegância e na
sofisticação da alta relojoaria, utilizando uma paleta
de cores composta por azul, marfim, areia e bronze.

## Funcionalidades

### Área do cliente

- Visualização do catálogo de relógios.
- Pesquisa de produtos.
- Filtro por coleção.
- Visualização dos detalhes dos produtos.
- Cadastro e autenticação de usuários.
- Sacola de compras.
- Alteração de quantidades e remoção de produtos.
- Validação de estoque.
- Finalização de compras.
- Simulação de pagamentos por PIX, boleto e cartão.

### Área administrativa

- Acesso restrito aos administradores.
- Cadastro de produtos.
- Listagem de produtos.
- Edição de produtos.
- Exclusão de produtos.
- Gerenciamento de estoque.
- Consulta ao histórico de vendas.

## Tecnologias utilizadas

- HTML5
- CSS3
- JavaScript
- Bootstrap
- PHP
- PDO
- MySQL
- XAMPP
- Git
- GitHub

## Segurança

- Senhas protegidas com password_hash.
- Autenticação por sessões PHP.
- Controle de acesso administrativo.
- Consultas preparadas com PDO.
- Proteção de credenciais por meio do arquivo .env.
- Validação de dados no servidor.
- Transações no banco de dados durante as compras.

## Estrutura do projeto

chronos/
├── colecao/
├── compras/
├── conta/
├── database/
│   └── schema.sql
├── gestao/
├── imagens/
├── includes/
├── .env.example
├── .gitignore
├── estilo.css
├── index.php
├── java.js
└── readme.md

## Como executar o projeto

1. Instale o XAMPP.
2. Clone este repositório.
3. Coloque a pasta chronos dentro da pasta htdocs.
4. Inicie o Apache e o MySQL pelo XAMPP.
5. Acesse o phpMyAdmin.
6. Crie um banco de dados chamado chronos.
7. Importe o arquivo database/schema.sql.
8. Crie o arquivo .env com base no .env.example.
9. Configure as credenciais do banco de dados.
10. Acesse http://localhost/chronos/ no navegador.

## Acesso administrativo

Cadastre um usuário pelo próprio site e altere seu
perfil para admin na tabela usuarios pelo phpMyAdmin.
Depois, realize novamente o login.

## Observação

Este projeto possui finalidade exclusivamente acadêmica.
Os pagamentos são simulados e não envolvem transações
financeiras reais.

## Desenvolvedor

Luiz Guilherme de Oliveira
