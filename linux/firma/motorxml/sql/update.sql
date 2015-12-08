use motorxml;

create table black_list  (
   id_list     int not null auto_increment primary key,
   cnpj_list   char(14) not null
)type=InnoDB;