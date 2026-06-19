# GOMOS — Rede Social de Academia 🏋️💪

> **Tagline:** *"Treina. Posta. Supera."*
> **Público-alvo:** Frequentadores de academia de qualquer nível.

O **GOMOS** é uma plataforma social completa desenvolvida em PHP Puro (padrão MVC) e MySQL para praticantes de musculação. Inspirado no compartilhamento de fichas de treino e na gamificação esportiva, os usuários podem montar fichas de treino dinâmicas, registrar check-ins em academias locais, curtir e comentar os treinos de amigos, comparar volumes de carga lado a lado e subir no ranking regional e nacional.

---

## 🌟 Funcionalidades e Recursos Principais

O GOMOS não é apenas um gerenciador de rotinas, mas um ecossistema integrado com foco em gamificação e compartilhamento social:

### 1. 📋 Minhas Fichas (Templates de Treino)
* **Rotinas Privadas por Padrão**: As rotinas criadas no construtor de treinos servem como seus templates de treino e ficam guardadas na aba "Minhas Fichas", sem poluir o feed principal.
* **Cópia de Fichas**: Gostou do treino de algum amigo no feed, na busca ou no perfil dele? Com apenas um clique você pode copiar a ficha completa para a sua biblioteca (salva com o sufixo `(Copiado)`), facilitando o compartilhamento de rotinas.

### 2. ⚡ Workout Tracker (Rastreador de Treinos Ativos)
* **Execução em Tempo Real**: Inicie qualquer ficha salva para abrir a interface do rastreador de treino.
* **Cronômetro e Timer de Descanso**: Acompanhe o tempo de treino decorrido. Ao marcar uma série concluída, um temporizador regressivo de descanso é acionado automaticamente e um **bip eletrônico** (Web Audio API) soa para avisar que o descanso acabou.
* **Inputs Flexíveis**: Registre peso e repetições reais feitas. Suporta entrada de decimais com vírgula local (ex: `12,5 kg`).
* **Modal Finalizar (Composer)**: Ao clicar em "Finalizar", o modal estilo Twitter calcula a duração exata do treino, a quantidade de séries concluídas e o **Volume Total de Carga (Ton)**. Adicione fotos e comentários para postar no feed.

### 3. 👥 Feed de Treinos & Interação Social
* **Feed estilo Twitter**: Apenas treinos **efetivamente realizados** aparecem no feed social de treinos dos amigos, mantendo a timeline ativa com treinos reais.
* **Mídia e Preview**: As publicações exibem fotos reais do treino, notas do atleta e um preview completo da tabela de exercícios com as séries, repetições e cargas reais executadas.
* **Interações Gamificadas**: Dê curtidas rápidas (com ícone de coração preenchido) e comente nas publicações para interagir com a comunidade.

### 4. 👤 Perfil do Atleta por Abas (Tabs)
* **Divisão Organizada**: Perfis divididos de forma elegante em duas abas:
  * **Histórico de Treinos**: Onde ficam todas as publicações de treinos efetivamente realizados por aquele atleta, com fotos e estatísticas.
  * **Fichas de Treino**: Onde o atleta lista seus templates públicos/privados, permitindo que outros atletas vejam e copiem as fichas.

### 5. 🏆 Sistema de Gamificação & Rankings
Suba de nível e ganhe destaque na comunidade acumulando pontos de evolução no ranking oficial:
* **Realizar um Treino**: **+10 pontos** (ao finalizar o treino no tracker).
* **Ficha Copiada por Outro Atleta**: **+10 pontos** (valoriza criadores de treinos úteis).
* **Curtida Recebida**: **+3 pontos** (para o dono do post).
* **Deixar Comentário**: **+1 ponto** (para o comentarista, incentivando interações).
* **Filtros por Abas**: Visualize a classificação dos melhores atletas em 3 esferas: Nacional (Brasil), Entre Amigos (sua rede de amigos) ou Regional (da mesma Cidade/Estado).

### 6. 📱 Interface Responsiva & Barra Lateral Colapsável
* **Mobile-First**: Projetado com foco em aparelhos móveis para ser usado diretamente no salão da academia.
* **Sidebar Inteligente**: No computador, a barra lateral de navegação pode ser recolhida/expandida para aumentar o espaço de visualização das fichas. O estado é persistido no navegador via `localStorage`.
* **Design Livre de Sobreposições**: As áreas de conteúdo e cabeçalhos possuem espaçamentos dinâmicos que recuam ao colapsar a sidebar ou abrir o menu hambúrguer no celular, garantindo visibilidade limpa das informações.
* **Busca Global**: Um campo de pesquisa no menu lateral permite buscar treinos e usuários dinamicamente com autocomplete/dropdown de resultados.

---

## 🎨 Conceito e Identidade Visual

A interface do GOMOS foi projetada para ter um impacto visual premium e moderno, adotando um tema escuro com detalhes vibrantes:

*   **Fundo Principal:** `#0D0D0D` (preto profundo - foco)
*   **Fundo dos Cards:** `#1A1A1A` (contraste elegante)
*   **Acento Primário:** `#FF6B00` (laranja intenso - energia física)
*   **Acento Secundário:** `#A3E635` (verde-limão - conquista de metas)
*   **Tipografia:** `Bebas Neue` (títulos imponentes) e `Inter` (leitura limpa e fluida do corpo)

---

## 📂 Estrutura do Projeto (MVC)

O projeto está organizado conforme o padrão profissional de separação de responsabilidades (Model-View-Controller):

```
/gomos/
├── /public/
│   ├── index.php                  ← Roteador principal
│   ├── .htaccess                  ← Reescrita de URLs amigáveis
│   ├── /assets/
│   │   ├── /css/
│   │   │   ├── style.css          ← Estilos globais
│   │   │   ├── landing.css        ← Estilos da landing page
│   │   │   └── dashboard.css      ← Estilos do portal logado
│   │   ├── /js/
│   │   │   ├── main.js            ← Comportamentos gerais + AJAX likes
│   │   │   └── treino.js          ← Builder dinâmico de exercícios
│   │   └── /img/
│   │       ├── logo.png           ← Gráfico oficial GOMOS
│   │       └── (imagens e avatares do sistema)
│
├── /app/
│   ├── /controllers/              ← Regras de controle de fluxo
│   │   ├── AuthController.php
│   │   ├── UsuarioController.php
│   │   ├── TreinoController.php
│   │   ├── RankingController.php
│   │   ├── AcademiaController.php
│   │   ├── FeedController.php
│   │   └── AdminController.php
│   │
│   ├── /models/                   ← Interações e queries PDO com o BD
│   │   ├── UsuarioModel.php
│   │   ├── TreinoModel.php
│   │   ├── ExercicioModel.php
│   │   ├── RankingModel.php
│   │   ├── AcademiaModel.php
│   │   ├── AmizadeModel.php
│   │   └── ComentarioModel.php
│   │
│   ├── /views/                    ← Camada de apresentação (Views)
│   │   ├── /landing/              ← Páginas públicas
│   │   ├── /auth/                 ← Telas de acesso
│   │   ├── /perfil/               ← Visualização e edição de perfis
│   │   ├── /treino/               ← Builder e comparador de volume
│   │   ├── /feed/                 ← Postagens dos amigos
│   │   ├── /ranking/              ← Leaderboard em abas
│   │   ├── /academia/             ← Buscas e check-ins
│   │   ├── /admin/                ← CRUDs administrativos e moderação
│   │   └── /partials/             ← Inclusões de cabeçalho, rodapé e menus
│   │
│   ├── /config/
│   │   └── Database.php           ← Conexão PDO Singleton
│   │
│   └── /helpers/
│       ├── Session.php            ← Controle de sessões e Toast flashes
│       └── Validator.php          ← Sanitização e validações de input
│
├── /database/
│   └── gomos.sql                  ← Tabelas, chaves e sementes (seed)
│
└── README.md
```

---

## 🛠️ Requisitos de Instalação (XAMPP)

1.  **Instalar o XAMPP** (com PHP 8.x e MySQL/MariaDB).
2.  Garantir que a extensão Apache **`mod_rewrite`** esteja ativada no seu arquivo `httpd.conf` para permitir URLs amigáveis.

---

## 🚀 Passo a Passo para Execução

### Passo 1: Mover o Projeto
Copie a pasta inteira do projeto (`TrabalhoFinalWebVasco`) para a pasta de arquivos do seu servidor Apache, normalmente localizada em:
*   **Windows:** `C:\xampp\htdocs\`

### Passo 2: Inicializar os Serviços
Abra o **Painel de Controle do XAMPP** e inicialize os módulos:
*   `Apache`
*   `MySQL`

### Passo 3: Importar o Banco de Dados
1.  Acesse o panel do banco pelo navegador: `http://localhost/phpmyadmin/`
2.  Crie um novo banco de dados chamado **`gomos`** com charset `utf8mb4_unicode_ci`.
3.  Selecione o banco `gomos`, clique na aba **Importar** e escolha o arquivo SQL localizado em `/database/gomos.sql`.
4.  Clique em **Executar** no final da página para rodar o script. As tabelas e dados de semente serão criados.

### Passo 4: Acessar a Aplicação
Abra o seu navegador e acesse a URL (ajustando o nome da pasta conforme configurado no htdocs):
*   `http://localhost/TrabalhoFinalWebVasco/gomos/`

---

## 🔑 Credenciais para Testes

Todos os usuários cadastrados como dados de sementes compartilham a mesma senha padrão: **`123456`**.

### 👤 Usuários Comuns (Musculação/Interação)
*   **Ramon Dino:** `ramon@dino.com` ou `ramondino` (Nível Avançado, pontuação alta)
*   **Renato Cariani:** `renato@cariani.com` ou `rcariani` (Nível Avançado)
*   **Lucas Paton:** `lcspaton@gmail.com` ou `lucaspaton` (Nível Iniciante)
*   **Carol Borba:** `carol@borba.com` ou `carolborba` (Nível Intermediário)

### 🔑 Usuários Administradores (Moderação/Painel)
*   **Super Admin:** `admin@gomos.com` (Acesso completo a CRUDs de academias, moderar treinos e cadastrar novos badges)
*   **Moderador:** `moderador@gomos.com`

---

## 🔒 Segurança e Boas Práticas

*   **Padrão Singleton:** Conexão única centralizada para otimizar requisições do MySQL.
*   **Zero SQL Injection:** Todas as queries são estruturadas utilizando Prepared Statements do PDO.
*   **Prevenção de XSS:** Validações rigorosas de tipo e sanitização com `htmlspecialchars` em todas as saídas no template.
*   **Senhas Seguras:** Criptografia de ponta-a-ponta com hashing nativo `password_hash()` com algoritmo bcrypt.
