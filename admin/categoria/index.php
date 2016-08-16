<?php
use \Firebase\JWT\JWT;

error_reporting(E_ALL);
ini_set('display_errors', 1);

$loader = require __DIR__ . '/../../vendor/autoload.php';

header('Content-Type', 'application/json;charset=utf-8');

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->config('debug', true);
$app->response()->header('Content-Type', 'application/json;charset=utf-8');

function validaExecucao($rslt,$operacao){
	if ($rslt)
		die(json_encode(array('estado'=>true,'texto'=>'Operacao realizada com sucesso!')));
	else
		die(json_encode(array('estado'=>false,'texto'=>'Ops, problemas ao tentar realizar a operação de '.$operacao)));
}

function validaToken($token, $key){
	$tempo = time();
	$decoded = JWT::decode($token, $key, array('HS256'));
	// die(json_encode($decoded->exp));
	if($decoded->dados->usuario == $key && $decoded->exp > $tempo){
			return true;
	}
	return false;
}

function getConn(){
	try {
		$connection = new PDO('mysql:dbname=CadastroLogin;host=127.0.0.1', 'root', 'root', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
		$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $connection;
	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}
}

$app->get('/', function() use ($app) {

	$id = $app->request()->params('id');
	$where = ($id) ? "WHERE ID = $id" : "";

	$sql = "SELECT * FROM categoria $where order by ordem";

	try {
		$categorias = getConn()->query($sql)->fetchAll(PDO::FETCH_OBJ);
		echo json_encode($categorias);
	} catch (Exception $e) {
		echo json_encode($e);
	}
});

$app->post('/deletar', function() use ($app) {

	$request = json_decode($app->getInstance()->request()->getBody());

	try{
		$stmt = getConn()->prepare("DELETE FROM categoria WHERE ID = :id");
		$rslt = $stmt->execute(array(":id"=>$request->id));
		validaExecucao($rslt,"Exclusão");
	} catch (PDOException $e) {
		die (json_encode(array('estado'=>false,'texto'=>$e->getMessage())));
	}
});

$app->post('/inserir',function() use ($app){

	// max da ordem
	$max = getConn()->query("SELECT 1 + MAX(coalesce(ordem,0)) as ordem  FROM categoria")->fetchAll(PDO::FETCH_OBJ);

	// recebe os parâmetros
	$request = json_decode($app->getInstance()->request()->getBody());
	$params = array( ":nome"=>$request->nome,
					 ":cor"=>$request->cor->cor,
					 ":fa_icone"=>$request->fa_icone,
					 ":ordem"=>$max[0]->ordem
	);

	try {

		$stmt = getConn()->prepare("INSERT INTO categoria (nome, cor, icone, ordem) VALUES (:nome, :cor, :fa_icone, :ordem)");
		$rslt = $stmt->execute($params);
		validaExecucao($rslt,"Inclusão");

	} catch (Exception $e) {
		die (json_encode(array('estado'=>false,'texto'=>$e->getMessage())));
	}
});

$app->post('/alterar',function() use ($app){
	$request = json_decode($app->getInstance()->request()->getBody());
	// die(json_encode($request));

	$params = array( ":id"=>$request->id,
					 ":nome"=>$request->nome,
					 ":cor"=>$request->cor,
					 ":icone"=>$request->fa_icone
	);
	try {
		$stmt = getConn()->prepare("UPDATE categoria set nome=:nome, cor=:cor, icone=:icone where id=:id");
		$rslt = $stmt->execute($params);
		validaExecucao($rslt,"Alteração");
	} catch (Exception $e) {
		die (json_encode(array('estado'=>false,'texto'=>$e->getMessage())));
	}

});

$app->post('/ordenar',function() use ($app){
	$request = json_decode($app->getInstance()->request()->getBody());
	$params = array( ":id"=>$request->id,":ordem"=>$request->ordem);
	try {
		$stmt = getConn()->prepare("UPDATE categoria set ordem=:ordem where id=:id");
		$rslt = $stmt->execute($params);
		validaExecucao($rslt,"Ordenação");
	} catch (Exception $e) {
		die (json_encode(array('estado'=>false,'texto'=>$e->getMessage())));
	}

});

//----------------------- Cadastra usuário -----------------------------

$app->post('/cadastrar_usuario',function() use ($app){
	$request = json_decode($app->getInstance()->request()->getBody());
	$params = array(":nome"=>$request->usuario->nome,
	 								":email"=>$request->usuario->email,
								  ":senha"=>$request->usuario->senha,
									":sobrenome"=>$request->usuario->sobrenome
									);

	// die(json_encode($request));

	$conn = getConn();
	$conn->beginTransaction();

	try {

		//Cadastra os dados na tabela de Usuario
		$stmt = $conn->prepare("INSERT INTO Usuario (usr_nm, usr_email, usr_sn, usr_sbnm) VALUES (:nome,:email,:senha,:sobrenome)");
		$stmt->execute(array(":nome"=>$request->usuario->nome,":email"=>$request->usuario->email,":senha"=>sha1($request->usuario->senha),":sobrenome"=>$request->usuario->sobrenome));
		$idUsr = $conn->lastInsertId();

		//Cadastra os dados da na tabela de telefone
		$stmt = $conn->prepare("INSERT INTO telefone (Usuario_id_usr, tlf_cl, tlf_rs) VALUES (:usuario,:celular,:residencial)");
		$stmt->execute(array(":usuario"=>$idUsr, ":celular"=>$request->telefone->celular,":residencial"=>$request->telefone->residencial));

		//Cadastra os dados na tabela de endereco
		$stmt = $conn->prepare("INSERT INTO Endereco (end_cs, end_qd, end_rua, Usuario_id_usr) VALUES (:complemento,:quadra,:rua,:idUsr)");
		$stmt->execute(array(":complemento"=>$request->endereco->complemento,":quadra"=>$request->endereco->quadra,":rua"=>$request->endereco->rua,":idUsr"=>$idUsr));

		$conn->commit();

		die(json_encode(array('estado'=>true,'texto'=>'Cadastro realizada com sucesso!')));
	} catch (Exception $e) {
		$conn->rollback();
		die (json_encode(array('estado'=>false,'texto'=>$e->getMessage())));
	}

});

//----------------------- Autenticacao -----------------------------

$app->get('/atenticar_usuario', function () use ($app) {

	$request = json_decode($app->getInstance()->request()->getBody());
	// $email = $app->request()->params('email');
	// $senha = $app->request()->params('senha');
	die(json_encode($request));
	$email = $request->email;
	$senha = $request->senha;

	// die(json_encode(sha1($senha)));

	try {
		$stmt = getConn()->prepare('SELECT * FROM Usuario WHERE usr_email = :email AND usr_sn = :senha');
		$stmt->execute(array(':email'=>$email, ':senha'=>sha1($senha)));
		// die(json_encode($stmt->fetchAll(PDO::FETCH_COLUMN)));
		$rslt = $stmt->fetchAll();
		// die(json_encode($rslt[0][2]));
		if(sizeof($rslt) > 0){
			$key = $email;
			$tempo = time();
			// die(json_encode($tempo));
			$token = array(
			    "iat" => $tempo,
					"iss" => "http://localhost",
					"exp" => $tempo + 1200,
					"dados" => [
						"usuario" =>  $rslt[0][2],
						"sobrenome" => $rslt[0][4]
					]
				);

			$jwt = JWT::ENCODE($token, $key);
			// $valido = validaToken($jwt, $email);
			// $decoded = JWT::decode($jwt, $key, array('HS256'));
			// die(json_encode($valido));
			die(json_encode(array("nome"=>$rslt[0][1], "sobrenome"=>$rslt[0][4], "token"=>$jwt)));

		} else {
			die (json_encode(array('estado'=>false,'texto'=>'Usuario ou senha invalida.')));
		}

	} catch (Exception $e) {
		die (json_encode(array('estado'=>false,'texto'=>$e->getMessage())));
	}


});

$app->put('/ordena/:ini_pos',function($ini_pos) use ($app) {

	$messages = [];
	try {
		$request = json_decode($app->getInstance()->request()->getBody());

		try {
			// update categoria set ordem = 99999999999 where ordem = ini_pos
			$params = array( ":ini_pos"=>$ini_pos, ":aux"=>"999999");
			$stmt = getConn()->prepare("UPDATE categoria set ordem = :aux where ordem = :ini_pos");
			array_push($messages, "UPDATE categoria set ordem = ".$params[":aux"]." where ordem = ".+$params[":ini_pos"]);
			$rslt = $stmt->execute($params);
			if (!$rslt)
				throw new Exception('Primeiro update não funcionou');

		} catch (Exception $e) {
			die(json_encode(array('estado'=>false,'texto'=>"1) ".$e->getMessage())));
		}

		try {

			$params = array( ":ini_pos"=>$ini_pos, ":fin_pos"=>$request->fin_pos);

			// 							update categoria set ordem = ordem+1 where ordem >=  ini_pos and ordem <=  fin_pos
			if ($ini_pos < $request->fin_pos){
				$stmt = getConn()->prepare("UPDATE categoria set ordem = ordem-1 where ordem > :ini_pos and ordem <= :fin_pos");
				array_push($messages, "UPDATE categoria set ordem = ordem-1 where ordem >= ".$params[":ini_pos"]." and ordem <= ".$params[":fin_pos"]);

			} else {
				$stmt = getConn()->prepare("UPDATE categoria set ordem = ordem+1 where ordem > :fin_pos and ordem <= :ini_pos");
				array_push($messages, "UPDATE categoria set ordem = ordem+1 where ordem >= ".$params[":fin_pos"]." and ordem <= ".$params[":ini_pos"]);
			}

			$rslt = $stmt->execute($params);
			if (!$rslt)
				throw new Exception('Segunto update não funcionou');

		} catch (Exception $e) {
			die(json_encode($params));
		}

		try{

			// 	update categoria set ordem =  fin_pos where ordem = 99999999999
			$params = array(":fin_pos"=>$request->fin_pos, ":aux"=>"999999");
			$stmt = getConn()->prepare("UPDATE categoria set ordem = :fin_pos where ordem = :aux");
			array_push($messages, "UPDATE categoria set ordem = ".$params[":fin_pos"]." where ordem = ".$params[":aux"]);
			$rslt = $stmt->execute($params);
			if (!$rslt)
				throw new Exception('Terceiro update não funcionou');

		} catch (Exception $e) {
			die(json_encode(array('estado'=>false,'texto'=>"3) ".$e->getMessage())));
		}

		array_push($messages,"Dados alterados com sucesso");
		echo json_encode(array('estado'=>true,'texto'=>$messages));

	} catch (Exception $e) {
		echo json_encode(array('estado'=>false,'texto'=>$e->getMessage()));
	}
});

$app->run();
