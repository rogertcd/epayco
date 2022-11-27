<?php
//Definir librerías necesarias


define('BASE_PATH', realpath(dirname(__FILE__)).'/');

include_once ('application/helpers/constantes.php');
include_once ("application/server.php");


//Instanciar el servidor y publicar los servicios
$server = Server::instanciar();
$server->configurar_salida();


exit();
