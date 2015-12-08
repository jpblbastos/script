@echo off&title    INTEGRADOR       ...:::: PORTO IDEIAS SOLUCOES ::::...

REM /**
REM *
REM * Este plugin é parte do projeto integração de ambiente heterogêneos
REM * Projetado para suprir as seguintes condições: Ser configuravél
REM *                                               Enviar o conteudo de uma pasta local para uma REMota
REM *
REM * @name           integrador.cmd
REM * @version        1.0.0
REM * @copyright      2015 &copy Porto Idéias Soluções 
REM * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
REM * @date           27-Maio-2015
REM * @description    Faz o envio por ftp de pastas para integração de ambientes heterogêneos
REM * @dependencies   secção de variaveis globais configuradas
REM *                 timeout.exe
REM *
REM * @version        2.0.0
REM * @copyright      2015 &copy Porto Idéias Soluções 
REM * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
REM * @date           27-Maio-2015
REM * @alter          Realizar a conexão/envio usando o winscp - app de conexão REMota
REM * @dependencies   secção de variaveis globais configuradas
REM *                 timeout.exe
REM *                 winscp.com - terá que salvar a conexão no programa antes de usar este
REM * @obsevation     Só altere na secção de variaveis globais
REM *
REM **/  

::::: INICIO secção de variaveis globais :::::
REM /**
REM * SLEEP
REM * Definição do path da dependencia
REM * @var string
REM **/
set SLEEP=C:\integrador\timeout32.exe

REM /**
REM * WINSCP
REM * Definição do path da dependencia
REM * @var string
REM **/
set WINSCP="C:\Arquivos de programas\WinSCP\WinSCP.com"

::::: Dados Ftp:::::
REM /**
REM * SESSION
REM * Definição da seção gravada no winscp
REM * @var string
REM **/
set SESSION=stiloweb@stiloweb.net.br

REM /**
REM * FTPCONF
REM * Definição do arquivo temporario para conexão
REM * @var string
REM **/
set FTPCONF=%TEMP%\conf_integrador.tmp

::::: Dados de pastas para manipulação :::::
REM /**
REM * PASTA_DADOS_LOCAL
REM * Definição da pasta local de dados usando curinga *
REM * @var string
REM **/
set PASTA_DADOS_LOCAL=S:\ASSAMIS\ARQ_OBJE.TXT

REM /**
REM * PASTA_IMG_LOCAL
REM * Definição da pasta local de imagens usando curinga *
REM * @var string
REM **/
set PASTA_IMG_LOCAL=S:\ASSAMIS\fotos\*.jpg

REM /**
REM * PASTA_DADOS_REMOTO
REM * Definição da pasta remota de dados 
REM * @var string
REM **/
set PASTA_DADOS_REMOTO=public_html/vesperimoveis/assamis/txt

REM /**
REM * PASTA_IMG_REMOTO
REM * Definição da pasta remota de imagens
REM * @var string
REM **/
set PASTA_IMG_REMOTO=public_html/vesperimoveis/assamis/img

REM /**
REM * PASTA_AREA_TRANSF
REM * Definição da pasta de area de transferencia das img's
REM * @var string
REM **/
set PASTA_AREA_TRANSF=C:\area_transf

::::: FIM secção de variaveis globais :::::


::::: INICIO secção principal :::::

:inicio 
CLS
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO             S I S T E M A   D E   I N T E G R A C A O   [versao 2.0.0]
ECHO. 
ECHO                                 [ AGUARDE ... ]
%SLEEP% 3 >%TEMP%\null
GOTO :envia_dados

:envia_dados
CLS
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO                  I N I C I A N D O   E N V I O   D O S   T X T  \o/
ECHO.
ECHO                                     [ AGUARDE ... ]
%SLEEP% 3 >%TEMP%\null
::::: Escreve dados para conexão ftp no arquivo tmp OBS.: NAO USE ESPACOS AQUI NA CONFIGURACAO:::::
ECHO.option batch on>%FTPCONF%
ECHO.option confirm off>>%FTPCONF%
ECHO.open %SESSION% >>%FTPCONF%
::ECHO.option transfer ascii>>%FTPCONF%
ECHO.cd %PASTA_DADOS_REMOTO%>>%FTPCONF%
ECHO.put %PASTA_DADOS_LOCAL%>>%FTPCONF%
ECHO.close>>%FTPCONF%
ECHO.exit>>%FTPCONF%
::::: Executa comando de conexão e envio :::::
%WINSCP% /console /script=%FTPCONF% /log=c:\integrador0.log 

GOTO :envia_img

:envia_img
CLS
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO                  I N I C I A N D O   E N V I O   D A S   F O T O S  \o/
ECHO.
ECHO                                      [ AGUARDE ... ]
%SLEEP% 3 >%TEMP%\null

::::: Prepara area de transferencia, carregando somente arquivos alterados da pasta local de img para seu envio :::::
::::: Usando xcopy :::::
::::: /d - Copia arquivos alterados em ou após data especificada de origem. :::::
::::: /s - Copia pastas e subpastas, a menos que estejam vazias. :::::
::::: /c - Ignora erros. :::::
XCOPY /d /s /c %PASTA_IMG_LOCAL% %PASTA_AREA_TRANSF% > %TEMP%\status && set /p st=<%TEMP%\status

::::: Verifica necesssidade de envio de arquivos :::::
IF "%st%"=="0 arquivo(s) copiado(s)" GOTO :nada_afazer

::::: Escreve dados para conexão ftp no arquivo tmp OBS.: NAO USE ESPACOS AQUI NA CONFIGURACAO:::::
ECHO.option batch on>%FTPCONF%
ECHO.option confirm off>>%FTPCONF%
ECHO.open %SESSION% >>%FTPCONF%
ECHO.option batch continue>>%FTPCONF%
::ECHO.cd %PASTA_DADOS_REMOTO%>>%FTPCONF%
::ECHO.keepuptodate -transfer=automatic>>%FTPCONF%
ECHO.synchronize both %PASTA_AREA_TRANSF% %PASTA_IMG_REMOTO%>>%FTPCONF%
ECHO.close>>%FTPCONF%
ECHO.exit>>%FTPCONF%
::::: Executa comando de conexão e envio :::::
%WINSCP% /console /script=%FTPCONF% /log=c:\integrador1.log 

GOTO :saida

:nada_afazer
CLS
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO            N E N H U M A   F O T O  N O V A  P A R A   E N V I A R :( 
%SLEEP% 5 >%TEMP%\null
GOTO :saida

:saida
CLS
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO.
ECHO                  O P E R A C O E S    R E A L I Z A D A S ;)
%SLEEP% 15 >%TEMP%\null
EXIT

::::: FIM secção principal :::::