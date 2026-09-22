<?php
	include '../php/conexion.php';
	include '../php/funciones.php';

	header('Content-Type: application/json');

	if (!isset($_POST['idJuntaDirectiva']) || !isset($_POST['tipoActividad'])) {
	    echo json_encode(['error' => 'Faltan datos']);
	    exit;
	}

	$idJuntaDirectiva = mysqli_real_escape_string($conexion, $_POST['idJuntaDirectiva']);
	$tipoActividad = mysqli_real_escape_string($conexion, $_POST['tipoActividad']);

	$actividades = [];

	if (!empty($idJuntaDirectiva) && !empty($tipoActividad)) {
	    $sql = "SELECT codigoActividad, temaActividad, fechaActividad, codigoCuenta 
	            FROM sm_mod_actividades 
	            WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND tipoActividad = '$tipoActividad' 
	            ORDER BY fechaActividad ASC";

	    $resultadoActividades = $conexion->query($sql);

	    if ($resultadoActividades && $resultadoActividades->num_rows > 0) {
	        while ($fila = $resultadoActividades->fetch_assoc()) {
	            $codigoActividad    = $fila['codigoActividad'];
	            $temaActividad      = $fila['temaActividad'];
	            $fechaActividad     = $fila['fechaActividad'];
	            $infoFechaActividad = strtoupper(infoFecha($fechaActividad, 'normal'));
	            $nombreActividad    = "$temaActividad | $infoFechaActividad";

	            $actividades[] = [
	                'codigoActividad' => $codigoActividad,
	                'nombreActividad' => $nombreActividad
	            ];
	        }
	    }
	}

	echo json_encode(['actividades' => $actividades]);

	$conexion->close();
?>
