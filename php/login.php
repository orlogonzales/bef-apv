<?php
	include('funciones.php');
	$usuario = $_POST['usuario'];
	$clave   = md5($_POST['clave']);
	$login   = login($usuario, $clave);
	$estadoLogin = $login['resultado'];
	$rol = $login['rol'];
	$descripcionRol = $login['descripcionRol'];
	$nombreUsuario = $login['nombreUsuario'];
	$respuesta = new stdClass();
	$respuesta->estadoLogin=$estadoLogin;
	$respuesta->rol=$rol;
	$respuesta->descripcionRol=$descripcionRol;
	$respuesta->nombreUsuario=$nombreUsuario;
	echo json_encode($respuesta);
?>