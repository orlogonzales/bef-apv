<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	$j=1;
	$datos = array();

	$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
	$tipoActividad    = $_POST['tipoActividad'];
	$codigoActividad  = $_POST['codigoActividad'];
	$codigoCuenta     = $_POST['codigoCuenta'];
	$fechaInicio      = $_POST['fechaInicio'];
	$fechaFin         = $_POST['fechaFin'];

	if($tipoActividad!='ALL'){
		$sql="SELECT sm_mod_asistencia.tipoActividad, sm_mod_asistencia.codigoActividad, sm_mod_asistencia.codigoSocio, sm_socios.dni, CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio, sm_mod_asistencia.asistio, sm_mod_asistencia.multa, sm_mod_asistencia.estadoPago FROM sm_mod_asistencia INNER JOIN sm_socios ON sm_mod_asistencia.codigoSocio = sm_socios.codigoSocio WHERE tipoActividad = '$tipoActividad' AND codigoActividad = '$codigoActividad' ORDER BY sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
	}else{
		$sql="SELECT sm_mod_asistencia.tipoActividad, sm_mod_asistencia.codigoActividad, sm_mod_asistencia.codigoSocio, sm_socios.dni, CONCAT( sm_socios.nombre, ' ', sm_socios.apPaterno, ' ', sm_socios.apMaterno ) AS nombreSocio, sm_mod_asistencia.asistio, sm_mod_asistencia.multa, sm_mod_asistencia.estadoPago FROM sm_mod_asistencia INNER JOIN sm_socios ON sm_mod_asistencia.codigoSocio = sm_socios.codigoSocio ORDER BY sm_mod_asistencia.tipoActividad ASC";
	}

	$consulta = $conexion->query($sql);
	$totalMultas=0;
	$montosPagados=0;
	$montosPorCobrar=0;
	while ($dato = $consulta->fetch_assoc()) {
		$query = "SELECT COUNT(id) AS perteneceJD FROM sm_mod_actividades WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
		$info = $conexion->query($query);
		$resultado = $info->fetch_assoc();
		$perteneceJD = $resultado[perteneceJD];

		if($perteneceJD>0){
			if(!empty($_POST['idJuntaDirectiva'])){
				$idJuntaDirectiva=$dato[idJuntaDirectiva];
			}
			
			if(!empty($_POST['codigoActividad'])){
				$codigoActividad=$dato[codigoActividad];
			}

			if(!empty($_POST['tipoActividad'])){
				$tipoActividad=$dato[tipoActividad];
			}

			$codigoSocio=$dato[codigoSocio];
			$dni=$dato[dni];
			$nombreSocio=$dato[nombreSocio];
			$asistio=$dato[asistio];
			$multa=$dato[multa];
			$estadoPago=$dato[estadoPago];
			$infoCodigoActividad='<span class="badge bg-gray-5">'.$codigoActividad.'</span>';

			if($tipoActividad=='FAE'){
				$infoTipoActividad ='FAENA';
			}else if($tipoActividad=='ASA'){
				$infoTipoActividad ='ASAMBLEA';
			}

			if($asistio=='SI'){
				$infoAsistencia='<span class="badge bg-success">SI</span>';
				$infoMulta='';
			}else{
				$infoAsistencia='<span class="badge bg-gray-5">NO</span>';
				$infoMulta='S/. '.$multa;
			}

			$query = "SELECT COUNT(id) AS lotesSocio FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
			$info = $conexion->query($query);
			$resultado = $info->fetch_assoc();
			$lotesSocio = $resultado[lotesSocio];

			$query = "SELECT temaActividad, fechaActividad FROM sm_mod_actividades WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND tipoActividad = '$tipoActividad' AND codigoActividad = '$codigoActividad'";
			$info = $conexion->query($query);
			$resultado = $info->fetch_assoc();
			$nombreActividad = $resultado[temaActividad];
			$fechaActividad = $resultado[fechaActividad];
			$infoFechaActividad=infoFecha($fechaActividad,'normal');

			$query = "SELECT monto, fechaOperacion FROM sm_mod_caja WHERE movimiento='ING' AND idJuntaDirectiva='$idJuntaDirectiva' AND codigoSocio='$codigoSocio' AND codigoConcepto ='$codigoActividad'";
			$info = $conexion->query($query);
			$resultado = $info->fetch_assoc();
			$monto = $resultado[monto];
			$fechaOperacion = $resultado[fechaOperacion];
			
			if($estadoPago=='SP' && $monto==$multa){
				$infoPago = '<span class="badge badge-success">S/P</span>';
				$infoCaja = '<span class="badge badge-info">VERIFICADO</span>';
			}else if($estadoPago=='SP' && $monto!=$multa){
				$infoPago = '<span class="badge badge-success">P/O</span>';
				$infoCaja = '<span class="badge badge-danger">ERROR</span>';
			}else{
				$infoPago = '<span class="badge badge-warning">N/P</span>';
				$infoCaja = '';
			}

			$menuOpciones='<button type="button" class="btn btn-xs btn-danger btnEliminaCargo" '.$desactiva.' data-id="'.$idCargoJunta.'" data-cargo="'.$cargoJunta.'">ELIMINA</button>';

			$datos[] = array(
				'Nro'                => ceros($j,2),
				'codigoActividad'    => $infoCodigoActividad,
				'infoTipoActividad'  => $infoTipoActividad,
				'nombreActividad'    => $nombreActividad,
				'fechaActividad'     => $infoFechaActividad,
				'nombreSocio'        => $nombreSocio,
				'lotesSocio'         => $lotesSocio,
				'asistenciActividad' => $infoAsistencia,
				'infoMulta'          => $infoMulta,
				'infoPago'           => $infoPago,
				'infoCaja'           => $infoCaja,
			);

			$totalMultas=$totalMultas+$multa;
			$infoTotalMultas='S/. '.moneda($totalMultas);
			
			if($monto>0){
				$montosPagados=$montosPagados+$monto;
				$infoMontosPagados='S/. '.moneda($montosPagados);
			}else{
				$montosPorCobrar=$montosPorCobrar+$multa;
				$InfoMontosPorCobrar='S/. '.moneda($montosPorCobrar);
			}

			$j++;
		}else{
			$datos[] = array();
		}
	}
	$conexion->close();

	$data=array(
		'data'            => $datos,
		'montoMultas'     => $totalMultas,
		'totalMultas'     => $infoTotalMultas,
		'montosPagados'   => $infoMontosPagados,
		'montosPorCobrar' => $InfoMontosPorCobrar
	);

	echo json_encode($data);
	exit;
?>