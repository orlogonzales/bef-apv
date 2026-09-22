<?php
	ini_set("memory_limit","512M");
	set_time_limit(500);
	session_start();

	$ruta="../";
	require_once($ruta.'php/funciones.php');
	require_once $ruta.'dompdf/lib/html5lib/Parser.php';
	require_once $ruta.'dompdf/lib/php-font-lib/src/FontLib/Autoloader.php';
	require_once $ruta.'dompdf/lib/php-svg-lib/src/autoload.php';
	require_once $ruta.'dompdf/src/Autoloader.php';
	Dompdf\Autoloader::register();
	use Dompdf\Dompdf;
	
	$conexion       =conexionDB();
	$codigoPartida   =$_GET[codigoPartida];
	$hoy            =fechaSQL(infoTiempo('fechaHoy'));
	$hora           =infoTiempo('hora');
	$dniUsuario     =$_SESSION['dni_apv'];
	$impresoPor     ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';
	
	
	$infoCodigo     ='<small>CODIGO DE PARTIDA: '.$codigoPartida.'</small>';
	$titulo          ="REPORTE DE PARTIDA";

	$sql="SELECT usuarioPartida, concepto, monto, codigoChequera, nroCheque, observaciones, fechaPartida, fechaCierre, estado, fecha, hora, usuario  FROM sm_partidas WHERE codigoPartida='$codigoPartida'";
	$row=mysqli_query($conexion,$sql);
	$n=mysqli_fetch_array($row);
	$usuarioPartida  =$n[usuarioPartida];
	$concepto        =texto($n[concepto]);
	$monto           =$n[monto];
	$codigoChequera  =$n[codigoChequera];
	$nroCheque       =$n[nroCheque];
	$observaciones   =texto($n[observaciones]);
	$fechaPartida    =$n[fechaPartida];
	$fechaCierre     =$n[fechaCierre];
	$estado          =$n[estado];
	$fecha           =$n[fecha];
	$hora            =$n[hora];
	$usuario         =$n[usuario];
	$entidadBancaria =infoBancos(infoChequeras($codigoChequera,'','','entidadBancaria'),'detalleEntidad');
	$detalleChequera =texto(infoChequeras($codigoChequera,'','','detalleChequera'));
	$infoChequera    =$entidadBancaria.' - '.$detalleChequera;

	$responsable=texto(datoUsuario($usuarioPartida,'nombreFull'));
	if($fechaPartida!="0000-00-00"){ $infoFechaPartida=infoFecha($fechaPartida,'larga'); }else{ $infoFechaPartida="SIN FECHA DE GASTO"; }
	if($fechaCierre!="0000-00-00"){ $infoFechaCierre=infoFecha($fechaCierre,'larga'); }else{ $infoFechaCierre="SIN FECHA DE CIERRE"; }

	$nombreUP     =texto(datoUsuario($usuarioPartida,'nombreCorto'));
	$infoRegistro =registradoPor($usuario,$fecha,$hora,'NO','');

	if($estado=="OPN"){ $infoEstado='PARTIDA ABIERTA'; }
	if($estado=="CLS"){ $infoEstado='PARTIDA CERRADA'; }
	if($estado=="USO"){ $infoEstado='PARTIDA EN USO'; }

	$montoDispuesto =infoPartida($codigoPartida,'totalSAL');
	$totalING       =infoPartida($codigoPartida,'totalING');
	$saldoPartida   =$monto-$montoDispuesto;
	$totalPartida   =infoPartida($codigoPartida,'montoPartida');
	$totalCierre    =$montoDispuesto+$totalING;
	
	if($totalING>0){ $montoIngresos='S/. '.moneda($totalING); }else{ $montoIngresos='----'; }
	$totales        ='<td colspan="7" class="totales textoCen"><strong>TOTAL EGRESOS:</strong>&nbsp;S/. '.moneda($montoDispuesto).'&nbsp;&nbsp;|&nbsp;&nbsp;<strong>TOTAL INGRESOS:</strong>&nbsp;'.$montoIngresos.'&nbsp;&nbsp;|&nbsp;&nbsp;<strong>TOTAL CIERRE:</strong>&nbsp;S/. '.moneda($totalCierre).'</td>';

	$infoObservaciones=$observaciones;
	$reportePartida='<a href="../documentos/reporte-partida.php?codigoPartida='.$codigoPartida.'" class="list-group-item"><i class="icon-printer2"></i> REGISTRO GASTOS DE PARTIDA</a>';


	$proceso ="IMPRESION DE <strong>".$titulo.' '.$concepto."</strong>";
	$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$hoy', '$hora', '$dniUsuario')";
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
			<h2>'.$concepto.'</h2>
			<h3>'.$infoCodigo.'</h3>
			
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infoTexto">
				<tr>
					<td class="resaltar" colspan="2"><strong>CONCEPTO:</strong><br>'.$concepto.'</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>RESPONSABLE DE GASTO:</strong><br>'.$responsable.'</td>
					<td class="resaltar"><strong>MONTO DE PARTIDA:</strong><br>S/. '.moneda($monto).'</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>CHEQUERA:</strong><br>'.$infoChequera.'</td>
					<td class="resaltar"><strong>NRO. DE CHEQUE:</strong><br>'.$nroCheque.'</td>
				</tr>
				<tr>
					<td class="resaltar mayusculas"><strong>FECHA DE GASTO:</strong><br>'.$infoFechaPartida.'</td>
					<td class="resaltar mayusculas"><strong>FECHA DE CIERRE:</strong><br>'.$infoFechaCierre.'</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>ESTADO:</strong><br>'.$infoEstado.'</td>
					<td class="resaltar"><strong>GENERADO POR:</strong><br>'.$infoRegistro.'</td>
				</tr>
				<tr>
					<td class="resaltar" colspan="2"><strong>OBSERVACIONES:</strong><br>'.$observaciones.'</td>
				</tr>
			</table>
			<br><hr><br>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">MOV</th>
					<th class="textoIzq">FECHA OPERACION</th>
					<th width="40%" class="textoIzq">DETALLE DE PROCESO</th>
					<th class="textoCen">MONTO</th>
					<th width="20%" class="textoIzq">DOCUMENTO</th>
					<th class="textoCen">REGISTO</th>
				</tr>
			</thead>
			<tbody>
	';

	$sql="SELECT movimiento, fechaOperacion, tipoDocumento, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE (movimiento='SAL' OR movimiento='ING') AND codigoConcepto='$codigoPartida' ORDER BY id DESC";
	$rs=mysqli_query($conexion,$sql);
	while($n=mysqli_fetch_array($rs)){
	$movimiento      =$n[movimiento];
	$fechaOperacion  =$n[fechaOperacion];
	$tipoDocumento   =$n[tipoDocumento];
	$nroDocumento    =$n[nroDocumento];
	$monto           =$n[monto];
	$detalleConcepto =$n[detalleConcepto];
	$observaciones   =$n[observaciones];
	$codigoOperacion =$n[codigoOperacion];
	$fecha           =$n[fecha];
	$hora            =$n[hora];
	$usuario         =$n[usuario];
	$infoRegistro    =registradoPor($usuario,$fecha,$hora,'NO','');

	$reporte.='
				<tr>
					<td class="textoCen">'.ceros($cuota,2).'</td>
					<td class="textoCen">'.$movimiento.'</td>
					<td class="textoIzq textoMayuscula">'.infoFecha($fechaOperacion,'corta').'</td>
					<td class="textoIzq">'.texto($detalleConcepto).'</td>
					<td class="textoDer textoNegrita resaltar"> S/. '.moneda($monto).'</td>
					<td class="textoIzq">'.infoTipoDOC($tipoDocumento).' N°: <strong>'.$nroDocumento.'</strong></td>
					<td width="15%" class="textoCen">'.$infoRegistro.'</span></td>
				</tr>
	';
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
	$dompdf->stream("reporte-partida-".$codigoPartida.".pdf");
?>