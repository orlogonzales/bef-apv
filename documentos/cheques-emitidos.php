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
	
	$conexion               =conexionDB();
	$fechaInicio            =$_GET[fechaInicio];
	$fechaFin               =$_GET[fechaFin];
	$tipoCheque             =$_GET[tipoCheque];
	$entidadBancaria        =$_GET[entidadBancaria];
	$codigoCuenta           =$_GET[codigoCuenta];
	$codigoChequera         =$_GET[codigoChequera];
	$codigoCuenta           =$_GET[codigoCuenta];
	$codigoChequera         =$_GET[codigoChequera];
	$usuarioConsulta        =$_GET[usuarioConsulta];
	$consultaFechaIni       =fechaSQL($fechaInicio);
	$consultaFechaFin       =fechaSQL($fechaFin);
	
	$totalEmitidosFechasPAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'totalEmitidosFechasPAR');
	$totalEmitidosFechasVAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'totalEmitidosFechasVAR');
	$totalEmitidosFechas    =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'totalEmitidosFechas');

	if($tipoCheque=="VAR"){
		$nroEmitidosFechasVAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'nroEmitidosFechasVAR');
		$nroEmitidosFechasPAR =0;
		if($totalEmitidosFechasVAR>0){ $totalCheques='S/. '.moneda($totalEmitidosFechasVAR); }
	}

	if($tipoCheque=="PAR"){
		$nroEmitidosFechasPAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'nroEmitidosFechasPAR');
		$nroEmitidosFechasVAR =0;
		if($totalEmitidosFechasPAR>0){ $totalCheques='S/. '.moneda($totalEmitidosFechasPAR); }
	}

	if($tipoCheque=="ALL"){
		$nroEmitidosFechasVAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'nroEmitidosFechasVAR');
		$nroEmitidosFechasPAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'nroEmitidosFechasPAR');
		if($totalEmitidosFechas>0){ $totalCheques='S/. '.moneda($totalEmitidosFechas); }
	}

	$nroCheques             =$nroEmitidosFechasVAR+$nroEmitidosFechasPAR;
	$hoy                    =fechaSQL(infoTiempo('fechaHoy'));
	$hora                   =infoTiempo('hora');
	$dniUsuario             =$_SESSION['dni_apv'];	
	$subtitulo              ="CONSULTA GENERADA DE [".$fechaInicio."] HASTA [".$fechaFin."]";
	$impresoPor             ='<span class="infoImpresion textoMayuscula"> Impreso por:'.datoUsuario($dniUsuario,'nombreFull').' - '.infoFecha($hoy,'larga').' - '.horacorta($hora).'</span>';

	if($entidadBancaria=="ALL"){ $infoEntidadBancaria="TODAS LAS ENTIDADES"; }else{ $infoEntidadBancaria =texto(infoBancos($entidadBancaria,'detalleEntidad')); }
	if($usuarioConsulta=="ALL"){ $beneficiarioCheque="TODOS LOS BENEFICIARIOS"; }else{ $beneficiarioCheque=texto(datoUsuario($usuarioConsulta,'nombreFull')); if($beneficiarioCheque==""){ $beneficiarioCheque=infoSocios($usuarioConsulta,'nombre'); }else{ $beneficiarioCheque=$beneficiarioCheque; } }
	if($tipoCheque=="ALL"){ $rotuloTitulo=""; }
	if($tipoCheque=="PAR"){ $rotuloTitulo=" POR PARTIDAS"; }
	if($tipoCheque=="VAR"){ $rotuloTitulo=" POR CONCEPTOS VARIOS"; }

	$titulo  ="REPORTE DE CHEQUES EMITIDOS".$rotuloTitulo;
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
			<h1>'.$titulo.'</h1>
			<h2>'.$subtitulo.'</h2><br>
			
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="resaltar"><strong>ENTIDAD BANCARIA:</strong><br>'.$infoEntidadBancaria.'</td>
					<td class="resaltar"><strong>CHEQUE EMITIDO A:</strong><br>'.$beneficiarioCheque.'</td>
				</tr>
				<tr>
					<td class="resaltar"><strong>TOTAL CHEQUES EMITIDOS:</strong><br>'.ceros($nroCheques,2).'</td>
					<td class="resaltar"><strong>MONTO TOTAL DE CHEQUES EMITIDOS:</strong><br>'.$totalCheques.'</td>
				</tr>
			</table>
			<br>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th class="textoCen">#</th>
					<th class="textoCen">FECHA</th>
					<th width="25%" class="textoIzq">CONCEPTO</th>
					<th width="15%" class="textoIzq">EMITIDO A</th>
					<th class="textoCen">CHEQUERA</th>
					<th class="textoCen">CHEQUE</th>
					<th class="textoCen">MONTO</th>
					<th class="textoCen">REGISTO</th>
				</tr>
			</thead>
			<tbody>
	';

	if($usuarioConsulta=="ALL"){ $infoUsuario=""; }else{ $infoUsuario="AND beneficiario='$usuarioConsulta'"; }
	if($tipoCheque=="ALL"){ $infoTipoCheque=""; }else{ $infoTipoCheque="AND tipoCheque='$tipoCheque'"; }
	if($entidadBancaria=="ALL"){ $infoEntidadBancaria=""; }else{ $infoEntidadBancaria="AND codigoBanco='$entidadBancaria'"; }
	if($codigoCuenta=="ALL"){ $infoCodigoCuenta=""; }else{ $infoCodigoCuenta="AND codigoCuenta='$codigoCuenta'"; }
	if($codigoChequera=="ALL"){ $infoCodigoChequera=""; }else{ $infoCodigoChequera="AND codigoChequera='$codigoChequera'";}

	$sql="SELECT tipoCheque, codigoBanco, codigoCuenta, codigoChequera, nroCheque, codigoCheque, fechaEmision, monto, tipoBeneficiario, beneficiario, concepto, observaciones, codigoOperacion, fecha, hora, usuario FROM sm_cheques WHERE fechaEmision BETWEEN '$consultaFechaIni' AND '$consultaFechaFin' $infoTipoCheque $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera";
	$rs=mysqli_query($conexion,$sql);
	$i=1;
	while($n=mysqli_fetch_array($rs)){
		$tipoCheque       =$n[tipoCheque];
		$codigoBanco      =$n[codigoBanco];
		$entidadBancaria  =infoBancos($codigoBanco,'detalleEntidad');
		$codigoCuenta     =$n[codigoCuenta];
		$codigoChequera   =$n[codigoChequera];
		$nroCheque        =$n[nroCheque];
		$codigoCheque     =$n[codigoCheque];
		$fechaEmision     =infoFecha($n[fechaEmision],'normal');
		$monto            =$n[monto];
		$tipoBeneficiario =$n[tipoBeneficiario];
		$beneficiario     =$n[beneficiario];
		$nombre           =infocheque('','','','','','',$beneficiario,'nombreBeneficiario');
		$concepto         =texto($n[concepto]);
		$observaciones    =texto($n[observaciones]);
		$codigoOperacion  =$n[codigoOperacion];
		$fecha            =$n[fecha];
		$hora             =$n[hora];
		$usuario          =$n[usuario];
		$chequera         =texto($entidadBancaria.' '.infoChequeras($codigoChequera,$codigoBanco,'','detalleChequera'));
		$registradoPor    =registradoPor($usuario,$fecha,$hora,'SI','label-default');
		$codigoPartida    =infoPartida($codigoOperacion,'codigoPartida');

	$reporte.='
				<tr>
					<td class="textoCen">'.ceros($i,2).'</td>
					<td class="textoCen textoMayuscula">'.$fechaEmision.'</td>
					<td class="textoIzq">'.$concepto.'</td>
					<td class="textoIzq">'.$nombre.'</td>
					<td class="textoIzq">'.$chequera.'</td>
					<td class="textoIzq">'.$nroCheque.'</td>
					<td class="textoDer">S/.&nbsp;'.moneda($monto).'</td>
					<td class="textoCen">'.$registradoPor.'</td>
				</tr>
	';

$i++; }

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
	
	$dompdf = new Dompdf();
	$dompdf->loadHtml($reporte);
	$dompdf->setPaper('A4', 'landscape');
	$dompdf->render();
	$canvas = $dompdf->getCanvas();
	$canvas->page_text(400, 560, "Página: {PAGE_NUM} de {PAGE_COUNT}", $font, 8, array(0,0,0));
	$dompdf->stream("reporte-cheques-emitidos.pdf");
?>