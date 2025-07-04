<?php
$pdoApp = new PDO(
    'mysql:host=localhost;dbname=appproducao;charset=utf8mb4',
    'mauro.vasconcelos',
    'Mavp220*'
);
$pdoApp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
