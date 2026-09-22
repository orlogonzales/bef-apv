<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	session_start();
	$rolUsuario        = $_SESSION['rol_apv'];
	$codigoSocio=$_POST['codigoSocio'];
	
	$sql="SELECT id, observacion, fecha, hora, usuario FROM sm_socios_observacion WHERE codigoSocio='$codigoSocio' ORDER BY id DESC";
	$notas = $conexion->query($sql);
	$j=1;

	while ($nota = $notas->fetch_assoc()) {
		$id           = $nota['id'];
		$observacion  = $nota['observacion'];
		$fecha        = $nota['fecha'];
		$hora         = $nota['hora'];
		$usuario      = $nota['usuario'];
		$infoRegistro = registradoPor($usuario,$fecha,$hora,'NO','');

		if($rolUsuario=='ADM'){
			$menuOpciones='<button type="button" class="btn btn-xs btn-warning btn-icon btn-rounded btnEliminaNota" data-id="'.$id.'" data-socio="'.$codigoSocio.'"><i class="icon-trash"></i></button>';
		}else{
			$menuOpciones='';
		}

		$datos[] = array(
			'Nro'              => ceros($j,2),
			'observacion'      => $observacion,
			'infoRegistro'     => $infoRegistro,
			'botoneraOpciones' => $menuOpciones
		);

		$j++;
	}
	$conexion->close();

	$data=array(
		'data' => $datos
	);

	echo json_encode($data);
	exit;
?>