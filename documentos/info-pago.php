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
	
	$conexion        =conexionDB();
	$codigoSocio     =$_GET['codigoSocio'];
	$codigoOperacion =$_GET['codigoOperacion'];
	$opcion          =$_GET['opcion'];
	$nombreSocio     =infoSocios($codigoSocio,'nombre');
	$lotesSocio      =infoSocios($codigoSocio,'cantidadLotes');
	$hoy             =fechaSQL(infoTiempo('fechaHoy'));
	$hora            =infoTiempo('hora');
	$dniUsuario      =$_SESSION['dni_apv'];
	$rutaCB          ='../assets/images/codigo-barra/';
	$fileCB          =$codigoSocio.'.png';
	$codBar          =$rutaCB.$fileCB;
	$impresoPor      ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';

	$sql="SELECT tipoActividad, fechaOperacion, codigoConcepto, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, fecha, hora, usuario FROM sm_mod_caja WHERE codigoOperacion='$codigoOperacion'";
	$infopago=mysqli_query($conexion,$sql);
	$dato=mysqli_fetch_array($infopago);
	$tipoActividad   =$dato['tipoActividad'];
	$fechaOperacion  =$dato['fechaOperacion'];
	$concepto        =$dato['concepto'];
	$codigoConcepto  =$dato['codigoConcepto'];
	$tipoDocumento   =$dato['tipoDocumento'];
	$nroDocumento    =$dato['nroDocumento'];
	$monto           =$dato['monto'];
	$detalleConcepto =$dato['detalleConcepto'];
	$fecha           =$dato['fecha'];
	$hora            =$dato['hora'];
	$usuario         =$dato['usuario'];

	if($tipoActividad=="CUO"){
		$infoDetallepago=texto(infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota'));
		$titulo="INFORMACION DE PAGO - CUOTA";
	}
	if($tipoActividad=="ASA"){
		$titulo="INFORMACION DE PAGO - ASAMBLEA";	
	}
	if($tipoActividad=="FAE"){
		$titulo="INFORMACION DE PAGO - FAENA";
	}

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

	$reporte.='
		<h1>'.$titulo.'</h1>
		<div class="codigobarra"><img src="'.$rutaCB.$fileCB.'"></div>
		<div class="infoCodigobarra">CODIGO SOCIO. '.$codigoSocio.'</div>
		'.$detalles
	;

	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="resaltar"><strong>CODIGO SOCIO:</strong><br>'.$codigoSocio.'</td>
				<td class="resaltar"><strong>NOMBRE SOCIO:</strong><br>'.texto($nombreSocio).'</td>
				<td class="resaltar"><strong>LOTES:</strong><br>'.ceros($lotesSocio,2).' LOTES</td>
			</tr>
		</table>
		<div class="s-30"></div>
	';

	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="resaltar" colspan="2">'.$infoDetallepago.'</td>
			</tr>
			<tr>
				<td class="resaltar"><strong>CONCEPTO:</strong>'.texto(infoPago('','',$concepto,'conceptoPago')).'</td>
				<td class="resaltar"><strong>MONTO DE PAGO:</strong> S/. '.moneda($monto).'</td>
			</tr>
			<tr>
				<td class="resaltar"><strong>DOCUMENTO DE PAGO:</strong>'.infoTipoDOC($tipoDocumento).'</td>
				<td class="resaltar"><strong>NRO DE DOCUMENTO:</strong>'.$nroDocumento.'</td>
			</tr>
			<tr>
				<td class="resaltar"><strong>FECHA DE OPERACION:</strong>'.strtoupper(infoFecha($fechaOperacion,'larga')).'</td>
				<td class="resaltar"><strong>COD DE OPERACION:</strong>'.$codigoOperacion.'</td>
			</tr>
		</table>
	';
	
	$reporte.=$impresoPor.'
		<body>
		</html>
	';

	$nombreArchivo="info-pago-".$codigoOperacion;
	$proceso ="IMPRESION DE <strong>INFORMACION DE PAGO REGISTRADPO EN CAJA</strong>";

	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);
	
	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'portait');
	$dompdf->render();
	$dompdf->stream($nombreArchivo.".pdf");
?>