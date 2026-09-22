<?php
	session_start();
	include ('funciones.php');
	$conexion    =conexionDB();
	$conexionBEF =conexionBEF();
	$codigoSocio =$_POST[codigoSocio];
	$operacion   =$_POST[operacion];
	$subir       =$_GET[subir];
	$fecha       =infoTiempo('fecha');
	$hora        =infoTiempo('hora');
	$dniUsuario  =$_SESSION['dni_apv'];
	$usuario     =$dniUsuario;
	mysqli_set_charset($conexion, "utf8"); 

	$servidor=$_SERVER['HTTP_HOST'];
	if($servidor=='app.bef'){
		$urlSistema="http://{$_SERVER['HTTP_HOST']}/ventas";
		$urlSocios="http://{$_SERVER['HTTP_HOST']}/apv";
	}else{
		$urlSistema="https://{$_SERVER['HTTP_HOST']}/ventas";
		$urlSocios="https://{$_SERVER['HTTP_HOST']}/apv";
	}

	if($operacion=="ELIMINA_CUENTA_SOCIO"){
		$sql="CALL eliminaSocio('$codigoSocio')";
		$socios=mysqli_query($conexion,$sql);
		$respuesta->consulta =$sql;

		if($socios){ $respuesta->mensaje ="CUENTA_ELIMINADA"; }else{ $respuesta->mensaje ="ERROR_CUENTA_ELIMINADA"; }
	}

	if($operacion=="MOVER_DATOS_CUENTA_SOCIO"){
		$cuenta_01=$_POST[cuenta_01];
		$cuenta_02=$_POST[cuenta_02];

		$sql="UPDATE sm_socios_observacion SET codigoSocio='$cuenta_02' WHERE codigoSocio='$cuenta_01'";
		$socios=mysqli_query($conexion,$sql);

		//->$sql="UPDATE sm_mod_cuotas_socios SET codigoSocio='$cuenta_02' WHERE codigoSocio='$cuenta_01'";
		//->$sql="UPDATE sm_procesos_cuotas SET codigoSocio='$cuenta_02' WHERE codigoSocio='$cuenta_01'";
		//->$sql="UPDATE sm_procesos_socios SET codigoSocio='$cuenta_02' WHERE codigoSocio='$cuenta_01'";
		//->$sql="UPDATE sm_procesos_actividades SET codigoSocio='$cuenta_02' WHERE codigoSocio='$cuenta_01'";

		$sql="CALL eliminaSocio('$cuenta_01')";
		$socios=mysqli_query($conexion,$sql);

		if($socios){ $respuesta->mensaje ="DATOS_SOCIOS_MOVIDOS"; }else{ $respuesta->mensaje ="ERROR_DATOS_SOCIOS_MOVIDOS"; }
	}

	if($operacion=="EDITA_DATOS_SOCIO"){
		$dni             =$_POST[dni];
		$nombre          =$_POST[nombre];
		$apPaterno       =$_POST[apPaterno];
		$apMaterno       =$_POST[apMaterno];
		$genero          =$_POST[genero];
		$nacionalidad    =$_POST[nacionalidad];
		$fechaNacimiento =fechaSQL($_POST[fechaNacimiento]);
		$estadoCivil     =$_POST[estadoCivil];
		$direccion       =$_POST[direccion];
		$departamento    =$_POST[departamento];
		$provincia       =$_POST[provincia];
		$distrito        =$_POST[distrito];
		$telefono        =$_POST[telefono];
		$celular         =$_POST[celular];

		$sql="UPDATE sm_socios SET genero='$genero', estadoCivil='$estadoCivil', direccion='$direccion', telefono='$telefono', celular='$celular' WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="UPDATE clientes SET genero='$genero', civil='$estadoCivil', direccion='$direccion', telefono='$telefono', celular='$celular' WHERE dni='$dni'";
		$rs=mysqli_query($conexionBEF,$sql);

		if($rs){
			$proceso="DATOS MODIFICADOS DE SOCIO ".$nombre;
			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="DATOS_SOCIOS_MODIFICADOS";
		}else{
			$respuesta->mensaje ="ERROR_DATOS_SOCIOS_MODIFICADOS";
		}
	}

	if($operacion=="ACTUALIZA_DIRECCION"){
		$codigoSocio =$_POST[codigoSocio];
		$codigoLote =$_POST[codigoLote];
		$direccion  =$_POST[direccion];

		$sql="UPDATE sm_lotes_socio SET direccion='$direccion' WHERE codigoSocio='$codigoSocio' AND codigoLote='$codigoLote'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$proceso="DIRECCION DE LOTE ".$codigoLote." FUE ACTUALIZADA.";
			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);
			
			if($rs){
				$respuesta->mensaje ="PROCESADO";
			}
		}
	}

	if($subir=="ACTUALIZA_FOTO"){
		$codigoSocio =$_GET[codigoSocio];
		$foto        =$_FILES['fotoSocio']['name'];
		$permitidos  =array("image/jpg", "image/jpeg");
		$limite_kb   =200;
		$temp        =explode(".", $_FILES['fotoSocio']['name']);
		$archivo     =$codigoSocio.'.'.end($temp);
		$ruta      ='../assets/images/socios/';
		$documento   ='../assets/images/socios/'.$archivo;
		unlink($documento);

		if (in_array($_FILES['fotoSocio']['type'], $permitidos) && $_FILES['fotoSocio']['size'] <= $limite_kb * 1024){
			$resultado = @move_uploaded_file($_FILES['fotoSocio']["tmp_name"], $ruta . $archivo);
			if ($resultado){

				$sql="UPDATE sm_socios SET fotoSocio='$archivo' WHERE codigoSocio='$codigoSocio'";
				$rs=mysqli_query($conexion,$sql);
				if($resultado){
					$proceso="FOTO DE SOCIO FUE ACTUALIZADA O CAMBIADA.";
					$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
					$rs=mysqli_query($conexion,$sql);
					$respuesta->mensaje = "PROCESADO";
				}else{
					$respuesta->mensaje = "NOPROCESADO";
				}

			} else {
				$respuesta->mensaje = "ERRORSERVER";
			}
		}else{ $respuesta->mensaje = "ERRORFILE"; }
	}

	if($operacion=="EXONERAR_DEUDAS_SOCIO"){
		$codigoSocio       =$_POST[codigoSocio];
		$tipoActividad     =$_POST[actividad];
		$codigoActividad   =$_POST[codigoActividad];
		$lotes             =infoSocios($codigoSocio,'lotes');
		$deuda             =$_POST[deuda];
		$porcentaje        =$_POST[porcentaje];
		$exonerado         =$_POST[montoAExonerar];
		$totalPago         =$_POST[totalPago];
		$nroDocumento      =$_POST[nroDocumento];
		$observacion       =$_POST[observacion];
		$codigoExoneracion =$codigoSocio.$codigoActividad;
		$dni               =$usuario;
		$nombreSocio=infoSocios($codigoSocio,'nombre');

		if($tipoActividad=='ASA'){ $infoActividad='ASAMBLEA'; }
		if($tipoActividad=='FAE'){ $infoActividad='FAENA'; }
		if($tipoActividad=='CUO'){ $infoActividad='CUOTA'; }

		if($tipoActividad!='CUO'){
			$nombreActividad=infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
		}else{
			$codigoConcepto=$codigoActividad;
			$nombreActividad=infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota');
		}

		$proceso="SE HA REGISTRADO UNA EXONERACION A FAVOR DE ".$nombreSocio." (".$codigoSocio.") DE ".$infoActividad." DE CONCEPTO ".$nombreActividad." (".$codigoActividad."), POR UNA DEUDA DE S/. ".moneda($deuda).", PAGANDO EL ".$porcentaje."% DEL TOTAL CON EL MONTO S/. ".moneda($totalPago);

		$sql="INSERT INTO sm_mod_exoneracion(codigoSocio, tipoActividad, codigoActividad, lotes, deuda, porcentaje, exonerado, totalPago, nroDocumento, observacion, codigoExoneracion, fecha, hora, usuario) VALUES('$codigoSocio', '$tipoActividad', '$codigoActividad', '$lotes', '$deuda', '$porcentaje', '$exonerado', '$totalPago', '$nroDocumento', '$observacion', '$codigoExoneracion', '$fecha', '$hora', '$usuario')";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			if($tipoActividad!="CUO"){
				$sql="UPDATE sm_mod_asistencia SET estadoPago='EX' WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'";
			}else{
				$sql="UPDATE sm_mod_cuotas_socios SET estadoPago='EX' WHERE codigoSocio='$codigoSocio' AND codigoCuota='$codigoActividad'";
			}
			$rs=mysqli_query($conexion,$sql);

			if($rs){
				if($totalPago>0){
					$movimiento       ='ING';
					$fechaOperacion   =$fecha;
					$tipoActividad    =$tipoActividad;
					$concepto         ="";
					$codigoSocio      =$codigoSocio;
					$codigoConcepto   =$codigoActividad;
					$tipoDocumento    ='VOU';
					$codigoBanco      ='';
					
					if($tipoActividad=="ASA"){ $codigoCuenta=infoActividad($idJuntaDirectiva,$codigoConcepto,'','cuentaBanco'); }
					if($tipoActividad=="FAE"){ $codigoCuenta=infoActividad($idJuntaDirectiva,$codigoConcepto,'','cuentaBanco'); }
					if($tipoActividad=="CUO"){ $codigoCuenta=infoCuota($idJuntaDirectiva,$codigoConcepto,'cuentaBanco'); }
					
					$codigoChequera   ='';
					$nroDocumento     =$nroDocumento;
					$monto            =$totalPago;
					$detalleConcepto  ='EXONERACION PAGO DE DEUDA DE '.$infoActividad.' <strong>'.$nombreActividad.'</strong>';
					$observaciones    ="";
					$codigoOperacion  =codOperacion($tipoActividad,$fecha,$hora,$usuario);

					$sql="INSERT INTO sm_mod_caja(movimiento, fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, codigoBanco, codigoCuenta, codigoChequera, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario) VALUES('$movimiento','$fechaOperacion','$tipoActividad','$concepto','$codigoSocio','$codigoConcepto','$tipoDocumento','$codigoBanco','$codigoCuenta','$codigoChequera','$nroDocumento','$monto','$detalleConcepto','$observaciones','$codigoOperacion','$fecha','$hora','$usuario')";
					$rs=mysqli_query($conexion,$sql);

					$proceso_caja="PAGO DE EXONERACION DE ".$infoActividad." <strong>".$nombreActividad."</strong> DE SOCIO ".$nombreSocio.", CON EL TOTAL S/. ".moneda($totalPago)." DE S/. ".moneda($deuda);
					$sql="INSERT INTO sm_procesos_caja(codigoOperacion, proceso, monto, fecha, hora, usuario) VALUES('$codigoOperacion', '$proceso_caja', '$monto', '$fecha', '$hora', '$usuario')";
					$rs=mysqli_query($conexion,$sql);

					if($tipoActividad!="CUO"){
						//REVISAR
						$sql="INSERT INTO sm_procesos_actividades(tipoActividad, codigoActividad, codigoSocio, proceso, fecha, hora, usuario) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$proceso_caja', '$fecha', '$hora', '$usuario')";
					}else{
						$sql="INSERT INTO sm_procesos_cuotas(codigoCuota, codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoActividad', '$codigoSocio', '$proceso_caja', '$fecha', '$hora', '$usuario')";
					}
					$rs=mysqli_query($conexion,$sql);
				}

				$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);

				$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dni', '$proceso', '$fecha', '$hora', '$usuario')";
				$rs=mysqli_query($conexion,$sql);
			}

			$respuesta->mensaje = "EXONERACION_PROCESADA";
		}else{
			$respuesta->mensaje = "EXONERACION_NO_PROCESADA";			
		}
	}

	if($operacion=="ELIMINA_EXONERACION_DEUDAS_SOCIO"){
		$codigoExoneracion =$_POST[codigoExoneracion];
		$tipoActividad     =$_POST[actividad];
		$codigoActividad   =infoExoneracionesALT($codigoSocio,$codigoExoneracion,'codigoActividad');
		$codigoConcepto    =$codigoActividad;
		$lotes             =infoSocios($codigoSocio,'lotes');
		$nombreSocio       =infoSocios($codigoSocio,'nombre');

		if($tipoActividad=='ASA'){ $infoActividad='ASAMBLEA'; }
		if($tipoActividad=='FAE'){ $infoActividad='FAENA'; }
		if($tipoActividad=='CUO'){ $infoActividad='CUOTA'; }

		if($tipoActividad!='CUO'){
			$nombreActividad=infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
		}else{
			$codigoConcepto=$codigoActividad;
			$nombreActividad=infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota');
		}
		
		$proceso="SE HA ELIMINADO UNA EXONERACION A FAVOR DE ".$nombreSocio." (".$codigoSocio.") DE ".$infoActividad." DE CONCEPTO ".$nombreActividad." (".$codigoActividad.")";

		$sql="DELETE FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoExoneracion='$codigoExoneracion'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			if($tipoActividad!="CUO"){
				$sql="UPDATE sm_mod_asistencia SET estadoPago='NP' WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'";
			}else{
				$sql="UPDATE sm_mod_cuotas_socios SET estadoPago='NP' WHERE codigoSocio='$codigoSocio' AND codigoCuota='$codigoActividad'";
			}
			$rs=mysqli_query($conexion,$sql);

			$sql="DELETE FROM sm_mod_caja WHERE  codigoSocio='$codigoSocio' AND tipoActividad='$tipoActividad' AND codigoConcepto='$codigoConcepto'";
			$rs=mysqli_query($conexion,$sql);


			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dni', '$proceso', '$fecha', '$hora', '$usuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="EXONERACION_ELIMINADA";
		}else{
			$respuesta->mensaje ="ERROR_EXONERACION_ELIMINADA";
		}
	}

	if($operacion=="VERIFICAR_VOUCHER_EXONERACION"){
		$nroDocumento=$_POST[nroDocumento];
		$verificaDocumento= verificaDOC($nroDocumento,'VOU');
		$respuesta->mensaje = $verificaDocumento;
	}

	if($operacion=="AGREGA_OBSERVACION"){
		$codigoSocio =$_POST[codigoSocio];
		$observacion =$_POST[observacion];
		$nombre      =infoSocios($codigoSocio,'nombreCorto');

		$sql="INSERT INTO sm_socios_observacion(codigoSocio, observacion, fecha, hora, usuario) VALUES('$codigoSocio', '$observacion', '$fecha', '$hora', '$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$proceso="NUEVA OBSERVACION AGREGADA A SOCIO ".$nombre;
			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="OBSERVACION_AGREGADA";
		}else{
			$respuesta->mensaje ="ERROR_OBSERVACION_AGREGADA";
		}		
	}

	if($operacion=="EDITA_OBSERVACION"){
		$codigoSocio =$_POST[codigoSocio];
		$id          =$_POST[id];
		$observacion =$_POST[observacion];
		$nombre      =infoSocios($codigoSocio,'nombreCorto');
		
		$sql="UPDATE sm_socios_observacion SET observacion='$observacion' WHERE id='$id'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$proceso="OBSERVACION MODIFICADA DE SOCIO ".$nombre;
			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="OBSERVACION_MODIFICADA";
		}else{
			$respuesta->mensaje ="ERROR_OBSERVACION_MODIFICADA";
		}		
	}

	if($operacion=="ELIMINAR_OBSERVACION"){
		$codigoSocio =$_POST[codigoSocio];
		$observacion =$_POST[observacion];
		$nombre      =infoSocios($codigoSocio,'nombreCorto');
		
		$sql="DELETE FROM sm_socios_observacion WHERE codigoSocio='$codigoSocio' AND id='$observacion'";
		$rs=mysqli_query($conexion,$sql);

		if($rs){
			$proceso="UNA OBSERVACION FUE ELIMINADA DE SOCIO ".$nombre;
			$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$sql="INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
			$rs=mysqli_query($conexion,$sql);

			$respuesta->mensaje ="OBSERVACION_ELIMINADA";
		}else{
			$respuesta->mensaje ="ERROR_OBSERVACION_ELIMINADA";
		}		
	}

	if($operacion=="ENTREGAR_TARJETA"){
		$codigoSocio =$_POST[codigoSocio];
		$nombre      =infoSocios($codigoSocio,'nombreCorto');
		
		$sql="UPDATE sm_socios SET eCardSocio='SI' WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$proceso="TARJETA DE SOCIO ENTREGADO A ".$nombre;
		$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		$proceso="TARJETA DE SOCIO ENTREGADO";
		$sql="INSERT INTO sm_tarjeta_socio(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);
		
		$respuesta->mensaje ="TARJETA_ENTREGADA";
	}

	if($operacion=="ENTREGAR_DUPLICADO_TARJETA"){
		$codigoSocio =$_POST[codigoSocio];
		$nombre      =infoSocios($codigoSocio,'nombreCorto');
		
		$sql="UPDATE sm_socios SET eCardSocio='SI' WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$proceso="DUPLICADO DE TARJETA DE SOCIO ENTREGADO A ".$nombre;
		$sql="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);

		$proceso="DUPLICADO DE TARJETA DE SOCIO ENTREGADO";
		$sql="INSERT INTO sm_tarjeta_socio(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
		$rs=mysqli_query($conexion,$sql);
		
		$respuesta->mensaje ="TARJETA_ENTREGADA";
	}

	if($operacion=="GENERAR_ARCHIVO_JSON"){
		$conexion=conexionDB();
		$sql="SELECT sm_socios.codigoSocio AS codigoSocio, sm_socios.nombre AS nombre, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_lotes_socio.lotes AS lotes FROM sm_socios, sm_lotes_socio WHERE sm_socios.codigoSocio=sm_lotes_socio.codigoSocio"; 
		$rs=mysqli_query($conexion,$sql);
		$socios=array();
		while($dato=mysqli_fetch_array($rs)){
			$codigoSocio =$dato[codigoSocio];
			$nombre      =utf8_encode($dato[nombre]);
			$apPaterno   =utf8_encode($dato[apPaterno]);
			$apMaterno   =utf8_encode($dato[apMaterno]);
			$lotes       =$dato[lotes];
			$socios[] =array('codigoSocio'=>$codigoSocio, 'nombre'=>$nombre, 'apPaterno'=>$apPaterno, 'apMaterno'=>$apMaterno, 'lotes'=>$lotes);
		}

		cerrarDB();

		$fp = fopen('../generados/socios.json', 'w');
		fwrite($fp, json_encode($socios));
		fclose($fp);
		$respuesta->mensaje ="ARCHIVO_GENERADO";
	}

	if($operacion=="ELIMINA_SOCIO"){
		$sql="DELETE FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_terminal_asistencia WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_socios WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_socios_observacion WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_tarjeta_socio WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_procesos_socios WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_procesos_actividades WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_procesos_cuotas WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$sql="DELETE FROM sm_mod_caja WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);

		$respuesta->mensaje ="SOCIO_ELIMINADO";
	}
	
	if($operacion=="ELIMINAR_FOTO_SOCIO"){
		$fotoSocio='../assets/images/socios/'.$codigoSocio.'.jpg';
		$sql="UPDATE sm_socios SET fotoSocio='' WHERE codigoSocio='$codigoSocio'";
		$rs=mysqli_query($conexion,$sql);
		//if($rs){
		//	unlink($fotoSocio)
		//	$respuesta->mensaje ="FOTO_SOCIO_ELIMINADO";
		//}
		$respuesta->mensaje =$fotoSocio;
	}

	if($operacion=='REGISTRA_ASISTENCIA_ACTIVIDAD' && !empty($_POST['codigoActividad'])){
		$codigoActividad=$_POST['codigoActividad'];
		$dniSocio=$_POST['dniSocio'];

		$sql="SELECT codigoSocio, CONCAT(nombre,' ',apPaterno,' ',apMaterno) AS nombreSocio FROM sm_socios WHERE dni='$dniSocio'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$totalFilas = mysqli_num_rows($row);
		if($totalFilas>0){
			$codigoSocio=$dato['codigoSocio'];
			$nombreSocio=$dato['nombreSocio'];

			$sql="SELECT tipoActividad, temaActividad, horaActividad FROM sm_mod_actividades WHERE codigoActividad = '$codigoActividad'";
			$row=mysqli_query($conexion,$sql);
			$dato=mysqli_fetch_array($row);
			$tipoActividad=$dato['tipoActividad'];
			$temaActividad=$dato['temaActividad'];
			$horaActividad=$dato['horaActividad'];
			$agregaMinutos = 120;
			$dt = new DateTime($horaActividad);
			$dt->modify("+{$agregaMinutos} minutes");
			$horaSalida = $dt->format('H:i:s');

			if($tipoActividad=='ASA'){
				$infoTipoActividad='ASAMBLEA';
			}else{
				$infoTipoActividad='FAENA';
			}

			$sql="SELECT COUNT(id) AS infoAsistencia FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND codigoSocio='$codigoSocio' AND  asistio='SI'";
			$row=mysqli_query($conexion,$sql);
			$dato=mysqli_fetch_array($row);
			$infoAsistencia=$dato['infoAsistencia'];

			$sql="SELECT multa FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND codigoSocio='$codigoSocio' AND asistio='NO'";
			$row=mysqli_query($conexion,$sql);
			$dato=mysqli_fetch_array($row);
			$multa=$dato['multa'];

			if($multa>0){
				$respuesta->resultado='SOCIO_CON_MULTA';
			}else{
				if($infoAsistencia>0){
					$respuesta->resultado='SOCIO_YA_REGISTRADO';
				}else{
					$sql="UPDATE sm_mod_asistencia SET asistio='SI', ingreso='$horaActividad', salida='$horaSalida' WHERE codigoActividad = '$codigoActividad' AND codigoSocio='$codigoSocio'";
					$rs=mysqli_query($conexion,$sql);
					$respuesta->resultado='ASISTENCIA_SOCIO_REGISTRADO';
					if($rs){
						$proceso="SE REGISTRO LA ASISTENCIA A LA ".$infoTipoActividad." - ".$temaActividad." AL SOCIO ".strtoupper($nombreSocio);
						$sql = "INSERT INTO sm_usuarios_operaciones(dni, proceso, fecha, hora, usuario) VALUES('$dniUsuario', '$proceso', '$fecha', '$hora', '$dniUsuario')";
						$record = $conexion->query($sql);
					}
					$respuesta->informacion = $sql;
				}
			}

		}else{
			$respuesta->resultado='SOCIO_NO_EXISTE';
		}
	}

	cerrarDB();
	echo json_encode($respuesta);
?>