<?php
require_once __DIR__ . '/session_inactivity.php';
require_once __DIR__ . '/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$login = trim($_POST['login']);
$senha = $_POST['senha'];

try {
    $sql = "
      SELECT id, usuario, email, senha, aprovado, nivel_acesso
        FROM usuarios
       WHERE usuario = ?
          OR email   = ?
       LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$login, $login]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    die("Erro ao buscar usuário: " . $e->getMessage());
}

if (!$user || !password_verify($senha, $user['senha'])) {
    header('Location: login.php?erro=Credenciais inválidas');
    exit;
}

if (!$user['aprovado']) {
    header('Location: login.php?erro=Usuário não aprovado');
    exit;
}

$_SESSION['user_id']      = $user['id'];
$_SESSION['usuario']      = $user['usuario'];
$_SESSION['nivel_acesso'] = $user['nivel_acesso'];

header('Location: dashboard.php');
exit;
    