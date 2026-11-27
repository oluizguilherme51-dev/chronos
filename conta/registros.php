<?php
session_name('CHRONOS_SESSION');
session_start();

require_once "../includes/conexao.php";

$mensagem = "";

if (empty($_SESSION['token_conta'])) {
    $_SESSION['token_conta'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if (!hash_equals($_SESSION['token_conta'], $_POST['token'] ?? '')) {

        $mensagem = "Solicitação inválida.";

    } elseif ($nome === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem = "Preencha os dados corretamente.";

    } elseif (strlen($senha) < 8 || strlen($senha) > 72) {

        $mensagem = "A senha deve ter entre 8 e 72 bytes.";

    } elseif ($senha !== $confirmar) {

        $mensagem = "As senhas não coincidem.";

    } else {

        $consulta = $pdo->prepare(
            "SELECT id FROM usuarios WHERE email = ?"
        );

        $consulta->execute([$email]);

        if ($consulta->fetch()) {

            $mensagem = "Este e-mail já está cadastrado.";

        } else {

            $senhaHash = password_hash(
                $senha,
                PASSWORD_DEFAULT
            );

            try {

                $consulta = $pdo->prepare(
                    "INSERT INTO usuarios
                    (nome, email, senha, perfil)
                    VALUES (?, ?, ?, 'cliente')"
                );

                $consulta->execute([
                    $nome,
                    $email,
                    $senhaHash
                ]);

                header("Location: entrar.php?cadastro=sucesso");
                exit;

            } catch (PDOException $erro) {

                $mensagem = "Não foi possível realizar o cadastro.";

            }
        }
    }
}

require_once "../includes/cabecalho.php";
?>

<main class="container chronos-conta">

    <div class="chronos-titulo-conta">

        <span>O UNIVERSO CHRONOS</span>

        <h1>Crie sua conta</h1>

        <p>
            Descubra uma nova maneira de apreciar o tempo.
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

                <a href="entrar.php">Entrar</a>

                <a href="registros.php" class="ativa">
                    Cadastro
                </a>

            </div>

            <h2>Cadastro</h2>

            <p>Preencha seus dados para criar uma conta.</p>

            <?php if ($mensagem !== ''): ?>

                <div class="alert alert-danger">

                    <?= htmlspecialchars($mensagem) ?>

                </div>

            <?php endif; ?>

            <form method="POST">

                <input type="hidden"
                       name="token"
                       value="<?= htmlspecialchars($_SESSION['token_conta']) ?>">

                <label for="nome">Nome completo</label>

                <input type="text"
                       id="nome"
                       name="nome"
                       placeholder="Seu nome completo"
                       maxlength="100"
                       autocomplete="name"
                       required>

                <label for="email">E-mail</label>

                <input type="email"
                       id="email"
                       name="email"
                       placeholder="Seu e-mail"
                       maxlength="150"
                       autocomplete="email"
                       required>

                <label for="senha">Senha</label>

                <input type="password"
                       id="senha"
                       name="senha"
                       placeholder="Sua senha"
                       minlength="8"
                       maxlength="72"
                       autocomplete="new-password"
                       required>

                <label for="confirmar">Confirmar senha</label>

                <input type="password"
                       id="confirmar"
                       name="confirmar"
                       placeholder="Confirme sua senha"
                       minlength="8"
                       maxlength="72"
                       autocomplete="new-password"
                       required>

                <button type="submit"
                        class="chronos-botao-conta">

                    CRIAR CONTA

                </button>

            </form>

            <p class="chronos-link-conta">

                Já possui uma conta?

                <a href="entrar.php">Entre aqui</a>

            </p>

        </div>

    </div>

</main>

<?php require_once "../includes/rodape.php"; ?>
