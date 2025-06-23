<?php
require_once __DIR__ . '/session_inactivity.php';
require_once 'conexao.php';
require_once 'envia_email.php';

if (empty($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'admin') {
    header('Location: login.php?erro=Acesso negado');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'], $_POST['acao'])) {
    header('Location: painel_admin.php');
    exit;
}

$id   = (int) $_POST['id'];
$acao = $_POST['acao'];

$stmt = $pdo->prepare("SELECT nome, email FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: painel_admin.php');
    exit;
}

if ($acao === 'aprovar') {
    $pdo->prepare("UPDATE usuarios SET aprovado = 1 WHERE id = :id")
        ->execute(['id' => $id]);

    $assunto = 'Seu cadastro foi aprovado';
    $mensagem = "
      Olá {$user['nome']},<br><br>
      Seu cadastro no Sistema ERP foi aprovado.<br>
      <a href='http://localhost/seu_projeto/login.php'>Entrar no APPProdução</a><br><br>
      Equipe APPProdução
    ";
    enviarEmail($user['email'], $assunto, $mensagem);

} elseif ($acao === 'reprovar') {
    $pdo->prepare("DELETE FROM usuarios WHERE id = :id")
        ->execute(['id' => $id]);
}

header('Location: painel_admin.php');
exit;
