<?php
//cabeça
include ('header.php');

//corpo
echo '<div id="wrapper">';
echo '<div id="doc">';
echo '<div id="box">';
echo '<h2>Digite abaixo os dados da NF-e a ser removida da base: </h2>';
echo "<form action='process-remove.php' method='post' >\n";
echo "   <table>\n";
echo "	   <tr>\n";
echo	      "<th>Cnpj-Cpf do Fornecedor:</th>\n";
echo             "<td>\n";
echo                     "<input type='text' name='cnpj' id='cnpj' value=''  size='14' maxlength='14'/>\n";
echo              "</td>\n";
echo      "</tr>\n";
echo "	   <tr>\n";
echo	      "<th>Numero: </th>\n";
echo             "<td>\n";
echo                     "<input type='text' name='nfe' id='nfe' value=''  size='10' maxlength='10'/>\n";
echo              "</td>\n";
echo      "</tr>\n";
echo      "<tr>\n";
echo          " <td><input type='submit' value='Execultar Já !' /></td>\n";
echo	  "</tr>\n";
echo "  </table>\n";	
echo "</form>\n";
echo '</div>';
echo '</div>';
echo '</div>';

//rodape
include ('footer.php');
?>

