<?php
session_start();
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Painel Admin – Sistema ERP</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="login-container admin">
    <h1>Administração do Sistema</h1>
    <div class="links">
      <a href="dashboard.php">← Voltar ao Dashboard</a>
    </div>

    <?php if (empty($pendentes)): ?>
      <p>Nenhum usuário pendente de aprovação.</p>
    <?php else: ?>
      <div class="table-wrapper">
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
                <form action="processa_aprovacao.php" method="POST"
                      onsubmit="event.preventDefault(); openConfirm(this, 'Você tem certeza que deseja aprovar este usuário?');">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <input type="hidden" name="acao" value="aprovar">
                  <button type="submit">Aprovar</button>
                </form>
                <form action="processa_aprovacao.php" method="POST"
                      onsubmit="event.preventDefault(); openConfirm(this, 'Você tem certeza que deseja reprovar este usuário?');">
                  <input type="hidden" name="id" value="<?= $u['id'] ?>">
                  <input type="hidden" name="acao" value="reprovar">
                  <button type="submit" style="background-color:#b00020;">Reprovar</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Modal de confirmação -->
  <div class="modal-overlay" id="confirmModal">
    <div class="modal">
      <h2>Confirmação</h2>
      <p id="modalMessage">Você tem certeza?</p>
      <button class="btn-confirm" id="modalYes">Sim</button>
      <button class="btn-cancel" id="modalNo">Não</button>
    </div>
  </div>

  <!-- Script de confirmação -->
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      const modal      = document.getElementById('confirmModal');
      const msgEl      = document.getElementById('modalMessage');
      const yesBtn     = document.getElementById('modalYes');
      const noBtn      = document.getElementById('modalNo');
      let currentForm  = null;

      window.openConfirm = function(form, message) {
        currentForm = form;
        msgEl.textContent = message;
        modal.style.display = 'flex';
      };

      yesBtn.addEventListener('click', function(){
        modal.style.display = 'none';
        if (currentForm) currentForm.submit();
      });

      noBtn.addEventListener('click', function(){
        modal.style.display = 'none';
      });
    });
  </script>
</body>
</html>
''