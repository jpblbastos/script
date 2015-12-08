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
# * @version        1.0
# * @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
# * @copyright      2011 &copy Twoclick Criações
# * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
# * @date           17-Ago-2012
# * @description    monitora o diretorio de spool, capturando os arquivos do ambiente
# *                 Unix, e os imprime de acordo com a printer informada no cabecalho 
# *                 de dados
# * @dependencies   arquivo de configuração : conf-printers.xml
# *                 arquivo de status       : printers.status
# *                 arquivo de conf-geral   : conf-geral.xml
# *
# **/  

#  // propriedades do script

# /**
# * RAIZDIR
# * Diretorio raiz da App
# * @var string
# */
RAIZDIR=

# /**
# * DIRSPOOL
# * Diretorio onde sera buscado os arquivos 
# * @var string
# */
DIRSPOOL=

# /**
# * DIRLIXO
# * Diretorio onde sera movido os arquivos
# * @var string
# */
DIRLIXO=

# /**
# * PARMS
# * Parametros da impressao
# * @var string
# */
PARMS="-o cpi=25"

# /**
# * CONFAPP
# * Arquivo de configuração da app
# * @var string
# */
CONFAPP=

# /**
# * CONFPRINTERS
# * Arquivo de configuração das printers
# * @var string
# */
CONFPRINTERS=

# /**
# * LOGAPP
# * Arquivo de log da app
# * @var string
# */
LOGAPP=

# /**
# * ARQSTATUS
# * Arquivo de status das printers
# * @var string
# */
ARQSTATUS=

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
   # seta raiz da app
   cd ../
   RAIZDIR=`pwd`
   # seta arquivo de log da app
   LOGAPP=$RAIZDIR/log/status-printers.log
   # zera arquivo de log
   >$LOGAPP
   # seta arquivo de configuração da app
   CONFAPP=$RAIZDIR/conf/conf-geral.xml
   # testa se arquivo configuracao existe
   if [ ! -e $CONFAPP ] ; then
      echo  Motor-Impressao Diz: OPS, Erro fatal, o arquivo de configuracao $CONFAPP nao existe, impossivel prosseguir em `date +%d.%b.%Y-%H.%M` ! >> $LOGAPP
      exit 1
   fi 
   # busca parametro DIRSPOOL
   for tag in dirSpool
   do
      OUT=`grep  $tag $CONFAPP | tr -d '\t' | sed 's/^<.*>\([^<].*\)<.*>$/\1/' `
      # fazendo o eval_trick
      eval ${tag}=`echo -ne \""${OUT}"\"`
   done
   DIRSPOOL=( `echo ${dirSpool}` )
   # busca parametro DIRLIXO
   for tag in dirLixo
   do
      OUT=`grep  $tag $CONFAPP | tr -d '\t' | sed 's/^<.*>\([^<].*\)<.*>$/\1/' `
      # fazendo o eval_trick
      eval ${tag}=`echo -ne \""${OUT}"\"`
   done
   DIRLIXO=( `echo ${dirLixo}` )
   # seta arquivo de configuração das printers
   CONFPRINTERS=$RAIZDIR/conf/conf-printers.xml
   # testa se arquivo configuracao printers existe
   if [ ! -e $CONFPRINTERS ] ; then
      echo  Motor-Impressao Diz: OPS, Erro fatal, o arquivo de configuracao das printers $CONFPRINTERS nao existe, impossivel prosseguir em `date +%d.%b.%Y-%H.%M` ! >> $LOGAPP
      exit 1
   fi 
   # seta arquivo de status das printers
   ARQSTATUS=$RAIZDIR/conf/printers.status
   # testa se arquivo status printers existe
   if [ ! -e $ARQSTATUS ] ; then
      echo  Motor-Impressao Diz: OPS, Erro fatal, o arquivo de status das printers $ARQSTATUS nao existe, impossivel prosseguir em `date +%d.%b.%Y-%H.%M` ! >> $LOGAPP
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
   echo  Motor-Impressao Diz: Iniciado o serviço de impressao spoolga da G. A. Silva em `date +%d.%b.%Y-%H.%M` >> $LOGAPP
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

# faz o loop principal da app 
echo $DIRLIXO $DIRSPOOL $PARMS

echo ${nomePrinter[@]}
# // fim corpo do script