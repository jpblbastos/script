#!/bin/bash
# ---------------------------------- #
# Nome: Desliga Pc                   #
# Por : Joao Paulo                   #
# Desc: Avisa o desligamento, caso   #
#       nao cancelado ele se desliga #
# Alt : 09-02-11                     #
# ---------------------------------- #


# --------- Aviso de Desligamento ----------- #
zenity   --title="Presta Atenção Jota" \
         --timeout=120 \
         --question \
         --width=2 \
         --window-icon=/home/jota/Icones/Pardus-Milky-2/actions/system-shutdown.png \
         --text "Ops, Seu micro sera Desligado, By By ....."

if [ "$?" = "1" ]; then
   exit 0
fi

# ------- Progress de Desligamento ------------#
 (
        echo "10" ; sleep 1
        echo "# Desligamento do Sistema Ubuntu..." ; sleep 1
        echo "20" ; sleep 1
        echo "# Salve todos seus arquivos..." ; sleep 1
        echo "50" ; sleep 1
        echo "# Feche todos os programas abertos... " ; sleep 1
        echo "75" ; sleep 1
        echo "# Foi bom trabalhar com voçê hoje ..." ; sleep 1
        echo "100" ; sleep 60
        sudo  shutdown -h now 
        ) |
        zenity --progress \
          --title="Estou Desligando " \
          --text="O Desligamento Ativado......." \
          --percentage=0
