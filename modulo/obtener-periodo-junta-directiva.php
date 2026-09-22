<?php
	include '../php/conexion.php';
	include '../php/funciones.php';

	$idJuntaDirectiva = isset($_POST['idJuntaDirectiva']) ? $_POST['idJuntaDirectiva'] : '';

	if (empty($idJuntaDirectiva)) {
	    echo json_encode(['error' => 'No se recibió el ID de Junta Directiva']);
	    exit;
	}

	$query = "SELECT fechaPeriodo AS fechaInicioPeriodo, fechaFinPeriodo FROM sm_junta_directiva WHERE idJuntaDirectiva = ?";
	$consulta = $conexion->prepare($query);
	$consulta->bind_param("s", $idJuntaDirectiva);
	$consulta->execute();
	$resultado = $consulta->get_result()->fetch_assoc();

	$fechaInicioPeriodo = $resultado['fechaInicioPeriodo'] ?? '';
	$fechaFinPeriodo = $resultado['fechaFinPeriodo'] ?? '';
	$infoGestion = '<strong>DESDE:</strong> '.infoFecha($fechaInicioPeriodo,'normal').' <strong>HASTA:</strong> '.infoFecha($fechaFinPeriodo,'normal');

	$dateFechaInicio = new DateTime($fechaInicioPeriodo);
	$fechaInicioPeriodo = $dateFechaInicio->format('d/m/Y');

	$dateFechaFin = new DateTime($fechaFinPeriodo);
	$fechaFinPeriodo = $dateFechaFin->format('d/m/Y');


	$respuesta = [
	    'fechaInicioPeriodo' => $fechaInicioPeriodo,
	    'fechaFinPeriodo'    => $fechaFinPeriodo,
	    'infoGestion'        => $infoGestion
	];

	echo json_encode($respuesta);

	$consulta->close();
	$conexion->close();
?>
