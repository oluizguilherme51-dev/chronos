<?php
session_name('CHRONOS_SESSION');
session_start();

require_once __DIR__ . '/../includes/conexao.php';

$id = (int) ($_GET['id'] ?? 0);

$consulta = $pdo->prepare(
    "SELECT relogios.*, colecoes.nome AS nome_colecao
     FROM relogios
     INNER JOIN colecoes
     ON relogios.colecao_id = colecoes.id
     WHERE relogios.id = ?"
);

$consulta->execute([$id]);

$relogio = $consulta->fetch();

if (!$relogio) {
    header("Location: ../index.php");
    exit;
}

if (empty($_SESSION['token_sacola'])) {
    $_SESSION['token_sacola'] = bin2hex(random_bytes(32));
}

require __DIR__ . '/../includes/cabecalho.php';
?>

<main class="container pagina-peca">

    <a href="../index.php#colecao" class="voltar-colecao">
        ← Retornar à coleção
    </a>

    <div class="row g-5 align-items-center">

        <div class="col-md-6">

            <div class="moldura-relogio">

                <img
                    src="../imagens/<?= htmlspecialchars($relogio['imagem']) ?>"
                    alt="<?= htmlspecialchars($relogio['modelo']) ?>">

            </div>

        </div>

        <div class="col-md-6 informacoes-peca">

            <span class="legenda">
                <?= htmlspecialchars($relogio['nome_colecao']) ?>
            </span>

            <h1>
                <?= htmlspecialchars($relogio['marca']) ?>
            </h1>

            <h2>
                <?= htmlspecialchars($relogio['modelo']) ?>
            </h2>

            <p class="referencia">
                Referência:
                <?= htmlspecialchars($relogio['referencia']) ?>
            </p>

            <div class="divisao-bronze"></div>

            <p class="descricao-relogio">
                <?= htmlspecialchars($relogio['descricao']) ?>
            </p>

            <p class="preco-relogio">
                R$ <?= number_format(
                    $relogio['preco'], 2, ',', '.'
                ) ?>
            </p>

            <?php if ($relogio['estoque'] > 0): ?>

                <p class="estoque-relogio">
                    Disponível — <?= $relogio['estoque'] ?>
                    unidade(s)
                </p>

                <form action="../compras/sacola.php"
                      method="POST">

                    <input type="hidden"
                           name="relogio_id"
                           value="<?= $relogio['id'] ?>">

                    <input type="hidden"
                           name="token"
                           value="<?= htmlspecialchars($_SESSION['token_sacola']) ?>">

                    <label for="quantidade">
                        Quantidade
                    </label>

                    <input type="number"
                           id="quantidade"
                           name="quantidade"
                           min="1"
                           max="<?= $relogio['estoque'] ?>"
                           value="1"
                           class="quantidade-relogio"
                           required>

                    <button type="submit"
                            name="adicionar"
                            class="botao-chronos botao-sacola">

                        Adicionar à sacola

                    </button>

                </form>

            <?php else: ?>

                <p class="text-danger">
                    Peça indisponível no momento.
                </p>

            <?php endif; ?>

            <p class="aviso-academico">
                Catálogo demonstrativo — projeto acadêmico.
            </p>

        </div>

    </div>

</main>

<?php require __DIR__ . '/../includes/rodape.php'; ?>