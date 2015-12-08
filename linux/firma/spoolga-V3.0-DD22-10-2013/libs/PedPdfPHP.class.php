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
* @name           PedPdfPHP.class.php
* @version        2.0
* @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
* @copyright      2011 &copy Twoclick Criações
* @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
* @date           27-Fev-2013
* @description    Classe manipuladora a criacao dos arquivos pdf's
* @dependencies   "config.php"
*                 "fpdf_v1.7/fpdf.php"
*
*
**/  

//define o caminho base da aplicacao
if (!defined('PATH_ROOT')) {
   define('PATH_ROOT', dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR);
}
//carrega as classes do PHPMailer
require_once('fpdf_v1.7/fpdf.php');

class PedPdfPHP extends FPDF {
    //atributos gerais
    public    $dirsentItemsPdfs='';
    public    $logo='';
    public    $pdfERROR='';
    private   $namePdf='';
    
    /**
     * __contruct
     * Construtor da classe PedPdfPHP
     * @param number $mododebug (Optional) 1-SIM ou 0-NÃO (0 default)
     * @package spoolga
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     */
    function __construct($orientation='L',$unit='mm',$format='A4'){
        //passar parametros para a classe principal 
        parent::FPDF($orientation,$unit,$format);
        $this->pdfERROR='';
        //inclui configuracoes
        if ( is_file(PATH_ROOT.'conf/config.php') ){
            include(PATH_ROOT.'conf/config.php');
            $this->dirsentItemsPdfs  = $dirsentItemsPdfs;
            $this->logo = $logo;
        }     
    } // end__construct
    
    /**
     * createPdf 
     * Função que cria o pdf e armazena em $dirsentItemsPdfs
     *
     * @package spoolga
     * @name    createPdf
     * @version 2.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @param   string $filename passar uma string com o caminho completo do pedido
     * @return  boolean TRUE sucesso ou FALSE falha
     */
    public function createPdf($filename=''){
        if(is_file($filename)){
            $retorno = true;
            //add pagina pdf
            $this->AddPage();
            //add imagem ao pdf 
            $this->Image($this->logo,100,0,90,0,'','http://www.casadosparafusos.com');
            //seta orientacao
            $this->SetY(28); 
            //seta fonte
            $this->SetFont('courier','B',7);
            //escreve no pdf
            $this->WritePdf($filename);
            //saida do documento
            $newnamepdf=$this->dirsentItemsPdfs.$this->namePdf.'.pdf';
            $this->Output($newnamepdf,'F');
        }else{
               $this->pdfERROR .= "O arquivo ".$filename." nao foi localizado!! \n";
               $retorno = false;
        }//fim if(is_file)
        return $retorno;        
    }//fim createPdf
    
    /**
     * Escreve as linhas do arquivo de pedido no pdf
     * @package spoolga
     * @name    WritePdf
     * @version 2.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @param   string $file
     */
    private function WritePdf($file){
      //carrega o arquivo para variavel $arquivo
      $arquivo  = file($file);
      //lê linha por linha do arquivo txt e atribui os valores
      for($l = 0; $l < count($arquivo); $l++) {
          $dados = $arquivo[$l];
          switch ( $l ) {
             case 0: 
                break;
             case 3:
                //pega o numero do pedido
                $this->namePdf=trim(substr($dados,20,6));
                //retira o percentual de desconto antes de gravar
                $dados=substr($dados,0,114).substr($dados,119,52);
                $this->Write(5,$dados);
                break;
             case 30: 
                break;
             case 31: 
                break;
             default:
                $this->Write(5,$dados);
                break;
          }//fim switch
      }//fim for
    }//fim __getLinesPed
} //fim da classe
?>
