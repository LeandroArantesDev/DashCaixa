<?php
$titulo = "Configurações";
include("../../includes/inicio.php");

// puxando informações do usuario
$stmt = $conexao->prepare("SELECT nome, email, tipo, ultimo_acesso, criado_em FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $_SESSION["id"]);
$stmt->execute();
$resultado = $stmt->get_result();
$row = $resultado->fetch_assoc();
$stmt->close();
?>
    <div class="conteudo">
        <div class="titulo">
            <div class="txt-titulo">
                <h1>Configurações</h1>
                <p>Veja e edite suas informações</p>
            </div>
        </div>
        <div class="interface-config">
            <div class="menu-pessoais">
                <div class="interface-titulo-pessoais">
                    <h2 class="text-lg font-bold">Informações Pessoais</h2>
                    <button class="botao-editar">Editar</button>
                </div>
                <div class="p-6 space-y-4">
                    <div class="text-mb text-gray-700 mb-1">
                        <label for="">Nome Completo</label>
                        <p class="text-gray-900 font-semibold"><?= htmlspecialchars($row['nome']) ?></p>
                    </div>
                    <div class="text-mb text-gray-700">
                        <label for="">Email</label>
                        <p class="text-gray-900 font-semibold"><?= htmlspecialchars($row['email']) ?></p>
                    </div>
                    <div class="text-m text-gray-700 mt-2">
                        <label for="">Tipo de Usuario</label>
                        <div class="flex items-center space-x-2 mt-2">
                            <?php if ($row['tipo'] == 1): ?> <!-- verificando se o usuario é adm -->
                                <i class="bi bi-shield px-2 py-1 rounded-lg bg-purple-100 text-purple-800"></i>
                                <p class="tipo-usuario">Administrador</p>
                            <?php else: ?> <!-- se não for a cor e o icone muda -->
                                <i class="bi bi-person px-2 py-1 rounded-lg bg-blue-100 text-blue-800"></i>
                                <p class="tipo-usuario bg-blue-100 text-blue-800">Caixa</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="menu-seguranca">
                <div class="interface-titulo-seguranca">
                    <h2 class="text-lg font-bold">Segurança</h2>
                </div>
                <div class="p-6 space-y-4">
                    <label for="">Senha</label>
                    <p class="text-gray-700 font-semibold">••••••••</p> <!-- Puxar do banco de dados -->
                </div>
                <div class="px-6">
                    <button class="botao-seguranca">
                        <i class="bi bi-lock"></i>
                        Alterar Senha <!-- Botão para abrir modal de alteração de senha -->
                    </button>
                </div>
                <!-- Modal Ainda não implementado -->
            </div>
        </div>

        <div class="interface-atividadesRecentes">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Atividade Recente</h2>
            </div>
            <div class="p-6 space-y-3">
                <div class="interface-conteudo-atividades">
                    <span class="text-gray-600">Ultimo Login</span>
                    <span class="text-gray-900 font-medium">
                        Hoje às
                        <?php
                        // puxando o ultimo acesso do usuario e formatando apenas o horario
                        $data = $row['ultimo_acesso'];
                        $horario = date('H:i', strtotime($data));
                        echo $horario;
                        ?>
                    </span>
                </div>
                <div class="interface-conteudo-atividades">
                    <span class="text-gray-600">Vendas realizadas hoje</span>
                    <span class="text-blue-600 font-medium">
                        <?php
                        // buscando as vendas realizadas hoje
                        $stmt = $conexao->prepare("SELECT COUNT(*) FROM vendas WHERE usuario_id = ? AND DATE(data_venda) = CURDATE()");
                        $stmt->bind_param("i", $_SESSION["id"]);
                        $stmt->execute();
                        $stmt->bind_result($fichas_hoje);
                        $stmt->fetch();
                        $stmt->close();
                        if ($fichas_hoje == 0) {
                            echo "Nenhuma venda hoje!";
                        } else {
                            echo $fichas_hoje." Vendas";
                        }
                        ?>
                    </span>
                </div>
                <div class="interface-conteudo-atividades">
                    <span class="text-gray-600">Conta criada em</span>
                    <span class="text-gray-900 font-medium"><?=htmlspecialchars(date('d/m/Y', strtotime($row['criado_em'])))?></span>
                </div>
            </div>
        </div>
    </div>
    </div>
<?php include("../../includes/fim.php") ?>