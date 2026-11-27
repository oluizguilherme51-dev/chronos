<?php
session_name('CHRONOS_SESSION');
session_start();

require_once "../includes/conexao.php";

$mensagem = "";

if (empty($_SESSION['token_conta'])) {
    $_SESSION['token_conta'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!hash_equals($_SESSION['token_conta'], $_POST['token'] ?? '')) {

        $mensagem = "Solicitação inválida.";

    } else {

        $consulta = $conexao->prepare(
            "SELECT * FROM usuarios WHERE email = ?"
        );

        $consulta->execute([$email]);

        $usuario = $consulta->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['perfil'] = $usuario['perfil'];

            header("Location: ../index.php");
            exit;

        } else {

            $mensagem = "E-mail ou senha incorretos.";

        }
    }
}

require_once "../includes/cabecalho.php";
?>

<main class="container chronos-conta">

    <div class="chronos-titulo-conta">

        <span>O UNIVERSO CHRONOS</span>

        <h1>Bem-vindo ao Chronos</h1>

        <p>
            A excelência está presente em cada segundo.
        </p>

    </div>

    <div class="chronos-painel-conta">

        <div class="chronos-imagem-conta">

            <img src="../imagens/datejust.jpg"
                 alt="Relógio Rolex Datejust">

            <div class="chronos-legenda-conta">

                <span>HAUTE HORLOGERIE</span>

                <h2>O tempo é a expressão da elegância.</h2>

            </div>

        </div>

        <div class="chronos-form-conta">

            <div class="chronos-abas-conta">

                <a href="entrar.php" class="ativa">
                    Entrar
                </a>

                <a href="registros.php">Cadastro</a>

            </div>

            <h2>Acesse sua conta</h2>

            <p>
                Entre para continuar sua experiência.
            </p>

            <?php if (($_GET['cadastro'] ?? '') === 'sucesso'): ?>

                <div class="alert alert-success">

                    Cadastro realizado com sucesso!

                </div>

            <?php endif; ?>

            <?php if ($mensagem !== ''): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($mensagem) ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <input type="hidden"
                       name="token"
                       value="<?= htmlspecialchars($_SESSION['token_conta']) ?>">

                <label for="email">E-mail</label>

                <input type="email"
                       id="email"
                       name="email"
                       placeholder="Seu e-mail"
                       autocomplete="email"
                       required>

                <label for="senha">Senha</label>

                <input type="password"
                       id="senha"
                       name="senha"
                       placeholder="Sua senha"
                       autocomplete="current-password"
                       required>

                <button type="submit"
                        class="chronos-botao-conta">

                    ENTRAR

                </button>

            </form>

            <p class="chronos-link-conta">

                Ainda não possui uma conta?

                <a href="registros.php">Cadastre-se</a>

            </p>

        </div>

    </div>

</main>

<?php require_once "../includes/rodape.php"; ?>