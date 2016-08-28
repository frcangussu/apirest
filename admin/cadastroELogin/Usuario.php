<?php

  // include("Autenticacao.php");
  class Usuario {
    var $conn;

    var $id_usr;

    var $nome;
    var $sobrenome;
    var $email;
    var $senha;

    var $isFace;
    var $isGoogle;

    var $tipoCadastro;


    function __construct($conn) {
      //Cria uma nova conexão
      $this->conn = $conn;
    }

    function cadastrar(){

      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('INSERT INTO Usuario (usr_nm, usr_email, usr_sbnm, usr_sn, usr_cdt_face, usr_cdt_google) VALUES (:usr_nm, :usr_email, :usr_sbnm, :usr_sn, :usr_isFace, :usr_isGoogle)');
        $stmt->execute(array("usr_nm"=>$this->nome, ":usr_email"=>$this->email, ":usr_sbnm"=>$this->sobrenome, ":usr_sn"=>$this->senha, ":usr_isFace"=>$this->isFace, ":usr_isGoogle"=>$this->isGoogle));
        $this->conn->commit();

      } catch (Exception $e) {
        $this->conn->rollback();
        die(json_encode($e->getMessage()));

      }

      return $stmt->rowCount() > 0;

    }

    function alterar(){

      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('UPDATE Usuario SET usr_nm =:nome, usr_email= :email, usr_sbnm=: sobreNome, usr_sn= :senha, usr_cdt_face: :isFace, usr_cdt_google= :isGoogle WHERE usr_id = :id_usr');
        $rstl = $stmt->execute(array(':nome'=>$this->nome, ':email'=>$this->email, ':sobreNome'=>$this->senha, ':senha'=>$this->senha, ':isFace'=> $this->isFace, ':isGoogle'=> $this->isGoogle, ':id_usr'=> $this->id_usr));
        $this->conn->commit();

      } catch (Exception $e) {
        $this->conn->rollback();
        throw new Exception("Error Processing Request", e.getMessage());

      }

      return $rstl->rowCount();
    }

    function excluir(){
      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('DELETE FROM Usuario WHERE usr_id = :id_usr');
        $rstl = $stmt->execute(array(':id_usr'=>$this->id_usr));

      } catch (Exception $e) {
        $this->conn->rollback();
        throw new Exception("Error Processing Request", e.getMessage());

      }

      return $rstl->rowCount();
    }

    function getUsuarioForId(){
      $stmt = $this->conn->prepare('SELECT * FROM Usuario WHERE id_usr = :id_usr');
      $stmt->execute(array(':id_usr'=>$this->id_usr));
      // die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));
      return $stmt->fetchObject();
    }

    function getUsuarioForEmail(){
      $stmt = $this->conn->prepare('SELECT * FROM Usuario WHERE usr_email = :usr_email');
      $stmt->execute(array(':usr_email'=>$this->email));
      return $stmt->fetchObject();
    }

    function getNome(){
      return $this->email;
    }

    function setNome($nome){
      $this->nome = $nome;
    }

    function getSobrenome(){
      return $this->sobrenome;
    }

    function setSobreNome($sobrenome){
      $this->sobrenome = $sobrenome;
    }

    function getEmail(){
      return $this->email;
    }

    function setEmail($email){
      $this->email = $email;
    }

    function getSenha(){
      return $this->senha;
    }

    function setSenha($senha){
      $this->senha = $senha;
    }

    function isFace(){
      return $this->isFace;
    }

    function setIsFace($isFace){
      $this->isFace = $isFace;
    }

    function isGoogle(){
      return $this->isGoogle;
    }

    function setIsGoogle($isGoogle){
      $this->isGoogle = $isGoogle;
    }

    function setTipoCadastro($tipoCadastro){
      $this->tipoCadastro = $tipoCadastro;
    }



  }

 ?>
