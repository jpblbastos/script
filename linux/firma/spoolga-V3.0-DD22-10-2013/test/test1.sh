#!/bin/bash

: ${sysconfig_spoolga:=/home/jota/Trabalhando_Nisso/motor-impressao/conf/varf_globais}
test -e $sysconfig_spoolga || echo siu exit 4
test -x $sysconfig_spoolga || exit 4
