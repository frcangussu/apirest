<?php
  use \Firebase\JWT\JWT;
  // namespace Autenticacao;
  class Autenticacao {

    var $conn;

    function __construct($conn){
      $this->conn = $conn;
    }

  //---------------------- AUTENTICAR USUARIO ----------------------------------

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
   function autenticar_usuario($email, $senha){

     try {
   		$stmt = $this->conn->prepare('SELECT * FROM Usuario WHERE usr_email = :usr_email AND usr_sn = :usr_sn');
   		$stmt->execute(array(':usr_email'=>$email, ':usr_sn'=>sha1($senha)));
   		// die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));
   		$rslt = $stmt->fetchAll();
   		// die(json_encode($rslt[0][2]));
 			$token = $this->gerarToken($email, $rslt[0][4]);
      return $token;

   	} catch (Exception $e) {
      // die(array("messages"=>"Usuario inválido"));
   		throw new Exception("Usuario inválido", "Usuario invalido!");

   	}

   }

   //----------------------- Verifica permissões -------------------------------

   /**
    * Verifiacao de permissoes do usuario
    *
    * PHP version 5.6
    *
    * @category Autenticacao
    * @package  Autenticacao
    * @author   Guilherme Dias dos Santos <guigds.dias@gmail.com>
    * @link     https://github.com/..
    */
   function tem_permissao(){


   }

   //----------------------- GERAR TOKEN ---------------------------------------

   /**
    * Gera token de usuario cadastrado pelo aplicativo
    *
    * PHP version 5.6
    *
    * @category Autenticacao
    * @package  Autenticacao
    * @author   Guilherme Dias dos Santos <guigds.dias@gmail.com>
    * @link     https://github.com/..
    * @param    object  $usuario
    * @param    string  $sobrenome
    */
   function gerarToken ($usuario, $sobrenome) {
     $tempo = time();
     // die(json_encode($tempo));
     $token = array(
         "iat" => $tempo, // timestamp que foi criado o token
         "iss" => "http://localhost", //ulr de acesso
         "exp" => $tempo + 1200, // timestamp que o tokem valerá
         "dados" => [
           "usuario" =>  $usuario,
           "sobrenome" => $sobrenome
         ]
       );

       $key = sha1($usuario . "portalMangueiral" . $sobrenome);
       $rtn = JWT::ENCODE($token, $key);
      return $rtn;
   }

   //--------------------- AUTENTICAR USUARIO ----------------------------------

   /**
    * Válida token gerado pelo aplicativo
    *
    * PHP version 5.6
    *
    * @category Autenticacao
    * @package  Autenticacao
    * @author   Guilherme Dias dos Santos <guigds.dias@gmail.com>
    * @link     https://github.com/..
    * @param    String  $token
    * @param    String  $usuario
    * @param    string  $sobrenome
    */
   function validaToken($token, $usuario, $sobrenome){
     $tempo = time();

     $key = sha1($usuario . "portalMangueiral" . $sobrenome);

     $decoded = JWT::decode($token, $key, array('HS256'));
     // die(json_encode($decoded->exp));
     if($decoded->dados->usuario == $usuario && $decoded->exp > $tempo){
       return true;
     }

     return false;
   }

 }

 ?>
