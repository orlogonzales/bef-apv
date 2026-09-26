<?php
	session_start();
	include ('funciones.php');
	$conexion        =conexionDB();
	$nroDocumento    =$_POST['documentoPago'];
	$verificaDOCPago =verificaDOC($nroDocumento,'VOU');
	if($verificaDOCPago=='EXISTE'){ $respuesta->mensaje ="EXISTE"; }
	if($verificaDOCPago=='NOEXISTE'){ $respuesta->mensaje ="NOEXISTE"; }
	echo json_encode($respuesta);
?>