<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config_appproducao.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die('ID de relatório inválido');
}

$sql = "
  SELECT 
    h.id, 
    h.data_referencia, 
    h.ts_registro,
    s.nome    AS setor, 
    g.nome    AS galpao, 
    r.nome    AS raca,
    h.semana, 
    h.qtde_galinhas, 
    h.qtde_mortes,
    h.vitalidade, 
    h.produtividade, 
    h.qtde_ovos,
    h.quem_registrou, 
    h.observacoes
  FROM historico_ovos h
  JOIN setores s ON h.setor_id = s.id
  JOIN galpoes g ON h.galpao_id  = g.id
  JOIN racas   r ON h.raca_id    = r.id
  WHERE h.id = :id
  LIMIT 1
";

$stmt = $pdoApp->prepare($sql);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    die('Relatório não encontrado');
}

$css = file_get_contents(__DIR__ . '/templates/pdf_style.css');

ob_start();
include __DIR__ . '/templates/relatorio_pdf.php';
$html = ob_get_clean();

$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);
$dompdf->loadHtml("<style>{$css}</style>" . $html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

header('Content-Type: application/pdf');
header("Content-Disposition: attachment; filename=\"relatorio_ovos_{$id}.pdf\"");
echo $dompdf->output();
exit;
