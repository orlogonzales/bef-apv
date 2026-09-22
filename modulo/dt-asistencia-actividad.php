<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	$j=1;
	$tipoActividad=$_POST['tipoActividad'];
	$codigoActividad=$_POST['codigoActividad'];
	$datos = array();
 	$sql="SELECT sm_mod_asistencia.codigoSocio, CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio, sm_socios.dni, sm_mod_asistencia.lotes, sm_mod_asistencia.asistio FROM sm_mod_asistencia INNER JOIN sm_socios ON sm_mod_asistencia.codigoSocio = sm_socios.codigoSocio WHERE codigoActividad = '$codigoActividad' AND sm_mod_asistencia.asistio = 'SI' ORDER BY sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
	$vigenciasJuntaDirectiva = $conexion->query($sql);

	while ($vigencia = $vigenciasJuntaDirectiva->fetch_assoc()) {
		$codigoSocio = $vigencia['codigoSocio'];
		$nombreSocio      = $vigencia['nombreSocio'];
		$dni         = $vigencia['dni'];
		$lotes       = $vigencia['lotes'];
		$asistio     = $vigencia['asistio'];
		$infoCodigoSocio='<span class="badge bg-gray-3">'.$codigoSocio.'</span>';
		$infoDNISocio='<span class="badge bg-gray-3">'.$dni.'</span>';

		$query = "SELECT lotes FROM sm_lotes_socio WHERE codigoSocio = '$codigoSocio'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$lotes = $resultado[lotes];

		$datos[] = array(
			'Nro'         => ceros($j,2),
			'codigoSocio' => $infoCodigoSocio,
			'nombreSocio' => $nombreSocio,
			'dni'         => $infoDNISocio,
			'lotes'       => $lotes,
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