<?php
include("conexion.php");

/*
DATOS:
- codigo
- nombre
- tipo (medicamento o material)
- movimiento (entrada / salida / nuevo)
- cantidad
*/

$codigo = $_POST['codigo'];
$nombre = $_POST['nombre'];
$tipo = $_POST['tipo'];
$movimiento = $_POST['movimiento'];
$cantidad = $_POST['cantidad'];

/* VALIDAR */
if(empty($codigo) || empty($nombre) || empty($movimiento) || empty($cantidad)){
    echo "error";
    exit;
}

/* INSERTAR MOVIMIENTO (CORRECTO) */
$sql = "INSERT INTO movimientos 
(codigo, nombre, tipo_movimiento, cantidad, tipo_producto, fecha)
VALUES 
('$codigo', '$nombre', '$movimiento', '$cantidad', '$tipo', NOW())";

if(mysqli_query($conexion, $sql)){
    echo "ok";
}else{
    echo "error";
}
?>