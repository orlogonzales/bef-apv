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
	$codigoCuota = $_GET['codigoCuota'] ?? 'ALL';
	$estadoPago = $_GET['estadoPago'] ?? 'ALL';
	$fechaInicio = $_GET['fechaInicio'] ?? '';
	$fechaFin = $_GET['fechaFin'] ?? '';

	$idJuntaDirectiva = $conexion->real_escape_string($idJuntaDirectiva);
	$codigoCuota = $conexion->real_escape_string($codigoCuota);
	$estadoPago = $conexion->real_escape_string($estadoPago);

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
		<h1>REPORTE DE CUOTAS</h1>';

	// Display cuota info
	if ($estadoPago !== 'ALL') {
	    $query = "SELECT conceptoCuota, fechaPago FROM sm_mod_cuotas WHERE codigoCuota = '$codigoCuota'";
	    $resultado = $conexion->query($query)->fetch_assoc();
	    $conceptoCuota = $resultado['conceptoCuota'] ?? '';
	    $fechaPago = $resultado['fechaPago'] ?? '';
	    $infoFechaPago = infoFecha($fechaPago, 'normal');
	    $infocuota = $conceptoCuota . ' | ' . strtoupper($infoFechaPago);
	    $reporte .= '<h2>' . $infocuota . '</h2><hr>';
	} else {
	    $reporte .= '<h2>TODAS LAS CUOTAS</h2><hr>';
	}

	$reporte .= '<h2>PRESIDENTE: ' . $nombrePresidente . '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;PERIODO: ' . $infoperiodo . '</h2><br>';

	// Table header
	$reporte .= '
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="textoCen">#</th>
				<th class="textoCen">CODIGO CUOTA</th>
				<th class="textoIzq">CONCEPTO CUOTA</th>
				<th class="textoCen">CODIGO SOCIO</th>
				<th class="textoIzq">SOCIO</th>
				<th class="textoCen">LTS</th>
				<th class="textoDer">CUOTA</th>
				<th class="textoCen">F. PAGO</th>
				<th class="textoCen">PAGO</th>
				<th class="textoCen">CAJA</th>
			</tr>
		</thead>
		<tbody>';

	// Build SQL query for cuotas based on estadoPago and codigoCuota
	$cEstadoPago = $estadoPago === 'SI' ? "AND estadoPago='SP'" : ($estadoPago === 'NO' ? "AND estadoPago='NP'" : '');

	$sql = "SELECT cs.idJuntaDirectiva, cs.codigoCuota, cs.codigoSocio, 
			CONCAT(s.nombre, ' ', s.apPaterno, ' ', s.apMaterno) AS nombreSocio, 
			cs.lotes, cs.montoCuota, cs.montoPago, cs.estadoPago 
			FROM sm_mod_cuotas_socios cs 
			INNER JOIN sm_socios s ON cs.codigoSocio = s.codigoSocio 
			WHERE cs.idJuntaDirectiva = '$idJuntaDirectiva' $cEstadoPago";

	if ($codigoCuota !== 'ALL') {
	    $sql .= " AND cs.codigoCuota = '$codigoCuota'";
	}

	$sql .= " ORDER BY cs.codigoCuota ASC, s.nombre ASC, s.apPaterno ASC, s.apMaterno ASC";


	$consulta = $conexion->query($sql);
	$i = 1;
	$totalCuotas = 0;
	$montosPagados = 0;
	$montosPorCobrar = 0;

	// Iterate over the query results
	while ($dato = $consulta->fetch_assoc()) {
	    $codigoSocio = $dato['codigoSocio'];
	    $codigoCuota = $dato['codigoCuota'];
	    $nombreSocio = $dato['nombreSocio'];
	    $lotesSocio = $dato['lotes'];
	    $montoCuota = $dato['montoCuota'];
	    $estadoPago = $dato['estadoPago'];

	    $infoCodigoCuota = '<span class="badge bg-gray-5">' . htmlspecialchars($codigoCuota) . '</span>';
	    $infoCodigoSocio = '<span class="badge bg-gray-5">' . htmlspecialchars($codigoSocio) . '</span>';
	    $infoMontoCuota = 'S/. ' . moneda($montoCuota);

	    // Fetch concept and payment date
	    $query = "SELECT conceptoCuota, fechaPago FROM sm_mod_cuotas WHERE codigoCuota = '$codigoCuota'";
	    $resultado = $conexion->query($query)->fetch_assoc();
	    $conceptoCuota = $resultado['conceptoCuota'] ?? '';
	    $fechaPago = $resultado['fechaPago'] ?? '';
	    $infoFechaPago = infoFecha($fechaPago, 'normal');

	    // Fetch the paid amount
	    $query = "SELECT SUM(monto) as montoTotal FROM sm_mod_caja WHERE movimiento='ING' 
	              AND idJuntaDirectiva='$idJuntaDirectiva' AND codigoSocio='$codigoSocio' 
	              AND codigoConcepto ='$codigoCuota'";
	    $montoPagado = $conexion->query($query)->fetch_assoc()['montoTotal'] ?? 0;

	    // Determine payment status and verification in caja
	    if ($estadoPago == 'SP' && $montoPagado == $montoCuota) {
	        $infoPago = '<span class="badge badge-success">S/P</span>';
	        $infoCaja = '<span class="badge badge-info">VERIFICADO</span>';
	    } elseif ($estadoPago == 'SP' && $montoPagado != $montoCuota) {
	        $infoPago = '<span class="badge badge-success">P/O</span>';
	        $infoCaja = '<span class="badge badge-danger">ERROR</span>';
	    } else {
	        $infoPago = '<span class="badge badge-warning">N/P</span>';
	        $infoCaja = '';
	    }

	    // Add to report
	    $reporte .= '
			<tr>
				<td class="textoCen">' . ceros($i, 3) . '</td>
				<td class="textoCen">' . $infoCodigoCuota . '</td>
				<td class="textoIzq">' . htmlspecialchars($conceptoCuota) . '</td>
				<td class="textoCen">' . $infoCodigoSocio . '</td>
				<td class="textoIzq">' . htmlspecialchars($nombreSocio) . '</td>
				<td class="textoCen">' . htmlspecialchars($lotesSocio) . '</td>
				<td class="textoDer">' . $infoMontoCuota . '</td>
				<td class="textoCen">' . strtoupper($infoFechaPago) . '</td>
				<td class="textoCen">' . $infoPago . '</td>
				<td class="textoCen">' . $infoCaja . '</td>
			</tr>';

	    // Calculate totals
	    $totalCuotas += $montoCuota;
	    $montosPagados += $montoPagado;
	    $montosPorCobrar += ($montoPagado == 0) ? $montoCuota : 0;

	    $i++;
	}

	// Total summary
	$reporte .= '</tbody></table><br>';
	$reporte .= '
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td><strong>TOTAL MULTAS</strong></td>
					<td class="textoDer">S/. ' . moneda($totalCuotas) . '</td>
				</tr>
				<tr>
					<td><strong>TOTAL PAGADOS</strong></td>
					<td class="textoDer">S/. ' . moneda($montosPagados) . '</td>
				</tr>
				<tr>
					<td><strong>TOTAL POR PAGAR</strong></td>
					<td class="textoDer">S/. ' . moneda($montosPorCobrar) . '</td>
				</tr>
			</tbody>
		</table><br>';

	$reporte .= $impresoPor;
	$reporte .= '</body></html>';

	// Logging the report generation
	$proceso = "IMPRESION DE REPORTE " . $infocuota . ' | JUNTA DIRECTIVA ' . $nombrePresidente . ' DEL PERIODO ' . $infoperiodo;
	$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) 
	        VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	mysqli_query($conexion, $sql);

	// Generating PDF
	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", null, 8, array(0,0,0));
	$dompdf->stream("reporte-cuotas-jd.pdf");
?>