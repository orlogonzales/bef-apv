<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
	$fecha = infoTiempo('fecha');
	$j=1;
	$datos = array();

	$sql="SELECT sm_junta_directiva_renuncia_integrantes.idRenunciante, sm_junta_directiva_renuncia_integrantes.idCargoJunta, sm_junta_directiva_cargos.cargoJunta, sm_junta_directiva_renuncia_integrantes.codigoSocio, sm_junta_directiva_renuncia_integrantes.motivoRenuncia, sm_junta_directiva_renuncia_integrantes.fechaRenuncia FROM sm_junta_directiva_renuncia_integrantes INNER JOIN sm_junta_directiva_cargos ON sm_junta_directiva_renuncia_integrantes.idCargoJunta = sm_junta_directiva_cargos.idCargoJunta WHERE sm_junta_directiva_renuncia_integrantes.idJuntaDirectiva = '$idJuntaDirectiva'";
	$juntaDirectiva = $conexion->query($sql);
	$i=1;

	while ($junta = $juntaDirectiva->fetch_assoc()) {
		$idRenunciante     = $junta[idRenunciante];
		$idCargoJunta      = $junta[idCargoJunta];
		$cargoJunta        = $junta[cargoJunta];
		$codigoSocio       = $junta[codigoSocio];
		$nombreSocio       = $junta[nombreSocio];
		$motivoRenuncia    = $junta[motivoRenuncia];
		$fechaRenuncia     = $junta[fechaRenuncia];

		$query = "SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio FROM sm_socios WHERE sm_socios.codigoSocio='$codigoSocio'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$nombreSocio = $resultado[nombreSocio];

		$infoCodigoSocio   = '<span class="badge bg-pink-100">'.$codigoSocio.'</span>';
		$infoFechaRenuncia = infoFecha($fechaRenuncia,'larga');
		$menuOpciones      = '<button type="button" class="btn btn-xs btn-dark-1 btnDetallesRenuncia" data-id="'.$idRenunciante.'">DETALLES</button>';

		$datos[] = array(
			'Nro'          => ceros($j,2),
			'socio'        => $codigoSocio,
			'cargo'        => $cargoJunta,
			'nombreSocio'  => $nombreSocio,
			'renuncia'     => $infoFechaRenuncia,
			'menuOpciones' => $menuOpciones,
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