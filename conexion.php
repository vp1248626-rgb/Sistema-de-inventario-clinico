<?php

$servidor = "localhost";
$usuario = "toto";
$password = "zS2100";
$bd = "INVENTARIO";

$conexion = mysqli_connect($servidor, $usuario, $password, $bd);

if(!$conexion){
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8");

?>