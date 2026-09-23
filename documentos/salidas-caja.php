<?php
	session_start();
	set_time_limit(300);
	ini_set("memory_limit","512M");
    
	$ruta="../";
	require_once($ruta.'php/funciones.php');
	require_once $ruta.'dompdf/lib/html5lib/Parser.php';
	require_once $ruta.'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
	require_once $ruta.'dompdf/lib/php-svg-lib/src/autoload.php';
	require_once $ruta.'dompdf/src/Autoloader.php';
	Dompdf\Autoloader::register();
	use Dompdf\Dompdf;
	
	$conexion        =conexionDB();
	$fechaInicio     =$_GET['fechaInicio'];
	$fechaFin        =$_GET['fechaFin'];
	$usuarioConsulta =$_GET['usuarioConsulta'];
	$tipoActividad   =$_GET['tipoActividad'];
	$consultaFechaIni =fechaSQL($fechaInicio);
	$consultaFechaFin =fechaSQL($fechaFin);

	$hoy             =fechaSQL(infoTiempo('fechaHoy'));
	$hora            =infoTiempo('hora');
	$totalSAL        =infoCaja($consultaFechaIni,$consultaFechaFin,'','','',$usuarioConsulta,'totalSAL');
	$simbolo         ="S/. ";
	$dniUsuario      =$_SESSION['dni_apv'];
	$impresoPor      ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$codigoOperacion ="";
	$subtitulo       ="RANGO DE FECHAS DEL REPORTE DE <u>[".infoFecha($fechaInicio,'info')."]</u> AL <u>[".infoFecha($fechaFin,'info')."]</u>";
	$titulo          ="REPORTE DE SALIDAS DE CAJA POR CONCEPTO VARIOS";
	$proceso         ="IMPRESION DE <strong>".$titulo."</strong>";
	$actividad       ="AND concepto='$tipoActividad'";
	$totales         ='<td colspan="7" class="totales textoCen"><strong>TOTAL SALIDAS:</strong>&nbsp;S/. '.moneda($totalSAL).'</td>';

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
					<th width="30%" class="textoIzq">DETALLE</th>
					<th width="20%" class="textoIzq">RESPONSABLE</th>
					<th class="textoCen">MONTO</th>
					<th width="10%" class="textoCen">DOCUMENTO</th>
					<th class="textoCen">USUARIO</th>
				</tr>
			</thead>
			<tbody>
	';

	$sql="SELECT fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE movimiento='SAL' AND concepto!='PAR' AND (fechaOperacion BETWEEN '$consultaFechaIni' AND '$consultaFechaFin') $consultaUsuario ORDER BY id DESC";
	$rs=mysqli_query($conexion,$sql);
	$i=1;
	while($n=mysqli_fetch_array($rs)){
		$fechaOperacion  =$n['fechaOperacion'];
		$tipoActividad   =$n['tipoActividad'];
		$concepto        =$n['concepto'];
		$codigoSocio     =$n['codigoSocio'];
		$codigoConcepto  =$n['codigoConcepto'];
		$tipoDocumento   =$n['tipoDocumento'];
		$nroDocumento    =$n['nroDocumento'];
		$monto           ='<span class="text-danger textoNegrita">S/. '.moneda($n['monto']).'</span>';
		$detalleConcepto =$n['detalleConcepto'];
		$codigoOperacion =$n['codigoOperacion'];
		$fecha           =$n['fecha'];
		$hora            =$n['hora'];
		$usuario         =$n['usuario'];
		$infofecha       =infoFecha($fecha,'normal');
		$nombreSocio     =texto(infoSocios($codigoSocio,'nombreCorto'));
		$documento       ='<strong>'.infoTipoDOC($tipoDocumento).'</strong> N°'.$nroDocumento;
		$infoUsuario     ='<span class="label label-default">'.datoUsuario($usuario,'nombre').'</span>';
		$infoConcepto    =$detalleConcepto;

		if($nombreSocio==""){ $nombreSocio=texto(datoUsuario($codigoSocio,'nombreCorto')); }else{ $nombreSocio=$nombreSocio; }

	$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,2).'</td>
					<td class="textoCen textoMayuscula">'.$infofecha.'</td>
					<td class="textoIzq">'.texto($detalleConcepto).'</td>
					<td class="textoIzq">'.$nombreSocio.'</td>
					<td class="textoDer textoNegrita resaltar">'.$monto.'</span></td>
					<td class="textoIzq resaltar">'.$documento.'</span></td>
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
	$dompdf->stream("reporte-salidas-caja.pdf");
?>