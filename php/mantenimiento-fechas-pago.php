<?php
	session_start();
	include ('funciones.php');
	$conexion       =conexionDB();
	$fecha          =infoTiempo('fecha');
	$hora           =infoTiempo('hora');
	$dniUsuario     =$_SESSION['dni_apv'];
	$cuotasPago     =$_POST['cuotasPago'];
	$codigoSocio    =$_POST['codigoSocio'];
	$conceptoPago   =$_POST['conceptoPago'];
	$codigoConcepto =$_POST['codigoConcepto'];
	$operacion      =$_POST['operacion'];
	$lotes          =infoSocios($codigoSocio,'cantidadLotes');

	if($cuotasPago==2){
		$montoCuota_1 =$_POST['montoCuota1'];
		$montoCuota_2 =$_POST['montoCuota2'];
		$fechaPago_1  =fechaSQL($_POST['fechaPago1']);
		$fechaPago_2  =fechaSQL($_POST['fechaPago2']);
	}

	if($cuotasPago==3){
		$montoCuota_1 =$_POST['montoCuota1'];
		$montoCuota_2 =$_POST['montoCuota2'];
		$montoCuota_3 =$_POST['montoCuota3'];
		$fechaPago_1  =fechaSQL($_POST['fechaPago1']);
		$fechaPago_2  =fechaSQL($_POST['fechaPago2']);
		$fechaPago_3  =fechaSQL($_POST['fechaPago3']);
	}

	if($cuotasPago==4){
		$montoCuota_1 =$_POST['montoCuota1'];
		$montoCuota_2 =$_POST['montoCuota2'];
		$montoCuota_3 =$_POST['montoCuota3'];
		$montoCuota_4 =$_POST['montoCuota4'];
		$fechaPago_1  =fechaSQL($_POST['fechaPago1']);
		$fechaPago_2  =fechaSQL($_POST['fechaPago2']);
		$fechaPago_3  =fechaSQL($_POST['fechaPago3']);
		$fechaPago_4  =fechaSQL($_POST['fechaPago4']);
	}

	if($cuotasPago==5){
		$montoCuota_1 =$_POST['montoCuota1'];
		$montoCuota_2 =$_POST['montoCuota2'];
		$montoCuota_3 =$_POST['montoCuota3'];
		$montoCuota_4 =$_POST['montoCuota4'];
		$montoCuota_5 =$_POST['montoCuota5'];
		$fechaPago_1  =fechaSQL($_POST['fechaPago1']);
		$fechaPago_2  =fechaSQL($_POST['fechaPago2']);
		$fechaPago_3  =fechaSQL($_POST['fechaPago3']);
		$fechaPago_4  =fechaSQL($_POST['fechaPago4']);
		$fechaPago_5  =fechaSQL($_POST['fechaPago5']);
	}

	if($conceptoPago=="CUO"){
		$totalPago       =infoPago($codigoSocio,$codigoConcepto,'','montoCuotaSocio');
		$detalleConcepto =infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota');
	}

	if(($conceptoPago=="ASA") or ($conceptoPago=="FAE")){
		$razon     =$_POST['razon'];
		$totalPago =infoPago($codigoSocio,$codigoConcepto,'','montoMultaSocio');

		if($razon=="TARDE"){
			$multaPor="TARDANZA";
			$multa=infoActividad($idJuntaDirectiva,$codigoConcepto,'','infoMultaporTardanza');
		}
		if($razon=="FALTA"){
			$multaPor="INASISTENCIA";
			$multa=infoActividad($idJuntaDirectiva,$codigoConcepto,'','infoMultaPorFalta');
		}

		if($conceptoPago=="ASA"){ $actividad="ASAMBLEA"; }
		if($conceptoPago=="FAE"){ $actividad="FAENA"; }

		$detalleConcepto =$multaPor.' A '.$actividad.' <strong>'.infoActividad($idJuntaDirectiva,$codigoConcepto,'','temaActividad').'</strong> | <strong>MULTA: S/. '.moneda($multa).'</strong> | <strong>LOTES: '.ceros($lotes,2).'</strong> | <strong>TOTAL: S/. '.moneda($totalPago).'</strong>';
	}

	if($operacion=="PROGRAMAR_FECHAS"){
		$tipoDocumento ="";
		$nroDocumento  ="";
		$fechaPago     ="";
		$estadoPago    ="PEN";
		$fecha         =$fecha;
		$hora          =$hora;
		$usuario       =$dniUsuario;
		$cuota         =1;
		
		while($cuota<=$cuotasPago){
			$cuota           =$cuota;
			$fechaProgramada =fechaSQL($_POST['fechaPago'.$cuota]);
			$montoPago       =$_POST['montoCuota'.$cuota];
			$documentoCodigo =$cuota.'-'.$codigoSocio;
			$codigoOperacion =generaCodigo(12,$documentoCodigo);

			$sql="INSERT INTO sm_mod_cuentas(codigoSocio, conceptoPago, codigoConcepto, cuota, fechaProgramada, montoPago, tipoDocumento, nroDocumento, fechaPago, estadoPago, codigoOperacion, fecha, hora, usuario) VALUES('$codigoSocio', '$conceptoPago', '$codigoConcepto', '$cuota', '$fechaProgramada', '$montoPago', '$tipoDocumento', '$nroDocumento', '$fechaPago', '$estadoPago', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			if($conceptoPago=="CUO"){
				$sql="UPDATE sm_mod_cuotas_socios SET estadoPago='MP' WHERE codigoSocio='$codigoSocio' AND codigoCuota='$codigoConcepto'";
				$rs=mysqli_query($conexion,$sql);
			}

			if(($conceptoPago=="ASA") or ($conceptoPago=="FAE")){
				$sql="UPDATE sm_mod_asistencia SET estadoPago='MP', caja='SI' WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoConcepto'";
				$rs=mysqli_query($conexion,$sql);
				
			}
			
			$respuesta->mensaje = "PROGRAMACION_FINALIZADA";
			$cuota++;
		}
		
		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
		///////////////////////////////////////////////////
		$proceso="MONTO DE PAGO <strong>S/. ".moneda($totalPago)."</strong> POR <strong>".$detalleConcepto."</strong> FUE DIVIDIDO EN <strong>".ceros($cuotasPago,2)." FECHAS DE PAGO</strong>";
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE SOCIO
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$usuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE CUOTA
		///////////////////////////////////////////////////
		if($conceptoPago=="CUO"){
			$sql="INSERT INTO sm_procesos_cuotas(codigoCuota, codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoConcepto', '$codigoSocio', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);
		}

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE ACTIVIDADES
		///////////////////////////////////////////////////
		if(($conceptoPago=="ASA") or ($conceptoPago=="FAE")){
			$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$conceptoPago', '$codigoConcepto', '$codigoSocio', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);
		}
	}
	
	cerrarDB();
	echo json_encode($respuesta);
?>