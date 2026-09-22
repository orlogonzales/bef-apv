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
	$codigoSocio     =$_GET[codigoSocio];
	$actividad       =$_GET[actividad];
	$codigoActividad =$_GET[codigoActividad];
	$operacion       =$_GET[operacion];
	$nombreSocio     =infoSocios($codigoSocio,'nombre');
	$lotesSocio      =infoSocios($codigoSocio,'cantidadLotes');
	$hoy             =fechaSQL(infoTiempo('fechaHoy'));
	$hora            =infoTiempo('hora');
	$dniUsuario      =$_SESSION['dni_apv'];
	$rutaCB          ='../assets/images/codigo-barra/';
	$fileCB          =$codigoSocio.'.png';
	$codBar          =$rutaCB.$fileCB;
	$impresoPor      ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';

	if($operacion=='CONSTANCIA_EXONERACION_DEUDA'){
		$sql="SELECT tipoActividad, fechaOperacion, codigoConcepto, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND tipoActividad='$actividad' AND codigoConcepto='$codigoActividad'";
		$infopago=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($infopago);

		$tipoActividad   =$dato[tipoActividad];
		$fechaOperacion  =$dato[fechaOperacion];
		$concepto        =$dato[concepto];
		$codigoConcepto  =$dato[codigoConcepto];
		$tipoDocumento   =$dato[tipoDocumento];
		$nroDocumento    =$dato[nroDocumento];
		$monto           =$dato[monto];
		$detalleConcepto =$dato[detalleConcepto];
		$codigoOperacion =$dato[codigoOperacion];

		$sql="SELECT deuda, porcentaje, exonerado, totalPago, observacion, codigoExoneracion, fecha, hora, usuario FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND tipoActividad='$actividad' AND codigoActividad='$codigoActividad'";
		$infopago=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($infopago);

		$deuda             =$dato[deuda];
		$porcentaje        =$dato[porcentaje];
		$exonerado         =$dato[exonerado];
		$totalPago         =$dato[totalPago];
		$observacion       =$dato[observacion];
		$codigoExoneracion =$dato[codigoExoneracion];
		$fecha             =$dato[fecha];
		$hora              =$dato[hora];
		$usuario           =$dato[usuario];

		if($porcentaje<100){
			$conceptoEXO='EXONERACION PARCIAL DE DEUDA';
			$tipoDocumento=infoTipoDOC($tipoDocumento);
			$nroDocumento=$nroDocumento;
			$montoExonerado='- S/. '.moneda($exonerado);
			$totalPagar='S/. '.moneda($totalPago);
		}else{
			$conceptoEXO='EXONERACION TOTAL DE DEUDA';
			$tipoDocumento='S/D';
			$nroDocumento='--';

			$montoExonerado='- S/. '.moneda($exonerado);
			$totalPagar='S/. 0.00';
		}

		if($actividad=="CUO"){
			$infoDetallepago=texto(infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota'));
			$tituloPagina="EXONERACION - CUOTA";                                    
			$tituloTablaEXO="INFORMACION DE EXONERACION";
			$tituloTablaPGO="INFORMACION DE PAGO";
		}
		if($actividad=="ASA"){
			$infoDetallepago   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
			$tituloPagina="EXONERACION - ASAMBLEA";	                    
			$tituloTablaEXO="INFORMACION DE EXONERACION";
			$tituloTablaPGO="INFORMACION DE PAGO";
		}
		if($actividad=="FAE"){
			$infoDetallepago   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
			$tituloPagina="EXONERACION - FAENA";                                    
			$tituloTablaEXO="INFORMACION DE EXONERACION";
			$tituloTablaPGO="INFORMACION DE PAGO";
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
			<h1>'.$tituloPagina.'</h1>
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
			<h1>'.$tituloTablaEXO.'</h1>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="resaltar" colspan="2">'.$infoDetallepago.'</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>DEUDA:</strong> S/. '.moneda($deuda).'</td>
					<td class="resaltar"><strong>PORCENTAJE DE EXONERACION:</strong> '.$porcentaje.'%</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>MONTO EXONERADO:</strong> '.$montoExonerado.'</td>
					<td class="resaltar"><strong>TOTAL A PAGAR:</strong> '.$totalPagar.'</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>OPERACION:</strong> '. strtoupper(datoUsuario($usuario,'nombreCorto')).' EL DIA '.strtoupper(infoFecha($fecha,'muycorta')).' A LAS  '.horacorta($hora).'</td>
					<td class="resaltar"><strong>COD DE OPERACION:</strong> '.$codigoExoneracion.'</td>
				</tr>
				<tr>
					<td class="resaltar" colspan="2">'.$observacion.'</td>
				</tr>
			</table>
			<div class="s-30"></div>
		';

		if($porcentaje<100){
			$reporte.='
				<h1>'.$tituloTablaPGO.'</h1>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td class="resaltar"><strong>CONCEPTO:</strong> '.$conceptoEXO.'</td>
						<td class="resaltar"><strong>MONTO DE PAGO:</strong> S/. '.moneda($monto).'</td>
					</tr>
					<tr>
						<td class="resaltar"><strong>DOCUMENTO DE PAGO:</strong> '.$tipoDocumento.'</td>
						<td class="resaltar"><strong>NRO DE DOCUMENTO:</strong> '.$nroDocumento.'</td>
					</tr>
					<tr>
						<td class="resaltar"><strong>FECHA DE OPERACION:</strong> '.strtoupper(infoFecha($fechaOperacion,'larga')).'</td>
						<td class="resaltar"><strong>COD DE OPERACION:</strong> '.$codigoOperacion.'</td>
					</tr>
				</table>
			';
		}
		
		$reporte.=$impresoPor.'
			<body>
			</html>
		';

		if($porcentaje<100){
			$proceso ="IMPRESION DE <strong>INFORMACION EXONERACION DE DEUDA Y REGISTRO EN CAJA</strong>";
		}else{
			$proceso ="IMPRESION DE <strong>INFORMACION EXONERACION DE DEUDA</strong>, OPERACION NO REGISTRADA EN CAJA, POR TRATARSE DE UNA EXONERACION TOTAL";
		}
		
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$hoy', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		$nombreArchivo="info-exoneracion-".$codigoSocio;
	}

	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'portait');
	$dompdf->render();
	$dompdf->stream($nombreArchivo.".pdf");
?>