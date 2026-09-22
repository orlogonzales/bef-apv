<?php
	require_once('funciones.php');
	date_default_timezone_set("America/Lima");
	ini_set("memory_limit","1024M");
	set_time_limit(-1);
	session_start();
	$conexion        = conexionDB();
	$conexionBEF     = conexionBEF();
	$fecha           = infoTiempo('fecha');
	$hora            = infoTiempo('hora');
	$dniUsuario      = $_SESSION['dni_apv'];
	$sincronizar     = 'SI';
	$datosPersonales = 'SI';

	if(!empty($_SESSION)){
		$sql="SELECT COUNT(codigoSocio) AS sociosRegistrados FROM sm_socios";
		$row = mysqli_query($conexion,$sql);
		$dato = mysqli_fetch_array($row);
		$sociosINI = $dato[sociosRegistrados];

		$sql="TRUNCATE TABLE sm_lotes_socio";
		$rs=mysqli_query($conexion,$sql);

		//VERIFICA TABLAS sm_lotes_socio CON sm_socios -> ELIMINO LOS SOBRANTES;
		$array_dni_socios=array();
		$sql="SELECT dni FROM sm_socios";
		$rs=mysqli_query($conexion,$sql);
		while($n=mysqli_fetch_array($rs)){
			$dni=$n[dni];
			array_push($array_dni_socios,$dni);
		}

		$array_dni_clientes=array();
		$sql="SELECT cliente FROM lotes WHERE adjudicado='SI' AND sectorPrincipal='NO' AND manzanaPrincipal='NO' GROUP BY cliente ORDER BY lotes DESC";
		$rs=mysqli_query($conexionBEF,$sql);
		while($n=mysqli_fetch_array($rs)){
			$cliente=$n[cliente];
			array_push($array_dni_clientes,$cliente);
		}

		$no_socios = array_diff($array_dni_socios, $array_dni_clientes);
		$d=1;

		//ELIMINA RASTROS DE SOCIOS QUE YA NO EXISTEN
		foreach ($no_socios as $key => $value) {
			$query="SELECT codigoSocio FROM sm_socios WHERE dni='$value'";
			$row = mysqli_query($conexion,$query);
			$dato = mysqli_fetch_array($row);
			$codigoSocio = $dato[codigoSocio];

			//ELIMINA RASTROS DE TABLAS
			$query="DELETE FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado01='OK';
			}else{
				$resultado01='ERROR';
			}
			
			$query="DELETE FROM sm_terminal_asistencia WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado02='OK';
			}else{
				$resultado02='ERROR';
			}
			
			$query="DELETE FROM sm_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado03='OK';
			}else{
				$resultado03='ERROR';
			}
			
			$query="DELETE FROM sm_socios_observacion WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado04='OK';
			}else{
				$resultado04='ERROR';
			}
			
			$query="DELETE FROM sm_tarjeta_socio WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado05='OK';
			}else{
				$resultado05='ERROR';
			}
			
			$query="DELETE FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado06='OK';
			}else{
				$resultado06='ERROR';
			}
			
			$query="DELETE FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado07='OK';
			}else{
				$resultado07='ERROR';
			}
			
			$query="DELETE FROM sm_procesos_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado08='OK';
			}else{
				$resultado08='ERROR';
			}
			
			$query="DELETE FROM sm_procesos_actividades WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado09='OK';
			}else{
				$resultado09='ERROR';
			}
			
			$query="DELETE FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado10='OK';
			}else{
				$resultado10='ERROR';
			}
			
			$query="DELETE FROM sm_procesos_cuotas WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado11='OK';
			}else{
				$resultado11='ERROR';
			}
			
			$query="DELETE FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado12='OK';
			}else{
				$resultado12='ERROR';
			}
			
			$query="DELETE FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado13='OK';
			}else{
				$resultado13='ERROR';
			}
			
			$query="DELETE FROM sm_mod_caja WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			if($row){
				$resultado14='OK';
			}else{
				$resultado14='ERROR';
			}

			/*
			echo $d.'<br>';
			echo $value.' - '.$codigoSocio.'<br>';
			echo 'resultado01 -> sm_terminal_socios -> '.$resultado01.'<br>';
			echo 'resultado02 -> sm_terminal_asistencia -> '.$resultado02.'<br>';
			echo 'resultado03 -> sm_socios -> '.$resultado03.'<br>';
			echo 'resultado04 -> sm_socios_observacion -> '.$resultado04.'<br>';
			echo 'resultado05 -> sm_tarjeta_socio -> '.$resultado05.'<br>';
			echo 'resultado06 -> sm_lotes_socio -> '.$resultado06.'<br>';
			echo 'resultado07 -> sm_relacion_socios -> '.$resultado07.'<br>';
			echo 'resultado08 -> sm_procesos_socios -> '.$resultado08.'<br>';
			echo 'resultado09 -> sm_procesos_actividades -> '.$resultado09.'<br>';
			echo 'resultado10 -> sm_mod_asistencia -> '.$resultado10.'<br>';
			echo 'resultado11 -> sm_procesos_cuotas -> '.$resultado11.'<br>';
			echo 'resultado12 -> sm_mod_cuotas_socios -> '.$resultado12.'<br>';
			echo 'resultado13 -> sm_mod_cuentas -> '.$resultado13.'<br>';
			echo 'resultado14 -> sm_mod_caja -> '.$resultado14.'<br>';
			echo '<hr>';
			$d++;
			*/
		}

		if($sincronizar=='SI'){
			$e=1;
			$i=1;
			//$debug="AND sector='1' AND manzana='M-1'";
			$debug='';
			//$sql="SELECT sector, manzana, lote, codigoLote, cliente, relacion, cliente2, COUNT(*) as lotes FROM lotes WHERE adjudicado='SI' AND sectorPrincipal='NO' AND manzanaPrincipal='NO' $debug GROUP BY cliente ORDER BY lotes DESC";

			$sql="SELECT sector, manzana, lote, codigoLote, cliente, relacion, cliente2, COUNT(*) AS lotes, m2 AS metraje FROM lotes WHERE adjudicado = 'SI' AND sectorPrincipal = 'NO' AND manzanaPrincipal = 'NO' GROUP BY cliente ORDER BY sector ASC, CAST(SUBSTRING_INDEX(manzana, '-', -1) AS UNSIGNED) ASC, CAST(SUBSTRING_INDEX(lote, '-', -1) AS UNSIGNED) ASC";
			$rs=mysqli_query($conexionBEF,$sql);
			$contar=mysqli_num_rows($rs);
			mysqli_set_charset($conexion, "utf8");
			while($n=mysqli_fetch_array($rs)){
				$socio           =$n[cliente];
				$coSocio         =$n[cliente2];
				$lotes           =infoSocio($socio,'lotes');
				$cantidad_lotes  =$lotes;
				$dni             =$n[cliente];
				$metraje         =$n[metraje];

				if($cantidad_lotes>0){
					$query="SELECT codigoSocio FROM sm_socios WHERE dni='$dni'";
					$row=mysqli_query($conexion,$query);
					$dato=mysqli_fetch_array($row);
					$codigoSocio=$dato[codigoSocio];
					
					if(strlen($codigoSocio)>0){
						$existe='SI';
						$codigoSocio=$codigoSocio;
					}else{
						$existe='NO';
						$codigoSocio='';
					}
					
					if($existe==SI){
						$codigoSocio=$codigoSocio;
					}else{
						$codigoSocio     ='CSARP'.$socio.$lotes;
					}

					// INFORMACION DE SOCIOS
					if($datosPersonales=='SI'){
						$query           = "SELECT tratamiento, nombre, paterno, materno, genero, nac, nacionalidad, ciudad, civil, direccion, departamento, provincia, distrito, telefono, celular FROM clientes WHERE dni='$dni'";
						$row             = mysqli_query($conexionBEF,$query);
						$dato            = mysqli_fetch_array($row);
						$tratamiento     = $dato[tratamiento];
						$nombre          = $dato[nombre];
						$paterno         = $dato[paterno];
						$materno         = $dato[materno];
						$genero          = $dato[genero];
						$fechaNacimiento = $dato[nac];
						$nacionalidad    = $dato[nacionalidad];
						$ciudad          = $dato[ciudad];
						$estadoCivil     = $dato[civil];
						$direccion       = $dato[direccion];
						$departamento    = $dato[departamento];
						$provincia       = $dato[provincia];
						$distrito        = $dato[distrito];
						$telefono        = $dato[telefono];
						$celular         = $dato[celular];
						$tratamiento     = strtoupper($tratamiento);
						$nombre          = strtoupper($nombre);
						$apPaterno       = strtoupper($paterno);
						$apMaterno       = strtoupper($materno);
						$direccion       = strtoupper($direccion);
						$distrito        = strtoupper($distrito);
						$fotoSocio       = '';

						$query="SELECT fechaAdjudica FROM adjudicalote WHERE cliente='$dni' LIMIT 1";
						$row           = mysqli_query($conexionBEF,$query);
						$dato          = mysqli_fetch_array($row);
						$fechaAdjudica = $dato[fechaAdjudica];

						$query="SELECT recibo FROM adjudicalote WHERE cliente='$dni' LIMIT 1";
						$row    = mysqli_query($conexionBEF,$query);
						$dato   = mysqli_fetch_array($row);
						$recibo = $dato[recibo];
						
						$observaciones   ='';
						$sincronizado    ='OK';
						$eCardSocio      ='NO';
						$relacion        =$n[relacion];

						if(strlen($coSocio==0)){
							$dniCS             =$coSocio;
							$query             = "SELECT tratamiento, nombre, paterno, materno, genero, nac, nacionalidad, ciudad, civil, direccion, departamento, provincia, distrito, telefono, celular FROM clientes WHERE dni='$dniCS'";
							$row               = mysqli_query($conexionBEF,$query);
							$dato              = mysqli_fetch_array($row);
							$tratamientoCS     = $dato[tratamiento];
							$nombreCS          = $dato[nombre];
							$apPaternoCS       = $dato[paterno];
							$apMaternoCS       = $dato[materno];
							$generoCS          = $dato[genero];
							$fechaNacimientoCS = $dato[nac];
							$nacionalidadCS    = $dato[nacionalidad];
							$ciudad            = $dato[ciudad];
							$estadoCivilCS     = $dato[civil];
							$direccionCS       = $dato[direccion];
							$departamentoCS    = $dato[departamento];
							$provinciaCS       = $dato[provincia];
							$distritoCS        = $dato[distrito];
							$telefonoCS        = $dato[telefono];
							$celularCS         = $dato[celular];
							$tratamientoCS     = strtoupper($tratamientoCS);
							$nombreCS          = strtoupper($nombreCS);
							$apPaternoCS       = strtoupper($apPaternoCS);
							$apMaternoCS       = strtoupper($apMaternoCS);
							$direccionCS       = strtoupper($direccionCS);
							$distritoCS        = strtoupper($distritoCS);
						}else{
							$dniCS            ='';
							$tratamientoCS    ='';
							$nombreCS         ='';
							$apPaternoCS      ='';
							$apMaternoCS      ='';
							$generoCS         ='';
							$fechaNacimientoCS='';
							$nacionalidadCS   ='';
							$estadoCivilCS    ='';
							$direccionCS      ='';
							$departamentoCS   ='';
							$provinciaCS      ='';
							$distritoCS       ='';
							$telefonoCS       ='';
							$celularCS        ='';
						}
					}
					
					// INFORMACION DE CO-SOCIOS
					if($datosPersonales=='SI'){
						if(strlen($coSocio==0)){
							$query="DELETE FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
							$socios=mysqli_query($conexion,$query);
						}else{
							$dniCS             =$coSocio;
							$query             = "SELECT tratamiento, nombre, paterno, materno, genero, nac, nacionalidad, ciudad, civil, direccion, departamento, provincia, distrito, telefono, celular FROM clientes WHERE dni='$dniCS'";
							$row               = mysqli_query($conexionBEF,$query);
							$dato              = mysqli_fetch_array($row);
							$tratamientoCS     = $dato[tratamiento];
							$nombreCS          = $dato[nombre];
							$apPaternoCS       = $dato[paterno];
							$apMaternoCS       = $dato[materno];
							$generoCS          = $dato[genero];
							$fechaNacimientoCS = $dato[nac];
							$nacionalidadCS    = $dato[nacionalidad];
							$ciudad            = $dato[ciudad];
							$estadoCivilCS     = $dato[civil];
							$direccionCS       = $dato[direccion];
							$departamentoCS    = $dato[departamento];
							$provinciaCS       = $dato[provincia];
							$distritoCS        = $dato[distrito];
							$telefonoCS        = $dato[telefono];
							$celularCS         = $dato[celular];
							$tratamientoCS     = strtoupper($tratamientoCS);
							$nombreCS          = strtoupper($nombreCS);
							$apPaternoCS       = strtoupper($apPaternoCS);
							$apMaternoCS       = strtoupper($apMaternoCS);
							$direccionCS       = strtoupper($direccionCS);
							$distritoCS        = strtoupper($distritoCS);

							$query="INSERT INTO sm_relacion_socios(codigoSocio, relacion, dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular) VALUES('$codigoSocio', '$relacion', '$dniCS', '$tratamientoCS', '$nombreCS', '$apPaternoCS', '$apMaternoCS', '$generoCS', '$fechaNacimientoCS', '$nacionalidadCS', '$estadoCivilCS', '$direccionCS', '$departamentoCS', '$provinciaCS', '$distritoCS', '$telefonoCS', '$celularCS')";
							$cosocio=mysqli_query($conexion,$query);
						}
					}

					$registrado=socioRegistrado($codigoSocio);
					$DNIRegistrado=infoSocios($dni,'verificaDNI');
					$codigoSocioRegistrado=infoSocios($DNIRegistrado,'codigoSocio');

					if($DNIRegistrado==$dni){
						$query="UPDATE sm_socios SET tratamiento='$tratamiento', nombre='$nombre', apPaterno='$apPaterno', apMaterno='$apMaterno', genero='$genero', fechaNacimiento='$fechaNacimiento', nacionalidad='$nacionalidad', estadoCivil='$estadoCivil', direccion='$direccion', departamento='$departamento', provincia='$provincia', distrito='$distrito', telefono='$telefono', celular='$celular', observaciones='$observaciones' WHERE codigoSocio='$codigoSocioRegistrado'";
						$socios=mysqli_query($conexion,$query);
					}else{
						$query="INSERT INTO sm_socios(codigoSocio, dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, fotoSocio, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular, fechaAdjudica, recibo, observaciones, sincronizado, eCardSocio, fecha, hora) VALUES('$codigoSocio', '$dni', '$tratamiento', '$nombre', '$apPaterno', '$apMaterno', '$genero', '$fechaNacimiento', '$fotoSocio', '$nacionalidad', '$estadoCivil', '$direccion', '$departamento', '$provincia', '$distrito', '$telefono', '$celular', '$fechaAdjudica', '$recibo', '$observaciones', '$sincronizado', '$eCardSocio', '$fecha', '$hora')";
						$socios=mysqli_query($conexion,$query);

						if($relacion!=""){
							$query="INSERT INTO sm_relacion_socios(codigoSocio, relacion, dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular) VALUES('$codigoSocio', '$relacion', '$dniCS', '$tratamientoCS', '$nombreCS', '$apPaternoCS', '$apMaternoCS', '$generoCS', '$fechaNacimientoCS', '$nacionalidadCS', '$estadoCivilCS', '$direccionCS', '$departamentoCS', '$provinciaCS', '$distritoCS', '$telefonoCS', '$celularCS')";
							$cosocio=mysqli_query($conexion,$query);
						}

						$proceso="SOCIO TRANSFERIDO AL SISTEMA DE SOCIOS";
						$query="INSERT INTO sm_procesos_socios(codigoSocio, proceso, fecha, hora, usuario) VALUES('$codigoSocio', '$proceso', '$fecha', '$hora','$dniUsuario')";
						$procesos=mysqli_query($conexion,$query);
					}

					$consultaLotes="SELECT sector, manzana, lote, codigoLote  FROM lotes WHERE cliente='$socio' AND adjudicado='SI' AND cliente IN(SELECT dni FROM cuentacorriente WHERE dni='$socio')";
					$queryConsultaLotes=mysqli_query($conexionBEF,$consultaLotes);
					mysqli_set_charset($conexion, "utf8");
					$x=1;

					while($info=mysqli_fetch_array($queryConsultaLotes)){
						$sector     =$info[sector];
						$manzana    =$info[manzana];
						$lote       =$info[lote];
						$codigoLote =$info[codigoLote];
						$direccion  ='';
						
						$query="INSERT INTO sm_lotes_socio(codigoSocio, lotes, sector, manzana, lote, metraje, codigoLote, direccion) VALUES('$codigoSocio', '$lotes', '$sector', '$manzana', '$lote', '$metraje', '$codigoLote', '$direccion')";
						$lotesSocio=mysqli_query($conexion,$query);
						$x++;
					}

					$i++;
				}else{
					$query="DELETE FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_terminal_asistencia WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_socios WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_socios_observacion WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_tarjeta_socio WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_procesos_socios WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_procesos_actividades WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_procesos_cuotas WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$query="DELETE FROM sm_mod_caja WHERE codigoSocio='$codigoSocio'";
					$row=mysqli_query($conexion,$query);
					$e++;
				}
			}

			$sql="SELECT COUNT(codigoSocio) AS sociosRegistrados FROM sm_socios";
			$row = mysqli_query($conexion,$sql);
			$dato = mysqli_fetch_array($row);
			$sociosFIN = $dato[sociosRegistrados];

			$totalSocios = $sociosFIN-$sociosINI;
			$proceso = "SISTEMA SICRONIZADO CON MATRIZ";
			$sql = "INSERT INTO sm_socios_sync(proceso, socios, fecha, hora, usuario) VALUES('$proceso', '$totalSocios','$fecha', '$hora','$dniUsuario')";
			$procesos = mysqli_query($conexion,$sql);
			$respuesta->sociosINI = $sociosINI;
			$respuesta->sociosFIN = $sociosFIN;
			$respuesta->totalSocios = $totalSocios;
			$respuesta->mensaje = "FINZALIZADO";
			cerrarDB();
		}
	}else{
		$respuesta->mensaje = "SESION_CERRADA";
	}
	echo json_encode($respuesta);