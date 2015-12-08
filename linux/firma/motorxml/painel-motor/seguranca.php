<?php
include("./config.php");

//faz autenticação
$user   = $_POST['user'];
$passwd = $_POST['passwd'];

$result = mysql_query("SELECT * FROM admin_user WHERE admin_nome='$user' AND admin_passwd='$passwd'");
$cnt    = @mysql_num_rows($result);
if($cnt >= 1){
  $_SESSION['login'] = $user;
  header("Location: http://128.1.0.156/painel-motor/painel");
} else {
  header("Location: http://128.1.0.156/painel-motor/erro.php");
}

?>
