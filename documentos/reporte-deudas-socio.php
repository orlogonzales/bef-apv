<?php
	session_start();
	set_time_limit(300);
	ini_set("memory_limit","512M");
	
	$ruta="../";
	require_once($ruta.'php/funciones.php');
	require_once($ruta.'php/conexion.php');
	require_once $ruta.'dompdf/lib/html5lib/Parser.php';
	require_once $ruta.'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
	require_once $ruta.'dompdf/lib/php-svg-lib/src/autoload.php';
	require_once $ruta.'dompdf/src/Autoloader.php';
	Dompdf\Autoloader::register();
	use Dompdf\Dompdf;
	
    $conexion         = conexionDB();
    $idJuntaDirectiva = $_GET['idJuntaDirectiva'];
    $concepto         = $_GET['concepto'];
    $codigoSocio      = $_GET['codigoSocio'];
    $infoSocio        = infoSocios($codigoSocio,'nombreLargo');

    /*OBTIENE ETIQUETA LOTES Y LOTES DE SOCIO */
    $sql="SELECT codigoLote FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
	$rs = $conexion->query($sql);
	$cantidadResultados = $rs->num_rows;
	if($cantidadResultados==1){
		$etiquetaLotes='LOTE';
	}else{
		$etiquetaLotes='LOTES';
	}
	$l=1;

	while ($datos = $rs->fetch_assoc()) {
		$codigoLote = $datos['codigoLote'];
		if($cantidadResultados>1){
			if($l<$cantidadResultados){
				$separador = '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
			}else{
				$separador = '';
			}
			$infoLotes .= $codigoLote.$separador;
		}else{
			$infoLotes .= $codigoLote;
		}
		$l++;
	}
	
    /* OBTIENE PERIODO DE JUTA DIRECTIVA Y NOMBRE DE PRESIDENTE */
    if($idJuntaDirectiva=='ALL'){
    	$infoJD='TODOS';
    }else{
	    $sql="SELECT sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_junta_directiva.fechaPeriodo, sm_junta_directiva.fechaFinPeriodo, sm_junta_directiva_integrantes.codigoSocio FROM sm_junta_directiva INNER JOIN sm_junta_directiva_integrantes ON sm_junta_directiva.idJuntaDirectiva = sm_junta_directiva_integrantes.idJuntaDirectiva INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE sm_junta_directiva.idJuntaDirectiva = '$idJuntaDirectiva' AND sm_junta_directiva_integrantes.idCargoJunta = 1";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombre=$resultado[nombre];
		$apPaterno=$resultado[apPaterno];
		$apMaterno=$resultado[apMaterno];
		$nombrePresidente=$nombre.' '.$apPaterno.' '.$apMaterno;
		$fechaPeriodo=$resultado[fechaPeriodo];
		$fechaFinPeriodo=$resultado[fechaFinPeriodo];
		$infoJD=$nombrePresidente.' / '.infoFecha($fechaPeriodo,'normal').' - '.infoFecha($fechaFinPeriodo,'normal');
    }

    /* OBTIENE DATOS DE DEUDA SEGUN CONCEPTO */
    if($concepto=='ASA'){
        $infoConcepto='ASAMBLEA';
        if($idJuntaDirectiva='ALL'){
            $consultaJD='';
        }else{
            $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
        }

        $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
        $row=mysqli_query($conexion,$query);
        $dato=mysqli_fetch_array($row);
        $cantidadRegistrosASA=$dato['cantidadRegistros'];
        $totalDeudaASA=$dato['totalDeuda'];
        $totalDeuda=$totalDeudaASA;
    }

    if($concepto=='FAE'){
    	$infoConcepto='FAENA';
        if($idJuntaDirectiva='ALL'){
            $consultaJD='';
        }else{
            $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
        }

        $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
        $row=mysqli_query($conexion,$query);
        $dato=mysqli_fetch_array($row);
        $cantidadRegistrosFAE=$dato['cantidadRegistros'];
        $totalDeudaFAE=$dato['totalDeuda'];
        $totalDeuda=$totalDeudaFAE;
    }

    if($concepto=='CUO'){
        $infoConcepto='CUOTA';
        if($idJuntaDirectiva='ALL'){
            $consultaJD='';
        }else{
            $consultaJD='idJuntaDirectiva='.$idJuntaDirectiva.' AND';
        }

        $query="SELECT COUNT(sm_mod_cuotas_socios.id) AS cantidadRegistros, SUM(sm_mod_cuotas_socios.montoCuota) AS totalDeuda FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE $consultaJD sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0";
        $row=mysqli_query($conexion,$query);
        $dato=mysqli_fetch_array($row);
        $cantidadRegistrosCUO=$dato['cantidadRegistros'];
        $totalDeudaCUO=$dato['totalDeuda'];
        
        $totalDeuda=$totalDeudaCUO;
    }

    if($concepto=='ALL'){
    	$infoConcepto='TODOS';
        if($idJuntaDirectiva='ALL'){
            $consultaJD='';
        }else{
            $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
        }

        $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
        $row=mysqli_query($conexion,$query);
        $dato=mysqli_fetch_array($row);
        $cantidadRegistrosASA=$dato['cantidadRegistros'];
        $totalDeudaASA=$dato['totalDeuda'];

        $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
        $row=mysqli_query($conexion,$query);
        $dato=mysqli_fetch_array($row);
        $cantidadRegistrosFAE=$dato['cantidadRegistros'];
        $totalDeudaFAE=$dato['totalDeuda'];

        if($idJuntaDirectiva='ALL'){
            $consultaJD='';
        }else{
            $consultaJD='idJuntaDirectiva='.$idJuntaDirectiva.' AND';
        }

        $query="SELECT COUNT(sm_mod_cuotas_socios.id) AS cantidadRegistros, SUM(sm_mod_cuotas_socios.montoCuota) AS totalDeuda FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE $consultaJD sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0";
        $row=mysqli_query($conexion,$query);
        $dato=mysqli_fetch_array($row);
        $cantidadRegistrosCUO=$dato['cantidadRegistros'];
        $totalDeudaCUO=$dato['totalDeuda'];
        
        $totalDeuda=$totalDeudaASA+$totalDeudaFAE+$totalDeudaCUO;
    }

	$hoy        = fechaSQL(infoTiempo('fechaHoy'));
	$hora       = infoTiempo('hora');
	$dniUsuario = $_SESSION['dni_apv'];
	$impresoPor = '<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';

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

	$reporte.='<h1>REPORTE DE DEUDAS</h1>';
	$reporte.='
		<h2 class="mayusculas">
		<strong>'.$infoSocio.'</strong><br>
		<strong>'.$etiquetaLotes.':</strong> '.$infoLotes.'<br>
		JUNTA DIRECTIVA:</strong> '.$infoJD.'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;
		<strong>CONCEPTO:</strong> '.$infoConcepto.'
		</h2><br>';

	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="textoCen">#</th>
				<th class="textoCen">CONCEPTO</th>
				<th class="textoIzq">DETALLE CONCEPTO</th>
				<th class="textoCen">FECHA</th>
				<th class="textoCen">LOTES</th>
				<th class="textoDer">DEUDA</th>
			</tr>
		</thead>
		<tbody>
	';

		if($concepto=='ALL'){
			if($cantidadRegistrosASA>0){
					$a=1;
					$deudaASA=0;
					$infoConcepto='ASAMBLEA';
					$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.fechaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta, sm_mod_asistencia.lotes, sm_mod_asistencia.multa FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0 ORDER BY sm_mod_actividades.fechaActividad ASC";
					$rs = $conexion->query($sql);
					while ($datos = $rs->fetch_assoc()) {
						$temaActividad = $datos['temaActividad'];
						$fechaActividad = $datos['fechaActividad'];
						$mTardanza = $datos['mTardanza'];
						$mFalta = $datos['mFalta'];
						$lotes = $datos['lotes'];
						$multa = $datos['multa'];

						$reporte.='
							<tr>
								<td class="textoCen">'.ceros($a,2).'</td>
								<td class="textoCen">'.$infoConcepto.'</td>
								<td class="textoIzq">'.$temaActividad.'</td>
								<td class="textoCen mayusculas">'.infoFecha($fechaActividad,'normal').'</td>
								<td class="textoCen">'.ceros($lotes,2).'</td>
								<td class="textoDer resaltar">S/. <strong>'.moneda($multa,2).'</strong></td>
							</tr>
						';

						$deudaASA=$deudaASA+$multa;
						$a++;
					}
			}else{ $deudaASA=0; }

			if($cantidadRegistrosFAE>0){
				if($b>0){
					$b=$a;
				}else{
					$b=1;
				}
				$deudaFAE=0;
				$infoConcepto='FAENA';
				$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.fechaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta, sm_mod_asistencia.lotes, sm_mod_asistencia.multa FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0 ORDER BY sm_mod_actividades.fechaActividad ASC";
				$rs = $conexion->query($sql);
				while ($datos = $rs->fetch_assoc()) {
					$temaActividad = $datos['temaActividad'];
					$fechaActividad = $datos['fechaActividad'];
					$mTardanza = $datos['mTardanza'];
					$mFalta = $datos['mFalta'];
					$lotes = $datos['lotes'];
					$multa = $datos['multa'];

					$reporte.='
						<tr>
							<td class="textoCen">'.ceros($b,2).'</td>
							<td class="textoCen">'.$infoConcepto.'</td>
							<td class="textoIzq">'.$temaActividad.'</td>
							<td class="textoCen mayusculas">'.infoFecha($fechaActividad,'normal').'</td>
							<td class="textoCen">'.ceros($lotes,2).'</td>
							<td class="textoDer resaltar">S/. <strong>'.moneda($multa,2).'</strong></td>
						</tr>
					';

					$deudaFAE=$deudaFAE+$multa;
					$b++;
				}
			}else{ $deudaFAE=0; }

			if($totalDeudaCUO>0){
					if($b>0){
						$c=$b;
					}else{
						$c=$a;
					}
					$deudaCUO=0;
					$infoConcepto='CUOTA';
					$sql="SELECT sm_mod_cuotas.conceptoCuota, sm_mod_cuotas.montoCuota, sm_mod_cuotas.fechaPago, sm_mod_cuotas_socios.lotes, sm_mod_cuotas_socios.montoPago FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0 ORDER BY sm_mod_cuotas.fechaPago ASC";
					$rs = $conexion->query($sql);
					while ($datos = $rs->fetch_assoc()) {
						$conceptoCuota = $datos['conceptoCuota'];
						$fechaPago = $datos['fechaPago'];
						$montoCuota = $datos['montoCuota'];
						$lotes = $datos['lotes'];
						$montoPago = $datos['montoPago'];

						$reporte.='
							<tr>
								<td class="textoCen">'.ceros($c,2).'</td>
								<td class="textoCen">'.$infoConcepto.'</td>
								<td class="textoIzq">'.$conceptoCuota.'</td>
								<td class="textoCen mayusculas">'.infoFecha($fechaPago,'normal').'</td>
								<td class="textoCen">'.ceros($lotes,2).'</td>
								<td class="textoDer resaltar">S/. <strong>'.moneda($montoPago,2).'</strong></td>
							</tr>
						';
					
						$deudaCUO=$deudaCUO+$montoPago;
						$c++;
					}
			}
		}

		if($concepto=='ASA'){
			if($cantidadRegistrosASA>0){
					$a=1;
					$deudaASA=0;
					$infoConcepto='ASAMBLEA';
					$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.fechaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta, sm_mod_asistencia.lotes, sm_mod_asistencia.multa FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0 ORDER BY sm_mod_actividades.fechaActividad ASC";
					$rs = $conexion->query($sql);
					while ($datos = $rs->fetch_assoc()) {
						$temaActividad = $datos['temaActividad'];
						$fechaActividad = $datos['fechaActividad'];
						$mTardanza = $datos['mTardanza'];
						$mFalta = $datos['mFalta'];
						$lotes = $datos['lotes'];
						$multa = $datos['multa'];
					
						$reporte.='
							<tr>
								<td class="textoCen">'.ceros($a,2).'</td>
								<td class="textoCen">'.$infoConcepto.'</td>
								<td class="textoIzq">'.$temaActividad.'</td>
								<td class="textoCen mayusculas">'.infoFecha($fechaActividad,'normal').'</td>
								<td class="textoCen">'.ceros($lotes,2).'</td>
								<td class="textoDer resaltar">S/. <strong>'.moneda($multa,2).'</strong></td>
							</tr>
						';

						$deudaASA=$deudaASA+$multa;
						$a++;
					}
			}else{ $deudaASA=0; }
		}

		if($concepto=='FAE'){
			if($cantidadRegistrosFAE>0){
				if($b>0){
					$b=$a;
				}else{
					$b=1;
				}
				$deudaFAE=0;
				$infoConcepto='FAENA';
				$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.fechaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta, sm_mod_asistencia.lotes, sm_mod_asistencia.multa FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0 ORDER BY sm_mod_actividades.fechaActividad ASC";
				$rs = $conexion->query($sql);
				while ($datos = $rs->fetch_assoc()) {
					$temaActividad = $datos['temaActividad'];
					$fechaActividad = $datos['fechaActividad'];
					$mTardanza = $datos['mTardanza'];
					$mFalta = $datos['mFalta'];
					$lotes = $datos['lotes'];
					$multa = $datos['multa'];
					
					$reporte.='
						<tr>
							<td class="textoCen">'.ceros($b,2).'</td>
							<td class="textoCen">'.$infoConcepto.'</td>
							<td class="textoIzq">'.$temaActividad.'</td>
							<td class="textoCen mayusculas">'.infoFecha($fechaActividad,'normal').'</td>
							<td class="textoCen">'.ceros($lotes,2).'</td>
							<td class="textoDer resaltar">S/. <strong>'.moneda($multa,2).'</strong></td>
						</tr>
					';

					$deudaFAE=$deudaFAE+$multa;
					$b++;
				}
			}else{ $deudaFAE=0; }
		}

		if($concepto=='CUO'){
			if($totalDeudaCUO>0){
				if($b>0){
					$c=$b;
				}else{
					$c=$a;
				}
				$deudaCUO=0;
				$infoConcepto='CUOTA';
				$sql="SELECT sm_mod_cuotas.conceptoCuota, sm_mod_cuotas.montoCuota, sm_mod_cuotas.fechaPago, sm_mod_cuotas_socios.lotes, sm_mod_cuotas_socios.montoPago FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0 ORDER BY sm_mod_cuotas.fechaPago ASC";
				$rs = $conexion->query($sql);
				while ($datos = $rs->fetch_assoc()) {
					$conceptoCuota = $datos['conceptoCuota'];
					$fechaPago = $datos['fechaPago'];
					$montoCuota = $datos['montoCuota'];
					$lotes = $datos['lotes'];
					$montoPago = $datos['montoPago'];
					
					$reporte.='
						<tr>
							<td class="textoCen">'.ceros($c,2).'</td>
							<td class="textoCen">'.$infoConcepto.'</td>
							<td class="textoIzq">'.$conceptoCuota.'</td>
							<td class="textoCen mayusculas">'.infoFecha($fechaPago,'normal').'</td>
							<td class="textoCen">'.ceros($lotes,2).'</td>
							<td class="textoDer resaltar">S/. <strong>'.moneda($montoPago,2).'</strong></td>
						</tr>
					';
			
					$deudaCUO=$deudaCUO+$montoPago;
					$c++;
				}
			}
		}

	$totalDeuda=$deudaASA+$deudaFAE+$deudaCUO;
	$reporte.='
		<tbody>
	</table>
	<br>
	<br>
	<h2>TOTAL DEUDAS <strong>S/.'.moneda($totalDeuda).'</strong></h2><br><hr>
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
	$dompdf->stream("reporte-deudas-socio-".$codigoSocio.".pdf");
?>