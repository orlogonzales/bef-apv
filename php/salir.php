<?php
	session_start();
	session_unset();
	session_destroy();
	$respuesta->resultado='SESION_CERRADA';
	echo json_encode($respuesta);
?>