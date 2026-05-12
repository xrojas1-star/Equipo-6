<?php
session_start();
$_SESSION['user_id']      = null;
$_SESSION['user_name']    = 'Visitante';
$_SESSION['user_role']    = 'visitante';
$_SESSION['es_visitante'] = true;
header("Location: home.php");
exit();
?>