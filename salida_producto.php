<?php

include("conexion.php");

/* RECIBIR DATOS JSON */
$data = json_decode(file_get_contents("php://input"), true);

$codigo = $data['codigo'];
$cantidad = intval($data['cantidad']);
$tipo = $data['tipo']; // medicamento o material

/* VALIDAR */
if(!$codigo || !$cantidad || !$tipo){
    echo json_encode(["status"=>"error","msg"=>"Datos incompletos"]);
    exit;
}

/*  USAR SOLO TABLA PRODUCTOS */
$tabla = "productos";

/* 🔍 OBTENER PRODUCTO */
$sql = "SELECT nombre, stock FROM $tabla WHERE codigo='$codigo' AND tipo='$tipo'";
$resultado = mysqli_query($conexion, $sql);

if(mysqli_num_rows($resultado) == 0){
    echo json_encode(["status"=>"error","msg"=>"Producto no encontrado"]);
    exit;
}

$fila = mysqli_fetch_assoc($resultado);
$stock_actual = intval($fila['stock']);
$nombre = $fila['nombre'];

/* VALIDAR STOCK */
if($stock_actual < $cantidad){
    echo json_encode(["status"=>"error","msg"=>"Stock insuficiente"]);
    exit;
}

/* RESTAR STOCK */
$nuevo_stock = $stock_actual - $cantidad;

$sql_update = "UPDATE $tabla SET stock=$nuevo_stock WHERE codigo='$codigo' AND tipo='$tipo'";
mysqli_query($conexion, $sql_update);

/*  REGISTRAR MOVIMIENTO */
$sql_mov = "INSERT INTO movimientos 
(codigo, nombre, tipo_movimiento, cantidad, tipo_producto, fecha)
VALUES 
('$codigo', '$nombre', 'salida', $cantidad, '$tipo', NOW())";

mysqli_query($conexion, $sql_mov);

/* RESPUESTA */
echo json_encode([
    "status"=>"ok",
    "msg"=>"Salida registrada correctamente",
    "stock_restante"=>$nuevo_stock
]);

?>