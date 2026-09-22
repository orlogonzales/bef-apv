<?php
	session_start();
	include ('funciones.php');
	$conexion      =conexionDB();
	$fecha         =infoTiempo('fecha');
	$hora          =infoTiempo('hora');
	$dniUsuario    =$_SESSION['dni_apv'];
	$operacion     =$_POST[operacion];
	$tipoActividad =$_POST[tipoActividad];
	$codigoConcepto =$_POST[codigoConcepto];

	if($tipoActividad=="ASA"){ $codigoCuenta=infoActividad($idJuntaDirectiva,$codigoConcepto,'','cuentaBanco'); }
	if($tipoActividad=="FAE"){ $codigoCuenta=infoActividad($idJuntaDirectiva,$codigoConcepto,'','cuentaBanco'); }
	if($tipoActividad=="CUO"){ $codigoCuenta=infoCuota($idJuntaDirectiva,$codigoConcepto,'cuentaBanco'); }

	if($operacion=="PAGO_MONTO_COMPLETO"){
		$idJuntaDirectiva = $_POST[idJuntaDirectiva];
		$codigoSocio      = $_POST[codigoSocio];
		$documentoPago    = $_POST[documentoPago];
		$montoPago        = $_POST[montoPago];
		$fechaPago        = fechaSQL($_POST[fechaPago]);
		$fecha            = $fecha;
		$hora             = $hora;
		$usuario          = $dniUsuario;

		if($tipoActividad=="ASA"){ $concepto="MCA"; }
		if($tipoActividad=="FAE"){ $concepto="MCF"; }

		if(($tipoActividad=="ASA") or ($tipoActividad=="FAE")){
			$razon     =$_POST[razon];

			if($razon=="TARDE"){
				$multaPor="TARDANZA";
				$multa=infoActividad($idJuntaDirectiva,$codigoConcepto,'','infoMultaporTardanza');
			}
			if($razon=="FALTA"){
				$multaPor="INASISTENCIA";
				$multa=infoActividad($idJuntaDirectiva,$codigoConcepto,'','infoMultaPorFalta');
			}

			if($tipoActividad=="ASA"){ $actividad="ASAMBLEA"; }
			if($tipoActividad=="FAE"){ $actividad="FAENA"; }

			$infoPago=infoActividad($idJuntaDirectiva,$codigoConcepto,'','temaActividad');
			$detalleConcepto ='PAGO DE MULTA POR '.$multaPor.' A '.$actividad.' <strong>'.$infoPago.'</strong>';
		}

		if($tipoActividad=="CUO"){
			$concepto="PCC";
			$infoPago=infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota');
			$detalleConcepto="PAGO DE CUOTA - <strong>".$infoPago."</strong>";
		}

		///////////////////////////////////////////////////
		/// DATOS DE TABLA CAJA
		///////////////////////////////////////////////////
		$movimiento      ="ING";
		$fechaOperacion  =$fechaPago;
		$tipoDocumento   ="VOU";
		$nroDocumento    =$documentoPago;
		$monto           =$montoPago;
		$observaciones   ="";
		$codigoOperacion =codOperacion($tipoActividad,$fecha,$hora,$usuario);

		$respuesta->informacion = $debePagar;
		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE CAJA
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_mod_caja(idJuntaDirectiva, movimiento, fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, codigoBanco, codigoCuenta, codigoChequera, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$idJuntaDirectiva', '$movimiento','$fechaOperacion','$tipoActividad','$concepto','$codigoSocio','$codigoConcepto','$tipoDocumento','$codigoBanco','$codigoCuenta','$codigoChequera','$nroDocumento','$monto','$detalleConcepto','$observaciones','$codigoOperacion','$fecha','$hora','$usuario')";
		$rs=mysqli_query($conexion,$sql);
		if($rs){
			///////////////////////////////////////////////////
			/// ACTUALIZAR DATOS DE PAGO - TABLA CUOTA
			///////////////////////////////////////////////////
			if($tipoActividad=="CUO"){
				$debePagar=infoCuota($idJuntaDirectiva,$codigoConcepto,'montoCuota');
				if($montoPago<$debePagar){
					$porcentajePago=($montoPago*100)/$debePagar;
				}else{
					$porcentajePago=100;
				}
				$sql="UPDATE sm_mod_cuotas_socios SET estadoPago = 'SP', caja = 'SI', montoPagado=$montoPago, porcentajePago='$porcentajePago' WHERE codigoCuota = '$codigoConcepto' AND codigoSocio = '$codigoSocio'";
				$rs=mysqli_query($conexion,$sql);
			}

			///////////////////////////////////////////////////
			/// ACTUALIZAR DATOS DE PAGO - TABLA ACTIVIDADES
			///////////////////////////////////////////////////
			if(($tipoActividad=="ASA") or ($tipoActividad=="FAE")){
				$debePagar=infoActividad($idJuntaDirectiva,$codigoConcepto,$codigoSocio,'totalMultaACTSocio');
				if($montoPago<$debePagar){
					$porcentajePago=($montoPago*100)/$debePagar;
				}else{
					$porcentajePago=100;
				}
				$sql="UPDATE sm_mod_asistencia SET estadoPago='SP', montoPagado='$montoPago', porcentajePago='100', caja='SI' WHERE codigoActividad='$codigoConcepto' AND codigoSocio='$codigoSocio'";
				$rs=mysqli_query($conexion,$sql);
			}

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE CAJA
			///////////////////////////////////////////////////
			$proceso=$detalleConcepto;
			$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE SOCIO
			///////////////////////////////////////////////////
			$proceso=$detalleConcepto.' &rarr; <strong>'.$codigoConcepto.' | S/. '.moneda($montoPago).'</strong>';
			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="MONTO_PAGADO";
		}else{
			$respuesta->mensaje ="ERROR_MONTO_PAGADO";
		}
	}

	if($operacion=="PAGO_MONTO_FECHA"){
		$idJuntaDirectiva = $_POST[idJuntaDirectiva];
		$nroCuota         = $_POST[nroCuota];
		$fechaPago        = fechaSQL($_POST[fechaPago]);
		$documentoPago    = $_POST[documentoPago];
		$codigoSocio      = $_POST[codigoSocio];
		$montoPago        = $_POST[montoPago];
		$totalCuotas      = infoPagoFechas($codigoSocio,$codigoConcepto,'FPSocioConcepto');
		$totalPagado      = infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasPagadas')+$montoPago;
		$lotes            = infoSocios($codigoSocio,'cantidadLotes');

		$tipoDocumento   ="VOU";
		$estadoPago      ="PGD";
		$fecha           =$fecha;
		$hora            =$hora;
		$usuario         =$dniUsuario;

		if($tipoActividad=="CUO"){
			$concepto           ="PCC";
			$infoPago           =infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota');
			$asunto             ='DE CUOTA <strong>'.$infoPago.'</strong>';
			$totalPago          =infoPago($codigoSocio,$codigoConcepto,'','montoCuotaSocio');
			$totalFechasPagadas =infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasPagadas');

			if($totalFechasPagadas>0){
				$montoPagadoFechas=$totalPagado;
				$porcentajePago=(($montoPagadoFechas*100)/$totalPago);
			}else{
				$montoPagadoFechas=$montoPago;
				$porcentajePago=(($montoPagadoFechas*100)/$totalPago);
			}
		}

		if(($tipoActividad=="ASA") or ($tipoActividad=="FAE")){
			$razon              =$_POST[razon];
			$totalPago          =infoPago($codigoSocio,$codigoConcepto,'','montoMultaSocio');
			$totalFechasPagadas =infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasPagadas');

			if($totalFechasPagadas>0){
				$montoPagadoFechas=$totalPagado;
				$porcentajePago=(($montoPagadoFechas*100)/$totalPago);
			}else{
				$montoPagadoFechas=$montoPago;
				$porcentajePago=(($montoPagadoFechas*100)/$totalPago);
			}
			
			if($tipoActividad=="ASA"){ $concepto="MCA"; }
			if($tipoActividad=="FAE"){ $concepto="MCF"; }

			if($razon=="TARDE"){ $multaPor="TARDANZA"; }
			if($razon=="FALTA"){ $multaPor="INASISTENCIA"; }

			if($tipoActividad=="ASA"){ $actividad="ASAMBLEA"; }
			if($tipoActividad=="FAE"){ $actividad="FAENA"; }

			$infoPago=infoActividad($idJuntaDirectiva,$codigoConcepto,'','temaActividad');
			$asunto  ='DE MULTA POR '.$multaPor.' A '.$actividad.' <strong>'.$infoPago.'</strong>';
		}

		if($porcentajePago >0 && $porcentajePago<100){
			$estadoPagoACT='MP';
		}else if($porcentajePago>=100){
			$estadoPagoACT='SP';
		}

		$movimiento      ="ING";
		$fechaOperacion  =$fechaPago;
		$nroDocumento    =$documentoPago;
		$monto           =$montoPago;
		$detalleConcepto ="<strong>PAGO FECHA ".ceros($nroCuota,2)." DE ".ceros($totalCuotas,2)."</strong>&nbsp;|&nbsp;".$asunto;
		$usuario         =$dniUsuario;
		$codigoOperacion =codOperacion($tipoActividad,$fecha,$hora,$usuario);
		
		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE CAJA
		///////////////////////////////////////////////////
		if($totalPagado>=$totalPago){
			if($tipoActividad=="CUO"){
				$sql="UPDATE sm_mod_cuotas_socios SET estadoPago='$estadoPagoACT', caja = 'SI', montoPagado=$montoPago, porcentajePago='$porcentajePago' WHERE codigoCuota = '$codigoConcepto' AND codigoSocio = '$codigoSocio'";
				$rs=mysqli_query($conexion,$sql);
			}
			if(($tipoActividad=="ASA") or ($tipoActividad=="FAE")){
				$sql="UPDATE sm_mod_asistencia SET estadoPago='$estadoPagoACT', montoPagado='$montoPago', porcentajePago='100', caja='SI' WHERE codigoActividad='$codigoConcepto' AND codigoSocio='$codigoSocio'";
				$rs=mysqli_query($conexion,$sql);
			}
		}

		///////////////////////////////////////////////////
		/// ACTUALIZAR DATOS - TABLA CUENTAS
		///////////////////////////////////////////////////
		$sql="UPDATE sm_mod_cuentas SET idJuntaDirectiva='$idJuntaDirectiva', tipoDocumento='$tipoDocumento', nroDocumento='$documentoPago', fechaPago='$fechaPago', estadoPago='$estadoPago' WHERE cuota='$nroCuota' AND codigoConcepto='$codigoConcepto' AND codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		/////////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE CAJA
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_mod_caja(idJuntaDirectiva, movimiento, fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, codigoBanco, codigoCuenta, codigoChequera, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$idJuntaDirectiva','$movimiento','$fechaOperacion','$tipoActividad','$concepto','$codigoSocio','$codigoConcepto','$tipoDocumento','$codigoBanco','$codigoCuenta','$codigoChequera','$nroDocumento','$monto','$detalleConcepto','$observaciones','$codigoOperacion','$fecha','$hora','$usuario')";
		$rs=mysqli_query($conexion,$sql);
		if($rs){
			if($tipoActividad=="CUO"){
				$sql="UPDATE sm_mod_cuotas_socios SET estadoPago='$estadoPagoACT', caja = 'SI', montoPagado=$montoPagadoFechas, porcentajePago='$porcentajePago' WHERE codigoCuota = '$codigoConcepto' AND codigoSocio = '$codigoSocio'";
				$rs=mysqli_query($conexion,$sql);
			}
			if(($tipoActividad=="ASA") or ($tipoActividad=="FAE")){
				$sql="UPDATE sm_mod_asistencia SET montoPagado='$montoPagadoFechas', porcentajePago='$porcentajePago' WHERE codigoActividad='$codigoConcepto' AND codigoSocio='$codigoSocio'";
				$rs=mysqli_query($conexion,$sql);
			}
		}
		
		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE CAJA
		///////////////////////////////////////////////////
		$proceso=$detalleConcepto;
		$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES CUO/ASA/FAE
		///////////////////////////////////////////////////
		$proceso=$detalleConcepto." - S/. ".moneda($montoPago);
		if($tipoActividad=="CUO"){
			$sql="INSERT INTO sm_procesos_cuotas(codigoCuota, codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoConcepto', '$codigoSocio', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);
		}

		if(($tipoActividad=="ASA") or ($tipoActividad=="FAE")){
			$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoConcepto', '$codigoSocio', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);
		}

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES SOCIOS
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$usuario')";
		$rs=mysqli_query($conexion,$sql);
		
		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);	

		$respuesta->mensaje ="FECHA_MONTO_PAGADO";
	}
	
	cerrarDB();
	echo json_encode($respuesta);
?>