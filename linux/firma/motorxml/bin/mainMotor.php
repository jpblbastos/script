<?php
/**
 * Este arquivo é parte do projeto motorxml - converte xml em txt (especifico para G. A. Silva & Cia Ltda)
 *
 * Este programa é um software livre: você pode redistribuir e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU (GPL)como é publicada pela Fundação
 * para o Software Livre, na versão 3 da licença, ou qualquer versão posterior
 * e/ou 
 * sob os termos da Licença Pública Geral Menor GNU (LGPL) como é publicada pela Fundação
 * para o Software Livre, na versão 3 da licença, ou qualquer versão posterior.
 *  
 * Você deve ter recebido uma cópia da Licença Publica GNU e da 
 * Licença Pública Geral Menor GNU (LGPL) junto com este programa.
 * Caso contrário consulte <http://www.fsfla.org/svnwiki/trad/GPLv3> ou
 * <http://www.fsfla.org/svnwiki/trad/LGPLv3>. 
 *
 *
 * @package   motorxml
 * @name      mainMotor
 * @version   2.0
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @copyright 2011 &copy; Eureka Soluçoes
 * @link      http://www.eurekasolucoes.com/
 * @author    Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
 */

/**
 * Manipulador das libs e funcoes do projeto, sua execulcao em modo de servico depende 
 * do parametro setado na variavel $SERVICE [0/1] onde 0 = nao, e 1 = sim
 * na variavel $TEMP define o tempo de busca em cada interacao no diretorio de entradas
 * 
 */

/**
 * Biblioteca de funcoes e classes
 */
include ('../includes/funcoesMotor.php');
include ('../libs/MotorTools.class.php');
include ('../libs/MotorMailPop3.class.php');
include ('../libs/MotorMailSmtp.class.php');

/**
 * Variaveis de configuracoes do servico
 */
//0 = nao e em modo de servico, 1 = modo de servico
$SERVICE=1;

//tempo de cada interacao
$TEMP=600;

//arquivo de log
$LOG='/home/motorxml/log/motorxml.log';

/**
 * Iniciando o servico
 */
date_default_timezone_set('America/Sao_Paulo');
my_log("Motor Diz: O servico de conversao de xml foi iniciado as ".date(' Y-m-d H:i:s') , $LOG);
while ($SERVICE == 1) {
    //conecta na conta pop3 para baixar os email
    $oMAIL = new MotorMailPop3();
    if (!$oMAIL->openBox()) {
        echo "OPS, algo de erro aconteceu no downloads da conta de email , ver log....\n";
        my_log("Motor Diz: OPS erro inesperado na operacao openBox, ".$oMAIL->errMsg. "em ".date('Y-m-d H:i:s') , $LOG);
    }
    if ($oMAIL->getNunBox()  > 0) {
        $oMAIL->getBox();
        my_log("Motor Diz: Legal foram baixados , ".$oMAIL->getNunBox(). " email para min trabalhar em ".date('Y-m-d H:i:s') , $LOG);
        $oMAIL->closeBox();
    } else {
        my_log("Motor Diz: Oh, caixa de entrada vazia da conta pop3 , sem nada para trabalhar em  " .date('Y-m-d H:i:s') , $LOG);
        $oMAIL->closeBox();
    }
    
    //espera a proxima interacao
    sleep($TEMP);
    if (!autoXMLtoTXT()) {
        //lembrete colocar aqui o envio da msg para o admin
        echo "OPS, algo de erro aconteceu na leitura dos xmls, ver log ....\n";
        my_log("Motor Diz: Algo de errado aconteceu na leitura dos xml ou na funcao autoXMLtoTXT da include funcoesMotor, as ".date(' d/m/y à\s h:i\h ') , $LOG);
    }
}
    
?>
