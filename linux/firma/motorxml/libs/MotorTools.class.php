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
 *      Versão 2 dos webservices da SEFAZ com comunicação via SOAP 1.2
 *      e conforme Manual de Integração Versão 4.0.1 NT2009.006 Dezembro 2009
 *
 * Atenção: Esta classe não mantêm a compatibilidade com a versão 1.10 da SEFAZ !!!
 *
 * @package   motorxml
 * @name      MotorTools
 * @version   2.0
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @copyright 2011 &copy; Eureka Soluçoes
 * @link      http://www.eurekasolucoes.com/
 * @author    Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
 *
 *  
 *                    Agradecimentos
 * A equipe nfephp pela iniciativa brilhante, leia mais em, 
 * link: http://www.nfephp.org/
 * 
 *                    Observacoes
 * Algumas funções foram copiadas/modificadas apartir do projeto nfephp, referenciado acima.
 * 
 *        CONTRIBUIDORES DO PROJETO NFEPHP (em ordem alfabetica):
 *  
 *              Bernardo Silva <bernardo at datamex dot com dot br>
 *              Bruno Bastos <brunomauro at gmail dot com>
 *              Diego Mosela <diego dot caicai at gmail dot com>
 *              Eduardo Pacheco <eduardo at onlyone dot com dot br>
 *              Fabricio Veiga <fabriciostuff at gmail dot com>              
 *              Felipe Bonato <montanhats at gmail dot com> 
 *              Gilmar de Paula Fiocca <gilmar at tecnixinfo dot com dot br>
 *              Giovani Paseto <giovaniw2 at gmail dot com>
 *              Giuliano <giusoft at hotmail dot com>
 *              Glauber <glaubercini at gmail dot com>
 *              Jorge Luiz Rodrigues Tomé <jlrodriguestome at hotmail dot com>
 * 	        Odair Jose Santos Junior <odairsantosjunior at gmail dot com>
 *              Paulo Gabriel Coghi <paulocoghi at gmail dot com>
 *              Paulo Henrique Demori <phdemori at hotmail dot com>
 *              Vini Lazev <vinilazev at gmail dot com>
 *              Walber da Silva Sales <eng dot walber at gmail dot com>
 *              Roberto L. Machado <linux.rlm at gmail dot com>
 */

//carrega a classe de banco de dados
require_once('MotorDB.class.php');
 
class MotorTools {
    
    // propriedades da classe
    /**
    * raizDir
    * Diretorio raiz da App
    * @var string
    */
    public $raizDir='';
    /**
    * entDir
    * Diretorio onde sao recebidas as nf-e para converssao
    * @var string
    */
    public $entDir='';
    /**
    * saidaDir
    * Diretorio onde vao os arquivos txt convertidos
    * @var string
    */
    public $saidaDir='';
    /**
    * procDir
    * Diretorio onde vao as nf-e processadas
    * @var string
    */
    public $procDir='';
    /**
    * repDir
    * Diretorio onde são armazenados as notas reprovadas na validação da API
    * @var string
    */
    public $repDir='';
    /**
    * xml
    * XML da NFe
    * @var string 
    */
    public $xml='';
    /**
    * nomeArq
    * nome do arquivo gerado pelo converssor
    * @var string
    */
    public $nomeArq='';
    /**
    * cnpjArq
    * cnpj do arquivo
    * @var string
    */
    public $cnpjArq='';
    /**
    * tpAmb
    * Tipo de ambiente 1-produção 2-homologação
    * @var string
    */
    public $tpAmb='';
    /**
    * chave
    * ID da NFe 44 digitos
    * @var string 
    */
    public $chave='';
    /**
    * txt
    * @var string TXT com NFe
    */
    public $txt='';
    /**
    * errMsg
    * Mensagens de erro do API
    * @var string
    */
    public $errMsg='';
    /**
    * errStatus
    * Status de erro
    * @var boolean
    */
    public $errStatus=false;
    /**
    * errCod
    * Codigo do erro
    * @var integer
    */
    public $errCod=0;
    /**
    * cnpjLivre
    * lista de cnpj que não sera tratado o codigo do produto,
    * prevalecendo o codigo como esta na nota
    * @var array
    */
    private $cnpjLivre = array("05151518000140");
	
    /**
    * __construct
    * Método construtor da classe
    * Este método utiliza o arquivo de configuração localizado no diretorio config
    * Este metodo pode estabelecer as configurações a partir do arquivo config.php ou 
    * através de um array passado na instanciação da classe.
    * 
    * @version marco 1.0
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
	    $this->tpAmb=$aConfig['ambiente'];
            $this->entDir=$aConfig['entradaxml'];
            $this->saidaDir=$aConfig['saidatxt'];
            $this->procDir=$aConfig['processadas'];
            $this->repDir=$aConfig['reprovadas'];
            }  else {
            //testa a existencia do arquivo de configuração
            if ( is_file($this->raizDir . 'config' . DIRECTORY_SEPARATOR . 'config.php') ){
                //carrega o arquivo de configuração
                include($this->raizDir . 'config' . DIRECTORY_SEPARATOR . 'config.php');
                // carrega propriedades da classe com os dados de configuração
                $this->tpAmb=$ambiente;
                $this->entDir=$DirXml;
                $this->saidaDir=$DirTxt;
                $this->procDir=$DirProc;
                $this->repDir=$DirRej;
            } else {
                // caso não exista arquivo de configuração retorna erro
                $this->errMsg = "Não foi localizado o arquivo de configuração.";
                $this->errStatus = true;
                return false;
               }
            }
         return true;
    } //fim __construct
	
    /**
    * nfeXmltoTxt
    * Método de extracao do xml da nfe
    *
    * @package   motorxml
    * @name      nfeXmltoTxt
    * @version   marco 1.0
    * @param     string $arq Path do arquivo xml
    * @return    boolean
    *
    */
    public function nfeXmltoTxt($arq) {
        //variavel que irá conter o resultado
        $txt = "";
        //verificar se a string passada como parametro é um arquivo
        if ( is_file($arq) ){
            $matriz[] = $arq;
        } else {
            if ( is_array($arq) ){
                $matriz = $arq;
            } else {
                return FALSE;
            }
        }
        
        $nnfematriz = count($matriz);
        //para cada nf passada na matriz
        for ($x = 0; $x < $nnfematriz; $x++ ){
            //carregar o conteúdo do arquivo xml em uma string
            $xml = file_get_contents($matriz[$x]);
            //instanciar o ojeto DOM
            $dom = new DOMDocument();
            //carregar o xml no objeto DOM
            if (!$dom->loadXML($xml)){
                $this->errMsg='Arquivo xml Corrompido ou Invalido '.$matriz[$x].' !';
                $this->errCod=3;
                return false;
            }   
            //carregar os grupos de dados possíveis da NFe
            $nfeProc    = $dom->getElementsByTagName("nfeProc")->item(0);
            $infNFe     = $dom->getElementsByTagName("infNFe")->item(0);
            $ide        = $dom->getElementsByTagName("ide")->item(0);
            $refNFe     = $dom->getElementsByTagName("refNFe");
            $refNF      = $dom->getElementsByTagName("refNF");
            $emit       = $dom->getElementsByTagName("emit")->item(0);
            $avulsa     = $dom->getElementsByTagName("avulsa")->item(0);
            $dest       = $dom->getElementsByTagName("dest")->item(0);
            $retirada   = $dom->getElementsByTagName("retirada")->item(0);
            $entrega    = $dom->getElementsByTagName("entrega")->item(0);
            $enderEmit  = $dom->getElementsByTagName("enderEmit")->item(0);
            $enderDest  = $dom->getElementsByTagName("enderDest")->item(0);
            $det        = $dom->getElementsByTagName("det");
            $cobr       = $dom->getElementsByTagName("cobr")->item(0);
            $ICMSTot    = $dom->getElementsByTagName("ICMSTot")->item(0);
            $ISSQNtot   = $dom->getElementsByTagName("ISSQNtot")->item(0);
            $retTrib    = $dom->getElementsByTagName("retTrib")->item(0);
            $transp     = $dom->getElementsByTagName("transp")->item(0);
            $infAdic    = $dom->getElementsByTagName("infAdic")->item(0);
            $procRef    = $dom->getElementsByTagName("procRef")->item(0);
            $exporta    = $dom->getElementsByTagName("exporta")->item(0);
            $compra     = $dom->getElementsByTagName("compra")->item(0);

            //Montando o registro cabecalho dos registros
            $id = $infNFe->getAttribute("Id") ? $infNFe->getAttribute("Id") : '';
            $this->chave = substr($id,-44);
            $chave = $this->chave;

            //B|cNF|nNF|dEmi|
            $cNF = $ide->getElementsByTagName('cNF')->item(0)->nodeValue;
            $nNF = $ide->getElementsByTagName('nNF')->item(0)->nodeValue;
            $dEmi = $ide->getElementsByTagName('dEmi')->item(0)->nodeValue;

            //C|xNome|
            $xNome = !empty($emit->getElementsByTagName('xNome')->item(0)->nodeValue) ? $emit->getElementsByTagName('xNome')->item(0)->nodeValue : '';
            $CNPJ = !empty($emit->getElementsByTagName('CNPJ')->item(0)->nodeValue) ? $emit->getElementsByTagName('CNPJ')->item(0)->nodeValue : '';
            $CPF = !empty($emit->getElementsByTagName('CPF')->item(0)->nodeValue) ? $emit->getElementsByTagName('CPF')->item(0)->nodeValue : '';
            
            //C02|CNPJ|
            //[ou]
            //C02a|CPF|
            if ( $CPF != '' ) {
                $cnpj_cpf = $CPF;
            }else {
                $cnpj_cpf = $CNPJ;
            } //fim CPF ou CNPJ
	    //move cnpj_cpf para variavel cnpjArq publica
            $this->cnpjArq="";
            $this->cnpjArq = $cnpj_cpf; 
            //Grava nome do txt a ser gerado
            $this->nomeArq = $this->string_pad($cnpj_cpf,14,0,STR_PAD_LEFT) . $this->number_pad($nNF,6);

            //W02|vNF|
            $vNF = !empty($ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue) ? $ICMSTot->getElementsByTagName("vNF")->item(0)->nodeValue : '';	

            //grava cabecalho no txt
            $txt = $this->number_pad($nNF,6).$this->string_pad($cnpj_cpf,14,0,STR_PAD_LEFT)."$dEmi".$this->number_pad($nNF,9)."$chave\r\n";

            //instaciar uma variável para contagem loop de produtos
            $i = 0;
            foreach ($det as $d){
                $tp_reg = "I";
                //H|
                $nItem = $det->item($i)->getAttribute("nItem");
                $infAdProd = !empty($det->item($i)->getElementsByTagName("infAdProd")->item(0)->nodeValue) ? $det->item($i)->getElementsByTagName("infAdProd")->item(0)->nodeValue : '';
				
                //instanciar os grupos de dados internos da tag det
             	$prod = $det->item($i)->getElementsByTagName("prod")->item(0);
	        $imposto = $det->item($i)->getElementsByTagName("imposto")->item(0);
		$ICMS = $imposto->getElementsByTagName("ICMS")->item(0);
	        $IPI  = $imposto->getElementsByTagName("IPI")->item(0);
                $DI =  $det->item($i)->getElementsByTagName("DI")->item(0);
                $adi =  $det->item($i)->getElementsByTagName("adi")->item(0);
                $veicProd = $det->item($i)->getElementsByTagName("veicProd")->item(0);
                $med = $det->item($i)->getElementsByTagName("med")->item(0);
                $arma = $det->item($i)->getElementsByTagName("arma")->item(0);
                $comb = $det->item($i)->getElementsByTagName("comb")->item(0);
                $II = $det->item($i)->getElementsByTagName("II")->item(0);
                $PIS = $det->item($i)->getElementsByTagName("PIS")->item(0);
                $PISST = $det->item($i)->getElementsByTagName("PISST")->item(0);
                $COFINS = $det->item($i)->getElementsByTagName("COFINS")->item(0);
                $COFINSST = $det->item($i)->getElementsByTagName("COFINSST")->item(0);
                $ISSQN = $det->item($i)->getElementsByTagName("ISSQN")->item(0);
                $i++;

                //I|cProd|xProd|qCom|vUnCom|
                $cProd      =  !empty($prod->getElementsByTagName("cProd")->item(0)->nodeValue) ? $prod->getElementsByTagName("cProd")->item(0)->nodeValue : '';
                $xProd      =  !empty($prod->getElementsByTagName("xProd")->item(0)->nodeValue) ? $prod->getElementsByTagName("xProd")->item(0)->nodeValue : '';
                $qCom       =  !empty($prod->getElementsByTagName("qCom")->item(0)->nodeValue) ? $prod->getElementsByTagName("qCom")->item(0)->nodeValue : '';
                $vUnCom     =  !empty($prod->getElementsByTagName("vUnCom")->item(0)->nodeValue) ? $prod->getElementsByTagName("vUnCom")->item(0)->nodeValue : '';

		//Buscando o ipi
		$vIPI=0.00;
                if ( isset($IPI) ){
                    //O|clEnq|CNPJProd|cSelo|qSelo|cEnq|
                    $clEnq = !empty($IPI->getElementsByTagName("clEnq")->item(0)->nodeValue) ? $IPI->getElementsByTagName("clEnq")->item(0)->nodeValue : '';
                    $CNPJProd = !empty($IPI->getElementsByTagName("CNPJProd")->item(0)->nodeValue) ? $IPI->getElementsByTagName("CNPJProd")->item(0)->nodeValue : '';
                    $cSelo = !empty($IPI->getElementsByTagName("clEnq")->item(0)->nodeValue) ? $IPI->getElementsByTagName("cSelo")->item(0)->nodeValue : '';
                    $qSelo = !empty($IPI->getElementsByTagName("qSelo")->item(0)->nodeValue) ? $IPI->getElementsByTagName("qSelo")->item(0)->nodeValue : '';
                    $cEnq = !empty($IPI->getElementsByTagName("cEnq")->item(0)->nodeValue) ? $IPI->getElementsByTagName("cEnq")->item(0)->nodeValue : '';
                    
                    //grupo de tributação de IPI NAO TRIBUTADO
                    $IPINT = $IPI->getElementsByTagName("IPINT")->item(0);
                    if ( isset($IPINT) ){
                        $CST = (string) !empty($IPINT->getElementsByTagName("CST")->item(0)->nodeValue) ? $IPINT->getElementsByTagName("CST")->item(0)->nodeValue : '';
                    }
                    //grupo de tributação de IPI
                    $IPITrib = $IPI->getElementsByTagName("IPITrib")->item(0);
                    if ( isset($IPITrib) ){
                        $CST = (string) !empty($IPITrib->getElementsByTagName("CST")->item(0)->nodeValue) ? $IPITrib->getElementsByTagName("CST")->item(0)->nodeValue : '';
                        $vIPI = !empty($IPITrib->getElementsByTagName("vIPI")->item(0)->nodeValue) ? $IPITrib->getElementsByTagName("vIPI")->item(0)->nodeValue : '';
                        $vBC = !empty($IPITrib->getElementsByTagName("vBC")->item(0)->nodeValue) ? $IPITrib->getElementsByTagName("vBC")->item(0)->nodeValue : '';
                        $pIPI = !empty($IPITrib->getElementsByTagName("pIPI")->item(0)->nodeValue) ? $IPITrib->getElementsByTagName("pIPI")->item(0)->nodeValue : '';
                        $qUnid = !empty($IPITrib->getElementsByTagName("qUnid")->item(0)->nodeValue) ? $IPITrib->getElementsByTagName("qUnid")->item(0)->nodeValue : '';
                        $vUnid = !empty($IPITrib->getElementsByTagName("vUnid")->item(0)->nodeValue) ? $IPITrib->getElementsByTagName("vUnid")->item(0)->nodeValue : '';
					}//fim ipi trib
                } // fim IPI

            //tira as casas decimais antes de gravar no txt
            $qCom   = 100 * number_format($qCom, 2, '.', '');
            $vUnCom = 100 * number_format($vUnCom, 2, '.', '');
            $vIPI   = 100 * number_format($vIPI, 2, '.', '');
     
            //tratamento de codigos dos produtos da nf-e
            //se cnpj_cpf estive na lista de livres, ira prevalecer o codigo como vem na nf-e
            if (!in_array($cnpj_cpf, $this->cnpjLivre)){
               //retira zeros a esquerda e caracteres indevidos da variavel $cProd
               while( $cProd[0] == "0" ) {
                  $cProd = substr($cProd, 1, strlen ($cProd));
               }
               $cProd=ereg_replace('[^a-zA-Z0-9]', '', $cProd); 
            }
            
            //concatena descricao do produto se maior que 50 caracteres
            $xProd = substr($xProd, 0, 49);

            //acumula registros dos produtos no txt
            $txt .= "$tp_reg".$this->string_pad($cProd,20,' ',STR_PAD_RIGHT).$this->string_pad($xProd,50,' ',STR_PAD_RIGHT).$this->number_pad($qCom,10).$this->number_pad($vUnCom,10).$this->number_pad($vIPI,10)."\r\n";

            } // fim foreach produtos
	   
        } //end for
        //seta a variavel txt da classe com o txt gerado
        $this->txt = $txt;
        
	//instancia classe do db
	$db = new MotorDB ();
	if (!$db->open()) {   
	    $this->errMsg='Impossivel de Prossegir, sem conexao com o DB !';
            $this->errStatus=true;
	    return false;
	}
        //verifica se o cnpj/cpf esta na lista de bloqueio
        $sql= "select cnpj_list from black_list where cnpj_list='".$cnpj_cpf."'";
        $db->query($sql);
        if ($resp = $db->linhas() > 0){
            //se estiver na lista de bloqueio sai da funcao com o codigo 2
            $this->errCod=2;
            $this->errMsg='O Cnpj/Cpf: '.$cnpj_cpf. ' da nfe '.$nNF.', esta na lista de bloqueio(fornecedor sem vinculos de codigo) ';
            return false;
        }
        $sql="";
        //verifica se a nota ja foi inserida no db antes
        $sql = "select chave_nf from nf where chave_nf='".$this->chave."'";
        $db->query($sql);
        if ($resp = $db->linhas() > 0) {
            //se nota existe a query vai ser um update
            $sql = "";
            $sql = "update nf set chave_nf='".$this->chave."', numero_nf='".$nNF."', numero_nfe='".$nNF."', cnpj_forn='".$cnpj_cpf."', nome_forn='".$xNome."', data_emis='".$dEmi."', valor_tot='".$vNF."', cstatus_nf=0 where chave_nf='".$this->chave."';" ;
        } else {
            //se a nf nao existir a query vai ser um insert
            $sql = "";
	    $sql = "insert into nf values ('".$this->chave."', '".$nNF."', '".$nNF."', '".$cnpj_cpf."', '".$xNome."', '".$dEmi."', '".$vNF."', '0');"; 
        }

	if (! $db->query($sql)) {
	    $this->errMsg='Impossivel de Prossegir, nao foi permitido ao iserir dados na tabela nf !';
	    $this->errStatus=true;
	    return false;
	}	 
        $db->close();
        $this->errCod=100; 
        return TRUE;
    }// fim da função nfeXmltoTxt

    /**
    * number_pad
    * Método de inserir a qtd de zeros a esquerda em inteiros especifico para integração cobol
    * 
    * @package    motorxml
    * @name       number_pad
    * @version    marco 1.0
    * @param      int $number numero a ser modificado
    * @param      int $n quantidade de zeros a esquerda
    * @return     result     
    */
    public function number_pad($number, $n) {
        //return str_pad((int) $number,$n,"0",STR_PAD_LEFT);
        return str_pad($number,$n,"0",STR_PAD_LEFT);
    }

    /**
    * string_pad
    * Método para modificar o tamanho das variaveis especifico para integração cobol
    * 
    * @package    motorxml
    * @name       string_pad
    * @version    marco 1.0
    * @param      string $str string a ser modificadas
    * @param      int    $n   numero de caracteres a ser colocado
    * @param      string $char caracter a ser colocado
    * @param      string $position posição a ser inserida 
    * @return     result
    */
    public function string_pad($str ,$n, $char, $position) {
        return str_pad($str,$n,"$char",$position);
    }
 
    /**
    * remove_zeros
    * Metodo para retirar os zeros de uma variavel numerica
    * 
    * @package    motorxml
    * @name       remove_zeros
    * @version    marco 1.0
    * @param      string $str string a ser modificadas
    * @return     result
    */
    public function remove_zeros($str) {
        return ($str / 100) * 100;
    }
 
} //fim da classe MotorTools
?>
