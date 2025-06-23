<?php
require_once __DIR__ . '/session_inactivity.php';
require_once 'conexao.php';
require_once 'envia_email.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $stmt  = $pdo->prepare("SELECT id, nome, aprovado FROM usuarios WHERE email = :e LIMIT 1");
    $stmt->execute(['e' => $email]);
    $user  = $stmt->fetch();

    if (!$user) {
        $erro = 'E-mail não cadastrado';
    } elseif (!$user['aprovado']) {
        $erro = 'Usuário ainda não aprovado';
    } else {
        $token  = bin2hex(random_bytes(16));
        $expira = date('Y-m-d H:i:s', time() + 3600);
        $pdo->prepare("
          UPDATE usuarios
          SET token_recuperacao = :t, validade_token = :v
          WHERE id = :id
        ")->execute([
          't'  => $token,
          'v'  => $expira,
          'id' => $user['id']
        ]);

        $link    = "alteracoes.local/redefinir_senha.php?token={$token}";
        $assunto = 'Redefinição de senha';
        $mensagem = "
          Olá {$user['nome']},<br><br>
          Clique no link abaixo para redefinir sua senha (válido por 1 hora):<br>
          <a href='{$link}'>Redefinir minha senha</a><br><br>
          Se não solicitou, ignore este e-mail.<br><br>
          Equipe APPProdução
        ";
        enviarEmail($email, $assunto, $mensagem);
        $sucesso = 'Link de redefinição enviado ao seu e-mail.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Esqueci Minha Senha – APPProdução</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div class="login-container">
    <h1>Esqueci Minha Senha</h1>

    <?php if (!empty($erro)): ?>
      <div class="error-message"><?= $erro ?></div>
    <?php elseif (!empty($sucesso)): ?>
      <div class="success-message"><?= $sucesso ?></div>
    <?php endif; ?>

    <form action="esqueci_senha.php" method="POST">
      <div class="form-group">
        <label for="email">E-mail cadastrado</label>
        <input type="email" name="email" id="email" required>
      </div>

      <button type="submit">Enviar link</button>
    </form>

    <div class="links">
      <a href="login.php">← Voltar ao login</a>
    </div>
  </div>
</body>
</html>
