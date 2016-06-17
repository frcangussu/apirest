<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$loader = require __DIR__ . '/../../vendor/autoload.php';

header('Content-Type', 'application/json;charset=utf-8');

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8');

function validaExecucao($rslt,$operacao){
	if ($rslt)
		die(json_encode(array('estado'=>true,'texto'=>'Operacao realizada com sucesso!')));
	else
		die(json_encode(array('estado'=>false,'texto'=>'Ops, problemas ao tentar realizar a operação de '.$operacao)));
}

function getConn(){
	try {
		$connection = new PDO('mysql:dbname=portalmangueiral;host=127.0.0.1', 'root', '123456', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
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

			// 							update categoria set ordem =  fin_pos where ordem = 99999999999
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
