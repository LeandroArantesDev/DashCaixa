<?php
// impedindo usuarios não fundadores de acessar nossas paginas
include("../../includes/valida_fundador.php");

$titulo = "Dashboard";
include("../../backend/funcoes/dashboard-fundadores.php");
include("../../includes/inicio.php");
?>
    <div class="conteudo">
        <div class="titulo">
            <div class="txt-titulo">
                <h1>Roadmap</h1>
                <p>Editar e atualizar o roadmap</p>
            </div>
        </div>
    </div>
<?php include("../../includes/fim.php") ?>