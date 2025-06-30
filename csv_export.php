<?php
require __DIR__.'/conexao.php';

$pdoApp = new PDO(
  'mysql:host=127.0.0.1;dbname=appproducao;charset=utf8mb4',
  'mauro.vasconcelos','Mavp220*',
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
);

$sql = "SELECT * FROM historico_ovos"; 
$stmt = $pdoApp->query($sql);

// Cabeçalhos para download
header('Content-Type: text/pdf; charset=UTF-8');
header('Content-Disposition: attachment; filename="historico_ovos_'.date('Ymd_His').'.pdf"');

$out = fopen('php://output','w');
// Cabeçalho pdf
fputcsv($out, ['ID','Data Ref.','Registro','Setor','Galpão','Raça','Semana',
               'Galinhas','Mortes','Vitalidade','Produtividade','Ovos','Quem','Obs']);

// Dados
while($row = $stmt->fetch(PDO::FETCH_NUM)) {
    fputcsv($out, $row);
}
fclose($out);
exit;