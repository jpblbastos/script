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
* @name           MailPedPHP.class.php
* @version        2.0
* @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
* @copyright      2011 &copy Twoclick Criações
* @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
* @date           25-Fev-2013
* @description    Classe manipuladora dos e-mail a serem enviados
* @dependencies   "config.php"
*                 "PHPMailer_v5.1/class.phpmailer.php"
*
********* AGRADECIMENTO AO PROJETO NFEPHP, POIS ESTA CLASSE E MODIFICADA  PARA SE 
********* ADEQUAR AS NECESSIDADE DO PROJETO SPOOLGA APARTIR DA CLASSE MailNfePHP.class.php
********* DO PROJETO NFEPHP
*
**/  

//define o caminho base da aplicacao
if (!defined('PATH_ROOT')) {
   define('PATH_ROOT', dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR);
}
//carrega as classes do PHPMailer
require_once(PATH_ROOT.'/libs/PHPMailer_v5.1/class.phpmailer.php');
//carrega a classe PedPdfPHP
require_once(PATH_ROOT.'/libs/PedPdfPHP.class.php');

class MailPedPHP {
    //atributos das configurações de e-mail
    public $mailAuth='1';
    public $mailFROM='';
    public $mailHOST='';
    public $mailUSER='';
    public $mailPASS='';
    public $mailPORT='';
    public $mailPROTOCOL='';            
    public $mailFROMmail='';
    public $mailFROMname='';
    public $mailREPLYTOmail='';
    public $mailREPLYTOname='';
    public $mailERROR='';
    public $dirOutBox='';
    public $dirsentItemsPdfs='';
    public $aMail=array('para'=>'',
                        'razao' =>'',
                        'cnpj'  =>'',
                        'numero'=>'',
                        'cliente'=>'',
                        'validade'=>'',
                        'valor'=>'',
                        'vendedor'=>'' 
                       );

    //atributos gerais
    protected $debugMode = 0;

    /**
     * "Layout Template" do corpo do email em html
     * Os dados variáveis da mensagem html são :
     * {numero}{cliente}{emitente}{cnpj}{validade]{valor}{vendedor}
     * esses campos serão substituidos durante o envio do email   
     * @var string 
     */
    protected $layouthtml = '';
    
    /**
     * __contruct
     * Construtor da classe MailPedPHP
     * @param number $mododebug (Optional) 1-SIM ou 0-NÃO (0 default)
     * @package spoolga
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     */
    function __construct($mododebug=0){
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
        //verifica a existencia do layout html para os e-mail's
        if (is_file(PATH_ROOT.'conf/layout_email.html')){
            $this->layouthtml = file_get_contents(PATH_ROOT.'conf/layout_email.html');
        }
        $this->mailERROR='';
        //inclui configuracoes
        if ( is_file(PATH_ROOT.'conf/config.php') ){
            include(PATH_ROOT.'conf/config.php');
            $this->mailAuth  = $mailAuth;
            $this->mailFROM  = $mailFROM;
            $this->mailHOST = $mailHOST;
            $this->mailUSER  = $mailUSER;
            $this->mailPASS  = $mailPASS;
            $this->mailPORT  = $mailPORT;
            $this->mailPROTOCOL  = $mailPROTOCOL;
            $this->mailFROMmail  = $mailFROMmail;
            $this->mailFROMname  = $mailFROMname;
            $this->mailREPLYTOmail = $mailREPLYTOmail;
            $this->mailREPLYTOname = $mailREPLYTOname;
            $this->dirOutBox = $dirOutBox;
            $this->dirsentItemsPdfs = $dirsentItemsPdfs;
        }     
    } // end__construct
    
    /**
     * enviaMail 
     * Função de envio de email do pedido
     *
     * @package spoolga
     * @name    enviaMail
     * @version 2.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @param   string $filename passar uma string com o caminho completo do pedido
     * @return  boolean TRUE sucesso ou FALSE falha
     */
    public function enviaMail($filename=''){
        if(is_file($filename)){
            $retorno = true;
            //atribui os dados do pedido na matriz $this->aMail
            $this->__getDadosPed($filename);
            //envia o e-mail
            if ( !$this->sendPed($filename,'1') ){
               $this->mailERROR .= "Falha ao enviar para ".$this->aMail['para']."!! \n";
               $retorno = false;
            }
        }else{
               $this->mailERROR .= "O arquivo ".$filename." nao foi localizado!! \n";
               $retorno = false;
        }//fim if(is_file)
        return $retorno;        
    }//fim enviaMail
    
    /**
     * sendPed
     * Função para envio dos pedidos por email usando PHPMailer
     *
     * @package spoolga
     * @name sendPed
     * @version 2.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @param string $arqPed Path do arquivo de pedido para geração do pdf
     * @param boolean $auth Indica se é necessária a autenticação
     * @return boolean TRUE sucesso ou FALSE falha
     */
    public function sendPed($filePed, $auth='') {
        //se existe array com os dados para o envio
        if (!is_array($this->aMail)){
            $this->mailERROR = "Não existem os parametros necessarios de envio!\n";
	    return false;
	}	
        //validar o endereço de email passado
        if (!$this->validEmailAdd($this->aMail['para'])){
            $this->mailERROR .= "O endereço informado não é valido! ".$this->aMail['para']."\n";
            return false;
        }
        //verifica parametro de autenticação
        if ($auth == '') {
            if (isset($this->mailAuth)){
                $auth = $this->mailAuth;
            } else {
                $auth = '1';
            }    
        }
        //criacao do pdf
        $pdf = new PedPdfPHP();
        $pdf->createPdf($filePed);
        $filePDF=$this->dirsentItemsPdfs.$this->aMail['numero'].'.pdf';
        //verifica se a criação do pdf foi um sucesso
        if(!is_file($filePDF)){
           $this->mailERROR .= "Erro ao gerar o pdf, impossivel prosseguir!\n";
	   return false;
        }
        // assunto email
        $subject = utf8_decode("Olá ".$this->aMail['cliente']." novo orçamento - Número ".$this->aMail['numero']."");
        //mensagem no corpo do email em html
        $htmlMessage = $this->layouthtml;
        //substitui os campos do layout {nome_campo} pelo valor das variáveis 
        $htmlMessage = str_replace('{cliente}', $this->aMail['cliente'], $htmlMessage);
        $htmlMessage = str_replace('{numero}', $this->aMail['numero'], $htmlMessage);
        $htmlMessage = str_replace('{emitente}', $this->aMail['razao'], $htmlMessage);
        $htmlMessage = str_replace('{cnpj}', $this->aMail['cnpj'], $htmlMessage);
        $htmlMessage = str_replace('{validade}', $this->aMail['validade'], $htmlMessage);
        $htmlMessage = str_replace('{valor}', $this->aMail['valor'], $htmlMessage);
        $htmlMessage = str_replace('{vendedor}', $this->aMail['vendedor'], $htmlMessage);
        //enviar o email
        if ( !$result = $this->__sendM($this->aMail['para'],$this->aMail['razao'],$subject,$htmlMessage,$filePDF,$auth)){
            //houve falha no envio reportar
            $this->mailERROR = "Houve erro no envio do email, DEBUGAR!! ".$this->mailERROR."\n"; 
        }
        return $result; //retorno da função
    } //fim da função sendNFe

    /**
     * __sendM
     * Função de envio do email
     * 
     * @package spoolga
     * @name __sendM
     * @version 2.0
     * @author   Joao Paulo Bastos L. <jpbl.bastos at gmail dot com> 
     * @param string $to            endereço de email do destinatário 
     * @param string $contato       Nome do contato - empresa
     * @param string $subject       Assunto
     * @param string $htmlMessage   Corpo do email em html
     * @param string $filePDF       path completo para o arquivo pdf
     * @param string $auth          Flag da autorização requerida 1-Sim 0-Não
     * @return boolean FALSE em caso de erro e TRUE se sucesso
     */
    private function __sendM($to,$contato,$subject,$htmlMessage,$filePDF,$auth){
        // o parametro true indica que uma exceção será criada em caso de erro,
        $mail = new PHPMailer(true); 
        // informa a classe para usar SMTP
        $mail->IsSMTP();
        // executa ações
        try {
            $mail->Host       = $this->mailHOST;        // SMTP server
            $mail->SMTPDebug  = 0;                      // habilita debug SMTP para testes
            $mail->Port       = $this->mailPORT;        // Seta a porta a ser usada pelo SMTP
            if ($auth=='1' && $this->mailUSER != '' && $this->mailPASS !=''){
                $mail->SMTPAuth   = true;                   // habilita autienticação SMTP
                if ($this->mailPROTOCOL !=''){
                    $mail->SMTPSecure = $this->mailPROTOCOL;    // "tls" ou "ssl"
                }    
		$mail->Username   = $this->mailUSER;        // Nome do usuários do SMTP
		$mail->Password   = $this->mailPASS;        // Password do usuário SMPT
            } else {
                $mail->SMTPAuth   = false;
            }	
            $mail->AddReplyTo($this->mailREPLYTOmail,$this->mailREPLYTOname); //Indicação do email de retorno
            $mail->AddAddress($to,$contato);            // nome do destinatário
            $mail->SetFrom($this->mailFROMmail,$this->mailFROMname); //identificação do emitente
            $mail->Subject = $subject;                  // Assunto
            $mail->MsgHTML($htmlMessage);               // Corpo da mensagem em HTML
            if (is_file($filePDF)){
                $mail->AddAttachment($filePDF);          // Anexo
            }
            $mail->Send();                              // Comando de envio
            $result = TRUE;
        // é necessário buscar o erro       
        } catch (phpmailerException $e) {               // captura de erros
            $this->mailERROR .= $e->errorMessage();      //Mensagens de erro do PHPMailer
            $result = FALSE;
        } catch (Exception $e) {
            $this->mailERROR .=  $e->getMessage();      //Mensagens de erro por outros motivos
            $result = FALSE;
        }
        return $result;
    } //fim __sendM
    
    /**
     * validEmailAdd
     * Função de validação dos endereços de email
     * 
     * @package spoolga
     * @name validEmailAdd
     * @version 1.02
     * @author  Douglas Lovell <http://www.linuxjournal.com/article/9585>
     * @param string $email Endereço de email a ser testado, podem ser passados vários endereços separados por virgula
     * @return boolean True se endereço é verdadeiro ou false caso haja algum erro 
     */
    public function validEmailAdd($email){
        $isValid = true;
        $aMails = explode(',', $email);
        foreach($aMails as $email){
            $atIndex = strrpos($email, "@");
            if (is_bool($atIndex) && !$atIndex){
                $this->mailERROR .= "$email - Isso não é um endereço de email.\n";
                $isValid = false;
            } else {
                $domain = substr($email, $atIndex+1);
                $local = substr($email, 0, $atIndex);
                $localLen = strlen($local);
                $domainLen = strlen($domain);
                if ($localLen < 1 || $localLen > 64){
                    // o endereço local é muito longo
                    $this->mailERROR .= "$email - O endereço é muito longo.\n";
                    $isValid = false;
                } else if ($domainLen < 1 || $domainLen > 255){
                    // o comprimento da parte do dominio é muito longa
                    $this->mailERROR .= "$email - O comprimento do dominio é muito longo.\n";
                    $isValid = false;
                } else if ($local[0] == '.' || $local[$localLen-1] == '.'){
                    // endereço local inicia ou termina com ponto
                    $this->mailERROR .= "$email - Parte do endereço inicia ou termina com ponto.\n";
                    $isValid = false;
                } else if (preg_match('/\\.\\./', $local)){
                    // endereço local com dois pontos consecutivos
                    $this->mailERROR .= "$email - Parte do endereço tem dois pontos consecutivos.\n";
                    $isValid = false;
                } else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
                    // caracter não valido na parte do dominio
                    $this->mailERROR .= "$email - Caracter não válido na parte do domínio.\n";
                    $isValid = false;
                } else if (preg_match('/\\.\\./', $domain)) {
                    // parte do dominio tem dois pontos consecutivos
                    $this->mailERROR .= "$email - Parte do domínio tem dois pontos consecutivos.\n";
                    $isValid = false;
                } else if (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))){
                    // caracter não valido na parte do endereço
                    if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))){
                        $this->mailERROR .= "$email - Caracter não válido na parte do endereço.\n";
                        $isValid = false;
                    }
                }
                if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))){
                    // dominio não encontrado no DNS
                    $this->mailERROR .= "$email - O domínio não foi encontrado no DNS.\n";
                    $isValid = false;
                }
            }
        }
        return $isValid;
    } //fim função validEmailAdd
    
   
    /**
     * Pega os dados do pedido para matriz $this->aMail
     * @package spoolga
     * @name    __getDadosPed
     * @version 2.0
     * @author  Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
     * @param   string $file
     */
    private function __getDadosPed($file){
      //carrega o arquivo para variavel $arquivo
      $arquivo  = file($file);
      //lê linha por linha do arquivo txt e atribui os valores
      for($l = 0; $l < count($arquivo); $l++) {
          $dados = $arquivo[$l];
          switch ( $l ) {
             case 0: 
                $this->aMail['para']=trim(substr($dados, 0, 60));
                break;
             case 2:
                $this->aMail['razao']='G. A. SILVA E CIA LTDA';
                $this->aMail['cnpj']=trim(substr($dados, 66, 18)); 
                break;
             case 3:
                $this->aMail['numero']=trim(substr($dados,20,6));
                $this->aMail['validade']=trim(substr($dados,62,10)); 
                $this->aMail['vendedor']=trim(substr($dados,92,22));
                break;
             case 4:
                $this->aMail['cliente']=trim(substr($dados,18,50));
                break;
             case 10:
                $this->aMail['valor']=trim(substr($dados,138,11)); 
                break;
          }
      }
    }

} //fim da classe
?>
