<?php
class Encuesta
{
 private $id;
 private $genero = "";
 private $hobby  = "";
 private $tiempo = "";
 private $nombre = "";

 public function __get($attr)
 {
  return $this->$attr;
 }

 public function __set($attr, $val)
 {
  return $this->$attr = $val;
 }
}
