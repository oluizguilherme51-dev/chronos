<?php
require_once __DIR__ . '/includes/conexao.php';

$pesquisa = trim($_GET['pesquisa'] ?? '');
$colecao = (int) ($_GET['colecao'] ?? 0);

$colecoes = $pdo->query(
    "SELECT * FROM colecoes ORDER BY id"
)->fetchAll();

$sql = "SELECT relogios.*, colecoes.nome AS nome_colecao
        FROM relogios
        INNER JOIN colecoes
        ON relogios.colecao_id = colecoes.id
        WHERE 1 = 1";

$parametros = [];

if ($pesquisa !== '') {
    $sql .= " AND (relogios.modelo LIKE ?
              OR relogios.marca LIKE ?
              OR relogios.referencia LIKE ?)";

    $parametros[] = "%$pesquisa%";
    $parametros[] = "%$pesquisa%";
    $parametros[] = "%$pesquisa%";
}

if ($colecao > 0) {
    $sql .= " AND relogios.colecao_id = ?";
    $parametros[] = $colecao;
}

$sql .= " ORDER BY relogios.id";

$consulta = $pdo->prepare($sql);
$consulta->execute($parametros);
$relogios = $consulta->fetchAll();

require __DIR__ . '/includes/cabecalho.php';
?>

<main>

    <section class="apresentacao">

        <span class="legenda">
            INSPIRADO NA ETERNIDADE
        </span>

        <h1>
            O tempo passa.
            <br>
            <em>O estilo permanece.</em>
        </h1>

        <div class="arco-principal">
            <img src="imagens/rolex1908.jpg"
                 alt="Rolex 1908 Platinum">
        </div>

        <a href="#colecao" class="link-editorial">
            Descubra a coleção ↓
        </a>

    </section>

    <section class="universo-chronos">

        <span class="legenda">
            O UNIVERSO CHRONOS
        </span>

        <h2>As quatro expressões do tempo.</h2>

        <div class="container">
            <div class="row g-4 justify-content-center">

                <?php
                $romanos = ['I', 'II', 'III', 'IV'];
                ?>

                <?php foreach ($colecoes as $i => $item): ?>

                    <div class="col-6 col-lg-3">

                        <a class="arco-colecao"
                           href="?colecao=<?= $item['id'] ?>#colecao">

                            <span class="numero-romano">
                                <?= $romanos[$i] ?? '' ?>
                            </span>

                            <h3>
                                <?= htmlspecialchars($item['nome']) ?>
                            </h3>

                            <p>
                                <?= htmlspecialchars($item['descricao']) ?>
                            </p>

                            <span>↗</span>

                        </a>

                    </div>

                <?php endforeach; ?>

            </div>
        </div>

    </section>

    <section class="catalogo container" id="colecao">

        <div class="titulo-catalogo">

            <span class="legenda">
                CURADORIA CHRONOS
            </span>

            <h2>Peças atemporais.</h2>

            <p>
                Uma seleção de ícones da alta relojoaria.
            </p>

        </div>

        <form method="GET" action="index.php#colecao"
              class="filtros">

            <input type="search"
                   name="pesquisa"
                   placeholder="Encontre seu relógio..."
                   value="<?= htmlspecialchars($pesquisa) ?>">

            <select name="colecao">

                <option value="0">
                    Todas as coleções
                </option>

                <?php foreach ($colecoes as $item): ?>

                    <option value="<?= $item['id'] ?>"
                        <?= $colecao == $item['id']
                            ? 'selected' : '' ?>>

                        <?= htmlspecialchars($item['nome']) ?>

                    </option>

                <?php endforeach; ?>

            </select>

            <button type="submit" class="botao-chronos">
                Buscar
            </button>

            <a href="index.php#colecao" class="limpar">
                Limpar
            </a>

        </form>

        <p class="resultado">
            <?= count($relogios) ?> peças encontradas
        </p>

        <div class="row g-5">

            <?php foreach ($relogios as $relogio): ?>

                <div class="col-6 col-lg-3">

                    <article class="peca">

                        <a href="colecao/detalhes.php?id=<?= $relogio['id'] ?>">

                            <div class="arco-produto">

                                <img
                                    src="imagens/<?= htmlspecialchars($relogio['imagem']) ?>"
                                    alt="<?= htmlspecialchars($relogio['modelo']) ?>"
                                    loading="lazy">

                            </div>

                        </a>

                        <span class="categoria-peca">
                            <?= htmlspecialchars($relogio['nome_colecao']) ?>
                        </span>

                        <h3>
                            <?= htmlspecialchars($relogio['marca']) ?>
                        </h3>

                        <p class="modelo">
                            <?= htmlspecialchars($relogio['modelo']) ?>
                        </p>

                        <p class="valor">
                            R$ <?= number_format(
                                $relogio['preco'], 2, ',', '.'
                            ) ?>
                        </p>

                        <p class="disponibilidade">

                            <?= $relogio['estoque'] > 0
                                ? 'Disponível'
                                : 'Indisponível' ?>

                        </p>

                        <a class="conhecer-peca"
                           href="colecao/detalhes.php?id=<?= $relogio['id'] ?>">

                            Conhecer peça ↗

                        </a>

                    </article>

                </div>

            <?php endforeach; ?>

            <?php if (empty($relogios)): ?>

                <p class="text-center">
                    Nenhum relógio encontrado.
                </p>

            <?php endif; ?>

        </div>

    </section>

    <section class="manifesto">

        <div class="mostrador">

            <span class="doze">XII</span>
            <span class="tres">III</span>
            <span class="seis">VI</span>
            <span class="nove">IX</span>

            <div class="ponteiro-hora"></div>
            <div class="ponteiro-minuto"></div>
            <div class="centro"></div>

        </div>

        <h2>Cada instante é único.</h2>

        <p>
            Mais do que medir o tempo,
            celebramos a arte de vivê-lo.
        </p>

        <span class="legenda">
            CHRONOS — A ARTE DO TEMPO
        </span>

    </section>

</main>

<?php require __DIR__ . '/includes/rodape.php'; ?>