#!/bin/bash

# /**
# * sysconfig_spoolga
# * arquivo de variaveis globais
# * @var int
# */
: ${sysconfig_spoolga:=/home/jota/Trabalhando_Nisso/spoolga/conf/var_globais}

. $sysconfig_spoolga

$SERVICESACCOUNT_BIN --impressions --email --display



   


