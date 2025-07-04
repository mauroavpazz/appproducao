<?php
require __DIR__ . '/conexao.php';
header('Content-Type: application/json');
if (empty($_GET['galpao_id'])) {
    echo '[]';
    exit;
}
$stmt = $pdoApp->prepare('
    SELECT id, nome
      FROM racas
     WHERE galpao_id = ?
  ORDER BY nome
');
$stmt->execute([ $_GET['galpao_id'] ]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
