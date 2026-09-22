<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
	$j=1;
	$datos = array();

	$sql="SELECT sm_junta_directiva_cuenta_banco.codigoCuenta, sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_junta_directiva_cuenta_banco INNER JOIN sm_banco_cuentas ON sm_junta_directiva_cuenta_banco.codigoCuenta = sm_banco_cuentas.codigoCuenta INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE idJuntaDirectiva = '$idJuntaDirectiva' ORDER BY sm_junta_directiva_cuenta_banco.codigoCuenta";
	$cuentasJuntaDirectiva = $conexion->query($sql);

	while ($cuenta = $cuentasJuntaDirectiva->fetch_assoc()) {
		$codigoCuenta=$cuenta[codigoCuenta];
		$entidad=$cuenta[entidad];
		$numeroCuenta=$cuenta[numeroCuenta];
		$detalleCuenta=$cuenta[detalle];
		
		$query = "SELECT COUNT(sm_mod_caja.codigoCuenta) AS cuentaUsadas FROM sm_mod_caja WHERE sm_mod_caja.codigoCuenta = '$codigoCuenta'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$cuentaUsadas = $resultado[cuentaUsadas];
		
		if($cuentaUsadas>0){
			$desactiva="disabled";
		}else{
			$desactiva="";
		}

		$menuOpciones='<button type="button" class="btn btn-xs btn-danger btnEliminaCuentaJD" '.$desactiva.' data-cuenta="'.$codigoCuenta.'">ELIMINA</button>';

		$datos[] = array(
			'Nro'           => ceros($j,2),
			'banco'         => $entidad,
			'nroCuenta'     => $numeroCuenta,
			'detalleCuenta' => $detalleCuenta,
			'opciones'      => $menuOpciones,
		);

		$j++;
	}
	$conexion->close();

	$data=array(
		'data' => $datos
	);

	echo json_encode($data);
	exit;
?>