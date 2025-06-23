<?php
require_once __DIR__ . '/session_inactivity.php';
require_once 'conexao.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Cadastro - Sistema ERP</title>
  <link rel="stylesheet" href="css/style.css">
  <!-- Ionicons for eye icon -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
  <div class="login-container">
    <h1>Cadastro</h1>

    <?php if (!empty($_GET['erro'])): ?>
      <div class="error-message">
        <?= htmlspecialchars($_GET['erro'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php elseif (!empty($_GET['sucesso'])): ?>
      <div class="success-message">
        <?= htmlspecialchars($_GET['sucesso'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form action="processa_registro.php" method="POST">
      <div class="form-group">
        <label for="nome">Nome completo</label>
        <input type="text" name="nome" id="nome" required>
      </div>

      <div class="form-group">
        <label for="usuario">Usuário</label>
        <input type="text" name="usuario" id="usuario" required>
      </div>

      <div class="form-group">
        <label for="email">E-mail</label>
        <input type="email" name="email" id="email" required>
      </div>

      <div class="form-group password-wrapper">
        <label for="senha">Senha</label>
        <input type="password" name="senha" id="senha" required>
        <button type="button" class="toggle-password" data-target="senha">
          <ion-icon name="eye-off-outline"></ion-icon>
        </button>
      </div>

      <div class="form-group password-wrapper">
        <label for="confirma_senha">Confirmar senha</label>
        <input type="password" name="confirma_senha" id="confirma_senha" required>
        <button type="button" class="toggle-password" data-target="confirma_senha">
          <ion-icon name="eye-off-outline"></ion-icon>
        </button>
      </div>

      <button type="submit">Registrar</button>
    </form>

    <div class="links">
      <a href="login.php">Voltar ao login</a>
    </div>
  </div>

  <script>
    document.querySelectorAll('.toggle-password').forEach(btn => {
      btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const isPwd = input.getAttribute('type') === 'password';
        input.setAttribute('type', isPwd ? 'text' : 'password');
        btn.querySelector('ion-icon').setAttribute(
          'name',
          isPwd ? 'eye-outline' : 'eye-off-outline'
        );
      });
    });
  </script>
</body>
</html>
