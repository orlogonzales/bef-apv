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
	$hoy             =fechaSQL(infoTiempo('fechaHoy'));
	$hora            =infoTiempo('hora');
	$dniUsuario      =$_SESSION['dni_apv'];
	$rutaCB          ='../assets/images/codigo-barra/';
	$fileCB          =$codigoSocio.'.png';
	$codBar          =$rutaCB.$fileCB;
	$impresoPor      ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';

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
		<h1>INFORME DE ASISTENCIAS, DEUDAS, PAGOS Y EXONERACIONES</h1>
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
		<div class="s-20"></div>
	';

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

	$reporte.='
		<table width="100%" border="0" cellspacing="0" cellpadding="10" style="margin-bottom:20px; border: none !important;">
			<tr>
				<td style="border: none !important;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr><td class="resaltar"><strong>ASAMBLEAS</strong></td></tr>
						<tr><td><strong>TOTAL EN ASAMBLEA:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_ASA_DEU_PAG.'</td></tr>
						<tr><td><strong>TOTAL PAGOS EFECTIVOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_pagos_ASA_efectivos.'</td></tr>
						<tr><td><strong>TOTAL PAGOS PROGRAMADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_pagos_ASA_programados.'</td></tr>
						<tr><td><strong>TOTAL CONDONADAS (JUSTIFICACIONES):</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_ASA_justificados.'</td></tr>
						<tr><td><strong>TOTAL PAGOS PENDIENTES:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_ASA_saldo.'</td></tr>
						<tr><td><strong>TOTAL PAGOS DEUDAS EXONERADAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalPagosExoneradoSocioASA.'</td></tr>
						<tr><td><strong>TOTAL MONTOS EXONERADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalExoneradoSocioASA.'</td></tr>
					</table>
				</td>
				<td style="border: none !important;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr><td class="resaltar"><strong>FAENAS</strong></td></tr>
						<tr><td><strong>TOTAL EN CUOTAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_FAE_DEU_PAG.'</td></tr>
						<tr><td><strong>TOTAL PAGOS EFECTIVOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_pagos_FAE_efectivos.'</td></tr>
						<tr><td><strong>TOTAL PAGOS PROGRAMADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_pagos_FAE_programados.'</td></tr>
						<tr><td><strong>TOTAL CONDONADAS (JUSTIFICACIONES):</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_FAE_justificados.'</td></tr>
						<tr><td><strong>TOTAL PAGOS PENDIENTES:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_FAE_saldo.'</td></tr>
						<tr><td><strong>TOTAL PAGOS DEUDAS EXONERADAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalPagosExoneradoSocioFAE.'</td></tr>
						<tr><td><strong>TOTAL MONTOS EXONERADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalExoneradoSocioFAE.'</td></tr>
					</table>
				</td>
				<td style="border: none !important;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr><td class="resaltar"><strong>CUOTAS</strong></td></tr>
						<tr><td><strong>TOTAL EN CUOTAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_CUO_DEU_PAG.'</td></tr>
						<tr><td><strong>TOTAL PAGOS EFECTIVOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_pagos_CUO_efectivos.'</td></tr>
						<tr><td><strong>TOTAL PAGOS PROGRAMADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_pagos_CUO_programados.'</td></tr>
						<tr><td><strong>TOTAL CONDONADAS (JUSTIFICACIONES):</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_CUO_justificados.'</td></tr>
						<tr><td><strong>TOTAL PAGOS PENDIENTES:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$info_total_CUO_saldo.'</td></tr>
						<tr><td><strong>TOTAL PAGOS DEUDAS EXONERADAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalPagosExoneradoSocioCUO.'</td></tr>
						<tr><td><strong>TOTAL MONTOS EXONERADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalExoneradoSocioCUO.'</td></tr>
					</table>
				</td>
				<td style="border: none !important;">
					<table width="100%" border="0" cellspacing="0" cellpadding="0">
						<tr><td class="resaltar"><strong>EXONERACIONES PAGADAS</strong></td></tr>
						<tr><td><strong>TOTAL EXONERADOS PAGADOS EN ASAMBLEAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalPagosExoneradoSocioASA.'</td></tr>
						<tr><td><strong>TOTAL EXONERADOS PAGADOS EN FAENAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalPagosExoneradoSocioFAE.'</td></tr>
						<tr><td><strong>TOTAL EXONERADOS PAGADOS EN CUOTAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalPagosExoneradoSocioCUO.'</td></tr>
						<tr><td><strong>TOTAL DEUDAS EXONERADAS PAGADAS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalPagosExoneradoSocio.'</td></tr>
						<tr><td><strong>&nbsp;</td></tr>
						<tr><td><strong>TOTAL DEUDA GENERAL:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalGeneralDeudas.'</td></tr>
						<tr><td><strong>TOTAL MONTOS EXONERADOS:</strong>&nbsp;&nbsp;&nbsp;&nbsp;'.$infoTotalExoneradoSocio.'</td></tr>
					</table>
				</td>
			</tr>
		</table>
	';

	if($asambleas>0){
		$reporte.='
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="textoCen">#</th>
						<th class="textoCen">CODIGO</th>
						<th class="textoIzq">TEMA ASAMBLEA</th>
						<th class="textoCen">FECHA</th>
						<th class="textoCen">RAZON</th>
						<th class="textoCen">MULTA</th>
						<th class="textoCen">TOTAL</th>
						<th class="textoCen">ESTADO</th>
						<th class="textoCen">%</th>
						<th class="textoCen">EXONERADO</th>
						<th class="textoCen">PAGADO</th>
						<th class="textoCen">FECHAS</th>
						<th class="textoCen">CAJA</th>
					</tr>
				</thead>
				<tbody>
		';
		$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND codigoSocio='$codigoSocio' ORDER BY id DESC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$tipoActividad   =$n[tipoActividad];
			$codigoActividad =$n[codigoActividad];
			$temaActividad   =texto(infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad'));
			$fechaActividad  =infoFecha(infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'fechaActividad'),'muycorta');
			$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
			$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
			$lotes           =$n[lotes];
			$asistio         =$n[asistio];
			$ingreso         =$n[ingreso];
			$salida          =$n[salida];
			$retraso         =$n[retraso];
			$multa           =$n[multa];
			$estadoPago      =$n[estadoPago];
			$conceptoPago    =$tipoActividad;
			$programado      =conceptoProgramado($codigoSocio,$codigoActividad);
			$porcentajePago  =porcentajePago($codigoSocio,$codigoActividad,$multa);
			$infoTotalMulta  ='S/. '.moneda($multa);

			$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
			$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
			$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');

			if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
			if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
			if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }
			
			if($programado>=2){
				$infoProgramado=ceros($programado,2).' FECHAS';
				$verificaPago   =verificaPagosProgramados($codigoActividad,$codigoSocio,$multa);
				$pagoProgramado ="SI";
			}else{
				$infoProgramado='';
				$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$multa);
				$pagoProgramado ="NO";
			}

			if($asistio=="IN"){
				$asistencia="";
				$razon="";
				$infoMulta="";
				$infoTotalMulta="";
				$estado='INVITADO';
			}

			if($asistio=="NO"){
				$asistencia="FALTO";
				$razon="FALTA";
				$infoMulta='S/. '.moneda($multaFalta);
			}

			if($asistio=="SI" and $retraso<=0.15){
				$asistencia="ASISTIO";
				$razon="PUNTUAL";
				$infoMulta='';
				$estado='ASISTIO';
				$infoTotalMulta='';
			}

			if($asistio=="SI" and $retraso>0.15){
				$asistencia="TARDANZA";
				$razon="TARDE";
				$infoMulta='S/. '.moneda($multaTarde);
			}
			
			if($asistio=="JU"){
				$asistencia="JUSTIFICADO";
				$razon="FALTA";
				$infoMulta='S/. '.moneda($multaFalta);
				$infoTotalMulta='-'.$infoTotalMulta;
				$estadoPago="JUS";
			}
			if($estadoPago=="NP" and $programado==0){ $estado='PENDIENTE'; }
			if($estadoPago=="SP" and $programado==0){ $estado='PAGADO'; }
			if($estadoPago=="SP" and $programado>=2){ $estado='PAGADO'; }
			if($estadoPago=="MP" and $programado>=2){ $estado='AL '.$porcentajePago.'%'; }
			if($estadoPago=="JUS"){ $estado='JUSTIFICADO'; }

			if($estadoPago=="EX"){
				$estado='EXONERADO';
				$monto=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
				$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$monto);
			}

			if($verificaPago=="ERROR" and $estadoPago==""){ $caja=''; }
			if($verificaPago=="ERROR" and $estadoPago=="NP"){ $caja=''; }
			if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='CAJA'; }
			if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='ERROR'; }
			if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='NO REGISTRADO'; }

		$reporte.='
					<tr>
						<td class="textoCen">'.ceros($i,2).'</td>
						<td class="textoCen">'.$codigoActividad.'</td>
						<td class="textoIzq" width="55%">'.$temaActividad.'</td>
						<td class="textoCen mayusculas">'.$fechaActividad.'</td>
						<td class="textoCen">'.$asistencia.'</td>
						<td class="textoDer">'.$infoMulta.'</td>
						<td class="textoDer">'.$infoTotalMulta.'</td>
						<td class="textoCen">'.$estado.'</td>
						<td class="textoCen">'.$infoPorcentajeEXO.'</td>
						<td class="textoDer">'.$infoExonerado.'</td>
						<td class="textoDer">'.$infoTotalPago.'</td>
						<td class="textoCen">'.$infoProgramado.'</td>
						<td class="textoCen">'.$caja.'</td>
					</tr>
		';

		$i++; }

		$reporte.='
				</tbody>
			</table>
			<div class="s-20"></div>
		';
	}else{ $reporte.='<div class="resaltar" style="padding:20px 10px 15px 10px; margin-bottom:20px; "><h2 class="textoCen">SIN DEUDAS PENDIENTES EN ASAMBLEAS</h2><p class="textoCen">NO SE HA ENCONTRADO EN EL SISTEMA NINGUN REGISTRO  CON DEUDAS PENDIENTES POR ASAMBLEAS</p></div>'; }

	if($faenas>0){
		$reporte.='
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="textoCen">#</th>
						<th class="textoCen">CODIGO</th>
						<th class="textoIzq">TEMA FAENA</th>
						<th class="textoCen">FECHA</th>
						<th class="textoCen">RAZON</th>
						<th class="textoCen">MULTA</th>
						<th class="textoCen">TOTAL</th>
						<th class="textoCen">ESTADO</th>
						<th class="textoCen">%</th>
						<th class="textoCen">EXONERADO</th>
						<th class="textoCen">PAGADO</th>
						<th class="textoCen">FECHAS</th>
						<th class="textoCen">CAJA</th>
					</tr>
				</thead>
				<tbody>
		';
		$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND codigoSocio='$codigoSocio' ORDER BY id DESC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$tipoActividad   =$n[tipoActividad];
			$codigoActividad =$n[codigoActividad];
			$temaActividad   =texto(infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad'));
			$fechaActividad  =infoFecha(infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'fechaActividad'),'muycorta');
			$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
			$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
			$lotes           =$n[lotes];
			$asistio         =$n[asistio];
			$ingreso         =$n[ingreso];
			$salida          =$n[salida];
			$retraso         =$n[retraso];
			$multa           =$n[multa];
			$estadoPago      =$n[estadoPago];
			$conceptoPago    =$tipoActividad;
			$programado      =conceptoProgramado($codigoSocio,$codigoActividad);
			$porcentajePago  =porcentajePago($codigoSocio,$codigoActividad,$multa);
			$infoTotalMulta  ='S/. '.moneda($multa);

			$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
			$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
			$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');

			if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
			if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
			if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }
			
			if($programado>=2){
				$infoProgramado=ceros($programado,2).' FECHAS';
				$verificaPago   =verificaPagosProgramados($codigoActividad,$codigoSocio,$multa);
				$pagoProgramado ="SI";
			}else{
				$infoProgramado='';
				$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$multa);
				$pagoProgramado ="NO";
			}

			if($asistio=="IN"){
				$asistencia="";
				$razon="";
				$infoMulta="";
				$infoTotalMulta="";
				$estado='INVITADO';
			}

			if($asistio=="NO"){
				$asistencia="FALTO";
				$razon="FALTA";
				$infoMulta='S/. '.moneda($multaFalta);
			}

			if($asistio=="SI" and $retraso<=0.15){
				$asistencia="ASISTIO";
				$razon="PUNTUAL";
				$infoMulta='';
				$estado='ASISTIO';
				$infoTotalMulta='';
			}

			if($asistio=="SI" and $retraso>0.15){
				$asistencia="TARDANZA";
				$razon="TARDE";
				$infoMulta='S/. '.moneda($multaTarde);
			}
			
			if($asistio=="JU"){
				$asistencia="JUSTIFICADO";
				$razon="FALTA";
				$infoMulta='S/. '.moneda($multaFalta);
				$infoTotalMulta='-'.$infoTotalMulta;
				$estadoPago="JUS";
			}
			if($estadoPago=="NP" and $programado==0){ $estado='PENDIENTE'; }
			if($estadoPago=="SP" and $programado==0){ $estado='PAGADO'; }
			if($estadoPago=="SP" and $programado>=2){ $estado='PAGADO'; }
			if($estadoPago=="MP" and $programado>=2){ $estado='AL '.$porcentajePago.'%'; }
			if($estadoPago=="JUS"){ $estado='JUSTIFICADO'; }

			if($estadoPago=="EX"){
				$estado='EXONERADO';
				$monto=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
				$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$monto);
			}

			if($verificaPago=="ERROR" and $estadoPago==""){ $caja=''; }
			if($verificaPago=="ERROR" and $estadoPago=="NP"){ $caja=''; }
			if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='CAJA'; }
			if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='ERROR'; }
			if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='NO REGISTRADO'; }

		$reporte.='
					<tr>
						<td class="textoCen">'.ceros($i,2).'</td>
						<td class="textoCen">'.$codigoActividad.'</td>
						<td class="textoIzq" width="55%">'.$temaActividad.'</td>
						<td class="textoCen mayusculas">'.$fechaActividad.'</td>
						<td class="textoCen">'.$asistencia.'</td>
						<td class="textoDer">'.$infoMulta.'</td>
						<td class="textoDer">'.$infoTotalMulta.'</td>
						<td class="textoCen">'.$estado.'</td>
						<td class="textoCen">'.$infoPorcentajeEXO.'</td>
						<td class="textoDer">'.$infoExonerado.'</td>
						<td class="textoDer">'.$infoTotalPago.'</td>
						<td class="textoCen">'.$infoProgramado.'</td>
						<td class="textoCen">'.$caja.'</td>
					</tr>
		';

		$i++; }

		$reporte.='
				</tbody>
			</table>
			<div class="s-20"></div>
		';
	}else{ $reporte.='<div class="resaltar" style="padding:20px 10px 15px 10px; margin-bottom:20px; "><h2 class="textoCen">SIN DEUDAS PENDIENTES EN FAENAS</h2><p class="textoCen">NO SE HA ENCONTRADO EN EL SISTEMA NINGUN REGISTRO  CON DEUDAS PENDIENTES POR FAENAS</p></div>'; }

	if($cuotas>0){
		$reporte.='
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<th class="textoCen">#</th>
						<th class="textoCen">CODIGO</th>
						<th class="textoIzq">CONCEPTO DE CUOTA</th>
						<th class="textoCen">CUOTA</th>
						<th class="textoCen">TOTAL</th>
						<th class="textoCen">ESTADO</th>
						<th class="textoCen">%</th>
						<th class="textoCen">EXONERADO</th>
						<th class="textoCen">PAGADO</th>
						<th class="textoCen">FECHAS</th>
						<th class="textoCen">CAJA</th>
					</tr>
				</thead>
				<tbody>
		';
		$sql="SELECT codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' ORDER BY id DESC";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$tipoActividad  ='CUO';
			$codigoCuota    =$n[codigoCuota];
			$lotes          =$n[lotes];
			$montoCuota     =$n[montoCuota];
			$montoPago      =$n[montoPago];
			$estadoPago     =$n[estadoPago];
			$dni            =infoSocios($codigoSocio,'dni');
			$conceptoCuota  =texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota'));
			$conceptoPago   =$tipoActividad;
			$programado     =conceptoProgramado($codigoSocio,$codigoCuota);
			$porcentajePago =porcentajePago($codigoSocio,$codigoCuota,$montoPago);
			$codigoActividad=$codigoCuota;

			$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
			$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
			$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');

			if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
			if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
			if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }

			if($programado>=2){
				$infoProgramado=ceros($programado,2).' FECHAS';
				$verificaPago   =verificaPagosProgramados($codigoActividad,$codigoSocio,$multa);
				$pagoProgramado ="SI";
			}else{
				$infoProgramado='';
				$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$multa);
				$pagoProgramado ="NO";
			}

			$totalPagado    =infoPagoFechas($codigoSocio,$codigoCuota,'totalFechasPagadas');

			if($estadoPago=="NP" and $programado==0){ $estado='PENDIENTE'; }
			if($estadoPago=="MP" and $programado>=2){ $estado='AL '.$porcentajePago.'%'; }
			if($estadoPago=="SP" and $programado==0){ $estado='PAGADO';}
			if($estadoPago=="SP" and $programado>=2){ $estado='PAGADO'; }

			if($estadoPago=="EX"){
				$estado='EXONERADO';
				$monto=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
				$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$monto);
			}

			if($verificaPago=="ERROR" and $estadoPago==""){ $caja=''; }
			if($verificaPago=="ERROR" and $estadoPago=="NP"){ $caja=''; }
			if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='CAJA'; }
			if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='ERROR'; }
			if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='NO REGISTRADO'; }

		$reporte.='
					<tr>
						<td class="textoCen">'.ceros($i,2).'</td>
						<td class="textoCen">'.$codigoCuota.'</td>
						<td class="textoIzq" width="60%">'.$conceptoCuota.'</td>
						<td class="textoDer">S/. '.$montoCuota.'</td>
						<td class="textoDer">S/. '.$montoPago.'</td>
						<td class="textoCen">'.$estado.'</td>
						<td class="textoCen">'.$infoPorcentajeEXO.'</td>
						<td class="textoDer">'.$infoExonerado.'</td>
						<td class="textoDer">'.$infoTotalPago.'</td>
						<td class="textoCen">'.$infoProgramado.'</td>
						<td class="textoCen">'.$caja.'</td>
					</tr>
		';

		$i++; }

		$reporte.='
				</tbody>
			</table>
		';
	}else{ $reporte.='<div class="resaltar" style="padding:20px 10px 15px 10px; margin-bottom:20px; "><h2 class="textoCen">SIN DEUDAS PENDIENTES EN CUOTAS</h2><p class="textoCen">NO SE HA ENCONTRADO EN EL SISTEMA NINGUN REGISTRO  CON DEUDAS PENDIENTES POR CUOTAS</p></div>'; }

	
	$reporte.=$impresoPor.'
		<body>
		</html>
	';

	$proceso ="IMPRESION DE INFORME <strong>ASISTENCIAS, DEUDAS, REGISTRO DE PAGOS EN CAJA Y EXONERACION DE DEUDAS</strong>";
	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$hoy', '$hora', '$dniUsuario')";
	$rs=mysqli_query($conexion,$sql);

	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
	$dompdf->stream("informe-socio-deudas-pagos-exoneraciones.pdf");
?>