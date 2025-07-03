<?php
session_start();
require __DIR__.'/conexao.php';
require __DIR__.'/config_appproducao.php';

$usuario = htmlspecialchars($_SESSION['usuario'], ENT_QUOTES, 'UTF-8');
$nivel   = $_SESSION['nivel_acesso'];

$sql = "
  SELECT h.id, h.data_referencia, h.ts_registro,
         s.nome AS setor, g.nome AS galpao, r.nome AS raca,
         h.semana, h.qtde_galinhas, h.qtde_mortes,
         h.vitalidade, h.produtividade, h.qtde_ovos,
         h.quem_registrou, h.observacoes
    FROM historico_ovos h
    JOIN setores s ON h.setor_id = s.id
    JOIN galpoes g ON h.galpao_id = g.id
    JOIN racas r ON h.raca_id = r.id
   ORDER BY h.ts_registro DESC
";
$stmt = $pdoApp->query($sql);
$relatorios = $stmt->fetchAll(PDO::FETCH_ASSOC);
// $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Relatórios</title>
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
<body class="production-app">

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

  <div class="app-container">
    <h1>Histórico de Relatórios de Ovos</h1>
    <!-- <a href="csv_export.php" class="button">Download CSV</a> -->
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Data Ref.</th>
            <th>Registro</th>
            <th>Setor</th>
            <th>Galpão</th>
            <th>Raça</th>
            <th>Semana</th>
            <th>Galinhas</th>
            <th>Mortes</th>
            <th>Vitalidade</th>
            <th>Produtividade</th>
            <th>Ovos</th>
            <th>Quem</th>
            <th>Obs.</th>
            <th>PDF</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($relatorios as $r): ?>
          <tr>
            <td><?=$r['id']?></td>
            <td><?=$r['data_referencia']?></td>
            <td><?=$r['ts_registro']?></td>
            <td><?=htmlspecialchars($r['setor'])?></td>
            <td><?=htmlspecialchars($r['galpao'])?></td>
            <td><?=htmlspecialchars($r['raca'])?></td>
            <td><?=$r['semana']?></td>
            <td><?=$r['qtde_galinhas']?></td>
            <td><?=$r['qtde_mortes']?></td>
            <td><?=number_format(($r['qtde_galinhas'] / ($r['qtde_galinhas'] + $r['qtde_mortes']))*100,2,',','.')?>%</td>
            <td><?=number_format(($r['qtde_ovos'] / $r['qtde_galinhas'])*100,2,',','.')?>%</td>
            <td><?=$r['qtde_ovos']?></td>
            <td><?=htmlspecialchars($r['quem_registrou'])?></td>
            <td><?=htmlspecialchars($r['observacoes'])?></td>
            <td><a href="pdf_relatorio.php?id=<?= $r['id'] ?>" class="botao_pdf">Gerar PDF</a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
  </div>
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