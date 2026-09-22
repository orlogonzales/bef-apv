<?php
	session_start();
	set_time_limit(300);
	ini_set("memory_limit","512M");
	ini_set("max_execution_time","3000");
    
	$ruta="../";
	require_once($ruta.'php/funciones.php');
	require_once $ruta.'dompdf/lib/html5lib/Parser.php';
	require_once $ruta.'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
	require_once $ruta.'dompdf/lib/php-svg-lib/src/autoload.php';
	require_once $ruta.'dompdf/src/Autoloader.php';
	Dompdf\Autoloader::register();
	use Dompdf\Dompdf;
	
	$conexion       =conexionDB();
	$conceptoPago   =$_GET[conceptoPago];
	$codigoConcepto =$_GET[codigoConcepto];
	$codigoSocio    =$_GET[codigoSocio];
	$nombreSocio    =infoSocios($codigoSocio,'nombre');
	$lotesSocio     =infoSocios($codigoSocio,'cantidadLotes');
	$totalpagado    =infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasPagadas');
	$totalSaldo     =infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasSaldo');
	$hoy            =fechaSQL(infoTiempo('fechaHoy'));
	$hora           =infoTiempo('hora');
	$dniUsuario     =$_SESSION['dni_apv'];
	$programadoPor  =infoPagoFechas($codigoSocio,$codigoConcepto,'programadoPor');
	$impresoPor     ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$totales        ='<td colspan="7" class="totales textoCen"><strong>TOTAL PAGADO:</strong>&nbsp;S/. '.moneda($totalpagado).'&nbsp;&nbsp;|&nbsp;&nbsp;<strong>TOTAL SALDO:</strong>&nbsp;S/. '.moneda($totalSaldo).'</td>';
	$infoCodigo     ='<small>CODIGO: '.$codigoConcepto.'</small>';

	if($conceptoPago=="CUO"){
		$totalPago       =infoPago($codigoSocio,$codigoConcepto,'','montoCuotaSocio');
		$detalleConcepto =infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota');
		$titulo          ="CRONOGRAMA DE PAGOS DE CUOTA";
		$costoUnitario   =infoCuota($idJuntaDirectiva,$codigoConcepto,'montoCuota');
		$totalMontoPago  =infoPago($codigoSocio,$codigoConcepto,'','montoCuotaSocio');
	}

	if(($conceptoPago=="ASA") or ($conceptoPago=="FAE")){
		$totalPago     =infoPago($codigoSocio,$codigoConcepto,'','montoMultaSocio');
		$asistio       =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'asistenciaSocio');
		$tardanza      =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'asistenciaTardeSocio');
		$montoUnitario ="xxxxxx";

		if($asistio=="SI" and $tardanza<=0.15){ $multaPor=""; }
		if($asistio=="SI" and $tardanza>0.15){ $multaPor="TARDANZA A "; }
		if($asistio=="NO"){ $multaPor="INASISTENCIA A "; }
		if($conceptoPago=="ASA"){ $actividad="ASAMBLEA "; }
		if($conceptoPago=="FAE"){ $actividad="FAENA "; }
		
		$detalleConcepto =$multaPor.$actividad.infoActividad($idJuntaDirectiva,$codigoConcepto,'','temaActividad');
		$titulo="CRONOGRAMA DE PAGOS";
	}

	if($usuarioConsulta=="ALL"){ $consultaUsuario=""; }
	if($usuarioConsulta!="ALL"){ $consultaUsuario=" AND usuario='$usuarioConsulta'"; }

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
			<h1>'.$titulo.'</h1>
			<h2>'.$detalleConcepto.'</h2>
			<h3>'.$infoCodigo.'</h3>
			
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infoTexto">
				<tr>
					<td class="resaltar"><strong>CODIGO SOCIO:</strong><br>'.$codigoSocio.'</td>
					<td class="resaltar"><strong>NOMBRE SOCIO:</strong><br>'.texto($nombreSocio).'</td>
					<td class="resaltar"><strong>LOTES:</strong><br>'.ceros($lotesSocio,2).' LOTES</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>COSTO UNITARIO:</strong><br>S/. '.moneda($costoUnitario).'</td>
					<td class="resaltar"><strong>TOTAL A PAGAR:</strong><br>S/. '.moneda($totalMontoPago).'</td>
					<td class="resaltar"><strong>FECHAS PROGRAMADAS POR:</strong><br>'.datoUsuario($programadoPor,'nombrePaterno').'</td>
				</tr>
			</table>
			<br><hr><br>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th width="15%" class="textoCen">COMPROMISO</th>
					<th class="textoCen">MONTO</th>
					<th class="textoCen">ESTADO</th>
					<th class="textoCen" colspan="3">FECHA PAGO</th>
				</tr>
			</thead>
			<tbody>
	';

	$sql="SELECT cuota, fechaProgramada, montoPago, tipoDocumento, nroDocumento, fechaPago, estadoPago, codigoOperacion, fecha, hora, usuario FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto'";
	$rs=mysqli_query($conexion,$sql);
	while($n=mysqli_fetch_array($rs)){
		$cuota           =$n[cuota];
		$fechaProgramada =$n[fechaProgramada];
		$montoPago       =$n[montoPago];
		$tipoDocumento   =$n[tipoDocumento];
		$nroDocumento    =$n[nroDocumento];
		$fechaPago       =$n[fechaPago];
		$estadoPago      =$n[estadoPago];
		$codigoOperacion =$n[codigoOperacion];
		$fecha           =$n[fecha];
		$hora            =$n[hora];
		$usuario         =$n[usuario];

		if($estadoPago=="PEN"){
			$infoEstadoPago="PENDIENTE";
			$infoDocumento="";
			$infoFechaPago="";
			$cobroCajero="";
		}
		if($estadoPago=="PGD"){
			$infoEstadoPago="PAGADO";
			$infoDocumento="VOU N° ".$nroDocumento;
			$infoFechaPago=infoFecha($fechaPago,'corta');
			$cobroCajero=datoUsuario(cobroCajero($nroDocumento),'nombre');
		}

	$reporte.='
				<tr>
					<td class="textoCen">'.ceros($cuota,2).'</td>
					<td class="textoIzq textoMayuscula">'.infoFecha($fechaProgramada,'corta').'</td>
					<td class="textoDer textoNegrita resaltar"> S/. '.moneda($montoPago).'</td>
					<td class="textoCen">'.$infoEstadoPago.'</td>
					<td width="15%" class="textoIzq textoMayuscula">'.$infoFechaPago.'</span></td>
					<td class="textoIzq">'.$infoDocumento.'</span></td>
					<td class="textoCen">'.$cobroCajero.'</td>
				</tr>
	';
}

	$reporte.='
				<tr>
					'.$totales.'
				</tr>
			</tbody>
			</table>
			'.$impresoPor.'
		<body>
		</html>
	';

	$proceso ="IMPRESION DE <strong>".$titulo.' '.$detalleConcepto."</strong>";
	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);
	
	cerrarDB();
	
	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'portait');
	$dompdf->render();
	$dompdf->stream("cronograma-pagos-".$codigoSocio.".pdf");
?>