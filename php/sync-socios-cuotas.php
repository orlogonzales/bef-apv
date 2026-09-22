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

	$sql="SELECT COUNT(codigoCuota) AS total FROM sm_mod_cuotas";
	$row = mysqli_query($conexion,$sql);
	$dato = mysqli_fetch_array($row);
	$resultados = $dato[total];

	// OBTENER DATOS DE CUOTAS
	//echo '<hr>'.$resultados.'<hr>';

	if(!empty($_SESSION)){
		if($resultados>0){
			$cuo=1;
			$lisSocios=1;
			$totales=1;
			$sql="SELECT sm_mod_cuotas.codigoCuota, sm_mod_cuotas.conceptoCuota, sm_mod_cuotas.montoCuota, sm_mod_cuotas.codigoCuenta, sm_mod_cuotas.fechaPago, sm_mod_cuotas.observacion FROM sm_mod_cuotas";
			$rs=mysqli_query($conexion,$sql);
			$nroCuotas=mysqli_num_rows($rs);
			while($n=mysqli_fetch_array($rs)){
				$codigoCuota   = $n[codigoCuota];
				$conceptoCuota = $n[conceptoCuota];
				$montoCuota    = $n[montoCuota];
				$codigoCuenta  = $n[codigoCuenta];
				$fechaPago     = $n[fechaPago];
				$observacion   = $n[observacion];

				if($cuo<=$nroCuotas){
					//echo '<hr>'.$cuo.' -> codigoCuota -> '.$codigoCuota.'<hr>';
					$query1="SELECT codigoSocio FROM sm_socios";
					$socios=mysqli_query($conexion,$query1);
					$totalSocios=mysqli_num_rows($socios);
					while($s=mysqli_fetch_array($socios)){
						$codigoSocio=$s[codigoSocio];

						if($lisSocios>$totalSocios){
							$lisSocios=1;
						}else{
							$query2="SELECT id, lotes, montoCuota, montoPago, estadoPago, caja, montoPagado, porcentajePago FROM sm_mod_cuotas_socios WHERE codigoCuota = '$codigoCuota' AND codigoSocio = '$codigoSocio'";
							$row = mysqli_query($conexion,$query2);
							$dato = mysqli_fetch_array($row);
							$id             = $dato[id];
							$lotes          = $dato[lotes];
							//$montoCuota     = $dato[montoCuota];
							$montoPago      = $dato[montoPago];
							$estadoPago     = $dato[estadoPago];
							$caja           = $dato[caja];
							$montoPagado    = $dato[montoPagado];
							//$porcentajePago = $dato[porcentajePago];

							$query5="SELECT COUNT(id) as total FROM sm_lotes_socio WHERE codigoSocio = '$codigoSocio'";
							$row = mysqli_query($conexion,$query5);
							$dato = mysqli_fetch_array($row);
							$totalLotes = $dato[total];

							if($id>0){
								//echo '<span style="color:green;">'.$totales.' -> '.$lisSocios.' DE '.$totalSocios.' -> REGISTRADO EN CUOTA -> '.$codigoSocio.'</span><br>';
								// VERIFICA SI SOCIO ADQUIRIO MAS LOTES Y SI TIENE PAGADO LA CUOTA AUMENTAR CUOTA POR NUMERO DE LOTES Y PONER EN PORCENTAJE SEGUN EL MONTO PAGADO Y ACTUALIZAR
								//$lotesINI=$montoPago/$montoCuota;
								//echo 'lotesINI -> '.$lotes.' -> montoPago ->'.$montoPago.' / montoCuota -> '.$montoCuota.' -> lotesFIN -> '.$totalLotes.'<br>';
								if($lotes==0){
									$montoPago=$montoCuota*$totalLotes;
									$estadoPago='NP';
									$caja='';
									$montoPagado='';
									$porcentajePago='';
									$query6="UPDATE sm_mod_cuotas_socios SET lotes='$totalLotes', montoCuota='$montoCuota', montoPago='$montoPago', estadoPago='$estadoPago', caja='$caja', montoPagado='$montoPagado', porcentajePago='$porcentajePago' WHERE codigoCuota = '$codigoCuota' AND codigoSocio = '$codigoSocio'";
									$registra=mysqli_query($conexion,$query6);
								}else{
									if($lotes!=$totalLotes){
										if($estadoPago=='SP'){
											$montoPago=$montoCuota*$totalLotes;
											$porcentajePago=($montoPago*100)/($montoCuota*$totalLotes);
										}else{
											$montoPago=$montoCuota*$totalLotes;
										}
										$query6="UPDATE sm_mod_cuotas_socios SET lotes='$totalLotes', montoCuota='$montoCuota', montoPago='$montoPago', estadoPago='$estadoPago', caja='$caja', montoPagado='$montoPagado', porcentajePago='$porcentajePago' WHERE codigoCuota = '$codigoCuota' AND codigoSocio = '$codigoSocio'";
										$registra=mysqli_query($conexion,$query6);
										if($registra){
											//echo '<span style="color:blue;">ACTUALIZADO</span><br>';
											$proceso="CUOTA ACTUALIZADA <strong>".$conceptoCuota."</strong> POR VARIACION DE LOTES";
											$query4="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
											$registra=mysqli_query($conexion,$query4);
										}
									}
								}
							}else{
								$montoPago=$montoCuota*$totalLotes;
								$estadoPago='NP';
								$caja='';
								$montoPagado='';
								$porcentajePago='';
								//echo '<span style="color:red;">'.$totales.' -> '.$lisSocios.' DE '.$totalSocios.' -> NO REGISTRADO EN CUOTA -> '.$codigoSocio.'</span><br>';
								$query3="INSERT INTO sm_mod_cuotas_socios(codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago, caja, montoPagado, porcentajePago) VALUES('$codigoCuota', '$codigoSocio', '$lotes', '$montoCuota', '$montoPago', '$estadoPago', '$caja', '$montoPagado', '$porcentajePago')";
								$registra=mysqli_query($conexion,$query3);
								if($registra){
									//echo '<span style="color:blue;">REGISTRADO</span><br>';
									$proceso="CUOTA AGREGADA <strong>".$conceptoCuota."</strong>";
									$query4="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
									$registra=mysqli_query($conexion,$query4);
								}
							}
							$totales++;
						}
						$lisSocios++;
					}
				}
				$cuo++;
			}
		}

		$respuesta->mensaje = "FINZALIZADO_SYNC_SOCIOS";
	}else{
		$respuesta->mensaje = "SESION_CERRADA";
	}
	
	echo json_encode($respuesta);