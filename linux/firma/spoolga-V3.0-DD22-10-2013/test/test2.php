<?php
if (!defined('PATH_ROOT')) {
   define('PATH_ROOT', dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR);
}

//carrega as classes do MailPedPHP.class.php
if ( is_file(PATH_ROOT.'libs/processorPdf.class.php') ){
   require_once(PATH_ROOT.'libs/processorPdf.class.php');
}else{
   echo "OPS, arquivo MailPedPHP.class.php nao encontrado, impossivel de prosseguir!!\n";
   exit;
}

   $oPdf = new processorPdf(0,'/mnt/box/printers/pdf/151620.imp');
   if ($oPdf->error){
      echo "errro no construtor\n";
      echo $oPdf->errorClass ."\n";
      exit;
   }else{
      echo "o documento sera convertido \n";
      if ($oPdf->convertTxttoPdf())
          echo "eba, ate que enfim deu certo, pode trabalhar no processor\n";
   }
   $oPdf->__showDados();
   echo $oPdf->__setDir();
  
?>