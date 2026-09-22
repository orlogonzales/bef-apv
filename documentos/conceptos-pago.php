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
	
	$conexion =conexionDB();
	$concepto =$_GET[concepto];

	if($concepto=="ASA"){
		$rotuloTitulo       ="ASAMBLEAS";
		$titulolista        ="DETALLE DE ASAMBLEA";
		$cantidadASAFAE     =infoActividad($idJuntaDirectiva,$codigoActividad,'','cantidadASA');
		$totalGlobalASA     =infoActividad($idJuntaDirectiva,'','','totalGlobalASA');
		$totalGlobalASA_JUS =infoActividad($idJuntaDirectiva,'','','totalGlobalASA_JUS');
		$totalGlobalASA_SP  =infoActividad($idJuntaDirectiva,'','','totalGlobalASA_SP');
		$totalGlobalASA_FP  =infoActividad($idJuntaDirectiva,'','','totalGlobalASA_FP');
		$totalGlobalASA_PGD =$totalGlobalASA_SP+$totalGlobalASA_FP;
		$totalGlobalASA_DEU =$totalGlobalASA-($totalGlobalASA_PGD+$totalGlobalASA_JUS);
		
		if($totalGlobalASA>0){ $infoTotalGlobal='S/. '.moneda($totalGlobalASA); }else{ $infoTotalGlobal='--'; }
		if($totalGlobalASA_JUS>0){ $infoTotalGlobal_JUS='- S/. '.moneda($totalGlobalASA_JUS); }else{ $infoTotalGlobal_JUS='--'; }
		if($totalGlobalASA_PGD>0){ $infoTotalGlobal_PGD='- S/. '.moneda($totalGlobalASA_PGD); }else{ $infoTotalGlobal_PGD='--'; }
		if($totalGlobalASA_DEU>0){ $infoTotalGlobal_DEU='S/. '.moneda($totalGlobalASA_DEU); }else{ $infoTotalGlobal_DEU='--'; }
	}

	if($concepto=="FAE"){
		$rotuloTitulo       ="FAENAS";
		$titulolista        ="DETALLE DE ASAMBLEA";
		$cantidadASAFAE     =infoActividad($idJuntaDirectiva,$codigoActividad,'','cantidadFAE');
		
		$totalGlobalFAE     =infoActividad($idJuntaDirectiva,'','','totalGlobalFAE');
		$totalGlobalFAE_JUS =infoActividad($idJuntaDirectiva,'','','totalGlobalFAE_JUS');
		$totalGlobalFAE_SP  =infoActividad($idJuntaDirectiva,'','','totalGlobalFAE_SP');
		$totalGlobalFAE_FP  =infoActividad($idJuntaDirectiva,'','','totalGlobalFAE_FP');
		$totalGlobalFAE_PGD =$totalGlobalFAE_SP+$totalGlobalFAE_FP;
		$totalGlobalFAE_DEU =$totalGlobalFAE-($totalGlobalFAE_PGD+$totalGlobalFAE_JUS);
		
		if($totalGlobalFAE>0){ $infoTotalGlobal='S/. '.moneda($totalGlobalFAE); }else{ $infoTotalGlobal='--'; }
		if($totalGlobalFAE_JUS>0){ $infoTotalGlobal_JUS='- S/. '.moneda($totalGlobalFAE_JUS); }else{ $infoTotalGlobal_JUS='--'; }
		if($totalGlobalFAE_PGD>0){ $infoTotalGlobal_PGD='- S/. '.moneda($totalGlobalFAE_PGD); }else{ $infoTotalGlobal_PGD='--'; }
		if($totalGlobalFAE_DEU>0){ $infoTotalGlobal_DEU='S/. '.moneda($totalGlobalFAE_DEU); }else{ $infoTotalGlobal_DEU='--'; }
	}

	if($concepto=="CUO"){
		$rotuloTitulo ="CUOTAS";
		$titulolista  ="DETALLE DE CUOTAS";
		$cantidadCUO  =infoCuota($idJuntaDirectiva,$codigoCuota,'cantidadCuotas');

		$totalGlobalCUO     =infoCuota($idJuntaDirectiva,'','totalGlobalCUO');
		$totalGlobalCUO_SP  =infoCuota($idJuntaDirectiva,'','totalGlobalCUO_SP');
		$totalGlobalCUO_FP  =infoCuota($idJuntaDirectiva,'','totalGlobalCUO_FP');
		$totalGlobalCUO_PGD =$totalGlobalCUO_SP+$totalGlobalCUO_FP;
		$totalGlobalCUO_DEU =$totalGlobalCUO-$totalGlobalCUO_PGD;

		if($totalGlobalCUO>0){ $infoTotalGlobalCUO='S/. '.moneda($totalGlobalCUO); }else{ $infoTotalGlobalCUO='--'; }
		if($totalGlobalCUO_PGD>0){ $infoTotalGlobalCUO_PGD='- S/. '.moneda($totalGlobalCUO_PGD); }else{ $infoTotalGlobalCUO_PGD='--'; }
		if($totalGlobalCUO_DEU>0){ $infoTotalGlobalCUO_DEU='S/. '.moneda($totalGlobalCUO_DEU); }else{ $infoTotalGlobalCUO_DEU='--'; }
	}
	
	$hoy                    =fechaSQL(infoTiempo('fechaHoy'));
	$hora                   =infoTiempo('hora');
	$dniUsuario             =$_SESSION['dni_apv'];	
	$subtitulo              ="CONSULTA GENERADA DE [".$fechaInicio."] HASTA [".$fechaFin."]";
	$impresoPor             ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';

	$titulo  ="REPORTE ECONOMICO DE ".$rotuloTitulo;
	$proceso ="IMPRESION DE <strong>".$titulo."</strong>";
	$sql     ="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs      =mysqli_query($conexion,$sql);

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
	';
	
	if($concepto=="ASA" OR $concepto=="FAE"){
		if($cantidadASAFAE>0){
			$reporte.='
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td class="resaltar"><strong>TOTAL:</strong><br>'.$infoTotalGlobal.'</td>
							<td class="resaltar"><strong>JUSTIFICADOS:</strong><br>'.$infoTotalGlobal_JUS.'</td>
							<td class="resaltar"><strong>PAGADOS:</strong><br>'.$infoTotalGlobal_PGD.'</td>
							<td class="resaltar"><strong>POR PAGAR:</strong><br>'.$infoTotalGlobal_DEU.'</td>
						</tr>
					</table>
					<br>
			';
		}else{ $reporte.=''; }
	}

	if($concepto=="CUO"){
		if($cantidadCUO>0){
			$reporte.='
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr>
							<td class="resaltar"><strong>TOTAL:</strong><br>'.$infoTotalGlobalCUO.'</td>
							<td class="resaltar"><strong>PAGADOS:</strong><br>'.$infoTotalGlobalCUO_PGD.'</td>
							<td class="resaltar"><strong>POR PAGAR:</strong><br>'.$infoTotalGlobalCUO_DEU.'</td>
						</tr>
					</table>
					<br>
			';
		}else{ $reporte.=''; }
	}

	if($concepto=="ASA" OR $concepto=="FAE"){
		if($cantidadASAFAE>0){
			$reporte.='
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th class="textoCen">#</th>
							<th class="textoIzq">'.$titulolista.'</th>
							<th class="textoCen">FECHA</th>
							<th class="textoCen">AFORO</th>
							<th class="textoDer">NETO</th>
							<th class="textoDer">JUSTI</th>
							<th class="textoDer">TOTAL</th>
							<th class="textoDer">CAJA</th>
							<th class="textoDer">DEBEN</th>
							<th class="textoDer">PAG</th>
							<th class="textoDer">PRO</th>
							<th class="textoDer">DEB</th>
						</tr>
					</thead>
					<tbody>
			';
		}else{ $reporte.=''; }
	}

	if($concepto=="CUO"){
		if($cantidadCUO>0){
			$reporte.='
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<thead>
						<tr>
							<th class="textoCen">#</th>
							<th class="textoIzq">'.$titulolista.'</th>
							<th class="textoCen">FECHA</th>
							<th class="textoCen">AFORO</th>
							<th class="textoDer">TOTAL</th>
							<th class="textoDer">CAJA</th>
							<th class="textoDer">DEBEN</th>
							<th class="textoDer">PAG</th>
							<th class="textoDer">PRO</th>
							<th class="textoDer">DEB</th>
						</tr>
					</thead>
					<tbody>
			';
		}else{ $reporte.=''; }
	}

	if($concepto=="ASA"){
		if($cantidadASAFAE>0){
			$sql="SELECT tipoActividad, codigoActividad, temaActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta FROM sm_mod_actividades WHERE tipoActividad='$concepto' ORDER BY id DESC";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoActividad          =$n[codigoActividad];
				$tipoActividad            =$n[tipoActividad];
				$temaActividad            =$n[temaActividad]; 
				$fechaActividad           =$n[fechaActividad]; 
				$horaActividad            =$n[horaActividad]; 
				$lugarActividad           =$n[lugarActividad]; 
				$aforo                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','aforo');
				$asistio                  =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
				$tarde                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','tarde');
				$falto                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','falto');
				$justifico                =infoActividad($idJuntaDirectiva,$codigoActividad,'','justifico');
				$mTardanzas               =infoActividad($idJuntaDirectiva,$codigoActividad,'','mtarde');
				$mFaltas                  =infoActividad($idJuntaDirectiva,$codigoActividad,'','mFalta');
				$totalMultas              =$mTardanzas+$mFaltas;
				$multasPagadas            =infoActividad($idJuntaDirectiva,$codigoActividad,'','mPagadas');
				$porPagar                 =$totalMultas-$multasPagadas;
				$montoJustificado         =infoActividad($idJuntaDirectiva,$codigoActividad,'','mJUS');
				$totalNeto                =$montoJustificado+$totalMultas;
				$nroSociosPagaron         =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosPagaron');
				$nroSociosFechasProgramas =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosFechasProgramas');
				$nroSociosDeben           =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosDeben');

				if($aforo>0){ $infoAforo=$aforo.' SOC'; }else{  $infoAforo='--'; }
				if($mTardanzas>0){ $infoMTardanzas="S/. ".moneda($mTardanzas); }else{  $infoMTardanzas='--'; }
				if($mFaltas>0){ $infoMFaltas="S/. ".moneda($mFaltas); }else{  $infoMFaltas='--'; }
				if($totalMultas>0){ $infoTotalMultas='S/. '.moneda($totalMultas); }else{ $infoTotalMultas='--'; }
		 		if($multasPagadas>0){ $infoMultasPagadas='S/. '.moneda($multasPagadas); }else{ $infoMultasPagadas='--'; }
				if($porPagar>0){ $infoPorPagar='S/. '.moneda($porPagar); }else{ $infoPorPagar='--'; }
				if($montoJustificado>0){ $infoMontoJustificado="- S/. ".moneda($montoJustificado); }else{ $infoMontoJustificado='--'; }
				if($totalNeto>0){ $infoTotalNeto="S/. ".moneda($totalNeto); }else{  $infoTotalNeto='--'; }
				if($nroSociosPagaron>0){ $infoNroSociosPagaron=$nroSociosPagaron.' SOC'; }else{ $infoNroSociosPagaron='--'; }
				if($nroSociosFechasProgramas>0){ $infoNroSociosFechasProgramas=$nroSociosFechasProgramas.' SOC'; }else{ $infoNroSociosFechasProgramas='--'; }
				if($nroSociosDeben>0){ $infoNroSociosDeben=$nroSociosDeben.' SOC'; }else{ $infoNroSociosDeben='--'; }

				$reporte.='
					<tr>
						<td class="textoCen">'.ceros($i,2).'</td>
						<td class="textoIzq" width="45%">'.texto($temaActividad).'</td>
						<td class="textoCen textoMayuscula">'.infoFecha($fechaActividad,'normal').'</td>
						<td class="textoCen">'.$infoAforo.'</td>
						<td class="textoDer">'.$infoTotalNeto.'</td>
						<td class="textoDer">'.$infoMontoJustificado.'</td>
						<td class="textoDer">'.$infoTotalMultas.'</td>
						<td class="textoDer">'.$infoMultasPagadas.'</td>
						<td class="textoDer">'.$infoPorPagar.'</td>
						<td class="textoDer">'.$infoNroSociosPagaron.'</td>
						<td class="textoDer">'.$infoNroSociosFechasProgramas.'</td>
						<td class="textoDer">'.$infoNroSociosDeben.'</td>
					</tr>
				';
				$i++;
			}
		}else{
			$reporte.='<h2 class="textoCen">SIN ASAMBLEAS</h2><p class="textoCen">NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN CONCEPTO DE '.$rotuloTitulo.'</p>';
		}
	}

	if($concepto=="FAE"){
		if($cantidadASAFAE>0){
			$sql="SELECT tipoActividad, codigoActividad, temaActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta FROM sm_mod_actividades WHERE tipoActividad='$concepto' ORDER BY id DESC";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoActividad          =$n[codigoActividad];
				$tipoActividad            =$n[tipoActividad];
				$temaActividad            =$n[temaActividad]; 
				$fechaActividad           =$n[fechaActividad]; 
				$horaActividad            =$n[horaActividad]; 
				$lugarActividad           =$n[lugarActividad]; 
				$aforo                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','aforo');
				$asistio                  =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
				$tarde                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','tarde');
				$falto                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','falto');
				$justifico                =infoActividad($idJuntaDirectiva,$codigoActividad,'','justifico');
				$mTardanzas               =infoActividad($idJuntaDirectiva,$codigoActividad,'','mtarde');
				$mFaltas                  =infoActividad($idJuntaDirectiva,$codigoActividad,'','mFalta');
				$totalMultas              =$mTardanzas+$mFaltas;
				$multasPagadas            =infoActividad($idJuntaDirectiva,$codigoActividad,'','mPagadas');
				$porPagar                 =$totalMultas-$multasPagadas;
				$montoJustificado         =infoActividad($idJuntaDirectiva,$codigoActividad,'','mJUS');
				$totalNeto                =$montoJustificado+$totalMultas;
				$nroSociosPagaron         =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosPagaron');
				$nroSociosFechasProgramas =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosFechasProgramas');
				$nroSociosDeben           =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosDeben');

				if($aforo>0){ $infoAforo=$aforo.' SOC'; }else{  $infoAforo='--'; }
				if($mTardanzas>0){ $infoMTardanzas="S/. ".moneda($mTardanzas); }else{  $infoMTardanzas='--'; }
				if($mFaltas>0){ $infoMFaltas="S/. ".moneda($mFaltas); }else{  $infoMFaltas='--'; }
				if($totalMultas>0){ $infoTotalMultas='S/. '.moneda($totalMultas); }else{ $infoTotalMultas='--'; }
		 		if($multasPagadas>0){ $infoMultasPagadas='S/. '.moneda($multasPagadas); }else{ $infoMultasPagadas='--'; }
				if($porPagar>0){ $infoPorPagar='S/. '.moneda($porPagar); }else{ $infoPorPagar='--'; }
				if($montoJustificado>0){ $infoMontoJustificado="- S/. ".moneda($montoJustificado); }else{ $infoMontoJustificado='--'; }
				if($totalNeto>0){ $infoTotalNeto="S/. ".moneda($totalNeto); }else{  $infoTotalNeto='--'; }
				if($nroSociosPagaron>0){ $infoNroSociosPagaron=$nroSociosPagaron.' SOC'; }else{ $infoNroSociosPagaron='--'; }
				if($nroSociosFechasProgramas>0){ $infoNroSociosFechasProgramas=$nroSociosFechasProgramas.' SOC'; }else{ $infoNroSociosFechasProgramas='--'; }
				if($nroSociosDeben>0){ $infoNroSociosDeben=$nroSociosDeben.' SOC'; }else{ $infoNroSociosDeben='--'; }

				$reporte.='
					<tr>
						<td class="textoCen">'.ceros($i,2).'</td>
						<td class="textoIzq" width="45%">'.texto($temaActividad).'</td>
						<td class="textoCen textoMayuscula">'.infoFecha($fechaActividad,'normal').'</td>
						<td class="textoCen">'.$infoAforo.'</td>
						<td class="textoDer">'.$infoTotalNeto.'</td>
						<td class="textoDer">'.$infoMontoJustificado.'</td>
						<td class="textoDer">'.$infoTotalMultas.'</td>
						<td class="textoDer">'.$infoMultasPagadas.'</td>
						<td class="textoDer">'.$infoPorPagar.'</td>
						<td class="textoDer">'.$infoNroSociosPagaron.'</td>
						<td class="textoDer">'.$infoNroSociosFechasProgramas.'</td>
						<td class="textoDer">'.$infoNroSociosDeben.'</td>
					</tr>
				';
				$i++;
			}
		}else{
			$reporte.='<h2 class="textoCen">SIN FAENAS</h2><p class="textoCen">NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN CONCEPTO DE '.$rotuloTitulo.'</p>';
		}
	}

	if($concepto=="CUO"){
		if($cantidadCUO>0){
			$sql="SELECT codigoCuota, conceptoCuota, montoCuota, fechaPago FROM sm_mod_cuotas ORDER BY id DESC";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoCuota   =$n[codigoCuota];
				$temaActividad =$n[conceptoCuota];
				$fechaPago     =$n[fechaPago];
				$aforo         =infoCuota($idJuntaDirectiva,$codigoCuota,'aforo');
				$pagaronFP     =infoCuota($idJuntaDirectiva,$codigoCuota,'totalFechasProgramadas');
				$pagaron       =infoCuota($idJuntaDirectiva,$codigoCuota,'pagaron');
				$programados   =infoCuota($idJuntaDirectiva,$codigoCuota,'fechasProgramadas');
				$deben         =infoCuota($idJuntaDirectiva,$codigoCuota,'deben');
				$totalCuotas   =infoCuota($idJuntaDirectiva,$codigoCuota,'totalCuotas');
				$totalPagados  =infoCuota($idJuntaDirectiva,$codigoCuota,'totalPagados');
				$totalDeben    =infoCuota($idJuntaDirectiva,$codigoCuota,'totalDeben');
				$totalPorpagar =$totalCuotas-($totalPagados+$pagaronFP);

				if($totalCuotas>0){ $infoTotalCuotas='S/.'.moneda($totalCuotas); }else{ $infoTotalCuotas='--'; }
				if($totalPagados>0){ $infoTotalPagados='S/.'.moneda($totalPagados+$pagaronFP); }else{ $infoTotalPagados='--'; }
				if($totalPorpagar>0){ $infoTotalPorpagar='S/.'.moneda($totalPorpagar); }else{ $infoTotalPorpagar='--'; }

				if($aforo>0){ $infoAforo=$aforo; }else{ $infoAforo='<i class="fa fa-ellipsis-h"></i>'; }
				if($pagaron>0){ $infoNroSociosPagaron=$pagaron; } else{ $infoNroSociosPagaron='<i class="fa fa-ellipsis-h"></i>'; }
				if($programados>0){ $infoNroSociosFechasProgramas=$programados; } else{ $infoNroSociosFechasProgramas='<i class="fa fa-ellipsis-h"></i>'; }
				if($deben>0){ $infoNroSociosDeben=$deben; } else{ $infoNroSociosDeben='<i class="fa fa-ellipsis-h"></i>'; }

				$reporte.='
					<tr>
						<td class="textoCen">'.ceros($i,2).'</td>
						<td class="textoIzq" width="55%">'.texto($temaActividad).'</td>
						<td class="textoCen textoMayuscula">'.infoFecha($fechaPago,'normal').'</td>
						<td class="textoCen">'.$infoAforo.'</td>
						<td class="textoDer">'.$infoTotalCuotas.'</td>
						<td class="textoDer">'.$infoTotalPagados.'</td>
						<td class="textoDer">'.$infoTotalPorpagar.'</td>
						<td class="textoDer">'.$infoNroSociosPagaron.'</td>
						<td class="textoDer">'.$infoNroSociosFechasProgramas.'</td>
						<td class="textoDer">'.$infoNroSociosDeben.'</td>
					</tr>
				';
				$i++;
			}
		}else{
			$reporte.='<h2 class="textoCen">SIN CUOTAS</h2><p class="textoCen">NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN CONCEPTO DE '.$rotuloTitulo.'</p>';
		}
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
	$dompdf->stream("reporte-economico-actividades.pdf");
?>