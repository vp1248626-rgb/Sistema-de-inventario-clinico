<?php

include("conexion.php");

/* RECIBIR DATOS */
$codigo = $_POST['codigo'];
$lote = $_POST['lote'];
$caducidad = isset($_POST['caducidad']) && $_POST['caducidad'] != "" ? $_POST['caducidad'] : null;
$cantidad = intval($_POST['cantidad']);
$tipo = $_POST['tipo']; // medicamento o material

/* VALIDAR */
if(!$codigo || !$cantidad || !$tipo || !$lote){
    echo json_encode(["status"=>"error","msg"=>"Datos incompletos"]);
    exit;
}

/* TABLA */
$tabla = "productos";

/* 🔍 OBTENER PRODUCTO EXACTO (POR LOTE Y CADUCIDAD) */
$stmt = $conexion->prepare("
    SELECT nombre, stock 
    FROM $tabla 
    WHERE codigo = ? 
    AND lote = ? 
    AND caducidad <=> ? 
    AND tipo = ?
");

$stmt->bind_param("ssss", $codigo, $lote, $caducidad, $tipo);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    echo json_encode(["status"=>"error","msg"=>"Producto no existe con ese lote"]);
    exit;
}

$producto = $res->fetch_assoc();
$nombre = $producto['nombre'];

/*  ACTUALIZAR STOCK CORRECTAMENTE */
$stmt2 = $conexion->prepare("
    UPDATE $tabla 
    SET stock = stock + ? 
    WHERE codigo = ? 
    AND lote = ? 
    AND caducidad <=> ? 
    AND tipo = ?
");

$stmt2->bind_param("issss", $cantidad, $codigo, $lote, $caducidad, $tipo);
$resultado = $stmt2->execute();

if(!$resultado){
    echo json_encode([
        "status"=>"error",
        "msg"=>$conexion->error
    ]);
    exit;
}

/*  GUARDAR MOVIMIENTO */
$stmt3 = $conexion->prepare("
    INSERT INTO movimientos (codigo, nombre, tipo_movimiento, cantidad, tipo_producto, fecha)
    VALUES (?, ?, 'entrada', ?, ?, NOW())
");

$stmt3->bind_param("ssis", $codigo, $nombre, $cantidad, $tipo);
$stmt3->execute();

/* RESPUESTA */
echo json_encode([
    "status"=>"ok",
    "msg"=>"Stock actualizado correctamente"
]);

?>