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
* @name           functions.php
* @version        3.0
* @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
* @copyright      2011 &copy Twoclick Criações
* @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
* @date           27-Ago-2013
* @description    arquivo de funcoes gerais
* @dependencies   config.php
*
**/  

/**
* listDir
* Método para obter todo o conteúdo de um diretorio, e
* que atendam ao critério indicado.
* @package        spoolga
* @name           listDir
* @version        2.0
* @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
* @param          string $dir Diretorio a ser pesquisado
* @param          string $fileMatch Critério de seleção pode ser usados coringas como *-nfe.xml
* @param          boolean $retpath se true retorna o path completo dos arquivos se false so retorna o nome dos arquivos
* @return         mixed Matriz com os nome dos arquivos que atendem ao critério estabelecido ou false
*/
function listDir($dir,$fileMatch,$retpath=true){
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
                my_log("Motor Diz: OPS erro inesperado, Falha não há permissão de leitura no diretorio escolhido em ".date('Y-m-d H:i:s') , $logApp);
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
 * @package        spoolga
 * @name           listDir
 * @version        2.0
 * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
 * @param   string $msg
 * @param   string $arqLog
*/
function  my_log ($msg, $arqLog) {
    //abre arquivo em ponteiro
    $log = fopen($arqLog,'a');
    //escreve no arquivo
    fwrite($log, "$msg \r\n");
    //fecha o arquivo
    fclose($log);
} 

/**
 * getNomeMesCurto
 * Método que retorna o nome do mes curto 
 *
 * @package        spoolga
 * @name           getNomeMesCurto
 * @version        3.0
 * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
 * @param    int $numMes
 * @return   string $nomeMes
*/
function  getNomeMesCurto ($numMes) {
     // array dos nome dos meses curtos
     $nomeMesCurto = array('01'=>'Jan',
                           '02'=>'Fev',
                           '03'=>'Mar',
                           '04'=>'Abr',
                           '05'=>'Mai',
                           '06'=>'Jun',
                           '07'=>'Jul',
                           '08'=>'Ago',
                           '09'=>'Set',
                           '10'=>'Out',
                           '11'=>'Nov',
                           '12'=>'Dez',
                           '13'=>'Err'
                          );
     if (($numMes < 01 ) || ($numMes > 12))
         return $nomeMesCurto[13];
     else
         return $nomeMesCurto["$numMes"];
}