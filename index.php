<?php
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\ServerRequestInterface as Request;

require 'vendor/autoload.php';
require 'src/models/EncuestaDB.class.php';
require 'src/Encuesta.class.php';
require 'src/library/simpleExcel.php';
$app = new \Slim\App;

// Routes
// all books

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

 return '';
}

$app->get('/encuesta', function (Request $request, Response $response) {

 try {
  // picking books from database
  $encuestaDb = new EncuestaDB();

  $encuesta = $encuestaDb->getAll();

  // custom json response
  $response->withStatus(200);
  $response->withHeader('Content-Type', 'application/json');
  return $response->withJson($encuesta);

 } catch (PDOException $e) {

  $response->withStatus(500);
  $response->withHeader('Content-Type', 'application/json');
  $error['err'] = $e->getMessage();
  return $response->withJson($error);
 }
});

$app->get('/excel', function (Request $request, Response $response) {

 try {

  $encuestaDb = new EncuestaDB();

  $encuesta = $encuestaDb->getAll();

  array_unshift($encuesta, ['ID', "GENERO", "HOBBY", "TIEMPO", "NOMBRE"]);

  $xlsx = SimpleXLSXGen::fromArray($encuesta)->downloadAs('resultados.xlsx');

 } catch (PDOException $e) {

  $response->withStatus(500);
  $response->withHeader('Content-Type', 'application/json');
  $error['err'] = $e->getMessage();
  return $response->withJson($error);
 }
});

// adding a book
$app->post('/encuesta', function (Request $request, Response $response) {
 try {
  $encuesta = new Encuesta();
  $encuesta->__set('genero', $request->getParam('genero'));
  $encuesta->__set('hobby', $request->getParam('hobby'));
  $encuesta->__set('tiempo', $request->getParam('tiempo'));
  $encuesta->__set('nombre', $request->getParam('nombre'));

  // adding book in db
  $encuestaDb = new EncuestaDB();
  $result     = $encuestaDb->add($encuesta);

  // custom json response
  $response->withStatus(200);
  $response->withHeader('Content-Type', 'application/json');
  $response->withHeader('Access-Control-Allow-Origin', '*');
  $response->withHeader('Access-Control-Allow-Headers', '*');
  return $response->withJson($result);

 } catch (PDOException $e) {
  $response->withStatus(500);
  $response->withHeader('Content-Type', 'application/json');
  $error['err'] = $e->getMessage();
  return $response->withJson($error);
 }
});

// update a book
$app->put('/encuesta', function (Request $request, Response $response) {
 try {

  $encuesta = new Encuesta();
  $encuesta->__set('id', $request->getParam('id'));
  $encuesta->__set('title', $request->getParam('title'));
  $encuesta->__set('author', $request->getParam('author'));
  $encuesta->__set('book_description', $request->getParam('description'));

  // updating book in db
  $encuestaDb = new EncuestaDB();
  $encuestaDb->update($encuesta);

  // custom json response
  $response->withStatus(200);
  $response->withHeader('Content-Type', 'application/json');
  $message['ok'] = "Book updated successfully";
  return $response->withJson($message);

 } catch (PDOException $e) {
  $response->withStatus(500);
  $response->withHeader('Content-Type', 'application/json');
  $error['err'] = $e->getMessage();
  return $response->withJson($error);
 }
});

$app->options('/encuesta', function (Request $request, Response $response) {
 try {

  $message['ok'] = true;
  $response->withHeader('Content-Type', 'application/json');
  $response->withHeader('Access-Control-Allow-Origin', '*');
  $response->withHeader('Access-Control-Allow-Headers', '*');

  return $response->withJson($message);

 } catch (PDOException $e) {
  $response->withStatus(500);
  $response->withHeader('Content-Type', 'application/json');
  $error['err'] = $e->getMessage();
  return $response->withJson($error);
 }
});

$app->run();
