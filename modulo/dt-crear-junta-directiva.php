<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	session_start();
	if($_SESSION['crearJD']){
		$fechaPeriodo = $_SESSION['crearJD']['baseJD']['fechaPeriodo'];
		$idVigencia   = $_SESSION['crearJD']['baseJD']['idVigencia'];
		$codigoBanco  = $_SESSION['crearJD']['cuentaBancoJunta']['codigoBanco'];
		$codigoCuenta = $_SESSION['crearJD']['cuentaBancoJunta']['codigoCuenta'];
		$cargosSocios = $_SESSION['crearJD']['cargosSocios'];
	}
	ksort($cargosSocios);

	$j=1;
	$datos = array();

	$sql="SELECT idCargoJunta, cargoJunta FROM sm_junta_directiva_cargos ORDER BY idCargoJunta ASC";
	$cargosJuntaDirectiva = $conexion->query($sql);

	while ($cargos = $cargosJuntaDirectiva->fetch_assoc()) {
		$idCargoJunta=$cargos[idCargoJunta];
		$cargoJunta=$cargos[cargoJunta];

		$codigoSocio = $_SESSION['crearJD']['cargosSocios'][$j];
		$query = "SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio FROM sm_socios WHERE sm_socios.codigoSocio = '$codigoSocio'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$nombreSocio = $resultado[nombreSocio];

		if(strlen($codigoSocio)>0){
			$menuOpciones='<button type="button" class="btn btn-xs btn-danger quitaCargoJunta" data-id="'.$idCargoJunta.'">ELIMINA</button>';
		}else{
			$menuOpciones='<button type="button" class="btn btn-xs btn-success asignaCargoJunta" data-id="'.$idCargoJunta.'">AGREGA</button>';
		}

		$datos[] = array(
			'Nro'          => ceros($j,2),
			'cargo'        => $cargoJunta,
			'menuOpciones' => $menuOpciones,
			'nombreSocio'  => $nombreSocio
		);

		$j++;
	}
	$conexion->close();

	$data=array(
		'data'           => $datos
	);

	echo json_encode($data);
	exit;
?>