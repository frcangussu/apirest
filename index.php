<<<<<<< HEAD
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$loader = require __DIR__ . '/vendor/autoload.php';

header('Content-Type', 'application/json;charset=utf-8');

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8');


function getConn(){
	try {
		return new PDO('mysql:dbname=CadastroLogin;host=127.0.0.1', 'root', 'root', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}
}

$app->get('/', function () {
	echo "principal";
});

$app->get('/categoria', function (){
	try {
		$categorias = getConn()->query("SELECT * FROM categoria")->fetchAll(PDO::FETCH_OBJ);
		echo "{categorias:".json_encode($categorias)."}";
	} catch (PDOException $e){
		print_r($e);
	} catch (Exception $ex) {
		print_r($ex);
	}
});

$app->get('/cadastrar-usuario', function (){
	try {
		$categorias = getConn()->query("SELECT * FROM categoria")->fetchAll(PDO::FETCH_OBJ);
		echo "{categorias:".json_encode($categorias)."}";
	} catch (PDOException $e){
		print_r($e);
	} catch (Exception $ex) {
		print_r($ex);
	}
});

$app->get('/logar', function (){
	try {
		$categorias = getConn()->query("SELECT * FROM categoria")->fetchAll(PDO::FETCH_OBJ);
		echo "{categorias:".json_encode($categorias)."}";
	} catch (PDOException $e){
		print_r($e);
	} catch (Exception $ex) {
		print_r($ex);
	}
});

$app->run();
