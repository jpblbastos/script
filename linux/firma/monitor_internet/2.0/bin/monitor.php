<?
/*
 ** Nome ...:       monitor.php
 ** Desc ...:       script para monitorar a conexao com a internet. Se a conexao estiver com falha o script start um script que se
                    conecta no modem utilizando telnet e o reinicia.
 ** Versao .:       2.0 
 ** Alt ....:       Arquivo de log
 ** Por ....:       Joao Paulo 
 ** Data ...:       25-04-2011
 **
*/

/* 
 ** Variaveis do Sistema 
*/

/* array dos pings */
$servidores = array (
"www.uol.com.br" => "200.147.67.142",
"www.noticiaslinux.com.br" => "209.85.14.162"
);

/* script de reboot */
$reboot = "/script-adm/reboot_modem.sh";

/* Data e Hora */
$data=date(' d/m/y à\s h:i\h ');

/* infinito $loop = 0 sim / $loop = 1 nao */
$loop = 0;

/*
 ** Funcao de gravar log
 ** Parm : msn
*/
function  my_log ($msn ) {
    $log = fopen('../log/status.log','a');
    fwrite($log, "$msn \n");
    fclose($log);
}


/*
 ** Comeco do laco infinito
*/
my_log ("\n\nComecando o Monitoramento de Internet em  $data ");
while ( $loop == 0) {
    /* Inicializa Variaveis */
	$test        = 1;
    $cont        = 0;
	$sem_conexao = 0;
    $site        = " ";
	$ip          = " ";
	reset ($servidores) ;
	/* Tempo de Espera */
	my_log ("\n\nAguardando Espera (20 segundos) ...");
    sleep(20);
	my_log ("Iniciando Teste de Internet ...");
    while ( $cont < 2) {
        while (list($site,$ip) = each($servidores)) {
            $comando = "/bin/ping -c 1 " . $ip;
			$saida   = shell_exec($comando);
			if ( ereg("bytes from",$saida)) {
			    $sem_conexao = 0;
				my_log ("\nConexao Normal com a Internet ....");
				my_log ("Teste: $site   IP:  $ip ");
				my_log ("Numero do Teste: $test ");
			} else {
			    $sem_conexao ++;
				my_log ("\nOPS, Sem Conexao com a Internet ... ");
				my_log ("Teste: $site   IP:  $ip ");
				my_log ("Numero do Teste: $test ");
			}
			$cont ++;
			$test ++;
        }		
	    if ($sem_conexao == 2) {
		    my_log ("\nOPS, a verificacao concluiu que estamos sem Internet ! em $data");
			my_log ("Fazendo reboot agora .... em $data");
		    shell_exec ($reboot);
			my_log ("Aguardando 45 segundos ate que o gateway seja atualizado ...");
			sleep(45);
			my_log ("Gateway atualizado ....  em $data");
		}
    }
}
?>