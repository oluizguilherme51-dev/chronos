<?php
session_name('CHRONOS_SESSION');
session_start();

require_once __DIR__ . '/../includes/conexao.php';

if (!isset($_SESSION['sacola'])) {
    $_SESSION['sacola'] = [];
}

if (empty($_SESSION['token_sacola'])) {
    $_SESSION['token_sacola'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['token'] ?? '';

    if (!hash_equals($_SESSION['token_sacola'], $token)) {
        exit('Solicitação inválida.');
    }

    $id = (int) ($_POST['relogio_id'] ?? 0);
    $quantidade = (int) ($_POST['quantidade'] ?? 1);

    $consulta = $pdo->prepare(
        "SELECT * FROM relogios WHERE id = ?"
    );

    $consulta->execute([$id]);
    $relogio = $consulta->fetch();

    if (isset($_POST['remover'])) {

        unset($_SESSION['sacola'][$id]);

        $_SESSION['mensagem_sacola'] =
            'Peça removida da sacola.';

    } elseif ($relogio) {

        if (isset($_POST['adicionar'])) {

            $quantidadeAtual =
                $_SESSION['sacola'][$id] ?? 0;

            $novaQuantidade =
                $quantidadeAtual + $quantidade;

        } elseif (isset($_POST['atualizar'])) {

            $novaQuantidade = $quantidade;

        } else {

            $novaQuantidade = 0;
        }

        if (
            $quantidade > 0 &&
            $novaQuantidade > 0 &&
            $novaQuantidade <= $relogio['estoque']
        ) {

            $_SESSION['sacola'][$id] = $novaQuantidade;

            $_SESSION['mensagem_sacola'] =
                'Sacola atualizada com sucesso.';

        } else {

            $_SESSION['mensagem_sacola'] =
                'Quantidade indisponível em estoque.';
        }
    }

    header('Location: sacola.php');
    exit;
}

$mensagem = $_SESSION['mensagem_sacola'] ?? '';

unset($_SESSION['mensagem_sacola']);

$itens = [];
$total = 0;
$estoqueValido = true;

foreach ($_SESSION['sacola'] as $id => $quantidade) {

    $consulta = $pdo->prepare(
        "SELECT * FROM relogios WHERE id = ?"
    );

    $consulta->execute([$id]);
    $relogio = $consulta->fetch();

    if (!$relogio) {
        unset($_SESSION['sacola'][$id]);
        continue;
    }

    $relogio['quantidade'] = $quantidade;

    $relogio['subtotal'] =
        $relogio['preco'] * $quantidade;

    if ($quantidade > $relogio['estoque']) {
        $estoqueValido = false;
    }

    $total += $relogio['subtotal'];

    $itens[] = $relogio;
}

require __DIR__ . '/../includes/cabecalho.php';
?>

<main class="container pagina-sacola">

    <div class="titulo-sacola">

        <span class="legenda">
            SUA SELEÇÃO PARTICULAR
        </span>

        <h1>Minha sacola</h1>

        <p>
            O tempo é precioso. Escolha como vivê-lo.
        </p>

    </div>

    <?php if ($mensagem): ?>

        <div class="mensagem-sacola">
            <?= htmlspecialchars($mensagem) ?>
        </div>

    <?php endif; ?>

    <?php if (empty($itens)): ?>

        <div class="sacola-vazia">

            <span class="ornamento">◇</span>

            <h2>Sua sacola está vazia</h2>

            <p>
                Descubra peças que atravessam gerações.
            </p>

            <a href="../index.php#colecao"
               class="botao-chronos">

                Explorar coleção

            </a>

        </div>

    <?php else: ?>

        <div class="row g-4">

            <div class="col-lg-8">

                <?php foreach ($itens as $item): ?>

                    <article class="item-sacola">

                        <div class="imagem-sacola">

                            <img
                                src="../imagens/<?= htmlspecialchars($item['imagem']) ?>"
                                alt="<?= htmlspecialchars($item['modelo']) ?>"
                            >

                        </div>

                        <div class="dados-sacola">

                            <span class="legenda">
                                PEÇA SELECIONADA
                            </span>

                            <h2>
                                <?= htmlspecialchars($item['marca']) ?>
                            </h2>

                            <p>
                                <?= htmlspecialchars($item['modelo']) ?>
                            </p>

                            <p class="preco-unitario">

                                R$ <?= number_format(
                                    $item['preco'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </p>

                            <form method="POST"
                                  class="form-sacola">

                                <input type="hidden"
                                       name="relogio_id"
                                       value="<?= $item['id'] ?>">

                                <input type="hidden"
                                       name="token"
                                       value="<?= htmlspecialchars($_SESSION['token_sacola']) ?>">

                                <label>Quantidade</label>

                                <input type="number"
                                       name="quantidade"
                                       min="1"
                                       max="<?= $item['estoque'] ?>"
                                       value="<?= $item['quantidade'] ?>"
                                       required>

                                <button type="submit"
                                        name="atualizar"
                                        class="atualizar-sacola">

                                    Atualizar

                                </button>

                                <button type="submit"
                                        name="remover"
                                        class="remover-sacola"
                                        formnovalidate>

                                    Remover

                                </button>

                            </form>

                            <?php if ($item['quantidade'] > $item['estoque']): ?>

                                <p class="text-danger mt-2">
                                    Quantidade indisponível em estoque.
                                </p>

                            <?php endif; ?>

                            <p class="subtotal-sacola">

                                Subtotal:

                                R$ <?= number_format(
                                    $item['subtotal'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </p>

                        </div>

                    </article>

                <?php endforeach; ?>

                <a href="../index.php#colecao"
                   class="voltar-colecao mt-4">

                    ← Continuar explorando

                </a>

            </div>

            <div class="col-lg-4">

                <aside class="resumo-sacola">

                    <span class="legenda">
                        SUA AQUISIÇÃO
                    </span>

                    <h2>Resumo do pedido</h2>

                    <div class="linha-resumo">

                        <span>Quantidade de peças</span>

                        <strong>
                            <?= array_sum($_SESSION['sacola']) ?>
                        </strong>

                    </div>

                    <div class="divisao-bronze"></div>

                    <div class="total-sacola">

                        <span>Total</span>

                        <strong>

                            R$ <?= number_format(
                                $total,
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>

                    <?php if ($estoqueValido): ?>

                        <a href="checkout.php"
                           class="botao-chronos finalizar-sacola">

                            FINALIZAR COMPRA

                        </a>

                    <?php else: ?>

                        <p class="text-danger">
                            Atualize as quantidades para continuar.
                        </p>

                    <?php endif; ?>

                    <p class="aviso-academico">

                        Compra demonstrativa —
                        projeto acadêmico.

                    </p>

                </aside>

            </div>

        </div>

    <?php endif; ?>

</main>

<?php require __DIR__ . '/../includes/rodape.php'; ?>