<?php
	session_start();
	set_time_limit(300);
	ini_set("memory_limit", "512M");

	$ruta = "../";
	require_once($ruta . 'php/funciones.php');
	require_once($ruta . 'php/conexion.php');
	require_once $ruta . 'dompdf/lib/html5lib/Parser.php';
	require_once $ruta . 'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
	require_once $ruta . 'dompdf/lib/php-svg-lib/src/autoload.php';
	require_once $ruta . 'dompdf/src/Autoloader.php';
	Dompdf\Autoloader::register();
	use Dompdf\Dompdf;

	$conexion = conexionDB();
	
	$idJuntaDirectiva = $_GET['idJuntaDirectiva'] ?? '';
	$movimiento       = $_GET['movimiento'] ?? '';
	$tipoActividad    = $_GET['tipoActividad'] ?? '';
	$codigoConcepto   = $_GET['codigoConcepto'] ?? '';
	$codigoCuenta     = $_GET['codigoCuenta'] ?? '';
	$fechaInicio      = $_GET['fechaInicio'] ?? '';
	$fechaFin         = $_GET['fechaFin'] ?? '';

	$idJuntaDirectiva = $conexion->real_escape_string($idJuntaDirectiva);

	$hoy = fechaSQL(infoTiempo('fechaHoy'));
	$hora = infoTiempo('hora');
	$dniUsuario = $_SESSION['dni_apv'];
	$impresoPor = '<span class="infoImpresion textoMayuscula"> Impreso por:' . datoUsuario($dniUsuario, 'nombreFull') . ' - ' . infoFecha($hoy, 'larga') . ' - ' . horacorta($hora) . '</span>';

	$query = "SELECT CONCAT(sm_socios.nombre,' ', sm_socios.apPaterno,' ', sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
	$row=mysqli_query($conexion,$query);
	$dato=mysqli_fetch_array($row);
	$nombrePresidente=$dato['nombrePresidente'];

	$query = "SELECT fechaPeriodo, fechaFinPeriodo FROM sm_junta_directiva WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
	$row=mysqli_query($conexion,$query);
	$dato=mysqli_fetch_array($row);
	$fechaPeriodo=$dato['fechaPeriodo'];
	$fechaFinPeriodo=$dato['fechaFinPeriodo'];
	$inicioPeriodo=infoFecha($fechaPeriodo,'year');
	$finPeriodo=infoFecha($fechaFinPeriodo,'year');
	$infoperiodo= $inicioPeriodo.' - '.$finPeriodo;

	// Start report generation
	$reporte = '
		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title>Reporte</title>
			<link href="' . $ruta . 'assets/css/reportes.css" rel="stylesheet" type="text/css">
			<link href="' . $ruta . 'assets/css/meeting.css" rel="stylesheet" type="text/css">
		</head>
		<body>
		<h1>REPORTE DE CAJA</h1>';
	$reporte .= '<h2>PRESIDENTE: ' . $nombrePresidente . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;PERIODO: ' . $infoperiodo . '</h2><br>';

	// Table header
	$reporte .= '
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="textoCen">#</th>
				<th class="textoCen">TIPO</th>
				<th class="textoIzq">CONCEPTO</th>
				<th class="textoCen">MOV</th>
				<th class="textoCen">F. OPERACION</th>
				<th class="textoCen">CODIGO SOCIO</th>
				<th class="textoIzq">SOCIO</th>
				<th class="textoCen">LTS</th>
				<th class="textoDer">MONTO</th>
				<th class="textoCen">REGISTRO</th>
			</tr>
		</thead>
		<tbody>';
	
	if($codigoConcepto!='ALL'){
	    $cCodigoConcepto="AND codigoConcepto='$codigoConcepto'";
	}else{
	    $cCodigoConcepto="";
	}

	if(strlen($codigoCuenta)>0){
	    $cCodigoCuenta="AND codigoCuenta='$codigoCuenta'";
	}else{
	    $cCodigoCuenta="";
	}


	$sql="SELECT idJuntaDirectiva, movimiento, fechaOperacion, tipoActividad, codigoSocio, codigoConcepto, codigoCuenta, monto, detalleConcepto, fecha, hora, usuario FROM sm_mod_caja WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND movimiento='$movimiento' AND tipoActividad='$tipoActividad' $cCodigoConcepto $cCodigoConcepto AND fechaOperacion BETWEEN '$fechaInicio' AND '$fechaFin' ORDER BY fechaOperacion ASC";
	$consulta = $conexion->query($sql);
	$totalConsulta = 0;
	$i = 1;
	// Iterate over the query results
	while ($dato = $consulta->fetch_assoc()) {
	   $idJuntaDirectiva   = $dato['idJuntaDirectiva'];
	    $movimiento         = $dato['movimiento'];
	    $fechaOperacion     = $dato['fechaOperacion'];
	    $tipoActividad      = $dato['tipoActividad'];
	    $codigoSocio        = $dato['codigoSocio'];
	    $codigoConcepto     = $dato['codigoConcepto'];
	    $codigoCuenta       = $dato['codigoCuenta'];
	    $monto              = $dato['monto'];
	    $detalleConcepto    = $dato['detalleConcepto'];
	    $fechaRegistro      = $dato['fecha'];
	    $horaRegistro       = $dato['hora'];
	    $usuario            = $dato['usuario'];
	    $infofechaOperacion = strtoupper(infofecha($fechaOperacion,'normal'));
	    $infoCodigoSocio    ='<span class="badge bg-gray-3">'.$codigoSocio.'</span>';
	    if($monto>0){
	        $infoMonto   = 'S/. '.moneda($monto);
	    }else{
	        $infoMonto   = '';
	    }

	    if($movimiento=='ING'){
	        $infoTipoMovimiento='<span class="badge badge-success">INGRESO</span>';
	    }else{
	        $infoTipoMovimiento='<span class="badge badge-danger">EGRESO</span>';
	    }

	    if($tipoActividad=='ASA'){
	        $infoTipoOperacion='<span class="badge bg-gray-5">ASAMBLEA</span>';
	    }else if($tipoActividad=='FAE'){
	        $infoTipoOperacion='<span class="badge bg-gray-4">FAENA</span>';
	    }else{
	        $infoTipoOperacion='<span class="badge bg-gray-3">CUOTA</span>';
	    }

	    if($tipoActividad=='ASA' || $tipoActividad=='FAE'){
	        $query="SELECT temaActividad FROM sm_mod_actividades WHERE codigoActividad = '$codigoConcepto'";
	        $info = $conexion->query($query);
	        $res = $info->fetch_assoc();
	        $conceptoMovimiento = $res['temaActividad'];
	    }else{
	        $query="SELECT conceptoCuota FROM sm_mod_cuotas WHERE codigoCuota = '$codigoConcepto'";
	        $info = $conexion->query($query);
	        $res = $info->fetch_assoc();
	        $conceptoMovimiento = $res['conceptoCuota'];
	    }

	    $query="SELECT CONCAT(nombre,' ',apPaterno,' ',apMaterno) AS nombreSocio FROM sm_socios WHERE codigoSocio = '$codigoSocio'";
	    $info = $conexion->query($query);
	    $res = $info->fetch_assoc();
	    $nombreSocio = $res['nombreSocio'];

	    $query = "SELECT COUNT(id) AS lotesSocio FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
	    $info = $conexion->query($query);
	    $resultado = $info->fetch_assoc();
	    $lotesSocio = $resultado['lotesSocio'];

	    $query = "SELECT nombre, paterno, materno FROM sm_usuarios WHERE dni='$usuario'";
	    $info = $conexion->query($query);
	    $res = $info->fetch_assoc();
	    $nombre          = $res['nombre'];
	    $paterno         = $res['paterno'];
	    $materno         = $res['materno'];
	    $usuarioRegistro = substr($nombre,0,1).substr($paterno,0,1).substr($materno,0,1);
	    $infoRegistro    = '<span class="badge bg-gray-5">'.strtoupper($usuarioRegistro.' | '.infoFecha($fechaRegistro,'normal')).'</span>';

	    // Add to report
	    $reporte .= '
			<tr>
				<td class="textoCen">' . ceros($i, 3) . '</td>
				<td class="textoCen">' . $infoTipoOperacion. '</td>
				<td class="textoIzq">' . $conceptoMovimiento. '</td>
				<td class="textoCen">' . $infoTipoMovimiento. '</td>
				<td class="textoCen">' . $infofechaOperacion. '</td>
				<td class="textoCen">' . $infoCodigoSocio. '</td>
				<td class="textoIzq">' . $nombreSocio. '</td>
				<td class="textoCen">' . $lotesSocio. '</td>
				<td class="textoDer">' . $infoMonto. '</td>
				<td class="textoCen">' . $infoRegistro. '</td>
			</tr>';

	    $totalConsulta += $monto;
	    $i++;
	}

	// Total summary
	$reporte .= '</tbody></table><br>';
	$reporte .= '
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td><strong>TOTAL CONSULTA</strong></td>
					<td class="textoDer">S/. ' . moneda($totalConsulta) . '</td>
				</tr>
			</tbody>
		</table><br>';

	$reporte .= $impresoPor;
	$reporte .= '</body></html>';

	$proceso = "IMPRESION DE REPORTE DE CAJA | JUNTA DIRECTIVA ". $nombrePresidente . ' DEL PERIODO ' . $infoperiodo;
	$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario)  VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	mysqli_query($conexion, $sql);

	// Generating PDF
	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", null, 8, array(0,0,0));
	$dompdf->stream("reporte-caja-jd.pdf");
?>