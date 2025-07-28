const btn_alterar_senha = document.getElementById("btn-alterar-senha");
const form_alterar_senha = document.getElementById("form-alterar-senha");
const input_alterar_senha = document.getElementById("input-senha");

function mostrarFormSenha() {
  // escondendo botão e input senha
  btn_alterar_senha.style.display = "none";
  input_alterar_senha.style.display = "none";

  // mostrando formulario de alterar senha
  form_alterar_senha.style.display = "block";
}

function esconderFormSenha() {
  // mostrando o botão e input senha
  btn_alterar_senha.style.display = "block";
  input_alterar_senha.style.display = "block";

  // escondendo o formulario de alterar senha
  form_alterar_senha.style.display = "none";
}

// variaveis para a div de informações
const btn_editar_info = document.getElementById("btn-editar-info");
const form_editar_info = document.getElementById("form-editar-info");
const div_info = document.getElementById("div-info");

function mostrarEditarInfo() {
  // escondendo o botão e div info
  btn_editar_info.style.display = "none";
  div_info.style.display = "none";

  // mostrando o formulario do editar info
  form_editar_info.style.display = "block";
}

function esconderEditarInfo() {
  // mostrando o botão e div info
  btn_editar_info.style.display = "block";
  div_info.style.display = "block";

  // escondendo o formulario do editar info
  form_editar_info.style.display = "none";
}

// Função para alternar visualização da senha e ícone
document.querySelectorAll(".button-eye").forEach(function (btn) {
  btn.addEventListener("click", function () {
    const input = btn.parentElement.querySelector(
      'input[type="password"], input[type="text"]'
    );
    const icon = btn.querySelector("i");
    if (input.type === "password") {
      input.type = "text";
      icon.classList.remove("bi-eye");
      icon.classList.add("bi-eye-slash");
    } else {
      input.type = "password";
      icon.classList.remove("bi-eye-slash");
      icon.classList.add("bi-eye");
    }
  });
});
