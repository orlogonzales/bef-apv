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
	$codigoSocio     =$_GET[codigoSocio];
	$nombreSocio     =infoSocios($codigoSocio,'nombre');
	$lotesSocio      =infoSocios($codigoSocio,'cantidadLotes');
	$totalDeudas     =infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasPagadas');
	$totalPagado     =infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasSaldo');
	$hoy             =fechaSQL(infoTiempo('fechaHoy'));
	$hora            =infoTiempo('hora');
	$dniUsuario      =$_SESSION['dni_apv'];
	$rutaCB          ='../assets/images/codigo-barra/';
	$fileCB          =$codigoSocio.'.png';
	$codBar          =$rutaCB.$fileCB;
	$deudasAsambleas =infoSocios($codigoSocio,'deudasAsambleas');
	$pagosAsambleas  =infoPago($codigoSocio,'','','pagosAsambleas');	
	$deudasFaenas    =infoSocios($codigoSocio,'deudasFaenas');
	$pagosFaenas     =infoPago($codigoSocio,'','','pagosFaenas');
	$deudasCuotas    =infoSocios($codigoSocio,'deudasCuotas');
	$pagosCuotas     =infoPago($codigoSocio,'','','pagosCuotas');
	$impresoPor      ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	$infoCodigo      ='<small>CODIGO: '.$codigoConcepto.'</small>';
	
	
	if($pagosAsambleas>0){ $infoPagosAsambleas='S/. '.moneda($pagosAsambleas); }else{ $infoPagosAsambleas='--'; }
	if($pagosFaenas>0){ $infoPagosFaenas='S/. '.moneda($pagosFaenas); }else{ $infoPagosFaenas='--'; }
	if($pagosCuotas>0){ $infoPagosCuotas='S/. '.moneda($pagosCuotas); }else{ $infoPagosCuotas='--'; }

	$asambleas   =infoSocios($codigoSocio,'nroAsambleas');
	$faenas      =infoSocios($codigoSocio,'nroFaenas');
	$cuotas      =infoSocios($codigoSocio,'nroCuotas');

	// ESTADISTICAS ASAMBLEAS
	$total_ASA_DEU_PAG=infoSocios($codigoSocio,'total_ASA_DEU_PAG');
	$pagos_ASA_efectivos=infoSocios($codigoSocio,'pagosAsambleas');
	$pagos_ASA_programados=infoSocios($codigoSocio,'pagosAsambleas_SI_MP');
	$total_ASA_justificados=infoSocios($codigoSocio,'total_ASA_justificados');
	$total_ASA_saldo=(infoSocios($codigoSocio,'porPagarAsambleas_NO_MP'))+(infoSocios($codigoSocio,'porPagarAsambleas_SI_MP'));

	if($total_ASA_DEU_PAG>0){ $info_total_ASA_DEU_PAG='S/. '.moneda($total_ASA_DEU_PAG); }else{ $info_total_ASA_DEU_PAG='--'; }
	if($pagos_ASA_efectivos>0){ $info_pagos_ASA_efectivos='- S/. '.moneda($pagos_ASA_efectivos); }else{ $info_pagos_ASA_efectivos='--'; }
	if($pagos_ASA_programados>0){ $info_pagos_ASA_programados='- S/. '.moneda($pagos_ASA_programados); }else{ $info_pagos_ASA_programados='--'; }
	if($total_ASA_justificados>0){ $info_total_ASA_justificados='- S/. '.moneda($total_ASA_justificados); }else{ $info_total_ASA_justificados='--'; }
	if($total_ASA_saldo>0){ $info_total_ASA_saldo='S/. '.moneda($total_ASA_saldo); }else{ $info_total_ASA_saldo='--'; }

	// ESTADISTICAS FAENAS
	$total_FAE_DEU_PAG=infoSocios($codigoSocio,'total_FAE_DEU_PAG');
	$pagos_FAE_efectivos=infoSocios($codigoSocio,'pagosFaenas');
	$pagos_FAE_programados=infoSocios($codigoSocio,'pagosFaenas_SI_MP');
	$total_FAE_justificados=infoSocios($codigoSocio,'total_FAE_justificados');
	$total_FAE_saldo=(infoSocios($codigoSocio,'porPagarFaenas_NO_MP'))+(infoSocios($codigoSocio,'porPagarFaenas_SI_MP'));

	if($total_FAE_DEU_PAG>0){ $info_total_FAE_DEU_PAG='S/. '.moneda($total_FAE_DEU_PAG); }else{ $info_total_FAE_DEU_PAG='--'; }
	if($pagos_FAE_efectivos>0){ $info_pagos_FAE_efectivos='- S/. '.moneda($pagos_FAE_efectivos); }else{ $info_pagos_FAE_efectivos='--'; }
	if($pagos_FAE_programados>0){ $info_pagos_FAE_programados='- S/. '.moneda($pagos_FAE_programados); }else{ $info_pagos_FAE_programados='--'; }
	if($total_FAE_justificados>0){ $info_total_FAE_justificados='- S/. '.moneda($total_FAE_justificados); }else{ $info_total_FAE_justificados='--'; }
	if($total_FAE_saldo>0){ $info_total_FAE_saldo='S/. '.moneda($total_FAE_saldo); }else{ $info_total_FAE_saldo='--'; }

	// ESTADISTICAS CUOTAS
	$total_CUO_DEU_PAG=infoSocios($codigoSocio,'total_CUO_DEU_PAG');
	$pagos_CUO_efectivos=infoSocios($codigoSocio,'pagosCuotas');
	$pagos_CUO_programados=infoSocios($codigoSocio,'pagosCuotas_SI_MP');
	$total_CUO_justificados=0;
	$total_CUO_saldo=(infoSocios($codigoSocio,'porPagarCuotas_NO_MP'))+(infoSocios($codigoSocio,'porPagarCuotas_SI_MP'));

	if($total_CUO_DEU_PAG>0){ $info_total_CUO_DEU_PAG='S/. '.moneda($total_CUO_DEU_PAG); }else{ $info_total_CUO_DEU_PAG='--'; }
	if($pagos_CUO_efectivos>0){ $info_pagos_CUO_efectivos='- S/. '.moneda($pagos_CUO_efectivos); }else{ $info_pagos_CUO_efectivos='--'; }
	if($pagos_CUO_programados>0){ $info_pagos_CUO_programados='- S/. '.moneda($pagos_CUO_programados); }else{ $info_pagos_CUO_programados='--'; }
	if($total_CUO_justificados>0){ $info_total_CUO_justificados='- S/. '.moneda($total_CUO_justificados); }else{ $info_total_CUO_justificados='--'; }
	if($total_CUO_saldo>0){ $info_total_CUO_saldo='S/. '.moneda($total_CUO_saldo); }else{ $info_total_CUO_saldo='--'; }

	$totalGeneralDeudas=$total_ASA_DEU_PAG+$total_FAE_DEU_PAG+$total_CUO_DEU_PAG;
	$totalDeudas = $total_ASA_saldo+$total_FAE_saldo+$total_CUO_saldo;

	if($totalDeudas>0){ $infoTotalDeudas='S/. '.moneda($totalDeudas); }else{ $infoTotalDeudas='--'; }
	if($totalGeneralDeudas>0){ $infoTotalGeneralDeudas='S/. '.moneda($totalGeneralDeudas); }else{ $infoTotalGeneralDeudas='--'; }

	// ESTADISTICAS EXONERACIONES
	$totalExoneradoSocio=infoExoneraciones($codigoSocio,'','totalExoneradoSocio');
	$totalExoneradoSocioASA=infoExoneraciones($codigoSocio,'','totalExoneradoSocioASA');
	$totalExoneradoSocioFAE=infoExoneraciones($codigoSocio,'','totalExoneradoSocioFAE');
	$totalExoneradoSocioCUO=infoExoneraciones($codigoSocio,'','totalExoneradoSocioCUO');

	if($totalExoneradoSocio>0){ $infoTotalExoneradoSocio='- S/. '.moneda($totalExoneradoSocio); }else{ $infoTotalExoneradoSocio='--'; }
	if($totalExoneradoSocioASA>0){ $infoTotalExoneradoSocioASA='- S/. '.moneda($totalExoneradoSocioASA); }else{ $infoTotalExoneradoSocioASA='--'; }
	if($totalExoneradoSocioFAE>0){ $infoTotalExoneradoSocioFAE='- S/. '.moneda($totalExoneradoSocioFAE); }else{ $infoTotalExoneradoSocioFAE='--'; }
	if($totalExoneradoSocioCUO>0){ $infoTotalExoneradoSocioCUO='- S/. '.moneda($totalExoneradoSocioCUO); }else{ $infoTotalExoneradoSocioCUO='--'; }

	$totalPagosExoneradoSocio=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocio');
	$totalPagosExoneradoSocioASA=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioASA');
	$totalPagosExoneradoSocioFAE=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioFAE');
	$totalPagosExoneradoSocioCUO=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioCUO');

	if($totalPagosExoneradoSocio>0){ $infoTotalPagosExoneradoSocio='S/. '.moneda($totalPagosExoneradoSocio); }else{ $infoTotalPagosExoneradoSocio='--'; }
	if($totalPagosExoneradoSocioASA>0){ $infoTotalPagosExoneradoSocioASA='S/. '.moneda($totalPagosExoneradoSocioASA); }else{ $infoTotalPagosExoneradoSocioASA='--'; }
	if($totalPagosExoneradoSocioFAE>0){ $infoTotalPagosExoneradoSocioFAE='S/. '.moneda($totalPagosExoneradoSocioFAE); }else{ $infoTotalPagosExoneradoSocioFAE='--'; }
	if($totalPagosExoneradoSocioCUO>0){ $infoTotalPagosExoneradoSocioCUO='S/. '.moneda($totalPagosExoneradoSocioCUO); }else{ $infoTotalPagosExoneradoSocioCUO='--'; }


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

	$reporte.='
		<h1>DEUDAS DE SOCIO</h1>
		<div class="codigobarra"><img src="'.$rutaCB.$fileCB.'"></div>
		<div class="infoCodigobarra">CODIGO SOCIO. '.$codigoSocio.'</div>
		'.$detalles
	;

	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="resaltar"><strong>CODIGO SOCIO:</strong><br>'.$codigoSocio.'</td>
				<td class="resaltar"><strong>NOMBRE SOCIO:</strong><br>'.texto($nombreSocio).'</td>
				<td class="resaltar"><strong>LOTES:</strong><br>'.ceros($lotesSocio,2).' LOTES</td>
			</tr>
		</table>
		<div class="s-30"></div>
	';

	// LISTA DE LOTES EN PROPIEDAD
	if($lotesSocio>0){
		$reporte.='
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="textoCen">#</th>
						<th class="textoCen">CODIGO</th>
						<th class="textoCen">SECTOR</th>
						<th class="textoCen">MANZANA</th>
						<th class="textoCen">LOTE</th>
						<th class="textoIzq">DIRECCION</th>
					</tr>
				</thead>
				<tbody>
		';

		$sql="SELECT  lotes, sector, manzana, lote, codigoLote, direccion FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio' ORDER BY id ASC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$lotes=$n[lotes];
			$sector=$n[sector];
			$manzana=$n[manzana];
			$lote=$n[lote];
			$codigoLote=$n[codigoLote];
			$direccion=$n[direccion];

			if($direccion!=''){ $infoDireccion=$direccion; }else{ $infoDireccion='DIRECCION AUN NO REGISTRADA EN LA BASE DE DATOS'; }

			$reporte.='
					<tr>
						<td class="textoCen">'.ceros($i,2).'</td>
						<td class="textoCen">'.$codigoLote.'</td>
						<td class="textoCen">'.$sector.'</td>
						<td class="textoCen">'.$manzana.'</td>
						<td class="textoCen">'.$lote.'</td>
						<td class="textoIzq">'.$infoDireccion.'</td>
					</tr>
			';
			$i++;
		}

		$reporte.='
				</tbody>
			</table>
			<div class="s-20"></div>
		';
	}

	if($totalDeudas>0){
		// REPORTE DE DEUDAS POR ASAMBLEAS
		if($deudasAsambleas>0){
			$reporte.='
				<h1>DEUDAS POR ASAMBLEAS</h1>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="textoCen">#</th>
						<th width="35%" class="textoCen">ASAMBLEA</th>
						<th class="textoCen">MULTA</th>
						<th class="textoCen">LOTES</th>
						<th class="textoCen">RAZON</th>
						<th class="textoCen">MONTO</th>
						<th class="textoCen">ESTADO</th>
					</tr>
				</thead>
				<tbody>
			';

			$sql="SELECT codigoActividad, lotes, asistio, retraso, multa,estadoPago FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND codigoSocio='$codigoSocio' AND (estadoPago='NP' OR estadoPago='MP')";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoActividad =$n[codigoActividad];
				$lotes           =$n[lotes];
				$asistio         =$n[asistio];
				$retraso         =$n[retraso];
				$multa           =$n[multa];
				$estadoPago      =$n[estadoPago];
				$actividad       =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
				$mTarde          =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
				$mFalta          =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');

				if($asistio=="NO"){ $razon="INASISTENCIA"; $infomulta='S/.&nbsp;'.moneda($mFalta); }
				if($asistio=="SI" AND $retraso>0.15){ $razon="TARDANZA"; $infomulta='S/.&nbsp;'.moneda($mTarde); }
				if($estadoPago=="NP"){ $infoEstado="DEBE"; }
				if($estadoPago=="MP"){ 
					$porcentajePago=porcentajePago($codigoSocio,$codigoActividad,$multa);
					if($porcentajePago>0){
						$infoEstado=$porcentajePago."% PAGADO";
					}else{
						$infoEstado="SIN PAGOS";
					}
				}

				$reporte.='
							<tr>
								<td class="textoCen">'.ceros($i,2).'</td>
								<td class="textoIzq">'.texto($actividad).'</td>
								<td class="textoDer">'.$infomulta.'</td>
								<td class="textoCen">'.ceros($lotes,2).' Lotes</td>
								<td class="textoCen">'.$razon.'</span></td>
								<td class="textoDer  resaltar">S/.&nbsp;'.$multa.'</td>
								<td class="textoCen">'.$infoEstado.'</span></td>
							</tr>
				';

				$i++;
			}

			$reporte.='
						<tr>
							<td colspan="7" class="totales textoCen">
								<strong>TOTAL:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_ASA_DEU_PAG.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>PAGADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoPagosAsambleas.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>EXONERACIONES:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalExoneradoSocioASA.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>TOTAL DEUDA:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_ASA_saldo.
							'</td>
						</tr>
					</tbody>
					</table>
					<div class="s-30"></div>
			';
		}

		// REPORTE DE DEUDAS POR FAENAS
		if($deudasFaenas>0){
			$reporte.='
				<h1>DEUDAS POR FAENAS</h1>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="textoCen">#</th>
						<th width="35%" class="textoCen">FAENA</th>
						<th class="textoCen">MULTA</th>
						<th class="textoCen">LOTES</th>
						<th class="textoCen">RAZON</th>
						<th class="textoCen">MONTO</th>
						<th class="textoCen">ESTADO</th>
					</tr>
				</thead>
				<tbody>
			';

			$sql="SELECT codigoActividad, lotes, asistio, retraso, multa,estadoPago FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND codigoSocio='$codigoSocio' AND (estadoPago='NP' OR estadoPago='MP')";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoActividad =$n[codigoActividad];
				$lotes           =$n[lotes];
				$asistio         =$n[asistio];
				$retraso         =$n[retraso];
				$multa           =$n[multa];
				$estadoPago      =$n[estadoPago];
				$actividad       =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
				$mTarde          =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
				$mFalta          =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');

				if($asistio=="NO"){ $razon="INASISTENCIA"; $infomulta='S/.&nbsp;'.moneda($mFalta); }
				if($asistio=="SI" AND $retraso>0.15){ $razon="TARDANZA"; $infomulta='S/.&nbsp;'.moneda($mTarde); }
				if($estadoPago=="NP"){ $infoEstado="DEBE"; }
				if($estadoPago=="MP"){ 
					$porcentajePago=porcentajePago($codigoSocio,$codigoActividad,$multa);
					if($porcentajePago>0){
						$infoEstado=$porcentajePago."% PAGADO";
					}else{
						$infoEstado="SIN PAGOS";
					}
				}

				$reporte.='
							<tr>
								<td class="textoCen">'.ceros($i,2).'</td>
								<td class="textoIzq">'.texto($actividad).'</td>
								<td class="textoDer">'.$infomulta.'</td>
								<td class="textoCen">'.ceros($lotes,2).' Lotes</td>
								<td class="textoCen">'.$razon.'</span></td>
								<td class="textoDer  resaltar">S/.&nbsp;'.$multa.'</td>
								<td class="textoCen">'.$infoEstado.'</span></td>
							</tr>
				';

				$i++;
			}

			$reporte.='
						<tr>
							<td colspan="7" class="totales textoCen">
								<strong>TOTAL:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_FAE_DEU_PAG.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>PAGADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoPagosFaenas.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>EXONERACIONES:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalExoneradoSocioFAE.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>TOTAL DEUDA:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_FAE_saldo.
							'</td>
						</tr>
					</tbody>
					</table>
					<div class="s-30"></div>
			';
		}

		// REPORTE DE DEUDAS POR CUOTAS
		if($deudasCuotas>0){
			$reporte.='
				<h1>DEUDAS POR CUOTAS</h1>
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="textoCen">#</th>
						<th width="35%" class="textoCen">CONCEPTO CUOTA</th>
						<th class="textoCen">CUOTA</th>
						<th class="textoCen">LOTES</th>
						<th class="textoCen">MONTO</th>
						<th class="textoCen">ESTADO</th>
					</tr>
				</thead>
				<tbody>
			';

			$sql="SELECT codigoCuota, lotes, montoCuota, montoPago, estadoPago FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' AND (estadoPago='NP' OR estadoPago='MP')";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoCuota =$n[codigoCuota];
				$lotes       =$n[lotes];
				$montoCuota  =$n[montoCuota];
				$montoPago   =$n[montoPago];
				$estadoPago  =$n[estadoPago];
				$cuota       =infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota');

				if($estadoPago=="NP"){ $infoEstado="DEBE"; }
				if($estadoPago=="MP"){ 
					$porcentajePago=porcentajePago($codigoSocio,$codigoCuota,$montoPago);
					if($porcentajePago>0){
						$infoEstado=$porcentajePago."% PAGADO";
					}else{
						$infoEstado="SIN PAGOS";
					}
				}

				$reporte.='
							<tr>
								<td class="textoCen">'.ceros($i,2).'</td>
								<td class="textoIzq">'.texto($cuota).'</td>
								<td class="textoDer">S/.&nbsp;'.$montoCuota.'</td>
								<td class="textoCen">'.ceros($lotes,2).' Lotes</td>
								<td class="textoDer  resaltar">S/.&nbsp;'.$montoPago.'</td>
								<td class="textoCen">'.$infoEstado.'</span></td>
							</tr>
				';

				$i++;
			}

			$reporte.='
						<tr>
							<td colspan="6" class="totales textoCen">
								<strong>TOTAL:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_CUO_DEU_PAG.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>PAGADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoPagosCuotas.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>EXONERACIONES:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalExoneradoSocioCUO.'&nbsp;&nbsp;|&nbsp;&nbsp;
								<strong>TOTAL DEUDA:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_CUO_saldo.
							'</td>
						</tr>
					</tbody>
					</table>
			';
		}
	}else{
		$reporte.='<div class="mensaje">SOCIO NO REGISTRA NINGUNA DEUDA</div>';
	}
	
	
	$reporte.=$impresoPor.'
		<body>
		</html>
	';
	
	$nombreArchivo="total-deudas-".$codigoSocio;
	$proceso ="IMPRESION DE <strong>TOTAL DEUDAS DE SOCIO</strong>";

	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);


	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'portait');
	$dompdf->render();
	$dompdf->stream($nombreArchivo.".pdf");
?>