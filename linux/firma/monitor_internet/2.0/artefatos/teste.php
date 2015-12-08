<!-- painel.php -->
<html>
<head>
<META HTTP-EQUIV="Refresh" CONTENT="60">
</head>
<body>
<h3>Painel de Monitoramento de Hosts</h3>
<table border=1 cellspacing=3>
<tr>
<?
$servidores = array (
"www.uol.com.br" => "200.147.67.142",
"www.noticiaslinux.com.br" => "209.85.14.162",
"sti" => "128.1.0.25",
"http://128.1.0.9/eureka" => "128.1.0.9"
);

while (list($site,$ip) = each($servidores)) {
$comando = "/bin/ping -c 1 " . $ip;
$saida = shell_exec($comando);

echo "<td>".$site."<br>".$ip."<br>"."Status:";
if ( ereg("bytes from",$saida) ) {
echo "<b>online</b></td>";
} else {
echo "<font color=red><b>nãresponde</b></font></td>";
}
}
?>
</tr>
</table>
</body>
</html>
<!-- fim do programa -->
