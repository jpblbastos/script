<?php
/**
 * Parametros de configuração do sistema
 *
 */

/**
###############################
########## GERAL ##############
###############################
*/

// tipo de ambiente esta informação deve ser editada pelo sistema
// 1-Produção 2-Homologação
// esta variável será utilizada para direcionar os arquivos e
// estabelecer o contato com o SEFAZ
$ambiente=2;

//esta variável contêm o nome do arquivo com todas as url dos webservices do sefaz
//incluindo a versao dos mesmos, pois alguns estados não estão utilizando as
//mesmas versões
$arquivoURLxml="def_ws2.xml";

//Diretório onde serão mantidos os arquivos com as NFe em xml
//a partir deste diretório serão montados todos os subdiretórios do sistema
//de manipulação e armazenamento das NFe
$raizDir = dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR;

//Diretório onde serão armazenadas os xml de entradas para a conversão
$DirXml="importacao/entrada/";

//Diretorio onde serão armazenados os xml processados com sucesso
$DirProc="integracao/xml/";

//Diretorio onde serão armazenados os xml rejeitados pelo motor
$DirRej="importacao/rejeitados/";

//Diretorio onde serão armazenados os txt convertidos pelo motor
$DirTxt="integracao/valida/";

//Diretorio onde serao armazenados os txt convertidos pelo motos sem passar pelo crivo do
//validador nfe da casa dos parafusos
$DirTxt1="integracao/txt/";

//URL base da API, passa a ser necessária em virtude do uso dos arquivos wsdl
//para acesso ao ambiente nacional
$baseurl="http://128.1.0.156/painel-motor";

//Versão em uso dos shemas utilizados para validação dos xmls
$schemes="PL_006g";

//Arquivo de logs
$arqLog="log/motorxml.log";

/**
###############################
#### CERITIFICADO DIGITAL #####
###############################
*/

//Nome do certificado que deve ser colocado na pasta certs da API
$certName = 'certificado_teste.pfx';
//Senha da chave privada
$keyPass = 'associacao';
//Senha de decriptaçao da chave, normalmente não é necessaria
$passPhrase="";

/**
###############################
#### CONFING DO EMAIL POP3 ####
###############################
*/

//host do servidor pop3
$hostpop="{pop.terra.com.br:110/pop3}";
//login da conta no servidor pop3
$loginpop="nfe.compra@casadosparafusos.com"; 
//senha da conta no servidor pop3
$passwordpop="758496"; 
//diretorio para salvar o email
$saveboxdir="importacao/entrada/" ; 

/**
###############################
######### EMAIL ADM ###########
###############################
*/
//Configuração do email do administrador para enviar erros
$mailFROM='nfe.compra@casadosparafusos.com';
$mailHOST='smtp.terra.com.br';
$mailPORT=587;
$mailPROTOCOL='ssl';
$mailUSER='nfe.compra@casadosparafusos.com';
$mailPASS='758496';
$mailFROMname='Sistema motorxml - ti Labs';
$mailFROMadmin="j.gasilva@terra.com.br";
$mailFROMuser="compras.ga@terra.com.br";

?>
