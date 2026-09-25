<?php
	session_start();
	include ('funciones.php');
	$conexion    =conexionDB();
	$fecha       =infoTiempo('fecha');
	$hora        =infoTiempo('hora');
	$dniUsuario  =$_SESSION['dni_apv'];
	$respuesta   =new \stdClass();
	$operacion   =$_POST['operacion'];

	if($operacion=="REGISTRA_CUOTA"){
		$conceptoCuota =utf8_decode($_POST['conceptoCuota']);
		$montoCuota    =$_POST['montoCuota'];
		$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
		$codigoCuenta     = $_POST['codigoCuenta'];
		$fechaPago     =fechaSQL($_POST['fechaPago']);
		$observacion   =utf8_decode($_POST['observacion']);
		$fecha         =$fecha;
		$hora          =$hora;
		$usuario       =$dniUsuario;
		$codigoCuota   =codCuota($_POST['fechaPago'],$usuario);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE CUOTAS
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_mod_cuotas(codigoCuota, conceptoCuota, montoCuota, idJuntaDirectiva, codigoCuenta, fechaPago, observacion, fecha, hora, usuario) VALUES('$codigoCuota', '$conceptoCuota', '$montoCuota', '$idJuntaDirectiva', '$codigoCuenta', '$fechaPago', '$observacion', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE SOCIOS
			///////////////////////////////////////////////////
			$sql="SELECT codigoSocio FROM sm_socios";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoSocio =$n['codigoSocio'];
				$lotes       =infoSocios($codigoSocio,'cantidadLotes');
				$montoPago   =$lotes*$montoCuota;
				$estadoPago  ="NP";
				$detalleCuota=$conceptoCuota;

				$sql="INSERT sm_mod_cuotas_socios(idJuntaDirectiva, codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago) VALUES('$idJuntaDirectiva', '$codigoCuota', '$codigoSocio', '$lotes', '$montoCuota', '$montoPago', '$estadoPago')";
				$asistencia=mysqli_query($conexion,$sql);

				$proceso="CUOTA AGREGADA A SOCIO - <strong>".$detalleCuota."</strong> &nbsp;|&nbsp; <strong>CUOTA S/.</strong> ".moneda($montoCuota);
				$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
				$bitacora=mysqli_query($conexion,$sql);

				$i++; 
			}

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE CUOTAS
			///////////////////////////////////////////////////
			$codigoSocio='';
	    	$proceso="CUOTA GENERADA - <strong>".$detalleCuota."</strong> &nbsp;|&nbsp; <strong>CUOTA S/.</strong> ".moneda($montoCuota);
			$sql="INSERT INTO sm_procesos_cuotas(codigoCuota, codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoCuota', '$codigoSocio', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);
			
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="CUOTA_ALMACENADA";
		}else{
			$respuesta->mensaje ="ERROR_CUOTA_ALMACENADA";
		}
	}

	if($operacion=="EDITA_CUOTA"){
		$codigoCuota   =$_POST['codigoCuota'];
		$conceptoCuota =utf8_decode($_POST['conceptoCuota']);
		$montoCuota    =$_POST['montoCuota'];
		$codigoCuenta  =$_POST['codigoCuenta'];
		$fechaPago     =fechaSQL($_POST['fechaPago']);
		$observacion   =utf8_decode($_POST['observacion']);
		$fecha         =$fecha;
		$hora          =$hora;
		$usuario       =$dniUsuario;

		$respuesta->informacion = $codigoCuota;

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE CUOTAS
		///////////////////////////////////////////////////
		$sql="UPDATE sm_mod_cuotas SET conceptoCuota='$conceptoCuota', montoCuota='$montoCuota', codigoCuenta='$codigoCuenta', fechaPago='$fechaPago', observacion='$observacion', fecha='$fecha', hora='$hora', usuario='$usuario' WHERE codigoCuota='$codigoCuota'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
			///////////////////////////////////////////////////
	    	$proceso="CUOTA DATOS MODIFICADOS - <strong>".$detalleCuota."</strong>";
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="CUOTA_ALMACENADA";
		}else{
			$respuesta->mensaje ="ERROR_CUOTA_ALMACENADA";
		}
	}

	if($operacion=="ELIMINAR_CUOTA"){
		$codigoCuota   =$_POST['codigoCuota'];
		$fecha         =$fecha;
		$hora          =$hora;
		$usuario       =$dniUsuario;
		$conceptoCuota =infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota');
		$montoCuota    =infoCuota($idJuntaDirectiva,$codigoCuota,'montoCuota');

		///////////////////////////////////////////////////
		/// ELIMINAR CUOTA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_cuotas WHERE codigoCuota='$codigoCuota'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// ELIMINAR CUOTA DE SOCIOS
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_cuotas_socios WHERE codigoCuota='$codigoCuota'";
		$rs=mysqli_query($conexion,$sql);

		$sql="SELECT codigoSocio FROM sm_socios";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$codigoSocio =$n['codigoSocio'];
			$lotes       =infoSocios($codigoSocio,'cantidadLotes');
			$totalCuota  =$montoCuota*$lotes;
			$proceso     ="ELIMINADO DE CUOTA - <strong>".$conceptoCuota."</strong> | <strong>MONTO:</strong> S/. ".moneda($montoCuota)." | <strong>LOTES:</strong> ".ceros($lotes,2)." | <strong>TOTAL CUOTA:</strong> S/. ".moneda($totalCuota);

			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$usuario')";
			$bitacora=mysqli_query($conexion,$sql);

			$i++; 
		}

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
		///////////////////////////////////////////////////
		$proceso="CUOTA ELIMINADA - <strong>".$conceptoCuota."</strong>";
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="CUOTA_ELIMINADA";
	}

	if($operacion=="ELIMINAR_CUOTA_EMERGENCIA"){
		$codigoCuota   =$_POST['codigoCuota'];
		$fecha         =$fecha;
		$hora          =$hora;
		$usuario       =$dniUsuario;
		$conceptoCuota =infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota');
		$montoCuota    =infoCuota($idJuntaDirectiva,$codigoCuota,'montoCuota');

		///////////////////////////////////////////////////
		/// ELIMINAR CUOTA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_cuotas WHERE codigoCuota='$codigoCuota'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// ELIMINAR CUOTA DE SOCIOS
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_cuotas_socios WHERE codigoCuota='$codigoCuota'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// ELIMINAR CUOTA DE CAJA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_caja WHERE codigoConcepto='$codigoCuota'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// ELIMINAR CUOTA DE CAJA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_cuentas WHERE codigoConcepto='$codigoCuota'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// ELIMINAR CUOTA DE CAJA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_procesos_cuotas WHERE codigoCuota='$codigoCuota'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
		///////////////////////////////////////////////////
		$proceso="CUOTA ELIMINADA - <strong>".$conceptoCuota."</strong>";
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="CUOTA_ELIMINADA";
	}

	
	if($operacion=="ACTUALIZA_SOCIOS_CUOTA"){
		$codigoCuota   =$_POST['codigoCuota'];

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE CUOTAS
		///////////////////////////////////////////////////
		$sql="SELECT codigoSocio FROM sm_socios";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$codigoSocio   =$n['codigoSocio'];
			$lotes         =infoSocios($codigoSocio,'cantidadLotes');
			$montoCuota    =infoCuota($idJuntaDirectiva,$codigoCuota,'montoCuota');
			$montoPago     =$lotes*$montoCuota;
			$estadoPago    ="NP";
			$verificaSocio =verificaSocioACT($codigoCuota,$codigoSocio,'verificaSocioCUO');

			if($codigoSocio==$verificaSocio){ }else{
				$sql="INSERT sm_mod_cuotas_socios(codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago) VALUES('$codigoCuota', '$codigoSocio', '$lotes', '$montoCuota', '$montoPago', '$estadoPago')";
				$asistencia=mysqli_query($conexion,$sql);
				if($asistencia){
					$proceso="CUOTA AGREGADA A SOCIO - <strong>".$detalleCuota."</strong> &nbsp;|&nbsp; <strong>CUOTA S/.</strong> ".moneda($montoCuota);
					$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
					$bitacora=mysqli_query($conexion,$sql);
				}else{}
			}

			$i++; 
		}
		$respuesta->mensaje ="SOCIOS_CUOTA_ACTUALIZADO";
	}

	cerrarDB();
	echo json_encode($respuesta);
?>