<?php
	session_start();
	include ('funciones.php');
	$conexion    =conexionDB();
	$fecha       =infoTiempo('fecha');
	$hora        =infoTiempo('hora');
	$dniUsuario  =$_SESSION['dni_apv'];
	$operacion   =$_POST[operacion];

	if($operacion=="REGISTRA_ACTIVIDAD"){
		$tipoActividad        = $_POST[tipoActividad];
		$codigoActividad      = codActividad($tipoActividad,fechaSQL($_POST[fechaActividad]),$_POST[horaActividad],$dniUsuario);
		$temaActividad        = cTexto($_POST[temaActividad]);
		$formaActividad       = cTexto($_POST[formaActividad]); //DATA: 0 -> SIN INTERNET | 1 -> CON INTERNET
		$formaControl         = cTexto($_POST[formaControl]); //DATA: 0 -> REGISTRO IN/OUT | 1 -> REGISTRO OUT
		$contenidoActividad   = cTexto($_POST[contenidoActividad]);
		$fechaActividad       = fechaSQL($_POST[fechaActividad]);
		$horaActividad        = $_POST[horaActividad];
		$lugarActividad       = cTexto($_POST[lugarActividad]);
		$mTardanza            = $_POST[mTardanza];
		$mFalta               = $_POST[mFalta];
		$codigoCuenta         = $_POST[codigoCuenta];
		$idJuntaDirectiva     = $_POST[idJuntaDirectiva];
		$mPenalidad           = $_POST[mPenalidad];
		$fechaInicioPenalidad = fechaSQL($_POST[fechaInicioPenalidad]);
		$fecha                = $fecha;
		$hora                 = $hora;
		$usuario              = $dniUsuario;
		$respuesta->informacion =$usuario;

		if($tipoActividad=="ASA"){ $tipoACT="ASAMBLEA"; }
		if($tipoActividad=="FAE"){ $tipoACT="FAENA"; }

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE ACTIVIDADES
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_mod_actividades(idJuntaDirectiva, codigoActividad, tipoActividad, temaActividad, formaActividad, formaControl, contenidoActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta, mPenalidad, fechaInicioPenalidad, codigoCuenta, fecha, hora, usuario) VALUES('$idJuntaDirectiva', '$codigoActividad', '$tipoActividad', '$temaActividad', '$formaActividad', '$formaControl', '$contenidoActividad', '$fechaActividad', '$horaActividad', '$lugarActividad', '$mTardanza', '$mFalta', '$mPenalidad', '$fechaInicioPenalidad', '$codigoCuenta', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE ACTIVIDADES
			///////////////////////////////////////////////////
			$proceso=$tipoACT." GENERADA | <strong>".$temaActividad."</strong>";
			$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA DE SOCIOS
			///////////////////////////////////////////////////
			$sql="SELECT codigoSocio FROM sm_socios";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoSocio =$n[codigoSocio];
				$lotes       =infoSocios($codigoSocio,'cantidadLotes');
				$asistio     ="IN";
				$ingreso     ="";
				$salida      ="";
				$retraso     ="";
				$multa       ="";
				$estadoPago  ="";
				$observacion ="";
				$documento   ="";

				$sql="INSERT sm_mod_asistencia(tipoActividad, codigoActividad, codigoSocio, lotes, asistio, ingreso, salida, retraso, multa, estadoPago, observacion, documento) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$lotes', '$asistio', '$ingreso', '$salida', '$retraso', '$multa', '$estadoPago', '$observacion', '$documento')";
				$asistencia=mysqli_query($conexion,$sql);

				$proceso="AGREGADO A <strong>".$tipoACT." ".$temaActividad."</strong>";
				$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
				$bitacora=mysqli_query($conexion,$sql);

				$i++; 
			}

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIOS
			///////////////////////////////////////////////////
			$proceso=$tipoACT." GENERADA | <strong>".$temaActividad."</strong>";
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="ACTIVIDAD_ALMACENADA";
		}else{
			$respuesta->mensaje ="ERROR_ACTIVIDAD_ALMACENADA";
		}
	}

	if($operacion=="EDITA_ACTIVIDAD"){
		$tipoActividad      =$_POST[tipoActividad];
		$codigoActividad    =$_POST[codigoActividad];
		$temaActividad      =cTexto($_POST[temaActividad]);
		$contenidoActividad =cTexto($_POST[contenidoActividad]);
		$fechaActividad     =fechaSQL($_POST[fechaActividad]);
		$horaActividad      =$_POST[horaActividad];
		$lugarActividad     =cTexto($_POST[lugarActividad]);
		$mTardanza          =$_POST[mTardanza];
		$mFalta             =$_POST[mFalta];
		$codigoCuenta       =$_POST[codigoCuenta];
		$fecha              =$fecha;
		$hora               =$hora;
		$usuario            =$dniUsuario;

		if($tipoActividad=="ASA"){ $tipoACT="ASAMBLEA"; }
		if($tipoActividad=="FAE"){ $tipoACT="FAENA"; }


		///////////////////////////////////////////////////
		/// ACTUALIZAR DATOS DE ACTIVIDAD
		///////////////////////////////////////////////////
		$sql="UPDATE sm_mod_actividades SET  temaActividad='$temaActividad', contenidoActividad='$contenidoActividad', fechaActividad='$fechaActividad', horaActividad='$horaActividad', lugarActividad='$lugarActividad', mTardanza='$mTardanza', mFalta='$mFalta', codigoCuenta='$codigoCuenta', fecha='$fecha', hora='$hora', usuario='$usuario' WHERE codigoActividad='$codigoActividad'";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->informacion = $sql;

		if($rs){
			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE ACTIVIDADES
			///////////////////////////////////////////////////
			$proceso=$tipoACT." DATOS MODIFICADOS | <strong>".$temaActividad."</strong>";
			$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIOS
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="ACTIVIDAD_MODIFICADA";
		}else{
			$respuesta->mensaje ="ERROR_ACTIVIDAD_MODIFICADA";
		}

	}

	if($operacion=="ELIMINAR_ACTIVIDADA"){
		$tipoActividad   =$_POST[tipoActividad];
		$codigoActividad =$_POST[codigoActividad];
		$fecha           =$fecha;
		$hora            =$hora;
		$usuario         =$dniUsuario;
		$conceptoCuota   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
		$montoTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
		$montoFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');

		if($tipoActividad=="ASA"){
			$concepto="ASAMBLEA";
		}
		if($tipoActividad=="FAE"){
			$concepto="FAENA";
		}

		///////////////////////////////////////////////////
		/// ELIMINAR CUOTA
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// ELIMINAR ACTIVIDAD DE SOCIOS
		///////////////////////////////////////////////////
		$sql="DELETE FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad'";
		$rs=mysqli_query($conexion,$sql);

		$sql="SELECT codigoSocio FROM sm_socios";
		$rs=mysqli_query($conexion,$sql);
		$i=1;
		while($n=mysqli_fetch_array($rs)){
			$codigoSocio =$n[codigoSocio];
			$proceso     ="ELIMINADO DE ".$concepto." - <strong>".$conceptoCuota."</strong> | <strong>M. FALTA:</strong> S/. ".moneda($montoFalta)." | <strong>M. TARDANZA:</strong> S/. ".moneda($montoTarde);

			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$usuario')";
			$bitacora=mysqli_query($conexion,$sql);

			$i++; 
		}

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
		///////////////////////////////////////////////////
		$proceso=$concepto." ELIMINADA - <strong>".$conceptoCuota."</strong>";
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);


		$respuesta->mensaje ="ACTIVIDAD_ELIMINADA";
	}

	if($_GET[operacion]=="REGISTRA_JUSTIFICACION"){
		$codigoSocio     =$_GET[codigoSocio];
		$razon           =$_GET[razon];
		$codigoActividad =$_GET[codigoActividad];
		$observacion     =ctexto($_GET[observacion]);
		$tipoActividad   =$_GET[tipoActividad];
		$archivoJUS      =$_FILES['documento']['name'];
		$conceptoACT     =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');

		
		if($razon=="TARDE"){ $infoRazon="ASISTENCIA CON TARDANZA"; }
		if($razon=="FALTA"){ $infoRazon="INASISTENCIA"; }

		if($tipoActividad=="ASA"){ $concepto="<strong>ASAMBLEA ".$conceptoACT."</strong>"; }
		if($tipoActividad=="FAE"){ $concepto="<strong>FAENA ".$conceptoACT."</strong>";	 }


		///////////////////////////////////////////////////
		/// ACTUALIZAR SOCIO CON JUSTIFICACION
		///////////////////////////////////////////////////
		$sql="UPDATE sm_mod_asistencia SET asistio='JU', observacion='$observacion' WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE ACTIVIDADES
		///////////////////////////////////////////////////
		$proceso="JUSTIFICACION DE ".$infoRazon." A ".$concepto;
		$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES SOCIOS
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		if($archivoJUS){
			$permitidos      =array("image/jpg", "image/jpeg");
			$limite_kb       =400;
			$temp            =explode(".", $_FILES['documento']['name']);
			$archivo         =$codigoActividad.'-'.$codigoSocio.'.'.end($temp);
			$ruta            ='../assets/images/docs/';
			$documento       ='../assets/images/docs/'.$archivo;
			unlink($documento);

			if (in_array($_FILES['documento']['type'], $permitidos) && $_FILES['documento']['size'] <= $limite_kb * 1024){
				$resultado = @move_uploaded_file($_FILES['documento']["tmp_name"], $ruta . $archivo);
				if ($resultado){
					///////////////////////////////////////////////////
					/// ACTUALIZAR SOCIO CON JUSTIFICACION
					///////////////////////////////////////////////////
					$sql="UPDATE sm_mod_asistencia SET documento='$archivo' WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'";
					$rs=mysqli_query($conexion,$sql);

					$respuesta->mensaje = "PROCESADO";
				} else {
					$respuesta->mensaje = "ERRORSERVER";
				}
			} else { $respuesta->mensaje = "ERRORFILE"; }
		}else{ $respuesta->mensaje = "PROCESADO"; }
	}

	if($operacion=="CONSIGUE_RAZON"){
		$codigoSocio     =$_POST[codigoSocio];
		$codigoActividad =$_POST[codigoActividad];
		$asistio         =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'asistenciaSocio');
		$tarde         =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'asistenciaTardeSocio');
		if($asistio=="NO"){ $razon="FALTA"; }
		if($asistio=="SI" AND $tarde>0.15){ $razon="TARDE"; }
		$respuesta->razon = $razon;
	}

	if($_GET[operacion]=="SUBIR_ARCHIVO_ASISTENCIAS"){
		/////////////////////////////////////////////////////////////////////
		/// CONECCION CON LA INTERFACE JSON DEL SISTEMA DE VENTAS
		/////////////////////////////////////////////////////////////////////
		$servidor=$_SERVER['HTTP_HOST'];
		if($servidor=='app.bef'){
			$urlSocios="http://{$_SERVER['HTTP_HOST']}/apv";
		}else{
			$urlSocios="https://{$_SERVER['HTTP_HOST']}/apv";
		}

		$codigoActividad =$_GET[codigoActividad];
		$archivoJSON     =$_FILES['archivo']['name'];
		$archivo         =explode(".", $_FILES['archivo']['name']);
		$tipoFile        =end($archivo);
		$nombre          =$_FILES['archivo']['name'];
		$upload          ='../json/'.$nombre;
		$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
		$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
		$horaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','horaActividad');
		$fileterminal01  ='T-01-ASIS-'.$codigoActividad.'.json';
		$fileterminal02  ='T-02-ASIS-'.$codigoActividad.'.json';
		$fileterminal03  ='T-03-ASIS-'.$codigoActividad.'.json';
		$fileterminal04  ='T-04-ASIS-'.$codigoActividad.'.json';
		$fileterminal05  ='T-05-ASIS-'.$codigoActividad.'.json';
		$nroArchivo      =substr($archivoJSON,3,1);

		if($nroArchivo==1){ $verifica =infoTerminal($codigoActividad,'',$fileterminal01,'verificaArchivo'); $archivo  =$fileterminal01; }
		if($nroArchivo==2){ $verifica =infoTerminal($codigoActividad,'',$fileterminal02,'verificaArchivo'); $archivo  =$fileterminal02; }
		if($nroArchivo==3){ $verifica =infoTerminal($codigoActividad,'',$fileterminal03,'verificaArchivo'); $archivo  =$fileterminal03; }
		if($nroArchivo==4){ $verifica =infoTerminal($codigoActividad,'',$fileterminal04,'verificaArchivo'); $archivo  =$fileterminal04; }
		if($nroArchivo==5){ $verifica =infoTerminal($codigoActividad,'',$fileterminal05,'verificaArchivo'); $archivo  =$fileterminal05; }
		if($archivoJSON==$verifica){ $verificaArchivo="EXISTE"; }else{ $verificaArchivo="NOEXISTE"; }

		$nombreArchivoJSON=$archivo;


		if($archivoJSON){
			if ($tipoFile=='json'){
				$resultado = @move_uploaded_file($_FILES['archivo']["tmp_name"], $upload);
				if ($resultado){

					//$urlJSON      = $urlSocios.'/json/'.$archivoJSON;
					//$urlJSON      = file_get_contents($upload);
					//$cDatos       = curl_init($urlJSON);
					//curl_setopt($cDatos,CURLOPT_RETURNTRANSFER, TRUE);
					//$dJSON        = curl_exec($cDatos);
					//$jfo          = json_decode($dJSON,true);
					//$contar       = count($jfo);

					$json_file = file_get_contents($upload);
					$jfo       = json_decode($json_file);
					$contar    = count($jfo);

					$codACT    =$jfo[0]->codigoActividad;
					$terminal  =$jfo[0]->terminal;
					if($codigoActividad==$codACT){
						if($verificaArchivo=="NOEXISTE"){
							if($contar>$socios){
								foreach($jfo as $obj){
									$codigoSocio =$obj->codigoSocio;
									$ingreso     =$obj->ingreso;
									$retraso     =$obj->retraso;
									$salida      =$obj->salida;
									$horaACT     =$obj->horaActividad;

									$sql="UPDATE sm_mod_asistencia SET ingreso='$ingreso', salida='$salida', retraso='$retraso', terminal='$terminal' WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'";
									$rs=mysqli_query($conexion,$sql);

									if($horaACT==$horaActividad){
										$sql="UPDATE sm_mod_actividades SET horaActividad='$horaACT' WHERE codigoActividad='$codigoActividad'";
									}
								}
								$sql="INSERT INTO sm_mod_asistencia_json(codigoActividad, terminal, socios, archivo, fecha, hora, usuario) VALUES('$codigoActividad', '$terminal', '$contar', '$archivoJSON', '$fecha', '$hora', '$dniUsuario')";
								$rs=mysqli_query($conexion,$sql);
								
								$respuesta->archivoJSON = $archivoJSON;
								$respuesta->nroRegistros = $contar;
								$respuesta->urlJSON = $urlJSON;
								$respuesta->consulta = $sql;

								$respuesta->mensaje ="ARCHIVO_SUBIDO_ACTUALIZADO";
							}else{ $respuesta->mensaje = "ARCHIVOINCORRECTO"; }
						}else{ $respuesta->mensaje = "ARCHIVOEXISTESERVIDOR"; }
					}else{ $respuesta->mensaje = "ERRORARCHIVONOPERTENECE"; }
				}else{$respuesta->mensaje = "ERRORSERVER"; }
			}else{ $respuesta->mensaje = "ERRORFORMATO"; }
		}else{ $respuesta->mensaje = "SINFILE"; }
	}

	if($operacion=="ELIMINAR_JUSTIFICACION"){
		$tipoActividad   =$_POST[tipoActividad];
		$codigoActividad =$_POST[codigoActividad];
		$codigoSocio     =$_POST[codigoSocio];
		$razon           =$_POST[razon];
		$lotes           =infoSocios($codigoSocio,'cantidadLotes');
		$conceptoACT     =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
		$montoTarde      =$lotes*infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
		$montoFalta      =$lotes*infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
		$documento       =$codigoActividad.'-'.$codigoSocio.'jpg';

		if($tipoActividad=="ASA"){ $concepto="<strong>ASAMBLEA ".$conceptoACT."</strong>"; }
		if($tipoActividad=="FAE"){ $concepto="<strong>FAENA ".$conceptoACT."</strong>";	 }

		if($razon=="TARDE"){
			$asistio="SI";
			$estadoPago="NP";
			$multa=$montoTarde;
		}
		if($razon=="FALTA"){
			$asistio="NO";
			$estadoPago="NP";
			$multa=$montoFalta;
		}

		///////////////////////////////////////////////////
		/// ELIMINA IMAGEN DE JUSTIFICACION
		///////////////////////////////////////////////////
		unlink($documento);

		///////////////////////////////////////////////////
		/// ACTUALIZAR SOCIO CON JUSTIFICACION
		///////////////////////////////////////////////////
		$sql="UPDATE sm_mod_asistencia SET asistio='$asistio', multa='$multa', estadoPago='$estadoPago', observacion='', documento='' WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE ACTIVIDADES
		///////////////////////////////////////////////////
		$proceso="ELIMINADO - JUSTIFICACION DE INASISTENCIA A ".$concepto;
		$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA DE OPERACIONES SOCIOS
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		///////////////////////////////////////////////////
		/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
		///////////////////////////////////////////////////
		$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="JUSTIFICACION_ELIMINADA";
	}

	if($operacion=="GENERA_ARCHIVO_ACTIVIDAD"){
		$tipoActividad   =$_POST[tipoActividad];
		$codigoActividad =$_POST[codigoActividad];
		$fecha           =$fecha;
		$hora            =$hora;
		$usuario         =$dniUsuario;
		$codigoSocio     ="";

		if($tipoActividad=="ASA"){ $rotulo="ASAMBLEA"; }
		if($tipoActividad=="FAE"){ $rotulo="FAENA"; }

		$sql="SELECT temaActividad, formaControl, fechaActividad, horaActividad, mTardanza, mFalta FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; 
		$infoActividad=array();
		$rs=mysqli_query($conexion,$sql);
		mysqli_set_charset($conexion, "utf8"); 
		while($dato=mysqli_fetch_array($rs)){
			$temaActividad   =$dato[temaActividad];
			$formaControl    =$dato[formaControl];
			$fechaActividad  =$dato[fechaActividad];
			$horaActividad   =$dato[horaActividad];
			$mTardanza       =$dato[mTardanza];
			$mFalta          =$dato[mFalta];
			$infoActividad[] =array(
				'tipoActividad'   => $tipoActividad,
				'codigoActividad' => $codigoActividad,
				'formaControl'    => $formaControl,
				'temaActividad'   => $temaActividad,
				'fechaActividad'  => $fechaActividad,
				'horaActividad'   => $horaActividad,
				'mTardanza'       => $mTardanza,
				'mFalta'          => $mFalta
			);
		}
		$respuesta->mensaje ="INFO -> ".$infoActividad;

		$fp = fopen('../generados/'.$codigoActividad.'.json', 'w');
		fwrite($fp, json_encode($infoActividad));
		fclose($fp);

		if (file_exists('../generados/'.$codigoActividad.'.json')){
			$archivo=$codigoActividad.'.json';

			///////////////////////////////////////////////////
			/// ACTUALIZA ACTIVIDAD CON ARCHIVO GENERADO
			///////////////////////////////////////////////////
			$sql="UPDATE sm_mod_actividades SET archivoACT='$archivo' WHERE codigoActividad='$codigoActividad'";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE ACTIVIDADES
			///////////////////////////////////////////////////
			$proceso="GENERADO MODULO DE ASISTENCIA A ".$rotulo." <strong>".$temaActividad."</strong>";
			$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			///////////////////////////////////////////////////
			/// INSERTAR DATOS EN TABLA OPERACIONES DE USUARIO
			///////////////////////////////////////////////////
			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$usuario', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="ARCHIVO_GENERADO";
		}else{
			$respuesta->mensaje ="ERROR_ARCHIVO_GENERADO";
		}
	}

	if($operacion=="PROCESA_ASISTENCIAS_ACTIVIDAD"){
		$codigoActividad =$_POST[codigoActividad];
		$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
		$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
		
		///////////////////////////////////////////////////
		/// VERIFICA ASIENTES A ACTIVIDAD
		///////////////////////////////////////////////////
		$sql="SELECT codigoSocio, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad'";
		$rs=mysqli_query($conexion,$sql);
		$x=0;
		while($datos=mysqli_fetch_array($rs)){
			$codigoSocio =$datos[codigoSocio];
			$lotes       =$datos[lotes];
			$ingreso     =$datos[ingreso];
			$salida      =$datos[salida];
			$retraso     =$datos[retraso];
			$infoIngreso =substr($ingreso,0,2).substr($ingreso,3,2).substr($ingreso,6,2);
			$infoSalida  =substr($salida,0,2).substr($salida,3,2).substr($salida,6,2);


			if($infoIngreso==0 and $infoSalida==0){
				$asistio="NO";
				$multa=$lotes*$multaFalta;
				$estadoPago="NP";
			}
			
			if($infoIngreso>0 and $infoSalida>0){
				$asistio="SI";
				if($retras==0.00){
					$multa=0;
					$estadoPago="";
				}
				if($retraso>0.15){
					$multa=$lotes*$multaTarde;
					$estadoPago="NP";
				}
			}

			if($infoIngreso>0 and $infoSalida==0){
				$asistio="NO";
				$multa=$lotes*$multaFalta;
				$estadoPago="NP";
			}

			///////////////////////////////////////////////////
			/// INGRESA ASISTENCIAS, FALTAS Y MULTAS
			///////////////////////////////////////////////////
			$sql="UPDATE sm_mod_asistencia SET asistio='$asistio', multa='$multa', estadoPago='$estadoPago' WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'";
			$rsa=mysqli_query($conexion,$sql);
		}
		
		///////////////////////////////////////////////////
		/// CAMBIA ESTADO DE ACTIVIDAD COMO FINALIZADO
		///////////////////////////////////////////////////
		$sql="UPDATE sm_mod_actividades SET procesado='SI' WHERE codigoActividad='$codigoActividad'";
		$rsa=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="PROCESAMIENTO_ASISTENCIA_COMPLETADO";
	}

	cerrarDB();
	echo json_encode($respuesta);
?>