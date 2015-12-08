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
 * @name      MotorMailPop3
 * @version   2.0
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @copyright 2011 &copy; Eureka Soluçoes
 * @link      http://www.eurekasolucoes.com/
 * @author    Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
 *
 */
     
class MotorMailPop3 {
    
    //propriedades da classe
    /**
    * raizDir
    * Diretorio raiz da App
    * @var string
    */
    public $raizDir='';
    /**
    * host
    * Host do servidor pop3
    * @var string
    */
    public $host='';
    /**
    * login
    * Login da conta pop3
    * @var string
    */
    public $login='';
    /**
    * passWord
    * Senha da Conta pop3
    * @var string
    */
    public $passWord='';
    /**
    * saveBoxDir
    * Diretorio para salvar os email
    * @var string
    */
    public $saveBoxDir='';
    /**
    * mBox
    * Objeto da caixa de entrada
    * @var object
    */
    public $mBox='';
    /**
    * errMsg
    * Variavel de resposta de erros
    * @var string
    */
    public $errMsg='';

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
    function __construct($aConfig='') {
         //obtem o path da biblioteca
         $this->raizDir = dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR;
         //verifica se foi passado uma matriz de configuração na inicialização da classe
         if(is_array($aConfig)) {
	    $this->host=$aConfig['host'];
            $this->login=$aConfig['login'];
            $this->passWord=$aConfig['passWord'];
            $this->saveBoxDir=$aConfig['saveBoxDir'];
            }  else {
            //testa a existencia do arquivo de configuração
            if ( is_file($this->raizDir . 'config' . DIRECTORY_SEPARATOR . 'config.php') ){
                //carrega o arquivo de configuração
                include($this->raizDir . 'config' . DIRECTORY_SEPARATOR . 'config.php');
                // carrega propriedades da classe com os dados de configuração
                $this->host=$hostpop;
                $this->login=$loginpop;
                $this->passWord=$passwordpop;
                $this->saveBoxDir=$saveboxdir;
            } else {
                // caso não exista arquivo de configuração retorna erro
                $this->errMsg = "Não foi localizado o arquivo de configuração.";
                return false;
               }
            }
         return true;
    } //fim __construct

    /**
    * openBox
    * Método de abertura da caixa de entrada do email
    *
    * @package   motorxml
    * @name      openBox
    * @version   2.0
    * @return    boolean 
    *
    */
    public function openBox() {
        $this->mBox = imap_open ($this->host,  $this->login, $this->passWord); 
        if ($this->mBox === false) {
            $this->errMsg = imap_last_error();
            return false;
        } else {
            return true;
        }

    } //fim openBox

    /**
    * closeBox
    * Método para fechar a caixa de entrada do email
    *
    * @package   motorxml
    * @name      closeBox
    * @version   2.0
    *
    */
    public function closeBox() {
        imap_close($this->mBox, CL_EXPUNGE);
    } //fim closeBox


    /**
    * getNunBox
    * Método para pegar a qtd de email da caixa de entrada
    *
    * @package   motorxml
    * @name      getNunBox
    * @version   2.0
    * @return    int  quantidade e email da caixa
    *
    */
    public function getNunBox() {
        $qtdMail = imap_num_msg($this->mBox);
        return $qtdMail;
    } //fim getNunBox

    /**
    * getBox
    * Método onde serão baixados todos os emails da conta 
    *
    * @package   motorxml
    * @name      getBox
    * @version   2.0
    *
    */
    public function getBox() {
        //cria o laço de busca das mensagens
       	for ($jk = 1; $jk <= imap_num_msg($this->mBox); $jk++){
	   $structure = imap_fetchstructure($this->mBox, $jk , FT_UID);    
	   $parts = $structure->parts;
	   $fpos = 2;
           //cria o laço para busca de anexos
	   for($i = 1; $i < count($parts); $i++){
	      $message["pid"][$i] = ($i);
	      $part = $parts[$i];
              //se a menssagem tiver anexo
	      if($part->disposition == "ATTACHMENT"){
		 $ext=$part->subtype;
		 $params = $part->dparameters;
		 $filename=$part->dparameters[0]->value;
		 $extension= substr($filename, -4);
                 //se for xml faz os downloads
                 if (strtolower($extension) == ".xml") {
		    $mege="";
		    $data="";
		    $mege = imap_fetchbody($this->mBox,$jk,$fpos);  
		    $fp=fopen($this->raizDir.$this->saveBoxDir.$filename,'w');
		    if($part->encoding == 3) { // 3 = BASE64
		       $data = base64_decode($mege);
		    }
		    elseif($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
                       $data = quoted_printable_decode($mege);
		    }
		    fputs($fp,$data);
		    fclose($fp);
		    $fpos+=1;
                 } 
              }
	   }
           //marca mensagens para ser deletadas no metodo closeBox
	   imap_delete($this->mBox,$jk);	
	}
    } //fim getBox

}//fim da classe MotorMail
?>
