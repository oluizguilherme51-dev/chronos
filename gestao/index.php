<?php
session_name('CHRONOS_SESSION');
session_start();

require_once __DIR__ . '/../includes/conexao.php';

if (
    empty($_SESSION['usuario_id']) ||
    ($_SESSION['perfil'] ?? '') !== 'admin'
) {
    header('Location: ../conta/entrar.php');
    exit;
}

require __DIR__ . '/../includes/cabecalho.php';
?>

<main class="container pagina-gestao">

    <div class="titulo-gestao">

        <span class="legenda">
            ADMINISTRAÇÃO CHRONOS
        </span>

        <h1>Painel administrativo</h1>

        <p>
            Bem-vindo,
            <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?>.
        </p>

    </div>

    <div class="row g-4 justify-content-center">

        <div class="col-md-5">

            <div class="cartao-gestao">

                <span class="numero-romano">I</span>

                <h2>Relógios</h2>

                <p>
                    Cadastre, edite e gerencie
                    as peças da coleção Chronos.
                </p>

                <a href="relogios.php"
                   class="botao-chronos">

                    GERENCIAR RELÓGIOS

                </a>

            </div>

        </div>

        <div class="col-md-5">

            <div class="cartao-gestao">

                <span class="numero-romano">II</span>

                <h2>Vendas</h2>

                <p>
                    Consulte os pedidos realizados
                    e suas formas de pagamento.
                </p>

                <a href="vendas.php"
                   class="botao-chronos">

                    VISUALIZAR VENDAS

                </a>

            </div>

        </div>

    </div>

    <div class="text-center mt-5">

        <a href="../index.php"
           class="voltar-colecao">

            ← Voltar à loja

        </a>

    </div>

</main>

<?php require __DIR__ . '/../includes/rodape.php'; ?>