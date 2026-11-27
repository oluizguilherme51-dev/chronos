document.addEventListener("DOMContentLoaded", function () {

    // Validar quantidade dos relógios

    const quantidades = document.querySelectorAll(
        'input[name="quantidade"]'
    );

    quantidades.forEach(function (campo) {

        campo.addEventListener("change", function () {

            const minimo = Number(campo.min || 1);
            const maximo = Number(campo.max);

            if (Number(campo.value) < minimo) {
                campo.value = minimo;
            }

            if (campo.max && Number(campo.value) > maximo) {

                campo.value = maximo;

                alert("Quantidade máxima disponível em estoque.");
            }

        });

    });


    // Confirmar remoção da sacola

    const botoesRemover = document.querySelectorAll(
        'button[name="remover"]'
    );

    botoesRemover.forEach(function (botao) {

        botao.addEventListener("click", function (evento) {

            const confirmar = confirm(
                "Deseja remover este relógio da sacola?"
            );

            if (!confirmar) {
                evento.preventDefault();
            }

        });

    });


    // Validar confirmação de senha no cadastro

    const formularioCadastro = document.querySelector(
        'form:has(input[type="password"])'
    );

    if (formularioCadastro) {

        const senhas = formularioCadastro.querySelectorAll(
            'input[type="password"]'
        );

        if (senhas.length === 2) {

            formularioCadastro.addEventListener(
                "submit",
                function (evento) {

                    if (senhas[0].value !== senhas[1].value) {

                        evento.preventDefault();

                        alert("As senhas precisam ser iguais.");

                        senhas[1].focus();
                    }

                }
            );

        }

    }


    // Destacar a forma de pagamento

    const pagamentos = document.querySelectorAll(
        'input[name="pagamento"]'
    );

    pagamentos.forEach(function (pagamento) {

        pagamento.addEventListener("change", function () {

            document.querySelectorAll(
                ".opcao-pagamento"
            ).forEach(function (opcao) {

                opcao.classList.remove("pagamento-selecionado");

            });

            if (pagamento.checked) {

                pagamento.closest(".opcao-pagamento")
                    .classList.add("pagamento-selecionado");

            }

        });

    });


    // Evitar pedidos duplicados

    const botaoCompra = document.querySelector(
        ".confirmar-compra"
    );

    if (botaoCompra && botaoCompra.form) {

        botaoCompra.form.addEventListener(
            "submit",
            function () {

                botaoCompra.disabled = true;

                botaoCompra.textContent = "PROCESSANDO PEDIDO...";

            }
        );

    }

});