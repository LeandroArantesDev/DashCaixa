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