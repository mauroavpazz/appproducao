<?php
require_once __DIR__ . '/session_inactivity.php';
require_once __DIR__ . '/conexao.php';

if (empty($_SESSION['user_id']) || $_SESSION['nivel_acesso'] !== 'admin') {
    header('Location: login.php?erro=Acesso negado');
    exit;
}

$stmt = $pdo->query("
  SELECT id, nome, usuario, email, criado_em
    FROM usuarios
   WHERE aprovado = 0
   ORDER BY criado_em DESC
");
$pendentes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <!-- ... -->
  <link rel="stylesheet" href="css/style.css">
</head>
<script>
  (function(){
    const logoutAfter = 5 * 60 * 1000; // 5 minutos em ms
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
  <div class="login-container admin">
    <h1>Administração do Sistema</h1>
    <div class="links">
      <a href="dashboard.php">← Voltar ao Dashboard</a>
    </div>

    <?php if (empty($pendentes)): ?>
      <p>Nenhum usuário pendente.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Nome completo</th>
            <th>Usuário</th>
            <th>E-mail</th>
            <th>Cadastrado em</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pendentes as $u): ?>
          <tr>
            <td><?= htmlspecialchars($u['nome'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($u['usuario'], ENT_QUOTES) ?></td>
            <td><?= htmlspecialchars($u['email'], ENT_QUOTES) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($u['criado_em'])) ?></td>
            <td>
              <form action="processa_aprovacao.php" method="POST">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <input type="hidden" name="acao" value="aprovar">
                <button type="submit">Aprovar</button>
              </form>
              <form action="processa_aprovacao.php" method="POST">
                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                <input type="hidden" name="acao" value="reprovar">
                <button type="submit" style="background-color:#b00020;">Reprovar</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
   <div id="logout-timer" class="logout-timer" >05:00</div>

  <script>
    (function(){
      const warningEl = document.getElementById('logout-timer');
      const maxTime     = 5 * 60;        
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
