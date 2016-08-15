<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$loader = require __DIR__ . '/../../vendor/autoload.php';

// header('Content-Type', 'image/jpeg;charset=utf-8');
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

$app->post('/enviar', function () {

	if (!isset($_FILES['file'])) {
		echo "No files uploaded!!";
		return;
	}

	$imgs = array();

	$file = $_FILES['file'];

	if ($file['error'] === 0) {
		$name = uniqid('img-'.date('Ymd').'-');
		try {
			move_uploaded_file($file['tmp_name'], 'files/' . $name);
			$img = array('url' => '/uploads/' . $name, 'name' => $file['name']);
			die(json_encode($img));
		} catch (Exception $e) {
			die(json_encode(array(">>> erro"=>$e->getMessage())));
		}
	}

	// die("Ok");
	// $imageCount = count($imgs);
	// if ($imageCount == 0) {
	// 	echo 'No files uploaded!!  <p><a href="/">Try again</a>';
	// 	return;
	// }

	/*$plural = ($imageCount == 1) ? '' : 's';

	echo <<<__HTML
	<!DOCTYPE html>
	<html>
	<head>
	<meta charset="utf-8"/>
	<title>Multiple Upload</title>
	</head>
	<body>
	<h1>Thanks for uploading $imageCount file{$plural}.</h1>
	<h2>File List</h2>
	__HTML;

	foreach($imgs as $img) {
	printf('%s <img src="%s" width="50" height="50" /><br/>', $img['name'], $img['url']);
}

echo <<<__HTML
<p><a href="/">Upload more</a>
</body>
</html>
__HTML";*/

});
$app->run();
