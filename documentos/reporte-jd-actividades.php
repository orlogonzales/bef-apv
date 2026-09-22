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

    $conexion   = conexionDB();
    $idJuntaDirectiva = $_GET['idJuntaDirectiva'];
	$tipoActividad    = $_GET['tipoActividad'];
	$codigoActividad  = $_GET['codigoActividad'];
	$codigoCuenta     = $_GET['codigoCuenta'];
	$fechaInicio      = $_GET['fechaInicio'];
	$fechaFin         = $_GET['fechaFin'];

	$hoy        = fechaSQL(infoTiempo('fechaHoy'));
	$hora       = infoTiempo('hora');
	$dniUsuario = $_SESSION['dni_apv'];
	$impresoPor = '<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$totalMetros= 0;

	if($tipoActividad=='FAE'){
		$rotulo="FAENA";
	}else if($tipoActividad=='ASA'){
		$rotulo="ASAMBLEA";
	}else if($tipoActividad=='ALL'){
		$rotulo="TODOS";
	}else{
		$rotulo="ERROR";
	}

	$query = "SELECT CONCAT(sm_socios.nombre,' ', sm_socios.apPaterno,' ', sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
	$row=mysqli_query($conexion,$query);
	$dato=mysqli_fetch_array($row);
	$nombrePresidente=$dato[nombrePresidente];

	$query = "SELECT fechaPeriodo, fechaFinPeriodo FROM sm_junta_directiva WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
	$row=mysqli_query($conexion,$query);
	$dato=mysqli_fetch_array($row);
	$fechaPeriodo=$dato[fechaPeriodo];
	$fechaFinPeriodo=$dato[fechaFinPeriodo];
	$inicioPeriodo=infoFecha($fechaPeriodo,'year');
	$finPeriodo=infoFecha($fechaFinPeriodo,'year');

	$infoperiodo= $inicioPeriodo.' - '.$finPeriodo;

	if($tipoActividad!='ALL'){
		$query = "SELECT temaActividad, fechaActividad FROM sm_mod_actividades WHERE codigoActividad = '$codigoActividad'";
		$row=mysqli_query($conexion,$query);
		$dato=mysqli_fetch_array($row);
		$temaActividad=$dato[temaActividad];
		$fechaActividad=$dato[fechaActividad];
		$infoTemaActividad=strtoupper($temaActividad.' | FECHA: '.infoFecha($fechaActividad,'normal'));

		$sql="SELECT sm_mod_asistencia.tipoActividad, sm_mod_asistencia.codigoActividad, sm_mod_asistencia.codigoSocio, sm_socios.dni, CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio, sm_mod_asistencia.asistio, sm_mod_asistencia.multa, sm_mod_asistencia.estadoPago FROM sm_mod_asistencia INNER JOIN sm_socios ON sm_mod_asistencia.codigoSocio = sm_socios.codigoSocio WHERE tipoActividad = '$tipoActividad' AND codigoActividad = '$codigoActividad' ORDER BY sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
	}else{
		$sql="SELECT sm_mod_asistencia.tipoActividad, sm_mod_asistencia.codigoActividad, sm_mod_asistencia.codigoSocio, sm_socios.dni, CONCAT( sm_socios.nombre, ' ', sm_socios.apPaterno, ' ', sm_socios.apMaterno ) AS nombreSocio, sm_mod_asistencia.asistio, sm_mod_asistencia.multa, sm_mod_asistencia.estadoPago FROM sm_mod_asistencia INNER JOIN sm_socios ON sm_mod_asistencia.codigoSocio = sm_socios.codigoSocio ORDER BY sm_mod_asistencia.tipoActividad ASC";
	}

	$rs=mysqli_query($conexion,$sql);

	$i=1;
	$totalMetros = 0;

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

	$reporte.='<h1>REPORTE DE '.$rotulo.'</h1>';
	$reporte.='<h2>'.$infoTemaActividad.'</h2><hr>';	
	$reporte.='<h2>PRESIDENTE: '.$nombrePresidente.'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;PERIODO: '.$infoperiodo.'</h2><br>';
	
	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="textoCen">#</th>
				<th class="textoCen">CODIGO</th>
				<th class="textoCen">TIPO/th>
				<th class="textoCen">ACTIVIDAD</th>
				<th class="textoCen">FECHA</th>
				<th class="textoIzq">SOCIO</th>
				<th class="textoCen">LTS</th>
				<th class="textoCen">ASISTIO</th>
				<th class="textoDer">MULTA</th>
				<th class="textoCen">PAGO</th>
				<th class="textoCen">CAJA</th>
			</tr>
		</thead>
		<tbody>
	';

	$consulta = $conexion->query($sql);
	$i=0;
	$totalMultas=0;
	$montosPagados=0;
	$montosPorCobrar=0;

	while($n=mysqli_fetch_array($rs)){
		$query = "SELECT COUNT(id) AS perteneceJD FROM sm_mod_actividades WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
		$row=mysqli_query($conexion,$query);
		$dato=mysqli_fetch_array($row);
		$perteneceJD=$dato[perteneceJD];
		if($perteneceJD>0){
			if(!empty($_GET['idJuntaDirectiva'])){
				$codigoActividad=$n[codigoActividad];
			}

			if(!empty($_GET['tipoActividad'])){
				$tipoActividad=$n[tipoActividad];
			}

			$codigoSocio=$n[codigoSocio];
			$dni=$n[dni];
			$nombreSocio=$n[nombreSocio];
			$asistio=$n[asistio];
			$multa=$n[multa];
			$estadoPago=$n[estadoPago];
			$infoCodigoActividad=$codigoActividad;

			if($tipoActividad=='FAE'){
				$infoTipoActividad ='FAENA';
			}else if($tipoActividad=='ASA'){
				$infoTipoActividad ='ASAMBLEA';
			}

			if($asistio=='SI'){
				$infoAsistencia='SI';
				$infoMulta='';
			}else{
				$infoAsistencia='NO';
				$infoMulta='S/. '.$multa;
			}

			$query = "SELECT COUNT(id) AS lotesSocio FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
			$info = $conexion->query($query);
			$resultado = $info->fetch_assoc();
			$lotesSocio = $resultado[lotesSocio];

			$query = "SELECT temaActividad, fechaActividad FROM sm_mod_actividades WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND tipoActividad = '$tipoActividad' AND codigoActividad = '$codigoActividad'";
			$info = $conexion->query($query);
			$resultado = $info->fetch_assoc();
			$nombreActividad = $resultado[temaActividad];
			$fechaActividad = $resultado[fechaActividad];
			$infoFechaActividad=infoFecha($fechaActividad,'normal');

			$query = "SELECT monto, fechaOperacion FROM sm_mod_caja WHERE movimiento='ING' AND idJuntaDirectiva='$idJuntaDirectiva' AND codigoSocio='$codigoSocio' AND codigoConcepto ='$codigoActividad'";
			$info = $conexion->query($query);
			$resultado = $info->fetch_assoc();
			$monto = $resultado[monto];
			$fechaOperacion = $resultado[fechaOperacion];
			
			if($estadoPago=='SP' && $monto==$multa){
				$infoPago = 'S/P';
				$infoCaja = 'VERIFICADO';
			}else if($estadoPago=='SP' && $monto!=$multa){
				$infoPago = 'P/O';
				$infoCaja = 'ERROR';
			}else{
				$infoPago = 'N/P';
				$infoCaja = '';
			}

			$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,3).'</td>
					<td class="textoCen">'.$infoCodigoActividad.'</td>
					<td class="textoCen">'.$infoTipoActividad.'</td>
					<td class="textoCen">'.$nombreActividad.'</td>
					<td class="textoCen">'.$infoFechaActividad.'</td>
					<td class="textoIzq">'.$nombreSocio.'</td>
					<td class="textoCen">'.$lotesSocio.'</td>
					<td class="textoCen">'.$infoAsistencia.'</td>
					<td class="textoDer">'.$infoMulta.'</td>
					<td class="textoCen">'.$infoPago.'</td>
					<td class="textoCen">'.$infoCaja.'</td>
				</tr>
			';

			$totalMultas=$totalMultas+$multa;
			$infoTotalMultas='S/. '.moneda($totalMultas);
			
			if($monto>0){
				$montosPagados=$montosPagados+$monto;
				$infoMontosPagados='S/. '.moneda($montosPagados);
			}else{
				$montosPorCobrar=$montosPorCobrar+$multa;
				$InfoMontosPorCobrar='S/. '.moneda($montosPorCobrar);
			}

			$i++;
		}
	}
	$reporte.='
		<tbody>
	</table>
	<br>
	';

	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tbody>
				<tr>
					<td><strong>TOTAL MULTAS</strong></td>
					<td class="textoDer">'.$infoTotalMultas.'</td>
				</tr>
				<tr>
					<td><strong>TOTAL PAGADOS</strong></td>
					<td class="textoDer">'.$montosPagados.'</td>
				</tr>
				<tr>
					<td><strong>TOTAL POR PAGAR</strong></td>
					<td class="textoDer">'.$InfoMontosPorCobrar.'</td>
				</tr>
			</tbody>
		</table>
		<br>
	';

	$reporte.=$impresoPor;
	$reporte.='
		<body>
		</html>
	';

	$proceso ="IMPRESION DE DEPORTE ".$infoTemaActividad.' | JUNTA DIRECTIVA '.$nombrePresidente.' DEL PERIODO '.$infoperiodo;
	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
	$dompdf->stream("reporte-actividad-jd.pdf");
?>