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
	
    $conexion   = conexionDB();
    $idJuntaDirectiva=$_GET['idJuntaDirectiva'];
    $concepto=$_GET['concepto'];
    $sector=$_GET['sector'];
    $manzana=$_GET['manzana'];
    $ordenar=$_GET['ordenar'];
    
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

    if($concepto=='ASA'){
        $infoConcepto='ASAMBLEA';
    }

    if($concepto=='FAE'){
        $infoConcepto='FAENA';
    }

    if($concepto=='CUO'){
        $infoConcepto='CUOTA';
    }

    if($concepto=='ALL'){
        $infoConcepto='TODOS';
    }

	$hoy        = fechaSQL(infoTiempo('fechaHoy'));
	$hora       = infoTiempo('hora');
	$dniUsuario = $_SESSION['dni_apv'];
	$impresoPor = '<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$totalDeudas= 0;

	if($ordenar=='LOTE'){
        $orden="CAST(SUBSTRING_INDEX(REPLACE(sm_lotes_socio.manzana, '-', '.'), '.', -1) AS UNSIGNED) ASC, CAST(SUBSTRING_INDEX(sm_lotes_socio.lote, '-', -1) AS UNSIGNED) ASC";
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

	$reporte.='<h1>REPORTE DE SOCIOS CON DEUDAS</h1>';
	$reporte.='<h2 class="mayusculas"><strong>JUNTA DIRECTIVA:</strong> '.$infoJD.'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<strong>CONCEPTO:</strong> '.$infoConcepto.' &nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<strong>SECTOR:</strong> '.$infoSector.'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<strong>MANZANA:</strong> '.$infoManzana.'</h2><br>';

	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<thead>
			<tr>
				<th class="textoCen">#</th>
				<th class="textoCen">S</th>
				<th class="textoCen">M</th>
				<th class="textoCen">L</th>
				<th class="textoCen">CODIGO</th>
				<th class="textoIzq">NOMBRE SOCIO</th>
				<th class="textoCen">LOTES</th>
				<th class="textoCen">CONCEPTO</th>
				<th class="textoDer">DEUDA</th>
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

		if($concepto=='ASA' || $concepto=='FAE'){
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
            }

            $query="SELECT SUM(sm_mod_asistencia.multa) AS totalDeuda, COUNT(sm_mod_asistencia.id) AS cantidadRegistros FROM sm_mod_asistencia, sm_mod_actividades WHERE $consultaJD sm_mod_asistencia.tipoActividad = '$concepto' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistros=$dato['cantidadRegistros'];
            $totalDeuda=$dato['totalDeuda'];
        }

        if($concepto=='CUO'){
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='idJuntaDirectiva='.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(id) AS cantidadRegistros, SUM(montoCuota) AS totalDeuda  FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' AND montoPago>0 AND estadoPago='NP' AND caja!='SI' AND montoPagado=0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistros=$dato['cantidadRegistros'];
            $totalDeuda=$dato['totalDeuda'];
        }

        if($concepto=='ALL'){
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

            $cantidadRegistros=$cantidadRegistrosASA+$cantidadRegistrosFAE+$cantidadRegistrosCUO;
            $totalDeuda=$totalDeudaASA+$totalDeudaFAE+$totalDeudaCUO;
        }

        if($cantidadRegistros>0 && $totalDeuda>0){
        	if($cantidadRegistros>1){
                if($concepto=='CUO'){
                    $etiquetaConcepto='CUOTAS';
                }
                if($concepto=='ASA'){
                    $etiquetaConcepto='ASAMBLEAS';
                }
                if($concepto=='FAE'){
                    $etiquetaConcepto='FAENAS';
                }
                if($concepto=='ALL'){
                    $etiquetaConcepto='CONCEPTOS';
                }
            }else{
                if($concepto=='CUO'){
                    $etiquetaConcepto='CUOTA';
                }
                if($concepto=='ASA'){
                    $etiquetaConcepto='ASAMBLEA';
                }
                if($concepto=='FAE'){
                    $etiquetaConcepto='FAENA';
                }
                if($concepto=='ALL'){
                    $etiquetaConcepto='CONCEPTO';
                }
            }
        	$infoMontoDeuda='S/.'.moneda($totalDeuda);
        	$infoConcepto = ceros($cantidadRegistros,2).' '.$etiquetaConcepto;
			$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,4).'</td>
					<td class="textoCen">'.$sector.'</td>
					<td class="textoCen">'.$manzana.'</td>
					<td class="textoCen">'.$lote.'</td>
					<td class="textoCen">'.$codigoSocio.'</td>
					<td class="textoIzq resaltar">'.texto($nombreSocio).'</td>
					<td class="textoCen">'.ceros($cantidadLotes,2).'</td>
					<td class="textoCen">'.$infoConcepto.'</td>
					<td class="textoDer resaltar">'.$infoMontoDeuda.'</td>
				</tr>
			';

			$totalDeudas=$totalDeudas+$totalDeuda;
			$i++;
		}	
	}
	$cantidadRegistros=0;
	$reporte.='
		<tbody>
	</table>
	<br>
	<br>
	<h2>TOTAL DEUDAS <strong>S/.'.moneda($totalDeudas).'</strong></h2><br><hr>
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
	$dompdf->stream("reporte-deudas-socios-sector-".$rotuloSector."-manzana-".$rotuloManzana.".pdf");
?>