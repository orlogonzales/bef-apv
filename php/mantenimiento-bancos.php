<?php
	session_start();
	include ('funciones.php');
	$conexion      =conexionDB();
	$fecha         =infoTiempo('fecha');
	$hora          =infoTiempo('hora');
	$usuario       =$_SESSION['dni_apv'];
	$operacion     =$_POST['operacion'];
	$respuesta     =new \stdClass();
	mysqli_set_charset($conexion, "utf8");

	if($operacion=="REGISTRA_BANCO"){
		$entidad     =mysqli_real_escape_string($conexion, $_POST['entidad']);
		$codigoBanco =codigoBanco();

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA BANCOS
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_bancos(codigoBanco, entidad, fecha, hora, usuario) VALUES('$codigoBanco', '$entidad', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES BANCOS
		///////////////////////////////////////////////////
		$proceso="ENTIDAD BANCARIA REGISTRADA - <strong>".$entidad."<strong>";
		$sql="INSERT INTO sm_bancos_operaciones(codigoBanco, proceso, fecha, hora, usuario) VALUES('$codigoBanco', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="ENTIDAD_REGISTRADA";
	}

	if($operacion=="ELIMINAR_BANCO"){
		$codigoBanco =$_POST['codigoBanco'];
		$entidad=infoBancos($codigoBanco,'detalleEntidad');

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA BANCOS
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_bancos WHERE codigoBanco='$codigoBanco'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES BANCOS
		///////////////////////////////////////////////////
		$proceso="ENTIDAD BANCARIA ELIMINADA - <strong>".$entidad."<strong>";
		$sql="INSERT INTO sm_bancos_operaciones(codigoBanco, proceso, fecha, hora, usuario) VALUES('$codigoBanco', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="BANCO_ELIMINADO";
	}

	if($operacion=="REGISTRA_CUENTA"){
		$codigoBanco    =$_POST['codigoBanco'];
		$numeroCuenta   =$_POST['numeroCuenta'];

		$detalle        =mysqli_real_escape_string($conexion, $_POST['detalle']);
		$nroCTAS        =infoCuentas('','','cuentasRegistradas')+1;
		$codigoCuenta   ="CTA".ceros($nroCTAS,3)."-".$codigoBanco;
		$estado         ="ACT";
		$detalleEntidad =infoBancos($codigoBanco,'detalleEntidad');
		$infoCTA        =$detalleEntidad." - CUENTA: ".$numeroCuenta." CODIGO: ".$codigoCuenta;
		$verificaCTA    =infoCuentas($codigoCuenta,'','verificaCTA');

		if($verificaCTA==$codigoCuenta){ $respuesta->mensaje ="CUENTA_EXISTE"; }else{
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA CUENTAS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_banco_cuentas(codigoCuenta, codigoBanco, numeroCuenta, detalle, fecha, hora, usuario, estado) VALUES('$codigoCuenta', '$codigoBanco', '$numeroCuenta', '$detalle', '$fecha', '$hora', '$usuario', '$estado')";
			$rs=mysqli_query($conexion,$sql);

			if($rs){
				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CUENTAS
				///////////////////////////////////////////////////
				$proceso="CUENTA REGISTRADA - <strong>".$infoCTA."<strong>";
				$sql="INSERT INTO sm_banco_cuentas_operaciones(codigoCuenta, proceso, fecha, hora, usuario) VALUES('$codigoCuenta', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES BANCOS
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_bancos_operaciones(codigoBanco, proceso, fecha, hora, usuario) VALUES('$codigoBanco', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				$respuesta->mensaje ="CUENTA_REGISTRADA";
			}else{
				$respuesta->mensaje ="ERROR_REGISTRO";
			}
		}
	}

	if($operacion=="ELIMINAR_CUENTA"){
		$codigoBanco  =$_POST['codigoBanco'];
		$codigoCuenta =$_POST['codigoCuenta'];
		$cuenta       =infoCuentas($codigoCuenta,'','detalleCuenta');
		$entidad      =infoBancos($codigoBanco,'detalleEntidad');

		///////////////////////////////////////////////////
		/// ELIMINAR CUENTA DE BANCO
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_banco_cuentas WHERE codigoCuenta='$codigoCuenta'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES CUENTAS
			///////////////////////////////////////////////////
			$proceso="CUENTA BANCARIA ELIMINADA - <strong>".$cuenta." / ".$entidad."<strong>";
			$sql="INSERT INTO sm_banco_cuentas_operaciones(codigoCuenta, proceso, fecha, hora, usuario) VALUES('$codigoCuenta', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES BANCOS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_bancos_operaciones(codigoBanco, proceso, fecha, hora, usuario) VALUES('$codigoBanco', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="CUENTA_ELIMINADA";
		}else{ $respuesta->mensaje ="ERROR_CUENTA_ELIMINADA"; }
	}

	if($operacion=="CAMBIAR_ESTADO_CUENTA"){
		$codigoBanco   =$_POST['codigoBanco'];
		$codigoCuenta  =$_POST['codigoCuenta'];
		$estado        =$_POST['estado'];
		$entidad       =infoBancos($codigoBanco,'detalleEntidad');
		$detalleCuenta =infoCuentas($codigoCuenta,'','detalleCuenta');
		$numeroCuenta  =infoCuentas($codigoCuenta,'','numeroCuenta');
		$infoCuenta    =$detalleCuenta." - CUENTA NRO: ".$numeroCuenta." / ".$entidad;

		if($estado=="ACT"){ $rotulo="ACTIVADA"; }
		if($estado=="INC"){ $rotulo="DESACTIVADA"; }

		///////////////////////////////////////////////////
		/// CAMBIAR ESTADO DE CUENTA
		///////////////////////////////////////////////////
		$sql="UPDATE sm_banco_cuentas SET estado='$estado' WHERE codigoCuenta='$codigoCuenta'";
		$rs=mysqli_query($conexion,$sql);
		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES CUENTAS
			///////////////////////////////////////////////////
			$proceso="CUENTA ".$rotulo." - <strong>".$infoCuenta."<strong>";
			$sql="INSERT INTO sm_banco_cuentas_operaciones(codigoCuenta, proceso, fecha, hora, usuario) VALUES('$codigoCuenta', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES BANCOS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_bancos_operaciones(codigoBanco, proceso, fecha, hora, usuario) VALUES('$codigoBanco', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="ESTADO_CAMBIADO";
		}else{
			$respuesta->mensaje ="ERROR_ESTADO_CAMBIADO";
		}
	}

	if($operacion=="REGISTRA_CHEQUERA"){
		$codigoBanco    =$_POST['codigoBanco'];
		$codigoCuenta   =$_POST['codigoCuenta'];
		$nroChequera    =$_POST['nroChequera'];
		$codigoChequera ="T".ceros($nroChequera,5)."-".$codigoCuenta;
		$detalle        =mysqli_real_escape_string($conexion, $_POST['detalle']);
		$estado         ="ACT";
		$detalleEntidad =infoBancos($codigoBanco,'detalleEntidad');
		$detalleCuenta  =infoCuentas($codigoCuenta,$codigoBanco,'detalleCuenta');
		$infoChequera   =$detalleEntidad." - TALONARIO ".$codigoChequera." / CUENTA ".$detalleCuenta;
		$existeChequera =infoChequeras($codigoChequera,'','','verificaChequera');

		if($codigoChequera==$existeChequera){ $respuesta->mensaje ="CHEQUERA_EXISTE"; }else{
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA CHEQUERAS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_chequera(codigoChequera, codigoBanco, codigoCuenta, detalle, fecha, hora, usuario, estado) VALUES('$codigoChequera', '$codigoBanco', '$codigoCuenta', '$detalle', '$fecha', '$hora', '$usuario', '$estado')";
			$rs=mysqli_query($conexion,$sql);

			if($rs){
				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CHEQUERAS
				///////////////////////////////////////////////////
				$proceso="CHEQUERA REGISTRADA - <strong>".$infoChequera."<strong>";
				$sql="INSERT INTO sm_chequera_operaciones(codigoChequera, proceso, fecha, hora, usuario) VALUES('$codigoChequera','$proceso','$fecha','$hora','$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CUENTAS
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_banco_cuentas_operaciones(codigoCuenta, proceso, fecha, hora, usuario) VALUES('$codigoCuenta', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES BANCOS
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_bancos_operaciones(codigoBanco, proceso, fecha, hora, usuario) VALUES('$codigoBanco', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				$respuesta->mensaje ="CHEQUERA_REGISTRADA";
			}else{ $respuesta->mensaje ="ERROR_CHEQUERA_REGISTRADA"; }
		}
	}

	if($operacion=="CAMBIAR_ESTADO_CHEQUERA"){
		$codigoBanco    =$_POST['codigoBanco'];
		$codigoChequera =$_POST['codigoChequera'];
		$estado         =$_POST['estado'];
		$detalleEntidad =infoBancos($codigoBanco,'detalleEntidad');
		$infoChequera   =$detalleEntidad." - CHEQUERA ".$codigoChequera;

		if($estado=="ACT"){ $rotulo="ACTIVADA"; }
		if($estado=="INC"){ $rotulo="DESACTIVADA"; }

		///////////////////////////////////////////////////
		/// CAMBIAR DATO DE CHEQUERAS
		///////////////////////////////////////////////////
		$sql="UPDATE sm_chequera SET estado='$estado' WHERE codigoChequera='$codigoChequera'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES CHEQUERAS
		///////////////////////////////////////////////////
		$proceso="CHEQUERA ".$rotulo." - <strong>".$infoChequera."<strong>";
		$sql="INSERT INTO sm_chequera_operaciones(codigoChequera, proceso, fecha, hora, usuario) VALUES('$codigoChequera','$proceso','$fecha','$hora','$usuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="ESTADO_CAMBIADO";
	}

	if($operacion=="ELIMINAR_CHEQUERA"){
		$codigoBanco    =$_POST['codigoBanco'];
		$codigoChequera =$_POST['codigoChequera'];
		$detalleEntidad =infoBancos($codigoBanco,'detalleEntidad');
		$detalleChquera =infoChequeras($codigoChequera,'','','detalleChequera');
		$infoChequera   =$detalleEntidad." - CHEQUERA ".$detalleChquera." / ".$codigoChequera;

		///////////////////////////////////////////////////
		/// ELIMINAR CHEQUERA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_chequera WHERE codigoChequera='$codigoChequera'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES CHEQUERAS
			///////////////////////////////////////////////////
			$proceso="CHEQUERA ELIMINADA - <strong>".$infoChequera."<strong>";
			$sql="INSERT INTO sm_chequera_operaciones(codigoChequera, proceso, fecha, hora, usuario) VALUES('$codigoChequera','$proceso','$fecha','$hora','$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES CUENTAS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_banco_cuentas_operaciones(codigoCuenta, proceso, fecha, hora, usuario) VALUES('$codigoCuenta', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES BANCOS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_bancos_operaciones(codigoBanco, proceso, fecha, hora, usuario) VALUES('$codigoBanco', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="CHEQUERA_ELIMINADA";
		}else{ $respuesta->mensaje ="ERROR_CHEQUERA_ELIMINADA"; }
	}

	cerrarDB();
	echo json_encode($respuesta);
?>