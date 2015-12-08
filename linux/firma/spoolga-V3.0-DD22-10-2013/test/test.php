<?php
require_once('/opt/spoolga/includes/functions.php');

//$mes=getNomeMesCurto(date('m'));

$nomeDir=date('d')."-".getNomeMesCurto(date('m'))."-".date('Y')."/";

echo $nomeDir;
?>
