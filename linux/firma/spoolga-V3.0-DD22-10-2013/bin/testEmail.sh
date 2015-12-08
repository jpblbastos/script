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
# * @name           testEmail
# * @version        2.0
# * @license        http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
# * @copyright      2011 &copy Twoclick Criações
# * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
# * @date           18-Fav-2013
# * @description    testa se email e valido e grava resultado em arquivo informado como parametro
# *                 EXCLUSIVO PARA UNIX HP
# * @parm           string  oEmail
# * @parm           string  arqResult
# *
# **/  

#  // propriedades do script

# /**
# * oEmail
# * Endereço a ser validado
# * @var string
# */
oEmail=

# /**
# * arqResult
# * Arquivo para grava o resultado do teste
# * @var string
# */
arqResult=

# // corpo do script

# zera variaveis
oEmail=
arqResult=

# verifica se ha paramentros suficientes
if [ $# -lt 2 ]; then
   printf "OPS, impossivel prosseguir parametros insuficientes.\n"
   printf "Usage: testEmail [oEmail] [arqResult]\n"
   exit 1
else
   oEmail=$1
   arqResult=$2
fi

# faz teste de validação
echo $oEmail |grep -E '^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)' >> /dev/null
if [ "$?" -eq 0 ] ; then
   echo 1 > $arqResult
else
   echo 0 > $arqResult
fi