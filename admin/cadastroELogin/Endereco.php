<?php

  class Endereco {

    var $conn;

    var $quadra;
    var $rua;
    var $complemento;


    function __construct($conn){
      $this->conn = $conn;
    }

    /**
     * Cadastrar Endereco
     *
     * PHP version 5.6
     *
     * @category Endereco
     * @package  cadastroELogin
     * @author   Guilherme Dias dos Santos <guigds.dias@gmail.com>
     * @link     https://github.com/..
     * @param    int  $is_usr id usuario
     * @param    string   $complementa    descricao complemento.
     * @param    string   $quadra     descricao quadra.
     * @param    string   $rua        id usuario.
     */
    function cadastrar($id_usr){
      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('INSERT INTO Endereco (end_cpl, end_qd, end_rua, Usuario_id_usr) VALUES (:end_cpl, :end_qd, :end_rua, :id_usr)');
        $stmt->execute(array(':end_cpl'=>$this->complemento, ':end_qd'=>$this->quadra, ':end_rua'=>$this->rua, ':id_usr'=>$id_usr));
        $this->conn->commit();

        return $stmt->rowCount();

      } catch (Exception $e) {
        $this->conn->rollback();
        die(json_encode($e->getMessage()));
      }

    }

    /**
     * Alterar Endereco
     *
     * PHP version 5.6
     *
     * @category alterar
     * @package  cadastroELogin
     * @author   Guilherme Dias dos Santos <guigds.dias@gmail.com>
     * @link     https://github.com/..
     * @param    int   $id_end    id endereco
     * @param    int   $id_usr    id usuario.
     * @param    string   $complementa    descricao complemento.
     * @param    string   $quadra     descricao quadra.
     * @param    string   $rua        id usuario.
     */
    function alterar($id_end, $id_usr){
      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('UPDATE Endereco SET end_cpl= :end_cpl, end_qd= :end_qd, end_rua= :end_rua WHERE Usuario_id_usr= :id_usr AND id_end= :id_end');
        $stmt->execute(array(':end_cpl'=>$this->complemento, ':end_qd'=>$this->quadra, ':end_rua'=>$this->rua, ':id_usr'=>$id_usr, ':id_end'=>$id_end));
        $this->conn->commit();

        return $stmt->rowCount() > 0;

      } catch (Exception $e) {
        $this->conn->rollback();
        die(json_encode($e->getMessage()));
      }
    }

    /**
     * excluir Endereco
     *
     * PHP version 5.6
     *
     * @category Endereco
     * @package  cadastroELogin
     * @author   Guilherme Dias dos Santos <guigds.dias@gmail.com>
     * @link     https://github.com/..
     * @param object|array  $payload    PHP object or array
     * @param string        $key        The secret key.
     */
    function excluir($id_end, $id_usr){
      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('DELETE FROM Endereco WHERE Usuario_id_usr= :id_usr AND id_end= :id_end');
        $stmt->execute(array(':id_usr'=>$id_usr, ':id_end'=>$id_end));
        $this->conn->commit();

        return $stmt->rowCount() > 0;

      } catch (Exception $e) {
        $this->conn->rollback();
        die(json_encode($e->getMessage()));
      }
    }

    /**
     * Autenticacao de usuario
     *
     * PHP version 5.6
     *
     * @category Autenticacao
     * @package  Autenticacao
     * @author   Guilherme Dias dos Santos <guigds.dias@gmail.com>
     * @link     https://github.com/..
     * @param object|array  $payload    PHP object or array
     * @param string        $key        The secret key.
     */
    function getEndereco($id_usr){
      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('SELECT * FROM Endereco WHERE Usuario_id_usr= :id_usr');
        $stmt->execute(array(':id_usr'=>$id_usr));
        $this->conn->commit();

        return $stmt->fetchObject();

      } catch (Exception $e) {
        $this->conn->rollback();
        die(json_encode($e->getMessage()));
      }
    }

    function getQuadra(){
      return $this->quadra;
    }

    function setQuadra($quadra){
      $this->quadra = $quadra;
    }

    function getRua(){
      return $this->rua;
    }

    function setRua($rua){
      $this->rua = $rua;
    }

    function getComplemento(){
      return $this->complemento;
    }

    function setComplemento($complemento){
      $this->complemento = $complemento;
    }


  }

 ?>
