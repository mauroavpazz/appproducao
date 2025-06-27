<?php
$pdoApp = new PDO(
  'mysql:host=127.0.0.1;dbname=appproducao;charset=utf8mb4',
  'mauro.vasconcelos','Mavp220*',
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]
);