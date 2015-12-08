<?php
//cabeça
include ('vars.php');
include ('header.php');

//resgatando os dados passados pelo form coment
$nfe      = $_POST['nfe'];
$cnpj     = $_POST['cnpj'];

//inserindo zeros a esquerda
$nfe  = str_pad($nfe,6,"0",STR_PAD_LEFT); 
$cnpj = str_pad($cnpj,14,"0",STR_PAD_LEFT); 


//monta comando
$comando = "/home/motorxml/integracao/valida/".$cnpj."".$nfe.".txt";

//escreve no arquivo de comandos
$fp = fopen($arqCmd,'w');
fwrite($fp,"$comando \n");
fclose($fp);

//corpo
echo '<div id="wrapper">';
echo '<div id="doc">';
echo '<div id="box">';
echo "<h2>A nf-e ".$nfe." do fornecedor ".$cnpj.",  foi marcada para ser removida.</h2>";
echo '</div>';
echo '</div>';
echo '</div>';

//rodape
include ('footer.php');
?>

