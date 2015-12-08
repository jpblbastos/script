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
 * Está atualizada para :
 *      PHP    5.3
 *      MYSQL  5.1 ou superior
 *
 *
 * @package   motorxml
 * @name      MotorMailSmtp
 * @version   2.0
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @copyright 2011 &copy; Eureka Soluçoes
 * @link      http://www.eurekasolucoes.com/
 * @author    Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
 *
 */

//carrega as classes do PHPMailer
require_once('PHPMailer/class.phpmailer.php');

class MotorMailSmtp {

    public $raizDir='';
    public $mailERROR='';
    public $mailFROM;
    public $mailHOST;
    public $mailUSER;
    public $mailPASS;
    public $mailPORT;
    public $mailPROTOCOL;
    public $mailFROMname;            
    public $mailFROMadmin;
    public $mailFROMuser;

    /**
    * __construct
    * Método construtor da classe
    * Este método utiliza o arquivo de configuração localizado no diretorio config
    * Este metodo pode estabelecer as configurações a partir do arquivo config.php ou 
    * através de um array passado na instanciação da classe.
    * 
    * @version 2.0
    * @package motorxml
    * @author  joao paulo bastos <jpbl.bastos at gmail dot com>
    * @param   array 
    * @return  boolean true sucesso false Erro
    */
    function __construct($aConfig=''){
         //obtem o path da biblioteca
         $this->raizDir = dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR;
         //verifica se foi passado uma matriz de configuração na inicialização da classe
         if(is_array($aConfig)) {
	    $this->mailFROM  = $aConfig['mailFROM'];
            $this->mailHOST = $aConfig['mailHOST'];
            $this->mailUSER  = $aConfig['mailUSER'];
            $this->mailPASS  = $aConfig['mailPASS'];
            $this->mailPORT  = $aConfig['mailPORT'];
            $this->mailPROTOCOL  = $aConfig['mailPROTOCOL']; 
            $this->mailFROMname  = $aConfig['mailFROMname'];           
            $this->mailFROMadmin  = $aConfig['mailFROMadmin'];
            $this->mailFROMuser  = $aConfig['mailFROMuser'];
         }  else {
            //testa a existencia do arquivo de configuração
            if ( is_file($this->raizDir . 'config' . DIRECTORY_SEPARATOR . 'config.php') ){
                //carrega o arquivo de configuração
                include($this->raizDir . 'config' . DIRECTORY_SEPARATOR . 'config.php');
                // carrega propriedades da classe com os dados de configuração
                $this->mailFROM  = $mailFROM;
                $this->mailHOST = $mailHOST;
                $this->mailUSER  = $mailUSER;
                $this->mailPASS  = $mailPASS;
                $this->mailPORT  = $mailPORT;
                $this->mailPROTOCOL  = $mailPROTOCOL;
                $this->mailFROMname  = $mailFROMname;
                $this->mailFROMadmin  = $mailFROMadmin;
                $this->mailFROMuser = $mailFROMuser;
            } else {
                // caso não exista arquivo de configuração retorna erro
                $this->errMsg = "Não foi localizado o arquivo de configuração.";
                return false;
               }
            }
         return true;
    } //fim __construct

    /**
     * sendMail
     * Função para envio de erros do motorxml para o adm usando as classes Mail::Pear
     *
     * @package motorxml
     * @name    sendMail
     * @version 2.0
     * @param   string $errMsg erro do motor
     * @param   string $identificação uma identificação a ser usada no assunto
     * @return  boolean TRUE sucesso ou FALSE falha
     */
    public function sendMail($erroMsg='',$identificacao='') {
        // assunto email
        $subject = utf8_decode("Motor Diz: Erro no arquivo - " . $identificacao);
        //mensagem no corpo do email em html
        $msg = "<p><b>Prezado Sr(a) Administração ti Labs, Usuario Compras</b>";
        $msg .= "<p>Você está recebendo um erro do MotorXml, leia com atenção.</br>";
        $msg .= "<h2><i>Erro:$erroMsg</i>";
        $msg .= "<p><p>Em caso de duvida entrar em contato com o setor responsavel, para maiores informações !</br>";
        $msg .= "<p><p>Atenciosamente,<p>Setor de Tecnologia da Informação";
        //corrige de utf8 para iso
        $msg = utf8_decode($msg);
        // O email será enviado no formato HTML
        $htmlMessage = "<body bgcolor='#ffffff'>$msg</body>";
        // o parametro true indica que uma exceção será criada em caso de erro,
        // é necessário buscar o erro   
        date_default_timezone_set('America/Toronto');    
        $mail = new PHPMailer(); 
        // informa a classe para usar SMTP
        // executa ações
        $mail->IsSMTP();                            // telling the class to use SMTP
        $mail->SMTPAuth   = true;                   // enable SMTP authentication
        $mail->Host       = $this->mailHOST;        // sets the SMTP server
        $mail->Port       = $this->mailPORT;        // set the SMTP port for the GMAIL server
        $mail->Username   = $this->mailUSER;        // SMTP account username
        $mail->Password   = $this->mailPASS;        // SMTP account password
        $mail->SetLanguage("br");
        $mail->SetFrom($this->mailFROM, $this->mailFROMname);
        $mail->AddAddress($this->mailFROMadmin);
        $mail->AddCC($this->mailFROMuser);
        $mail->Subject    = $subject;
        $mail->MsgHTML($htmlMessage);
        if(!$mail->Send()) {
	   echo $mail->ErrorInfo ;
           return false;
        } else {
           return true;
        }
    } //fim da função sendNFeMail

} //fim da classe
?>
