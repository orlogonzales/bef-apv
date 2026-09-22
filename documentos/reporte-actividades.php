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
	$tipoActividad   =$_GET[tipoActividad];
	$codigoActividad =$_GET[codigoActividad];
	$opcion          =$_GET[opcion];
	$hoy             =fechaSQL(infoTiempo('fechaHoy'));
	$hora            =infoTiempo('hora');
	$dniUsuario      =$_SESSION['dni_apv'];
	$impresoPor      ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$temaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');

	if($tipoActividad=="ASA"){ $actividad="ASAMBLEA"; $rotuloArchivo="asamblea"; }
	if($tipoActividad=="FAE"){ $actividad="FAENA"; $rotuloArchivo="faena"; }

	$sql="SELECT codigoActividad, tipoActividad, temaActividad, contenidoActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta, fecha, hora, usuario FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'";
	$row=mysqli_query($conexion,$sql);
	$dato=mysqli_fetch_array($row);
	$temaActividad      =$dato[temaActividad];
	$contenidoActividad =$dato[contenidoActividad];
	$fechaActividad     =$dato[fechaActividad];
	$horaActividad      =$dato[horaActividad];
	$lugarActividad     =$dato[lugarActividad];
	$mTardanza         =$dato[mTardanza];
	$mFalta            =$dato[mFalta];
	$fecha             =$dato[fecha];
	$hora              =$dato[hora];
	$usuario           =$dato[usuario];
	$aforo             =infoActividad($idJuntaDirectiva,$codigoActividad,'','aforo');
	$asistio           =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
	$tarde             =infoActividad($idJuntaDirectiva,$codigoActividad,'','tarde');
	$justifico         =infoActividad($idJuntaDirectiva,$codigoActividad,'','justifico');
	$falto             =infoActividad($idJuntaDirectiva,$codigoActividad,'','falto');
	$mTardanzas        =infoActividad($idJuntaDirectiva,$codigoActividad,'','mtarde');
	$mFaltas           =infoActividad($idJuntaDirectiva,$codigoActividad,'','mFalta');
	$mJUS              =infoActividad($idJuntaDirectiva,$codigoActividad,'','mJUS');
	$totalMultas       =$mTardanzas+$mFaltas;
	$multasPagadas     =infoActividad($idJuntaDirectiva,$codigoActividad,'','mPagadas');
	$porPagar          =$totalMultas-$multasPagadas;
	$totalNeto         =$mJUS+$totalMultas;

	if($asistio>0){ $asistio=ceros($asistio,4); }else{ $asistio='----'; }
	if($tarde>0){ $tarde=ceros($tarde,4); }else{ $tarde='----'; }
	if($justifico>0){ $justifico=ceros($justifico,4); }else{ $justifico='----'; }
	if($falto>0){ $falto=ceros($falto,4); }else{ $falto='----'; }
	if($mTardanzas>0){ $mTardanzas='S/. '.moneda($mTardanzas,4); }else{ $mTardanzas='----'; }
	if($mFaltas>0){ $mFaltas='S/. '.moneda($mFaltas,4); }else{ $mFaltas='----'; }
	if($mJUS>0){ $mJUS='S/. - '.moneda($mJUS,4); }else{ $mJUS='----'; }
	if($totalMultas>0){ $totalMultas='S/. '.moneda($totalMultas,4); $totalNeto='S/. '.moneda($totalNeto); }else{ $totalMultas='----'; $totalNeto='----'; }
	if($multasPagadas>0){ $multasPagadas='S/. '.moneda($multasPagadas,4); }else{ $multasPagadas='----'; }
	if($porPagar>0){ $porPagar='S/. '.moneda($porPagar,4); }else{ $porPagar='----'; }

	$infoDatos='
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="noTabla">
			<tr>
				<td class="noTabla">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infoTexto">
						<tr><td class="resaltar mayusculas"><strong>INVITADOS:</strong> '.ceros($aforo,4).' SOCIOS</td></tr>
						<tr><td class="resaltar"><strong>ASISTENTES:</strong> '.$asistio.' SOCIOS</td></tr>
						<tr><td class="resaltar"><strong>TARDANZAS:</strong> '.$tarde.' SOCIOS</td></tr>
						<tr><td class="resaltar"><strong>FALTAS:</strong> '.$falto.' SOCIOS</td></tr>
						<tr><td class="resaltar"><strong>JUSTIFICACIONES:</strong> '.$justifico.' SOCIOS</td></tr>
						<tr><td class="resaltar"><strong>TOTAL JUSTIFICADOS:</strong> '.$mJUS.'</td></tr>
					</table>
				</td>
				<td class="noTabla espacioT">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infoTexto">
						<tr><td class="resaltar mayusculas"><strong>FECHA '.$actividad.':</strong><br>'.infoFecha($fechaActividad,'corta').'</td></tr>
						<tr><td class="resaltar"><strong>LUGAR '.$actividad.':</strong><br>'.texto($lugarActividad).'</td></tr>
						<tr><td class="resaltar"><strong>MULTA INASISTENCIA:</strong><br> S/. '.moneda($mFalta).'</td></tr>
						<tr><td class="resaltar"><strong>FECHA TARDANZA:</strong><br> S/. '.moneda($mTardanza).'</td></tr>
					</table>
				</td>
				<td class="noTabla">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infoTexto">
						<tr><td class="resaltar mayusculas"><strong>M. TARDANZAS:</strong> '.$mTardanzas.'</td></tr>
						<tr><td class="resaltar"><strong>M. INASISTENCIAS:</strong> '.$mFaltas.'</td></tr>
						<tr><td class="resaltar"><strong>TOTAL MULTAS:</strong> '.$totalMultas.'</td></tr>
						<tr><td class="resaltar"><strong>TOTAL PAGADO:</strong> '.$multasPagadas.'</td></tr>
						<tr><td class="resaltar"><strong>TOTAL POR PAGAR:</strong> '.$porPagar.'</td></tr>
						<tr><td class="resaltar"><strong>TOTAL NETO:</strong> '.$totalNeto.'</td></tr>
					</table>
				</td>
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
		$titulo    ='ASISTENTES A '.$actividad;
		$codigoACT ='CODIGO '.$actividad.': '.$codigoActividad;

		$reporte.='
			<h1>'.$titulo.'</h1>
			<h2>'.$temaActividad.'</h2>
			<h3>'.$codigoACT.'</h3>
			'.$infoDatos.'
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">CODIGO SOCIO</th>
					<th class="textoIzq">NOMBRE SOCIO</th>
					<th class="textoCen">DNI</th>
					<th class="textoCen">MULTA</th>
					<th class="textoCen">LTS</th>
					<th class="textoCen">TOTAL</th>
					<th class="textoCen">ASISTIO</th>
					<th class="textoCen">RAZON</th>
					<th class="textoCen">ESTADO</th>
				</tr>
			</thead>
			<tbody>
		';

		$sql="SELECT sm_mod_asistencia.asistio, sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.multa, sm_mod_asistencia.retraso, sm_mod_asistencia.estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_asistencia, sm_socios WHERE sm_mod_asistencia.codigoSocio=sm_socios.codigoSocio AND sm_mod_asistencia.codigoActividad='$codigoActividad' ORDER BY sm_socios.apPaterno ASC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$asistio        =$n[asistio];
			$codigoSocio    =$n[codigoSocio];
			$lotes          =$n[lotes];
			$temaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
			$multaTarde     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
			$multaFalta     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
			$multa          =$n[multa];
			$retraso        =$n[retraso];
			$estadoPago     =$n[estadoPago];
			$apPaterno      =$n[apPaterno];
			$apMaterno      =$n[apMaterno];
			$nombre         =$n[nombre];
			$dni            =$n[dni];
			$nombre         =texto($apPaterno.' '.$apMaterno.' '.$nombre);
			$porcentajePago =porcentajePago($codigoSocio,$codigoActividad,$multa);
			$razonBTpago    ='inasistencia';
			$rotuloCM       ='MULTA INASISTENCIA A '.$rotActMay;
			$programado     =conceptoProgramado($codigoSocio,$codigoActividad);

			if($programado>=2){ $verificaPago=verificaPagosProgramados($codigoActividad,$codigoSocio,$multa); }else{ $verificaPago=verificaPago($codigoActividad,$codigoSocio,$multa); }

			if($asistio=="SI"){
				$infoAsistio="SI";
				$infoRazon="";
				$multaPU="";
				$totalpago="";
				$infoEstado="ASISTIO";
			}

			if($asistio=="SI" and $retraso>0.15){
				$infoAsistio="SI";
				$infoRazon="TARDE";
				$multaPU="S/. ".moneda($multaTarde);
				$totalpago="S/. ".moneda($multa);
				if($verificaPago=="OK"){ $caja='[CAJA]'; }
				if($verificaPago=="ERROR"){ $caja='[ERROR]'; }
				if($estadoPago=="NP"){ $infoEstado="SIN PAGO"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago==0){ $infoEstado="PROGRAMADO ".ceros($programado,2)."F"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago>0){ $infoEstado="PAGO AL ".$porcentajePago."%"; }
				if($estadoPago=="SP"){ $infoEstado="PAGADO - ".$caja; }
				if($estadoPago=="SP" and $porcentajePago>0){ $infoEstado="PAGADO - ".ceros($programado,2)."F ".$caja; }
			}

			if($asistio=="NO"){
				$infoAsistio="NO";
				$infoRazon="FALTO";
				$multaPU="S/. ".moneda($multaFalta);
				$totalpago="S/. ".moneda($multa);
				if($verificaPago=="OK"){ $caja='[CAJA]'; }
				if($verificaPago=="ERROR"){ $caja='[ERROR]'; }
				if($estadoPago=="NP"){ $infoEstado="SIN PAGO"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago==0){ $infoEstado="PROGRAMADO ".ceros($programado,2)."F"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago>0){ $infoEstado="PAGO AL ".$porcentajePago."%"; }
				if($estadoPago=="SP"){ $infoEstado="PAGADO - ".$caja; }
				if($estadoPago=="SP" and $porcentajePago>0){ $infoEstado="PAGADO - ".ceros($programado,2)."F ".$caja; }
			}

			if($asistio=="JU" and $retraso>0.15 and $multa>0){
				$infoAsistio="TARDE";
				$infoRazon="JUSTIFICADO";
				$multaPU="S/. ".moneda($multaTarde);
				$totalpago="S/. ".moneda($multa);
				$infoEstado="JUSTIFICADO - TARDANZA ".ceros($retraso,2)." MIN";
			}

			if($asistio=="JU" and $retraso==0 and $multa>0){
				$infoAsistio="NO";
				$infoRazon="JUSTIFICADO";
				$multaPU="S/. ".moneda($multaFalta);
				$totalpago="S/. ".moneda($multa);
				$infoEstado="JUSTIFICADO - FALTA";
			}

			if($asistio=="IN"){
				$infoAsistio="";
				$infoRazon="";
				$multaPU="";
				$totalpago="";
				$infoEstado="S/P";
			}

		$reporte.='
			<tr>
				<td class="textoCen">'.ceros($i,4).'</td>
				<td class="textoCen">'.$codigoSocio.'</td>
				<td class="textoIzq">'.texto($nombre).'</td>
				<td class="textoCen">'.$dni.'</td>
				<td class="textoDer resaltar">'.$multaPU.'</td>
				<td class="textoCen">'.ceros($lotes,2).'</td>
				<td class="textoDer resaltar">'.$totalpago.'</td>
				<td class="textoCen">'.$infoAsistio.'</td>
				<td class="textoCen">'.$infoRazon.'</td>
				<td class="textoCen">'.$infoEstado.'</td>
			</tr>
		';

		$i++; }

		$reporte.='
			</tbody>
		</table>
		';
	}

	if($opcion=="reporte_tardanza"){
		$titulo    ='ASISTENTES CON TARDANZA A '.$actividad;
		$codigoACT ='CODIGO '.$actividad.': '.$codigoActividad;

		$reporte.='
			<h1>'.$titulo.'</h1>
			<h2>'.$temaActividad.'</h2>
			<h3>'.$codigoACT.'</h3>
			'.$infoDatos.'
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">CODIGO SOCIO</th>
					<th class="textoIzq">NOMBRE SOCIO</th>
					<th class="textoCen">DNI</th>
					<th class="textoCen">MULTA</th>
					<th class="textoCen">LTS</th>
					<th class="textoCen">TOTAL</th>
					<th class="textoCen">ASISTIO</th>
					<th class="textoCen">RAZON</th>
					<th class="textoCen">ESTADO</th>
				</tr>
			</thead>
			<tbody>
		';

		$sql="SELECT sm_mod_asistencia.asistio, sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.multa, sm_mod_asistencia.retraso, sm_mod_asistencia.estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_asistencia, sm_socios WHERE sm_mod_asistencia.codigoSocio=sm_socios.codigoSocio AND sm_mod_asistencia.codigoActividad='$codigoActividad' AND sm_mod_asistencia.asistio='SI' AND sm_mod_asistencia.retraso>0.15 ORDER BY sm_socios.apPaterno ASC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$asistio        =$n[asistio];
			$codigoSocio    =$n[codigoSocio];
			$lotes          =$n[lotes];
			$temaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
			$multaTarde     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
			$multaFalta     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
			$multa          =$n[multa];
			$retraso        =$n[retraso];
			$estadoPago     =$n[estadoPago];
			$apPaterno      =$n[apPaterno];
			$apMaterno      =$n[apMaterno];
			$nombre         =$n[nombre];
			$dni            =$n[dni];
			$nombre         =texto($apPaterno.' '.$apMaterno.' '.$nombre);
			$porcentajePago =porcentajePago($codigoSocio,$codigoActividad,$multa);
			$razonBTpago    ='inasistencia';
			$rotuloCM       ='MULTA INASISTENCIA A '.$rotActMay;
			$programado     =conceptoProgramado($codigoSocio,$codigoActividad);

			if($programado>=2){ $verificaPago=verificaPagosProgramados($codigoActividad,$codigoSocio,$multa); }else{ $verificaPago=verificaPago($codigoActividad,$codigoSocio,$multa); }

			if($asistio=="SI"){
				$infoAsistio="SI";
				$infoRazon="";
				$multaPU="";
				$totalpago="";
				$infoEstado="ASISTIO";
			}

			if($asistio=="SI" and $retraso>0.15){
				$infoAsistio="SI";
				$infoRazon="TARDE";
				$multaPU="S/. ".moneda($multaTarde);
				$totalpago="S/. ".moneda($multa);
				if($verificaPago=="OK"){ $caja='[CAJA]'; }
				if($verificaPago=="ERROR"){ $caja='[ERROR]'; }
				if($estadoPago=="NP"){ $infoEstado="SIN PAGO"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago==0){ $infoEstado="PROGRAMADO ".ceros($programado,2)."F"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago>0){ $infoEstado="PAGO AL ".$porcentajePago."%"; }
				if($estadoPago=="SP"){ $infoEstado="PAGADO - ".$caja; }
				if($estadoPago=="SP" and $porcentajePago>0){ $infoEstado="PAGADO - ".ceros($programado,2)."F ".$caja; }
			}

			if($asistio=="NO"){
				$infoAsistio="NO";
				$infoRazon="FALTO";
				$multaPU="S/. ".moneda($multaFalta);
				$totalpago="S/. ".moneda($multa);
				if($verificaPago=="OK"){ $caja='[CAJA]'; }
				if($verificaPago=="ERROR"){ $caja='[ERROR]'; }
				if($estadoPago=="NP"){ $infoEstado="SIN PAGO"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago==0){ $infoEstado="PROGRAMADO ".ceros($programado,2)."F"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago>0){ $infoEstado="PAGO AL ".$porcentajePago."%"; }
				if($estadoPago=="SP"){ $infoEstado="PAGADO - ".$caja; }
				if($estadoPago=="SP" and $porcentajePago>0){ $infoEstado="PAGADO - ".ceros($programado,2)."F ".$caja; }
			}

			if($asistio=="JU" and $retraso>0.15 and $multa>0){
				$infoAsistio="TARDE";
				$infoRazon="JUSTIFICADO";
				$multaPU="S/. ".moneda($multaTarde);
				$totalpago="S/. ".moneda($multa);
				$infoEstado="JUSTIFICADO - TARDANZA ".ceros($retraso,2)." MIN";
			}

			if($asistio=="JU" and $retraso==0 and $multa>0){
				$infoAsistio="NO";
				$infoRazon="JUSTIFICADO";
				$multaPU="S/. ".moneda($multaFalta);
				$totalpago="S/. ".moneda($multa);
				$infoEstado="JUSTIFICADO - FALTA";
			}

			if($asistio=="IN"){
				$infoAsistio="";
				$infoRazon="";
				$multaPU="";
				$totalpago="";
				$infoEstado="S/P";
			}

		$reporte.='
			<tr>
				<td class="textoCen">'.ceros($i,4).'</td>
				<td class="textoCen">'.$codigoSocio.'</td>
				<td class="textoIzq">'.texto($nombre).'</td>
				<td class="textoCen">'.$dni.'</td>
				<td class="textoDer resaltar">'.$multaPU.'</td>
				<td class="textoCen">'.ceros($lotes,2).'</td>
				<td class="textoDer resaltar">'.$totalpago.'</td>
				<td class="textoCen">'.$infoAsistio.'</td>
				<td class="textoCen">'.$infoRazon.'</td>
				<td class="textoCen">'.$infoEstado.'</td>
			</tr>
		';

		$i++; }

		$reporte.='
			</tbody>
		</table>
		';
	}

	if($opcion=="reporte_falta"){
		$titulo    ='INASISTENCIAS A '.$actividad;
		$codigoACT ='CODIGO '.$actividad.': '.$codigoActividad;

		$reporte.='
			<h1>'.$titulo.'</h1>
			<h2>'.$temaActividad.'</h2>
			<h3>'.$codigoACT.'</h3>
			'.$infoDatos.'
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">CODIGO SOCIO</th>
					<th class="textoIzq">NOMBRE SOCIO</th>
					<th class="textoCen">DNI</th>
					<th class="textoCen">MULTA</th>
					<th class="textoCen">LTS</th>
					<th class="textoCen">TOTAL</th>
					<th class="textoCen">ASISTIO</th>
					<th class="textoCen">RAZON</th>
					<th class="textoCen">ESTADO</th>
				</tr>
			</thead>
			<tbody>
		';

		$sql="SELECT sm_mod_asistencia.asistio, sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.multa, sm_mod_asistencia.retraso, sm_mod_asistencia.estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_asistencia, sm_socios WHERE sm_mod_asistencia.codigoSocio=sm_socios.codigoSocio AND sm_mod_asistencia.codigoActividad='$codigoActividad' AND sm_mod_asistencia.asistio='NO' ORDER BY sm_socios.apPaterno ASC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$asistio        =$n[asistio];
			$codigoSocio    =$n[codigoSocio];
			$lotes          =$n[lotes];
			$temaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
			$multaTarde     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
			$multaFalta     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
			$multa          =$n[multa];
			$retraso        =$n[retraso];
			$estadoPago     =$n[estadoPago];
			$apPaterno      =$n[apPaterno];
			$apMaterno      =$n[apMaterno];
			$nombre         =$n[nombre];
			$dni            =$n[dni];
			$nombre         =texto($apPaterno.' '.$apMaterno.' '.$nombre);
			$porcentajePago =porcentajePago($codigoSocio,$codigoActividad,$multa);
			$razonBTpago    ='inasistencia';
			$rotuloCM       ='MULTA INASISTENCIA A '.$rotActMay;
			$programado     =conceptoProgramado($codigoSocio,$codigoActividad);

			if($programado>=2){ $verificaPago=verificaPagosProgramados($codigoActividad,$codigoSocio,$multa); }else{ $verificaPago=verificaPago($codigoActividad,$codigoSocio,$multa); }

			if($asistio=="SI"){
				$infoAsistio="SI";
				$infoRazon="";
				$multaPU="";
				$totalpago="";
				$infoEstado="ASISTIO";
			}

			if($asistio=="SI" and $retraso>0.15){
				$infoAsistio="SI";
				$infoRazon="TARDE";
				$multaPU="S/. ".moneda($multaTarde);
				$totalpago="S/. ".moneda($multa);
				if($verificaPago=="OK"){ $caja='[CAJA]'; }
				if($verificaPago=="ERROR"){ $caja='[ERROR]'; }
				if($estadoPago=="NP"){ $infoEstado="SIN PAGO"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago==0){ $infoEstado="PROGRAMADO ".ceros($programado,2)."F"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago>0){ $infoEstado="PAGO AL ".$porcentajePago."%"; }
				if($estadoPago=="SP"){ $infoEstado="PAGADO - ".$caja; }
				if($estadoPago=="SP" and $porcentajePago>0){ $infoEstado="PAGADO - ".ceros($programado,2)."F ".$caja; }
			}

			if($asistio=="NO"){
				$infoAsistio="NO";
				$infoRazon="FALTO";
				$multaPU="S/. ".moneda($multaFalta);
				$totalpago="S/. ".moneda($multa);
				if($verificaPago=="OK"){ $caja='[CAJA]'; }
				if($verificaPago=="ERROR"){ $caja='[ERROR]'; }
				if($estadoPago=="NP"){ $infoEstado="SIN PAGO"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago==0){ $infoEstado="PROGRAMADO ".ceros($programado,2)."F"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago>0){ $infoEstado="PAGO AL ".$porcentajePago."%"; }
				if($estadoPago=="SP"){ $infoEstado="PAGADO - ".$caja; }
				if($estadoPago=="SP" and $porcentajePago>0){ $infoEstado="PAGADO - ".ceros($programado,2)."F ".$caja; }
			}

			if($asistio=="JU" and $retraso>0.15 and $multa>0){
				$infoAsistio="TARDE";
				$infoRazon="JUSTIFICADO";
				$multaPU="S/. ".moneda($multaTarde);
				$totalpago="S/. ".moneda($multa);
				$infoEstado="JUSTIFICADO - TARDANZA ".ceros($retraso,2)." MIN";
			}

			if($asistio=="JU" and $retraso==0 and $multa>0){
				$infoAsistio="NO";
				$infoRazon="JUSTIFICADO";
				$multaPU="S/. ".moneda($multaFalta);
				$totalpago="S/. ".moneda($multa);
				$infoEstado="JUSTIFICADO - FALTA";
			}

			if($asistio=="IN"){
				$infoAsistio="";
				$infoRazon="";
				$multaPU="";
				$totalpago="";
				$infoEstado="S/P";
			}

		$reporte.='
			<tr>
				<td class="textoCen">'.ceros($i,4).'</td>
				<td class="textoCen">'.$codigoSocio.'</td>
				<td class="textoIzq">'.texto($nombre).'</td>
				<td class="textoCen">'.$dni.'</td>
				<td class="textoDer resaltar">'.$multaPU.'</td>
				<td class="textoCen">'.ceros($lotes,2).'</td>
				<td class="textoDer resaltar">'.$totalpago.'</td>
				<td class="textoCen">'.$infoAsistio.'</td>
				<td class="textoCen">'.$infoRazon.'</td>
				<td class="textoCen">'.$infoEstado.'</td>
			</tr>
		';

		$i++; }

		$reporte.='
			</tbody>
		</table>
		';
	}

	if($opcion=="reporte_justificacion"){
		$titulo    ='JUSTIFICACIONES A '.$actividad;
		$codigoACT ='CODIGO '.$actividad.': '.$codigoActividad;

		$reporte.='
			<h1>'.$titulo.'</h1>
			<h2>'.$temaActividad.'</h2>
			<h3>'.$codigoACT.'</h3>
			'.$infoDatos.'
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">CODIGO SOCIO</th>
					<th class="textoIzq">NOMBRE SOCIO</th>
					<th class="textoCen">DNI</th>
					<th class="textoCen">MULTA</th>
					<th class="textoCen">LTS</th>
					<th class="textoCen">TOTAL</th>
					<th class="textoCen">ASISTIO</th>
					<th class="textoCen">RAZON</th>
					<th class="textoCen">ESTADO</th>
				</tr>
			</thead>
			<tbody>
		';

		$sql="SELECT sm_mod_asistencia.asistio, sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.multa, sm_mod_asistencia.retraso, sm_mod_asistencia.estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_asistencia, sm_socios WHERE sm_mod_asistencia.codigoSocio=sm_socios.codigoSocio AND sm_mod_asistencia.codigoActividad='$codigoActividad' AND sm_mod_asistencia.asistio='JU' ORDER BY sm_socios.apPaterno ASC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$asistio        =$n[asistio];
			$codigoSocio    =$n[codigoSocio];
			$lotes          =$n[lotes];
			$temaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
			$multaTarde     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
			$multaFalta     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
			$multa          =$n[multa];
			$retraso        =$n[retraso];
			$estadoPago     =$n[estadoPago];
			$apPaterno      =$n[apPaterno];
			$apMaterno      =$n[apMaterno];
			$nombre         =$n[nombre];
			$dni            =$n[dni];
			$nombre         =texto($apPaterno.' '.$apMaterno.' '.$nombre);
			$porcentajePago =porcentajePago($codigoSocio,$codigoActividad,$multa);
			$razonBTpago    ='inasistencia';
			$rotuloCM       ='MULTA INASISTENCIA A '.$rotActMay;
			$programado     =conceptoProgramado($codigoSocio,$codigoActividad);

			if($programado>=2){ $verificaPago=verificaPagosProgramados($codigoActividad,$codigoSocio,$multa); }else{ $verificaPago=verificaPago($codigoActividad,$codigoSocio,$multa); }

			if($asistio=="SI"){
				$infoAsistio="SI";
				$infoRazon="";
				$multaPU="";
				$totalpago="";
				$infoEstado="ASISTIO";
			}

			if($asistio=="SI" and $retraso>0.15){
				$infoAsistio="SI";
				$infoRazon="TARDE";
				$multaPU="S/. ".moneda($multaTarde);
				$totalpago="S/. ".moneda($multa);
				if($verificaPago=="OK"){ $caja='[CAJA]'; }
				if($verificaPago=="ERROR"){ $caja='[ERROR]'; }
				if($estadoPago=="NP"){ $infoEstado="SIN PAGO"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago==0){ $infoEstado="PROGRAMADO ".ceros($programado,2)."F"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago>0){ $infoEstado="PAGO AL ".$porcentajePago."%"; }
				if($estadoPago=="SP"){ $infoEstado="PAGADO - ".$caja; }
				if($estadoPago=="SP" and $porcentajePago>0){ $infoEstado="PAGADO - ".ceros($programado,2)."F ".$caja; }
			}

			if($asistio=="NO"){
				$infoAsistio="NO";
				$infoRazon="FALTO";
				$multaPU="S/. ".moneda($multaFalta);
				$totalpago="S/. ".moneda($multa);
				if($verificaPago=="OK"){ $caja='[CAJA]'; }
				if($verificaPago=="ERROR"){ $caja='[ERROR]'; }
				if($estadoPago=="NP"){ $infoEstado="SIN PAGO"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago==0){ $infoEstado="PROGRAMADO ".ceros($programado,2)."F"; }
				if($estadoPago=="MP" and $programado>=2 and $porcentajePago>0){ $infoEstado="PAGO AL ".$porcentajePago."%"; }
				if($estadoPago=="SP"){ $infoEstado="PAGADO - ".$caja; }
				if($estadoPago=="SP" and $porcentajePago>0){ $infoEstado="PAGADO - ".ceros($programado,2)."F ".$caja; }
			}

			if($asistio=="JU" and $retraso>0.15 and $multa>0){
				$infoAsistio="TARDE";
				$infoRazon="JUSTIFICADO";
				$multaPU="S/. ".moneda($multaTarde);
				$totalpago="S/. - ".moneda($multa);
				$infoEstado="JUSTIFICADO - TARDANZA ".ceros($retraso,2)." MIN";
			}

			if($asistio=="JU" and $retraso==0 and $multa>0){
				$infoAsistio="NO";
				$infoRazon="JUSTIFICADO";
				$multaPU="S/. ".moneda($multaFalta);
				$totalpago="S/. - ".moneda($multa);
				$infoEstado="JUSTIFICADO - FALTA";
			}

			if($asistio=="IN"){
				$infoAsistio="";
				$infoRazon="";
				$multaPU="";
				$totalpago="";
				$infoEstado="S/P";
			}

		$reporte.='
			<tr>
				<td class="textoCen">'.ceros($i,4).'</td>
				<td class="textoCen">'.$codigoSocio.'</td>
				<td class="textoIzq">'.texto($nombre).'</td>
				<td class="textoCen">'.$dni.'</td>
				<td class="textoDer resaltar">'.$multaPU.'</td>
				<td class="textoCen">'.ceros($lotes,2).'</td>
				<td class="textoDer resaltar">'.$totalpago.'</td>
				<td class="textoCen">'.$infoAsistio.'</td>
				<td class="textoCen">'.$infoRazon.'</td>
				<td class="textoCen">'.$infoEstado.'</td>
			</tr>
		';

		$i++; }

		$reporte.='
			</tbody>
		</table>
		';
	}

	$reporte.=$impresoPor.'
		<body>
		</html>
	';

	$proceso ="IMPRESION DE <strong>".$titulo.' - '.$temaActividad."</strong>";
	$codigoSocio="";

	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);
	
	cerrarDB();

	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
	$dompdf->stream("reporte-".$rotuloArchivo."-".$codigoActividad.".pdf");
?>