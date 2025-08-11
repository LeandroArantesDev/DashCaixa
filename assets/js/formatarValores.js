// Função para formatar input de preço como aplicativo de banco
function formatarPrecoInput(inputElement, inputHiddenElement = null) {
  // Remove tudo que não é número
  let valor = inputElement.value.replace(/\D/g, "");

  // Se não tem valor, reseta
  if (valor === "") {
    inputElement.value = "R$ 0,00";
    if (inputHiddenElement) {
      inputHiddenElement.value = "0.00";
    }
    return;
  }

  // Converte para número e divide por 100 para ter centavos
  let numero = parseInt(valor) / 100;

  // Formata como moeda brasileira
  let formatado = numero.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
    minimumFractionDigits: 2,
  });

  // Atualiza o input visível
  inputElement.value = formatado;

  // Atualiza o input hidden com o valor real (formato americano para o backend)
  if (inputHiddenElement) {
    inputHiddenElement.value = numero.toFixed(2);
  }
}

// Função para aplicar formatação em um input
function aplicarFormatacaoPreco(inputId, inputHiddenId = null) {
  const input = document.getElementById(inputId);
  const inputHidden = inputHiddenId
    ? document.getElementById(inputHiddenId)
    : null;

  if (!input) return;

  // Event listener para formatação durante digitação
  input.addEventListener("input", function (e) {
    formatarPrecoInput(this, inputHidden);
  });

  // Event listener para validar teclas
  input.addEventListener("keydown", function (e) {
    // Permite apenas números, backspace, delete e tab
    if (
      !/[0-9]/.test(e.key) &&
      !["Backspace", "Delete", "Tab", "ArrowLeft", "ArrowRight"].includes(e.key)
    ) {
      e.preventDefault();
    }
  });

  // Inicializa o campo com R$ 0,00
  input.value = "R$ 0,00";
  if (inputHidden) {
    inputHidden.value = "0.00";
  }
}

// Função para converter valor formatado de volta para número
function converterPrecoParaNumero(valorFormatado) {
  // Remove R$, espaços e converte vírgula para ponto
  let numero = valorFormatado.replace(/[R$\s]/g, "").replace(",", ".");
  return parseFloat(numero) || 0;
}

// Função para formatar um valor numérico para exibição
function formatarPrecoExibicao(valor) {
  let numero = parseFloat(valor) || 0;
  return numero.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
    minimumFractionDigits: 2,
  });
}

// Função para formatar outros tipos de valores monetários
function formatarValorMonetario(input) {
  let valor = input.value.replace(/\D/g, "");

  if (valor === "") {
    input.value = "R$ 0,00";
    return;
  }

  let numero = parseInt(valor) / 100;
  input.value = numero.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
    minimumFractionDigits: 2,
  });
}
