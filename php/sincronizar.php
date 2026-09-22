<table border="1" cellspacing="0" cellpadding="5" style="font-family: arial; font-size:10px;">
 <tr>
	<td style="background-color:#CCA;"><strong>#</strong></td>
	<td style="background-color:#CCA;"><strong>CodSocio</strong></td>
	<td style="background-color:#CCA;"><strong>DNI</strong></td>
	<td style="background-color:#CCA;"><strong>Tra</strong>.</td>
	<td style="background-color:#CCA;"><strong>Nombre</strong></td>
	<td style="background-color:#CCA;"><strong>Paterno</strong></td>
	<td style="background-color:#CCA;"><strong>Materno</strong></td>
	<td style="background-color:#CCA;"><strong>Gen</strong></td>
	<td style="background-color:#CCA;"><strong>Nac</strong>.</td>
	<td style="background-color:#CCA;"><strong>Foto</strong></td>
	<td style="background-color:#CCA;"><strong>Nacio</strong></td>
	<td style="background-color:#CCA;"><strong>ECivil</strong></td>
	<td style="background-color:#CCA;"><strong>Direccion</strong></td>
	<td style="background-color:#CCA;"><strong>Dep</strong></td>
	<td style="background-color:#CCA;"><strong>Provincia</strong></td>
	<td style="background-color:#CCA;"><strong>Distrito</strong></td>
	<td style="background-color:#CCA;"><strong>Telefono</strong></td>
	<td style="background-color:#CCA;"><strong>Celular</strong></td>

	<td style="background-color:#CCA;"><strong>Rel</strong></td>
	<td style="background-color:#CCA;"><strong>dni</strong></td>
	<td style="background-color:#CCA;"><strong>Tra</strong></td>
	<td style="background-color:#CCA;"><strong>nombre</strong></td>
	<td style="background-color:#CCA;"><strong>apPaterno</strong></td>
	<td style="background-color:#CCA;"><strong>apMaterno</strong></td>
	<td style="background-color:#CCA;"><strong>Gen</strong></td>
	<td style="background-color:#CCA;"><strong>Nac</strong></td>
	<td style="background-color:#CCA;"><strong>Nacio</strong></td>
	<td style="background-color:#CCA;"><strong>ECivil</strong></td>
	<td style="background-color:#CCA;"><strong>direccion</strong></td>
	<td style="background-color:#CCA;"><strong>Dep</strong></td>
	<td style="background-color:#CCA;"><strong>provincia</strong></td>
	<td style="background-color:#CCA;"><strong>distrito</strong></td>
	<td style="background-color:#CCA;"><strong>telefono</strong></td>
	<td style="background-color:#CCA;"><strong>celular</strong></td>

	<td style="background-color:#CCA;"><strong>LTS</strong></td>

	<td style="background-color:#CCA;"><strong>C_l01</strong></td>
	<td style="background-color:#CCA;"><strong>S_l01</strong></td>
	<td style="background-color:#CCA;"><strong>M_l01</strong></td>
	<td style="background-color:#CCA;"><strong>L_l01</strong></td>
	<td style="background-color:#CCA;"><strong>D_l01</strong></td>

	<td style="background-color:#CCA;"><strong>C_l02</strong></td>
	<td style="background-color:#CCA;"><strong>S_l02</strong></td>
	<td style="background-color:#CCA;"><strong>M_l02</strong></td>
	<td style="background-color:#CCA;"><strong>L_l02</strong></td>
	<td style="background-color:#CCA;"><strong>D_l02</strong></td>

	<td style="background-color:#CCA;"><strong>C_l03</strong></td>
	<td style="background-color:#CCA;"><strong>S_l03</strong></td>
	<td style="background-color:#CCA;"><strong>M_l03</strong></td>
	<td style="background-color:#CCA;"><strong>L_l03</strong></td>
	<td style="background-color:#CCA;"><strong>D_l03</strong></td>

	<td style="background-color:#CCA;"><strong>C_l04</strong></td>
	<td style="background-color:#CCA;"><strong>S_l04</strong></td>
	<td style="background-color:#CCA;"><strong>M_l04</strong></td>
	<td style="background-color:#CCA;"><strong>L_l04</strong></td>
	<td style="background-color:#CCA;"><strong>D_l04</strong></td>

	<td style="background-color:#CCA;"><strong>C_l05</strong></td>
	<td style="background-color:#CCA;"><strong>S_l05</strong></td>
	<td style="background-color:#CCA;"><strong>M_l05</strong></td>
	<td style="background-color:#CCA;"><strong>L_l05</strong></td>
	<td style="background-color:#CCA;"><strong>D_l05</strong></td>

	<td style="background-color:#CCA;"><strong>C_l06</strong></td>
	<td style="background-color:#CCA;"><strong>S_l06</strong></td>
	<td style="background-color:#CCA;"><strong>M_l06</strong></td>
	<td style="background-color:#CCA;"><strong>L_l06</strong></td>
	<td style="background-color:#CCA;"><strong>D_l06</strong></td>

	<td style="background-color:#CCA;"><strong>Observa</strong></td>
	<td style="background-color:#CCA;"><strong>Sincro</strong></td>
	<td style="background-color:#CCA;"><strong>Fecha</strong></td>
	<td style="background-color:#CCA;"><strong>Hora</strong></td>
 </tr>


<?php
	require_once('funciones.php');
	date_default_timezone_set("America/Lima");
	ini_set("memory_limit","512M");
	set_time_limit(900);
	session_start();
	$conexion    =conexionDB();
	$conexionBEF =conexionBEF();
	$fecha       =infoTiempo('fecha');
	$hora        =infoTiempo('hora');
	$dniUsuario  =$_SESSION['dni_apv'];

	echo '<hr>'.$hora.'<hr>';

	////////////////////////////////////////////////////////////////////
	/// XXXXX
	////////////////////////////////////////////////////////////////////

	$i=1;
	//$demo="AND CLIENTE='23832206'";
	//$sql="SELECT sector, manzana, lote, codigoLote, cliente, relacion, cliente2 FROM lotes WHERE adjudicado='SI' AND sectorPrincipal='NO' AND manzanaPrincipal='NO' AND estadoPagado='SI' $demo ORDER BY sector ASC";
	$sql="SELECT sector, manzana, lote, codigoLote, cliente, relacion, cliente2, COUNT(*) as lotes FROM lotes WHERE adjudicado='SI' AND sectorPrincipal='NO' AND manzanaPrincipal='NO' AND estadoPagado='SI' $demo GROUP BY cliente ORDER BY lotes DESC";
	$rs=mysqli_query($conexionBEF,$sql);
	$contar=mysqli_num_rows($rs);
	while($n=mysqli_fetch_array($rs)){
		$socio           =$n[cliente];
		$lotes           =infoSocio($socio,'lotes');
		$codigoSocio     ='APVRP'.$socio.$lotes;
		$dni             =$socio;
		$tratamiento     =strtoupper(infoSocio($socio,'tratamiento'));
		$nombre          =strtoupper(infoSocio($socio,'nombre'));
		$apPaterno       =strtoupper(infoSocio($socio,'paterno'));
		$apMaterno       =strtoupper(infoSocio($socio,'materno'));
		$genero          =infoSocio($socio,'genero');
		$fechaNacimiento =infoSocio($socio,'nacimiento');
		$fotoSocio       ='';
		$nacionalidad    =infoSocio($socio,'nacionalidad');
		$estadoCivil     =infoSocio($socio,'civil');
		$direccion       =strtoupper(infoSocio($socio,'direccion'));
		$departamento    =infoSocio($socio,'departamento');
		$provincia       =infoSocio($socio,'provincia');
		$distrito        =strtoupper(infoSocio($socio,'distrito'));
		$telefono        =infoSocio($socio,'telefono');
		$celular         =infoSocio($socio,'celular');
		$fechaAdjudica   =infoSocio($socio,'fechaAdjudica');
		$recibo          =infoSocio($socio,'recibo');
		$observaciones   ='';
		$sincronizado    ='OK';
		$eCardSocio      ='NO';
		$relacion        =$n[relacion];
		if($relacion!=""){
			$coSocio         =$n[cliente2];
			$dniCS            =$coSocio;
			$tratamientoCS    =strtoupper(infoSocio($coSocio,'tratamiento'));
			$nombreCS         =strtoupper(infoSocio($coSocio,'nombre'));
			$apPaternoCS      =strtoupper(infoSocio($coSocio,'paterno'));
			$apMaternoCS      =strtoupper(infoSocio($coSocio,'materno'));
			$generoCS         =infoSocio($coSocio,'genero');
			$fechaNacimientoCS=infoSocio($coSocio,'nacimiento');
			$nacionalidadCS   =infoSocio($coSocio,'nacionalidad');
			$estadoCivilCS    =infoSocio($coSocio,'civil');
			$direccionCS      =strtoupper(infoSocio($coSocio,'direccion'));
			$departamentoCS   =infoSocio($coSocio,'departamento');
			$provinciaCS      =infoSocio($coSocio,'provincia');
			$distritoCS       =strtoupper(infoSocio($coSocio,'distrito'));
			$telefonoCS       =infoSocio($coSocio,'telefono');
			$celularCS        =infoSocio($coSocio,'celular');
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

		$C_l01=infoLotes($dni,'codigoLote','1');
		$S_l01=infoLotes($dni,'sector','1');
		$M_l01=infoLotes($dni,'manzana','1');
		$L_l01=infoLotes($dni,'lote','1');
		$D_l01='';

		$C_l02=infoLotes($dni,'codigoLote','2');
		$S_l02=infoLotes($dni,'sector','2');
		$M_l02=infoLotes($dni,'manzana','2');
		$L_l02=infoLotes($dni,'lote','2');
		$D_l02='';

		$C_l03=infoLotes($dni,'codigoLote','3');
		$S_l03=infoLotes($dni,'sector','3');
		$M_l03=infoLotes($dni,'manzana','3');
		$L_l03=infoLotes($dni,'lote','3');
		$D_l03='';

		$C_l04=infoLotes($dni,'codigoLote','4');
		$S_l04=infoLotes($dni,'sector','4');
		$M_l04=infoLotes($dni,'manzana','4');
		$L_l04=infoLotes($dni,'lote','4');
		$D_l04='';

		$C_l05=infoLotes($dni,'codigoLote','5');
		$S_l05=infoLotes($dni,'sector','5');
		$M_l05=infoLotes($dni,'manzana','5');
		$L_l05=infoLotes($dni,'lote','5');
		$D_l05='';

		$C_l06=infoLotes($dni,'codigoLote','6');
		$S_l06=infoLotes($dni,'sector','6');
		$M_l06=infoLotes($dni,'manzana','6');
		$L_l06=infoLotes($dni,'lote','6');
		$D_l06='';

		$C_l07=infoLotes($dni,'codigoLote','7');
		$S_l07=infoLotes($dni,'sector','7');
		$M_l07=infoLotes($dni,'manzana','7');
		$L_l07=infoLotes($dni,'lote','7');
		$D_l07='';

		$C_l08=infoLotes($dni,'codigoLote','8');
		$S_l08=infoLotes($dni,'sector','8');
		$M_l08=infoLotes($dni,'manzana','8');
		$L_l08=infoLotes($dni,'lote','8');
		$D_l08='';

		$C_l09=infoLotes($dni,'codigoLote','9');
		$S_l09=infoLotes($dni,'sector','9');
		$M_l09=infoLotes($dni,'manzana','9');
		$L_l09=infoLotes($dni,'lote','9');
		$D_l09='';

		$C_l10=infoLotes($dni,'codigoLote','10');
		$S_l10=infoLotes($dni,'sector','10');
		$M_l10=infoLotes($dni,'manzana','10');
		$L_l10=infoLotes($dni,'lote','10');
		$D_l10='';

		$registrado=socioRegistrado($codigoSocio);
?>
	<tr>
		<td><?php echo $i ?></td>
		<td><?php echo $codigoSocio ?></td>
		<td><?php echo $dni ?></td>
		<td><?php echo $tratamiento ?></td>
		<td><?php echo $nombre ?></td>
		<td><?php echo $apPaterno ?></td>
		<td><?php echo $apMaterno ?></td>
		<td><?php echo $genero ?></td>
		<td><?php echo $fechaNacimiento ?></td>
		<td><?php echo $fotoSocio ?></td>
		<td><?php echo $nacionalidad ?></td>
		<td><?php echo $estadoCivil ?></td>
		<td><?php echo $direccion ?></td>
		<td><?php echo $departamento ?></td>
		<td><?php echo $provincia ?></td>
		<td><?php echo $distrito ?></td>
		<td><?php echo $telefono ?></td>
		<td><?php echo $celular ?></td>
		<td style="background-color:#CCA;"><?php echo $relacion ?></td>
		<td><?php echo $dniCS ?></td>
		<td><?php echo $tratamientoCS ?></td>
		<td><?php echo $nombreCS ?></td>
		<td><?php echo $apPaternoCS ?></td>
		<td><?php echo $apMaternoCS ?></td>
		<td><?php echo $generoCS ?></td>
		<td><?php echo $fechaNacimientoCS ?></td>
		<td><?php echo $nacionalidadCS ?></td>
		<td><?php echo $estadoCivilCS ?></td>
		<td><?php echo $direccionCS ?></td>
		<td><?php echo $departamentoCS ?></td>
		<td><?php echo $provinciaCS ?></td>
		<td><?php echo $distritoCS ?></td>
		<td><?php echo $telefonoCS ?></td>
		<td><?php echo $celularCS ?></td>
		
		<td style="background-color:#CCD;"><?php echo $lotes ?></td>
		<td style="background-color:#CCA;"><?php echo $C_l01 ?></td>
		<td><?php echo $S_l01 ?></td>
		<td><?php echo $M_l01 ?></td>
		<td><?php echo $L_l01 ?></td>
		<td><?php echo $D_l01 ?></td>

		<td style="background-color:#CCA;"><?php echo $C_l02 ?></td>
		<td><?php echo $S_l02 ?></td>
		<td><?php echo $M_l02 ?></td>
		<td><?php echo $L_l02 ?></td>
		<td><?php echo $D_l02 ?></td>

		<td style="background-color:#CCA;"><?php echo $C_l03 ?></td>
		<td><?php echo $S_l03 ?></td>
		<td><?php echo $M_l03 ?></td>
		<td><?php echo $L_l03 ?></td>
		<td><?php echo $D_l03 ?></td>

		<td style="background-color:#CCA;"><?php echo $C_l04 ?></td>
		<td><?php echo $S_l04 ?></td>
		<td><?php echo $M_l04 ?></td>
		<td><?php echo $L_l04 ?></td>
		<td><?php echo $D_l04 ?></td>

		<td style="background-color:#CCA;"><?php echo $C_l05 ?></td>
		<td><?php echo $S_l05 ?></td>
		<td><?php echo $M_l05 ?></td>
		<td><?php echo $L_l05 ?></td>
		<td><?php echo $D_l05 ?></td>

		<td style="background-color:#CCA;"><?php echo $C_l06 ?></td>
		<td><?php echo $S_l06 ?></td>
		<td><?php echo $M_l06 ?></td>
		<td><?php echo $L_l06 ?></td>
		<td><?php echo $D_l06 ?></td>

		<td><?php echo $observaciones ?></td>
		<td><?php echo $sincronizado ?></td>
		<td><?php echo $fecha ?></td>
		<td><?php echo $hora ?></td>
	</tr>
<?php
		if($registrado=="OK"){}else{
			mysqli_set_charset($conexion, "utf8");
			$sql="INSERT INTO sm_socios(codigoSocio, dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, fotoSocio, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular, fechaAdjudica, recibo, observaciones, sincronizado, eCardSocio, fecha, hora) VALUES('$codigoSocio', '$dni', '$tratamiento', '$nombre', '$apPaterno', '$apMaterno', '$genero', '$fechaNacimiento', '$fotoSocio', '$nacionalidad', '$estadoCivil', '$direccion', '$departamento', '$provincia', '$distrito', '$telefono', '$celular', '$fechaAdjudica', '$recibo', '$observaciones', '$sincronizado', '$eCardSocio', '$fecha', '$hora')";
			$socios=mysqli_query($conexion,$sql);

			$sql="INSERT INTO sm_lotes_socio(codigoSocio, lotes, C_l01, S_l01, M_l01, L_l01, D_l01, C_l02, S_l02, M_l02, L_l02, D_l02, C_l03, S_l03, M_l03, L_l03, D_l03, C_l04, S_l04, M_l04, L_l04, D_l04, C_l05, S_l05, M_l05, L_l05, D_l05, C_l06, S_l06, M_l06, L_l06, D_l06, C_l07, S_l07, M_l07, L_l07, D_l07, C_l08, S_l08, M_l08, L_l08, D_l08, C_l09, S_l09, M_l09, L_l09, D_l09, C_l10, S_l10, M_l10, L_l10, D_l10) VALUES('$codigoSocio', '$lotes', '$C_l01', '$S_l01', '$M_l01', '$L_l01', '$D_l01', '$C_l02', '$S_l02', '$M_l02', '$L_l02', '$D_l02', '$C_l03', '$S_l03', '$M_l03', '$L_l03', '$D_l03', '$C_l04', '$S_l04', '$M_l04', '$L_l04', '$D_l04', '$C_l05', '$S_l05', '$M_l05', '$L_l05', '$D_l05', '$C_l06', '$S_l06', '$M_l06', '$L_l06', '$D_l06', '$C_l07', '$S_l07', '$M_l07', '$L_l07', '$D_l07', '$C_l08', '$S_l08', '$M_l08', '$L_l08', '$D_l08', '$C_l09', '$S_l09', '$M_l09', '$L_l09', '$D_l09', '$C_l10', '$S_l10', '$M_l10', '$L_l10', '$D_l10')";
			$terrenos=mysqli_query($conexion,$sql);

			if($relacion!=""){
				$sql="INSERT INTO sm_relacion_socios(codigoSocio, relacion, dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular) VALUES('$codigoSocio', '$relacion', '$dniCS', '$tratamientoCS', '$nombreCS', '$apPaternoCS', '$apMaternoCS', '$generoCS', '$fechaNacimientoCS', '$nacionalidadCS', '$estadoCivilCS', '$direccionCS', '$departamentoCS', '$provinciaCS', '$distritoCS', '$telefonoCS', '$celularCS')";
				$cosocio=mysqli_query($conexion,$sql);
			}
		}
		//$respuesta->mensaje = $i;
		//echo json_encode($respuesta);
		$i++; 
	}

	echo '<hr>'.$hora.'<hr>';
?>
</table>