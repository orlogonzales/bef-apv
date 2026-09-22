<?php 
	include ('funciones.php');
	$conexion    =conexionDB();
	echo '<option value="" selected>SELECCIONE SOCIO</option>';
	$sql="SELECT codigoSocio FROM sm_socios";
	$rs=mysqli_query($conexion,$sql);
	while($datos=mysqli_fetch_array($rs)){
		echo '<option value="'.$datos[0].'">'.texto(infoSocios($datos[0],'nombre')).'</option>';
	}
?>