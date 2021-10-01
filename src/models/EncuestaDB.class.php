<?php
require __DIR__ . '/../../configDB.php';

class EncuestaDB
{
 private $pdo;

 public function connect()
 {
  $connect_str = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;

  $pdo = new PDO($connect_str, DB_USER, DB_PASSWORD);

  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $this->pdo = $pdo;
 }

 public function getAll()
 {
  $sql = "SELECT id, genero, hobby, tiempo, nombre FROM encuesta";
  $this->connect();

  $stmt = $this->pdo->query($sql);

  $this->pdo = null;
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }

 public function findById(int $id)
 {
  $sql = "SELECT id, genero, hobby, tiempo, nombre FROM encuesta WHERE id=$id";
  $this->connect();

  $stmt = $this->pdo->query($sql);

  $this->pdo = null;
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
 }

 public function add(Encuesta $encuesta)
 {
  $sql = "INSERT INTO encuesta Values (default, :nombre, :genero, :hobby, :tiempo )";

  $this->connect();

  $stmt = $this->pdo->prepare($sql);

  $res = $stmt->execute(array(
   ":genero" => $encuesta->__get('genero'),
   ":hobby"  => $encuesta->__get('hobby'),
   ":tiempo" => $encuesta->__get('tiempo'),
   ":nombre" => $encuesta->__get('nombre'),
  )
  );

  return $this->getAll();
 }

 public function update(Encuesta $encuesta)
 {
  $sql = "UPDATE encuesta SET genero = :genero, hobby = :hobby, tiempo = :tiempo WHERE id = :id";

  $this->connect();

  $stmt = $this->pdo->prepare($sql);

  $res = $stmt->execute(array(
   ":id"               => $encuesta->__get('id'),
   ":title"            => $encuesta->__get('title'),
   ":author"           => $encuesta->__get('author'),
   ":book_description" => $encuesta->__get('description'),
  )
  );
 }

}
