<?
$ddf = fopen('erro.log','a');
fwrite($ddf,"comeco do monitor\n");
fclose($ddf); 


$loop = 0;
$comando = "clear";
while ($loop == 0){
    $ddf = fopen('erro.log','a');
    fwrite($ddf,"teste\n");
    fclose($ddf);
    sleep(3);
    exec ("clear");
}
?>
