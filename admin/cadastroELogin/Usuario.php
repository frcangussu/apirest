<?php

  // include("Autenticacao.php");
  class Usuario {
    var $conn;

    var $id_usr;

    var $nome;
    var $sobrenome;
    var $email;
    var $senha;
    var $telefone;
    var $endereco;


    function __construct($conn) {
      //Cria uma nova conexão
      $this->conn = $conn;
    }

    function cadastrar($tipoCadastro) {

      if ($tipoCadastro == 1 || $tipoCadastro == 2) { // Verifica se é cadastro pelo Facebook ou Google

        $rslt = getUsuarioForEmail(); // Verifica se já existe usuario no app.
        if (sizeof($rslt) == 0) {
          cadastrar_usuario();

        } else {


          }

        }

      } else {

        $rslt = getUsuarioForEmail(); // Verifica se já existe usuario no app.
        $rsltFace = getUsuarioForReferenciaFacebookOurGoogle($tipoCadastro);

        if ( sizeof($rslt) == 0 ) {
          cadastrar_usuario();

        } else if( sizeof($rsltFace) == 0 ) {

        }

        die(json_encode(array("mensagem"=>'Usuário já cadastrado.', "TIPO"=>'Error')));


      }

    }

    function alterar(){

    }

    function excluir(){

    }

    function getUsuarioForId(){
      $stmt = $this->conn->getConn()->prepare('SELECT * FROM Usuario WHERE id_usr = :id_usr');
      $stmt->execute(array(':id_usr'=>$this->id_usr));
      // die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));
      return $stmt->fetchAll();;
    }

    function getUsuarioForEmail(){
      $stmt = $this->conn->getConn()->prepare('SELECT * FROM Usuario WHERE usr_email = :usr_email');
      $stmt->execute(array(':usr_email'=>$this->email));
      // die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));
      return $stmt->fetchAll();;
    }

    function getUsuarioForReferenciaFacebookOurGoogle($tipoCadastro){

      if($tipoCadastro == 1){// É cadastro pelo Facebook

        $this->senha = sha1("Facebook");
        $this->sobrenome = "";

        $stmt = $this->conn->getConn()->prepare('SELECT * FROM info_face WHERE face_usr = :face_usr');
        $stmt->execute(array(':face_usr'=>$this->email));

        // die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));

      } else {

        $this->senha = sha1("Google");
        $this->sobrenome = "";

        $stmt = $this->conn->getConn()->prepare('SELECT * FROM info_google WHERE goo_usr = :goo_usr');
        $stmt->execute(array(':goo_usr'=>$this->email));
        // die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));

      }

      return $stmt->fetchAll();;
    }

    function cadastrar_usuario(){

      $this->conn->beginTransaction();

      try {
        $stmt = $this->conn->prepare('INSERT INTO Usuario (usr_nm, usr_email, usr_sbnm, usr_sn) VALUES (:usr_nm, :usr_email, :usr_sbnm, :usr_sn)');
        $stmt->execute(array("usr_nm"=>$this->nome, ":usr_email"=>$this->email, ":usr_sbnm"=>$this->sobrenome, "usr_sn"=>$this->senha));
        $this->conn->commit();
        return true;

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

  }

 ?>
