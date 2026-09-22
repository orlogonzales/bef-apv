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
    $sector     = $_GET[sector];
	$manzana    = $_GET[manzana];
	$ordenar    = $_GET[ordenar];
	$hoy        = fechaSQL(infoTiempo('fechaHoy'));
	$hora       = infoTiempo('hora');
	$dniUsuario = $_SESSION['dni_apv'];
	$impresoPor = '<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$totalMetros= 0;

	if($ordenar=='LOTE'){
		$orden="CAST(SUBSTRING_INDEX(sm_lotes_socio.lote, '-', -1) AS UNSIGNED) ASC";
	}

	if($ordenar=='NOMBRE'){
		$orden="sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
	}

	if($ordenar=='APELLIDO'){
		$orden="sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
	}

	if($sector!='ALL' && $manzana!='ALL'){
		$infoSector=$sector;
		$infoManzana=$manzana;
		$rotuloSector=$sector;
		$rotuloManzana=$manzana;
		$sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio WHERE sm_lotes_socio.sector = '$sector' AND sm_lotes_socio.manzana = '$manzana' ORDER BY $orden";
	}

	if($sector!='ALL' && $manzana=='ALL'){
		$infoSector=$sector;
		$infoManzana='TODAS LAS MANZANAS';
		$rotuloSector=$sector;
		$rotuloManzana='todos';
		$sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio WHERE sm_lotes_socio.sector = '$sector' ORDER BY $orden";
	}

	if($sector=='ALL' && $manzana=='ALL'){
		$infoSector='TODOS LOS SECTORES';
		$infoManzana='TODAS LAS MANZANAS';
		$rotuloSector='todos';
		$rotuloManzana='todos';
		$sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio ORDER BY $orden";
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

	$reporte.='<h1>REPORTE DE SOCIOS</h1>';
	$reporte.='<h2>SECTOR: '.$infoSector.'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;MANZANA: '.$infoManzana.'</h2><br>';

	
	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="textoCen">#</th>
				<th class="textoCen">CODIGO</th>
				<th class="textoIzq">NOMBRE SOCIO</th>
				<th class="textoCen">DNI</th>
				<th class="textoCen">CELULAR</th>
				<th class="textoCen">LTS</th>
				<th class="textoCen">S</th>
				<th class="textoCen">M</th>
				<th class="textoCen">L</th>
				<th class="textoCen">METRAJE</th>
			</tr>
		</thead>
		<tbody>
	';

	while($n=mysqli_fetch_array($rs)){
		$codigoSocio     = $n['codigoSocio'];
		$dni             = $n['dni'];
		$tratamiento     = $n['tratamiento'];
		$nombre          = $n['nombre'];
		$apPaterno       = $n['apPaterno'];
		$apMaterno       = $n['apMaterno'];
		$nombreSocio     = trim($nombre.' '.$apPaterno.' '.$apMaterno);
		$genero          = $n['genero'];
		$fechaNacimiento = $n['fechaNacimiento'];
		$fotoSocio       = $n['fotoSocio'];
		$nacionalidad    = $n['nacionalidad'];
		$estadoCivil     = $n['estadoCivil'];
		$direccion       = $n['direccion'];
		$departamento    = $n['departamento'];
		$provincia       = $n['provincia'];
		$distrito        = $n['distrito'];
		$telefono        = $n['telefono'];
		$celular         = $n['celular'];
		$observaciones   = $n['observaciones'];
		$sector          = $n['sector'];
		$manzana         = $n['manzana'];
		$lote            = $n['lote'];
		$metraje         = $n['metraje'];
		$infoMetraje     = $metraje.' m²';

		$cantidadLotes   = infoSocios($codigoSocio,'cantidadLotes');
		$reporte.='
			<tr>
				<td class="textoCen">'.ceros($i,4).'</td>
				<td class="textoCen">'.$codigoSocio.'</td>
				<td class="textoIzq">'.texto($nombreSocio).'</td>
				<td class="textoCen">'.$dni.'</td>
				<td class="textoCen resaltar">'.$celular.'</td>
				<td class="textoCen">'.ceros($cantidadLotes,2).'</td>
				<td class="textoDer resaltar">'.$sector.'</td>
				<td class="textoCen">'.$manzana.'</td>
				<td class="textoCen">'.$lote.'</td>
				<td class="textoCen">'.$infoMetraje.'</td>
			</tr>
		';

		$totalMetros=bcdiv(($totalMetros+$metraje), '1', 2);
		$i++;
	}
	$reporte.='
		<tbody>
	</table>
	<br>
	<br>
	<h2>TOTAL METRAJE EN REPORTE '.moneda($totalMetros).'m²</h2>
	';

	$reporte.=$impresoPor;
	$reporte.='
		<body>
		</html>
	';

	$proceso ="IMPRESION DE DEPORTE DE SOCIOS DE SECTOR ".$infoSector." Y MANZANA ".$infoManzana;
	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
	$dompdf->stream("reporte-socios-sector-".$rotuloSector."-manzana-".$rotuloManzana.".pdf");
?>