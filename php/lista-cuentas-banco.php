<?php 
	include ('funciones.php');
	$conexion    =conexionDB();
	$codigoBanco =$_GET['codigoBanco'];
	$sql="SELECT codigoCuenta, numeroCuenta, detalle FROM sm_banco_cuentas WHERE codigoBanco='$codigoBanco' AND estado='ACT'";
	$rs=mysqli_query($conexion,$sql);
	echo '<option value="" selected>SELECCIONE CUENTA BANCARIA</option>';
	while($datos=mysqli_fetch_array($rs)){
		$codCuenta    =$datos['codigoCuenta'];
		$numeroCuenta =$datos['numeroCuenta'];
		$detalle      =$datos['detalle'];
		$infoCuenta   =infoBancos($codigoBanco,'detalleEntidad').' - '.texto($detalle).' ('.$numeroCuenta.')';
		echo '<option value="'.$codCuenta.'">'.$infoCuenta.'</option>';
	}
?>