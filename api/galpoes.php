<?php

require __DIR__ . '/conexao.php';

header('Content-Type: application/json');
if (empty($_GET['setor_id'])) {
    echo '[]';
    exit;
}

$stmt = $pdoApp->prepare('
    SELECT id, nome
      FROM galpoes
     WHERE setor_id = ?
  ORDER BY nome
');
$stmt->execute([ $_GET['setor_id'] ]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
