<?php
include("conexion.php");

/* RECIBIR DATOS */
$codigo = $_POST['codigo'];
$nombre = $_POST['nombre'];
$lote = $_POST['lote'];
$caducidad = isset($_POST['caducidad']) ? $_POST['caducidad'] : null;
$cantidad = intval($_POST['cantidad']);
$tipo = $_POST['tipo'];

/* LIMPIAR CADUCIDAD */
if($caducidad == "" || $caducidad == "Sin caducidad"){
    $caducidad = null;
}

/* VALIDAR */
if(empty($codigo) || empty($nombre) || empty($cantidad) || empty($tipo) || empty($lote)){
    echo "error";
    exit;
}

/* VERIFICAR DUPLICADO */
$check = $conexion->prepare("
SELECT id FROM productos 
WHERE codigo = ? AND lote = ? AND caducidad <=> ? AND tipo = ?
");

$check->bind_param("ssss", $codigo, $lote, $caducidad, $tipo);
$check->execute();
$resultado = $check->get_result();

if($resultado->num_rows > 0){
    echo "existe";
    exit;
}

/* INSERTAR PRODUCTO */
$sql = $conexion->prepare("
INSERT INTO productos (codigo, nombre, lote, caducidad, stock, tipo)
VALUES (?, ?, ?, ?, ?, ?)
");

$sql->bind_param("ssssis", $codigo, $nombre, $lote, $caducidad, $cantidad, $tipo);

if($sql->execute()){

    /* GUARDAR MOVIMIENTO */
    $stmt = $conexion->prepare("
    INSERT INTO movimientos 
    (codigo, nombre, tipo_movimiento, cantidad, tipo_producto, fecha)
    VALUES (?, ?, 'nuevo', ?, ?, NOW())
    ");

    $stmt->bind_param("ssis", $codigo, $nombre, $cantidad, $tipo);
    $stmt->execute();

    echo "ok";

}else{
    echo $conexion->error;
}
?>