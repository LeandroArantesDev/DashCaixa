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
  const btnCalcularTroco = document.getElementById("btn-calcular-troco");
  const inputValorRecebido = document.getElementById("input-valor-recebido");
  const valorTrocoBox = document.getElementById("valor-troco");
  const valorTrocoValor = document.getElementById("valor-troco-valor");
  const btnChamarCalcularTroco = document.getElementById(
    "btn-chamar-calcular-troco"
  );

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

  // Botão para mostrar o campo de calcular troco
  if (btnChamarCalcularTroco) {
    btnChamarCalcularTroco.addEventListener("click", function (e) {
      e.preventDefault();
      btnChamarCalcularTroco.style.display = "none";
      btnCalcularTroco.style.display = "flex";
      if (inputValorRecebido) inputValorRecebido.focus();
    });
  }

  // Atualiza a lista e o total
  function atualizarCarrinho() {
    listaCarrinho.innerHTML = "";
    let total = 0;

    if (carrinho.length === 0) {
      listaCarrinho.innerHTML = `<li class="carrinho-vazio text-center text-gray-500">
                    <i class="bi bi-cart text-6xl mx-auto text-gray-300"></i>
                    <p class="text-lg font-medium mt-10">Nenhum item adicionado</p>
                    <p>Busque e adicione produtos à ficha</p>
                </li>`;
      btnLimparCarrinho.style.display = "none";
      valorTotal.textContent = "R$ 0,00";
      btnFinalizar.disabled = true;
      inputItens.value = "";
      // ESCONDE os botões de troco
      if (btnChamarCalcularTroco) btnChamarCalcularTroco.style.display = "none";
      if (btnCalcularTroco) btnCalcularTroco.style.display = "none";
      if (inputValorRecebido) inputValorRecebido.value = ""; // Limpa o campo valor recebido
      if (valorTrocoBox) valorTrocoBox.style.display = "none"; // Esconde o troco também
      if (valorTrocoValor) valorTrocoValor.textContent = "R$ 0,00"; // Reseta o texto do troco
      return;
    }

    if (carrinho.length > 0) {
      btnLimparCarrinho.style.display = "block";
      btnFinalizar.disabled = false;
      if (btnChamarCalcularTroco) btnChamarCalcularTroco.style.display = "flex";
      if (btnCalcularTroco) btnCalcularTroco.style.display = "none";
    }

    carrinho.forEach((item, idx) => {
      const li = document.createElement("li");
      li.className = "item-carrinho";
      li.innerHTML = `
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        <div class="space-y-2 max-h-80 overflow-y-auto"> 
                            <div class="flex items-center justify-between py-1 px-2 border border-borda rounded-lg bg-gray-50">
                                <div class="flex-1"> 
                                    <h3 class="font-medium text-left text-gray-900 "> ${
                                      item.nome
                                    } </h3>
                                    <p class="text-sm text-left text-gray-500"> ${item.preco
                                      .toFixed(2)
                                      .replace(".", ",")} cada</p>
                                    <p class="text-sm text-left font-medium text-principal"> Subtotal: R$ ${(
                                      item.preco * item.quantidade
                                    )
                                      .toFixed(2)
                                      .replace(".", ",")}</p>
                                </div>
                                <div class="flex items-center space-x-3"> 
                                    <div class="flex items-center space-x-2 overflow-hidden bg-fundo-interface rounded-lg border border-borda">
                                        <button type="button" class="btn-quantidade cursor-pointer p-2 text-gray-500 hover:text-gray-700 hover:scale-160 hover:bg-gray-100 rounded-l-lg" data-acao="diminuir" data-idx="${idx}">
                                        -</button>
                                        <span class="quantidade w-10 text-center font-medium text-lg">${
                                          item.quantidade
                                        }</span>
                                        <button type="button" class="btn-quantidade cursor-pointer p-2 text-gray-500 hover:text-gray-700 hover:scale-160 hover:bg-gray-100 rounded-r-lg" data-acao="aumentar" data-idx="${idx}">+</button>
                                    </div>
                                    <button type="button" class="remover-item p-2 cursor-pointer text-red-500 hover:text-red-700 hover:bg-red-50 hover:scale-120 hover:rounded-lg" data-idx="${idx}" title="Remover">
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

  if (inputValorRecebido) {
    inputValorRecebido.addEventListener("input", function () {
      const recebido = parseFloat(this.value.replace(",", "."));
      const total = parseFloat(
        valorTotal.textContent.replace("R$ ", "").replace(",", ".")
      );
      if (!isNaN(recebido) && recebido >= total && total > 0) {
        const troco = recebido - total;
        valorTrocoValor.textContent =
          "R$ " + troco.toFixed(2).replace(".", ",");
        valorTrocoBox.style.display = "flex";
      } else {
        valorTrocoValor.textContent = "R$ 0,00";
        valorTrocoBox.style.display = "none";
      }
    });
  }
});
