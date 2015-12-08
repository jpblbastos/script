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
* @name           processorPdf.class.php
* @version        3.0
* @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
* @copyright      2013 &copy its Code
* @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
* @date           27-Ago-2013
* @description    Classe manipuladora para conversão/criacao dos arquivos pdf's
* @dependencies   "config.php"
*                 "fpdf_v1.7/fpdf.php"
*
**/  

//define timezone
date_default_timezone_set('America/Sao_Paulo');

//define o caminho base da aplicacao
if (!defined('PATH_ROOT')) {
     define('PATH_ROOT', dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR);
}

//carrega a classe fpdf
if ( is_file(PATH_ROOT.'libs/fpdf_v1.7/fpdf.php') ){
     require_once(PATH_ROOT.'libs/fpdf_v1.7/fpdf.php');
}else{
     echo "OPS, arquivo fpdf.php nao encontrada, impossivel de prosseguir!!\n";
     exit;
}

//carrega arquivo functions.php
if ( is_file(PATH_ROOT.'includes/functions.php') ){
   require_once(PATH_ROOT.'includes/functions.php');
}else{
   echo "OPS, arquivo functions.php nao encontrado, impossivel de prosseguir!!\n";
   exit;
}  

class processorPdf {
     // atributos public
     public $oPdf=array('user'=>'',
                        'dirDest'=>'',
                        'compress'=>'',
                        'nomePdf'=>''
                       );
     public $errorClass ='';
     public $error=false;
     // atributos protegidos
     protected $debugMode  = 0;
     protected $mapUser ='';
     protected $arqTxt     ='';

     /**
     * __contruct
     * Construtor da classe MailPedPHP
     * @param number $mododebug (Optional) 1-SIM ou 0-NÃO (0 default)
     * @package spoolga
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     */
     function __construct($mododebug=0,$file=''){
         if(is_numeric($mododebug)){
             $this->debugMode = $mododebug;
         }
         if($this->debugMode){
             //ativar modo debug
             error_reporting(E_ALL);ini_set('display_errors', 'On');
         } else {
             //desativar modo debug
             error_reporting(0);ini_set('display_errors', 'Off');
         }
         
         //inclui configuracoes
         if ( is_file(PATH_ROOT.'conf/config.php') ){
             include(PATH_ROOT.'conf/config.php');
             $this->mapUser = $arqMapUser;
         }else{
             echo "OPS, arquivo config.php nao encontrado, impossivel de prosseguir!!\n";
             exit; 
         }     

         //carrega arquivo a ser tratato
         if (is_file($file)){
             $this->arqTxt = file($file);
         }else{
             $this->errorClass.= "O arquivo ".$file." nao foi localizado!! Impossivel prosseguir. \n";
             $this->error=true; 
         }
     } // end__construct

    /**
     * @name    __getDados
     * Pega os dados do arquivo para matriz $this->oPdf
     * @package spoolga
     * @version 3.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @param   string $file
     */
     private function __getDados($file){
         //lê linha 1 e pega os dados
         $dados = explode("|",$file[0]);
         //remove todos os espaços adicionais, tabs, linefeed, e CR
         //de todos os campos de dados retirados do TXT
         for ($x=0; $x < count($dados); $x++) {
             if( !empty($dados[$x]) ) {
                 $dados[$x] = preg_replace('/\s\s+/', " ", $dados[$x]);
                 $dados[$x] = trim($dados[$x]);
             } else {
                 $this->errorClass.= "Ops, Erro dados do cabeçalho não foram localizados!! Impossivel prosseguir. \n";
                 return false;
             }//end if
         } //end for
         //seta dados 
         $this->oPdf['user']=strtolower($dados[1]);
         $this->oPdf['nomePdf']=$dados[1]."-".date('H-i-s')."-".$dados[2].".pdf";
         $this->oPdf['compress']=$dados[3];
         return true; 
     }
     
     /**
     * @name    __setDir
     * Seta o nome do diretorio do dia convertido para destino dos pdf's
     * @package spoolga
     * @version 3.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @return  $dateConv
     */
     public function __setDir(){
         return (date('d-m-Y')."/");
     }   

     /**
     * @name    __getDirUser
     * Pega o diretorio do usuario
     * @package spoolga
     * @version 3.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @param   string $nomeUser
     */
     private function __getDirUser($nomeUser){
         // carrega arquivo xml e faz laço
         $dir='';
         $xml = simplexml_load_file($this->mapUser);
         foreach ($xml->User as $user) {
             if ($user->nameUser ==  $this->oPdf['user']){ 
                $this->oPdf['dirDest']=$user->dirUser;
                return true;
             }
         } // fim do foreach
         $this->errorClass.= "Ops, este usuario ".$this->oPdf['user']." não esta cadastrado para esta opção de relatorios em pdf ! Desculpe impossivel prosseguir. \n";
         return false;
     }
     
    /**
     * @name    convertTxttoPdf
     * Realiza a conversão do txt para o pdf
     * @package spoolga
     * @version 3.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @return  boolean
     */
     public function convertTxttoPdf(){
         # pega os dados necessarios para a converção
         if (!$this->__getDados($this->arqTxt))
             return false;
         
         if (!$this->__getDirUser($this->oPdf['user']))
             return false;
         
         # instancia a classe fpdf e realiza a conversão
         $docPdf = new FPDF();
         
         //lê linha por linha do arquivo txt e atribui os valores
         for($l = 1; $l < count($this->arqTxt); $l++) {
             $dados = $this->arqTxt[$l];
             if((strpos($dados, 'NP|')!==false)){
                 $docPdf->AddPage();
                 $docPdf->SetY(5); 
                 $docPdf->SetX(1); 
                 //seta fonte
                 $docPdf->SetFont('courier','B',$this->oPdf['compress']);
             }else{
                 $docPdf->Write(4,$dados);
             }
         }//fim for
         /*Grava pdf*/
         # define novo destino
         $newDestPdf=$this->oPdf['dirDest'].$this->__setDir().$this->oPdf['nomePdf'];
         $docPdf->Output($newDestPdf,'F');
         if (is_file($newDestPdf))
             return true; 
         else
             return false;
     }

     /**
     * @name    __showDados
     * Mostra os dados
     * @package spoolga
     * @version 3.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     */
     public function __showDados(){
         if (!$this->__getDados($this->arqTxt)){
             echo  $this->errorClass ."\n"; 
         }
         if (!$this->__getDirUser($this->oPdf['user'])){
             echo  $this->errorClass ."\n";
         }
         //mostra
         echo "Mostra matriz oPdf\n";
         print_r($this->oPdf);
     }
} //fim da classe
?>
