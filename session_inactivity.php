<?php

session_start();

$timeout = 40 * 60; 

if (
    isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $timeout
) {
    session_unset();
    session_destroy();
    header('Location: login.php?erro=Sessão expirada por inatividade');
    exit;
}

$_SESSION['last_activity'] = time();
