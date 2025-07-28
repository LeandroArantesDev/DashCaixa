<div id="modal" class="bg-white w-30 h-30 fixed top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2">
    <h2>Venda finalizada com sucesso!</h2>
    <p>Você deseja imprimir uma ficha?</p>
    <div class="div-btn">
        <button type="button" onclick="esconderModal()">Sair</button>
        <button type="button" onclick="imprimirFicha()">Imprimir</button>
    </div>
    <div id="overlay-modal" onclick="esconderModal()"></div>
    <iframe id="iframe-ficha" src="#" class="hidden"></iframe>
</div>
<script>
    const modal = document.getElementById("modal");
    const iframeFicha = document.getElementById("iframe-ficha");
    let vendaId = null;

    function abrirModal(id) {
        vendaId = id;
        // Define o src do iframe com o ID da venda
        iframeFicha.src = `../../backend/vendas/ficha.php?id=${id}`;
        // troca o estado do modal
        modal.style.visibility = "visible";

        // Adiciona um listener para verificar se há erros no iframe
        iframeFicha.onerror = function() {
            console.error('Erro ao carregar o iframe');
        };

        iframeFicha.onload = function() {
            console.log('Iframe carregado com sucesso');
        };
    }

    function imprimirFicha() {
        if (vendaId && iframeFicha.src) {
            try {
                // Verifica se o iframe está carregado
                if (iframeFicha.contentDocument && iframeFicha.contentDocument.readyState === 'complete') {
                    // Imprime diretamente se já estiver carregado
                    iframeFicha.contentWindow.print();
                    // Aguarda um pouco antes de esconder o modal para garantir que a impressão iniciou
                    setTimeout(() => {
                        esconderModal();
                    }, 1000);
                } else {
                    // Se não estiver carregado, aguarda o carregamento
                    iframeFicha.onload = function() {
                        setTimeout(() => {
                            iframeFicha.contentWindow.print();
                            // Esconde o modal após a impressão
                            setTimeout(() => {
                                esconderModal();
                            }, 1000);
                        }, 500);
                    };
                }
            } catch (error) {
                console.error('Erro ao imprimir:', error);
                alert('Erro ao tentar imprimir a ficha.');
            }
        }
    }

    function esconderModal() {
        // Esconde o modal
        modal.style.visibility = "hidden";
        vendaId = null;
        iframeFicha.src = "";
    }
</script>