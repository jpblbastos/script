create  database motorxml;

use motorxml;

create table nf  (
   chave_nf    char(44) not null primary key,
   numero_nf   char(09) not null,
   numero_nfe  char(08) not null,
   cnpj_forn   char(14) not null,
   nome_forn   char(50) not null,
   data_emis   date not null,
   valor_tot   dec(11,2) not null,
   cstatus_nf  int default 0
)type=InnoDB;

create table admin_user (
   admin_id     int not null auto_increment primary key,
   admin_nome   char(10) not null,
   admin_passwd char(08) not null
)type=InnoDB;

GRANT SELECT, INSERT, UPDATE, DELETE ON motorxml.* to motorxml@localhost IDENTIFIED  BY 'motor321';
