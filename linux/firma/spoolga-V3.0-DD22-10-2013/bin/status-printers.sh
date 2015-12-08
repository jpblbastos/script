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
# * @name           status-printers.sh
# * @version        1.0
# * @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
# * @copyright      2011 &copy Twoclick Criações
# * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
# * @date           17-Ago-2012
# * @description    em base no arquivo de configuração das printers, este script faz uma 
# *                 verificação se as mesmas estão on-line e grava seu status (0 = off / 1 = on)
# *                 em um arquivo de status de acordo com o nome da printer.
# * @dependencies   arquivo de configuração : conf-printers.xml
# *                 arquivo de status       : printers.status
# *                 arquivo  de variaveis   : var_globais
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
# * ipPrinter
# * ips das printers
# * @var array
# */
ipPrinter=


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
   # zera arquivo de log
   >$LOGAPP
   # testa se arquivo configuracao existe
   if [ ! -e $CONFPRINTERS ] || [ ! -e  $ARQSTATUS ] ; then
      echo  Motor-Impressao Diz: OPS, Erro fatal, o arquivo de configuracoes nao encontrado $CONFPRINTERS $ARQSTATUS, impossivel prosseguir em `date +%d.%b.%Y-%H.%M` ! >> $LOGAPP
      exit 1
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
   echo  Motor-Impressao Diz: Serviço iniciado em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
   for (( i = 0 ; i < ${#nomePrinter[@]} ; i++ )) 
   do
      echo  Motor-Impressao Diz: ${nomePrinter[$i]} carregada para monitoramento em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
   done
} 

# /**
# * writePrinters
# * Método que escreve as printers no arquivo de status
# * @version 1.0
# * @package spoolga
# * @author  joao paulo bastos <jpbl.bastos at gmail dot com>
# */
function writePrinters(){
   echo  Motor-Impressao Diz: Escrevendo printers no arquivo de status em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
   # zera arquivo
   >$ARQSTATUS
   for (( i = 0 ; i < ${#nomePrinter[@]} ; i++ )) 
   do
      echo ${nomePrinter[$i]}=x >> $ARQSTATUS
   done
} 

# /**
# * loadConfPrinters
# * Método que carrega arquivo de configurações das printers
# * @version 1.0
# * @package spoolga
# * @author  joao paulo bastos <jpbl.bastos at gmail dot com>
# */
function loadConfPrinters(){
   # faz o loop para ler o arquivo de configurações printers atraz de "nomePrinter ipPrinter"
   for tag in nomePrinter ipPrinter
   do
      OUT=`grep  $tag $CONFPRINTERS | tr -d '\t' | sed 's/^<.*>\([^<].*\)<.*>$/\1/' `
      # fazendo o eval_trick
      eval ${tag}=`echo -ne \""${OUT}"\"`
   done
   # carregando para os array
   nomePrinter=( `echo ${nomePrinter}` )
   ipPrinter=( `echo ${ipPrinter}` )
} 

# // fim funcoes do script

# // corpo do script

setGlobais                 # seta variaveis globais da app
loadConfPrinters           # carrega configuracao das printers
writeCab                   # escreve cabecalho no log de monitoramento
writePrinters              # escreve printers no arquivo de status

# faz o loop principal da app 
while [ "$LOOP" = 1 ]; do
   # faz loop dentro do array
   for (( i = 0 ; i < ${#nomePrinter[@]} ; i++ )) 
   do
      # recebe status atual do arquivo
      ST=`grep ^${nomePrinter[$i]} $ARQSTATUS |cut -c10-10`
      # verifica status com ping da printer
      if ping -c 1 ${ipPrinter[$i]} > /dev/null ; then
         STNEW=1     # status on-line
      else
         STNEW=0     # status off-line
      fi
      # compara status da printer para gravação no arquivo
      if [ "$ST" != "$STNEW" ] ; then
         sed -i s/${nomePrinter[$i]}=$ST/${nomePrinter[$i]}=$STNEW/  $ARQSTATUS
         echo Motor-Impressao Diz: OPS, ${nomePrinter[$i]} sem conexao em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
      elif [ "$STNEW" -eq "0" ] ; then
         echo Motor-Impressao Diz: OPS, ${nomePrinter[$i]} sem conexao em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
      else
         echo Motor-Impressao Diz: ${nomePrinter[$i]} conectada em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
      fi
   done
   # espera o tempo para refazer o loop
   sleep $TEMPLOOP
done
# // fim corpo do script
