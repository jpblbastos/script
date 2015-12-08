<?php
 /**
 * Este arquivo é parte do projeto motorxml - converte xml em txt (especifico para G. A. Silva & Cia Ltda)
 *
 * Este programa é um software livre: você pode redistribuir e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU (GPL)como é publicada pela Fundação
 * para o Software Livre, na versão 3 da licença, ou qualquer versão posterior
 * e/ou 
 * sob os termos da Licença Pública Geral Menor GNU (LGPL) como é publicada pela Fundação
 * para o Software Livre, na versão 3 da licença, ou qualquer versão posterior.
 *  
 * Você deve ter recebido uma cópia da Licença Publica GNU e da 
 * Licença Pública Geral Menor GNU (LGPL) junto com este programa.
 * Caso contrário consulte <http://www.fsfla.org/svnwiki/trad/GPLv3> ou
 * <http://www.fsfla.org/svnwiki/trad/LGPLv3>. 
 *
 * Está atualizada para :
 *      PHP    5.3
 *      MYSQL  5.1 ou superior
 *
 *
 * @package   motorxml
 * @name      MotorDB
 * @version   2.0
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @copyright 2011 &copy; Eureka Soluçoes
 * @link      http://www.eurekasolucoes.com/
 * @author    Joao Paulo Bastos L. <jpbl.bastos at gmail dot com>
 *
 */
     
define("DB_HOSTI","localhost");    // host de conexão com o MySQL
define("DB_USERNAMEI","motorxml"); // nome do usuário para conexão
define("DB_PASSWORDI","motor321"); // senha do usuário para conexão
define("DB_DATABASEI","motorxml"); // nome do bd
     
class MotorDB {
    // propriedades da classe
    public $dbi;
    public $query;
     
    /**
    * open
    * Conectar ao banco de dados
    *
    * @package   motorxml
    * @name      open
    * @version   marco 1.0
    * @return    boolean true sucesso false Erro
    */
    public function open() {
         // conecta com o bd com as variáveis prédefinidas
         $this->dbi = mysql_connect(DB_HOSTI, DB_USERNAMEI, DB_PASSWORDI);
         if (!$this->dbi) {
             echo "Erro na conexão!";
	     return false;
         }
         if (!mysql_select_db(DB_DATABASEI)) {
             echo "Erro na seleção do banco de dados!";
             return false;
         }
	 return true;
    }
     
    /**
    * close
    * Fecha conexao com banco de dados
    *
    * @package   motorxml
    * @name      close
    * @version   marco 1.0
    */
    public function close(){
         mysql_close($this->dbi);
    }
     
    /**
    * query
    * Execulta a sql no DB
    *
    * @package   motorxml
    * @name      query
    * @version   marco 1.0
    * @return    boolean true sucesso false Erro
    */ 
    public function query($sql){
         if ($this->query = mysql_query($sql, $this->dbi) ) {
             return true;
	 } else {
             return false;
         }
    }

    /**
    * linhas
    * retorna quantas linhas aquela query resultou
    *
    * @package   motorxml
    * @name      linhas
    * @version   marco 1.0
    * @return    int qtd de linhas da consulta
    */ 
    public function linhas(){
         return mysql_num_rows($this->query);
    }
     
}//fim da classe
?>
