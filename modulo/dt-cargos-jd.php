<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	$j=1;
	$datos = array();

	$sql="SELECT idCargoJunta, cargoJunta FROM sm_junta_directiva_cargos ORDER BY idCargoJunta ASC";
	$cargosJuntaDirectiva = $conexion->query($sql);

	while ($cargos = $cargosJuntaDirectiva->fetch_assoc()) {
		$idCargoJunta=$cargos[idCargoJunta];
		$cargoJunta=$cargos[cargoJunta];
		
		$query = "SELECT COUNT(codigoSocio) AS cargoAsignado FROM sm_junta_directiva_integrantes WHERE idCargoJunta = '$idCargoJunta'";
		$consulta = $conexion->query($query);
		$resultado = $consulta->fetch_assoc();
		$cargoAsignado = $resultado[cargoAsignado];
		
		if($cargoAsignado>0){
			$infoAsignados='<span class="badge badge-success">'.ceros($cargoAsignado,2).'VECES</span>';
			$desactiva="disabled";
		}else{
			$infoAsignados='<span class="badge badge-warning">NINGUNA</span>';
			$desactiva="";
		}

		if($vigenciaJunta>1){
			$infoVigencia=$vigenciaJunta.' AÑOS';
		}else{
			$infoVigencia=$vigenciaJunta.' AÑO';
		}

		$menuOpciones='<button type="button" class="btn btn-xs btn-danger btnEliminaCargo" '.$desactiva.' data-id="'.$idCargoJunta.'" data-cargo="'.$cargoJunta.'">ELIMINA</button>';

		$datos[] = array(
			'Nro'          => ceros($j,2),
			'cargo'        => $cargoJunta,
			'asignados'    => $infoAsignados,
			'menuOpciones' => $menuOpciones
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