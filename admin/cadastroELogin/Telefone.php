<?php

  class Telefone {
    var $id_tlf;

    var $celular;
    var $residencial;

    var $conn;

    function __construct($conn){
      $this->conn = $conn;

    }

    function cadastrar($id_usr) {

      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare("INSERT INTO telefone (tlf_cl, tlf_rs, Usuario_id_usr) VALUES (:tlf_cl, :tlf_rs, :id_usr)");
        $stmt->execute(array(":tlf_cl"=>$this->celular, ":tlf_rs"=>$this->residencial, ":id_usr"=>$id_usr));
        $this->conn->commit();

        return $stmt->rowCount();

      } catch (Exception $e) {
        $this->conn->rollback();
        die(json_encode($e->getMessage()));
      }

    }

    function alterar($id_tlf, $id_usr){

      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('UPDATE telefone SET tlf_cl = :tlf_cl, tlf_rs = :tlf_rs WHERE id_tlf = :id_tlf AND Usuario_id_usr = :id_usr');
        $stmt->execute(array(':tlf_cl'=>$this->celular, ':tlf_rs'=>$this->residencial, 'id_tlf'=>$id_tlf, 'id_usr'=>$id_usr));
        $this->conn->commit();
        return $stmt->rowCount() > 0;

      } catch (Exception $e) {
        $this->conn->rollback();
        die(json_encode($e->getMessage()));
      }

    }

    function excluir($id_tlf, $id_usr) {

      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('DELETE from telefone WHERE id_tlf = :id_tlf AND Usuario_id_usr = :id_usr');
        $stmt->execute(array('id_tlf'=>$id_tlf, 'id_usr'=>$id_usr));

        $this->conn->commit();
        return $stmt->rowCount() > 0;

      } catch (Exception $e) {
        $this->conn->rollback();
        die(json_encode($e->getMessage()));
      }

    }

    function getTelefone($id_usr){

      try {
        $stmt = $this->conn->prepare('SELECT * FROM telefone WHERE Usuario_id_usr = :id_usr');
        $stmt->execute(array(':id_usr'=>$id_usr));

        return $stmt->fetchObject();

      } catch (Exception $e) {
        die(json_encode($e->getMessage()));
      }
    }

    function getCelular(){
      return $this->celular;
    }

    function setCelular ($celular){
      $this->celular = $celular;
    }

    function getResidencial(){
      return $this->residencial;
    }

    function setResidencial ($residencial){
      $this->residencial = $residencial;
    }

  }
 ?>
