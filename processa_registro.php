<?php
require_once __DIR__ . '/conexao.php';
require_once __DIR__ . '/envia_email.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}

$nome    = trim($_POST['nome']);
$usuario = trim($_POST['usuario']);
$email   = trim($_POST['email']);
$senha   = $_POST['senha'];
$conf    = $_POST['confirma_senha'];

if ($senha !== $conf) {
    header('Location: registro.php?erro=As senhas não conferem');
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :u OR email = :e");
$stmt->execute(['u' => $usuario, 'e' => $email]);
if ($stmt->fetch()) {
    header('Location: registro.php?erro=Usuário ou e-mail já cadastrado');
    exit;
}

$hash = password_hash($senha, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
  INSERT INTO usuarios (nome, usuario, email, senha, aprovado, nivel_acesso)
  VALUES (:n, :u, :e, :s, 0, 'usuario')
");
$stmt->execute([
  'n' => $nome,
  'u' => $usuario,
  'e' => $email,
  's' => $hash
]);

$assunto = 'Confirmação de cadastro';
$mensagem = "
  Olá {$nome},<br><br>
  Seu cadastro foi recebido com sucesso e está aguardando aprovação do administrador.<br>
  Assim que for aprovado, você receberá outro e-mail.<br><br>
  Atenciosamente,<br>
  Equipe APPProdução
";
enviarEmail($email, $assunto, $mensagem);

$emailAdmin = 'mauro@mavpaz.com.br'; 
$assuntoAdmin = 'Novo cadastro pendente';
$mensagemAdmin = "
  Novo usuário cadastrado:<br>
  - Nome: {$nome}<br>
  - Usuário: {$usuario}<br>
  - E-mail: {$email}<br><br>
  Acesse o painel de administração para aprovar ou reprovar.
";
enviarEmail($emailAdmin, $assuntoAdmin, $mensagemAdmin);

header('Location: registro.php?sucesso=Cadastro enviado! Aguarde aprovação.');
exit;