<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config_appproducao.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// recebe o ID pelo GET
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die('ID de relatório inválido');
}

// pegando os dados do relatório
$stmt = $pdoApp->prepare("
  SELECT h.*, s.nome AS setor, g.nome AS galpao, r.nome AS raca
  FROM historico_ovos h
  JOIN setores s ON h.setor_id = s.id
  JOIN galpoes g ON h.galpao_id = g.id
  JOIN racas r   ON h.raca_id   = r.id
  WHERE h.id = :id
  LIMIT 1
");
$stmt->execute(['id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    die('Relatório não encontrado');
}

// carrega o template em buffer
ob_start();
include __DIR__ . '/templates/relatorio_pdf.php';
$html = ob_get_clean();

// configura e gera o PDF
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// envia ao navegador para download do próprio pdf
$pdfName = "relatorio_ovos_{$id}.pdf";
header('Content-Type: application/pdf');
header("Content-Disposition: attachment; filename=\"{$pdfName}\"");
echo $dompdf->output();
exit;
