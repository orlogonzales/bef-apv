<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	
	$datos = array();
	$sql="SELECT sm_junta_directiva_fin_periodo.tiempo FROM sm_junta_directiva_fin_periodo WHERE id='1'";
	$tiempoRatificacion = $conexion->query($sql);

	while ($finPeriodo = $tiempoRatificacion->fetch_assoc()) {
		$tiempo=$finPeriodo[tiempo];

		if($tiempo>1){
			$infoTiempo = '<span class="text-danger">'.ceros($tiempo,2).' MESES DE TIEMPO DE PRORROGA</span> PARA RATIFICAR JUNTA DIRECTIVA';
		}else{
			$infoTiempo = '<span class="text-danger">'.ceros($tiempo,2).' MES DE TIEMPO DE  PRORROGA</span> PARA RATIFICAR JUNTA DIRECTIVA';
		}

		$datos[] = array(
			'tiempo' => $infoTiempo,
		);
	}
	$conexion->close();

	$data=array(
		'data' => $datos
	);

	echo json_encode($data);
	exit;
?>