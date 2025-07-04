<?php
/** @var array $data Dados do relatório */
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="/templates/pdf_style.css">
</head>
<body>
  <h1>Relatório de Ovos</h1>
  <p><strong>ID:</strong> <?= $data['id'] ?> &nbsp; 
     <strong>Data:</strong> <?= $data['data_referencia'] ?> &nbsp; 
     <strong>Registrado em:</strong> <?= $data['ts_registro'] ?>
  </p>
    <div>
      <table>
        <thead>
          <tr>
            <th>Setor</th><th>Galpão</th><th>Raça</th>
            <th>Semana</th><th>Galinhas</th><th>Mortes</th>
            <th>Vitalidade (%)</th><th>Ovos</th><th>Produtividade (%)</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><?= htmlspecialchars($data['setor']) ?></td>
            <td><?= htmlspecialchars($data['galpao']) ?></td>
            <td><?= htmlspecialchars($data['raca']) ?></td>
            <td><?= $data['semana'] ?></td>
            <td><?= $data['qtde_galinhas'] ?></td>
            <td><?= $data['qtde_mortes'] ?></td>
            <td><?= number_format(($data['qtde_galinhas'] / ($data['qtde_galinhas'] + $data['qtde_mortes']))*100,2,',','.') ?></td>
            <td><?= $data['qtde_ovos'] ?></td>
            <td><?= number_format(($data['qtde_ovos'] / $data['qtde_galinhas'])*100,2,',','.') ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php if (!empty($data['observacoes'])): ?>
    <p><strong>Observações:</strong><br>
       <?= nl2br(htmlspecialchars($data['observacoes'])) ?></p>
  <?php endif; ?>
</body>
</html>