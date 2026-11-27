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

$vendas = $pdo->query(
    "SELECT vendas.*, usuarios.nome AS cliente,
     usuarios.email AS email_cliente
     FROM vendas
     INNER JOIN usuarios
     ON vendas.usuario_id = usuarios.id
     ORDER BY vendas.id DESC"
)->fetchAll();

$totalVendas = count($vendas);
$totalFaturamento = 0;

foreach ($vendas as $venda) {
    $totalFaturamento += $venda['valor_total'];
}

require __DIR__ . '/../includes/cabecalho.php';
?>

<main class="container pagina-gestao">

    <a href="index.php" class="voltar-colecao">
        ← Voltar ao painel
    </a>

    <div class="titulo-gestao">

        <span class="legenda">
            ADMINISTRAÇÃO CHRONOS
        </span>

        <h1>Histórico de vendas</h1>

        <p>
            Acompanhe os pedidos realizados na Chronos.
        </p>

    </div>

    <div class="row g-4 mb-5">

        <div class="col-md-6">

            <div class="indicador-vendas">

                <span class="legenda">
                    PEDIDOS REALIZADOS
                </span>

                <h2><?= $totalVendas ?></h2>

            </div>

        </div>

        <div class="col-md-6">

            <div class="indicador-vendas">

                <span class="legenda">
                    FATURAMENTO SIMULADO
                </span>

                <h2>

                    R$ <?= number_format(
                        $totalFaturamento,
                        2,
                        ',',
                        '.'
                    ) ?>

                </h2>

            </div>

        </div>

    </div>

    <?php if (empty($vendas)): ?>

        <div class="sacola-vazia">

            <span class="ornamento">◇</span>

            <h2>Nenhuma venda registrada</h2>

            <p>
                Os pedidos realizados aparecerão aqui.
            </p>

        </div>

    <?php else: ?>

        <?php foreach ($vendas as $venda): ?>

            <?php

            $consulta = $pdo->prepare(
                "SELECT itens_venda.*, relogios.marca,
                 relogios.modelo, relogios.imagem
                 FROM itens_venda
                 INNER JOIN relogios
                 ON itens_venda.relogio_id = relogios.id
                 WHERE itens_venda.venda_id = ?"
            );

            $consulta->execute([$venda['id']]);

            $itens = $consulta->fetchAll();

            ?>

            <section class="cartao-venda">

                <div class="cabecalho-venda">

                    <div>

                        <span class="legenda">
                            PEDIDO CHRONOS
                        </span>

                        <h2>
                            Pedido #<?= $venda['id'] ?>
                        </h2>

                    </div>

                    <span class="data-venda">

                        <?= date(
                            'd/m/Y H:i',
                            strtotime($venda['data_venda'])
                        ) ?>

                    </span>

                </div>

                <div class="row g-3 dados-venda">

                    <div class="col-md-4">

                        <span>Cliente</span>

                        <strong>
                            <?= htmlspecialchars($venda['cliente']) ?>
                        </strong>

                        <small>
                            <?= htmlspecialchars($venda['email_cliente']) ?>
                        </small>

                    </div>

                    <div class="col-md-4">

                        <span>Forma de pagamento</span>

                        <strong>
                            <?= htmlspecialchars($venda['pagamento']) ?>
                        </strong>

                        <small>Pagamento simulado</small>

                    </div>

                    <div class="col-md-4">

                        <span>Valor total</span>

                        <strong>

                            R$ <?= number_format(
                                $venda['valor_total'],
                                2,
                                ',',
                                '.'
                            ) ?>

                        </strong>

                    </div>

                </div>

                <div class="table-responsive">

                    <table class="tabela-gestao">

                        <thead>

                            <tr>
                                <th>Imagem</th>
                                <th>Relógio</th>
                                <th>Quantidade</th>
                                <th>Valor unitário</th>
                                <th>Subtotal</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($itens as $item): ?>

                                <tr>

                                    <td>

                                        <img
                                            class="imagem-gestao"
                                            src="../imagens/<?= htmlspecialchars($item['imagem']) ?>"
                                            alt="<?= htmlspecialchars($item['modelo']) ?>"
                                        >

                                    </td>

                                    <td>

                                        <strong>
                                            <?= htmlspecialchars($item['marca']) ?>
                                        </strong>

                                        <small>
                                            <?= htmlspecialchars($item['modelo']) ?>
                                        </small>

                                    </td>

                                    <td>
                                        <?= $item['quantidade'] ?>
                                    </td>

                                    <td>

                                        R$ <?= number_format(
                                            $item['valor_unitario'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>

                                    <td>

                                        R$ <?= number_format(
                                            $item['valor_unitario'] *
                                            $item['quantidade'],
                                            2,
                                            ',',
                                            '.'
                                        ) ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        <?php endforeach; ?>

    <?php endif; ?>

</main>

<?php require __DIR__ . '/../includes/rodape.php'; ?>