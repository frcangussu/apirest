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

$app->get('/getUsuarioEmail', function() use($app, $autenticacao, $conn) {

	$email = $app->request()->params('email');

	$usuario = new Usuario($conn->getConn());
	$usuario->setEmail($email);
	die(json_encode( $usuario->getUsuarioForEmail() ));

});

//------------------------------- CADASTRAR USUARIO -------------------------------

$app->post('/cadastrar_usuario', function() use($app, $autenticacao, $conn) {

	$request = json_decode($app->getInstance()->request()->getBody());

	$tipo = $request->tipoCadastro;

	$usuario = new Usuario($conn->getConn());
	$usuario->setNome($request->usuario->nome);
	$usuario->setEmail($request->usuario->email);
	$usuario->setSenha($request->usuario->senha);
	$usuario->setSobreNome($request->usuario->sobreNome);

	// die(json_encode($request->tipoCadastro==1));
	if($tipo == 1){
		$usuario->setIsFace(true);
	} else if($tipo == 2){
		$usuario->setIsGoogle(true);
	} else {
		$usuario->setIsApp(true);
	}

	die(json_encode( $usuario->cadastrar() ));
});

//------------------------------- Alterar USUARIO -------------------------------

$app->post('/alterar_usuario', function() use($app, $autenticacao, $conn) {

	$request = json_decode($app->getInstance()->request()->getBody());

	$usuario = new Usuario($conn->getConn());

	$usuario->setNome($request->usuario->nome);
	$usuario->setEmail($request->usuario->email);
	$usuario->setSenha($request->usuario->senha);
	$usuario->setSobreNome($request->usuario->sobreNome);
	$usuario->setIsFace($request->usuario->isFace);
	$usuario->setIsGoogle($request->usuario->isGoogle);

	$usuario->setIsApp(true);

	die(json_encode($usuario->alterar($request->usuario->id_usr)));
});

//------------------------------- AUTENTICAR USUARIO -------------------------------

$app->post('/autenticar_usuario', function() use($app, $autenticacao) {
	$request = json_decode($app->getInstance()->request()->getBody());
	$rstl = $autenticacao->autenticar_usuario($request->email, $request->senha);

	if(sizeof($rstl)){
			die(json_encode(array("usuario"=>$rstl, "token"=>$autenticacao->gerarToken($app->request()->params('email'), $app->request()->params('senha') ))));
	}

	$app->response()->status(400);
  $app->response()->header('X-Status-Reason', 'Usuario ou senha invalidos.');
});

//------------------------------- GERAR TOKEN -------------------------------

$app->post('/gerarToken', function() use($app, $autenticacao) {
	$request = json_decode($app->getInstance()->request()->getBody());
	$token = $autenticacao->gerarToken($request->nome, $request->chave);
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
