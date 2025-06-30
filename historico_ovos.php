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
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Histórico de Relatórios</title>
  <link rel="stylesheet" href="css/style.css">
</head>
  
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
    <a href="csv_export.php" class="button">Download PDF</a>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Data Ref.</th><th>Registro</th>
            <th>Setor</th><th>Galpão</th><th>Raça</th>
            <th>Semana</th><th>Galinhas</th><th>Mortes</th>
            <th>Vitalidade</th><th>Produtividade</th><th>Ovos</th>
            <th>Quem</th><th>Obs.</th>
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
            <td><?=($r['qtde_galinhas'] / ($r['qtde_galinhas'] + $r['qtde_mortes']))*100?>%</td>
            <td><?=($r['qtde_ovos'] / $r['qtde_galinhas'])*100?>%</td>
            <td><?=$r['qtde_ovos']?></td>
            <td><?=htmlspecialchars($r['quem_registrou'])?></td>
            <td><?=htmlspecialchars($r['observacoes'])?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    <nav class="nav-links">
      <a href="relatorio_ovos.php">← Voltar ao Registro</a>
    </nav>
  </div>
</body>
</html
