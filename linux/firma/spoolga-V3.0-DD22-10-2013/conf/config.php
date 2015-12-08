<?php
/**
*
* Este arquivo é parte do projeto spoolga - sistema de impressao G. A. Silva
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
* @package        spoolga
* @name           config.php
* @version        2.0
* @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
* @copyright      2011 &copy Twoclick Criações
* @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
* @date           25-Fev-2013
* @description    arquivo de configuracao php
*
**/

//###############################
//#### CONFIGURAÇÕES GERAIS #####
//###############################
//defini se a aplicacao e um servico
$service=TRUE;
//tempo de loop
$tempo=10;
$tempo1=5;
//diretorio de caixa saida dos e-mails
$dirOutBox='/opt/spoolga/mailBox/outBox/';
//diretorio itens enviados
$dirsentItems='/opt/spoolga/mailBox/sentItems/';
//diretorio pdfs gerados
$dirsentItemsPdfs='/opt/spoolga/mailBox/sentItems/pdfs/';
//diretorio arquivos txt para converter para pdf
$dirTxttoPdf='/mnt/box/printers/pdf/';
//dirretorio de lixo
$dirLixo='/tmp/spoolprinted/';
//arquivo de mapeamento dos usuarios
$arqMapUser='/opt/spoolga/conf/map-user.xml';
//arquivo de log da app
$logApp='/opt/spoolga/log/status-printers.log';
//arquivo de registro dos e-mail
$logEmail='/opt/spoolga/log/record_email.log';
//logo gasilva
$logo='/opt/spoolga/artefatos/logo.jpg';

//###############################
//############ EMAIL ############
//###############################
//Configuração do email
$mailAuth='1';
$mailFROM='envio@casadosparafusos.com';
$mailHOST='mail.casadosparafusos.com';
$mailUSER='envio@casadosparafusos.com';
$mailPASS='415263ga';
$mailPROTOCOL='';
$mailPORT='587';
$mailFROMmail='envio@casadosparafusos.com';
$mailFROMname='Casa dos Parafusos';
$mailREPLYTOmail='envio@casadosparafusos.com';
$mailREPLYTOname='Casa dos Parafusos';
