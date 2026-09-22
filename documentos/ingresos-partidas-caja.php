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
	$fechaInicio     =$_GET[fechaInicio];
	$fechaFin        =$_GET[fechaFin];
	$usuarioConsulta =$_GET[usuarioConsulta];
	$tipoActividad   =$_GET[tipoActividad];
	$hoy             =fechaSQL(infoTiempo('fechaHoy'));
	$hora            =infoTiempo('hora');
	$totalCLS        =infoPartida('','totalPartidasCierre');
	$simbolo         ="S/. ";
	$dniUsuario      =$_SESSION['dni_apv'];
	$impresoPor      ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$codigoOperacion ="";
	$subtitulo       ="RANGO DE FECHAS DEL REPORTE DE <u>[".infoFecha($fechaInicio,'info')."]</u> AL <u>[".infoFecha($fechaFin,'info')."]</u>";
	$titulo          ="REPORTE DE INGRESOS A CAJA POR CONCEPTO DE PARTIDAS";
	$proceso         ="IMPRESION DE <strong>".$titulo."</strong>";
	$actividad       ="AND concepto='$tipoActividad'";
	$totales         ='<td colspan="9" class="totales textoCen"><strong>TOTAL POR PARTIDAS:</strong>&nbsp;S/. '.moneda($totalCLS).'</td>';

	if($usuarioConsulta=="ALL"){ $consultaUsuario=""; }
	if($usuarioConsulta!="ALL"){ $consultaUsuario=" AND usuario='$usuarioConsulta'"; }

	$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

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
			<h2>'.$subtitulo.'</h2>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">FECHA</th>
					<th width="20%" class="textoIzq">PARTIDA</th>
					<th width="30%" class="textoIzq">DETALLE</th>
					<th width="15%" class="textoIzq">RESPONSABLE</th>
					<th class="textoCen">ASIGNADO</th>
					<th class="textoCen">GASTADO</th>
					<th class="textoCen">DEVUELTO</th>
					<th class="textoCen">USUARIO</th>
				</tr>
			</thead>
			<tbody>
	';

	$sql="SELECT fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE movimiento='ING' $actividad AND (fechaOperacion BETWEEN '$fechaInicio' AND '$fechaFin') $consultaUsuario ORDER BY id DESC";
	$rs=mysqli_query($conexion,$sql);
	$i=1;
	while($n=mysqli_fetch_array($rs)){
		$fechaOperacion    =$n[fechaOperacion];
		$tipoActividad     =$n[tipoActividad];
		$concepto          =$n[concepto];
		$codigoSocio       =$n[codigoSocio];
		$codigoConcepto    =$n[codigoConcepto];
		$tipoDocumento     =$n[tipoDocumento];
		$nroDocumento      =$n[nroDocumento];
		$monto             =$n[monto];
		$detalleConcepto   =$n[detalleConcepto];
		$codigoOperacion   =$n[codigoOperacion];
		$fecha             =$n[fecha];
		$hora              =$n[hora];
		$usuario           =$n[usuario];
		$infofecha         =infoFecha($fecha,'normal');
		$nombreSocio       =utf8_encode(infoSocios($codigoSocio,'nombre'));
		$infoLotes         =ceros(infoSocios($codigoSocio,'cantidadLotes'),2);
		$conceptopago      =infoPago('','',$concepto,'conceptoPago');
		$infoUsuario       =datoUsuario($usuario,'nombre');
		
		$infofecha         =infoFecha($fecha,'normal');
		$nombreResponsable =texto(datoUsuario($codigoSocio,'nombrePaterno'));
		$infoMonto         ='<span class="text-danger textoNegrita">S/. '.moneda($monto).'</span>';
		$montoPartida      ='<span class="text-danger textoNegrita">S/. '.infoPartida($codigoConcepto,'montoPartida').'</span>';
		$montoGastado      ='<span class="text-danger textoNegrita">S/. '.infoPartida($codigoConcepto,'montoGastado').'</span>';
		$conceptoPartida   ='<span class="label bg-brown">'.texto(infoPartida($codigoConcepto,'conceptoPartida')).'</span>';
		$infoUsuario       ='<span class="label label-default">'.datoUsuario($usuario,'nombre').'</span>';

	$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,2).'</td>
					<td class="textoCen textoMayuscula">'.$infofecha.'</td>
					<td class="textoIzq">'.$conceptoPartida.'</td>
					<td class="textoIzq">'.texto($detalleConcepto).'</td>
					<td class="textoIzq">'.$nombreResponsable.'</td>
					<td class="textoDer textoNegrita resaltar">'.$montoPartida.'</span></td>
					<td class="textoDer textoNegrita resaltar">'.$montoGastado.'</span></td>
					<td class="textoDer textoNegrita resaltar">'.$infoMonto.'</span></td>
					<td class="textoCen">'.$infoUsuario.'</td>
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
			'.$impresoPor.'
		<body>
		</html>
	';
	
	cerrarDB();
	
	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
	$dompdf->stream("reporte-ingreso-caja-partida.pdf");
?>