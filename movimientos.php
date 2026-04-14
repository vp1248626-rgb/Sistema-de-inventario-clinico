<?php
include("conexion.php");

/* VALIDAR TIPO */
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'medicamento';

/*  SEGURIDAD */
if($tipo != 'medicamento' && $tipo != 'material'){
    die("Acceso no permitido");
}

/*  FILTRO */
$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'todo';

$where = "";

if($filtro == "hoy"){
    $where = "AND DATE(fecha) = CURDATE()";
}
elseif($filtro == "semana"){
    $where = "AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)";
}
elseif($filtro == "mes"){
    $where = "AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
}

/* CONSULTA */
$sql = "SELECT * FROM movimientos 
WHERE tipo_producto='$tipo' $where
ORDER BY fecha DESC";

$resultado = mysqli_query($conexion, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Movimientos</title>
<link rel="stylesheet" href="estilos.css">

<style>
.sidebar{
    position: fixed;
    left:0;
    top:0;
    height:100vh;
    width:30%;
}
.main-content{
    margin-left:30%;
    height:100vh;
    overflow-y:auto;
    padding:50px;
}
.tabla-contenedor{
    background:white;
    border-radius:10px;
    padding:20px;
    box-shadow:0 5px 10px rgba(0,0,0,0.1);
}

/*  NUEVO DISEÑO FILTROS */
.filtros{
    background:white;
    padding:15px;
    border-radius:10px;
    box-shadow:0 3px 8px rgba(0,0,0,0.1);
    margin-bottom:20px;
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}

.filtros label{
    font-weight:bold;
}

/* SELECT */
.filtros select{
    padding:6px 10px;
    border-radius:6px;
    border:1px solid #ccc;
}

/* BOTONES MÁS PEQUEÑOS */
.btn-mini{
    padding:6px 12px;
    font-size:13px;
    border:none;
    border-radius:6px;
    cursor:pointer;
    background:#009688;
    color:white;
    transition:0.2s;
}

.btn-mini:hover{
    background:#00796b;
}

/* BOTÓN DESCARGA */
.btn-descarga{
    background:#3f51b5;
}
.btn-descarga:hover{
    background:#2c3e9f;
}
</style>
</head>

<body class="panel-body">

<div class="container">

<!-- SIDEBAR -->
<div class="sidebar">
<h2>Movimientos</h2>

<nav>
<a href="<?php echo ($tipo == 'material') ? 'materiales.html' : 'panel.html'; ?>">⬅ Volver</a>
</nav>

<div class="logout">
<button onclick="window.location.href='index.html'">Cerrar sesión</button>
</div>
</div>

<!-- CONTENIDO -->
<div class="main-content">

<h1>
<?php 
echo ($tipo == 'material') ? 'Movimientos de Materiales' : 'Movimientos de Medicamentos'; 
?>
</h1>

<!--  FILTROS MEJORADOS -->
<div class="filtros">
<label>Filtrar:</label>

<select id="tipoFiltro">
    <option value="todo" <?php if($filtro=="todo") echo "selected"; ?>>Todo</option>
    <option value="hoy" <?php if($filtro=="hoy") echo "selected"; ?>>Hoy</option>
    <option value="semana" <?php if($filtro=="semana") echo "selected"; ?>>Esta semana</option>
    <option value="mes" <?php if($filtro=="mes") echo "selected"; ?>>Este mes</option>
</select>

<button class="btn-mini" onclick="filtrar()">Aplicar</button>
<button class="btn-mini btn-descarga" onclick="exportarExcel()">⬇ Descargar</button>
</div>

<div class="tabla-contenedor">

<table border="1" width="100%" cellpadding="10" id="tablaMovimientos">
<thead>
<tr>
<th>Código</th>
<th>Nombre</th>
<th>Tipo</th>
<th>Cantidad</th>
<th>Fecha</th>
</tr>
</thead>

<tbody>

<?php
if(mysqli_num_rows($resultado) > 0){
    while($fila = mysqli_fetch_assoc($resultado)){
        echo "<tr>
        <td>{$fila['codigo']}</td>
        <td>{$fila['nombre']}</td>
        <td>{$fila['tipo_movimiento']}</td>
        <td>{$fila['cantidad']}</td>
        <td>{$fila['fecha']}</td>
        </tr>";
    }
}else{
    echo "<tr><td colspan='5'>No hay movimientos registrados</td></tr>";
}
?>

</tbody>
</table>

</div>

</div>
</div>

<script>

/*  FILTRAR */
function filtrar(){
    let f = document.getElementById("tipoFiltro").value;
    window.location.href = "movimientos.php?tipo=<?php echo $tipo; ?>&filtro=" + f;
}

/*  EXPORTAR EXCEL */
function exportarExcel(){
    let tabla = document.getElementById("tablaMovimientos").outerHTML;
    let a = document.createElement("a");
    a.href = 'data:application/vnd.ms-excel,' + encodeURIComponent(tabla);
    a.download = "movimientos.xls";
    a.click();
}

</script>

</body>
</html>