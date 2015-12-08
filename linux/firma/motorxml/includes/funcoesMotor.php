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
 * @name      funcoesMotor
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
 *
 */

/**
 * Funcoes Globais
*/
date_default_timezone_set('America/Sao_Paulo');

 
/**
* autoXMLtoTXT
* Método para converter todas as nf em formato xml para o formato txt
* localizadas na pasta "entradas". Os arquivos xml apos terem sido
* convertidos com sucesso são movidos para a pasta processados.
* Funcao retirada da classe ToolsNFePHP 
* Author Original Roberto L. Machado <linux.rlm at gmail dot com>
*
* @version marco 1.0
* @package motorxml
* @author  João Paulo Bastos Leite <jpbl.bastos at gmail dot com>
* @param   none
* @return  boolean true sucesso false Erro
*/
function autoXMLtoTXT(){
    //carrega as configuracoes 
    if (is_file('../config/config.php')){
        include("../config/config.php");
    } else {
         my_log("Motor Diz: Impossivel de prossegir, arquivo de configuracoes config.php, nao encontrado !".date('Y-m-d H:i:s') , '/home/motorxml/log/motorxml.log');
         return FALSE;
    }
    //varre pasta "entradas" a procura de NFes em xml
    $nomeDir = $raizDir.$DirXml;
    $aName = listDir($nomeDir , '*.xml', true);
    // se foi retornado algum arquivo
    if ( count($aName ) > 0){
        for ( $x=0; $x < count($aName); $x++ ) {
            //carrega nfe em xml para converter em txt
            $filename = $aName[$x];
            //instancia a classe de conversão
            $oXML = new MotorTools();
            //convete o arquivo
            if ($oXML->nfeXmltoTxt($filename)){
                //salvar o txt
                $txt = $oXML->txt;
                //verifica se cnpj e das nossas lojas
                if (($oXML->cnpjArq === "02532281000159") || ($oXML->cnpjArq === "02532281000230") || ($oXML->cnpjArq === "02532281001120")){
                   $txtname = $raizDir.$DirTxt1.$oXML->nomeArq.'.txt';
                } else {
                   $txtname = $raizDir.$DirTxt.$oXML->nomeArq.'.txt';
                }
                //destino do xml processado
                $newname  = $raizDir.$DirProc.'procNFE'.$oXML->chave.'AUT.xml';
            } elseif($oXML->errCod ===2) {//cnpj/cpf na lista de bloqueio de integracao
                //o cnpj/cpf esta na lista de bloqueio de integracao, porem sera movido para a pasta xml
                $newname  = $raizDir.$DirProc.'procNFE'.$oXML->chave.'AUT.xml';
                my_log("Motor Diz: OPS , ".$oXML->errMsg. ", porem o xml sera armazenado ! em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                rename($filename,$newname);
            } elseif($oXML->errCod === 3){//xml invalido ou corropindo
                $ident= substr($filename,34);
                my_log("Motor Diz: OPS erro inesperado, ".$oXML->errMsg. "em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                //enviando o erro para a analize
                $send = new MotorMailSmtp();
                if (!$send->sendMail($oXML->errMsg, $ident )){
                    my_log("Motor Diz: OPS erro de smtp, nao foi possivel o envio do erro para os administradores do sistema em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                }else {
                    my_log("Motor Diz: OK, o erro foi encaminhado para a analise dos administradors em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                }
                //movida para rejeitados
                $newname = $raizDir.$DirRej.substr($filename,34);
                rename($filename,$newname);
            } else {
                my_log("Motor Diz: OPS erro inesperado, ".$oXML->errMsg. "em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                //enviando o erro para a analize
                $send = new MotorMailSmtp();
                if (!$send->sendMail($oXML->errMsg, substr($filename,34))){
                    my_log("Motor Diz: OPS erro de smtp, nao foi possivel o envio do erro para os administradores do sistema em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                }else {
                    my_log("Motor Diz: OK, o erro foi encaminhado para a analise dos administradors em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                }
                return false;
            }
           //verifica se a geracao do arquivo txt foi ok
           if ($oXML->errCod === 100){
              if ( !file_put_contents($txtname, $txt) ){
                   my_log("Motor Diz: OPS erro inesperado, provavelmente a geracao do txt nao obteve sucesso - chave ".$oXML->chave.", em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                   return false;
              } else {
                  //grava no arquivo de log
                  my_log("Motor Diz: A NF-e ".$oXML->chave." foi convertida para txt com sucesso ! em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                  //mover o xml para a pasta processadas
                  rename($filename,$newname);
              }
           } //fim da gravação
         } // fim do for
    } //fim do teste de contagem
    return true;
} //fim autoXMLtoTXT



/**
* listDir
* Método para obter todo o conteúdo de um diretorio, e
* que atendam ao critério indicado.
* @version marco 1.0
* @package motorxml
* @author Roberto L. Machado <linux.rlm at gmail dot com>
* @param string $dir Diretorio a ser pesquisado
* @param string $fileMatch Critério de seleção pode ser usados coringas como *-nfe.xml
* @param boolean $retpath se true retorna o path completo dos arquivos se false so retorna o nome dos arquivos
* @return mixed Matriz com os nome dos arquivos que atendem ao critério estabelecido ou false
*/
function listDir($dir,$fileMatch,$retpath=false){
    if ( trim($fileMatch) != '' && trim($dir) != '' ) {
        //passar o padrão para minúsculas
        $fileMatch = strtolower($fileMatch);
        //cria um array limpo
        $aName=array();
        //guarda o diretorio atual
        $oldDir = getcwd().DIRECTORY_SEPARATOR;
        //verifica se o parametro $dir define um diretorio real
        if ( is_dir($dir) ) {
            //mude para o novo diretorio
            chdir($dir);
            //pegue o diretorio
            $diretorio = getcwd().DIRECTORY_SEPARATOR;
            if (strtolower($dir) != strtolower($diretorio)) {
                my_log("Motor Diz: OPS erro inesperado, Falha não há permissão de leitura no diretorio escolhido em ".date('Y-m-d H:i:s') , $raizDir.$arqLog);
                return false;
            }
            //abra o diretório
            $ponteiro  = opendir($diretorio);
            $x = 0;
            // monta os vetores com os itens encontrados na pasta
            while (false !== ($file = readdir($ponteiro))) {
                //procure se não for diretorio
                if ($file != "." && $file != ".." ) {
                    if ( !is_dir($file) ){
                        $tfile = strtolower($file);
                        //é um arquivo então
                        //verifique se combina com o $fileMatch
                        if (fnmatch($fileMatch, $tfile)){
                            if ($retpath){
                                $aName[$x] = $dir.$file;
                            } else {
                                $aName[$x] = $file;
                            }
                            $x++;
                        }
                    } //endif é diretorio
                } //endif é  . ou ..
            }//endwhile
            closedir($ponteiro);
            //volte para o diretorio anterior
            chdir($oldDir);
        }//endif do teste se é um diretorio
    }//endif
    return $aName;
} //fim da função listDir

/**
 * my_log
 * Método de gravar log
 *
 * @version marco 1.0
 * @package motorxml
 * @author  João Paulo Bastos Leite <jpbl.bastos at gmail dot com>
 * @param   string $msg
 * @param   string $arqLog
*/
function  my_log ($msg, $arqLog ) {
    //verifica se arquivo de log esta maior que 1 MB, se estiver zera o mesmo
    $nSize = filesize($arqLog); 
    if ($nSize > 1048576) {
        $log  = fopen($arqLog,'w');
        fwrite($log,"Motor Diz: Novo arquivo de log criado em  ".date('Y-m-d H:i:s')."\r\n");
        fclose($log);
    }

    $log = fopen($arqLog,'a');
    fwrite($log, "$msg \r\n");
    fclose($log);
}

?>
