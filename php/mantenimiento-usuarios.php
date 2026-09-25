<?php
	session_start();
	include ('funciones.php');
	$conexion   =conexionDB();
	$fecha      =infoTiempo('fecha');
	$hora       =infoTiempo('hora');
	$dniUsuario =$_SESSION['dni_apv'];
	$operacion  =$_POST['operacion'];
	$usuario    =$dniUsuario;

	if($operacion=="CERRAR_SESION"){
		session_start();
		unset($_SESSION['login_apv']);
		unset($_SESSION['usuario_apv']);
		unset($_SESSION['dni_apv']);
		unset($_SESSION['foto_apv']);
		unset($_SESSION['rol_apv']);
		session_unset();
		session_destroy();

		if($_SESSION['login_apv']==''){
			session_start();
			unset($_SESSION['login_apv']);
			unset($_SESSION['usuario_apv']);
			unset($_SESSION['dni_apv']);
			unset($_SESSION['foto_apv']);
			unset($_SESSION['rol_apv']);
			session_unset();
			session_destroy();
			$respuesta->estado  ='SESION_CERRADA';
		}else{
			session_start();
			unset($_SESSION['login_apv']);
			unset($_SESSION['usuario_apv']);
			unset($_SESSION['dni_apv']);
			unset($_SESSION['foto_apv']);
			unset($_SESSION['rol_apv']);
			session_unset();
			session_destroy();
			$respuesta->estado  ='ERROR_SESION_CERRADA';
		}
	}
	
	if($operacion=="VERIFICA_ACTIVIDAD_USUARIO"){
		$usuario=$_POST['usuario'];
		$sesion=verificaEstadoActividad($usuario);
		$respuesta->sesion =$sesion;
	}

	if($operacion=="ELIMINA_USUARIO"){
		$dni     =$_POST['dni'];

		$sql="DELETE FROM sm_usuarios WHERE dni='$dni'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$respuesta->resultado='ELIMINADO';
		}else{
			$respuesta->resultado='ERROR';
		}
	}

	if($operacion=="CAMBIA_CLAVE"){
		$dni   =$_POST['dni'];
		$clave =md5($_POST['clave']);
		$usuario=$dniUsuario;

		$sql="UPDATE sm_usuarios SET clave='$clave' WHERE dni='$dni'";
		$rs=mysqli_query($conexion,$sql);

		$proceso="CAMBIO DE CLAVE DE USUARIO";
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dni', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="CLAVE_CAMBIADA";
	}

	if($operacion=="CAMBIA_ESTADO"){
		$dni     =$_POST['dni'];
		$estado  =$_POST['estado'];
		$usuario =$dniUsuario;

		if($estado=="DESACTIVA"){
			$estadoUsuario="INC";
			$proceso="PRIVILEGIOS DEL USUARIO FUERON REVOCADOS DEL SISTEMA";
			$mensaje="USUARIO_REVOCADO";
		}
		if($estado=="ACTIVA"){
			$estadoUsuario="ACT";
			$proceso="PRIVILEGIOS DEL USUARIO FUERON ACTIVADOS EN EL SISTEMA";
			$mensaje="USUARIO_ACTIVADO";
		}

		$sql="UPDATE sm_usuarios SET estado='$estadoUsuario' WHERE dni='$dni'";
		$rs=mysqli_query($conexion,$sql);

		
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dni', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje = $mensaje;
	}

	if($operacion=="VERIFICA_DNI"){
		$dni       =$_POST['dni'];
		$existeDNI =datoUsuario($dni,'verificaDNI');
		if($existeDNI==$dni){ $respuesta->mensaje ="DNI_EXISTE"; }else{ $respuesta->mensaje ="DNI_NO_EXISTE"; }
	}

	if($operacion=="VERIFICA_USER"){
		$usuario       =$_POST['usuario'];
		$existeUSER    =datoUsuario($usuario,'verificaUSER');
		if($existeUSER==$usuario){ $respuesta->mensaje="EXISTE_USER"; }else{  $respuesta->mensaje="NO_EXISTE_USER"; }
	}

	if($operacion=="REGISTRA_USUARIO"){
		$dni           =$_POST['UsuarioDNI'];
		$nombre        =utf8_decode($_POST['usuarioNombre']);
		$paterno       =utf8_decode($_POST['usuarioPaterno']);
		$materno       =utf8_decode($_POST['usuarioMaterno']);
		$foto          ="";
		$genero        =$_POST['usuarioGenero'];
		$email         =$_POST['usuarioEmail'];
		$telefono      =$_POST['usuarioTelefono'];
		$rol           =$_POST['usuarioRol'];
		$usuario       =$_POST['usuarioLogin'];
		$clave         =md5($_POST['usuarioClave']);
		$estado        ='ACT';
		$fechaRegistro =$fecha;
		
		$sql="INSERT INTO sm_usuarios(dni, nombre, paterno, materno, foto, genero, email, telefono, rol, usuario, clave, estado, fechaRegistro) VALUES('$dni', '$nombre', '$paterno', '$materno', '$foto', '$genero', '$email', '$telefono', '$rol', '$usuario', '$clave', '$estado', '$fechaRegistro')";
		$rs=mysqli_query($conexion,$sql);

		$proceso="USUARIO REGISTRADO EN EL SISTEMA";
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dni', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="USUARIO_REGISTRADO";
	}

	if($operacion=="MODIFICA_USUARIO"){
		$dni           =$_POST['UsuarioDNI'];
		$nombre        =utf8_decode($_POST['usuarioNombre']);
		$paterno       =utf8_decode($_POST['usuarioPaterno']);
		$materno       =utf8_decode($_POST['usuarioMaterno']);
		$genero        =$_POST['usuarioGenero'];
		$email         =$_POST['usuarioEmail'];
		$telefono      =$_POST['usuarioTelefono'];
		$rol           =$_POST['usuarioRol'];
		$fechaRegistro =$fecha;

		$sql="UPDATE sm_usuarios SET nombre='$nombre', paterno='$paterno', materno='$materno', genero='$genero', email='$email', telefono='$telefono', rol='$rol' WHERE dni='$dni'";
		$rs=mysqli_query($conexion,$sql);

		$proceso="MODIFICACION DE DATOS DE USUARIO";
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dni', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="USUARIO_MODIFICADO";
	}
	
	if($_GET['operacion']=="ACTUALIZA_FOTO"){
		$dni        =$_GET['dni'];
		$foto       =$_FILES['fotoUsuario']['name'];
		$permitidos =array("image/jpg", "image/jpeg");
		$limite_kb  =200;
		$temp       =explode(".", $_FILES['fotoUsuario']['name']);
		$archivo    =$dni.'.'.end($temp);
		$ruta       ='../assets/images/user/';
		$documento  ='../assets/images/user/'.$archivo;
		unlink($documento);

		if (in_array($_FILES['fotoUsuario']['type'], $permitidos) && $_FILES['fotoUsuario']['size'] <= $limite_kb * 1024){
			$resultado = @move_uploaded_file($_FILES['fotoUsuario']["tmp_name"], $ruta . $archivo);
			if ($resultado){
				$sql="UPDATE sm_usuarios SET foto='$archivo' WHERE dni='$dni'";
				$rs=mysqli_query($conexion,$sql);

				$proceso="FOTO DE USUARIO FUE ACTUALIZADA O CAMBIADA";
				$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dni', '$proceso', '$fecha', '$hora', '$dniUsuario')";
				$rs=mysqli_query($conexion,$sql);
				$respuesta->mensaje = "PROCESADO";
			} else {
				$respuesta->mensaje = "ERRORSERVER";
			}
		} else {
			$respuesta->mensaje = "ERRORFILE";
		}
	}

	if($operacion=="AGREGA_CATEGORIA_NOTA"){
		$categoria =$_POST['categoria'];

		$sql="INSERT INTO sm_notas_categorias(categoria, fecha, hora, usuario) VALUES('$categoria', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$respuesta->mensaje ="CATEGORIA_AGREGADA_NOTA";
		}else{
			$respuesta->mensaje ="ERROR_CATEGORIA_AGREGADA_NOTA";
		}
	}

	if($operacion=="ELIMINAR_CATEGORIA_NOTA"){
		$id=$_POST['id'];
		
		$sql="DELETE FROM sm_notas_categorias WHERE id='$id'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$respuesta->mensaje ="CATEGORIA_ELIMINADA_NOTA";
		}else{
			$respuesta->mensaje ="ERROR_CATEGORIA_ELIMINADA_NOTA";
		}		
	}

	if($operacion=="AGREGA_NOTA"){
		$observacion =$_POST['observacion'];
		$idCategoria =$_POST['idCategoria'];
		
		$sql="INSERT INTO sm_notas(idCategoria, observacion, fecha, hora, usuario) VALUES('$idCategoria','$observacion', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$respuesta->mensaje ="OBSERVACION_AGREGADA";
		}else{
			$respuesta->mensaje ="ERROR_OBSERVACION_AGREGADA";
		}
	}

	if($operacion=="EDITA_NOTA"){
		$id          =$_POST['id'];
		$observacion =$_POST['observacion'];
		
		$sql="UPDATE sm_notas SET observacion='$observacion' WHERE id='$id'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$respuesta->mensaje ="OBSERVACION_MODIFICADA";
		}else{
			$respuesta->mensaje ="ERROR_OBSERVACION_MODIFICADA";
		}		
	}

	if($operacion=="ELIMINAR_NOTA"){
		$codigoSocio =$_POST['codigoSocio'];
		$observacion =$_POST['observacion'];
		$nombre      =infoSocios($codigoSocio,'nombreCorto');
		
		$sql="DELETE FROM sm_notas WHERE id='$observacion'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$respuesta->mensaje ="OBSERVACION_ELIMINADA";
		}else{
			$respuesta->mensaje ="ERROR_OBSERVACION_ELIMINADA";
		}		
	}

	echo json_encode($respuesta);
?>





 