<div id="modal-vendas" class="fixed invisible top-0 left-0 w-full h-full z-1000">
    <div class="flex absolute flex-col p-5 items-center left-1/2 top-1/2 -translate-1/2 shadow-2xl bg-white rounded-2xl gap-3 z-950">
        <h2 class="text-xl font-semibold">Venda finalizada com sucesso!</h2>
        <p>Você deseja imprimir uma ficha?</p>
        <div class="flex gap-2 w-full">
            <button class="border border-[var(--cinza-borda)] hover:bg-gray-200 p-2 rounded-lg w-1/2 cursor-pointer" type="button" onclick="esconderModal()">Sair</button>
            <button class="border border-[var(--cinza-borda)] hover:bg-sky-700 p-2 rounded-lg w-1/2 cursor-pointer bg-sky-600 text-white" type="button" onclick="imprimirFicha()">
                <i class="bi bi-printer"></i>
                <span>Imprimir</span>
            </button>
        </div>
    </div>
    <div id="overlay-modal-vendas" class="absolute top-0 left-0 w-full h-full bg-black/50 z-900" onclick="esconderModal()"></div>
    <iframe id="iframe-ficha" src="#" class="hidden"></iframe>
</div>
<script>
    const modal = document.getElementById("modal-vendas");
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