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
  const valorTrocoLabel = document.getElementById("valor-troco-label");
  const btnChamarCalcularTroco = document.getElementById(
    "btn-chamar-calcular-troco"
  );

  // Função para obter o estoque de um produto pelo ID
  function obterEstoqueProduto(produtoId) {
    const produtoCard = document
      .querySelector(`[data-id="${produtoId}"]`)
      .closest(".produto-card");
    const estoqueText = produtoCard.querySelector("h3").textContent;

    // Extrai o número do estoque do texto
    const match = estoqueText.match(/(\d+)/);
    return match ? parseInt(match[0]) : 0;
  }

  // Função para mostrar mensagem de erro
  function mostrarMensagemEstoque(mensagem) {
    // Cria um toast ou alerta temporário
    const toast = document.createElement("div");
    toast.className =
      "border border-borda flex items-center justify-center gap-4 text-lg rounded-xl fixed right-8 bottom-[-50%] shadow-sm bg-fundo-interface px-8 py-2 animate-[show_5s]";
    toast.innerHTML = `<i class="bi bi-info-circle-fill"></i> ${mensagem}`;
    document.body.appendChild(toast);

    setTimeout(() => {
      document.body.removeChild(toast);
    }, 3000);
  }

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

  // Adicionar produto ao carrinho (MODIFICADO)
  document.querySelectorAll(".add-button").forEach((btn) => {
    btn.addEventListener("click", function () {
      const id = this.dataset.id;
      const nome = this.dataset.nome;
      const preco = parseFloat(this.dataset.preco);
      const quantidade = 1;
      const estoqueDisponivel = obterEstoqueProduto(id);

      // Verifica se já existe no carrinho
      const existente = carrinho.find((item) => item.id === id);
      const quantidadeAtual = existente ? existente.quantidade : 0;
      const novaQuantidade = quantidadeAtual + quantidade;

      // Verifica se a nova quantidade não excede o estoque
      if (novaQuantidade > estoqueDisponivel) {
        mostrarMensagemEstoque(
          `Estoque insuficiente! Disponível: ${estoqueDisponivel}, no carrinho: ${quantidadeAtual}`
        );
        return;
      }

      if (existente) {
        existente.quantidade += quantidade;
      } else {
        carrinho.push({
          id,
          nome,
          preco,
          quantidade,
          estoque: estoqueDisponivel, // Armazena o estoque para referência
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

  // Atualiza a lista e o total (MODIFICADO)
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
      if (btnChamarCalcularTroco) btnChamarCalcularTroco.style.display = "none";
      if (btnCalcularTroco) btnCalcularTroco.style.display = "none";
      if (inputValorRecebido) inputValorRecebido.value = "";
      if (valorTrocoBox) valorTrocoBox.style.display = "none";
      if (valorTrocoValor) valorTrocoValor.textContent = "R$ 0,00";
      return;
    }

    if (carrinho.length > 0) {
      btnLimparCarrinho.style.display = "block";
      btnFinalizar.disabled = false;
      if (btnChamarCalcularTroco) btnChamarCalcularTroco.style.display = "flex";
      if (btnCalcularTroco) btnCalcularTroco.style.display = "none";
    }

    carrinho.forEach((item, idx) => {
      const estoqueAtual = obterEstoqueProduto(item.id);
      const li = document.createElement("li");
      li.className = "item-carrinho";
      li.innerHTML = `
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        <div class="space-y-2 max-h-80 overflow-y-auto"> 
                            <div class="flex items-center justify-between py-1 px-2 border border-borda rounded-lg bg-gray-50">
                                <div class="flex-1"> 
                                    <h3 class="font-medium text-left text-gray-900"> ${
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
                                        <button type="button" class="btn-quantidade cursor-pointer p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-l-lg" data-acao="diminuir" data-idx="${idx}">
                                        -</button>
                                        <span class="quantidade w-10 text-center font-medium text-lg">${
                                          item.quantidade
                                        }</span>
                                        <button type="button" class="btn-quantidade cursor-pointer p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-r-lg ${
                                          item.quantidade >= estoqueAtual
                                            ? "opacity-50 cursor-not-allowed"
                                            : ""
                                        }" data-acao="aumentar" data-idx="${idx}" ${
        item.quantidade >= estoqueAtual ? "disabled" : ""
      }>+</button>
                                    </div>
                                    <button type="button" class="remover-item p-2 cursor-pointer text-red-700 hover:text-red-800 hover:bg-red-50 hover:scale-120 hover:rounded-lg" data-idx="${idx}" title="Remover">
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

    // Event listeners para os controles de quantidade (MODIFICADO)
    document.querySelectorAll(".btn-quantidade").forEach((btn) => {
      btn.addEventListener("click", function () {
        if (this.disabled) return; // Previne clique em botão desabilitado

        const idx = parseInt(this.dataset.idx);
        const acao = this.dataset.acao;
        const item = carrinho[idx];
        const estoqueDisponivel = obterEstoqueProduto(item.id);

        if (acao === "aumentar") {
          if (item.quantidade < estoqueDisponivel) {
            carrinho[idx].quantidade++;
          } else {
            mostrarMensagemEstoque(
              `Estoque máximo atingido! Disponível: ${estoqueDisponivel}`
            );
            return;
          }
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
      if (!isNaN(recebido) && total > 0) {
        if (recebido >= total) {
          const troco = recebido - total;
          valorTrocoLabel.textContent = "TROCO:";
          valorTrocoValor.textContent =
            "R$ " + troco.toFixed(2).replace(".", ",");
          valorTrocoValor.classList.remove("text-red-600");
          valorTrocoValor.classList.add("text-green-600");
          valorTrocoBox.style.display = "flex";
        } else if (recebido < total) {
          const falta = total - recebido;
          valorTrocoLabel.textContent = "FALTA:";
          valorTrocoValor.textContent =
            "R$ " + falta.toFixed(2).replace(".", ",");
          valorTrocoValor.classList.remove("text-green-600");
          valorTrocoValor.classList.add("text-red-600");
          valorTrocoBox.style.display = "flex";
        }
      } else {
        valorTrocoLabel.textContent = "TROCO:";
        valorTrocoValor.textContent = "R$ 0,00";
        valorTrocoValor.classList.remove("text-red-600");
        valorTrocoValor.classList.add("text-green-600");
        valorTrocoBox.style.display = "none";
      }
    });
  }
});
