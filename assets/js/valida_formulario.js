document.addEventListener("DOMContentLoaded", function () {

    //Validação em tempo real do nome;
    document.querySelectorAll('input[name="nome"]').forEach(function (input) {
        input.addEventListener("input", function () {
            this.value = this.value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ ]/g, "").slice(0, 100);
        });
    });

    //Validação em tempo real do campo descrição;
    document.querySelectorAll('input[name="descricao"]').forEach(function (input) {
        input.addEventListener("input", function () {
            this.value = this.value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ0-9 !?,.]/g, "").slice(0, 100);
        })
    })

    //Validação em tempo real do campo descrição;
    document.querySelectorAll('input[name="quantidadeComida"]').forEach(function (input) {
        input.addEventListener("input", function () {
            // Permite apenas números inteiros entre 1 e 25
            let valor = this.value.replace(/\D/g, "");
            if (valor !== "") {
                valor = Math.max(1, Math.min(25, parseInt(valor, 10))).toString();
            }
            this.value = valor;
        })
    })

    //Validação em tempo real do campo ingredientes;
    document.querySelectorAll('input[name="ingredientes"]').forEach(function (input) {
        input.addEventListener("input", function () {
            // Agora permite letras, acentos, números, espaços e !?,.
            this.value = this.value.replace(/[^A-Za-zÀ-ÖØ-öø-ÿ0-9 !?,.]/g, "").slice(0, 100);
        })
    })

    //Validação em tempo real do campo email;
    document.querySelectorAll('input[type="email"]').forEach(function (input) {
        input.addEventListener("input", function () {
            // Permite apenas caracteres válidos para e-mail
            this.value = this.value.replace(/[^a-zA-Z0-9@._%+-]/g, "").slice(0, 40);
        })
    })

    //Validação em tempo real do campo senha;
    document.querySelectorAll('input[type="password"]').forEach(function (input) {
        input.addEventListener("input", function () {
            // Permite letras, números e símbolos comuns de senha, até 40 caracteres
            this.value = this.value.replace(/[^a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/g, "").slice(0, 40);
        })
    })

    document.querySelectorAll("form").forEach(function (form) {
        form.addEventListener("submit", function (e) {
            let valido = true;
            let mensagens = [];

            let nome = form.querySelector('input[name="nome"]');
            if (nome && !/^(?=.{3,18}$)[A-Za-zÀ-ÖØ-öø-ÿ]+(?: [A-Za-zÀ-ÖØ-öø-ÿ]+)*$/.test(nome.value.trim())) {
                valido = false;
                mensagens.push("Digite um nome com pelo menos 3 letras (no máximo 18 caracteres)");
            }

            let descricao = form.querySelector('input[name="descricao"]');
            if (descricao && descricao.value.trim().length < 3) {
                valido = false;
                mensagens.push("A descrição deve ter pelo menos 3 caracteres.");
            }

            let email = form.querySelector('input[type="email"]');
            if (email && !/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i.test(email.value.trim())) {
                valido = false;
                mensagens.push("Insira um e-mail válido.");
            }

            let preco = form.querySelector('input[name="preco"]');
            if (preco && (isNaN(parseFloat(preco.value)) || parseFloat(preco.value) <= 0 || parseFloat(preco.value) > 9999.99)) {
                valido = false;
                mensagens.push("Insira um preço válido entre 0.01 e 9999.99.");
            }

            let ingredientes = form.querySelector('input[name="ingredientes"]');
            if (ingredientes) {
                const valor = ingredientes.value.trim();
                // Agora permite letras, acentos, números, espaços e !?,.
                const regex = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9 !?,.]{3,100}$/;
                if (!regex.test(valor)) {
                    valido = false;
                    mensagens.push("Os ingredientes devem ter entre 3 e 100 caracteres e conter apenas letras, números, espaços e os símbolos ! ? , .");
                }
            }

            let senha = form.querySelector('input[type="password"]');
            let alterarSenhaCheckbox = form.querySelector('input[name="alterarsenha"]');
            let deveValidarSenha = true;

            // Se existe o checkbox e ele NÃO está marcado, não valida a senha
            if (alterarSenhaCheckbox && !alterarSenhaCheckbox.checked) {
                deveValidarSenha = false;
            }

            if (senha && deveValidarSenha) {
                const valor = senha.value;
                const requisitos = [
                    { regex: /.{8,}/, mensagem: "A senha deve conter no mínimo 8 caracteres." },
                    { regex: /[A-Z]/, mensagem: "A senha deve conter pelo menos uma letra maiúscula." },
                    { regex: /[a-z]/, mensagem: "A senha deve conter pelo menos uma letra minúscula." },
                    { regex: /[0-9]/, mensagem: "A senha deve conter pelo menos um número." },
                    { regex: /[^A-Za-z0-9]/, mensagem: "A senha deve conter pelo menos um símbolo." }
                ];
                requisitos.forEach(req => {
                    if (!req.regex.test(valor)) {
                        valido = false;
                        mensagens.push(req.mensagem);
                    }
                });
            }

            if (!valido) {
                e.preventDefault();
                alert(mensagens.join("\n"));
            } else {
                const btn_form = form.querySelector("button[type='submit'],button:not([type])");
                if (btn_form) {
                    btn_form.disabled = true;
                }
            }
        });
    });
});