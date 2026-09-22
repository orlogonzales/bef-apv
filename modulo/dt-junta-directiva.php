<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
	$fecha = infoTiempo('fecha');
	$j=1;
	$datos = array();

	$sql="SELECT sm_junta_directiva_integrantes.codigoSocio, sm_junta_directiva_integrantes.idCargoJunta, sm_junta_directiva_cargos.cargoJunta, sm_junta_directiva_integrantes.renunciaJunta, CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio INNER JOIN sm_junta_directiva_cargos ON sm_junta_directiva_integrantes.idCargoJunta = sm_junta_directiva_cargos.idCargoJunta WHERE idJuntaDirectiva = '$idJuntaDirectiva' ORDER BY sm_junta_directiva_integrantes.idCargoJunta ASC";
	$juntaDirectiva = $conexion->query($sql);
	$i=1;

	while ($junta = $juntaDirectiva->fetch_assoc()) {
		$codigoSocio     = $junta[codigoSocio];
		$idCargoJunta    = $junta[idCargoJunta];
		$cargoJunta      = $junta[cargoJunta];
		$renunciaJunta   = $junta[renunciaJunta];
		$nombreSocio     = $junta[nombreSocio];
		$infoCodigoSocio = $codigoSocio;

		if($renunciaJunta==1){
			$query = "SELECT COUNT(id) AS cambios FROM sm_junta_directiva_renuncia_integrantes WHERE idJuntaDirectiva='$idJuntaDirectiva' AND idCargoJunta='$idCargoJunta'";
			$consulta = $conexion->query($query);
			$resultado = $consulta->fetch_assoc();
			$cambios = $resultado[cambios];

			if($cambios>1){
				$infoRenuncia='<span class="badge badge-danger">'.ceros($cambios,2).' VECES</span>';
			}else{
				$infoRenuncia='<span class="badge badge-danger">'.ceros($cambios,2).' VEZ</span>';
			}

		}else{
			$infoRenuncia='';
			$cambios='';
		}

		$menuOpciones='<button type="button" class="btn btn-xs btn-warning btnCambiarIntegrante" data-socio="'.$codigoSocio.'" data-idcargo="'.$idCargoJunta.'">RENUNCIA</button>';

		$datos[] = array(
			'Nro'          => ceros($j,2),
			'socio'        => $infoCodigoSocio,
			'cargo'        => $cargoJunta,
			'renuncia'     => $infoRenuncia,
			'nombreSocio'  => $nombreSocio,
			'menuOpciones' => $menuOpciones
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