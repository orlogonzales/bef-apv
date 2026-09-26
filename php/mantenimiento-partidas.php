<?php
	session_start();
	include ('funciones.php');
	$conexion      =conexionDB();
	$fecha         =infoTiempo('fecha');
	$hora          =infoTiempo('hora');
	$dniUsuario    =$_SESSION['dni_apv'];
	$operacion     =$_POST['operacion'];
	mysqli_set_charset($conexion, "utf8");

	if($operacion=="REGISTRA_PARTIDA"){
		$movimiento      ="PAR";
		$tipoCheque      ="PAR";
		$usuarioPartida  =$_POST['usuarioPartida'];
		$concepto        =mysqli_real_escape_string($conexion,$_POST['conceptoPartida']);
		$monto           =$_POST['montoPartida'];
		$codigoChequera  =$_POST['codigoChequera'];
		$codigoBanco     =infoChequeras($codigoChequera,'','','entidadBancaria');
		$detalleChequera =infoBancos($codigoBanco,'detalleEntidad').' - '.infoChequeras($codigoChequera,'','','detalleChequera');
		$codigoCuenta    =infoChequeras($codigoChequera,'','','codigoCuenta');
		$nroCheque       =$_POST['nroCheque'];
		$fechaPartida    =fechaSQL($_POST['fechaPartida']);
		$observaciones   =mysqli_real_escape_string($conexion,$_POST['observaciones']);
		$fechaCierre     ="";
		$estado          ="OPN";
		$fecha           =$fecha;
		$hora            =$hora;
		$usuario         =$dniUsuario;
		$codigoPartida   =codOperacion($movimiento,$fechaPartida,$hora,$codigoSocio);
		$fechaOperacion  =$fechaPartida;
		$tipoActividad   ="";
		$codigoSocio     =$usuarioPartida;
		$beneficiario    =$codigoSocio;
		$tipoBeneficiario=mysqli_real_escape_string($conexion,$_POST['tipoResposanble']);
		$codigoConcepto  =$codigoPartida;
		$tipoDocumento   ="CHB";
		$nroDocumento    =$nroCheque;
		$detalleConcepto =$concepto;
		$respaldo        =$movimiento.round($monto);
		$codigoOperacion =generaCodigo(19,$respaldo);
		$codigoCheque    =$codigoChequera.'-'.$nroDocumento;
		$proceso         ="PARTIDA GENERADA - ".$concepto;
		$procesoCHB      ="CHEQUE EMITIDO - PARTIDA <strong>".$detalleConcepto."</strong> - CHEQUERA <strong>".$detalleChequera."</strong>";


		if($tipoBeneficiario =="USR"){
			$nombre=datoUsuario($codigoSocio,'nombreFull');
		}
		if($tipoBeneficiario =="SOC"){
			$nombre=infoSocios($codigoSocio,'nombre');
		}
		
		$verficaBeneficiario=infocheque('','','','','','',$beneficiario,'verficaBeneficiario');
		if($beneficiario==$verficaBeneficiario){
			$sql="UPDATE sm_cheques_beneficiarios SET nombre='$nombre' WHERE documento='$beneficiario'";
			$rs=mysqli_query($conexion,$sql);
		}else{
			$sql="INSERT INTO sm_cheques_beneficiarios(documento,nombre) VALUES('$beneficiario','$nombre')";
			$rs=mysqli_query($conexion,$sql);
		}

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE PARTIDAS
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_partidas(codigoPartida, usuarioPartida, concepto, monto, codigoCuenta, codigoChequera, nroCheque, fechaPartida, fechaCierre, observaciones, estado, codigoOperacion, fecha, hora, usuario) VALUES('$codigoPartida', '$usuarioPartida', '$concepto', '$monto', '$codigoCuenta', '$codigoChequera', '$nroCheque', '$fechaPartida', '$fechaCierre', '$observaciones', '$estado', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);
		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES PARTIDAS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_procesos_partidas(codigoPartida, proceso, monto, fecha, hora, usuario) VALUES('$codigoPartida', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE CHEQUES EMITIDOS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_cheques(tipoCheque, codigoBanco, codigoCuenta, codigoChequera, nroCheque, codigoCheque, fechaEmision, monto, tipoBeneficiario, beneficiario, concepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$tipoCheque', '$codigoBanco', '$codigoCuenta', '$codigoChequera', '$nroDocumento', '$codigoCheque', '$fechaOperacion', '$monto', '$tipoBeneficiario', '$beneficiario', '$detalleConcepto', '$observaciones', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES CHEQUERA
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_cheques_operaciones(codigoChequera, nroCheque, proceso, monto, fecha, hora, usuario) VALUES('$codigoChequera', '$nroDocumento', '$procesoCHB', '$monto', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE CAJA
			///////////////////////////////////////////////////
			$concepto        ='';
			$sql="INSERT INTO sm_mod_caja(movimiento, fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, codigoBanco, codigoCuenta, codigoChequera, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$movimiento', '$fechaOperacion', '$tipoActividad', '$concepto', '$codigoSocio', '$codigoConcepto', '$tipoDocumento', '$codigoBanco', '$codigoCuenta', '$codigoChequera', '$nroDocumento', '$monto', '$detalleConcepto', '$observaciones', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES CAJA
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$procesoCHB', '$monto', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="PARTIDA_ALMACENADA";
		}else{ $respuesta->mensaje ="ERROR_PARTIDA_ALMACENADA"; }
	}

	if($operacion=="EDITAR_PARTIDA"){
		$codigoPartida   =$_POST['codigoPartida'];
		$usuarioPartida  =$_POST['usuarioPartida'];
		$concepto        =utf8_decode($_POST['conceptoPartida']);
		$monto           =$_POST['monto'];
		$fechaPartida    =fechaSQL($_POST['fechaPartida']);
		$fechaCierre     ="";
		$estado          =$_POST['estado'];
		$fecha           =$fecha;
		$hora            =$hora;
		$usuario         =$dniUsuario;
		
		$fechaOperacion  =$fechaPartida;
		$codigoSocio     =$usuarioPartida;
		$codigoConcepto  =$codigoPartida;
		$tipoDocumento   ="";
		$nroDocumento    ="";
		$detalleConcepto =$concepto;
		$proceso         ="PARTIDA MODIFICADA - ".$concepto;
		
		$sql="UPDATE sm_partidas SET usuarioPartida='$usuarioPartida', concepto='$concepto', monto='$monto', fechaPartida='$fechaPartida', estado='$estado', fecha='$fecha', hora='$hora', usuario='$dniUsuario' WHERE codigoPartida='$codigoPartida'";
		$rs=mysqli_query($conexion,$sql);

		$sql="UPDATE sm_mod_caja SET fechaOperacion='$fechaOperacion', codigoSocio='$codigoSocio', codigoConcepto='$codigoConcepto', monto='$monto', detalleConcepto='$detalleConcepto', fecha='$fecha', hora='$hora', usuario='$usuario' WHERE codigoConcepto='$codigoConcepto'";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_procesos_partidas(codigoPartida, proceso, monto, fecha, hora, usuario) VALUES('$codigoPartida', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);
		
		$respuesta->mensaje ="PARTIDA_MODIFICADA";
	}

	if($operacion=="ELIMINAR_PARTIDA"){
		$codigoPartida   =$_POST['codigoPartida'];
		$fecha           =$fecha;
		$hora            =$hora;
		$usuario         =$dniUsuario;
		$conceptoPartida =infoPartida($codigoPartida,'conceptoPartida');
		$monto           =infoPartida($codigoPartida,'montoPartida');
		$usuarioPartida  =infoPartida($codigoPartida,'usuarioPartida');
		$concepto        =$conceptoPartida." - <strong>RESPONSABLE:</strong> ".datoUsuario($usuarioPartida,'nombrePaterno');
		$proceso         ="PARTIDA ELIMINADA - ".$concepto;

		///////////////////////////////////////////////////
		/// ELIMINA DATOS TABLA PARTIDAS
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_partidas WHERE codigoPartida='$codigoPartida'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// ELIMINA DATOS DE TABLA CAJA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_caja WHERE codigoConcepto='$codigoPartida'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES PARTIDAS
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_procesos_partidas(codigoPartida, proceso, monto, fecha, hora, usuario) VALUES('$codigoPartida', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES USUARIOS
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);
		
		$respuesta->mensaje ="PARTIDA_ELIMINADA";
	}
	
	cerrarDB();
	echo json_encode($respuesta);
?>