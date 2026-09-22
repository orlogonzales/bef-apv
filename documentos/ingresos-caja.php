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
	
	$conexion         =conexionDB();
	$fechaInicio      =$_GET[fechaInicio];
	$fechaFin         =$_GET[fechaFin];
	$usuarioConsulta  =$_GET[usuarioConsulta];
	$tipoActividad    =$_GET[tipoActividad];
	$consultaFechaIni =fechaSQL($fechaInicio);
	$consultaFechaFin =fechaSQL($fechaFin);
	$hoy              =fechaSQL(infoTiempo('fechaHoy'));
	$hora             =infoTiempo('hora');
	$simbolo          ="S/. ";
	$dniUsuario       =$_SESSION['dni_apv'];
	$impresoPor       ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$codigoOperacion  ="";
	$subtitulo        ="RANGO DE FECHAS DEL REPORTE DE <u>[".$fechaInicio."]</u> AL <u>[".$fechaFin."]</u>";
	
	$totalING =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','','',$usuarioConsulta,'totalActividades');
	$totalASA =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','ASA','',$usuarioConsulta,'totalActividades');
	$totalFAE =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','FAE','',$usuarioConsulta,'totalActividades');
	$totalCUO =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','CUO','',$usuarioConsulta,'totalActividades');
	$totalCLS =infoPartida('','totalPartidasCierre');

	if($totalING>0){ $infoTotalING="S/. ".moneda($totalING); }else{ $infoTotalING=""; }
	if($totalASA>0){ $infoTotalASA="S/. ".moneda($totalASA); }else{ $infoTotalASA=""; }
	if($totalFAE>0){ $infoTotalFAE="S/. ".moneda($totalFAE); }else{ $infoTotalFAE=""; }
	if($totalCUO>0){ $infoTotalCUO="S/. ".moneda($totalCUO); }else{ $infoTotalCUO=""; }
	if($totalCLS>0){ $infoTotalPAR="S/. ".moneda($totalCLS); }else{ $infoTotalPAR=""; }

	if($tipoActividad=="ALL"){
		$titulo  ="REPORTE DE INGRESOS A CAJA POR CONCEPTOS PROPIOS";
		$proceso ="IMPRESION DE <strong>".$titulo."</strong>";
		$actividad='';
		$totales='<td colspan="9" class="totales textoCen"><strong>TOTAL INGRESOS:</strong>&nbsp;'.$infoTotalING.'</td>';
	}

	if($tipoActividad=="CUO"){
		$titulo  ="REPORTE DE INGRESOS A CAJA POR CONCEPTO DE CUOTAS";
		$proceso ="IMPRESION DE <strong>".$titulo."</strong>";
		$actividad="AND tipoActividad='$tipoActividad'";
		$totales='<td colspan="9" class="totales textoCen"><strong>TOTAL POR CUOTAS:</strong>&nbsp;'.$infoTotalCUO.'</td>';
	}

	if($tipoActividad=="ASA"){
		$titulo  ="REPORTE DE INGRESOS A CAJA POR CONCEPTO DE ASAMBLEAS";
		$proceso ="IMPRESION DE <strong>".$titulo."</strong>";
		$actividad="AND tipoActividad='$tipoActividad'";
		$totales='<td colspan="9" class="totales textoCen"><strong>TOTAL POR ASAMBLEAS:</strong>&nbsp;'.$infoTotalASA.'</td>';
	}

	if($tipoActividad=="FAE"){
		$titulo  ="REPORTE DE INGRESOS A CAJA POR CONCEPTO DE FAENAS";
		$proceso ="IMPRESION DE <strong>".$titulo."</strong>";
		$actividad="AND tipoActividad='$tipoActividad'";
		$totales='<td colspan="9" class="totales textoCen"><strong>TOTAL POR FAENAS:</strong>&nbsp;'.$infoTotalFAE.'</td>';
	}

	if($tipoActividad=="PAR"){
		$titulo  ="REPORTE DE INGRESOS A CAJA POR CONCEPTO DE PARTIDAS";
		$proceso ="IMPRESION DE <strong>".$titulo."</strong>";
		$actividad="AND concepto='$tipoActividad'";
		$totales='<td colspan="9" class="totales textoCen"><strong>TOTAL POR FAENAS:</strong>&nbsp;'.$infoTotalPAR.'</td>';
	}

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
					<th width="15%" class="textoIzq">NOMBRE DE SOCIO</th>
					<th class="textoCen">LT</th>
					<th class="textoCen">CTO</th>
					<th width="45%" class="textoIzq">DETALLE</th>
					<th class="textoCen">TOT</th>
					<th class="textoCen">DOC</th>
					<th class="textoCen">USER</th>
				</tr>
			</thead>
			<tbody>
	';

	$sql="SELECT fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE movimiento='ING' AND concepto!='PAR' $actividad AND (fechaOperacion BETWEEN '$consultaFechaIni' AND '$consultaFechaFin') $consultaUsuario ORDER BY id DESC";
	$rs=mysqli_query($conexion,$sql);
	$i=1;
	while($n=mysqli_fetch_array($rs)){
		$fechaOperacion  =$n[fechaOperacion];
		$tipoActividad   =$n[tipoActividad];
		$concepto        =$n[concepto];
		$codigoSocio     =$n[codigoSocio];
		$codigoConcepto  =$n[codigoConcepto];
		$tipoDocumento   =$n[tipoDocumento];
		$nroDocumento    =$n[nroDocumento];
		$monto           =$n[monto];
		$detalleConcepto =$n[detalleConcepto];
		$codigoOperacion =$n[codigoOperacion];
		$fecha           =$n[fecha];
		$hora            =$n[hora];
		$usuario         =$n[usuario];
		$infofecha       =infoFecha($fecha,'normal');
		$nombreSocio     =texto(infoSocios($codigoSocio,'nombreCorto'));
		$infoLotes       =ceros(infoSocios($codigoSocio,'cantidadLotes'),2);
		$conceptopago    =infoPago('','',$concepto,'conceptoPago');
		$infoUsuario     =datoUsuario($usuario,'nombre');
		$infoDocumento   =$tipoDocumento.'.'.$nroDocumento;

		if($tipoActividad=="ASA"){ $infoTipoActividad="ASAMBLEA"; }
		if($tipoActividad=="FAE"){ $infoTipoActividad="FAENA"; }
		if($tipoActividad=="CUO"){ $infoTipoActividad="CUOTA"; }

		if($concepto=="PAR"){
			$nombreSocio=datoUsuario($codigoSocio,'nombreCorto');
			$infoLotes="--";
		}

	$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,2).'</td>
					<td class="textoCen textoMayuscula">'.$infofecha.'</td>
					<td class="textoIzq">'.$nombreSocio.'</td>
					<td class="textoCen">'.$infoLotes.'</td>
					<td class="textoCen">'.$infoTipoActividad.'</td>
					<td class="textoIzq">'.texto($detalleConcepto).'</td>
					<td class="textoDer textoNegrita resaltar">'.$simbolo.moneda($monto).'</td>
					<td class="textoIzq">'.$infoDocumento.'</td>
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
	$dompdf->stream("reporte-ingresos-caja.pdf");
?>