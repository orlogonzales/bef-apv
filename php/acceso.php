<?php
	include ('funciones.php');
	$usuario =limpiar($_POST['usuario']); 
	$clave   =md5(limpiar($_POST['clave']));
	$login   =login($usuario, $clave);
	$respuesta->login =$login;
	echo json_encode($respuesta);
?>