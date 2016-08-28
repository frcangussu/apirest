<?php

  // namespace ConexaoPDO;
  class ConexaoPDO {

    function getConn(){
    	try {
    		$connection = new PDO('mysql:dbname=CadastroLogin;host=127.0.0.1', 'root', 'root', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    		return $connection;
    	} catch (PDOException $e) {
    		echo 'Connection failed: ' . $e->getMessage();
    	}
    }

  }

?>
