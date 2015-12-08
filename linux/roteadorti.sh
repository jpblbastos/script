#!/bin/bash
# ------------------------------------------------ #
#               Administracao ti Labs              #
# ------------------------------------------------ #
# @name    : roteadorti                            #
# @desc    : faz o roteamento entre redes          #
# @author  : Joao Paulo                            #
# @date    : 08-09-2011                            #
# ------------------------------------------------ #
#                  Alteracoes                      #
#                                                  #
# data         alteracao                 por       #
#                                                  #
# ------------------------------------------------ #

printf "Iniciando o iptable_nat ... \n"
modprobe iptable_nat           

printf "Setando como true o ip_forward ... \n"
echo 1 > /proc/sys/net/ipv4/ip_forward

printf "Aplicando regras gerais do firewall ... \n"
iptables -t nat -A POSTROUTING -o eth0 -j MASQUERADE
iptables -t nat -A POSTROUTING -o eth1 -j MASQUERADE
