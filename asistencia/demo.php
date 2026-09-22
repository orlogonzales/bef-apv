<?php
	$actividad="TEMA O NOMBRE DE LA ACTIIVDAD QUE SE VA HA REALIZAR COMO ASAMBLEAS O FAENAS";
	$fecha="CUSCO 13 ENERO 2016";
	$nombre="NOMBRE Y APELLIDO DE SOCIO";
	$asistencia="HORA INGRESO: 8:00 // HORA SALIDA: 13:00";
	$tarde="TARDANZA. 0.25 MINUTOS";
	$multa="MULTA: S/. 150.00 x 04 LOTES";
	if(($handle = @fopen("\\\\OGG-LABS\Terminal", "w")) === FALSE) { die('No se pudo Imprimir, Verifique su conexion con el Terminal'.$handle); }
	//if(($handle = @fopen("USB001", "w")) === FALSE) { die('No se pudo Imprimir, Verifique su conexion con el Terminal'.$handle); }
	//$dato = $_POST['datos']; 


	fwrite($handle, chr(27). chr(64));//reinicio
	fwrite($handle, chr(27). chr(100). chr(0));
	fwrite($handle, chr(27). chr(97). chr(1)); //centrado
	fwrite($handle,"==========================================");
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(32). chr(3)); //Espacio entre letras
	fwrite($handle, chr(27). chr(33). chr(16)); //Fuente doble alto
	fwrite($handle,"APV RESIDENCIAL EL PARAISO");
	fwrite($handle, chr(27). chr(33). chr(0)); //Fuente doble alto
	fwrite($handle, chr(27). chr(32). chr(0)); //Espacio entre letras
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle,"==========================================");
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, $actividad);
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, $fecha);
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle,"------------------------------------------");
	fwrite($handle, chr(27). chr(33). chr(0)); //fuente normal
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, $nombre);
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, $asistencia);
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, $tarde);
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, $multa);
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(27). chr(100). chr(1));
	fwrite($handle, chr(29). chr(86). chr(1));
	fprintf($handle);
	$salida = shell_exec('USB001 lpr');
	fclose($handle);
?>
