#!/bin/sh
# /**
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
# * @package        topdf
# * @name           topdf.sh 
# * @version        0.1
# * @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
# * @copyright      2014 &copy Porto Ideias 
# * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
# * @date           25-Fevereiro-2014
# * @description    Escreve cabeçalho e envia o arquivo para processamento pdf do spoolga
# *                 . Especifico para compatibilidade pfd para cobol 
# *
# **/  


# // verifica passagem de parametro 
if [ $# -le 0 ]; then
        printf "OPS, Por favor especifique um arquivo para converssao !"
        exit 1
fi

#  // propriedades do script

# /**
# * nameArq
# * Parametro com o nome do arquivo a ser manipulado
# * @var String
# */
nameArq=$1
if [ ! -e $nameArq ]; then 
   echo "OPS, arqvivo $nameArq nao existe impossivel continuar !" 
   exit 1
fi

# /**
# * compress
# * Parametro de comprenssao da converssao
# * @var String
# */
compress=$2
if [ -z $compress ]; then
   compress=5
fi

# /**
# * nameNew
# * Novo nome para o arquivo a ser convertido
# * @var String
# */
nameNew=`date +%N`.imp

# /**
# * user
# * Nome do usuario
# * @var String
# */
user=`whoami`

# /**
# * cabecalho
# * Dados para converssao
# * @var String
# */
cabecalho=`echo A\|$user\|9999\|$compress`

# /**
# * nextPage
# * Informa proxima pagina
# * @var String
# */
nextPage="NP|"

# /**
# * dirTagert
# * Diretorio de destino
# * @var String
# */
dirTagert=/home/jota/tmp

# // fim das propriedades do script


# // corpo do script
echo $cabecalho > $nameNew # // escreve cabeçalho de infos
echo $nextPage  >>$nameNew # // cria pagina
cat  $nameArq   >>$nameNew # // move conteudo  

# envia arquivo
if mv -f $nameNew $dirTagert ; then
   rm -f $nameArq
else
   echo "OPS, erro no envio do $nameNew para motor converssao !"
   exit 1
fi
# // fim corpo do script