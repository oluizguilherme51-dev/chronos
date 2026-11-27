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

if (empty($_SESSION['token_gestao'])) {
    $_SESSION['token_gestao'] = bin2hex(random_bytes(32));
}

$mensagem = $_SESSION['mensagem_gestao'] ?? '';
unset($_SESSION['mensagem_gestao']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!hash_equals(
        $_SESSION['token_gestao'],
        $_POST['token'] ?? ''
    )) {
        exit('Solicitação inválida.');
    }

    $id = (int) ($_POST['id'] ?? 0);

    if (isset($_POST['excluir'])) {

        try {

            $consulta = $pdo->prepare(
                "DELETE FROM relogios WHERE id = ?"
            );

            $consulta->execute([$id]);

            $mensagem = 'Relógio excluído com sucesso.';

        } catch (PDOException $erro) {

            $mensagem = 'Não foi possível excluir a peça. Ela pode estar vinculada a uma venda.';
        }

    } elseif (isset($_POST['salvar'])) {

        $marca = trim($_POST['marca'] ?? '');
        $modelo = trim($_POST['modelo'] ?? '');
        $referencia = trim($_POST['referencia'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');
        $colecao = (int) ($_POST['colecao_id'] ?? 0);
        $preco = $_POST['preco'] ?? '';
        $estoque = filter_var(
            $_POST['estoque'] ?? '',
            FILTER_VALIDATE_INT
        );

        $imagem = trim($_POST['imagem'] ?? '');

        if (
            $marca === '' ||
            $modelo === '' ||
            $referencia === '' ||
            $descricao === '' ||
            $colecao <= 0 ||
            !is_numeric($preco) ||
            $preco <= 0 ||
            $estoque === false ||
            $estoque < 0 ||
            $imagem === '' ||
            basename($imagem) !== $imagem ||
            !is_file(__DIR__ . '/../imagens/' . $imagem)
        ) {

            $mensagem = 'Confira todos os campos e o nome da imagem.';

        } else {

            try {

                if ($id > 0) {

                    $consulta = $pdo->prepare(
                        "UPDATE relogios SET
                            colecao_id = ?,
                            marca = ?,
                            modelo = ?,
                            referencia = ?,
                            descricao = ?,
                            preco = ?,
                            estoque = ?,
                            imagem = ?
                         WHERE id = ?"
                    );

                    $consulta->execute([
                        $colecao,
                        $marca,
                        $modelo,
                        $referencia,
                        $descricao,
                        $preco,
                        $estoque,
                        $imagem,
                        $id
                    ]);

                    $mensagem = 'Relógio atualizado com sucesso.';

                } else {

                    $consulta = $pdo->prepare(
                        "INSERT INTO relogios
                        (colecao_id, marca, modelo, referencia,
                         descricao, preco, estoque, imagem)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                    );

                    $consulta->execute([
                        $colecao,
                        $marca,
                        $modelo,
                        $referencia,
                        $descricao,
                        $preco,
                        $estoque,
                        $imagem
                    ]);

                    $mensagem = 'Relógio cadastrado com sucesso.';
                }

            } catch (PDOException $erro) {

                $mensagem = 'Não foi possível salvar o relógio.';
                error_log($erro->getMessage());
            }
        }
    }

    $_SESSION['mensagem_gestao'] = $mensagem;

    header('Location: relogios.php');
    exit;
}

$colecoes = $pdo->query(
    "SELECT * FROM colecoes ORDER BY nome"
)->fetchAll();

$relogios = $pdo->query(
    "SELECT relogios.*, colecoes.nome AS nome_colecao
     FROM relogios
     INNER JOIN colecoes
     ON relogios.colecao_id = colecoes.id
     ORDER BY relogios.id DESC"
)->fetchAll();

$edicao = null;

if (isset($_GET['editar'])) {

    $consulta = $pdo->prepare(
        "SELECT * FROM relogios WHERE id = ?"
    );

    $consulta->execute([
        (int) $_GET['editar']
    ]);

    $edicao = $consulta->fetch();
}

require __DIR__ . '/../includes/cabecalho.php';
?>

<main class="container pagina-gestao">

    <a href="index.php" class="voltar-colecao">
        ← Voltar ao painel
    </a>

    <div class="titulo-gestao">

        <span class="legenda">
            ADMINISTRAÇÃO DA COLEÇÃO
        </span>

        <h1>Gerenciar relógios</h1>

        <p>
            Cadastre e organize as peças da Chronos.
        </p>

    </div>

    <?php if ($mensagem): ?>

        <div class="mensagem-sacola">
            <?= htmlspecialchars($mensagem) ?>
        </div>

    <?php endif; ?>

    <section class="formulario-gestao">

        <span class="legenda">
            COLEÇÃO CHRONOS
        </span>

        <h2>
            <?= $edicao ? 'Editar relógio' : 'Cadastrar relógio' ?>
        </h2>

        <form method="POST" class="row g-3">

            <input type="hidden"
                   name="token"
                   value="<?= htmlspecialchars($_SESSION['token_gestao']) ?>">

            <input type="hidden"
                   name="id"
                   value="<?= $edicao['id'] ?? 0 ?>">

            <div class="col-md-6">

                <label>Marca</label>

                <input type="text"
                       name="marca"
                       maxlength="100"
                       value="<?= htmlspecialchars($edicao['marca'] ?? '') ?>"
                       required>

            </div>

            <div class="col-md-6">

                <label>Modelo</label>

                <input type="text"
                       name="modelo"
                       maxlength="150"
                       value="<?= htmlspecialchars($edicao['modelo'] ?? '') ?>"
                       required>

            </div>

            <div class="col-md-6">

                <label>Referência</label>

                <input type="text"
                       name="referencia"
                       maxlength="100"
                       value="<?= htmlspecialchars($edicao['referencia'] ?? '') ?>"
                       required>

            </div>

            <div class="col-md-6">

                <label>Coleção</label>

                <select name="colecao_id" required>

                    <option value="">
                        Selecione
                    </option>

                    <?php foreach ($colecoes as $colecao): ?>

                        <option
                            value="<?= $colecao['id'] ?>"
                            <?= ($edicao['colecao_id'] ?? 0) == $colecao['id'] ? 'selected' : '' ?>
                        >

                            <?= htmlspecialchars($colecao['nome']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="col-md-6">

                <label>Preço (R$)</label>

                <input type="number"
                       name="preco"
                       min="0.01"
                       step="0.01"
                       value="<?= htmlspecialchars($edicao['preco'] ?? '') ?>"
                       required>

            </div>

            <div class="col-md-6">

                <label>Estoque</label>

                <input type="number"
                       name="estoque"
                       min="0"
                       value="<?= htmlspecialchars($edicao['estoque'] ?? '') ?>"
                       required>

            </div>

            <div class="col-12">

                <label>Nome do arquivo da imagem</label>

                <input type="text"
                       name="imagem"
                       maxlength="255"
                       placeholder="Exemplo: datejust.jpg"
                       value="<?= htmlspecialchars($edicao['imagem'] ?? '') ?>"
                       required>

                <small>
                    A imagem deve estar na pasta imagens.
                </small>

            </div>

            <div class="col-12">

                <label>Descrição</label>

                <textarea name="descricao"
                          rows="4"
                          required><?= htmlspecialchars($edicao['descricao'] ?? '') ?></textarea>

            </div>

            <div class="col-12">

                <button type="submit"
                        name="salvar"
                        class="botao-chronos">

                    <?= $edicao ? 'SALVAR ALTERAÇÕES' : 'CADASTRAR RELÓGIO' ?>

                </button>

                <?php if ($edicao): ?>

                    <a href="relogios.php"
                       class="cancelar-gestao">

                        Cancelar edição

                    </a>

                <?php endif; ?>

            </div>

        </form>

    </section>

    <section class="listagem-gestao">

        <span class="legenda">
            INVENTÁRIO CHRONOS
        </span>

        <h2>Relógios cadastrados</h2>

        <p>
            <?= count($relogios) ?> peças cadastradas.
        </p>

        <div class="table-responsive">

            <table class="tabela-gestao">

                <thead>

                    <tr>
                        <th>Imagem</th>
                        <th>Relógio</th>
                        <th>Coleção</th>
                        <th>Preço</th>
                        <th>Estoque</th>
                        <th>Ações</th>
                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($relogios as $relogio): ?>

                        <tr>

                            <td>

                                <img
                                    class="imagem-gestao"
                                    src="../imagens/<?= htmlspecialchars($relogio['imagem']) ?>"
                                    alt="<?= htmlspecialchars($relogio['modelo']) ?>"
                                >

                            </td>

                            <td>

                                <strong>
                                    <?= htmlspecialchars($relogio['marca']) ?>
                                </strong>

                                <small>
                                    <?= htmlspecialchars($relogio['modelo']) ?>
                                </small>

                            </td>

                            <td>
                                <?= htmlspecialchars($relogio['nome_colecao']) ?>
                            </td>

                            <td>

                                R$ <?= number_format(
                                    $relogio['preco'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </td>

                            <td>
                                <?= $relogio['estoque'] ?>
                            </td>

                            <td>

                                <div class="acoes-gestao">

                                    <a
                                        href="?editar=<?= $relogio['id'] ?>"
                                        class="editar-gestao"
                                    >

                                        Editar

                                    </a>

                                    <form method="POST"
                                          onsubmit="return confirm('Deseja excluir este relógio?');">

                                        <input type="hidden"
                                               name="id"
                                               value="<?= $relogio['id'] ?>">

                                        <input type="hidden"
                                               name="token"
                                               value="<?= htmlspecialchars($_SESSION['token_gestao']) ?>">

                                        <button type="submit"
                                                name="excluir"
                                                class="excluir-gestao">

                                            Excluir

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </section>

</main>

<?php require __DIR__ . '/../includes/rodape.php'; ?>