<?php
include('../autenticacao/autenticacao.php');
include('../conexaoPDO/conexaoPDO.php');
include('Usuario.php');
include('Telefone.php');
include('Endereco.php');

//------------------------------- Configuracoes --------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);
$loader = require __DIR__ . '/../../vendor/autoload.php';
header('Content-Type', 'application/json;charset=utf-8');

// ------------------------------ Slim -----------------------------------------

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();
$app->response->headers->set('Content-Type', 'application/json;charset=utf-8');

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

	$usuario = new Usuario($conn->getConn());
	$usuario->setEmail($email);
	$rslt = $usuario->getUsuarioForEmail();

	if($rslt !== false){
		die(json_encode($rslt));
	}

	$app->response()->status(400);
  $app->response()->header('X-Status-Reason', 'Nao existe usuario');

});

//------------------------------- CADASTRAR USUARIO -------------------------------

$app->post('/cadastrar_usuario', function() use($app, $autenticacao, $conn) {

	$request = json_decode($app->getInstance()->request()->getBody());

	$tipo = $request->tipoCadastro;

	$usuario = new Usuario($conn->getConn());
	$usuario->setNome($request->usuario->nome);
	$usuario->setEmail($request->usuario->email);
	$usuario->setSobreNome($request->usuario->sobreNome);
	$usuario->setTipoCadastro($request->tipoCadastro);

	// die(json_encode($request->tipoCadastro==1));
	if($request->tipoCadastro == 1){
		$usuario->setIsFace(true);
	}

	if($request->tipoCadastro == 2){
		$usuario->setIsGoogle(true);
	}

	die(json_encode($usuario->cadastrar()));
});

//------------------------------- AUTENTICAR USUARIO -------------------------------

$app->get('/autenticar_usuario', function() use($app, $autenticacao) {
	if($autenticacao->autenticar_usuario($app->request()->params('email'), $app->request()->params('senha'))){
			die(json_encode(array("token"=>$autenticacao->getToken())));
	}

	$app->response()->status(400);
  $app->response()->header('X-Status-Reason', 'Usuario ou senha invalidos.');
});

//------------------------------- GERAR TOKEN -------------------------------

$app->post('/gerarToken', function() use($app, $autenticacao) {
	$request = json_decode($app->getInstance()->request()->getBody());
	$token = $autenticacao->gerarToken($request->usuario->nome, $request->usuario->chave);
	die(json_encode($token));

});

//------------------------------- BUSCAR TELEFONE ------------------------------

$app->get('/getTelefone', function () use ($app, $conn) {

	$idUsr = $app->request()->params('id_usr');

	$telefone  = new Telefone($conn->getConn());

	die(json_encode($telefone->getTelefone($idUsr)));

});

//---------------------------- CADASTRAR TELEFONE ------------------------------

$app->post('/cadastrarTelefone', function () use ($app, $conn) {
	$request = json_decode($app->getInstance()->request()->getBody());

	$celular = $request->celular;
	$residencial = $request->residencial;
	$idUsr = $request->idUsr;

	$telefone  = new Telefone($conn->getConn());

	$telefone->setCelular($celular);
	$telefone->setResidencial($residencial);

	if($telefone->cadastrar($idUsr)){
		die('Cadastro realizado com sucesso');
	}

	$app->response()->status(400);
	$app->response()->header('X-Status-Reason', 'Ocorreu um error no processo.');

});

//------------------------------ ALTERAR TELEFONE ------------------------------

$app->post('/alterarTelefone', function () use ($app, $conn) {
	$request = json_decode($app->getInstance()->request()->getBody());

	$celular = $request->celular;
	$residencial = $request->residencial;
	$idUsr = $request->idUsr;
	$idTlf = $request->idTlf;

	$telefone  = new Telefone($conn->getConn());

	$telefone->setCelular($celular);
	$telefone->setResidencial($residencial);

	if($telefone->alterar($idTlf, $idUsr)){
		die('Alteracao realizada com sucesso');
	}

	$app->response()->status(400);
	$app->response()->header('X-Status-Reason', 'Ocorreu um erro no processo.');

});

//------------------------------ EXCLUIR TELEFONE ------------------------------

$app->get('/excluirTelefone', function () use ($app, $conn) {
	$id_end = $app->request()->params('id_end');
	$id_usr = $app->request()->params('id_usr');

	$telefone = new Telefone($conn->getConn());

	if($telefone->excluir($id_end, $id_usr)){
			mensagem('Telefone excluido','INFO');
	}

	$app->response()->status(400);
	$app->response()->header('X-Status-Reason', 'Ocorreu um erro no processo.');

});

//---------------------------- CADASTRAR ENDERECO ------------------------------

$app->post('/cadastrarEndereco', function () use ($app, $conn) {
	$request = json_decode($app->getInstance()->request()->getBody());

	$id_usr = $request->id_usr;

	$endereco = new Endereco($conn->getConn());
	$endereco->setComplemento($request->complemento);
	$endereco->setQuadra($request->quadra);
	$endereco->setRua($request->rua);

	if($endereco->cadastrar($id_usr)){
		mensagem('Endereco cadastrado com sucesso.', 'INFO');
	};

	$app->response()->status(400);
	$app->response()->header('X-Status-Reason', 'Ocorreu um erro no processo.');

});

//---------------------------- ALTERAR ENDERECO ------------------------------

$app->post('/alterarEndereco', function () use ($app, $conn) {
	$request = json_decode($app->getInstance()->request()->getBody());

	$id_tlf = $request->id_tlf;
	$id_usr = $request->id_usr;

	$endereco = new Endereco($conn->getConn());
	$endereco->setComplemento($request->complemento);
	$endereco->setQuadra($request->quadra);
	$endereco->setRua($request->rua);

	if($endereco->alterar($id_tlf, $id_usr)){
		mensagem('Endereco alterado com sucesso.', 'INFO');
	};

	$app->response()->status(400);
	$app->response()->header('X-Status-Reason', 'Ocorreu um erro no processo.');

});

//---------------------------- EXCLUIR ENDERECO ------------------------------

$app->get('/excluirEndereco', function () use ($app, $conn) {
	$id_end = $app->request()->params('id_end');
	$id_usr = $app->request()->params('id_usr');

	$endereco = new Endereco($conn->getConn());

	if($endereco->excluir($id_end, $id_usr)){
			mensagem('Endereco excluido','INFO');
	}

	$app->response()->status(400);
	$app->response()->header('X-Status-Reason', 'Ocorreu um erro no processo.');

});

//---------------------------- BUSCAR ENDERECO ------------------------------

$app->get('/getEndereco', function () use ($app, $conn) {
	$id_usr = $app->request()->params('id_usr');

	$endereco = new Endereco($conn->getConn());

	die(json_encode($endereco->getEndereco($id_usr)));

	$app->response()->status(400);
	$app->response()->header('X-Status-Reason', 'Ocorreu um erro no processo.');

});

function mensagem($mensagem, $tipoMensagem){
	die(json_encode(array("mensagem"=>$mensagem,"TIPO"=>$tipoMensagem)));
}


$app->run();
