<?php
require_once __DIR__ . '/session_inactivity.php';
require_once 'conexao.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
$nivel   = $_SESSION['nivel_acesso'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Dashboard – APPProdução</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<script>
  (function(){
    const logoutAfter = 40 * 60 * 1000; 
    let timer;

    function resetTimer() {
      clearTimeout(timer);
      timer = setTimeout(() => {
        window.location.href = 'logout.php';
      }, logoutAfter);
    }

    ['load','mousemove','mousedown','click','scroll','keypress']
      .forEach(evt => window.addEventListener(evt, resetTimer));

    resetTimer();
  })();
</script>
<body>
  <div class="header">
    <h1>Bem-vindo, <?= $usuario ?>!</h1>
    <p>Você está logado como <strong><?= $nivel ?></strong>.</p>
    <div class="links">
      <a href="dashboard.php">Dashboard</a>
      <?php if ($nivel === 'admin'): ?>
        <a href="painel_admin.php">Administração do Sistema</a>
      <?php endif; ?>
      <a href="logout.php">Sair</a>
    </div>
  </div>
  <div class="login-container dashboard">
    <nav class="nav-links">
      <a href="ovos.php">Produção de Ovos</a>
      <a href="frangocorte.php">Frango de Corte</a>
      <a href="frangoabatido.php">Abate de Frango</a>
      </nav>
  </div>
  <div id="logout-timer" class="logout-timer" >40:00</div>

  <script>
    (function(){
      const warningEl = document.getElementById('logout-timer');
      const maxTime     = 40 * 60;        
      let remaining     = maxTime;       
      let logoutTimer;                   
      let countdownTimer;                

      function startTimers() {
        clearTimeout(logoutTimer);
        clearInterval(countdownTimer);
        remaining = maxTime;
        renderTime();

        logoutTimer = setTimeout(() => {
          window.location.href = 'logout.php';
        }, maxTime * 1000);

        countdownTimer = setInterval(() => {
          remaining--;
          if (remaining <= 0) {
            clearInterval(countdownTimer);
          }
          renderTime();
        }, 1000);
      }

      function renderTime() {
        const min = String(Math.floor(remaining/60)).padStart(2,'0');
        const sec = String(remaining%60).padStart(2,'0');
        warningEl.textContent = `${min}:${sec}`;
      }

      ['load','mousemove','mousedown','click','scroll','keypress']
        .forEach(evt => window.addEventListener(evt, startTimers));

      startTimers();
    })();
  </script>
</body>
</html>
