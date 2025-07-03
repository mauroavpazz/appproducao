<?php
session_start();

// require __DIR__ . '/api/galpoes.php';
// require __DIR__ . '/api/racas.php';

$pdoApp = new PDO(
    'mysql:host=localhost;dbname=appproducao;charset=utf8mb4',
    'mauro.vasconcelos','Mavp220*'
);

if(empty($_SESSION['user_id'])){
    header('Location: login_app.php'); exit;
}

$usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
$nivel   = $_SESSION['nivel_acesso'];

$stmt = $pdoApp->query('SELECT id,nome FROM setores ORDER BY nome');
$setores = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdoApp->query('SELECT id,nome FROM galpoes ORDER BY nome');
$galpoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdoApp->query('SELECT id,nome FROM racas ORDER BY nome');
$racas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Registrar Relatório de Ovos</title>
  <link rel="stylesheet" href="css/style.css">
  <a href="ovos.php" class="botao_voltar" ><img src="images/back-icon.svg" alt="voltar"></a>
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
  <div class="app-container">
    <h1 class="titulo_rrelatorio">Registro do Relatório de Ovos</h1>
    <?php if(!empty($_GET['erro'])): ?>
      <div class="error-message"><?=htmlspecialchars($_GET['erro'])?></div>
    <?php elseif(!empty($_GET['sucesso'])): ?>
      <div class="success-message"><?=htmlspecialchars($_GET['sucesso'])?></div>
    <?php endif; ?>

    <form class="formulario_relatorio" action="processa_relatorio.php" method="POST" onsubmit="event.preventDefault(); openConfirm(this, 'Você tem certeza que deseja registrar este relatório?');">
      <div class="form-group">
        <label for="data_referencia">Data de Referência:</label>
        <input type="date" id="data_referencia" name="data_referencia" required>
      </div>

      <div class="form-group">
        <label for="setor">Setor:</label>
        <select id="setor" name="setor_id" required>
          <option value="">Selecione um setor</option>
          <?php foreach($setores as $s): ?>
            <option value="<?=$s['id']?>"><?=htmlspecialchars($s['nome'])?></option>:
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="galpao">Galpão:</label>
        <select id="galpao" name="galpao_id" required>
          <option value="">Selecione o setor primeiro</option>
          <?php foreach($galpoes as $g): ?>
            <option value="<?=$g['id']?>"><?=htmlspecialchars($g['nome'])?></option>:
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="raca">Raça:</label>
        <select id="raca" name="raca_id" required>
          <option value="">Selecione o galpão primeiro</option>
          <?php foreach($racas as $r): ?>
            <option value="<?=$r['id']?>"><?=htmlspecialchars($r['nome'])?></option>:
          <?php endforeach; ?>
        </select>
      </div>
      
      <div class="form-group">
        <label for="semana">Semana:</label>
        <input type="number" id="semana" name="semana" min="1" required>
      </div>

      <div class="form-group">
        <label for="qtde_galinhas">Quantidade de Galinhas:</label>
        <input type="number" id="qtde_galinhas" name="qtde_galinhas" min="0" required>
      </div>

      <div class="form-group">
        <label for="qtde_mortes">Quantidade de Mortes:</label>
        <input type="number" id="qtde_mortes" name="qtde_mortes" min="0" required>
      </div>

      <div class="form-group">
        <label for="qtde_ovos">Quantidade de Ovos:</label>
        <input type="number" id="qtde_ovos" name="qtde_ovos" min="0" required>
      </div>

      <div class="form-group">
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="3"></textarea>
      </div>
      
      <input type="hidden" name="id" value="<?= $u['id'] ?>">
      <input type="hidden" name="acao" value="aprovar">
      
      <button type="submit">Registrar Relatório</button>

    </form>
    </div>

    <div class="modal-overlay" id="confirmModal">
      <div class="modal">
        <h2>Confirmação</h2>
        <p id="modalMessage">Você tem certeza?</p>
        <button class="btn-confirm" id="modalYes">Sim</button>
        <button class="btn-cancel" id="modalNo">Não</button>
      </div>
    </div>
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
  <script>
    document.getElementById('setor').addEventListener('change', function(){
      const id = this.value;
      fetch('api/galpoes.php?setor_id='+id)
        .then(res=>res.json())
        .then(data=>{
          const sel = document.getElementById('galpao');
          sel.innerHTML = '<option value="">Selecione o galpão</option>';
          data.forEach(g=> sel.innerHTML += `<option value="${g.id}">${g.nome}</option>`);
          document.getElementById('raca').innerHTML = '<option value="">Selecione o galpão primeiro</option>';
        });
    });
    // Raças dependendo do galpão escolhido daquele setor
    document.getElementById('galpao').addEventListener('change', function(){
      const id = this.value;
      fetch('api/racas.php?galpao_id='+id)
        .then(res=>res.json())
        .then(data=>{
          const sel = document.getElementById('raca');
          sel.innerHTML = '<option value="">Selecione a raça</option>';
          data.forEach(r=> sel.innerHTML += `<option value="${r.id}">${r.nome}</option>`);
        });
    });
  </script>
  <div id="logout-timer" class="logout-timer" >40:00</div>

  <script>
    (function(){
      const warningEl = document.getElementById('logout-timer');
      const maxTime = 40 * 60;        
      let remaining = maxTime;       
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
</html