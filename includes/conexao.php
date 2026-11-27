<?php

$ambiente = parse_ini_file(__DIR__ . '/../.env');

if ($ambiente === false) {
    die("Arquivo de configuração não encontrado.");
}

try {
    $pdo = new PDO(
        "mysql:host=" . $ambiente['DB_HOST'] .
        ";dbname=" . $ambiente['DB_NAME'] .
        ";charset=utf8mb4",
        $ambiente['DB_USER'],
        $ambiente['DB_PASS']
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $erro) {
    die("Não foi possível conectar ao banco de dados.");
}