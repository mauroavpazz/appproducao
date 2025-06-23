<?php
require_once __DIR__ . '/session_inactivity.php';
require_once 'conexao.php';

$token      = $_REQUEST['token'] ?? '';
$mostrarForm = false;
$erro       = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $token) {
    $stmt = $pdo->prepare("
      SELECT id, nome FROM usuarios
      WHERE token_recuperacao = :t
        AND validade_token >= NOW()
      LIMIT 1
    ");
    $stmt->execute(['t' => $token]);
    if ($stmt->fetch()) {
        $mostrarForm = true;
    } else {
        $erro = 'Link inválido ou expirado.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $senha = $_POST['senha'];
    $conf  = $_POST['confirma_senha'];

    if ($senha !== $conf) {
        $erro        = 'As senhas não conferem.';
        $mostrarForm = true;
    } else {
        $stmt = $pdo->prepare("
          SELECT id FROM usuarios
          WHERE token_recuperacao = :t
            AND validade_token >= NOW()
          LIMIT 1
        ");
        $stmt->execute(['t' => $token]);
        $user = $stmt->fetch();

        if (!$user) {
            $erro = 'Link inválido ou expirado.';
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);
            $pdo->prepare("
              UPDATE usuarios
              SET senha = :s, token_recuperacao = NULL, validade_token = NULL
              WHERE id = :id
            ")->execute([
              's'  => $hash,
              'id' => $user['id']
            ]);

            header('Location: login.php?sucesso=Senha redefinida com sucesso');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Redefinir Senha – Sistema ERP</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <div class="login-container">
    <h1>Redefinir Senha</h1>

    <?php if ($erro): ?>
      <div class="error-message"><?= $erro ?></div>
    <?php endif; ?>

    <?php if ($mostrarForm): ?>
      <form action="redefinir_senha.php" method="POST">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES) ?>">
        <label for="senha">Nova senha</label>
        <input type="password" name="senha" id="senha" required>
        <label for="confirma_senha">Confirmar nova senha</label>
        <input type="password" name="confirma_senha" id="confirma_senha" required>
        <button type="submit">Redefinir</button>
      </form>
    <?php else: ?>
      <div class="links">
        <a href="esqueci_senha.php">Solicitar novo link</a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
