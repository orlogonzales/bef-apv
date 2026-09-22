<?php
	include '../php/conexion.php';
	include '../php/funciones.php';

	header('Content-Type: application/json');

	if (!isset($_POST['idJuntaDirectiva']) || !isset($_POST['codigoCuota'])) {
	    echo json_encode(['error' => 'Faltan datos']);
	    exit;
	}
	
	$idJuntaDirectiva = mysqli_real_escape_string($conexion, $_POST['idJuntaDirectiva']);
	$codigoCuota = mysqli_real_escape_string($conexion, $_POST['codigoCuota']);

	$cuentasBancarias = [];

	if (!empty($idJuntaDirectiva) && !empty($codigoCuota)) {
	    $sql = "SELECT sm_mod_cuotas.codigoCuenta, sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_mod_cuotas INNER JOIN sm_banco_cuentas ON sm_mod_cuotas.codigoCuenta = sm_banco_cuentas.codigoCuenta INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE codigoCuota = '$codigoCuota' AND idJuntaDirectiva = '$idJuntaDirectiva' ORDER BY sm_banco_cuentas.codigoBanco ASC";
	    $resultados = $conexion->query($sql);
	    if ($resultados && $resultados->num_rows > 0) {
	        while ($fila = $resultados->fetch_assoc()) {
	            $codigoCuenta = $fila['codigoCuenta'];
	            $entidad      = $fila['entidad'];
	            $numeroCuenta = $fila['numeroCuenta'];
	            $detalle      = $fila['detalle'];
	            $infoCuenta   = "$entidad | $numeroCuenta | $detalle";

	            $cuentasBancarias[] = [
	                'codigoCuenta' => $codigoCuenta,
	                'infoCuenta' => $infoCuenta
	            ];
	        }
	    }
	}

	echo json_encode(['cuentasBancarias' => $cuentasBancarias]);

	$conexion->close();
?>