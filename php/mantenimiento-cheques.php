<?php
	session_start();
	include ('funciones.php');
	$conexion      =conexionDB();
	$fecha         =infoTiempo('fecha');
	$hora          =infoTiempo('hora');
	$usuario       =$_SESSION['dni_apv'];
	$operacion     =$_POST['operacion'];
	mysqli_set_charset($conexion, "utf8");

	if($operacion=="REGISTRA_CHEQUE"){
		$movimiento          ="SAL";
		$tipoCheque          ="VAR";
		$fechaOperacion      =fechaSQL(mysqli_real_escape_string($conexion, $_POST['RC_fechaEmision']));
		$tipoActividad       ="";
		$concepto            ="";
		$tipoBeneficiario    =mysqli_real_escape_string($conexion, $_POST['RC_tipoBeneficiario']);
		$usuarioBeneficiario =mysqli_real_escape_string($conexion, $_POST['RC_usuarioBeneficiario']);
		$dniBeneficiario     =mysqli_real_escape_string($conexion, $_POST['RC_dniBeneficiario']);
		$nombreBeneficiario  =mysqli_real_escape_string($conexion, $_POST['RC_nombreBeneficiario']);

		if($tipoBeneficiario =="NOR"){
			$codigoSocio =$dniBeneficiario;
			$beneficiario=$dniBeneficiario;
			$nombre      =$nombreBeneficiario;
		}
		if($tipoBeneficiario =="USR"){
			$dniBeneficiario =$usuarioBeneficiario;
			$codigoSocio     =$usuarioBeneficiario;
			$beneficiario    =$usuarioBeneficiario;
			$nombre          =datoUsuario($codigoSocio,'nombreFull');
		}
		if($tipoBeneficiario =="SOC"){
			$dniBeneficiario =$usuarioBeneficiario;
			$codigoSocio     =$usuarioBeneficiario;
			$beneficiario    =$usuarioBeneficiario;
			$nombre          =infoSocios($codigoSocio,'nombre');
		}
		
		$verficaBeneficiario=infocheque('','','','','','',$dniBeneficiario,'verficaBeneficiario');
		if($dniBeneficiario==$verficaBeneficiario){
			$sql="UPDATE sm_cheques_beneficiarios SET nombre='$nombre' WHERE documento='$dniBeneficiario'";
			$rs=mysqli_query($conexion,$sql);
		}else{
			$sql="INSERT INTO sm_cheques_beneficiarios(documento, nombre) VALUES('$dniBeneficiario','$nombre')";
			$rs=mysqli_query($conexion,$sql);
		}

		$codigoConcepto      ="";
		$tipoDocumento       ="CHB";
		$codigoChequera      =mysqli_real_escape_string($conexion, $_POST['RC_codigoChequera']);
		$codigoBanco         =infoChequeras($codigoChequera,'','','entidadBancaria');
		$detalleChequera     =infoBancos($codigoBanco,'detalleEntidad').' - '.infoChequeras($codigoChequera,'','','detalleChequera');
		$codigoCuenta        =infoChequeras($codigoChequera,'','','codigoCuenta');
		$nroDocumento        =mysqli_real_escape_string($conexion, $_POST['RC_nroCheque']);
		$monto               =mysqli_real_escape_string($conexion, $_POST['RC_montoEmitido']);
		$detalleConcepto     =mysqli_real_escape_string($conexion, $_POST['RC_conceptoCheque']);
		$observaciones       =mysqli_real_escape_string($conexion,$_POST['RC_observaciones']);
		$respaldo            =$movimiento.round($monto);
		$codigoOperacion     =generaCodigo(19,$respaldo);
		$codigoCheque        =$codigoChequera.'-'.$nroDocumento;
		$proceso             ="CHEQUE EMITIDO A <strong>".$nombre."</strong>, POR: <strong> S/.".moneda($monto)."</strong> DE CHEQUERA <strong>".$detalleChequera."</strong>";

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA CAJA
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_mod_caja(movimiento, fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, codigoBanco, codigoCuenta, codigoChequera, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$movimiento', '$fechaOperacion', '$tipoActividad', '$concepto', '$codigoSocio', '$codigoConcepto', '$tipoDocumento', '$codigoBanco', '$codigoCuenta', '$codigoChequera', '$nroDocumento', '$monto', '$detalleConcepto', '$observaciones', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);
		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE CHEQUES EMITIDOS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_cheques(tipoCheque, codigoBanco, codigoCuenta, codigoChequera, nroCheque, codigoCheque, fechaEmision, monto, tipoBeneficiario, beneficiario, concepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$tipoCheque', '$codigoBanco', '$codigoCuenta', '$codigoChequera', '$nroDocumento', '$codigoCheque', '$fechaOperacion', '$monto', '$tipoBeneficiario', '$beneficiario', '$detalleConcepto', '$observaciones', '$codigoOperacion', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);
			if($rs){
				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CHEQUERA
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_cheques_operaciones(codigoChequera, nroCheque, proceso, monto, fecha, hora, usuario) VALUES('$codigoChequera', '$nroDocumento', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CHEQUERA
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_chequera_operaciones(codigoChequera, proceso, fecha, hora, usuario) VALUES('$codigoChequera', '$proceso', $fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CAJA
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				$respuesta->mensaje ="CHEQUE_REGISTADO";
			}
		}else{ $respuesta->mensaje ="ERROR_CHEQUE_REGISTADO"; }
	}

	if($operacion=="NOMBRE_BENEFICIARIO"){
		$dniBeneficiario=mysqli_real_escape_string($conexion, $_POST['dniBeneficiario']);
		$verficaBeneficiario=infocheque('','','','','','',$dniBeneficiario,'verficaBeneficiario');
		if($dniBeneficiario==$verficaBeneficiario){
			$nombreBeneficiario=infocheque('','','','','','',$dniBeneficiario,'nombreBeneficiario');
			$respuesta->nombreBeneficiario =$nombreBeneficiario;
		}else{
			$respuesta->nombreBeneficiario ="";
		}
	}

	if($operacion=="ELIMINA_CHEQUE"){
		$codigoBanco     =mysqli_real_escape_string($conexion, $_POST['codigoBanco']);
		$codigoCuenta    =mysqli_real_escape_string($conexion, $_POST['codigoCuenta']);
		$codigoChequera  =mysqli_real_escape_string($conexion, $_POST['codigoChequera']);
		$nroCheque       =mysqli_real_escape_string($conexion, $_POST['nroCheque']);
		$beneficiario    =mysqli_real_escape_string($conexion, $_POST['beneficiario']);
		$monto           =mysqli_real_escape_string($conexion, $_POST['monto']);
		$codigoOperacion =mysqli_real_escape_string($conexion, $_POST['codigoOperacion']);
		$detalleChequera =infoBancos($codigoBanco,'detalleEntidad').' - '.infoChequeras($codigoChequera,'','','detalleChequera');
		$nombre          =infocheque('','','','','','',$beneficiario,'nombreBeneficiario');
		$proceso         ="<strong>CHEQUE ELIMINADO #".$nroCheque."</strong>, EMITIDO A <strong>".$nombre."</strong>, POR: <strong> S/. ".moneda($monto)."</strong> DE CHEQUERA <strong>".$detalleChequera."</strong>";


		///////////////////////////////////////////////////
		/// ELIMINAR CHEQUE DE CAJA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_caja WHERE tipoDocumento='CHB' AND codigoBanco='$codigoBanco' AND codigoCuenta='$codigoCuenta' AND codigoChequera='$codigoChequera' AND nroDocumento='$nroCheque'";
		$rs=mysqli_query($conexion,$sql);
		if($rs){
			///////////////////////////////////////////////////
			/// ELIMINAR CHEQUE DE TABLA DE CHEQUES
			///////////////////////////////////////////////////
			$sql="DELETE FROM sm_cheques WHERE codigoBanco='$codigoBanco' AND codigoCuenta='$codigoCuenta' AND codigoChequera='$codigoChequera' AND nroCheque='$nroCheque'";
			$rs=mysqli_query($conexion,$sql);
			if($rs){
				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CHEQUERA
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_cheques_operaciones(codigoChequera, nroCheque, proceso, monto, fecha, hora, usuario) VALUES('$codigoChequera', '$nroCheque', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CHEQUERA
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_chequera_operaciones(codigoChequera, proceso, fecha, hora, usuario) VALUES('$codigoChequera', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES CAJA
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso', '$monto', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				///////////////////////////////////////////////////
				/// INSERTAR DATOS EN TABLA DE OPERACIONES USUARIO
				///////////////////////////////////////////////////
				$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				$respuesta->mensaje ="CHEQUE_ELIMINADO";
			}else{
				$respuesta->mensaje ="ERROR_CHEQUE_ELIMINADO";
			}
		}
	}

	echo json_encode($respuesta);
?>