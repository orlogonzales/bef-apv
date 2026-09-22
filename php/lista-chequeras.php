<?php
	include ('funciones.php');
	$codigoCuenta =$_POST['codigoCuenta'];
	$nroChequera  =1+(1*(infoChequeras('','',$codigoCuenta,'chequerasRegistradosCTA')));
	$respuesta->mensaje = ceros($nroChequera,5);
	$respuesta->chequera = $nroChequera;
	echo json_encode($respuesta);
?>