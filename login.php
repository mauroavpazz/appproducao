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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login – ERP</title>
  <meta name="description" content="Tela de login do ERP com design claro em azul e amarelo.">
  <link rel="stylesheet" href="css/style.css">

  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
</head>

<body>
  <main class="login-container" role="main" aria-labelledby="login-title">
    <header>
      <h1 id="login-title">APPProdução</h1>
    </header>

    <?php if (!empty($_GET['erro'])): ?>
      <div class="error-message" role="alert">
        <?= htmlspecialchars($_GET['erro'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <form action="verifica_login.php" method="POST" novalidate>
      <div class="form-group">
        <label for="login">Usuário ou E-mail</label>
        <input
          type="text"
          name="login"
          id="login"
          required
          placeholder="Digite seu usuário ou e-mail"
          autocomplete="username"
        >
      </div>

      <div class="form-group">
        <label for="senha">Senha</label>
        <div class="password-wrapper">
          <input
            type="password"
            name="senha"
            id="senha"
            required
            placeholder="••••••••"
            autocomplete="current-password"
          >
          <button
            type="button"
            class="toggle-password"
            id="togglePassword"
            aria-label="Mostrar ou ocultar senha"
          >
            <ion-icon name="eye-outline"></ion-icon>
          </button>
        </div>
      </div>

      <button type="submit">Entrar</button>
    </form>

    <footer class="links" role="contentinfo">
      <a href="registro.php">Cadastrar-se</a>
      <span aria-hidden="true">|</span>
      <a href="esqueci_senha.php">Esqueci minha senha</a>
    </footer>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const pwdInput  = document.getElementById('senha');
      const toggleBtn = document.getElementById('togglePassword');
      const icon      = toggleBtn.querySelector('ion-icon');

      toggleBtn.addEventListener('click', () => {
        const hidden = pwdInput.type === 'password';
        pwdInput.type = hidden ? 'text' : 'password';
        icon.setAttribute('name', hidden ? 'eye-off-outline' : 'eye-outline');
      });
    });
  </script>
</body>
</html>
