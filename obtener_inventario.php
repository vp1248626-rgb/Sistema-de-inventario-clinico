<?php

include("conexion.php");

/* RECIBIR TIPO */
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : '';

if(!$tipo){
    echo json_encode([
        "status"=>"error",
        "msg"=>"Tipo no especificado"
    ]);
    exit;
}

/* CONSULTA A PRODUCTOS */
$sql = "SELECT codigo, nombre, lote, caducidad, stock FROM productos WHERE tipo='$tipo'";
$resultado = mysqli_query($conexion, $sql);

if(!$resultado){
    echo json_encode([
        "status"=>"error",
        "msg"=>mysqli_error($conexion)
    ]);
    exit;
}

$datos = [];

while($fila = mysqli_fetch_assoc($resultado)){
    $datos[] = [
        "codigo" => $fila["codigo"],
        "nombre" => $fila["nombre"],
        "lote" => $fila["lote"],
        "caducidad" => $fila["caducidad"],
        "stock" => intval($fila["stock"])
    ];
}

/* RESPUESTA */
echo json_encode([
    "status" => "ok",
    "data" => $datos
]);

?>