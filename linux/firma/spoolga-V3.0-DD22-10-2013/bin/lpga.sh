#!/bin/sh
# /**
# *
# * Este arquivo é parte do projeto spoolga - sistema de impressao G. A. Silva
# *
# * Este programa é um software livre: você pode redistribuir e/ou modificá-lo
# * sob os termos da Licença Pública Geral GNU (GPL)como é publicada pela Fundação
# * para o Software Livre, na versão 3 da licença, ou qualquer versão posterior
# * e/ou 
# * sob os termos da Licença Pública Geral Menor GNU (LGPL) como é publicada pela Fundação
# * para o Software Livre, na versão 3 da licença, ou qualquer versão posterior.
# *  
# * Você deve ter recebido uma cópia da Licença Publica GNU e da 
# * Licença Pública Geral Menor GNU (LGPL) junto com este programa.
# * Caso contrário consulte <http://www.fsfla.org/svnwiki/trad/GPLv3> ou
# * <http://www.fsfla.org/svnwiki/trad/LGPLv3>. 
# *
# * @package        spoolga
# * @name           lpga.sh
# * @version        2.0
# * @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
# * @copyright      2011 &copy Twoclick Criações
# * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
# * @date           17-Ago-2012
# * @description    monitora o diretorio de spool, capturando os arquivos do ambiente
# *                 Unix, e os imprime de acordo com a printer informada no cabecalho 
# *                 de dados
# * @dependencies   arquivo de configuração       : conf-printers.xml
# *                 arquivo de status             : printers.status
# *                 arquivo de variaveis globais  : var_globais
# * @changeVersion  envio dos pedidos por email: acrescentado funcao whatsHere para manipulacao
# *                 da linha cabecalho do arquivo, e tratamento de arquivos .eml
# * @dateChange     18-Fev-2013         
# * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
# *
# **/  

#  // propriedades do script

# /**
# * sysconfig_spoolga
# * arquivo de variaveis globais
# * @var int
# */
: ${sysconfig_spoolga:=/opt/spoolga/conf/var_globais}
test -e $sysconfig_spoolga || exit 4
test -x $sysconfig_spoolga || exit 5

# /**
# * TEMPLOOP
# * Tempo de espera de cada volta
# * @var int
# */
TEMPLOOP=5

# /**
# * LOOP
# * Variavel que define o loop infinito ( 0 = false / 1 = true)
# * @var int
# */
LOOP=1

# /**
# * nomePrinter
# * nomes das printers
# * @var array
# */
nomePrinter=

# /**
# * nomeLocalPrinter
# * nome local das printers
# * @var array
# */
nomeLocalPrinter=


# // fim das propriedades do script


# // funcoes do script

# /**
# * setGlobais
# * Método que seta as variaveis globais
# * @version 1.0
# * @package spoolga
# * @author  joao paulo bastos <jpbl.bastos at gmail dot com>
# */
function setGlobais(){
   # carrega arquivo variaveis globais
   . $sysconfig_spoolga
   # testa se arquivo configuracoes existe
   if [ ! -e $CONFPRINTERS ] || [ ! -e $ARQSTATUS ] ; then
      echo  Motor-Impressao Diz: OPS, Erro fatal, o arquivo de configuracoes nao encontrado $CONFPRINTERS $ARQSTATUS, impossivel prosseguir em `date +%d.%b.%Y-%H.%M` ! >> $LOGAPP
      exit 1
   else
      echo  Motor-Impressao Diz: Os arquivos de configuracoes $CONFPRINTERS $ARQSTATUS VAR_GLOBAIS, foram localizados  em `date +%d.%b.%Y-%H.%M` ! >> $LOGAPP
   fi 
   # testa se diretorio de entrada de arquivos existe
   if [ ! -d $DIRSPOOL ]  ; then 
      echo  Motor-Impressao Diz: OPS, Isso e uma alerta, o diretorio de trabalho $DIRSPOOL nao localizado em `date +%d.%b.%Y-%H.%M` ! >> $LOGAPP
   else
      echo  Motor-Impressao Diz: O diretorio de trabalho $DIRSPOOL foi localizado em `date +%d.%b.%Y-%H.%M` ! >> $LOGAPP
   fi
} 

# /**
# * writeCab
# * Método que escreve cabecalho do log
# * @version 1.0
# * @package spoolga
# * @author  joao paulo bastos <jpbl.bastos at gmail dot com>
# */
function writeCab(){
   echo  Motor-Impressao Diz: Iniciado o serviço de impressao spoolga da G. A. Silva em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
} 

# /**
# * searchIdxPrinter
# * Método que faz procura o indice da printer desejada no array
# * @version 1.0
# * @package spoolga
# * @author  joao paulo bastos <jpbl.bastos at gmail dot com>
# * @parm    string nameSearch
# * @return  int    indice
# */
function searchIdxPrinter(){
   # printer a ser procurada
   nameSearch=$1
   # faz o loop para fazer a varredura no array nomePrinter
   for (( x = 0 ; x < ${#nomePrinter[@]} ; x++ )) 
   do
     if [ "${nomePrinter[$x]}" = "$nameSearch" ] ; then
        return $x
     fi
   done
} 

# /**
# * whatsHere
# * Método que verifica a instrução da primeira linha, se e um email ou um impressao
# * @version 2.0
# * @package spoolga
# * @author  joao paulo bastos <jpbl.bastos at gmail dot com>
# * @parm    string oVerif
# * @return  int 0 - se for invalida , 1 - se for email , 2 - se for impressora
# */
function whatsHere(){
   # pega valor passado
   oVerif=$1
   # verifica se e um email passado no paramentro
   if [[ "$oVerif" =~ $REGEX ]] ; then
      return 1
   fi
   # faz o loop para fazer a varredura no array nomePrinter
   for (( x = 0 ; x < ${#nomePrinter[@]} ; x++ )) 
   do
     if [ "${nomePrinter[$x]}" = "$oVerif" ] ; then
        return 2
     fi
   done
   # senao retorna 0 - invalida
   return 0  
}

# /**
# * loadConfPrinters
# * Método que carrega arquivo de configurações das printers
# * @version 1.0
# * @package spoolga
# * @author  joao paulo bastos <jpbl.bastos at gmail dot com>
# */
function loadConfPrinters(){
   # faz o loop para ler o arquivo de configurações printers atraz de "nomePrinter nomeLocalPrinter"
   for tag in nomePrinter nomeLocalPrinter
   do
      OUT=`grep  $tag $CONFPRINTERS | tr -d '\t' | sed 's/^<.*>\([^<].*\)<.*>$/\1/' `
      # fazendo o eval_trick
      eval ${tag}=`echo -ne \""${OUT}"\"`
   done
   # carregando para os array
   nomePrinter=( `echo ${nomePrinter}` )
   nomeLocalPrinter=( `echo ${nomeLocalPrinter}` )
} 

# // fim funcoes do script

# // corpo do script

setGlobais                 # seta variaveis globais da app
loadConfPrinters           # carrega configuracao das printers
writeCab                   # escreve cabecalho no log de monitoramento
# pausa para carga total do sistema
sleep 120
# trata o diretorio de spool
error=1
while [ "$error" -eq 1 ] ; do
   if mount -a 2>&- ; then 
      if [ -d $DIRSPOOL ] ; then
         error=0
         echo  Motor-Impressao Diz: Eba, entrei no diretorio $DIRSPOOL com sucesso em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
      else
         echo  Motor-Impressao Diz: OPS, falha ao entrar no diretorio $DIRSPOOL, tentando outra vez  em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
      fi
   else
      echo  Motor-Impressao Diz: OPS, falha ao montar /mnt/box, tentando outra vez  em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
      sleep 10
   fi
done

# faz o loop principal da app 
while [ "$LOOP" = 1 ]; do
   # verifica se ha impressoes a fazer
   N_IMP=`ls $DIRSPOOL/*.imp $DIRSPOOL/*.eml |wc -l`
   if [ "$N_IMP" -gt 0 ] ; then 
      # faz o laco entre os arquivos da a seren impressos
      for i in `ls -A $DIRSPOOL/*.imp $DIRSPOOL/*.eml 2>&-` ; do
         # pega valor da primeira linha do arquivo para testar
         firstLine=`sed 1q $i`
         # verifica qual e o parametro passado
         whatsHere $firstLine
         # recebe return
         flag=`echo $?`
         # se for um email 
         if [ "$flag" -eq 1 ] ; then
            # movendo o arquivo
            mv -f $i $DIROUTBOX
            echo  Motor-Impressao Diz: Arquivo $i movido para pasta de e-mails a serem enviados em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
         # se e uma PRINTER
         elif [ "$flag" -eq 2 ] ; then
            # passa valor para variavel remotePrinter
            remotePrinter=$firstLine
            # pega status da printer no arquivo de status
            stPrinter=`grep ^$remotePrinter $ARQSTATUS |cut -c10-10`
            # se estiver online faz a impressao
            if [ "$stPrinter" -eq 1 ] ; then
               # pega o indice da impressora
               searchIdxPrinter $remotePrinter
               idxPrinter=`echo $?`
               # faz a impressao
               if sed -e '1d' $i | lpr -P ${nomeLocalPrinter[$idxPrinter]} $PARMS ; then
                  echo  Motor-Impressao Diz: Impressao $i realizada com sucesso na ${nomePrinter[$idxPrinter]} em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
                  mv -f $i $DIRLIXO
               else
                  echo  Motor-Impressao Diz: OPS, Algo de errado aconteceu com a impressao $i em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
               fi
            else # se estiver offline ignora arquivo
               echo  Motor-Impressao Diz: Impressao $i nao pode ser realizada, verifique status da impressora, pulando a mesma em `date +%d.%b.%Y-%H.%M` >> $LOGAPP 
            fi
         else
            # parametro invalido para primeira linha
            echo  Motor-Impressao Diz: OPS, parametro da primeira linha invalido no arquivo $i em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
         fi
      done   
   else
      echo  Motor-Impressao Diz: OPS, Existem $N_IMP impressoes, sem nada o que fazer em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
   fi
   # espera o tempo para refazer o loop
   sleep $TEMPLOOP
done
# // fim corpo do script
