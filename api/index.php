<?php
	session_start();
	date_default_timezone_set("America/Lima");
	$usuarioActivo=$_SESSION['dni_apv'];

	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
		$urlSistema   = 'https://'.$_SERVER['SERVER_NAME'];
	}else{
		$urlSistema   = 'http://'.$_SERVER['SERVER_NAME'];
	}

	include_once "../php/funciones.php";
	$fecha=infoTiempo('fecha');
	$hora=infoTiempo('hora');

	$tiempoRestraso=15; //EN MINUTOS
	$tiempoFalta=25; //EN MINUTOS

	$operacion = $_POST['operacion'];

	if($operacion=='login'){
		$usuario = $_POST[usuario];
		$clave   = md5($_POST[clave]);
		$login   = login($usuario, $clave);
		
		if($login=='NOEXISTE'){
			$respuesta->estado  = "ERROR";
			$respuesta->usuario = "NOEXISTE";
		}else if($login=='USUARIOINACTIVO'){
			$respuesta->estado  = "ERROR";
			$respuesta->usuario = "INACTIVO";
		}else{
			$respuesta->estado  = "OK";
			$respuesta->usuario = $login;
		}
	}

	if($operacion=='salirActividades'){
		session_start();
		session_unset();
		session_destroy();
		$respuesta->resultado  = "OK";
	}
	
	if(!empty($_SESSION)){
		if($operacion=='inicarControl'){
			$codigoActividad = $_POST[codigoActividad];
			$formaActividad = $_POST[formaActividad];
			session_start();
			$_SESSION['codigoActividad']=$codigoActividad;
			$_SESSION['estadoActividad']='abierto';
			$_SESSION['formaActividad']=$formaActividad;
			$respuesta->resultado  = "OK";
		}

		if($operacion=='terminarActividad'){
			session_start();
			$_SESSION['estadoActividad']='terminado';
			$respuesta->resultado  = "OK";
		}
		
		if($operacion=='REGISTRA_ASISTENCIA'){
			$conexion=conexionDB();
			$codigoSocio     =$_POST[codigoSocio];
			if(strlen($codigoSocio)>=14){
				$sql = "SELECT sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno FROM sm_socios WHERE sm_socios.codigoSocio = '$codigoSocio'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$nombre    = $dato[nombre];
				$apPaterno = $dato[apPaterno];
				$apMaterno = $dato[apMaterno];
				$nombreSocio= trim($nombre.' '.$apPaterno.' '.$apMaterno);

				$sql = "SELECT COUNT(sm_lotes_socio.lotes) AS lotes FROM sm_lotes_socio WHERE sm_lotes_socio.codigoSocio = '$codigoSocio'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$lotes    = $dato[lotes];
				
				$codigoActividad = $_POST[codigoActividad];
				$sql = "SELECT sm_mod_actividades.tipoActividad, sm_mod_actividades.temaActividad, sm_mod_actividades.formaActividad, sm_mod_actividades.formaControl, sm_mod_actividades.fechaActividad, sm_mod_actividades.horaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta FROM sm_mod_actividades WHERE sm_mod_actividades.codigoActividad = '$codigoActividad'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$tipoActividad  = $dato[tipoActividad];
				$temaActividad  = $dato[temaActividad];
				$formaActividad = $dato[formaActividad];
				$formaControl   = $dato[formaControl];
				$fechaActividad = $dato[fechaActividad];
				$horaActividad  = $dato[horaActividad];
				$mTardanza      = $dato[mTardanza];
				$mFalta         = $dato[mFalta];

				$fechaACT       = strtoupper(infoFecha($fechaActividad,'normal'));
				$horaACT        = infoHora($horaActividad);
				$fecha          = $fechaACT." // ".$horaACT;

				$sql = "SELECT asistio, ingreso, salida FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$asistio  = $dato[asistio];
				$ingreso  = $dato[ingreso];
				$salida  = $dato[salida];

				if($formaControl==0){
					if($asistio=='IN' && $ingreso=='00:00:00'){
						$json=retraso($horaActividad,$hora);
						$retrasoHora=$json['retrasoHora'];
						$retrasoMinutos=$json['retrasoMinutos'];

						if($retrasoHora <= 0){
							$retrasoHora=0;
						}else{
							$retrasoHora=$retrasoHora;
						}

						if($retrasoMinutos < 0){
							$retrasoMinutos=0;
						}else{
							$retrasoMinutos=$retrasoMinutos;
						}

						if($retrasoHora < 0){
							$asistencia='temprano';
							$minutosTarde=0;
							$evalua="1";
							$asistio='SI';
							$multa=0;
						}else{
							if($retrasoMinutos>=0 && $retrasoMinutos<=$tiempoRestraso){
								$asistencia='puntual';
								$minutosTarde=0;
								$retraso=$minutosTarde/100;
								$evalua="2";
								$asistio='SI';
								$multa=0;
							}
							if($retrasoMinutos>$tiempoRestraso && $retrasoMinutos<=$tiempoFalta){
								$asistencia='tarde';
								$minutosTarde=ceros($retrasoMinutos,2);
								$retraso=$minutosTarde/100;
								$evalua="3";
								$asistio='SI';
								$multa=$mTardanza;
							}
							if($retrasoMinutos>$tiempoFalta){
								$asistencia='falta';
								$minutosTarde=ceros($retrasoMinutos,2);
								$retraso=$minutosTarde/100;
								$evalua="4";
								$asistio='NO';
								$multa=$mFalta;
							}
						}

						if($asistio=='IN' && ($asistencia=='temprano' || $asistencia=='puntual')){
							$proceso='registra_ingreso_puntual';
						}

						if($asistio=='IN' && $asistencia=='tarde'){
							$proceso='registra_ingreso_tardanza';
						}

						if($asistio=='IN' && $asistencia=='falta'){
							$proceso='registra_ingreso_falta';
						}

						$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso = '$hora', retraso='$retraso', multa='$multa' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
						$respuesta->estado  = 'registrado';
						
						$respuesta->retrasoHora  = $retrasoHora;
						$respuesta->retrasoMinutos  = $retrasoMinutos;
						$respuesta->retraso  = $retraso;
						$respuesta->asistencia  = $asistencia;
						$respuesta->multa  = $multa;
						$respuesta->evalua  = $evalua;
						$respuesta->sql  = $sql;
					}else{
						$respuesta->estado  = 'ya_registro_ingreso';
					}
				}else{
					$sql = "SELECT ingreso FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$ingreso  = $dato[ingreso];

					if($ingreso!='00:00:00'){
						$respuesta->estado  = 'ya_registro_ingreso';
					}else{
						$asistio='SI';
						$hora=$horaActividad;
						$retraso=0;
						$multa=0;
						$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso = '$hora', retraso='$retraso', multa='$multa' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
						$respuesta->estado  = 'registrado';
					}
				}
			}else{
				$respuesta->estado  = 'documento_no_valido';
			}
			
			$cerrarDB=cerrarDB();
		}

		if($operacion=='REGISTRA_ASISTENCIA_DNI'){
			$conexion=conexionDB();
			$DNISocio     =$_POST[DNISocio];
			if(strlen($DNISocio)==8){
				$sql="SELECT codigoSocio FROM sm_socios WHERE dni = '$DNISocio'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$codigoSocio = $dato[codigoSocio];

				if(strlen($codigoSocio)>=14){
					$sql = "SELECT sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno FROM sm_socios WHERE sm_socios.codigoSocio = '$codigoSocio'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$nombre    = $dato[nombre];
					$apPaterno = $dato[apPaterno];
					$apMaterno = $dato[apMaterno];
					$nombreSocio= trim($nombre.' '.$apPaterno.' '.$apMaterno);

					$sql = "SELECT COUNT(sm_lotes_socio.lotes) AS lotes FROM sm_lotes_socio WHERE sm_lotes_socio.codigoSocio = '$codigoSocio'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$lotes    = $dato[lotes];
					
					$codigoActividad = $_POST[codigoActividad];
					$sql = "SELECT sm_mod_actividades.tipoActividad, sm_mod_actividades.temaActividad, sm_mod_actividades.formaActividad, sm_mod_actividades.formaControl, sm_mod_actividades.fechaActividad, sm_mod_actividades.horaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta FROM sm_mod_actividades WHERE sm_mod_actividades.codigoActividad = '$codigoActividad'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$tipoActividad  = $dato[tipoActividad];
					$temaActividad  = $dato[temaActividad];
					$formaActividad = $dato[formaActividad];
					$formaControl   = $dato[formaControl];
					$fechaActividad = $dato[fechaActividad];
					$horaActividad  = $dato[horaActividad];
					$mTardanza      = $dato[mTardanza];
					$mFalta         = $dato[mFalta];

					$fechaACT       = strtoupper(infoFecha($fechaActividad,'normal'));
					$horaACT        = infoHora($horaActividad);
					$fecha          = $fechaACT." // ".$horaACT;

					$sql = "SELECT asistio, ingreso, salida FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$asistio  = $dato[asistio];
					$ingreso  = $dato[ingreso];
					$salida  = $dato[salida];

					if($formaControl==0){
						if($asistio=='IN' && $ingreso=='00:00:00'){
							$json=retraso($horaActividad,$hora);
							$retrasoHora=$json['retrasoHora'];
							$retrasoMinutos=$json['retrasoMinutos'];

							if($retrasoHora <= 0){
								$retrasoHora=0;
							}else{
								$retrasoHora=$retrasoHora;
							}

							if($retrasoMinutos < 0){
								$retrasoMinutos=0;
							}else{
								$retrasoMinutos=$retrasoMinutos;
							}

							if($retrasoHora < 0){
								$asistencia='temprano';
								$minutosTarde=0;
								$evalua="1";
								$asistio='SI';
								$multa=0;
							}else{
								if($retrasoMinutos>=0 && $retrasoMinutos<=$tiempoRestraso){
									$asistencia='puntual';
									$minutosTarde=0;
									$retraso=$minutosTarde/100;
									$evalua="2";
									$asistio='SI';
									$multa=0;
								}
								if($retrasoMinutos>$tiempoRestraso && $retrasoMinutos<=$tiempoFalta){
									$asistencia='tarde';
									$minutosTarde=ceros($retrasoMinutos,2);
									$retraso=$minutosTarde/100;
									$evalua="3";
									$asistio='SI';
									$multa=$mTardanza;
								}
								if($retrasoMinutos>$tiempoFalta){
									$asistencia='falta';
									$minutosTarde=ceros($retrasoMinutos,2);
									$retraso=$minutosTarde/100;
									$evalua="4";
									$asistio='NO';
									$multa=$mFalta;
								}
							}

							if($asistio=='IN' && ($asistencia=='temprano' || $asistencia=='puntual')){
								$proceso='registra_ingreso_puntual';
							}

							if($asistio=='IN' && $asistencia=='tarde'){
								$proceso='registra_ingreso_tardanza';
							}

							if($asistio=='IN' && $asistencia=='falta'){
								$proceso='registra_ingreso_falta';
							}

							$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso = '$hora', retraso='$retraso', multa='$multa' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
							$row=mysqli_query($conexion,$sql);
							$respuesta->estado  = 'registrado';
							
							$respuesta->retrasoHora  = $retrasoHora;
							$respuesta->retrasoMinutos  = $retrasoMinutos;
							$respuesta->retraso  = $retraso;
							$respuesta->asistencia  = $asistencia;
							$respuesta->multa  = $multa;
							$respuesta->evalua  = $evalua;
							$respuesta->sql  = $sql;
						}else{
							$respuesta->estado  = 'ya_registro_ingreso';
						}
					}else{
						$sql = "SELECT ingreso FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
						$dato=mysqli_fetch_array($row);
						$ingreso  = $dato[ingreso];

						if($ingreso!='00:00:00'){
							$respuesta->estado  = 'ya_registro_ingreso';
						}else{
							$asistio='SI';
							$hora=$horaActividad;
							$retraso=0;
							$multa=0;
							$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso = '$hora', retraso='$retraso', multa='$multa' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
							$row=mysqli_query($conexion,$sql);
							$respuesta->estado  = 'registrado';
						}
					}
				}else{
					$respuesta->estado  = 'documento_no_valido';
				}				
			}else{
				$respuesta->estado  = 'documento_no_valido';
			}
			$cerrarDB=cerrarDB();
		}

		if($operacion=='REGISTRA_SALIDA'){
			$conexion=conexionDB();
			$codigoSocio     =$_POST[codigoSocio];
			if(strlen($codigoSocio)>=14){
				$sql = "SELECT sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno FROM sm_socios WHERE sm_socios.codigoSocio = '$codigoSocio'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$nombre    = $dato[nombre];
				$apPaterno = $dato[apPaterno];
				$apMaterno = $dato[apMaterno];
				$nombreSocio= trim($nombre.' '.$apPaterno.' '.$apMaterno);

				$sql = "SELECT COUNT(sm_lotes_socio.lotes) AS lotes FROM sm_lotes_socio WHERE sm_lotes_socio.codigoSocio = '$codigoSocio'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$lotes    = $dato[lotes];
				
				$codigoActividad = $_POST[codigoActividad];
				$sql = "SELECT sm_mod_actividades.tipoActividad, sm_mod_actividades.temaActividad, sm_mod_actividades.formaActividad, sm_mod_actividades.formaControl, sm_mod_actividades.fechaActividad, sm_mod_actividades.horaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta FROM sm_mod_actividades WHERE sm_mod_actividades.codigoActividad = '$codigoActividad'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$tipoActividad  = $dato[tipoActividad];
				$temaActividad  = $dato[temaActividad];
				$formaActividad = $dato[formaActividad];
				$formaControl   = $dato[formaControl];
				$fechaActividad = $dato[fechaActividad];
				$horaActividad  = $dato[horaActividad];
				$mTardanza      = $dato[mTardanza];
				$mFalta         = $dato[mFalta];

				$fechaACT       = strtoupper(infoFecha($fechaActividad,'normal'));
				$horaACT        = infoHora($horaActividad);
				$fecha          = $fechaACT." // ".$horaACT;

				if($formaControl==0){
					$sql = "SELECT asistio, ingreso, salida, retraso FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$asistio  = $dato[asistio];
					$ingreso  = $dato[ingreso];
					$salida  = $dato[salida];
					$retraso  = $dato[retraso];

					if($retraso>0){
						$retraso=$retraso*100;
					}else{
						$retraso=$retraso;
					}

					$respuesta->asistio  = $asistio;
					$respuesta->ingreso  = $ingreso;
					$respuesta->salida  = $salida;
					
					if($asistio!='IN' && $ingreso!='00:00:00' && $salida=='00:00:00'){
						if($asistio=='SI' && $ingreso!='00:00:00'){
							$sql = "UPDATE sm_mod_asistencia SET salida = '$hora' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
							$row=mysqli_query($conexion,$sql);
							$respuesta->estado  = 'registrado';
						}else{
							$respuesta->estado  = 'falto_por_tardanza';
						}
					}else{
						$respuesta->estado  = 'ya_registro_salida';
					}
				}else{
					$sql = "SELECT asistio, ingreso, salida, retraso FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$asistio  = $dato[asistio];
					$ingreso  = $dato[ingreso];
					$salida   = $dato[salida];

					$respuesta->asistio  = $asistio;
					$respuesta->ingreso  = $ingreso;
					$respuesta->salida  = $salida;

					if($asistio=='SI' && $ingreso!='00:00:00' && $salida!='00:00:00'){
						$respuesta->estado  = 'ya_registro_salida';
					}

					if($asistio=='SI' && $ingreso!='00:00:00' && $salida=='00:00:00'){
						$asistio='SI';
						$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso='$horaActividad', salida = '$hora' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
						$respuesta->estado  = 'registrado';
					}

					if($asistio=='SI' && $ingreso=='00:00:00' && $salida=='00:00:00'){
						$asistio='SI';
						$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso='$horaActividad', salida = '$hora' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
						$respuesta->estado  = 'registrado';
					}

					if($asistio=='IN' && $ingreso=='00:00:00' && $salida=='00:00:00'){
						$asistio='SI';
						$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso='$horaActividad', salida = '$hora' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
						$respuesta->estado  = 'registrado';
					}
				}
			}else{
				$respuesta->estado  = 'documento_no_valido';
			}
			
			$cerrarDB=cerrarDB();
		}

		if($operacion=='REGISTRA_SALIDA_DNI'){
			$conexion=conexionDB();
			$DNISocio     =$_POST[DNISocio];

			if(strlen($DNISocio)==8){
				$sql="SELECT codigoSocio FROM sm_socios WHERE dni = '$DNISocio'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$codigoSocio = $dato[codigoSocio];

				if(strlen($codigoSocio)>=14){
					$sql = "SELECT sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno FROM sm_socios WHERE sm_socios.codigoSocio = '$codigoSocio'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$nombre    = $dato[nombre];
					$apPaterno = $dato[apPaterno];
					$apMaterno = $dato[apMaterno];
					$nombreSocio= trim($nombre.' '.$apPaterno.' '.$apMaterno);

					$sql = "SELECT COUNT(sm_lotes_socio.lotes) AS lotes FROM sm_lotes_socio WHERE sm_lotes_socio.codigoSocio = '$codigoSocio'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$lotes    = $dato[lotes];
					
					$codigoActividad = $_POST[codigoActividad];
					$sql = "SELECT sm_mod_actividades.tipoActividad, sm_mod_actividades.temaActividad, sm_mod_actividades.formaActividad, sm_mod_actividades.formaControl, sm_mod_actividades.fechaActividad, sm_mod_actividades.horaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta FROM sm_mod_actividades WHERE sm_mod_actividades.codigoActividad = '$codigoActividad'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$tipoActividad  = $dato[tipoActividad];
					$temaActividad  = $dato[temaActividad];
					$formaActividad = $dato[formaActividad];
					$formaControl   = $dato[formaControl];
					$fechaActividad = $dato[fechaActividad];
					$horaActividad  = $dato[horaActividad];
					$mTardanza      = $dato[mTardanza];
					$mFalta         = $dato[mFalta];

					$fechaACT       = strtoupper(infoFecha($fechaActividad,'normal'));
					$horaACT        = infoHora($horaActividad);
					$fecha          = $fechaACT." // ".$horaACT;

					if($formaControl==0){
						$sql = "SELECT asistio, ingreso, salida, retraso FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
						$dato=mysqli_fetch_array($row);
						$asistio  = $dato[asistio];
						$ingreso  = $dato[ingreso];
						$salida  = $dato[salida];
						$retraso  = $dato[retraso];

						if($retraso>0){
							$retraso=$retraso*100;
						}else{
							$retraso=$retraso;
						}

						$respuesta->asistio  = $asistio;
						$respuesta->ingreso  = $ingreso;
						$respuesta->salida  = $salida;
						
						if($asistio!='IN' && $ingreso!='00:00:00' && $salida=='00:00:00'){
							if($asistio=='SI' && $ingreso!='00:00:00'){
								$sql = "UPDATE sm_mod_asistencia SET salida = '$hora' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
								$row=mysqli_query($conexion,$sql);
								$respuesta->estado  = 'registrado';
							}else{
								$respuesta->estado  = 'falto_por_tardanza';
							}
						}else{
							$respuesta->estado  = 'ya_registro_salida';
						}
					}else{
						$sql = "SELECT asistio, ingreso, salida, retraso FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
						$dato=mysqli_fetch_array($row);
						$asistio  = $dato[asistio];
						$ingreso  = $dato[ingreso];
						$salida   = $dato[salida];

						$respuesta->asistio  = $asistio;
						$respuesta->ingreso  = $ingreso;
						$respuesta->salida  = $salida;

						if($asistio=='SI' && $ingreso!='00:00:00' && $salida!='00:00:00'){
							$respuesta->estado  = 'ya_registro_salida';
						}

						if($asistio=='SI' && $ingreso!='00:00:00' && $salida=='00:00:00'){
							$asistio='SI';
							$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso='$horaActividad', salida = '$hora' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
							$row=mysqli_query($conexion,$sql);
							$respuesta->estado  = 'registrado';
						}

						if($asistio=='SI' && $ingreso=='00:00:00' && $salida=='00:00:00'){
							$asistio='SI';
							$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso='$horaActividad', salida = '$hora' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
							$row=mysqli_query($conexion,$sql);
							$respuesta->estado  = 'registrado';
						}

						if($asistio=='IN' && $ingreso=='00:00:00' && $salida=='00:00:00'){
							$asistio='SI';
							$sql = "UPDATE sm_mod_asistencia SET asistio='$asistio', ingreso='$horaActividad', salida = '$hora' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
							$row=mysqli_query($conexion,$sql);
							$respuesta->estado  = 'registrado';
						}
					}
				}else{
					$respuesta->estado  = 'documento_no_valido';
				}
			}else{
				$respuesta->estado  = 'documento_no_valido';
			}
			$cerrarDB=cerrarDB();
		}

		if($operacion=='cerrarActividad'){
			$conexion=conexionDB();
			$codigoActividad = $_SESSION['codigoActividad'];

			$sql = "SELECT sm_mod_actividades.tipoActividad, sm_mod_actividades.temaActividad, sm_mod_actividades.formaActividad, sm_mod_actividades.formaControl, sm_mod_actividades.fechaActividad, sm_mod_actividades.horaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta FROM sm_mod_actividades WHERE sm_mod_actividades.codigoActividad = '$codigoActividad'";
			$row=mysqli_query($conexion,$sql);
			$dato=mysqli_fetch_array($row);
			$tipoActividad  = $dato[tipoActividad];
			$temaActividad  = $dato[temaActividad];
			$formaActividad = $dato[formaActividad];
			$formaControl   = $dato[formaControl];
			$fechaActividad = $dato[fechaActividad];
			$horaActividad  = $dato[horaActividad];
			$mTardanza      = $dato[mTardanza];
			$mFalta         = $dato[mFalta];

			if($formaControl==0){
				//ACTUALIZA TABLA sm_mod_asistencia_json
				$sql = "UPDATE sm_mod_actividades SET procesado='SI' WHERE sm_mod_asistencia_json.codigoActividad = '$codigoActividad'";
				$row=mysqli_query($conexion,$sql);
			
				//ACTUALIZA TABLA sm_mod_asistencia SOCIO POR SOCIO
				$sql="SELECT sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.asistio, sm_mod_asistencia.ingreso, sm_mod_asistencia.salida, sm_mod_asistencia.retraso, sm_mod_asistencia.multa FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad'";
				$rs=mysqli_query($conexion,$sql);
				$i=1;
				while($n=mysqli_fetch_array($rs)){
					$codigoSocio = $n[codigoSocio];
					$lotes       = $n[lotes];
					$asistio     = $n[asistio];
					$ingreso     = $n[ingreso];
					$salida      = $n[salida];
					$retraso     = $n[retraso];
					$multa       = $n[multa];
					$infoRetraso = $retraso*100;
					$mTardanza   = $mTardanza*$lotes;
					$mFalta      = $mFalta*$lotes;

					// ASISTENTE PUNTUAL (INICIO A FIN) SIN MULTAS
					if($asistio=='SI' && $ingreso!='00:00:00' && $salida!='00:00:00' && $infoRetraso<=$tiempoRestraso){
						$sql = "UPDATE sm_mod_asistencia SET asistio='SI', multa='0.00' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
					}

					// ASISTENTE TARDON (INICIO A FIN) CON MULTAS
					if($asistio=='SI' && $ingreso!='00:00:00' && $salida!='00:00:00' && ($infoRetraso>=$tiempoRestraso || $infoRetraso<$tiempoFalta)){
						$sql = "UPDATE sm_mod_asistencia SET asistio='SI', multa='$mTardanza' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
					}

					// ASISTENTE PUNTUAL (REGISTRO INICIO Y SE FUE) CON MULTAS
					if($asistio=='SI' && $ingreso!='00:00:00' && $salida=='00:00:00'){
						$sql = "UPDATE sm_mod_asistencia SET asistio='NO', multa='$mFalta' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
					}

					// ASISTENTE (LLEGO FUERA DE HORA) CON MULTAS
					if($asistio=='NO' && $ingreso!='00:00:00' && $infoRetraso>=$tiempoFalta){
						$sql = "UPDATE sm_mod_asistencia SET asistio='NO', multa='$mFalta' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
					}

					// INASISTENTES
					if($asistio=='IN' && $ingreso=='00:00:00' && $salida=='00:00:00'){
						$sql = "UPDATE sm_mod_asistencia SET asistio='NO', multa='$mFalta' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
					}
					$i++;
				}
			}else{
				//ACTUALIZA TABLA sm_mod_asistencia SOCIO POR SOCIO
				$sql="SELECT sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.asistio, sm_mod_asistencia.ingreso, sm_mod_asistencia.salida, sm_mod_asistencia.retraso, sm_mod_asistencia.multa FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad'";
				$rs=mysqli_query($conexion,$sql);
				$i=1;
				while($n=mysqli_fetch_array($rs)){
					$codigoSocio = $n[codigoSocio];
					$lotes       = $n[lotes];
					$asistio     = $n[asistio];
					$ingreso     = $n[ingreso];
					$salida      = $n[salida];
					$retraso     = $n[retraso];
					$multa       = $n[multa];
					$infoRetraso = $retraso*100;
					$mTardanza   = $mTardanza*$lotes;
					$multaFalta  = $mFalta*$lotes;

					//$respuesta->multaFalta  = $multaFalta;
					// ASISTENTE PUNTUAL (INICIO A FIN) SIN MULTAS
					if($asistio!='SI'){
						$sql = "UPDATE sm_mod_asistencia SET asistio='NO', multa='$multaFalta' WHERE codigoActividad = '$codigoActividad' AND codigoSocio = '$codigoSocio'";
						$row=mysqli_query($conexion,$sql);
					}

					$multaFalta=0;
					
					$i++;
				}
			}

			$sql = "UPDATE sm_mod_actividades SET procesado='SI' WHERE codigoActividad = '$codigoActividad'";
			$row=mysqli_query($conexion,$sql);

			$cerrarDB=cerrarDB();
			
			session_start();
			unset($_SESSION['codigoActividad']);
			unset($_SESSION['estadoActividad']);
			
			$respuesta->socios  = $i;
			$respuesta->resultado  = "OK";
		}
	}else{
		$respuesta->mensaje = "SESION_CERRADA";
	}

	echo json_encode($respuesta);