<?php
//Dados para conex�o com o MySQL
$host    = "localhost";                       //Servidor
$user    = "motorxml";                        //Usu�rio
$pass    = "motor321";                        //Senha
$bd      = "motorxml";                        //Base de Dados

//N�o mexa!!!
mysql_connect($host, $user, $pass);
mysql_select_db($bd);

?>
