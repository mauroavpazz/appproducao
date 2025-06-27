<?php
require __DIR__.'/conexao.php';

// Query igual ao histórico
$sql =   /* mesmo SELECT de historico_ovos.php acima */
$stmt = $pdoApp->query($sql);

// Cabeçalhos para download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="historico_ovos_'.date('Ymd_His').'.csv"');

$out = fopen('php://output','w');
// Cabeçalho CSV
fputcsv($out, ['ID','Data Ref.','Registro','Setor','Galpão','Raça','Semana',
               'Galinhas','Mortes','Vitalidade','Produtividade','Ovos','Quem','Obs']);

// Dados
while($row = $stmt->fetch(PDO::FETCH_NUM)) {
    fputcsv($out, $row);
}
fclose($out);
exit;