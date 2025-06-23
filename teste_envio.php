<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/envia_email.php';

enviarEmail(
  'mauroavpaz@gmail.com',
  'Teste SMTP com Debug',
  '<p>Se você recebeu este e-mail, a integração está funcionando!</p>'
);
