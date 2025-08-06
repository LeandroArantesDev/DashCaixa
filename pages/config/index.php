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
                <button id="btn-editar-info" class="botao-editar" onclick="mostrarEditarInfo()">Editar</button>
                <!-- botão que mostrara o formulario de editar infomações pessoais -->
            </div>
            <!-- formualario que ficara escondido -->
            <form id="form-editar-info" action="../../backend/config/alterar_info.php" method="POST"
                class="p-6 space-y-4 hidden">
                <!-- inputs escondigos -->
                <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">
                <div>
                    <label for="nome">Nome</label>
                    <div class="relative">
                        <i class="bi bi-person icons text-2xl"></i>
                        <input type="text" name="nome" id="nome" value="<?= htmlspecialchars($row['nome']) ?>"
                            placeholder="Seu nome" required class="interface-input-pessoais">
                    </div>
                </div>
                <div>
                    <label for="email">Email</label>
                    <div class="relative">
                        <i class="bi bi-envelope icons text-2xl"></i>
                        <input type="email" name="email" id="email" value="<?= htmlspecialchars($row['email']) ?>"
                            placeholder="Seu email" required class="interface-input-pessoais">
                    </div>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button"
                        class="btn-cancela"
                        onclick="esconderEditarInfo()">
                        Cancelar
                    </button>
                    <button class="btn-envia" type="submit">
                        <i class="bi bi-floppy"></i>
                        <span>
                            Salvar
                        </span>
                    </button>
                </div>
            </form>
            <div id="div-info" class="p-6 space-y-4">
                <div class="text-mb text-gray-700 mb-1">
                    <label for="nome">Nome Completo</label>
                    <p class="text-subtitulos-config font-semibold"><?= htmlspecialchars($row['nome']) ?></p>
                </div>
                <div class="text-mb text-gray-700">
                    <label for="email">Email</label>
                    <p class="text-subtitulos-config font-semibold"><?= htmlspecialchars($row['email']) ?></p>
                </div>
                <div class="text-m text-gray-700 mt-2">
                    <label for="tipo">Tipo de Usuario</label>
                    <div class="flex items-center space-x-2 mt-2">
                        <?php if ($row['tipo'] == 1): ?> <!-- verificando se o usuario é adm -->
                            <i class="bi bi-shield px-2 py-1 rounded-lg bg-purple-100 text-purple-800"></i>
                            <p class="tipo-usuario">Administrador</p>
                        <?php elseif ($row['tipo'] == 0): ?> <!-- caso for caixa, a cor e o icone mudam -->
                            <i class="bi bi-person px-2 py-1 rounded-lg bg-blue-100 text-blue-800"></i>
                            <p class="tipo-usuario bg-blue-100 text-blue-800">Caixa</p>
                        <?php elseif ($row['tipo'] == 2): ?>
                            <i class="bi bi-person px-2 py-1 rounded-lg bg-gray-700 text-white shadow-sm"></i>
                            <p class="tipo-usuario bg-gray-700 text-white shadow-sm border border-white">Fundador</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="menu-seguranca">
            <div class="interface-titulo-seguranca">
                <h2 class="text-lg font-bold">Segurança</h2>
            </div>
            <!-- formulario que ficara escondido -->
            <form action="../../backend/config/alterar_senha.php" method="POST" id="form-alterar-senha"
                class="p-6 space-y-4 hidden">
                <!-- inputs escondigos -->
                <input type="hidden" name="csrf" id="csrf" value="<?= gerarCSRF() ?>">

                <div class="relative">
                    <label for="senha-atual">Senha Atual</label>
                    <div class="relative">
                        <i class="bi bi-lock icons"></i>
                        <input type="password" name="senha-atual" id="senha-atual" placeholder="Senha Atual"
                            class="interface-input-seguranca">
                        <button type="button" class="button-eye">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="nova-senha">Nova Senha</label>
                    <div class="relative">
                        <i class="bi bi-lock icons"></i>
                        <input type="password" name="nova-senha" id="nova-senha" placeholder="Nova Senha"
                            class="interface-input-seguranca">
                        <button type="button" class="button-eye">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div>
                    <label for="confirmar-senha">Confirmar Nova Senha</label>
                    <div class="relative">
                        <i class="bi bi-lock icons"></i>
                        <input type="password" name="confirmar-senha" id="confirmar-senha"
                            placeholder="Confirmar Nova Senha" class="interface-input-seguranca">
                        <button type="button" class="button-eye">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="flex gap-2 w-full">
                    <button type="button"
                        class="btn-cancela"
                        onclick="esconderFormSenha()">
                        Cancelar
                    </button>
                    <button class="btn-envia" type="submit">
                        Enviar
                    </button>
                </div>
            </form>
            <div id="input-senha" class="p-6 space-y-4">
                <label for="">Senha</label>
                <p class="text-gray-700 font-semibold">••••••••</p>
            </div>
            <div class="px-6">
                <button id="btn-alterar-senha" class="botao-seguranca" onclick="mostrarFormSenha()">
                    <i class="bi bi-lock"></i>
                    Alterar Senha <!-- Botão para mostrar o formulario de alteração de senha -->
                </button>
            </div>
        </div>
    </div>

    <div class="interface-atividadesRecentes">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-subtitulos-config">Atividade Recente</h2>
        </div>
        <div class="p-6 space-y-3">
            <div class="interface-conteudo-atividades">
                <span class="text-titulos-config">Ultimo Login</span>
                <span class="text-subtitulos-config font-medium">
                    Hoje às
                    <?php
                    // puxando o ultimo acesso do usuario e formatando apenas o horario
                    $data = $row['ultimo_acesso'];
                    $horario = date('H:i', strtotime($data));
                    echo $horario;
                    ?>
                </span>
            </div>
            <!-- mostrando as vendas caso o usuario não for fundador -->
            <?php if ($_SESSION['tipo'] != 2): ?>
                <div class="interface-conteudo-atividades">
                    <span class="text-titulos-config">Vendas realizadas hoje</span>
                    <span class="text-principal font-medium">
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
                            echo $fichas_hoje . " Vendas";
                        }
                        ?>
                    </span>
                </div>
            <?php endif; ?>
            <div class="interface-conteudo-atividades">
                <span class="text-titulos-config">Conta criada em</span>
                <span class="text-subtitulos-config font-medium"><?= htmlspecialchars(date('d/m/Y', strtotime($row['criado_em']))) ?></span>
            </div>
        </div>
    </div>

    <script src="../../assets/js/config.js"></script>

    <?php include("../../includes/fim.php") ?>