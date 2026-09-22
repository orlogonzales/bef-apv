<?php 
	include ('funciones.php');
	$conexion    =conexionDB();
	echo '<option value="" selected>SELECCIONE USUARIO</option>';
	$sql="SELECT dni FROM sm_usuarios WHERE estado='ACT'";
	$rs=mysqli_query($conexion,$sql);
	while($datos=mysqli_fetch_array($rs)){
		echo '<option value="'.$datos[0].'">'.texto(datoUsuario($datos[0],'nombreFull')).'</option>';
	}
?>