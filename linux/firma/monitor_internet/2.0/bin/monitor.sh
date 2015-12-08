#!/bin/bash
# ------------------------------------------------------- #
#         G. A. Silva e Cia Ltda                          #
#                                                         #
# Script: monitor.sh                                      #
# Desc..: inicia / reinicia / para, o monitor de internet #
# Parm..: monitor start/restart/stop                      #
# Por...: João Paulo                                      #
# Data..: 26-04-2011                                      #
# ------------------------------------------------------- #


#------Variavel de resposta-----#
resp=$1

#------Start OObj-------#
if [ "$resp" = "start" ]; then
  clear
  echo 'Iniciando o Servico'
  sleep 5
  php monitor.php

#-------Stop OObj---#
elif [ "$resp" = "stop" ]; then
  clear
  echo 'Parando o Servico'
  sleep 5
  killall php 

#-----Restart OObj---#
elif [ "$resp" = "restart" ]; then
  clear
  echo 'Parando o Servico'
  killall php 
  sleep 5
  echo ''
  echo ''
  echo 'Iniciando o Servico'
  sleep 5
  php monitor.php

#----Opcao Invalida--------#
else
  clear
  echo 'OPS, Opcao invalida, desculpe.....'
fi
