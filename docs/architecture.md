# Arquitetura e Estrutura do Sistema ActShare

Este documento descreve a arquitetura geral da plataforma ActShare EAD, detalhando a estrutura do projeto, o fluxo de execução das requisições e a organização dos arquivos.

## Visão Geral
O ActShare EAD é construído sobre uma arquitetura leve em PHP Vanilla com compatibilidade para XAMPP (Apache e MySQL/MariaDB) e servidores compartilhados. Ele adota um padrão inspirado no MVC (Model-View-Controller) simplificado:

1. **Roteador Principal (`index.php`)**: Gerencia o roteamento de páginas amigáveis da interface.
2. **Roteador da API (`api/router.php`)**: Gerencia as requisições REST de backend, expõe endpoints em formato JSON e valida dados.
3. **Visões (`views/`)**: Arquivos de interface do usuário em PHP misturado com HTML/TailwindCSS, injetando cabeçalho, rodapé e lógica JS para consumo de APIs.
4. **Negócio/APIs (`api/`)**: Endpoints isolados agrupados por domínio que processam a lógica e consultam o banco de dados.
5. **Configurações e Conexão (`config.php`, `includes/`)**: Centraliza credenciais, sessões, tratamento de CORS e helpers de banco de dados.

---

## Estrutura de Diretórios

```
raiz/
├── api/                     # APIs e rotas de backend do sistema
│   ├── admin/               # Endpoints administrativos (cupons, certificados, perguntas)
│   ├── aluno/               # Endpoints do aluno (aulas, progresso, quizzes, perfil)
│   ├── auth/                # Endpoints de autenticação (login, cadastro, me, logout)
│   ├── master/              # Endpoints corporativos / Gestão B2B (alunos, gestores, relatórios)
│   ├── router.php           # Roteador central da API
│   └── index.php            # Entrada da pasta api (trata redirecionamentos)
├── assets/                  # Arquivos estáticos front-end
│   ├── css/                 # Estilos (app.css, index.css)
│   ├── img/                 # Imagens (logos, fundos de certificado)
│   └── js/                  # Scripts JS puros (auth.js, api.js, player.js, painel.js)
├── docs/                    # Documentação técnica do sistema (esta pasta)
├── includes/                # Helpers globais (db.php, auth.php, JWT, CORS, response)
├── migrations/              # Scripts de migração SQL
├── views/                   # Telas e interfaces da aplicação
│   ├── admin/               # Painel administrativo (cursos, cupons, perguntas, relatórios)
│   ├── gestor/              # Painel corporativo do Gestor (B2B dashboard, curso-detalhe)
│   ├── layout/              # Elementos compartilhados (header.php, footer.php, admin-header.php)
│   └── *.php                # Interfaces gerais (home, login, painel, player, certificado)
├── config.php               # Arquivo de configurações globais e constantes
├── index.php                # Roteador e entrada principal de páginas
└── .htaccess                # Configurações do Apache para URL amigável (mod_rewrite)
```

---

## Fluxo de Roteamento

### 1. Roteamento de Páginas (Frontend)
Todas as requisições de páginas passam pelo arquivo `index.php` na raiz, amparado pelas diretivas de reescrita do `.htaccess`.
- O roteador mapeia a URL (ex: `/gestor`) para uma rota estática ou dinâmica e inclui o arquivo PHP correspondente dentro de `views/`.
- Caso a rota não seja localizada, devolve uma resposta HTTP 404 personalizada utilizando a estrutura visual padrão da aplicação.

### 2. Roteamento da API (Backend)
Todas as chamadas Ajax para `/api/...` são redirecionadas para `api/router.php`.
- O roteador de API faz a correspondência exata do método HTTP (GET, POST, PUT, DELETE) e do padrão de segmentos (ex: `master/cursos/:id/participantes`), extrai parâmetros dinâmicos e executa o arquivo de controle correspondente.
- A resposta é consistentemente retornada em formato JSON por meio da biblioteca central de respostas (`includes/response.php`).

---

## Tecnologias e Estilização
- **Backend**: PHP 8.x nativo (sem dependências pesadas, garantindo alta velocidade de carregamento).
- **Banco de Dados**: PDO MySQL/MariaDB (consultas parametrizadas e transações robustas).
- **Front-end**: HTML5 semântico com TailwindCSS injetado via CDN para flexibilidade e velocidade de desenvolvimento.
- **Javascript**: JS Vanilla estruturado com Promises (`fetch` assíncrono), desacoplado de frameworks pesados para maior compatibilidade.
- **Estilização Customizada**: Centralizada no `assets/css/app.css` para micro-animações, customização de barras de rolagem e refinamentos estéticos premium.
