Dash Caixa - Sistema de Gestão e Emissão de Fichas
Um sistema web moderno e eficiente para a administração de cantinas e pequenos comércios, com foco no controle de vendas, gestão de estoque e emissão de fichas para retirada de produtos.

🚀 Visão Geral
O Dash Caixa foi desenvolvido para simplificar a rotina de vendas e gestão. Ele permite o registro de vendas realizadas externamente (balcão), dando baixa automática no estoque e gerando uma ficha física para o cliente retirar o produto. O sistema não processa pagamentos, servindo como uma ferramenta de controle e organização.

✨ Funcionalidades Principais
Dashboard Gerencial: Painel de controle para o administrador com cards informativos sobre faturamento do dia, vendas realizadas, produtos mais vendidos e alertas de estoque baixo.

Gestão de Produtos (CRUD): Interface completa para criar, editar e excluir produtos, incluindo informações como nome, preço, categoria e quantidade em estoque.

Gestão de Categorias (CRUD): Organize seus produtos em categorias para facilitar a busca e os relatórios.

Gestão de Usuários (CRUD): Crie e gerencie os usuários do sistema, atribuindo permissões específicas.

Registro de Vendas e Emissão de Fichas: Uma tela otimizada para o operador de caixa registrar os itens de uma venda, finalizar o pedido e imprimir uma ficha única para o cliente.

Controle de Estoque: A baixa de estoque é feita automaticamente a cada venda finalizada.

👤 Papéis de Usuário
O sistema conta com dois níveis de acesso para garantir a segurança e a organização do fluxo de trabalho:

Administrador: Possui acesso total a todas as funcionalidades do sistema, incluindo o dashboard gerencial, todos os CRUDs e o histórico completo de vendas.

Caixa: Possui acesso limitado, focado na tela de "Registro de Venda". Pode registrar vendas e imprimir as fichas correspondentes. Não tem acesso a configurações, relatórios ou gestão de usuários.

🛠️ Tecnologias Utilizadas
Este projeto foi construído com uma stack moderna, visando performance e uma ótima experiência de desenvolvimento.

Back-end: PHP 8+ (Puro)

Front-end: HTML5, Tailwind CSS

Build Tool: Vite.js

Banco de Dados: MySQL / MariaDB

⚙️ Instalação e Configuração Local
Siga os passos abaixo para rodar o projeto em seu ambiente de desenvolvimento.

Clone o repositório:

Bash

git clone https://github.com/seu-usuario/dash-caixa.git
cd dash-caixa
Instale as dependências do Node.js:

Bash

npm install
Configure seu servidor web local (XAMPP, WAMP, etc.) para apontar para a raiz do projeto.

Importe o banco de dados: Utilize o arquivo .sql (se houver) para criar a estrutura de tabelas necessárias.

Configure a conexão com o banco de dados em seu arquivo de configuração PHP.

💻 Como Desenvolver
Para trabalhar no projeto, você precisará rodar dois servidores simultaneamente em dois terminais diferentes:

Terminal 1: Inicie o Vite
Este comando irá compilar os assets (CSS, JS) e ficará "assistindo" por alterações com HMR (Hot Module Replacement).

Bash

npm run dev
Terminal 2: Inicie o servidor PHP (caso não esteja usando XAMPP/WAMP)

Bash

php -S localhost:8000
Acesse o projeto através da URL do seu servidor PHP (ex: http://localhost/dash-caixa ou http://localhost:8000).

Desenvolvido por Ruan, Leandro e Walysson.


Banco de dados


-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS dash_caixa;
USE dash_caixa;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo BOOLEAN DEFAULT 0,                  -- 0 = Caixa, 1 = Administrador
    ultimo_acesso DATETIME DEFAULT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    estoque INT NOT NULL DEFAULT 0,
    categoria_id INT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE SET NULL
);

-- Tabela de vendas
CREATE TABLE vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    data_venda TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabela de itens da venda
CREATE TABLE itens_venda (
    id INT AUTO_INCREMENT PRIMARY KEY,
    venda_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- Tabela de erros
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

-- Tabela de sessões
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


-- Assumindo que o banco já foi criado e estamos usando:
USE dash_caixa;

-- Inserindo usuários
INSERT INTO usuarios (nome, email, senha, tipo)
VALUES 
('Administrador', 'admin@dash.com', SHA2('admin123', 256), 1),
('Caixa 01', 'caixa1@dash.com', SHA2('caixa123', 256), 0),
('Caixa 02', 'caixa2@dash.com', SHA2('caixa123', 256), 0);

-- Inserindo categorias
INSERT INTO categorias (nome)
VALUES 
('Salgados'),
('Bebidas'),
('Doces');

-- Inserindo produtos
INSERT INTO produtos (nome, preco, estoque, categoria_id)
VALUES 
('Coxinha', 5.00, 100, 1),
('Refrigerante Lata', 4.50, 80, 2),
('Barra de Chocolate', 3.00, 60, 3);

-- Inserindo vendas
INSERT INTO vendas (usuario_id, total)
VALUES 
(2, 9.50),
(2, 8.00),
(3, 5.00);

-- Inserindo itens da venda
INSERT INTO itens_venda (venda_id, produto_id, quantidade, preco_unitario)
VALUES 
(1, 1, 1, 5.00),  -- Coxinha
(1, 2, 1, 4.50),  -- Refrigerante
(2, 3, 2, 3.00),  -- Chocolate
(2, 1, 1, 5.00),  -- Coxinha
(3, 1, 1, 5.00);  -- Coxinha

-- Inserindo erros
INSERT INTO erros (usuario_id, rota, mensagem, codigo, ip, navegador)
VALUES 
(1, '/vendas/finalizar', 'Erro ao finalizar venda: estoque insuficiente', 500, '192.168.0.10', 'Mozilla/5.0'),
(2, '/login', 'Senha incorreta', 401, '192.168.0.11', 'Chrome/114.0'),
(NULL, '/produtos', 'Acesso negado: não autenticado', 403, '192.168.0.20', 'Edge/120.0');

-- Inserindo sessões
INSERT INTO sessoes (usuario_id, token, ip, navegador)
VALUES 
(1, 'sess_admin_abc123', '192.168.0.10', 'Mozilla/5.0'),
(2, 'sess_caixa1_xyz456', '192.168.0.11', 'Chrome/114.0'),
(3, 'sess_caixa2_qwe789', '192.168.0.12', 'Safari/13.1');
