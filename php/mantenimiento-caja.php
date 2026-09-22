<?php
	session_start();
	include ('funciones.php');
	$conexion      =conexionDB();
	$fecha         =infoTiempo('fecha');
	$hora          =infoTiempo('hora');
	$dniUsuario    =$_SESSION['dni_apv'];
	$operacion     =$_POST[operacion];
	$tipoActividad =$_POST[tipoActividad];

	if($operacion=="REGISTRA_GASTOS_PARTIDA"){
		$concepto        =$_POST[concepto];
		$codigoPartida   =$_POST[codigoPartida];
		$movimiento      =$_POST[movimiento];
		$codigoConcepto  =$_POST[codigoConcepto];
		$detalleConcepto =utf8_decode($_POST[detalleConcepto]);
		$fechaOperacion  =fechaSQL($_POST[fechaOperacion]);
		$monto           =$_POST[monto];
		$tipoDocumento   =$_POST[tipoDocumento];
		$nroDocumento    =$_POST[nroDocumento];
		$observaciones   =utf8_decode($_POST[observaciones]);
		$usuario         =$dniUsuario;
		$codigoSocio     =$dniUsuario;
		$codigoOperacion =codOperacion($concepto,$fecha,$hora,$usuario);

		$montoDispuesto =infoPartida($codigoPartida,'totalSAL');
		$totalING       =infoPartida($codigoPartida,'totalING');
		$totalPartida   =infoPartida($codigoPartida,'montoPartida');

		if($montoDispuesto==0){ $montoDispuesto=0; }else{ $montoDispuesto=$montoDispuesto; }
		if($totalING==0){ $totalING=0; }else{ $totalING=$totalING; }
		if($totalPartida==0){ $totalPartida=0; }else{ $totalPartida=$totalPartida; }

		$saldoPartida   =moneda($totalPartida-($montoDispuesto+$monto));
		
		if($saldoPartida==0){ 
			$estado="CLS";
			$proceso="CIERRE Y REGISTRO DE GASTOS DE PARTIDA - ".$detalleConcepto;
		}
		if($saldoPartida>0 and $saldoPartida<$totalPartida){
			$estado="USO";
			$proceso="REGISTRO DE GASTOS DE PARTIDA - ".$detalleConcepto;
		}

		if($estado=="CLS"){ $sql="UPDATE sm_partidas SET estado='$estado', fechaCierre='$fecha' WHERE codigoPartida='$codigoPartida'"; }
		else{ $sql="UPDATE sm_partidas SET estado='$estado' WHERE codigoPartida='$codigoPartida'"; }
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_mod_caja(movimiento, fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$movimiento', '$fechaOperacion', '$tipoActividad', '$concepto', '$codigoSocio', '$codigoConcepto', '$tipoDocumento', '$nroDocumento', '$monto', '$detalleConcepto', '$observaciones', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_procesos_partidas(codigoPartida, proceso, monto, fecha, hora, usuario) VALUES('$codigoPartida', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);
		
		$respuesta->mensaje ="GASTOS_REGISTRADOS";
	}

	if($operacion=="REGISTRA_CIERRE_PARTIDA"){
		$concepto        =$_POST[concepto];
		$codigoPartida   =$_POST[codigoPartida];
		$movimiento      =$_POST[movimiento];
		$codigoConcepto  =$_POST[codigoConcepto];
		$detalleConcepto =utf8_decode($_POST[detalleConcepto]);
		$fechaOperacion  =fechaSQL($_POST[fechaOperacion]);
		$monto           =$_POST[monto];
		$tipoDocumento   =$_POST[tipoDocumento];
		$nroDocumento    =$_POST[nroDocumento];
		$observaciones   =utf8_decode($_POST[observaciones]);
		$usuario         =$dniUsuario;
		$codigoSocio     =$dniUsuario;
		$codigoOperacion =codOperacion($concepto,$fecha,$hora,$usuario);
		
		$montoDispuesto  =infoPartida($codigoPartida,'totalSAL');
		$totalING        =infoPartida($codigoPartida,'totalING');
		$totalPartida    =infoPartida($codigoPartida,'montoPartida');
		$saldoPartida    =moneda($totalPartida-($montoDispuesto+$monto));
		$proceso         =$detalleConcepto;

		if($montoDispuesto==0){ $montoDispuesto=0; }else{ $montoDispuesto=$montoDispuesto; }
		if($totalING==0){ $totalING=0; }else{ $totalING=$totalING; }
		if($totalPartida==0){ $totalPartida=0; }else{ $totalPartida=$totalPartida; }
		if($saldoPartida==0 or $saldoPartida<=0){ $estado="CLS"; }

		if($estado=="CLS"){ $sql="UPDATE sm_partidas SET estado='$estado', fechaCierre='$fecha' WHERE codigoPartida='$codigoPartida'"; }
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_mod_caja(movimiento, fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$movimiento', '$fechaOperacion', '$tipoActividad', '$concepto', '$codigoSocio', '$codigoConcepto', '$tipoDocumento', '$nroDocumento', '$monto', '$detalleConcepto', '$observaciones', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_procesos_partidas(codigoPartida, proceso, monto, fecha, hora, usuario) VALUES('$codigoPartida', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);
		
		$respuesta->mensaje ="PARTIDA_CERRADA";
	}
	
	if($operacion=="REGISTRA_SALIDA_CAJA"){
		$movimiento      =$_POST[movimiento];
		$tipoResposanble =$_POST[tipoResposanble];
		$responsableSAL  =$_POST[responsableSAL];
		$concepto        =$_POST[concepto];
		$detalleConcepto =utf8_decode($_POST[detalleConcepto]);
		$fechaOperacion  =fechaSQL($_POST[fechaOperacion]);
		$monto           =$_POST[monto];
		$tipoDocumento   =$_POST[tipoDocumento];
		$nroDocumento    =$_POST[nroDocumento];
		$observaciones   =utf8_decode($_POST[observaciones]);
		$usuario         =$dniUsuario;
		$codigoSocio     =$responsableSAL;
		$codigoOperacion =codOperacion($movimiento,$fechaOperacion,$hora,$responsableSAL);

		$respuesta->informacion = $codigoSocio;

		$proceso="SALIDA DE CAJA - ".$detalleConcepto;

		$sql="INSERT INTO sm_mod_caja(movimiento, fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$movimiento', '$fechaOperacion', '$tipoActividad', '$concepto', '$codigoSocio', '$codigoConcepto', '$tipoDocumento', '$nroDocumento', '$monto', '$detalleConcepto', '$observaciones', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$proceso="SALIDA DE CAJA - ".$detalleConcepto." x S/. ".moneda($monto);
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);
		
		$respuesta->mensaje ="SALIDA_REGISTRADA";
	}

	cerrarDB();
	echo json_encode($respuesta);
?>