<?php
require_once __DIR__.'/includes/db_functions.php';
require_once __DIR__.'/includes/session_functions.php';
session_start();
verifierSession(); // Si tu veux bloquer l'accès à cette page sans session
session_unset();
session_destroy();
header('Location: index.php');
exit;
?>