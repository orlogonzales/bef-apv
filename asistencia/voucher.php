<?php
	include('php/funciones.php');
	$codigoSocio="APVRP238747097";
	$codigoActividad="ASA2016101408300023978817";
	//$codigoActividad=$_GET[codigoActividad];
	//$codigoSocio=$_GET[codigoSocio];
	$actividad  =texto(infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad'));
	$fecha      =infoFecha(infoActividad($idJuntaDirectiva,$codigoActividad,'','fechaActividad'),'larga')." - ".infoHora(infoActividad($idJuntaDirectiva,$codigoActividad,'','horaActividad'));


	$conexion=conexionDB();
	$sql="SELECT nombre FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'";
	$row=mysqli_query($conexion,$sql);
	$dato=mysqli_fetch_array($row);
	$nombre=$dato[0];


	//$nombre     =texto(infoSocio($codigoSocio,'nombre').' '.infoSocio($codigoSocio,'apPaterno').' '.infoSocio($codigoSocio,'apMaterno'));
	$lotes      =infoSocio($codigoSocio,'lotes');
	$asistencia ="HORA INGRESO: 8:00 // HORA SALIDA: 13:00";
	$tarde      ="TARDANZA. 0.25 MINUTOS";
	$multa      ="MULTA: S/. 150.00 x 04 LOTES";


	echo "actividad --> ".$actividad."<br>";
	echo "fecha --> ".$fecha."<br>";
	echo "nombre --> ".$nombre."<br>";
	echo "codigoSocio --> ".$codigoSocio."<br>";
	echo "asistencia --> ".$asistencia."<br>";
	echo "tarde --> ".$tarde."<br>";
	echo "lotes --> ".$lotes."<br>";
	echo "multa --> ".$multa."<br>";
	echo "sql --> ".$sql."<br>";
?>
