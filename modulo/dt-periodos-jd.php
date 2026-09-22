<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');

	$fecha = infoTiempo('fecha');
	$j=1;
	$datos = array();

	$sql="SELECT sm_junta_directiva.idJuntaDirectiva, sm_junta_directiva.ratificaJunta, sm_junta_directiva.fechaPeriodo, sm_junta_directiva.idVigencia, sm_junta_directiva.fechaFinPeriodo, sm_junta_directiva.extensionJuntaDirectiva FROM sm_junta_directiva ORDER BY fechaPeriodo DESC";
	$juntaDirectiva = $conexion->query($sql);

	while ($junta = $juntaDirectiva->fetch_assoc()) {
		$idJuntaDirectiva        = $junta[idJuntaDirectiva];
		$ratificaJunta           = $junta[ratificaJunta];
		$fechaPeriodo            = $junta[fechaPeriodo];
		$idVigencia              = $junta[idVigencia];
		$fechaFinPeriodo         = $junta[fechaFinPeriodo];
		$extensionJuntaDirectiva = $junta[extensionJuntaDirectiva];

		$query = "SELECT vigenciaJunta FROM sm_junta_directiva_vigencia WHERE idVigencia = '$idVigencia'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$vigenciaJunta = $resultado[vigenciaJunta];
		$infoPeriodo = $vigenciaJunta/12;
		
		$query="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE sm_junta_directiva_integrantes.idJuntaDirectiva = '$idJuntaDirectiva' AND sm_junta_directiva_integrantes.idCargoJunta = 1";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$nombrePresidente=$resultado[nombrePresidente];

		$query="SELECT sm_junta_directiva_fin_periodo.tiempo FROM sm_junta_directiva_fin_periodo WHERE id='1'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$tiempo=$resultado[tiempo];

		$query="SELECT sm_bancos.entidad, sm_banco_cuentas.numeroCuenta FROM sm_junta_directiva_cuenta_banco INNER JOIN sm_banco_cuentas ON sm_junta_directiva_cuenta_banco.codigoCuenta = sm_banco_cuentas.codigoCuenta INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE sm_junta_directiva_cuenta_banco.idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$entidad=$resultado[entidad];
		$numeroCuenta=$resultado[numeroCuenta];
		$infoCuenta=$entidad.' | '.$numeroCuenta;

		$query="SELECT COUNT(idJuntaDirectiva) AS nroCuentas FROM sm_junta_directiva_cuenta_banco WHERE idJuntaDirectiva='$idJuntaDirectiva'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$nroCuentas = $resultado[nroCuentas];

		if($nroCuentas>0){
			if($nroCuentas>1){
				$infoCuenta = '<button type="button" data-toggle="tooltip" title="VER CUENTAS BANCO" class="btn btn-xs btn-danger btnVerCuentasJD" data-id="'.$idJuntaDirectiva.'">'.ceros($nroCuentas,2).' CUENTAS DE BANCO</button>';
			}else{
				$infoCuenta = '<button type="button" data-toggle="tooltip" title="VER CUENTAS BANCO" class="btn btn-xs btn-danger btnVerCuentasJD" data-id="'.$idJuntaDirectiva.'">'.ceros($nroCuentas,2).' CUENTA DE BANCO</button>';
			}
		}else{
			
			$infoCuenta = '<button type="button" data-toggle="tooltip" title="VER CUENTAS BANCO" class="btn btn-xs btn-warning btnVerCuentasJD" data-id="'.$idJuntaDirectiva.'">AGREGAR CUENTA DE BANCO</button>';
		}

		$feFP = new DateTime($fechaFinPeriodo);
		$intervalFP = new DateInterval('P'.$tiempo.'M');
		$feFP->add($intervalFP);
		$fechaFinPeriodoGracia = $feFP->format('Y-m-d');

		$infoFechaInicio= '<span class="badge bg-slate-800">'.infoFecha($fechaPeriodo, 'normal').'</span>';
		$infoFechaFin= '<span class="badge bg-slate-800">'.infoFecha($fechaFinPeriodo, 'normal').'</span>';
		$infoCodigoJunta='<span class="badge badge-secondary">'.$idJuntaDirectiva.'</span>';

		if($ratificaJunta==0){
			$infoRatificado='<span class="badge bg-slate-800">NO</span>';
		}else{
			$infoRatificado='<span class="badge badge-success">SI</span>';
		}

		/*
		if($j==1){
			$infoRatificado='';
		}else{
			$infoRatificado=$infoRatificado;
		}
		*/

		$duracionVigencia=$vigenciaJunta/12;

		if($vigenciaJunta>1){
			$infoPeriodo=$duracionVigencia.' AÑOS';
		}else{
			$infoPeriodo=$duracionVigencia.' AÑO';
		}

		if($extensionJuntaDirectiva==1){
			$infoExtensionJD='<span class="badge bg-danger-800">AMPLIADO</span>';
		}else{
			$infoExtensionJD='';
		}

		//$fecha='2019-01-11';
		$inicioPeriodo = new DateTime($fechaPeriodo);
		$finPeriodo = new DateTime($fechaFinPeriodo);
		$FinPeriodoGracia = new DateTime($fechaFinPeriodoGracia);		
		$fechaActual = new DateTime($fecha);

		if($ratificaJunta==1){
			$desactivaModifica="disabled";
		}else{
			if ($fechaActual >= $inicioPeriodo && $fechaActual <= $finPeriodo) {
			    $desactivaModifica="";
			} else {
			    $desactivaModifica="disabled";
			}
		}

		if($ratificaJunta==1){
			$desactivaRatifica="disabled";
		}else{
			if ($fechaActual >= $finPeriodo && $fechaActual <= $FinPeriodoGracia) {
			    $desactivaRatifica="";
			} else {
			    $desactivaRatifica="disabled";
			}
		}
		
		if($fechaActual>=$finPeriodo && $fechaActual <= $FinPeriodoGracia){
			$desactivaAmpliar="";
		}else{
			$desactivaAmpliar="disabled";
		}


		$menuOpciones='
			<button type="button" data-toggle="tooltip" title="VER JUNTA DIRECTIVA" class="btn btn-xs btn-dark btnVerJD" data-id="'.$idJuntaDirectiva.'">VER</button>
			<button type="button" data-toggle="tooltip" title="AMPLIAR PERIODO DE JUNTA" class="btn btn-xs btn-info btnAmpliarJD" '.$desactivaAmpliar.' data-id="'.$idJuntaDirectiva.'">AMPLIAR</button>
			<button type="button" data-toggle="tooltip" title="MODIFICA JUNTA DIRECTIVA" class="btn btn-xs btn-danger btnModificaJD" '.$desactivaModifica.' data-id="'.$idJuntaDirectiva.'">MODIFICA</button>
			<button type="button" data-toggle="tooltip" title="RATIFICA JUNTA DIRECTIVA" class="btn btn-xs btn-success btnRatificaJD" '.$desactivaRatifica.' data-id="'.$idJuntaDirectiva.'">RATIFICA</button>
		';

		$datos[] = array(
			'Nro'              => ceros($j,2),
			'codigoJunta'      => $infoCodigoJunta,
			'nombrePresidente' => $nombrePresidente,
			'cuentaBancaria'   => $infoCuenta,
			'fechaInicio'      => $infoFechaInicio,
			'periodo'          => $infoPeriodo,
			'ampliacion'       => $infoExtensionJD,
			'fechaFin'         => $infoFechaFin,
			'ratificado'       => $infoRatificado,
			'menuOpciones'     => $menuOpciones,
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