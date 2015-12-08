@echo off&title    INTEGRADOR       ...:::: PORTO IDEIAS SOLUCOES ::::...

rem /**
rem *
rem * Este plugin é parte do projeto integração de ambiente heterogêneos
rem * Projetado para suprir as seguintes condições: Ser configuravél
rem *                                               Enviar o conteudo de uma pasta local para uma remota
rem *
rem * @name           integrador.cmd
rem * @version        1.0.0
rem * @copyright      2015 &copy Porto Idéias Soluções 
rem * @author         Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
rem * @date           27-Maio-2015
rem * @description    Faz o envio por ftp de pastas para integração de ambientes heterogêneos
rem * @dependencies   secção de variaveis globais configuradas
rem *                 timeout.exe
rem * @obsevation     Só altere na secção de variaveis globais
rem *
rem **/  

::::: INICIO secção de variaveis globais :::::
rem /**
rem * SLEEP
rem * Definição do path da dependencia
rem * @var string
rem **/
set SLEEP=C:\Users\Jota\Desktop\Projetos\stiloweb\timeout.exe


::::: Dados Ftp:::::
rem /**
rem * HOST
rem * Definição do host para conecção
rem * @var string
rem **/
set HOST=uyara.com.br

rem /**
rem * USER
rem * Definição do usuario para conecção
rem * @var string
rem **/
set USER=uyara

rem /**
rem * PASSWD
rem * Definição da senha para conecção
rem * @var string
rem **/
set PASSWD=a1b2c3d4e5

rem /**
rem * FTPCONF
rem * Definição do arquivo temporario para conecção
rem * @var string
rem **/
set FTPCONF=%TEMP%\conf_integrador.tmp

rem /**
rem * EXECON
rem * Executa conecção
rem * @var string
rem **/
set EXECON=FTP -i -s:%FTPCONF%

::::: Dados de pastas para manipulação :::::
rem /**
rem * PASTA_DADOS_LOCAL
rem * Definição da pasta local de dados usando curinga *
rem * @var string
rem **/
set PASTA_DADOS_LOCAL=C:\yuara_base\txt\*

rem /**
rem * PASTA_IMG_LOCAL
rem * Definição da pasta local de imagens usando curinga *
rem * @var string
rem **/
set PASTA_IMG_LOCAL=C:\yuara_base\img\*

rem /**
rem * PASTA_DADOS_REMOTO
rem * Definição da pasta remota de dados 
rem * @var string
rem **/
set PASTA_DADOS_REMOTO=txt

rem /**
rem * PASTA_IMG_REMOTO
rem * Definição da pasta remota de imagens
rem * @var string
rem **/
set PASTA_IMG_REMOTO=img

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
ECHO             S I S T E M A   D E   I N T E G R A C A O   [versao 1.0.0]
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
ECHO                  I N I C I A N D O   E N V I O   D O S   T X T 
ECHO.
ECHO                                  [ AGUARDE ... ]
%SLEEP% 3 >%TEMP%\null
::::: Escreve dados para conecção ftp no arquivo tmp OBS.: NAO USE ESPACOS AQUI NA CONFIGURACAO:::::
ECHO.open %HOST%>%FTPCONF%
ECHO.%USER%>>%FTPCONF%
ECHO.%PASSWD%>>%FTPCONF%
ECHO.ascii>>%FTPCONF%
ECHO.cd %PASTA_DADOS_REMOTO%>>%FTPCONF%
ECHO.mput %PASTA_DADOS_LOCAL%>>%FTPCONF%
ECHO.quit>>%FTPCONF%
::::: Executa comando de conecção e envio :::::
%EXECON%

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
ECHO                  I N I C I A N D O   E N V I O   D A S   F O T O S 
ECHO.
ECHO                                  [ AGUARDE ... ]
%SLEEP% 3 >%TEMP%\null
::::: Escreve dados para conecção ftp no arquivo tmp OBS.: NAO USE ESPACOS AQUI NA CONFIGURACAO:::::
ECHO.open %HOST%>%FTPCONF%
ECHO.%USER%>>%FTPCONF%
ECHO.%PASSWD%>>%FTPCONF%
ECHO.binary>>%FTPCONF%
ECHO.cd %PASTA_IMG_REMOTO%>>%FTPCONF%
ECHO.mput %PASTA_IMG_LOCAL%>>%FTPCONF%
ECHO.quit>>%FTPCONF%     
::::: Executa comando de conecção e envio :::::
%EXECON%

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
%SLEEP% 5 >%TEMP%\null
EXIT

::::: FIM secção principal :::::