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

    function cadastrar() {

        if($this->tipoCadastro == 1){ // Cadastro pelo Facebook
          $this->isFace = true;
          $this->senha = 'Facebook';
          $this->sobrenome = '';

        }else if($this->tipoCadastro == 2){ // Cadastro pelo Google
          $this->isGoogle = true;
          $this->senha = 'Google';
          $this->sobrenome = '';

        } else { // Cadastro normal
            $this->isFace = false;
            $this->isGoogle = false;
        }

        $rslt = getUsuarioForEmail(); // Verifica se já existe usuário no app.
        if ( sizeof($rslt) == 0 ){
          try {
              cadastrar_usuario();
              return true;
          } catch (Exception $e) {
              die(json_encode(array("mensagem"=>$e->getMessage(), "TIPO"=>'Error')));
          }
        }

        return false
    }

    function alterar(){

    }

    function excluir(){

    }

    function getUsuarioForId(){
      $stmt = $this->conn->getConn()->prepare('SELECT * FROM Usuario WHERE id_usr = :id_usr');
      $stmt->execute(array(':id_usr'=>$this->id_usr));
      // die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));
      return $stmt->fetchAll();
    }

    function getUsuarioForEmail(){
      $stmt = $this->conn->getConn()->prepare('SELECT * FROM Usuario WHERE usr_email = :usr_email');
      $stmt->execute(array(':usr_email'=>$this->email));
      // die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));
      return $stmt->fetchAll();
    }

    function cadastrar_usuario(){

      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('INSERT INTO Usuario (usr_nm, usr_email, usr_sbnm, usr_sn,usr_cdt_face, usr_cd_google) VALUES (:usr_nm, :usr_email, :usr_sbnm, :usr_sn, :usr_isFace, :usr_isGoogle)');
        $stmt->execute(array("usr_nm"=>$this->nome, ":usr_email"=>$this->email, ":usr_sbnm"=>$this->sobrenome, "usr_sn"=>$this->senha, "usr_isFace"=>$this->isFace, "usr_isGoogle"=>$this->isGoogle));
        $this->conn->commit();
      } catch (Exception $e) {
        $this->conn->rollback();
        throw new Exception("Error Processing Request", 'Erro ao cadastrar');

      }

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

    function getTelefone(){
      return $this->telefone;
    }

    function setTelefone($telefone){
      $this->telefone = $telefone;
    }

    function setTipoCadastro($tipoCadastro){
      $this->tipoCadastro = $tipoCadastro;
    }

  }

 ?>
