<?php
include ('header.php');
include("./config.php");

$query    = "select * from nf";
$result   = mysql_query($query);
$num_rows = @mysql_num_rows($result);

//faz o laco
echo '<div id="wrapper">';
echo '<div id="doc">';
echo '<div id="box">';
echo '<table>';
echo '<tr>';
echo '<th>Chave NF-e</th> ';
echo '<th>Numero NF</th> ';
echo '<th>Numero NF-e</th> ';
echo '<th>Cnpj do Fornecedor</th> ';
echo '<th>Nome do Fornecedor</th> ';
echo '<th>Data de Emissão</th> ';
echo '<th>Valor Total</th> ';
echo '<th>Status</th> ';
echo '</tr>';
for ($i = 0; $i < $num_rows; $i ++) {
	 $row = mysql_fetch_array($result);
     echo '<tr>';
     echo '<td>'.stripslashes($row['chave_nf']).'</td>';
     echo '<td>'.stripslashes($row['numero_nf']).'</td>';
     echo '<td>'.stripslashes($row['numero_nfe']).'</td>';
     echo '<td>'.stripslashes($row['cnpj_forn']).'</td>';
     echo '<td>'.stripslashes($row['nome_forn']).'</td>';
     echo '<td>'.stripslashes($row['data_emis']).'</td>';
     echo '<td>'.stripslashes($row['valor_tot']).'</td>';
     echo '<td>Importada</td>';
}
echo '</table>';
echo '</div>';
echo '</div>';
echo '</div>';
mysql_close($result);

include ('footer.php');
?>

