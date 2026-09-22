<?php
	require_once('funciones.php');
	date_default_timezone_set("America/Lima");
	ini_set("memory_limit","1024M");
	set_time_limit(-1);
	session_start();
	$conexion     = conexionDB();
	$dniUsuario   = $_SESSION['dni_apv'];
	$fecha        = infoTiempo('fecha');
	$hora         = infoTiempo('hora');

	if(!empty($_SESSION)){
		$tipoActividad= 'ASA';
		$tipoACT      ="ASAMBLEA";
		$sql="SELECT COUNT(codigoActividad) AS total FROM sm_mod_actividades WHERE tipoActividad='$tipoActividad'";
		$row = mysqli_query($conexion,$sql);
		$dato = mysqli_fetch_array($row);
		$resultados = $dato[total];

		// OBTENER DATOS DE ASAMBLEAS
		//echo '<hr>'.$tipoACT.' -> '.$resultados.'<hr>';
		if($resultados>0){
			$act=1;
			$lisSocios=1;
			$totales=1;
			$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.codigoActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta FROM sm_mod_actividades WHERE tipoActividad = '$tipoActividad'";
			$rs=mysqli_query($conexion,$sql);
			$actividades=mysqli_num_rows($rs);
			while($n=mysqli_fetch_array($rs)){
				$temaActividad   = $n[temaActividad];
				$codigoActividad = $n[codigoActividad];
				$mTardanza       = $n[mTardanza];
				$mFalta          = $n[mFalta];

				if($act<=$actividades){
					//echo '<hr>'.$act.' -> codigoActividad -> '.$codigoActividad.'<hr>';
					// LISTAR SOCIOS PARA VERIFICAR SI ASISTIERON O NO
					$query1="SELECT codigoSocio FROM sm_socios";
					$socios=mysqli_query($conexion,$query1);
					$totalSocios=mysqli_num_rows($socios);
					while($s=mysqli_fetch_array($socios)){
						$codigoSocio=$s[codigoSocio];
						if($lisSocios>$totalSocios){
							$lisSocios=1;
						}else{
							$query2="SELECT id, lotes, asistio, terminal, ingreso, salida, retraso, multa FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio='$codigoSocio'";
							$row = mysqli_query($conexion,$query2);
							$dato = mysqli_fetch_array($row);
							$id       = $dato[id];
							$lotes    = $dato[lotes];
							$asistio  = $dato[asistio];
							$terminal = $dato[terminal];
							$ingreso  = $dato[ingreso];
							$salida   = $dato[salida];
							$retraso  = $dato[retraso];
							$multa    = $dato[multa];

							if($id>0){
								//echo '<span style="color:green;">'.$lisSocios.' DE '.$totales.' -> SOCIO ASISTIO -> '.$codigoSocio.' -> '.$totalSocios.'</span><br>';
								$multa =$multa*$lotes;
								if($asistio=='SI' && $ingreso!='00:00:00' && $salida=='00:00:00'){
									$query3="UPDATE sm_mod_asistencia SET asistio='NO', multa='$multa' WHERE codigoSocio='$codigoSocio'";
									$registra=mysqli_query($conexion,$query3);
									//echo '<span style="color:blue;">REGISTRO MULTA</span><br>';
								}
							}else{
								//echo '<span style="color:red;">'.$lisSocios.' -> NO REGISTRADO -> '.$codigoSocio.' -> '.$totalSocios.'</span><br>';
								$asistio         ="IN";
								$ingreso         ="";
								$salida          ="";
								$retraso         ="";
								$multa           =$multa*$lotes;
								$estadoPago      ="";
								$observacion     ="";
								$documento       ="";
								$query3="INSERT sm_mod_asistencia(tipoActividad, codigoActividad, codigoSocio, lotes, asistio, ingreso, salida, retraso, multa, estadoPago, observacion, documento) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$lotes', '$asistio', '$ingreso', '$salida', '$retraso', '$multa', '$estadoPago', '$observacion', '$documento')";
								$registra=mysqli_query($conexion,$query3);
								if($registra){
									//echo '<span style="color:blue;">REGISTRADO</span><br>';
									$proceso="AGREGADO A <strong>".$tipoACT." ".$temaActividad."</strong>";
									$query4="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
									$registra=mysqli_query($conexion,$query4);
								}
							}
							$totales++;
						}
						$lisSocios++;
					}
				}
				$act++;
			}
		}

		$tipoActividad= 'FAE';
		$tipoACT      ="FAENA";
		$sql="SELECT COUNT(codigoActividad) AS total FROM sm_mod_actividades WHERE tipoActividad='$tipoActividad'";
		$row = mysqli_query($conexion,$sql);
		$dato = mysqli_fetch_array($row);
		$resultados = $dato[total];

		// OBTENER DATOS DE FAENAS
		//echo '<hr>'.$tipoACT.' -> '.$resultados.'<hr>';
		if($resultados>0){
			$act=1;
			$lisSocios=1;
			$totales=1;
			$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.codigoActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta FROM sm_mod_actividades WHERE tipoActividad = '$tipoActividad'";
			$rs=mysqli_query($conexion,$sql);
			$actividades=mysqli_num_rows($rs);
			while($n=mysqli_fetch_array($rs)){
				$temaActividad   = $n[temaActividad];
				$codigoActividad = $n[codigoActividad];
				$mTardanza       = $n[mTardanza];
				$mFalta          = $n[mFalta];

				if($act<=$actividades){
					//echo '<hr>'.$act.' -> codigoActividad -> '.$codigoActividad.'<hr>';
					// LISTAR SOCIOS PARA VERIFICAR SI ASISTIERON O NO
					$query1="SELECT codigoSocio FROM sm_socios";
					$socios=mysqli_query($conexion,$query1);
					$totalSocios=mysqli_num_rows($socios);
					while($s=mysqli_fetch_array($socios)){
						$codigoSocio=$s[codigoSocio];
						if($lisSocios>$totalSocios){
							$lisSocios=1;
						}else{
							$query2="SELECT id, lotes, asistio, terminal, ingreso, salida, retraso, multa FROM sm_mod_asistencia WHERE codigoActividad = '$codigoActividad' AND codigoSocio='$codigoSocio'";
							$row = mysqli_query($conexion,$query2);
							$dato = mysqli_fetch_array($row);
							$id       = $dato[id];
							$lotes    = $dato[lotes];
							$asistio  = $dato[asistio];
							$terminal = $dato[terminal];
							$ingreso  = $dato[ingreso];
							$salida   = $dato[salida];
							$retraso  = $dato[retraso];
							$multa    = $dato[multa];

							if($id>0){
								//echo '<span style="color:green;">'.$lisSocios.' DE '.$totales.' -> SOCIO ASISTIO -> '.$codigoSocio.' -> '.$totalSocios.'</span><br>';
								$multa =$multa*$lotes;
								if($asistio=='SI' && $ingreso!='00:00:00' && $salida=='00:00:00'){
									$sql="UPDATE sm_mod_asistencia SET asistio='NO', multa='$multa' WHERE codigoSocio='$codigoSocio'";
									$registra=mysqli_query($conexion,$sql);
									//echo '<span style="color:blue;">REGISTRO MULTA</span><br>';
								}
							}else{
								//echo '<span style="color:red;">'.$lisSocios.' -> NO REGISTRADO -> '.$codigoSocio.' -> '.$totalSocios.'</span><br>';
								$asistio         ="IN";
								$ingreso         ="";
								$salida          ="";
								$retraso         ="";
								$multa           =$multa*$lotes;
								$estadoPago      ="";
								$observacion     ="";
								$documento       ="";
								$query3="INSERT sm_mod_asistencia(tipoActividad, codigoActividad, codigoSocio, lotes, asistio, ingreso, salida, retraso, multa, estadoPago, observacion, documento) VALUES('$tipoActividad', '$codigoActividad', '$codigoSocio', '$lotes', '$asistio', '$ingreso', '$salida', '$retraso', '$multa', '$estadoPago', '$observacion', '$documento')";
								$registra=mysqli_query($conexion,$query3);
								if($registra){
									//echo '<span style="color:blue;">REGISTRADO</span><br>';
									$proceso="AGREGADO A <strong>".$tipoACT." ".$temaActividad."</strong>";
									$query4="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
									$registra=mysqli_query($conexion,$query4);
								}
							}
							$totales++;
						}
						$lisSocios++;
					}
				}
				$act++;
			}
		}

		$respuesta->mensaje = "FINZALIZADO_SYNC_SOCIOS";
	}else{
		$respuesta->mensaje = "SESION_CERRADA";
	}

	echo json_encode($respuesta);
