<?php
session_name('CHRONOS_SESSION');
session_start();

require_once __DIR__ . '/../includes/conexao.php';

if (empty($_SESSION['usuario_id'])) {
    header('Location: ../conta/entrar.php');
    exit;
}

if (empty($_SESSION['token_checkout'])) {
    $_SESSION['token_checkout'] = bin2hex(random_bytes(32));
}

$mensagem = '';
$pedido = $_SESSION['pedido_confirmado'] ?? null;

unset($_SESSION['pedido_confirmado']);

$sacola = $_SESSION['sacola'] ?? [];

if (empty($sacola) && !$pedido) {
    header('Location: sacola.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['token'] ?? '';
    $pagamento = $_POST['pagamento'] ?? '';

    $formasPagamento = ['PIX', 'Boleto', 'Cartão'];

    if (!hash_equals($_SESSION['token_checkout'], $token)) {

        $mensagem = 'Solicitação inválida.';

    } elseif (!in_array($pagamento, $formasPagamento, true)) {

        $mensagem = 'Selecione uma forma de pagamento.';

    } elseif (empty($sacola)) {

        $mensagem = 'Sua sacola está vazia.';

    } else {

        try {

            $pdo->beginTransaction();

            $total = 0;
            $produtos = [];

            ksort($sacola);

            foreach ($sacola as $id => $quantidade) {

                $consulta = $pdo->prepare(
                    "SELECT * FROM relogios
                     WHERE id = ?
                     FOR UPDATE"
                );

                $consulta->execute([$id]);

                $relogio = $consulta->fetch();

                if (
                    !$relogio ||
                    $quantidade <= 0 ||
                    $quantidade > $relogio['estoque']
                ) {
                    throw new RuntimeException(
                        'Uma das peças não possui estoque suficiente.'
                    );
                }

                $total += $relogio['preco'] * $quantidade;

                $produtos[] = [
                    'id' => $id,
                    'quantidade' => $quantidade,
                    'preco' => $relogio['preco']
                ];
            }

            $consulta = $pdo->prepare(
                "INSERT INTO vendas
                (usuario_id, pagamento, valor_total)
                VALUES (?, ?, ?)"
            );

            $consulta->execute([
                $_SESSION['usuario_id'],
                $pagamento,
                $total
            ]);

            $vendaId = $pdo->lastInsertId();

            foreach ($produtos as $produto) {

                $consulta = $pdo->prepare(
                    "INSERT INTO itens_venda
                    (venda_id, relogio_id, quantidade, valor_unitario)
                    VALUES (?, ?, ?, ?)"
                );

                $consulta->execute([
                    $vendaId,
                    $produto['id'],
                    $produto['quantidade'],
                    $produto['preco']
                ]);

                $consulta = $pdo->prepare(
                    "UPDATE relogios
                     SET estoque = estoque - ?
                     WHERE id = ?"
                );

                $consulta->execute([
                    $produto['quantidade'],
                    $produto['id']
                ]);
            }

            $pdo->commit();

            $_SESSION['sacola'] = [];

            $_SESSION['pedido_confirmado'] = [
                'id' => $vendaId,
                'pagamento' => $pagamento,
                'total' => $total
            ];

            unset($_SESSION['token_checkout']);

            header('Location: checkout.php');
            exit;

        } catch (Throwable $erro) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            if ($erro instanceof RuntimeException &&
                !($erro instanceof PDOException)) {

                $mensagem = $erro->getMessage();

            } else {

                $mensagem = 'Não foi possível finalizar a compra.';

                error_log($erro->getMessage());
            }
        }
    }
}

$itens = [];
$total = 0;
$estoqueValido = true;

foreach ($sacola as $id => $quantidade) {

    $consulta = $pdo->prepare(
        "SELECT * FROM relogios WHERE id = ?"
    );

    $consulta->execute([$id]);

    $relogio = $consulta->fetch();

    if (!$relogio) {
        $estoqueValido = false;
        continue;
    }

    if ($quantidade <= 0 || $quantidade > $relogio['estoque']) {
        $estoqueValido = false;
    }

    $relogio['quantidade'] = $quantidade;
    $relogio['subtotal'] = $relogio['preco'] * $quantidade;

    $total += $relogio['subtotal'];

    $itens[] = $relogio;
}

require __DIR__ . '/../includes/cabecalho.php';
?>

<main class="container pagina-checkout">

    <?php if ($pedido): ?>

        <div class="pedido-confirmado">

            <div class="simbolo-confirmacao">✓</div>

            <span class="legenda">
                UMA ESCOLHA ATEMPORAL
            </span>

            <h1>Pedido confirmado</h1>

            <p>
                Obrigado por escolher a Chronos,
                <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?>.
            </p>

            <div class="informacoes-pedido">

                <p>
                    Número do pedido:
                    <strong>#<?= htmlspecialchars($pedido['id']) ?></strong>
                </p>

                <p>
                    Pagamento:
                    <strong>
                        <?= htmlspecialchars($pedido['pagamento']) ?>
                    </strong>
                </p>

                <p>
                    Valor total:
                    <strong>
                        R$ <?= number_format($pedido['total'], 2, ',', '.') ?>
                    </strong>
                </p>

            </div>

            <p class="aviso-academico">
                Pedido registrado com pagamento simulado.
            </p>

            <a href="../index.php"
               class="botao-chronos">

                VOLTAR À COLEÇÃO

            </a>

        </div>

    <?php else: ?>

        <div class="titulo-checkout">

            <span class="legenda">
                O PRÓXIMO CAPÍTULO
            </span>

            <h1>Finalizar compra</h1>

            <p>
                Uma escolha que permanece através do tempo.
            </p>

        </div>

        <?php if ($mensagem): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($mensagem) ?>
            </div>

        <?php endif; ?>

        <div class="row g-4">

            <div class="col-lg-7">

                <div class="painel-checkout">

                    <span class="legenda">
                        I — IDENTIFICAÇÃO
                    </span>

                    <h2>Seus dados</h2>

                    <p>
                        Olá,
                        <strong>
                            <?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?>
                        </strong>.
                    </p>

                    <p>
                        Sua conta está conectada.
                    </p>

                    <div class="divisao-bronze"></div>

                    <span class="legenda">
                        II — PAGAMENTO
                    </span>

                    <h2>Forma de pagamento</h2>

                    <p>
                        Selecione sua opção de pagamento.
                    </p>

                    <form method="POST">

                        <input type="hidden"
                               name="token"
                               value="<?= htmlspecialchars($_SESSION['token_checkout']) ?>">

                        <label class="opcao-pagamento">

                            <input type="radio"
                                   name="pagamento"
                                   value="PIX"
                                   required>

                            <div>
                                <strong>PIX</strong>
                                <small>Pagamento instantâneo</small>
                            </div>

                        </label>

                        <label class="opcao-pagamento">

                            <input type="radio"
                                   name="pagamento"
                                   value="Boleto"
                                   required>

                            <div>
                                <strong>Boleto bancário</strong>
                                <small>Pagamento por boleto</small>
                            </div>

                        </label>

                        <label class="opcao-pagamento">

                            <input type="radio"
                                   name="pagamento"
                                   value="Cartão"
                                   required>

                            <div>
                                <strong>Cartão</strong>
                                <small>Pagamento com cartão</small>
                            </div>

                        </label>

                        <p class="aviso-academico">
                            Pagamento exclusivamente simulado.
                            Nenhum valor será cobrado.
                        </p>

                        <?php if ($estoqueValido): ?>

                            <button type="submit"
                                    class="botao-chronos confirmar-compra">

                                CONFIRMAR PEDIDO

                            </button>

                        <?php else: ?>

                            <p class="text-danger">
                                Existem produtos indisponíveis.
                                Atualize sua sacola.
                            </p>

                        <?php endif; ?>

                    </form>

                </div>

                <a href="sacola.php"
                   class="voltar-colecao mt-4">

                    ← Voltar à sacola

                </a>

            </div>

            <div class="col-lg-5">

                <aside class="resumo-checkout">

                    <span class="legenda">
                        SUA SELEÇÃO
                    </span>

                    <h2>Resumo do pedido</h2>

                    <?php foreach ($itens as $item): ?>

                        <div class="produto-checkout">

                            <div class="imagem-checkout">

                                <img
                                    src="../imagens/<?= htmlspecialchars($item['imagem']) ?>"
                                    alt="<?= htmlspecialchars($item['modelo']) ?>"
                                >

                            </div>

                            <div>

                                <h3>
                                    <?= htmlspecialchars($item['marca']) ?>
                                </h3>

                                <p>
                                    <?= htmlspecialchars($item['modelo']) ?>
                                </p>

                                <small>
                                    Quantidade: <?= $item['quantidade'] ?>
                                </small>

                                <p class="valor-checkout">

                                    R$ <?= number_format(
                                        $item['subtotal'],
                                        2,
                                        ',',
                                        '.'
                                    ) ?>

                                </p>

                            </div>

                        </div>

                    <?php endforeach; ?>

                    <div class="divisao-bronze"></div>

                    <div class="total-sacola">

                        <span>Valor total</span>

                        <strong>

                            R$ <?= number_format($total, 2, ',', '.') ?>

                        </strong>

                    </div>

                </aside>

            </div>

        </div>

    <?php endif; ?>

</main>

<?php require __DIR__ . '/../includes/rodape.php'; ?>