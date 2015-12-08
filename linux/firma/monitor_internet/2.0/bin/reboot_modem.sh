#!/usr/bin/expect --
# ---------------------------------------------------------------------- #
#                    G. A. Silva & Cia Ltda                              #
#                                                                        #
#  Nome: reboot_modem.sh                                                 #
#  Desc: reinicia modem da internet com conexão telnet                   #
#        usando a biblioteca expect como interpretadora                  #
#  Por.: João Paulo                                                      # 
#  Data: 25-04-2011                                                      #
#  Obs : Antes de usar e necessario instalar a biblioteca expect         #
#                  apt-get install expect                                #
# ---------------------------------------------------------------------- #
set timeout 900
spawn telnet 128.1.0.20

expect "login:"
send "admin\r"

expect "password:"
send "admin\r"

expect "xsh> "
send "do reboot\r"

expect "xsh> "
send "exit\r"

close
wait
