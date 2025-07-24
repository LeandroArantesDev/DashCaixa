# Dash Caixa - Sistema de Gestão e Emissão de Fichas

Um sistema web moderno e eficiente para a administração de cantinas e pequenos comércios, com foco no controle de vendas, gestão de estoque e emissão de fichas para retirada de produtos.

---

## 🚀 Visão Geral

O **Dash Caixa** foi desenvolvido para simplificar a rotina de vendas e gestão. Ele permite o registro de vendas realizadas externamente (balcão), dando baixa automática no estoque e gerando uma ficha física para o cliente retirar o produto. O sistema **não processa pagamentos**, servindo como uma ferramenta de controle e organização.

---

## ✨ Funcionalidades Principais

- **Dashboard Gerencial**: Painel com cards informativos sobre faturamento diário, vendas realizadas, produtos mais vendidos e alertas de estoque baixo.
- **Gestão de Produtos (CRUD)**: Cadastro, edição e exclusão de produtos com nome, preço, categoria e estoque.
- **Gestão de Categorias (CRUD)**: Organização dos produtos em categorias.
- **Gestão de Usuários (CRUD)**: Criação de usuários e atribuição de permissões.
- **Registro de Vendas e Emissão de Fichas**: Tela otimizada para registrar vendas e imprimir fichas únicas para o cliente.
- **Controle de Estoque**: Baixa automática de estoque a cada venda.

---

## 👤 Papéis de Usuário

- **Administrador**: Acesso total ao sistema, incluindo dashboard, cadastros, relatórios e histórico.
- **Caixa**: Acesso limitado à tela de registro de vendas e impressão de fichas.

---

## 🛠️ Tecnologias Utilizadas

- **Back-end**: PHP 8+ (puro)
- **Front-end**: HTML5, Tailwind CSS
- **Build Tool**: Vite.js
- **Banco de Dados**: MySQL / MariaDB

---

## ⚙️ Instalação e Configuração Local

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/dash-caixa.git
cd dash-caixa
```

### 2. Instale as dependências do Node.js

```bash
npm install
```

### 3. Configure o ambiente

- Configure seu servidor web local (XAMPP, WAMP, etc.) para apontar para a raiz do projeto.
- Importe o banco de dados utilizando o arquivo `.sql` disponível.
- Configure as credenciais de acesso ao banco no arquivo de configuração PHP.

---

## 💻 Como Desenvolver

Você precisará rodar dois servidores simultaneamente em dois terminais diferentes:

### Terminal 1: Inicie o Vite

```bash
npm run dev
```

Este comando compila os assets e monitora alterações com HMR (Hot Module Replacement).

### Terminal 2: Inicie o servidor PHP

```bash
php -S localhost:8000
```

Acesse o sistema via navegador em: [http://localhost:8000](http://localhost:8000)

---

## 📦 Banco de Dados

```sql
-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS dash_caixa;
USE dash_caixa;

-- Usuários
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  senha VARCHAR(255) NOT NULL,
  tipo BOOLEAN DEFAULT 0,
  ultimo_acesso DATETIME DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categorias
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Produtos
CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  estoque INT NOT NULL DEFAULT 0,
  categoria_id INT,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Vendas
CREATE TABLE vendas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Itens da Venda
CREATE TABLE itens_venda (
  id INT AUTO_INCREMENT PRIMARY KEY,
  venda_id INT NOT NULL,
  produto_id INT NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Erros
CREATE TABLE erros (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  rota VARCHAR(255) NOT NULL,
  mensagem TEXT NOT NULL,
  codigo INT DEFAULT NULL,
  ip VARCHAR(45) DEFAULT NULL,
  navegador TEXT DEFAULT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- Sessões
CREATE TABLE sessoes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  token VARCHAR(255) NOT NULL,
  ip VARCHAR(45) DEFAULT NULL,
  navegador TEXT DEFAULT NULL,
  iniciado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
  encerrado_em DATETIME DEFAULT NULL,
  ativo BOOLEAN DEFAULT 1,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);
```

---

## 🧪 Dados Iniciais

```sql
-- Usuários
INSERT INTO usuarios (nome, email, senha, tipo) VALUES
('Administrador', 'admin@dash.com', SHA2('admin123', 256), 1),
('Caixa 01', 'caixa1@dash.com', SHA2('caixa123', 256), 0),
('Caixa 02', 'caixa2@dash.com', SHA2('caixa123', 256), 0);

-- Categorias
INSERT INTO categorias (nome) VALUES
('Salgados'), ('Bebidas'), ('Doces');

-- Produtos
INSERT INTO produtos (nome, preco, estoque, categoria_id) VALUES
('Coxinha', 5.00, 100, 1),
('Refrigerante Lata', 4.50, 80, 2),
('Barra de Chocolate', 3.00, 60, 3);

-- Vendas
INSERT INTO vendas (usuario_id, total) VALUES
(2, 9.50), (2, 8.00), (3, 5.00);

-- Itens da Venda
INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario) VALUES
(1, 1, 1, 5.00),
(1, 2, 1, 4.50),
(2, 3, 2, 3.00),
(2, 1, 1, 5.00),
(3, 1, 1, 5.00);

-- Erros
INSERT INTO erros (usuario_id, rota, mensagem, codigo, ip, navegador) VALUES
(1, '/vendas/finalizar', 'Erro ao finalizar venda: estoque insuficiente', 500, '192.168.0.10', 'Mozilla/5.0'),
(2, '/login', 'Senha incorreta', 401, '192.168.0.11', 'Chrome/114.0'),
(NULL, '/produtos', 'Acesso negado: não autenticado', 403, '192.168.0.20', 'Edge/120.0');

-- Sessões
INSERT INTO sessoes (usuario_id, token, ip, navegador) VALUES
(1, 'sess_admin_abc123', '192.168.0.10', 'Mozilla/5.0'),
(2, 'sess_caixa1_xyz456', '192.168.0.11', 'Chrome/114.0'),
(3, 'sess_caixa2_qwe789', '192.168.0.12', 'Safari/13.1');
```

---

## 👨‍💻 Desenvolvido por

Ruan, Leandro e Walysson
