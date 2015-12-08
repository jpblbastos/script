<?php

if (!defined('PATH_ROOT')) {
   define('PATH_ROOT', dirname(dirname( __FILE__ )) . DIRECTORY_SEPARATOR);
}

//carrega as classes do MailPedPHP.class.php
if ( is_file(PATH_ROOT.'libs/docPdf.class.php') ){
   require_once(PATH_ROOT.'libs/docPdf.class.php');
}else{
   echo "OPS, arquivo docPdf.class.php nao encontrado, impossivel de prosseguir!!\n";
   exit;
}

   //$data= file();
   $oPdf = new docPdf();
   $oPdf->convertTxttoPdf();
      

?>