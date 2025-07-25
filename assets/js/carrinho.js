document.addEventListener("DOMContentLoaded", () => {
    const carrinho = [];
    const listaCarrinho = document.getElementById("lista-carrinho");
    const valorTotal = document.getElementById("valor-total");
    const itensTotal = document.getElementById("itens-total");
    const formImprimir = document.getElementById("form-imprimir");
    const inputItens = document.getElementById("input-itens");
    const carrinhoLateral = document.getElementById("carrinho-lateral");
    const btnFecharCarrinho = carrinhoLateral.querySelector(".cabecalho button");
    const inputBusca = document.getElementById("busca");
    const produtoCards = document.querySelectorAll(".produto-card");
    const btnLimparCarrinho = document.getElementById("limpar-carrinho");

    // Funcionalidade de busca
    inputBusca.addEventListener("input", function () {
        const termoBusca = this.value.toLowerCase();

        produtoCards.forEach(card => {
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
    document.querySelectorAll(".add-button").forEach(btn => {
        btn.addEventListener("click", function () {
            const id = this.dataset.id;
            const nome = this.dataset.nome;
            const preco = parseFloat(this.dataset.preco);
            const quantidade = 1;

            // Verifica se já existe no carrinho
            const existente = carrinho.find(item => item.id === id);
            if (existente) {
                existente.quantidade += quantidade;
            } else {
                carrinho.push({
                    id,
                    nome,
                    preco,
                    quantidade
                });
            }

            atualizarCarrinho();
        });
    });

    // Botão fechar carrinho
    document.getElementById("fechar-carrinho").addEventListener("click", function () {
        carrinhoLateral.classList.remove("ativo");
    });

    // Ao imprimir, limpa o carrinho e recarrega a página
    formImprimir.addEventListener("submit", function () {
        setTimeout(() => {
            carrinho.length = 0; // esvazia o array
            atualizarCarrinho();
            window.location.reload();
        }, 500); // espera meio segundo para o submit abrir o PDF
    });

    // Atualiza a lista e o total
    function atualizarCarrinho() {
        listaCarrinho.innerHTML = "";
        let total = 0;

        if (carrinho.length === 0) {
            listaCarrinho.innerHTML = '<li class="carrinho-vazio"><p>Nenhum produto selecionado</p></li>';
            formImprimir.style.display = "none";
            btnLimparCarrinho.style.display = "none";
            valorTotal.textContent = "R$ 0,00";
            inputItens.value = "";
            return;
        }

        // Verificar se tem itens no carrinho para mostrar o botão de esvaziar carrinho
        if (carrinho.length > 0) {
            btnLimparCarrinho.style.display = "block";
        }

        carrinho.forEach((item, idx) => {
            const li = document.createElement("li");
            li.className = "item-carrinho";
            li.innerHTML = `
                    <div class="item-info">
                        <span class="item-nome">${item.nome}</span>
                        <span class="item-preco">R$ ${item.preco.toFixed(2).replace('.', ',')} cada</span>
                        <span class="item-preco">Subtotal: R$ ${(item.preco * item.quantidade).toFixed(2).replace('.', ',')}</span>
                    </div>
                        <div class="item-controles">
                        <button type="button" class="btn-quantidade" data-acao="diminuir" data-idx="${idx}">-</button>
                        <span class="quantidade">${item.quantidade}</span>
                        <button type="button" class="btn-quantidade" data-acao="aumentar" data-idx="${idx}">+</button>
                    </div>
                    <div class="item-preco-remover">
                        <button type="button" class="remover-item" data-idx="${idx}" title="Remover">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                `;
            listaCarrinho.appendChild(li);
            total += item.preco * item.quantidade;
        });

        valorTotal.textContent = "R$ " + total.toFixed(2).replace('.', ',');
        itensTotal.textContent = carrinho.length + "itens";
        formImprimir.style.display = "block";
        inputItens.value = JSON.stringify(carrinho);

        // Event listeners para os controles de quantidade
        document.querySelectorAll(".btn-quantidade").forEach(btn => {
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
        document.querySelectorAll(".remover-item").forEach(btn => {
            btn.addEventListener("click", function () {
                const idx = parseInt(this.dataset.idx);
                const acao = this.dataset.acao;
                carrinho.splice(idx, 1);
                atualizarCarrinho();
            });
        });
    }
});