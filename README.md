# Freddy Gestor

## Descrição

Freddy Gestor é uma aplicação simples de exemplo desenvolvida em PHP puro, utilizando diversas bibliotecas populares do ecossistema PHP. O objetivo é demonstrar como construir uma aplicação web moderna sem depender de frameworks de mercado como Laravel ou Symfony. Este projeto serve como um guia prático para desenvolvedores que desejam entender os fundamentos de uma arquitetura MVC (Model-View-Controller) personalizada, integração com banco de dados, autenticação, validação de formulários e muito mais.

A aplicação simula um sistema básico de gestão de produtos, com funcionalidades de cadastro, listagem, edição e exclusão de produtos, além de um sistema de autenticação de usuários.

## Tecnologias e Bibliotecas Utilizadas

O projeto foi construído utilizando as seguintes bibliotecas e ferramentas populares do PHP:

- **Doctrine DBAL**: Para abstração e interação com o banco de dados MySQL.
- **Monolog**: Para logging estruturado e gerenciamento de logs.
- **PHPMailer**: Para envio de e-mails (usado em funcionalidades como redefinição de senha).
- **Twig**: Para renderização de templates HTML de forma segura e eficiente.
- **FastRoute**: Para roteamento rápido e eficiente das URLs.
- **Respect Validation**: Para validação de dados de formulários.
- **PHP-DI (Dependency Injection)**: Para injeção de dependências e gerenciamento de serviços.
- **Laminas Diactoros e HttpHandlerRunner**: Para manipulação de requisições e respostas HTTP PSR-7.
- **Dotenv**: Para carregamento de variáveis de ambiente.
- **Graham Campbell Result Type**: Para tipos de retorno mais expressivos.

Outras dependências incluem PSR standards para cache, containers, logs, etc., garantindo compatibilidade e boas práticas.

## Estrutura do Projeto

A estrutura de diretórios segue uma arquitetura organizada e modular:

```
/
├── composer.json          # Configuração do Composer e dependências
├── docker-compose.yml      # Configuração do Docker para ambiente de desenvolvimento
├── freddy.sql             # Script SQL para criação do banco de dados
├── start.sh               # Script de inicialização
├── config/                # Arquivos de configuração
│   ├── database.php       # Configuração da conexão com o banco
│   └── routes.php         # Definição das rotas da aplicação
├── public/                # Ponto de entrada público
│   ├── index.php          # Arquivo principal de entrada
│   └── uploads/           # Diretório para uploads de arquivos
├── src/                   # Código fonte da aplicação
│   ├── Controllers/       # Controladores da aplicação (Auth, Produto, etc.)
│   ├── Entities/          # Entidades do domínio (Produto, Usuario)
│   ├── Exceptions/        # Exceções customizadas
│   ├── Middleware/        # Middlewares (ex: autenticação)
│   ├── Models/            # Modelos de dados e lógica de negócio
│   ├── Services/          # Serviços (ex: envio de e-mail)
│   └── Support/           # Classes de suporte (validação, upload, etc.)
├── test/                  # Diretório para testes
├── Views/                 # Templates Twig
│   ├── auth/              # Templates de autenticação
│   ├── errors/            # Templates de erro (404, 500)
│   ├── layout/            # Layout base
│   └── produtos/          # Templates de produtos
├── storage/               # Diretório para dados persistentes
│   ├── cache/             # Cache da aplicação
│   └── logs/              # Logs da aplicação
└── vendor/                # Dependências instaladas pelo Composer
```

## Instalação e Configuração

### Pré-requisitos

- PHP 8.0 ou superior
- Composer
- Docker e Docker Compose (opcional, para ambiente containerizado)
- MySQL ou MariaDB

### Passos de Instalação

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/seu-usuario/freddy-gestor.git
   cd freddy-gestor
   ```

2. **Instale as dependências:**
   ```bash
   composer install
   ```

3. **Configure o ambiente:**
   - Copie o arquivo `.env.example` para `.env` e ajuste as variáveis de ambiente (banco de dados, e-mail, etc.).
   - Execute o script SQL `freddy.sql` no seu banco de dados MySQL para criar as tabelas necessárias.

4. **(Opcional) Usando Docker:**
   - Execute `docker-compose up` para subir o ambiente containerizado.
   - A aplicação estará disponível em `http://localhost:8000`.

5. **Execute a aplicação:**
   - Se não estiver usando Docker, inicie um servidor PHP: `php -S localhost:8000 -t public/`
   - Acesse `http://localhost:8000` no navegador.

## Uso

Após a instalação, você pode:

- Acessar a página de login em `/login`.
- Cadastrar novos usuários em `/cadastro`.
- Gerenciar produtos: listar em `/produtos`, criar em `/produtos/criar`, editar em `/produtos/editar/{id}`.

## Contribuição

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues ou pull requests no repositório.

## Licença

Este projeto é distribuído sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.