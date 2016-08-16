<?php

  class Telefone {
    var $id_tlf;
    
    var $celular;
    var $residencial;

    var $conn;

    function __construct($conn){
      $this->conn = $conn

    }

    function cadastrar($usuario) {
      $stmt = $conn->prepare("INSERT INTO telefone (tlf_cl, tfl_rs, Usuario_id_usr) VALUES (:tlf_cl, :tlf_rs)");
      $stmt.execute(array(":tlf_cl"=>$this->celular, ":tlf_rs"=>$this->residencial,":usuario"=>$usuario));

    }

    function getTelefone(){
      $stmt = $this->conn->getConn()->prepare('SELECT * FROM telefone WHERE id_tlf = :id_tlf');
      $stmt->execute(array(':id_tlf'=>$this->id_tlf));
      // die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));
      return $stmt->fetchAll();;

    }

    function getCelular(){
      return $this->celular;
    }

    function setCelular ($celular){

    }

    function getResidencial(){
      return $this->residencial;
    }

    function setResidencial ($residencial){
      $this->residencial = $residencial;
    }

  }
 ?>
