<?php
	ini_set("memory_limit","512M");
	set_time_limit(500);
	session_start();

	$ruta="../";
	require_once($ruta.'php/funciones.php');
	require_once $ruta.'dompdf/lib/html5lib/Parser.php';
	require_once $ruta.'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
	require_once $ruta.'dompdf/lib/php-svg-lib/src/autoload.php';
	require_once $ruta.'dompdf/src/Autoloader.php';
	Dompdf\Autoloader::register();
	use Dompdf\Dompdf;

	$conexion      =conexionDB();
	$conceptoPago  =$_GET[conceptoPago];
	$opcion        =$_GET[opcion];
	$hoy           =fechaSQL(infoTiempo('fechaHoy'));
	$hora          =infoTiempo('hora');
	$dniUsuario    =$_SESSION['dni_apv'];
	$aforo         =infoCuota($idJuntaDirectiva,$conceptoPago,'aforo');
	$pagaron       =infoCuota($idJuntaDirectiva,$conceptoPago,'pagaron');
	$deben         =infoCuota($idJuntaDirectiva,$conceptoPago,'deben');
	$totalCuotas   =infoCuota($idJuntaDirectiva,$conceptoPago,'totalCuotas');
	$totalPagados  =infoCuota($idJuntaDirectiva,$conceptoPago,'totalPagados');
	$totalDeben    =infoCuota($idJuntaDirectiva,$conceptoPago,'totalDeben');
	$totalPorpagar =$totalCuotas-$totalPagados;
	$impresoPor   ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';

	if($totalCuotas>0){ $infoTotalCuotas='S/. '.moneda($totalCuotas); }else{ $infoTotalCuotas='----'; }
	if($pagaron>0){ $nPagaron=ceros($pagaron,4); }else{ $nPagaron='----'; }
	if($deben>0){ $nDeben=ceros($deben,4); }else{ $nDeben='----'; }
	if($totalPagados>0){ $mTotalPagado='S/. '.moneda($totalPagados); }else{ $mTotalPagado='----'; }
	if($totalDeben>0){ $mTotalDeben='S/. '.moneda($totalDeben); }else{ $mTotalDeben='----'; }

	$estadisticas='
	<table class="infoTexto resaltar" width="80%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td><strong>SOCIOS CON CUOTA:</strong> '.ceros($aforo,4).'</td>
			<td><strong>MONTO TOTAL EN CUOTAS:</strong> '.$infoTotalCuotas.'</td>
		</tr>
		<tr>
			<td><strong>NRO. DE SOCIOS QUE PAGARON:</strong> '.$nPagaron.'</td>
			<td><strong>TOTAL CUOTAS PAGADAS:</strong> '.$mTotalPagado.'</td>
		</tr>
		<tr>
			<td><strong>NRO. DE SOCIOS QUE DEBEN:</strong> '.$nDeben.'</td>
			<td><strong>TOTAL POR PAGAR:</strong> '.$mTotalDeben.'</td>
		</tr>
	</table>
	<br><hr><br>
	';

	$reporte='
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml">
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Reporte</title>
		<link href="'.$ruta.'assets/css/reportes.css" rel="stylesheet" type="text/css">
		<link href="'.$ruta.'assets/css/meeting.css" rel="stylesheet" type="text/css">
		</head>
		<body>
	';

	if($opcion=="reporte_socios"){
		$nombreArchivo="reporte-socios-cuota-".$conceptoPago;
		$titulo="REPORTE DE CUOTAS";
		$subtitulo="CUOTAS PAGADAS Y NO PAGADAS";
		$codigo="CODIGO DE CUOTA - ".$conceptoPago;
		
		$reporte.='
			<h1>'.$titulo.'</h1>
			<h2>'.$subtitulo.'</h2>
			<h3>'.$codigo.'</h3>
			'.$estadisticas.'
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">CODIGO SOCIO</th>
					<th class="textoIzq">APELLIDO PATERNO</th>
					<th class="textoIzq">APELLIDO MATERNO</th>
					<th class="textoIzq">NOMBRE</th>
					<th class="textoCen">DNI</th>
					<th class="textoCen">LOTES</th>
					<th class="textoCen">CUOTA</th>
					<th class="textoCen">MONTO</th>
					<th class="textoCen">ESTADO</th>
					<th class="textoCen">CAJA</th>
				</tr>
			</thead>
			<tbody>
		';

		$sql="SELECT sm_mod_cuotas_socios.codigoCuota AS codigoCuota, sm_mod_cuotas_socios.codigoSocio AS codigoSocio, sm_mod_cuotas_socios.lotes AS lotes, sm_mod_cuotas_socios.montoCuota AS montoCuota, sm_mod_cuotas_socios.montoPago AS montoPago, sm_mod_cuotas_socios.estadoPago AS estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_cuotas_socios, sm_socios WHERE codigoCuota='$conceptoPago' AND sm_mod_cuotas_socios.codigoSocio=sm_socios.codigoSocio ORDER BY sm_socios.apPaterno ASC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$codigoSocio =$n[codigoSocio];
			$lotes       =$n[lotes];
			$montoCuota  =$n[montoCuota];
			$montoPago   =$n[montoPago];
			$estadoPago  =$n[estadoPago];
			$dni         =$n[dni];
			$nombre      =$n[nombre];
			$apPaterno   =$n[apPaterno];
			$apMaterno   =$n[apMaterno];
			$programado  =conceptoProgramado($codigoSocio,$conceptoPago);

			if($estadoPago=="NP" and $programado==0){
				$estado       ='PENDIENTE';
				$verificaPago ="PEN";
			}

			if($estadoPago=="MP" and $programado>=2){
				$estado='PENDIENTE | '.ceros($programado,2).' FECHAS';
				$verificaPago ="PEN";
			}

			if($estadoPago=="SP" and $programado==0){
				$estado       ='PAGADO';
				$verificaPago =verificaPago($conceptoPago,$codigoSocio,$montoPago);
			}

			if($estadoPago=="SP" and $programado>=2){
				$estado       ='PAGADO | '.ceros($programado,2).' FECHAS';
				$verificaPago =verificaPagosProgramados($conceptoPago,$codigoSocio,$montoPago);
			}

			if($verificaPago=="PEN"){ $caja='---'; }
			if($verificaPago=="OK"){ $caja='CAJA'; }
			if($verificaPago=="ERROR"){ $caja='ERROR'; }
		
			$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,4).'</td>
					<td class="textoCen">'.$codigoSocio.'</td>
					<td class="textoIzq">'.texto($apPaterno).'</td>
					<td class="textoIzq">'.texto($apMaterno).'</td>
					<td class="textoIzq">'.texto($nombre).'</td>
					<td class="textoCen">'.$dni.'</td>
					<td class="textoCen">'.ceros($lotes,2).' LOTES</td>
					<td class="textoDer resaltar">S/. '.moneda($montoCuota).'</td>
					<td class="textoDer resaltar">S/. '.moneda($montoPago).'</td>
					<td class="textoCen">'.$estado.'</td>
					<td class="textoCen">'.$caja.'</td>
				</tr>
			';

			$i++;
		}

		$reporte.='
				<tr>
					'.$totales.'
				</tr>
			</tbody>
			</table>
		';
	}

	if($opcion=="reporte_socios_pagaron"){
		$nombreArchivo="reporte-socios-cuota-".$conceptoPago;
		$titulo="REPORTE DE CUOTAS";
		$subtitulo="CUOTAS PAGADAS";
		$codigo="CODIGO DE CUOTA - ".$conceptoPago;
		
		$reporte.='
			<h1>'.$titulo.'</h1>
			<h2>'.$subtitulo.'</h2>
			<h3>'.$codigo.'</h3>
			'.$estadisticas.'
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">CODIGO SOCIO</th>
					<th class="textoIzq">APELLIDO PATERNO</th>
					<th class="textoIzq">APELLIDO MATERNO</th>
					<th class="textoIzq">NOMBRE</th>
					<th class="textoCen">DNI</th>
					<th class="textoCen">LOTES</th>
					<th class="textoCen">CUOTA</th>
					<th class="textoCen">MONTO</th>
					<th class="textoCen">ESTADO</th>
					<th class="textoCen">CAJA</th>
				</tr>
			</thead>
			<tbody>
		';

		$sql="SELECT sm_mod_cuotas_socios.codigoCuota AS codigoCuota, sm_mod_cuotas_socios.codigoSocio AS codigoSocio, sm_mod_cuotas_socios.lotes AS lotes, sm_mod_cuotas_socios.montoCuota AS montoCuota, sm_mod_cuotas_socios.montoPago AS montoPago, sm_mod_cuotas_socios.estadoPago AS estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_cuotas_socios, sm_socios WHERE codigoCuota='$conceptoPago' AND estadoPago='SP' AND sm_mod_cuotas_socios.codigoSocio=sm_socios.codigoSocio ORDER BY sm_socios.apPaterno ASC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$codigoSocio =$n[codigoSocio];
			$lotes       =$n[lotes];
			$montoCuota  =$n[montoCuota];
			$montoPago   =$n[montoPago];
			$estadoPago  =$n[estadoPago];
			$dni         =$n[dni];
			$nombre      =$n[nombre];
			$apPaterno   =$n[apPaterno];
			$apMaterno   =$n[apMaterno];
			$programado  =conceptoProgramado($codigoSocio,$conceptoPago);

			if($estadoPago=="NP" and $programado==0){
				$estado       ='PENDIENTE';
				$verificaPago ="PEN";
			}

			if($estadoPago=="MP" and $programado>=2){
				$estado='PENDIENTE | '.ceros($programado,2).' FECHAS';
				$verificaPago ="PEN";
			}

			if($estadoPago=="SP" and $programado==0){
				$estado       ='PAGADO';
				$verificaPago =verificaPago($conceptoPago,$codigoSocio,$montoPago);
			}

			if($estadoPago=="SP" and $programado>=2){
				$estado       ='PAGADO | '.ceros($programado,2).' FECHAS';
				$verificaPago =verificaPagosProgramados($conceptoPago,$codigoSocio,$montoPago);
			}

			if($verificaPago=="PEN"){ $caja='---'; }
			if($verificaPago=="OK"){ $caja='CAJA'; }
			if($verificaPago=="ERROR"){ $caja='ERROR'; }
		
			$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,4).'</td>
					<td class="textoCen">'.$codigoSocio.'</td>
					<td class="textoIzq">'.texto($apPaterno).'</td>
					<td class="textoIzq">'.texto($apMaterno).'</td>
					<td class="textoIzq">'.texto($nombre).'</td>
					<td class="textoCen">'.$dni.'</td>
					<td class="textoCen">'.ceros($lotes,2).' LOTES</td>
					<td class="textoDer resaltar">S/. '.moneda($montoCuota).'</td>
					<td class="textoDer resaltar">S/. '.moneda($montoPago).'</td>
					<td class="textoCen">'.$estado.'</td>
					<td class="textoCen">'.$caja.'</td>
				</tr>
			';

			$i++;
		}

		$reporte.='
				<tr>
					'.$totales.'
				</tr>
			</tbody>
			</table>
		';
	}

	if($opcion=="reporte_socios_deben"){
		$nombreArchivo="reporte-socios-cuota-".$conceptoPago;
		$titulo="REPORTE DE CUOTAS";
		$subtitulo="CUOTAS PAGADAS";
		$codigo="CODIGO DE CUOTA - ".$conceptoPago;
		
		$reporte.='
			<h1>'.$titulo.'</h1>
			<h2>'.$subtitulo.'</h2>
			<h3>'.$codigo.'</h3>
			'.$estadisticas.'
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">CODIGO SOCIO</th>
					<th class="textoIzq">APELLIDO PATERNO</th>
					<th class="textoIzq">APELLIDO MATERNO</th>
					<th class="textoIzq">NOMBRE</th>
					<th class="textoCen">DNI</th>
					<th class="textoCen">LOTES</th>
					<th class="textoCen">CUOTA</th>
					<th class="textoCen">MONTO</th>
					<th class="textoCen">ESTADO</th>
					<th class="textoCen">CAJA</th>
				</tr>
			</thead>
			<tbody>
		';

		$sql="SELECT sm_mod_cuotas_socios.codigoCuota AS codigoCuota, sm_mod_cuotas_socios.codigoSocio AS codigoSocio, sm_mod_cuotas_socios.lotes AS lotes, sm_mod_cuotas_socios.montoCuota AS montoCuota, sm_mod_cuotas_socios.montoPago AS montoPago, sm_mod_cuotas_socios.estadoPago AS estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_cuotas_socios, sm_socios WHERE codigoCuota='$conceptoPago' AND (estadoPago='NP' OR estadoPago='MP') AND sm_mod_cuotas_socios.codigoSocio=sm_socios.codigoSocio ORDER BY sm_socios.apPaterno ASC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$codigoSocio =$n[codigoSocio];
			$lotes       =$n[lotes];
			$montoCuota  =$n[montoCuota];
			$montoPago   =$n[montoPago];
			$estadoPago  =$n[estadoPago];
			$dni         =$n[dni];
			$nombre      =$n[nombre];
			$apPaterno   =$n[apPaterno];
			$apMaterno   =$n[apMaterno];
			$programado  =conceptoProgramado($codigoSocio,$conceptoPago);

			if($estadoPago=="NP" and $programado==0){
				$estado       ='PENDIENTE';
				$verificaPago ="PEN";
			}

			if($estadoPago=="MP" and $programado>=2){
				$estado='PENDIENTE | '.ceros($programado,2).' FECHAS';
				$verificaPago ="PEN";
			}

			if($estadoPago=="SP" and $programado==0){
				$estado       ='PAGADO';
				$verificaPago =verificaPago($conceptoPago,$codigoSocio,$montoPago);
			}

			if($estadoPago=="SP" and $programado>=2){
				$estado       ='PAGADO | '.ceros($programado,2).' FECHAS';
				$verificaPago =verificaPagosProgramados($conceptoPago,$codigoSocio,$montoPago);
			}

			if($verificaPago=="PEN"){ $caja='---'; }
			if($verificaPago=="OK"){ $caja='CAJA'; }
			if($verificaPago=="ERROR"){ $caja='ERROR'; }
		
			$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,4).'</td>
					<td class="textoCen">'.$codigoSocio.'</td>
					<td class="textoIzq">'.texto($apPaterno).'</td>
					<td class="textoIzq">'.texto($apMaterno).'</td>
					<td class="textoIzq">'.texto($nombre).'</td>
					<td class="textoCen">'.$dni.'</td>
					<td class="textoCen">'.ceros($lotes,2).' LOTES</td>
					<td class="textoDer resaltar">S/. '.moneda($montoCuota).'</td>
					<td class="textoDer resaltar">S/. '.moneda($montoPago).'</td>
					<td class="textoCen">'.$estado.'</td>
					<td class="textoCen">'.$caja.'</td>
				</tr>
			';

			$i++;
		}

		$reporte.='
				<tr>
					'.$totales.'
				</tr>
			</tbody>
			</table>
		';
	}

	$reporte.='
			'.$impresoPor.'
		<body>
		</html>
	';
	
	$proceso     ="IMPRESION DE - <strong>".$titulo."</strong> &nbsp;|&nbsp; <strong>".$subtitulo."</strong> &nbsp;|&nbsp; <strong>".$codigo."</strong>";
	$codigoSocio ="";
	$sql="INSERT INTO sm_procesos_cuotas(codigoCuota, codigoSocio, proceso, fecha, hora, usuario) VALUES('$conceptoPago', '$codigoSocio', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$bitacora=mysqli_query($conexion,$sql);

	cerrarDB();
	
	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
	$dompdf->stream($nombreArchivo.".pdf");
?>