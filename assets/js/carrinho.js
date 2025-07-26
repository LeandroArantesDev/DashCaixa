document.addEventListener("DOMContentLoaded", () => {
    const carrinho = [];
    const listaCarrinho = document.getElementById("lista-carrinho");
    const valorTotal = document.getElementById("valor-total");
    const itensTotal = document.getElementById("itens-total");
    const formImprimir = document.getElementById("form-imprimir");
    const inputItens = document.getElementById("input-itens");
    const btnFinalizar = document.getElementById("btn-finalizar");
    const inputBusca = document.getElementById("busca");
    const produtoCards = document.querySelectorAll(".produto-card");
    const btnLimparCarrinho = document.getElementById("limpar-carrinho");

    // Funcionalidade de busca
    inputBusca.addEventListener("input", function () {
        const termoBusca = this.value.toLowerCase();

        produtoCards.forEach((card) => {
            const nomeProduto = card.querySelector("h3").textContent.toLowerCase();
            const categoria = card.querySelector("p").textContent.toLowerCase();

            if (nomeProduto.includes(termoBusca) || categoria.includes(termoBusca)) {
                card.style.display = "flex";
            } else {
                card.style.display = "none";
            }
        });
    });

    // Botão limpar carrinho
    btnLimparCarrinho.addEventListener("click", function () {
        if (carrinho.length > 0 && confirm("Deseja limpar todo o carrinho?")) {
            carrinho.length = 0;
            atualizarCarrinho();
        }
    });

    // Adicionar produto ao carrinho
    document.querySelectorAll(".add-button").forEach((btn) => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const nome = this.dataset.nome;
            const preco = parseFloat(this.dataset.preco);
            const quantidade = 1;

            // Verifica se já existe no carrinho
            const existente = carrinho.find((item) => item.id === id);
            if (existente) {
                existente.quantidade += quantidade;
            } else {
                carrinho.push({
                    id,
                    nome,
                    preco,
                    quantidade,
                });
            }

            atualizarCarrinho();
        });
    });

    // Ao imprimir, limpa o carrinho e recarrega a página
    formImprimir.addEventListener("submit", function () {

        // Botão fica inacessível ao mandar 
        btnFinalizar.disabled = "true";
        btnFinalizar.innerHTML = "Processando...";

        setTimeout(() => {
            carrinho.length = 0; // esvazia o array
            atualizarCarrinho();
        }, 500);
    });

    // Atualiza a lista e o total
    function atualizarCarrinho() {
        listaCarrinho.innerHTML = "";
        let total = 0;

        if (carrinho.length === 0) {
            listaCarrinho.innerHTML = `<li class="carrinho-vazio text-center text-gray-500 py-12">
                    <i class="bi bi-cart text-6xl mx-auto text-gray-300"></i>
                    <p class="text-lg font-medium mt-10">Nenhum item adicionado</p>
                    <p>Busque e adicione produtos à ficha</p>
                </li>`;
            btnLimparCarrinho.style.display = "none";
            valorTotal.textContent = "R$ 0,00";
            btnFinalizar.disabled = true;
            inputItens.value = "";
            return;
        }

        // Verificar se tem itens no carrinho para mostrar o botão de esvaziar carrinho
        if (carrinho.length > 0) {
            btnLimparCarrinho.style.display = "block";
            btnFinalizar.disabled = false;
        }

        carrinho.forEach((item, idx) => {
            const li = document.createElement("li");
            li.className = "item-carrinho";
            li.innerHTML = `
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        <div class="space-y-3 max-h-80 overflow-y-auto"> 
                            <div class="flex items-center justify-between p-6 border border-gray-200 rounded-lg bg-gray-50">
                                <div class="flex-1"> 
                                    <h3 class="font-medium text-left text-gray-900 text-base"> ${item.nome
                } </h3>
                                    <p class="text-sm text-left text-gray-500"> ${item.preco
                    .toFixed(2)
                    .replace(".", ",")} cada</p>
                                    <p class="text-sm text-left font-medium text-blue-600"> Subtotal: R$ ${(
                    item.preco * item.quantidade
                )
                    .toFixed(2)
                    .replace(".", ",")}</p>
                                </div>
                                <div class="flex items-center space-x-3"> 
                                    <div class="flex items-center space-x-2 bg-white rounded-lg border border-gray-300">
                                        <button type="button" class="btn-quantidade cursor-pointer p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-l-lg" data-acao="diminuir" data-idx="${idx}">
                                        -</button>
                                        <span class="quantidade w-10 text-center font-medium text-lg">${item.quantidade
                }</span>
                                        <button type="button" class="btn-quantidade cursor-pointer p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-r-lg" data-acao="aumentar" data-idx="${idx}">+</button>
                                    </div>
                                    <button type="button" class="remover-item p-2 cursor-pointer text-red-500 hover:text-red-700 hover:bg-red-50  hover:rounded-lg" data-idx="${idx}" title="Remover">
                                    <i class="bi bi-trash3"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            listaCarrinho.appendChild(li);
            total += item.preco * item.quantidade;
        });

        valorTotal.textContent = "R$ " + total.toFixed(2).replace(".", ",");
        itensTotal.textContent = carrinho.length + "itens";
        inputItens.value = JSON.stringify(carrinho);

        // Event listeners para os controles de quantidade
        document.querySelectorAll(".btn-quantidade").forEach((btn) => {
            btn.addEventListener("click", function () {
                const idx = parseInt(this.dataset.idx);
                const acao = this.dataset.acao;

                if (acao === "aumentar") {
                    carrinho[idx].quantidade++;
                } else if (acao === "diminuir" && carrinho[idx].quantidade > 1) {
                    carrinho[idx].quantidade--;
                } else if (acao === "diminuir" && carrinho[idx].quantidade == 1) {
                    carrinho.splice(idx, 1);
                }
                atualizarCarrinho();
            });
        });

        // Event listeners para remover item
        document.querySelectorAll(".remover-item").forEach((btn) => {
            btn.addEventListener("click", function () {
                const idx = parseInt(this.dataset.idx);
                const acao = this.dataset.acao;
                carrinho.splice(idx, 1);
                atualizarCarrinho();
            });
        });
    }
});
