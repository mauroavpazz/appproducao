<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config_appproducao.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 1) Recebe e valida o ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die('ID de relatório inválido');
}

// 2) Monta a query COM UM único placeholder :id
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

// 3) Prepara e vincula EXPLICITAMENTE
$stmt = $pdoApp->prepare($sql);
// bindValue garante que o nome do parâmetro (:id) exista e seja INT
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

// 4) Executa e busca
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$data) {
    die('Relatório não encontrado');
}

// 5) Carrega CSS e template, gera PDF (conforme já tinhamos)
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
