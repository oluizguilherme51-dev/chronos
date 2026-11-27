<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('CHRONOS_SESSION');
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Chronos | A arte do tempo</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

   <link rel="stylesheet" href="/chronos/estilo.css?v=2">
</head>

<body>

<div class="faixa-superior">
    A ARTE DE MEDIR O TEMPO — MMXXVI
</div>

<header class="cabecalho">
    <nav class="navegacao">

        <div class="menu-esquerdo">
            <a href="/chronos/index.php">Início</a>
            <a href="/chronos/index.php#colecao">Coleções</a>
        </div>

        <a class="identidade" href="/chronos/index.php">
            <div class="logotipo">
                CHR<span class="relogio-logo"></span>NOS
            </div>
            <span class="subtitulo-logo">
                HAUTE HORLOGERIE
            </span>
        </a>

        <div class="menu-direito">

            <?php if (isset($_SESSION['usuario_id'])): ?>

                <?php if ($_SESSION['perfil'] === 'admin'): ?>
                    <a href="/chronos/gestao/index.php">Gestão</a>
                <?php endif; ?>

                <a href="/chronos/conta/sair.php">Sair</a>

            <?php else: ?>

                <a href="/chronos/conta/entrar.php">Entrar</a>
                <a href="/chronos/conta/registros.php">Cadastro</a>

            <?php endif; ?>

            <a href="/chronos/compras/sacola.php">Sacola</a>

        </div>

    </nav>
</header>