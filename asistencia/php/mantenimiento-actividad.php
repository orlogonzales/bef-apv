<?php
	include('funciones.php');
	$conexion=conexionDB();
	session_start();
	$fecha=infoTiempo('fecha');
	$hora=infoTiempo('hora');
	date_default_timezone_set("America/Lima");

	if($_GET[operacion]=="SUBIR_ARCHIVO"){
		$permitidos =array("aplication/octet-stream");
		$archivoACT =$_FILES['archivo']['name'];
		$archivo    =explode(".", $_FILES['archivo']['name']);
		$tipoFile   =end($archivo);
		$nombre     =$_FILES['archivo']['name'];
		$upload     ='../json/'.$nombre;

		if (file_exists($upload)){ unlink($upload); }

		if($archivoACT){
			if ($tipoFile=='json'){
				$resultado = @move_uploaded_file($_FILES['archivo']["tmp_name"], $upload);
				if ($resultado){
					$json            = file_get_contents($upload);
					$datos           = json_decode($json);
					$tipoActividad   = $datos[0]->tipoActividad;
					$temaActividad   = $datos[0]->temaActividad;
					$fechaActividad  = $datos[0]->fechaActividad;
					$horaActividad   = $datos[0]->horaActividad;
					
					if($horaActividad>=1 and $horaActividad<=12){ $uh="AM"; }
					if($horaActividad>=13 and $horaActividad<=23){ $uh="PM"; }

					if(is_null($tipoActividad)){
						unlink($upload);
						$respuesta->mensaje = "ARCHIVONOVALIDO";
					}else{
						if($tipoActividad=="ASA"){ $titulo="INFORMACION DE ASAMBLEA"; }
						if($tipoActividad=="FAE"){ $titulo="INFORMACION DE FAENA"; }
						$_SESSION['$archivo'] =$upload;
						$respuesta->titulo = $titulo;
						$respuesta->tema = $temaActividad;
						$respuesta->fecha = strtoupper(infoFecha($fechaActividad,'normal'));
						$respuesta->hora = horaCorta($horaActividad).' '.$uh;
						$respuesta->mensaje = "ARCHIVOSUBIDO";
					}
				} else {
					$respuesta->mensaje = "ERRORSERVER";
				}
			} else { $respuesta->mensaje = "ERRORFORMATO"; }
		}else{ $respuesta->mensaje = "SINFILE"; }
	}

	if($_POST[operacion]=="PROCESAR_ACTIVIDAD"){
		$archivo         =$_SESSION['$archivo'];
		$json            =file_get_contents($archivo);
		$datos           =json_decode($json);
		$tipoActividad   =$datos[0]->tipoActividad;
		$codigoActividad =$datos[0]->codigoActividad;
		$temaActividad   =$datos[0]->temaActividad;
		$fechaActividad  =$datos[0]->fechaActividad;
		$horaActividad   =$datos[0]->horaActividad;
		$mTardanza       =$datos[0]->mTardanza;
		$mFalta          =$datos[0]->mFalta;
		$archivoACT      =$codigoActividad.".json";
		$codigoUsuario   ="";
		$estado          ="ACT";

		$sql="INSERT INTO sm_terminal_actividades(codigoActividad, tipoActividad, temaActividad, fechaActividad, horaActividad, mTardanza, mFalta, archivoACT, fecha, hora, codigoUsuario, estado) VALUES('$codigoActividad', '$tipoActividad', '$temaActividad', '$fechaActividad', '$horaActividad', '$mTardanza', '$mFalta', '$archivoACT', '$fecha', '$hora', '$codigoUsuario', '$estado')";
		$rs=mysqli_query($conexion,$sql);

		if($rs==true){
			unset($_SESSION['archivo']);
			$_SESSION['$codigoActividad'] =$codigoActividad;
			$respuesta->mensaje = "FINALILZADO";
		}else{
			$respuesta->mensaje = "ERROR";
		}
	}

	if($_POST[operacion]=="REGISTRA_ASISTENCIA"){
		$codigoSocio     =$_POST[codigoSocio];
		$nombreSocio     =infoSocio($codigoSocio,'nombre');
		$apPaterno       =infoSocio($codigoSocio,'apPaterno');
		$apMaterno       =infoSocio($codigoSocio,'apMaterno');
		$lotes           =infoSocio($codigoSocio,'lotes');
		$nombre          =texto($nombreSocio." ".$apPaterno." ".$apMaterno);
		$codigoActividad =$_SESSION['$codigoActividad'];
		$actividad       =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
		$mTarde          =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'infoMultaporTardanza');
		$fechaACT        =strtoupper(infoFecha(infoActividad($idJuntaDirectiva,$codigoActividad,'','fechaActividad'),'normal'));
		$horaACT         =infoHora(infoActividad($idJuntaDirectiva,$codigoActividad,'','horaActividad'));
		$fecha           =$fechaACT." // ".$horaACT;
		$ingreso         =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'ingreso');
		$salida          =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'salida');
		

		if($ingreso==""){ $registra="INGRESO"; }
		if($ingreso!="" and $salida=="00:00:00"){ $registra="SALIDA"; }

		if($registra=="INGRESO" and $salida==""){
			$horaActividad=infoActividad($idJuntaDirectiva,$codigoActividad,'','horaActividad');
			$horaActual=$hora;
			$ingreso=$horaActual;
			if($horaActual>=$horaActividad){
				$retraso=retraso($horaActividad,$horaActual);
			}else{
				$retraso=0;
			}
			$salida="";
			$sql="INSERT INTO sm_terminal_asistencia(codigoActividad, codigoSocio, ingreso, retraso, salida) VALUES('$codigoActividad', '$codigoSocio', '$ingreso', '$retraso', '$salida')";
			$rs=mysqli_query($conexion,$sql);

			if($rs==true){
				$respuesta->mensaje = "INGRESO_REGISTRADO";
			}else{
				$respuesta->mensaje = "ERROR_REGISTRO";
			}
		}

		if($registra=="SALIDA" and $ingreso!=""){
			$sql="UPDATE sm_terminal_asistencia SET salida='$hora' WHERE codigoSocio='$codigoSocio' AND  codigoActividad='$codigoActividad'";
			$rs=mysqli_query($conexion,$sql);
			
			if($rs==true){
				$ingreso    =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'ingreso');
				$salida     =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'salida');
				$asistencia ="INGRESO ".$ingreso." // SALIDA: ".$salida;
				$retraso    =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'retraso');
				$fixdni     =substr($codigoSocio, 0, strlen($codigoSocio)-1);
				$dni        =substr($fixdni, 5, 8);

				if($retraso>0.15){
					$tarde="TARDANZA: ".$retraso." MINUTOS";
					$multa="MULTA: S/.".moneda($lotes*$mTarde)." x ".ceros($lotes,2)." LOTES";
				}else{
					$tarde="";
					$multa="";
				}

				if(($handle = @fopen("\\\\OGG-LABS\Terminal", "w")) === FALSE) { die('No se pudo Imprimir, Verifique su conexion con el Terminal'.$handle); }
				fwrite($handle, chr(27). chr(64));//reinicio
				fwrite($handle, chr(27). chr(100). chr(0));
				fwrite($handle, chr(27). chr(97). chr(1)); //centrado
				fwrite($handle,"==========================================");
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(32). chr(3)); //Espacio entre letras
				fwrite($handle, chr(27). chr(33). chr(16)); //Fuente doble alto
				fwrite($handle,"APV RESIDENCIAL EL PARAISO");
				fwrite($handle, chr(27). chr(33). chr(0)); //Fuente doble alto
				fwrite($handle, chr(27). chr(32). chr(0)); //Espacio entre letras
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle,"==========================================");
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, $actividad);
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, $fecha);
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle,"------------------------------------------");
				fwrite($handle, chr(27). chr(33). chr(0)); //fuente normal
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, $nombre);
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, "DNI:". $dni);
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(32). chr(3)); //Espacio entre letras
				fwrite($handle, chr(27). chr(33). chr(16)); //Fuente doble alto
				fwrite($handle, $codigoSocio);
				fwrite($handle, chr(27). chr(33). chr(0)); //Fuente doble alto
				fwrite($handle, chr(27). chr(32). chr(0)); //Espacio entre letras
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle,"------------------------------------------");
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, $asistencia);
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, $tarde);
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, $multa);
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(27). chr(100). chr(1));
				fwrite($handle, chr(29). chr(86). chr(1));
				fprintf($handle);
				$salida = shell_exec('USB001 lpr');
				fclose($handle);

				$respuesta->mensaje = "SALIDA_REGISTRADA";
			}else{
				$respuesta->mensaje = "ERROR_REGISTRO";
			}
		}

		if($ingreso>"00:00:00" and $salida>"00:00:00"){ $respuesta->mensaje = "SOCIO_REGISTRADO"; }
	}

	if($_POST[operacion]=="TRANSFERIR_ASISTENCIA"){
		$codigoActividad =$_POST[codigoActividad];
		$horaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','horaActividad');
		$fechaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','fechaActividad');
		$tipoActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','tipoActividad');
		$terminal        =terminal();
		$ruta            ="../asistencias/";

		if($tipoActividad=="ASA"){ $actividad="ASAMBLEA"; }
		if($tipoActividad=="FAE"){ $actividad="FAENA"; }

		$archivo         ="T".ceros($terminal,2)."-ASISTENCIA-".$actividad."-".infoFecha($fechaActividad,'file').".json";

		if (file_exists($ruta.$archivo)){  unlink($ruta.$archivo); }

		$sql="SELECT codigoSocio, ingreso, retraso, salida FROM sm_terminal_asistencia WHERE codigoActividad='$codigoActividad'";
		$rs=mysqli_query($conexion,$sql);
		mysqli_set_charset($conexion, "utf8");
		$asistencia=array();
		$procesados=1;
		while($dato=mysqli_fetch_array($rs)){
			$codigoSocio  =$dato[codigoSocio];
			$ingreso      =$dato[ingreso];
			$retraso      =$dato[retraso];
			$salida       =$dato[salida];
			$asistencia[] =array('codigoActividad'=>$codigoActividad, 'horaActividad'=>$horaActividad, 'codigoSocio'=>$codigoSocio, 'ingreso'=>$ingreso, 'retraso'=>$retraso, 'salida'=>$salida, 'terminal'=>$terminal);
			$procesados++;
		}

		$fp = fopen($ruta.$archivo, 'w');
		fwrite($fp, json_encode($asistencia));
		fclose($fp);

		if (file_exists($ruta.$archivo)){ 
			$sql="UPDATE sm_terminal_actividades SET estado='INC' WHERE codigoActividad='$codigoActividad'";
			$rs=mysqli_query($conexion,$sql);
			$respuesta->mensaje = "TRANSFERENCIA_FINALIZADA";
		}else{
			$respuesta->mensaje = "ERROR_GENERAR_ARCHIVO";
		}
	}

	if($_POST[operacion]=="INICIAR_CONTROL_ASISTENCIA"){
		$codigoActividad =$_POST[codigoActividad];
		$estadoActividad =infoActividad($idJuntaDirectiva,$codigoActividad,'','estado');

		if($estadoActividad=="INC"){
			unset($_SESSION['$codigoActividad']);
			$respuesta->mensaje = "ACTIVIDAD_INACTIVA";
		}else{
			$_SESSION['$codigoActividad']=$codigoActividad;
			$respuesta->mensaje = "INCIAR_CONTROL";
		}

	}
	
	if($_POST[operacion]=="ACTUALIZAR_HORA"){
		$codigoActividad =$_POST[codigoActividad];
		$horaActividad =$_POST[horaActividad];
		
		$sql="UPDATE sm_terminal_actividades SET horaActividad='$horaActividad' WHERE codigoActividad='$codigoActividad'";
		$rs=mysqli_query($conexion,$sql);

		if($rs==true){
				$respuesta->mensaje = "HORA_ACTUALIZADA";
		}else{
			$respuesta->mensaje = "ERROR_REGISTRO";
		}
	}

	echo json_encode($respuesta);
?>