<?php
	include '../php/conexion.php';
	include '../php/funciones.php';

	header('Content-Type: application/json');

	if (!isset($_POST['idJuntaDirectiva'])) {
	    echo json_encode(['error' => 'Faltan datos']);
	    exit;
	}

	$idJuntaDirectiva = mysqli_real_escape_string($conexion, $_POST['idJuntaDirectiva']);

	$cuotas = [];

	if (!empty($idJuntaDirectiva)) {
		$sql = "SELECT codigoCuota, conceptoCuota, montoCuota, fechaPago FROM sm_mod_cuotas WHERE idJuntaDirectiva = '$idJuntaDirectiva' ORDER BY fechaPago ASC";
	    $resultadoCuotas = $conexion->query($sql);

	    if ($resultadoCuotas && $resultadoCuotas->num_rows > 0) {
	        while ($fila = $resultadoCuotas->fetch_assoc()) {
	            $codigoCuota    = $fila['codigoCuota'];
	            $conceptoCuota      = $fila['conceptoCuota'];
	            $montoCuota     = $fila['montoCuota'];
	            $fechaPago     = $fila['fechaPago'];
	            $infoMontoCuota = moneda($montoCuota);
	            $infoFechaPago = strtoupper(infoFecha($fechaPago, 'normal'));
	            $nombreCuota    = "$conceptoCuota | MONTO: S/. $infoMontoCuota  | FECHA PAGO: $infoFechaPago";

	            $cuotas[] = [
	                'codigoCuota' => $codigoCuota,
	                'nombreCuota' => $nombreCuota
	            ];
	        }
	    }
	}

	echo json_encode(['cuotas' => $cuotas]);

	$conexion->close();
?>
