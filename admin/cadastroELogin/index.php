<?php
include('../autenticacao/autenticacao.php');
include('../conexaoPDO/conexaoPDO.php');
include('Usuario.php');

//------------------------------- Configuracoes --------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);
$loader = require __DIR__ . '/../../vendor/autoload.php';
header('Content-Type', 'application/json;charset=utf-8');

// ------------------------------ Slim -----------------------------------------

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->response()->header('Content-Type', 'application/json;charset=utf-8');

// ------------------------------ ConexaoPDO -----------------------------------

$conn = new ConexaoPDO();

// ------------------------------ Autenticacao -----------------------------------

$autenticacao = new Autenticacao($conn->getConn());

// ------------------------------- VALIDA EXECUCAO------------------------------

function validaExecucao($rslt,$operacao){
	if ($rslt)
		die(json_encode(array('estado'=>true,'texto'=>'Operacao realizada com sucesso!')));
	else
		die(json_encode(array('estado'=>false,'texto'=>'Ops, problemas ao tentar realizar a operação de '.$operacao)));
}

//------------------------------- BUSCAR USUARIO -------------------------------

$app->get('/getUsuario', function() use($app, $autenticacao, $conn) {

	$email = $app->request()->params('email');

	//  var $usuario = new Usuario();
	// $usuario->setEmail($email);
	// $rslt = $usuario->getUsuarioForEmail();

	// if($rslt > 0){
	// 		$token = $autenticacao->gerarToken($rslt[3], $rslt[4]);
	// }

	die(json_encode(array("token"=>$isVl)));
});

//------------------------------- CADASTRAR USUARIO -------------------------------

$app->post('/cadastrar_usuario', function() use($app, $autenticacao, $conn) {

	$request = json_decode($app->getInstance()->request()->getBody());

	// die(json_encode($request));
	try {

			$usuario = new Usuario($conn->getConn());
			$usuario->setNome($request->usuario->nome);
			$usuario->setEmail($request->usuario->email);
			$usuario->tipoCadastro($request->usuario->tipoCadastro);

			$usuario->setSobreNome('');

			$usuario->cadastrar(); // 0 indica que é um cadastro pelo aplicativo

			if($usuario->cadastrar()){
					$token = $autenticacao->gerarToken($request->usuario->nome, $request->usuario->sobreNome);
			}

			die(json_encode($token));

	} catch (Exception $e) {
		die(json_encode(array("mensagem"=>'Não foi possivel realizar o cadastro. Ocorreu o erro: ' . $e->getMessage(), "TIPO"=> 'ERROR')));
	}
});

//------------------------------- AUTENTICAR USUARIO -------------------------------

$app->get('/autenticar_usuario', function() use($app, $autenticacao) {

	$token = $autenticacao->autenticar_usuario($app->request()->params('email'), $app->request()->params('senha'));

	// $isVl = $autenticacao->validaToken($token, $app->request()->params('email'), sha1($app->request()->params('senha')));

	die(json_encode(array("token"=>$token)));
});

//------------------------------- AUTENTICAR USUARIO FACEBOOK -------------------------------

// $app->get('/autenticar_usuario_face', function() use($app, $autenticacao) {
//
// 	email = $app->request()->params('email')
//
// 	$usuario = new Usuario();
// 	$usuario->setEmail($email);
// 	$rslt = $usuario->getUsuarioForEmail();
//
// 	if($rslt > 0){
// 			$token = $autenticacao->gerarToken($rslt[3], $rslt[4]);
// 	}
//
// 	die(json_encode(array("token"=>$token)));
// });

//------------------------------- AUTENTICAR USUARIO GOOGLE -------------------------------

// $app->get('/autenticar_usuario_google', function() use($app, $autenticacao) {
//
// 	$token = $autenticacao->autenticar_usuario($app->request()->params('email'), $app->request()->params('senha'));
//
// 	// $isVl = $autenticacao->validaToken($token, $app->request()->params('email'), sha1($app->request()->params('senha')));
//
// 	die(json_encode(array("token"=>$token)));
// });

//------------------------------- TESTE ----------------------------------------


$app->get('/', function() use ($app, $conn) {
	$id = $app->request()->params('id');
	$where = ($id) ? "WHERE ID = $id" : "";

	$sql = "SELECT * FROM categoria $where order by ordem";

	try {
		$categorias = $conn->query($sql)->fetchAll(PDO::FETCH_OBJ);
		echo json_encode($categorias);
	} catch (Exception $e) {
		echo json_encode($e);
	}
});


$app->run();
