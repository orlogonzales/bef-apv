<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	$j=1;
	$datos = array();
 	$sql="SELECT idVigencia, vigenciaJunta, activo FROM sm_junta_directiva_vigencia ORDER BY vigenciaJunta ASC";
	$vigenciasJuntaDirectiva = $conexion->query($sql);

	while ($vigencia = $vigenciasJuntaDirectiva->fetch_assoc()) {
		$idVigencia=$vigencia[idVigencia];
		$vigenciaJunta=$vigencia[vigenciaJunta];
		$activo=$vigencia[activo];
		
		$query = "SELECT COUNT(sm_junta_directiva.id) vigenciaAsignada FROM sm_junta_directiva WHERE sm_junta_directiva.idVigencia = '$idVigencia'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$vigenciaAsignada = $resultado[vigenciaAsignada];
		
		if($vigenciaAsignada>0){
			$infoAsignados='<span class="badge badge-success">'.ceros($vigenciaAsignada,2).'VECES</span>';
		}else{
			$infoAsignados='';
		}

		if($vigenciaJunta>1){
			$infoVigencia=$vigenciaJunta.' MESES';
		}else{
			$infoVigencia=$vigenciaJunta.' MES';
		}

		if($activo==1){
			$infoActivo='<span class="badge badge-success">ACTIVO</span>';
			$menuOpciones='<button type="button" class="btn btn-xs btn-dark-1 btnEliminaCargo" disabled>ACTIVO</button>';
		}else{
			$infoActivo='<span class="badge bg-pink-100">INACTIVO</span>';
			$menuOpciones='<button type="button" class="btn btn-xs btn-dark btnActivaVigencia" data-id="'.$idVigencia.'" data-vigencia="'.$infoVigencia.'">ACTIVAR</button>';
		}

		$datos[] = array(
			'vigenciaJunta'    => $infoVigencia.$duracionYear,
			'vigenciaAsignada' => $infoAsignados,
			'activo'           => $infoActivo,
			'menuOpciones'     => $menuOpciones,
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