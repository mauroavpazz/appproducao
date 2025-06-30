<?php
session_start();
require_once 'conexao.php';  // define $pdoApp
// require __DIR__.'/conexao_erp.php';  // define $pdoErp
require 'config_appproducao.php';
// require 'config_erp.php';

// usa $pdoApp para inserir em historico_ovos
// e $pdoErp para inserir em relatorio_ovos

if(
    empty($_POST['data_referencia']) ||
    empty($_POST['setor_id']) ||
    empty($_POST['galpao_id']) ||
    empty($_POST['raca_id'])
) {
    header('Location: relatorio_ovos.php?erro=Preencha os campos obrigatórios');
    exit;
}

// Dados do form
$data        = $_POST['data_referencia'];
$setor_id    = $_POST['setor_id'];
$galpao_id   = $_POST['galpao_id'];
$raca_id     = $_POST['raca_id'];
$semana      = (int) $_POST['semana'];
$qtde_galinhas = (int) $_POST['qtde_galinhas'];
$qtde_mortes = (int) $_POST['qtde_mortes'];
$vitalidade  = (float) $_POST['vitalidade'];
$produtividade= (float) $_POST['produtividade'];
$qtde_ovos   = (int) $_POST['qtde_ovos'];
$quem        = $_SESSION['usuario'];
$obs         = $_POST['observacoes'] ?? '';

try {
    // Inicia transações
    $pdoApp->beginTransaction();
    // $pdoErp->beginTransaction();

    // 4.1 Insere em appproducao.historico_ovos
    $sql1 = "INSERT INTO historico_ovos
      (data_referencia, setor_id, galpao_id, raca_id,
       semana, qtde_galinhas, qtde_mortes, vitalidade,
       produtividade, qtde_ovos, quem_registrou, observacoes)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt1 = $pdoApp->prepare($sql1);
    $stmt1->execute([
      $data, $setor_id, $galpao_id, $raca_id,
      $semana, $qtde_galinhas, $qtde_mortes,
      $vitalidade, $produtividade, $qtde_ovos,
      $quem, $obs
    ]);

    // 4.2 Insere em erp.relatorio_ovos
    $sql2 = str_replace('historico_ovos','relatorio_ovos',$sql1);
    // $stmt2 = $pdoErp->prepare($sql2);
    // $stmt2->execute([
    //   $data, $setor_id, $galpao_id, $raca_id,
    //   $semana, $qtde_galinhas, $qtde_mortes,
    //   $vitalidade, $produtividade, $qtde_ovos,
    //   $quem, $obs
    // ]);

    // Commit se tudo ok
    $pdoApp->commit();
    // $pdoErp->commit();

    header('Location: relatorio_ovos.php?sucesso=Relatório registrado com sucesso');
    exit;
} catch(Exception $e) {
    // Rollback ambos
    $pdoApp->rollBack();
    // $pdoErp->rollBack();
    header('Location: relatorio_ovos.php?erro=' . rawurlencode($e->getMessage()));
    exit;
}