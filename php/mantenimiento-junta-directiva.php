<?php
	session_start();
	include ('funciones.php');
	include '../php/conexion.php';
	$dniUsuario    = $_SESSION['dni_apv'];
	$fecha         = infoTiempo('fecha');
	$hora          = infoTiempo('hora');
	$fechaRegistro = $fecha;
	$horaRegistro  = $hora;
	$usuario       = $dniUsuario;

	if ($_POST['operacion'] === 'VERIFICA_FECHA' && isset($_POST['fechaConsulta'])) {
		$fechaConsulta=fechaSQL($_POST['fechaConsulta']);
		$validaFecha=infoFecha($fechaConsulta,'valida');

		$respuesta->informacion=$fechaConsulta;

		if($validaFecha=='VALIDO'){
			$sql="SELECT COUNT(sm_junta_directiva.idJuntaDirectiva) AS JDEncontrada, sm_junta_directiva.idJuntaDirectiva FROM sm_junta_directiva WHERE '$fechaConsulta' BETWEEN sm_junta_directiva.fechaPeriodo AND sm_junta_directiva.fechaFinPeriodo";
			$consulta = $conexion->query($sql);
			$resultado = $consulta->fetch_assoc();
			$JDEncontrada=$resultado[JDEncontrada];
			$idJuntaDirectiva=$resultado[idJuntaDirectiva];

			if($JDEncontrada>0){
				$sql="SELECT YEAR(fechaPeriodo) AS inicioPeriodio, YEAR(fechaFinPeriodo) AS finPeriodio FROM sm_junta_directiva WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
				$consulta = $conexion->query($sql);
				$resultado = $consulta->fetch_assoc();
				$inicioPeriodio=$resultado[inicioPeriodio];
				$finPeriodio=$resultado[finPeriodio];

				$sql="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
				$consulta = $conexion->query($sql);
				$resultado = $consulta->fetch_assoc();
				$nombrePresidente=$resultado[nombrePresidente];

				$validaFecha='FECHA_OCUPADA';
				$infoFechaPeriodo='<span class="text-danger">FECHA OCUPADA POR JUNTA DIRECTIVA</span>';
				$informacionJunta='LA FECHA INGRESADA PERTENECE A UNA JUNTA DIRECTIVA PRESIDIDA POR <strong>'.$nombrePresidente.'</strong>, DEL PERIODO <strong>'.$inicioPeriodio.' - '.$finPeriodio.'</strong>, PARA CONTINUAR CON EL REGISTRO DE UNA NUEVA JUNTA DIRECTIVA ELIJA OTRA FECHA.';
			}else{
				$validaFecha='FECHA_VALIDA';
				$infoFechaPeriodo='<span class="text-purple-800">'.infoFecha($fechaConsulta,'larga').'</span>';
				$informacionJunta='';
			}
		}else{
			$infoFechaPeriodo='<span class="text-danger">FECHA INCORRECTA</span>';
			$validaFecha='FECHA_INVALIDA';
			$informacionJunta='';
		}
		$respuesta->validaFecha=$validaFecha;
		$respuesta->infoFechaPeriodo=$infoFechaPeriodo;
		$respuesta->informacionJunta=$informacionJunta;
	}

	if ($_POST['operacion'] === 'INICIA_SESION_JUNTA_DIRECTIVA' && isset($_POST['fechaPeriodo']) && isset($_POST['idVigencia'])) {
		$fechaPeriodo=fechaSQL($_POST['fechaPeriodo']);
		$idVigencia=$_POST['idVigencia'];

		if (!isset($_SESSION['crearJD'])) {
		    $_SESSION['crearJD'] = [];
		}

		$_SESSION['crearJD']['baseJD'] = [
		    'fechaPeriodo' => $fechaPeriodo,
		    'idVigencia'   => $idVigencia
		];

		$respuesta->resultado='SESION_JD_CREADA';
	}
	
	if ($_POST['operacion'] === 'INICIA_SESION_CUENTA_BANCO_JUNTA_DIRECTIVA' && isset($_POST['codigoBanco']) && isset($_POST['codigoCuenta'])) {
		$codigoBanco=$_POST['codigoBanco'];
		$codigoCuenta=$_POST['codigoCuenta'];

		$_SESSION['crearJD']['cuentaBancoJunta'] = [
			'codigoBanco'  => $codigoBanco,
			'codigoCuenta' => $codigoCuenta
		];
		
		$respuesta->resultado='CUENTA_BANCO_ASIGNADO';
	}

	if ($_POST['operacion'] === 'ASIGNA_SESION_SOCIO_CARGO_JUNTA_DIRECTIVA' && isset($_POST['idCargoJunta']) && isset($_POST['codigoSocio'])) {
		$idCargoJunta=$_POST['idCargoJunta'];
		$codigoSocio=$_POST['codigoSocio'];
		
		if (!isset($_SESSION['crearJD']['cargosSocios'])) {
		    $_SESSION['crearJD']['cargosSocios'] = array();
		}
		$_SESSION['crearJD']['cargosSocios'][$idCargoJunta] = $codigoSocio;

		$respuesta->resultado='SOCIO_ASIGNADO_OK';
	}

	if ($_POST['operacion'] === 'ELIMINA_SESION_SOCIO_CARGO_JUNTA_DIRECTIVA' && isset($_POST['idCargoJunta'])) {
		$idCargoJunta=$_POST['idCargoJunta'];
		unset($_SESSION['crearJD']['cargosSocios'][$idCargoJunta]);
		$respuesta->resultado="ELIMINA_VARIABLE_SESION_CARGO";
	}

	if($_POST['operacion']==="REGISTRA_JUNTA_DIRECTIVA"){
		if($_SESSION['crearJD']){
			$fechaPeriodo = $_SESSION['crearJD']['baseJD']['fechaPeriodo'];
			$idVigencia   = $_SESSION['crearJD']['baseJD']['idVigencia'];
			$codigoBanco  = $_SESSION['crearJD']['cuentaBancoJunta']['codigoBanco'];
			$codigoCuenta = $_SESSION['crearJD']['cuentaBancoJunta']['codigoCuenta'];
			$cargosSocios = $_SESSION['crearJD']['cargosSocios'];
			$cargosOcupados=count($cargosSocios);
		}
		ksort($cargosSocios);
		$array_cargo   = $_POST['idCargoJunta'];
		$array_socio   = $_POST['codigoSocio'];

		$sql="SELECT MAX(id) AS ultimoID FROM sm_junta_directiva";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$ultimoID=$resultado[ultimoID]+1;
		
	    $idJuntaDirectiva="ARPJDP".ceros($ultimoID,5);
	    $ratificaJunta = 0;
	    $renunciaJunta = 0;
		$j             = 1;

		$sql="SELECT vigenciaJunta FROM sm_junta_directiva_vigencia WHERE idVigencia='$idVigencia'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$vigenciaJunta=$resultado[vigenciaJunta];

		$objFechaFinPeriodo = new DateTime($fechaPeriodo);
		$intervalo = new DateInterval('P'.$vigenciaJunta.'M');
		$objFechaFinPeriodo->add($intervalo);
		$fechaFinPeriodo=$objFechaFinPeriodo->format('Y-m-d');
		$fechaInicioPeriodo=$fechaPeriodo;

		$periodoInicio=infoFecha($fechaPeriodo,'year');
		$periodoFin=infoFecha($fechaFinPeriodo,'year');

		$sql="SELECT sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_banco_cuentas INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE sm_bancos.codigoBanco = '$codigoBanco' AND sm_banco_cuentas.codigoCuenta = '$codigoCuenta'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$entidad=$resultado[entidad];
		$numeroCuenta=$resultado[numeroCuenta];
		$detalle=$resultado[detalle];

		$sql="INSERT INTO sm_junta_directiva(idJuntaDirectiva, ratificaJunta, fechaPeriodo, idVigencia, fechaFinPeriodo, fechaRegistro, horaRegistro, usuario) VALUES('$idJuntaDirectiva', '$ratificaJunta', '$fechaPeriodo', '$idVigencia', '$fechaFinPeriodo', '$fechaRegistro', '$horaRegistro', '$usuario')";
		$record = $conexion->query($sql);

		$proceso="SE REGISTRO JUNTA DIRECTIVA ".$idJuntaDirectiva.", PARA EL PERIODO ".$periodoInicio." - ".$periodoFin;
		$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$record = $conexion->query($sql);
		$respuesta->resultado=$sql;

		$sql="INSERT INTO sm_junta_directiva_cuenta_banco(idJuntaDirectiva, codigoCuenta, fechaRegistro, horaRegistro, usuario) VALUES('$idJuntaDirectiva', '$codigoCuenta', '$fechaRegistro', '$horaRegistro', '$usuario')";
		$record = $conexion->query($sql);

		$proceso="SE REGISTRO CUENTA DE BANCO PARA LA JUNTA DIRECTIVA ".$idJuntaDirectiva.", EN ".$entidad." Y NUMERO DE CUENTA, ".$numeroCuenta." PARA EL PERIODO ".$periodoInicio." - ".$periodoFin;
		$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$record = $conexion->query($sql);
		$respuesta->resultado=$sql;
		
		$sql="SELECT idCargoJunta, cargoJunta FROM sm_junta_directiva_cargos ORDER BY idCargoJunta ASC";
		$cargosJuntaDirectiva = $conexion->query($sql);
		while ($cargos = $cargosJuntaDirectiva->fetch_assoc()) {
			$idCargoJunta = $cargos[idCargoJunta];
			$cargoJunta   = strtoupper($cargos[cargoJunta]);
			$codigoSocio  = $_SESSION['crearJD']['cargosSocios'][$j];

			$sql = "SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio FROM sm_socios WHERE codigoSocio = '$codigoSocio'";
			$consulta = $conexion->query($sql);
			$resultado = $consulta->fetch_assoc();
			$nombreSocio=$resultado[nombreSocio];

			$proceso="REGISTRO DE CARGO ".$cargoJunta." REPRESENTADO POR ".$nombreSocio." PARA LA JUNTA DIRECTIVA ".$idJuntaDirectiva.", DEL PERIODO ".$periodoInicio." - ".$periodoFin;
			$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$record = $conexion->query($sql);

			$sql="INSERT INTO sm_junta_directiva_integrantes(idJuntaDirectiva, idCargoJunta, codigoSocio, renunciaJunta, fechaRegistro, horaRegistro, usuario) VALUES('$idJuntaDirectiva', '$idCargoJunta', '$codigoSocio', '$renunciaJunta', '$fechaRegistro', '$horaRegistro', '$usuario')";
			$record = $conexion->query($sql);
			$j++;
		}

		/*------------ REGISTRO O ACTUALIZACION DE TABLAS PARA VINCULAR JUNTA DIRECTIVA ------------*/
		if(strlen($idJuntaDirectiva)>0){
			//VERIFICAR SI EN ESTA GESTION EXISTE REUNIONES, FAENAS  PARA ACTUALIZAR CON LA JD REGISTRADA
			$query="SELECT COUNT(id) AS asambleasRegistradas FROM sm_mod_actividades WHERE tipoActividad='ASA' AND fechaActividad BETWEEN '$fechaInicioPeriodo' AND '$fechaFinPeriodo'";
			$consulta = $conexion->query($query);
			$resultado = $consulta->fetch_assoc();
			$asambleasRegistradas=$resultado[asambleasRegistradas];

			if($asambleasRegistradas>0){
				$query="UPDATE sm_mod_actividades SET idJuntaDirectiva='$idJuntaDirectiva' WHERE tipoActividad='ASA' AND fechaActividad BETWEEN '$fechaInicioPeriodo' AND '$fechaFinPeriodo'";
				$record = $conexion->query($query);
			}

			$query="SELECT COUNT(id) AS faenasRegistradas FROM sm_mod_actividades WHERE tipoActividad='FAE' AND fechaActividad BETWEEN '$fechaInicioPeriodo' AND '$fechaFinPeriodo'";
			$consulta = $conexion->query($query);
			$resultado = $consulta->fetch_assoc();
			$faenasRegistradas=$resultado[faenasRegistradas];

			if($faenasRegistradas>0){
				$query="UPDATE sm_mod_actividades SET idJuntaDirectiva='$idJuntaDirectiva' WHERE tipoActividad='FAE' AND fechaActividad BETWEEN '$fechaInicioPeriodo' AND '$fechaFinPeriodo'";
				$record = $conexion->query($sql);
			}

			if($asambleasRegistradas>0 || $faenasRegistradas>0){
				$query="SELECT codigoActividad FROM sm_mod_actividades WHERE fechaActividad BETWEEN '$fechaInicioPeriodo' AND '$fechaFinPeriodo'";
				$actividades = $conexion->query($query);
				while ($dato = $actividades->fetch_assoc()) {
					$codigoActividad = $dato[codigoActividad];
					$sql="UPDATE sm_mod_cuentas SET idJuntaDirectiva='$idJuntaDirectiva' WHERE codigoConcepto='$codigoActividad'";
					$record = $conexion->query($sql);

					$sql="UPDATE sm_mod_caja SET idJuntaDirectiva='$idJuntaDirectiva' WHERE codigoConcepto='$codigoActividad'";
					$record = $conexion->query($sql);
				}
			}

			//VERIFICAR SI EN ESTA GESTION EXISTE CUOTAS PARA ACTUALIZAR CON LA JD REGISTRADA
			$query="SELECT COUNT(id) AS cuotasRegistradas FROM sm_mod_cuotas WHERE fecha BETWEEN '$fechaInicioPeriodo' AND '$fechaFinPeriodo'";
			$consulta = $conexion->query($query);
			$resultado = $consulta->fetch_assoc();
			$cuotasRegistradas=$resultado[cuotasRegistradas];

			if($cuotasRegistradas>0){
				$query="UPDATE sm_mod_cuotas SET idJuntaDirectiva='$idJuntaDirectiva' WHERE fecha BETWEEN '$fechaInicioPeriodo' AND '$fechaFinPeriodo'";
				$record = $conexion->query($query);

				$sql="SELECT codigoCuota FROM sm_mod_cuotas WHERE fecha BETWEEN '$fechaInicioPeriodo' AND '$fechaFinPeriodo'";
				$cuotas = $conexion->query($sql);

				while ($dato = $cuotas->fetch_assoc()) {
					$codigoCuota = $dato[codigoCuota];
					$query="UPDATE sm_mod_cuotas_socios SET idJuntaDirectiva='$idJuntaDirectiva' WHERE codigoCuota='$codigoCuota'";
					$record = $conexion->query($query);

					$query="UPDATE sm_mod_caja SET idJuntaDirectiva='$idJuntaDirectiva' WHERE codigoConcepto='$codigoCuota'";
					$record = $conexion->query($query);
				}
			}
		}
		/*------------ REGISTRO O ACTUALIZACION DE TABLAS PARA VINCULAR JUNTA DIRECTIVA ------------*/

		unset($_SESSION['crearJD']);
		$respuesta->resultado = 'JUNTA_DIRECTIVA_REGISTRO_OK';
	}

	if($_POST['operacion']==="REGISTRA_CUENTA_BANCO_JUNTA_DIRECTIVA"){
		$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
		$codigoBanco     = $_POST['codigoBanco'];
		$codigoCuenta     = $_POST['codigoCuenta'];
		$detalleCuenta = $_POST['detalleCuenta'];
		
		$sql="SELECT COUNT(codigoCuenta) AS cuentaRegistrada FROM sm_junta_directiva_cuenta_banco WHERE codigoCuenta = '$codigoCuenta' AND idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$cuentaRegistrada=$resultado[cuentaRegistrada];
		if($cuentaRegistrada>0){
			$respuesta->resultado='CUENTA_EN_USO';
			echo json_encode($respuesta);
			exit;
		}

		$sql="SELECT sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_banco_cuentas INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE sm_bancos.codigoBanco = '$codigoBanco' AND sm_banco_cuentas.codigoCuenta = '$codigoCuenta'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$entidad=$resultado[entidad];
		$numeroCuenta=$resultado[numeroCuenta];
		$detalle=$resultado[detalle];

		$sql="SELECT fechaPeriodo, fechaFinPeriodo FROM sm_junta_directiva WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$fechaPeriodo=$resultado[fechaPeriodo];
		$fechaFinPeriodo=$resultado[fechaFinPeriodo];

		$periodoInicio=infoFecha($fechaPeriodo,'year');
		$periodoFin=infoFecha($fechaFinPeriodo,'year');

		$sql="SET GLOBAL FOREIGN_KEY_CHECKS=0";
		$record = $conexion->query($sql);
		
		$sql="SET FOREIGN_KEY_CHECKS=0";
		$record = $conexion->query($sql);

		$sql="INSERT INTO sm_junta_directiva_cuenta_banco(idJuntaDirectiva, codigoCuenta, fechaRegistro, horaRegistro, usuario) VALUES('$idJuntaDirectiva', '$codigoCuenta', '$fechaRegistro', '$horaRegistro', '$usuario')";
		$record = $conexion->query($sql);
		if($record){
			$proceso="SE REGISTRO CUENTA DE BANCO PARA LA JUNTA DIRECTIVA ".$idJuntaDirectiva.", EN ".$entidad." Y NUMERO DE CUENTA, ".$numeroCuenta." PARA EL PERIODO ".$periodoInicio." - ".$periodoFin;
			$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$record = $conexion->query($sql);
			$respuesta->resultado='CUENTA_BANCO_ASIGNADO';
		}
	}

	
	if($_POST['operacion']==="ELIMINA_CUENTA_JUNTA_DIRECTIVA"){
		$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
		$codigoCuenta     = $_POST['codigoCuenta'];


		$sql="SELECT sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_banco_cuentas INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE sm_banco_cuentas.codigoCuenta = '$codigoCuenta'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$entidad=$resultado[entidad];
		$numeroCuenta=$resultado[numeroCuenta];
		$detalle=$resultado[detalle];

		$sql="SELECT fechaPeriodo, fechaFinPeriodo FROM sm_junta_directiva WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$fechaPeriodo=$resultado[fechaPeriodo];
		$fechaFinPeriodo=$resultado[fechaFinPeriodo];

		$periodoInicio=infoFecha($fechaPeriodo,'year');
		$periodoFin=infoFecha($fechaFinPeriodo,'year');

		$sql="SET GLOBAL FOREIGN_KEY_CHECKS=0";
		//$record = $conexion->query($sql);
		
		$sql="SET FOREIGN_KEY_CHECKS=0";
		//$record = $conexion->query($sql);

		$sql="DELETE FROM sm_junta_directiva_cuenta_banco WHERE idJuntaDirectiva='$idJuntaDirectiva' AND codigoCuenta='$codigoCuenta'";
		$record = $conexion->query($sql);
		if($record){
			$proceso="SE ELIMINO CUENTA DE BANCO DE JUNTA DIRECTIVA ".$idJuntaDirectiva.", EN ".$entidad." Y NUMERO DE CUENTA, ".$numeroCuenta." PARA EL PERIODO ".$periodoInicio." - ".$periodoFin;
			$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$record = $conexion->query($sql);
			$respuesta->resultado='CUENTA_BANCO_ELIMINADA_JUNTA_DIRECTIVA';
		}else{
			$respuesta->resultado='CUENTA_BANCO_ELIMINADA_JUNTA_DIRECTIVA_ERROR';
		}
	}

	if($_POST['operacion']==="ELIMINA_SESION_REGISTRO_JUNTA_DIRECTIVA"){
		unset($_SESSION['crearJD']);
		if (!isset($_SESSION['crearJD'])) {
		    $respuesta->resultado = 'SESION_ELIMINADA_OK';
		} else {
		    $respuesta->resultado = 'SESION_ELIMINADA_ERROR';
		}
	}

	if($_POST['operacion']==="REGISTRA_CARGO_JUNTA_DIRECTIVA"){
		$cargoJunta = trim($_POST['cargoJunta']);
		$query = "SELECT COUNT(sm_junta_directiva_cargos.idCargoJunta) AS cargoRegistrado FROM sm_junta_directiva_cargos WHERE cargoJunta = '$cargoJunta'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$cargoRegistrado = $resultado[cargoRegistrado];

		if($cargoRegistrado>0){
			$respuesta->resultado='CARGO_EXISTE';
		}else{
			$sql = "INSERT INTO sm_junta_directiva_cargos(cargoJunta) VALUES('$cargoJunta')";
			$record = $conexion->query($sql);

			if($record){
				$proceso="REGISTRO DE NUEVO CARGO DE JUNTA DIRECTIVA  - ".$cargoJunta;
				$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
				$record = $conexion->query($sql);

				$respuesta->resultado='CARGO_REGISTRADO_OK';
			}else{
				$respuesta->resultado='CARGO_REGISTRADO_ERROR';
			}
		}
	}

	if($_POST['operacion']==="ELIMINA_CARGO_JUNTA_DIRECTIVA"){
		$idCargoJunta=$_POST['idCargoJunta'];
		$cargoJunta=$_POST['cargoJunta'];

		$sql="DELETE FROM sm_junta_directiva_cargos WHERE idCargoJunta='$idCargoJunta'";
		$record = $conexion->query($sql);

		if($record){
			$proceso="SE ELIMINO CARGO DE JUNTA DIRECTIVA  - ".$cargoJunta;
			$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$record = $conexion->query($sql);
			$respuesta->resultado='CARGO_ELIMINADO_OK';
		}else{
			$respuesta->resultado='CARGO_ELIMINADO_ERROR';
		}
	}

	if($_POST['operacion']==="REGISTRA_CARGO_VIGENCIA_JUNTA_DIRECTIVA"){
		$vigenciaJunta = trim($_POST['vigenciaJunta']);
		$activo = 0;

		$query = "SELECT COUNT(sm_junta_directiva_vigencia.vigenciaJunta) AS vigenciaRegistrada FROM sm_junta_directiva_vigencia WHERE vigenciaJunta = '$vigenciaJunta'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$vigenciaRegistrada = $resultado[vigenciaRegistrada];

		if($vigenciaRegistrada>0){
			$respuesta->resultado='VIGENCIA_EXISTE';
		}else{
			$sql = "INSERT INTO sm_junta_directiva_vigencia(vigenciaJunta, activo, fechaRegistro, horaRegistro, usuario) VALUES('$vigenciaJunta', '$activo', '$fecha', '$hora', '$dniUsuario')";
			$record = $conexion->query($sql);

			if($vigenciaJunta>1){
				$infoVigencia=$vigenciaJunta.' AÑOS';
			}else{
				$infoVigencia='1 AÑO';
			}

			if($record){
				$proceso="REGISTRO DE NUEVO VIGENCIA PARA JUNTA DIRECTIVA  - ".$infoVigencia;
				$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
				$record = $conexion->query($sql);

				$respuesta->resultado='VIGENCIA_REGISTRADO_OK';
			}else{
				$respuesta->resultado='VIGENCIA_REGISTRADO_ERROR';
			}
		}
	}
	
	if($_POST['operacion']==="PREDEFINIR_VIGENCIA_JUNTA_DIRECTIVA"){
		$idVigencia=$_POST['idVigencia'];
		$infoVigencia=$_POST['infoVigencia'];

		$query = "SELECT idVigencia AS idVigenciaActiva FROM sm_junta_directiva_vigencia WHERE activo = '1'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$idVigenciaActiva = $resultado[idVigenciaActiva];

		$sql="UPDATE sm_junta_directiva_vigencia SET activo='0' WHERE idVigencia='$idVigenciaActiva'";
		$record = $conexion->query($sql);

		$sql="UPDATE sm_junta_directiva_vigencia SET activo='1' WHERE idVigencia='$idVigencia'";
		$record = $conexion->query($sql);

		if($record){
			$proceso="SE HA PREDEFINIDO LA VIGENCIA - ".$infoVigencia;
			$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$record = $conexion->query($sql);
			$respuesta->resultado='VIGENCIA_ACTIVA_OK';
		}else{
			$respuesta->resultado='VIGENCIA_ACTIVA_ERROR';
		}
	}

	if($_POST['operacion']==="ACTUALIZAR_FIN_PERIODO_RATIFICAR_JUNTA_DIRECTIVA"){
		$tiempo=$_POST['tiempo'];
		if($tiempo>1){
			$infoTiempo=ceros($tiempo,2).' MES';
		}else{
			$infoTiempo=ceros($tiempo,2).' MESES';
		}

		$sql="UPDATE sm_junta_directiva_fin_periodo SET tiempo = '$tiempo' WHERE id='1'";
		$record = $conexion->query($sql);

		if($record){
			$proceso="SE HA PREDEFINIDO EL TIEMPO PARA RATIFICAR JUNTA DIRECTIVA EN ".$infoTiempo;
			$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fechaRegistro', '$horaRegistro', '$usuario')";
			$record = $conexion->query($sql);
			$respuesta->resultado='PERIODO_REGISTRADO_OK';
		}else{
			$respuesta->resultado='PERIODO_REGISTRADO_ERROR';
		}
		$respuesta->informacion=$infoTiempo;
	}

	if($_POST['operacion']==="REGISTRA_RENUNCIA_JUNTA_DIRECTIVA"){
		$motivoRenuncia      = filter_var($_POST['motivoRenuncia'], FILTER_SANITIZE_STRING);
		$fechaRenuncia       = fechaSQL($_POST['fechaRenuncia']);
		$codigoSocio         = $_POST['codigoSocio'];
		$idJuntaDirectiva    = $_POST['idJuntaDirectiva'];
		$idCargoJunta        = $_POST['idCargoJunta'];
		$codigoSocioRenuncia = $_POST['codigoSocioRenuncia'];
		$codigoSocioRemplazo = $codigoSocio;
		$renunciaJunta       = 1;
		$idRenunciante       = $idJuntaDirectiva.$idCargoJunta.$codigoSocio;

		$query = "SELECT sm_junta_directiva.fechaPeriodo, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva.fechaFinPeriodo FROM sm_junta_directiva INNER JOIN sm_junta_directiva_vigencia ON sm_junta_directiva.idVigencia = sm_junta_directiva_vigencia.idVigencia WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$fechaPeriodo = $resultado[fechaPeriodo];
		$vigenciaJunta = $resultado[vigenciaJunta];
		$fechaFinPeriodo=$resultado[fechaFinPeriodo];

		$fechaInicio=date("Y", strtotime($fechaPeriodo));
		$fechaFin=date("Y", strtotime($fechaFinPeriodo));

		$query = "SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio FROM sm_socios WHERE codigoSocio = '$codigoSocio'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$nombreSocioRemplazante = $resultado[nombreSocio];

		$query = "SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio FROM sm_socios WHERE codigoSocio = '$codigoSocioRenuncia'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$nombreSocioRemplazado = $resultado[nombreSocio];

		$query = "SELECT sm_junta_directiva_cargos.cargoJunta FROM sm_junta_directiva_cargos WHERE sm_junta_directiva_cargos.idCargoJunta = '$idCargoJunta'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$cargoJunta = $resultado[cargoJunta];

		$sql="UPDATE sm_junta_directiva_integrantes SET codigoSocio='$codigoSocio', renunciaJunta='$renunciaJunta', fechaRegistro='$fechaRegistro', horaRegistro='$horaRegistro', usuario='$usuario' WHERE idJuntaDirectiva='$idJuntaDirectiva' AND idCargoJunta='$idCargoJunta'";
		$recordActualiza = $conexion->query($sql);

		$sql="INSERT sm_junta_directiva_renuncia_integrantes(idRenunciante, idJuntaDirectiva, idCargoJunta, codigoSocio, codigoSocioRemplazo, motivoRenuncia, fechaRenuncia, fechaRegistro, horaRegistro, usuario) VALUES('$idRenunciante', '$idJuntaDirectiva', '$idCargoJunta', '$codigoSocioRenuncia', '$codigoSocioRemplazo', '$motivoRenuncia', '$fechaRenuncia', '$fechaRegistro', '$horaRegistro', '$usuario')";
		$recordInserta = $conexion->query($sql);

		if($recordActualiza && $recordInserta){
			$proceso="CAMBIO DE INTEGRANTE DEL CARGO ".strtoupper($cargoJunta)." DE ".$nombreSocioRemplazante." POR ".$nombreSocioRemplazado." PARA EL PERIODO DE LA JUNTA DIRECTIVA ".$fechaInicio." - ".$fechaFin;
			$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$record = $conexion->query($sql);
			$respuesta->resultado='REGISTRA_RENUNCIA_OK';
		}else{
			$respuesta->resultado='REGISTRA_RENUNCIA_ERROR';
		}
	}

	if($_POST['operacion']==="AMPLIA_FIN_PERIODO_JUNTA_DIRECTIVA"){
		$idJuntaDirectiva        = $_POST['idJuntaDirectiva'];
		$idVigencia              = $_POST['idVigencia'];
		$fechaFinPeriodo         = $_POST['fechaFinPeriodo'];
		$extensionJuntaDirectiva = 1;
		$idExtensionJD           = $idJuntaDirectiva.str_replace("-","",$fechaFinPeriodo);

		$sql="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombrePresidente=$resultado[nombrePresidente];

		$sql="SELECT vigenciaJunta FROM sm_junta_directiva_vigencia WHERE idVigencia='$idVigencia'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$vigenciaJunta=$resultado[vigenciaJunta];

		$objFechaExtensionPeriodo = new DateTime($fechaFinPeriodo);
		$intervalo                = new DateInterval('P'.$vigenciaJunta.'M');
		$objFechaExtensionPeriodo->add($intervalo);
		$fechaExtensionPeriodo    = $objFechaExtensionPeriodo->format('Y-m-d');

		if($vigenciaJunta>=12){
			$periodo=$vigenciaJunta/12;
			if($periodo>1){
				$infoVigencia=$vigenciaJunta.' AÑOS';
			}else{
				$infoVigencia=$vigenciaJunta.' AÑO';
			}
		}else{
			if($vigenciaJunta>1){
				$infoVigencia=$vigenciaJunta.' MESES';
			}else{
				$infoVigencia=$vigenciaJunta.' MES';
			}
		}

		$infoPeriodoInicio=strtoupper( infoFecha($fechaInicioPeriodo,'muycorta'));
		$infoPeriodoFin=strtoupper(infoFecha($fechaFinPeriodo,'muycorta'));

		$sql="UPDATE sm_junta_directiva SET extensionJuntaDirectiva='$extensionJuntaDirectiva', fechaFinPeriodo='$fechaExtensionPeriodo' WHERE idJuntaDirectiva='$idJuntaDirectiva'";
		$record = $conexion->query($sql);

		$sql="INSERT INTO sm_junta_directiva_extension(idExtensionJD, idJuntaDirectiva, fechaFinPeriodo, fechaExtensionPeriodo, fechaRegistro, horaRegistro, usuario) VALUES('$idExtensionJD', '$idJuntaDirectiva', '$fechaFinPeriodo', '$fechaExtensionPeriodo', '$fechaRegistro', '$horaRegistro', '$usuario')";
		$record = $conexion->query($sql);

		if($record){
			$proceso="SE AMPLIO EL PERIODO DE LA JUNTA DIRECTIVA DE ".$nombrePresidente." DEL LA FECHA ".$infoPeriodoInicio." A LA FECHA ".$infoPeriodoFin." ENTENDIENDOLO POR ".$infoVigencia;
			$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$record = $conexion->query($sql);
			$respuesta->resultado='REGISTRA_ENTENSION_PERIODO_OK';
		}else{
			$respuesta->resultado='REGISTRA_ENTENSION_PERIODO_ERROR';
		}
	}

	if($_POST['operacion']==="RATIFICA_ACTUAL_JUNTA_DIRECTIVA"){
		$fechaPeriodo=fechaSQL($_POST['fechaPeriodo']);
		$idVigencia=$_POST['idVigencia'];
		$codigoCuenta=$_POST['codigoCuenta'];
		$idJuntaDirectivaSaliente=$_POST['idJuntaDirectiva'];

		$sql="SELECT CONCAT(sm_socios.nombre, ' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectivaSaliente' AND idCargoJunta = '1'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombrePresidente=$resultado[nombrePresidente];

		$sql="SELECT vigenciaJunta FROM sm_junta_directiva_vigencia WHERE idVigencia='$idVigencia'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$vigenciaJunta=$resultado[vigenciaJunta];

		$fechaFinPeriodo = new DateTime($fechaPeriodo);
		$intervalo = new DateInterval('P'.$vigenciaJunta.'M');
		$fechaFinPeriodo->add($intervalo);
		$fechaFinPeriodo=$fechaFinPeriodo->format('Y-m-d');

		$periodoInicio=infoFecha($fechaPeriodo,'year');
		$periodoFin=infoFecha($fechaFinPeriodo,'year');
		
		$sql="SELECT MAX(id) AS ultimoID FROM sm_junta_directiva";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$ultimoID=$resultado[ultimoID]+1;
		
		$sql="UPDATE sm_junta_directiva SET ratificaJunta='1' WHERE idJuntaDirectiva='$idJuntaDirectivaSaliente'";
		$record = $conexion->query($sql);

		$proceso="SE RATIFICO LA JUNTA DIRECTIVA ".$idJuntaDirectivaSaliente." PRESIDIDO POR ".$nombrePresidente.", PARA EL PERIODO ".$periodoInicio." - ".$periodoFin;
		$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$record = $conexion->query($sql);

	    $idJuntaDirectiva="ARPJDP".ceros($ultimoID,5);
		$extensionJuntaDirectiva=0;
	    $ratificaJunta=0;
	    $renunciaJunta=0;

	    $sql="INSERT INTO sm_junta_directiva(idJuntaDirectiva, ratificaJunta, fechaPeriodo, idVigencia, fechaFinPeriodo, extensionJuntaDirectiva, fechaRegistro, horaRegistro, usuario) VALUES('$idJuntaDirectiva', '$ratificaJunta', '$fechaPeriodo', '$idVigencia', '$fechaFinPeriodo', '$extensionJuntaDirectiva', '$fechaRegistro', '$horaRegistro', '$usuario')";
		$record = $conexion->query($sql);
		$respuesta->informacion=$sql;

		$sql="INSERT INTO sm_junta_directiva_cuenta_banco(idJuntaDirectiva, codigoCuenta, fechaRegistro, horaRegistro, usuario) VALUES('$idJuntaDirectiva', '$codigoCuenta', '$fechaRegistro', '$horaRegistro', '$usuario')";
		$record = $conexion->query($sql);
		
		if($record){
			$sql="SELECT idCargoJunta, codigoSocio FROM sm_junta_directiva_integrantes WHERE idJuntaDirectiva = '$idJuntaDirectivaSaliente'";
			$consultaJunta = $conexion->query($sql);
			while ($junta = $consultaJunta->fetch_assoc()) {
				$idCargoJunta=$junta['idCargoJunta'];
				$codigoSocio=$junta['codigoSocio'];

				$sql="SELECT cargoJunta FROM sm_junta_directiva_cargos WHERE idCargoJunta='$idCargoJunta'";
				$consulta = $conexion->query($sql);
				$resultado = $consulta->fetch_assoc();
				$cargoJunta=$resultado[cargoJunta];

				$sql="SELECT CONCAT( sm_socios.nombre, ' ', sm_socios.apPaterno, ' ', sm_socios.apMaterno ) AS nombreSocio FROM sm_socios WHERE codigoSocio='$codigoSocio'";
				$consulta = $conexion->query($sql);
				$resultado = $consulta->fetch_assoc();
				$nombreSocio=$resultado[nombreSocio];

				$sql="INSERT INTO sm_junta_directiva_integrantes(idJuntaDirectiva, idCargoJunta, codigoSocio, renunciaJunta, fechaRegistro, horaRegistro, usuario) VALUES('$idJuntaDirectiva', '$idCargoJunta', '$codigoSocio', '$renunciaJunta', '$fechaRegistro', '$horaRegistro', '$usuario')";
				$record = $conexion->query($sql);

				if($record){
					$proceso="SE RATIFICO EL CARGO ".strtoupper($cargoJunta)." DE LA JUNTA DIRECTIVA ".$idJuntaDirectiva." A CARGO DE ".$nombreSocio.", PARA EL PERIODO ".$periodoInicio." - ".$periodoFin;
					$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
					$record = $conexion->query($sql);
				}
			}
			$respuesta->resultado='REGISTRA_RATIFICACION_OK';
		}else{
			$respuesta->resultado='REGISTRA_RATIFICACION_ERROR';
		}
	}
	echo json_encode($respuesta);
	$conexion->close();
?>