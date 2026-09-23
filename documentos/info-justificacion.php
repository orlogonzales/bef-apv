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

	$documentos      ='../assets/images/docs/';
	$conexion        =conexionDB();
	$tipoActividad   =$_GET['tipoActividad'];
	$codigoActividad =$_GET['codigoActividad'];
	$codigoSocio     =$_GET['codigoSocio'];
	$nombreSocio     =infoSocios($codigoSocio,'nombre');
	$lotesSocio      =infoSocios($codigoSocio,'cantidadLotes');
	$hoy             =fechaSQL(infoTiempo('fechaHoy'));
	$hora            =infoTiempo('hora');
	$dniUsuario      =$_SESSION['dni_apv'];
	$impresoPor      ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$temaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');

	if($tipoActividad=="ASA"){ $titulo="JUSTIFICACION DE INASISTENCIA A ASAMBLEA"; $actividad="ASAMBLEA"; }
	if($tipoActividad=="FAE"){ $titulo="JUSTIFICACION DE INASISTENCIA A FAENA"; $actividad="FAENA"; }

	$sql="SELECT observacion, documento FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND codigoSocio='$codigoSocio'";
	$row=mysqli_query($conexion,$sql);
	$dato=mysqli_fetch_array($row);
	$observacion =texto($dato['observacion']);
	$documento   =$dato['documento'];
	if($documento){ $imagenJUS=$documentos.$documento; }else{ $imagenJUS=''; }

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
			<h1>'.$titulo.'</h1><br>			
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infoTexto">
				<tr>
					<td class="resaltar"><strong>CODIGO SOCIO:</strong><br>'.$codigoSocio.'</td>
					<td class="resaltar"><strong>NOMBRE SOCIO:</strong><br>'.texto($nombreSocio).'</td>
					<td class="resaltar"><strong>LOTES:</strong><br>'.ceros($lotesSocio,2).' LOTES</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>CODIGO '.$actividad.':</strong><br>'.$codigoActividad.'</td>
					<td class="resaltar" colspan="2"><strong>'.$actividad.':</strong><br>'.texto($temaActividad).'</td>
				</tr>
			</table>
			<br><hr><br>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infoTexto">
				<tr>
					<td class="resaltar"><strong>OBSERVACIONES DE JUSTIFICACION:</strong><br><p>'.$observacion.'</p></td>
				</tr>
	';
				if($documento){
					$reporte.='
						<tr>
							<td class="resaltar textoCen"><img src="'.$imagenJUS.'" class="docJUS"></td>
						</tr>
					';
				}

	$reporte.='
			</table>
	';

	$reporte.=$impresoPor.'
		<body>
		</html>
	';
	$proceso ="IMPRESION DE <strong>".$titulo.' - '.$temaActividad."</strong>";
	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);
	
	cerrarDB();

	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'portait');
	$dompdf->render();
	$dompdf->stream("justificacion-".$codigoSocio.".pdf");
?>