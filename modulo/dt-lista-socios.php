<?php
	include('../php/conexion.php');
	include ('../php/funciones.php');
	$sector=$_POST['sector'];
	$manzana=$_POST['manzana'];
	$ordenar=$_POST['ordenar'];

	$j=1;
	$datos = array();

	if($ordenar=='LOTE'){
		$orden="CAST(SUBSTRING_INDEX(REPLACE(sm_lotes_socio.manzana, '-', '.'), '.', -1) AS UNSIGNED) ASC, CAST(SUBSTRING_INDEX(sm_lotes_socio.lote, '-', -1) AS UNSIGNED) ASC";
	}

	if($ordenar=='NOMBRE'){
		$orden="sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
	}

	if($ordenar=='APELLIDO'){
		$orden="sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
	}

	if($sector!='ALL' && $manzana!='ALL'){
		$sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio WHERE sm_lotes_socio.sector = '$sector' AND sm_lotes_socio.manzana = '$manzana' ORDER BY $orden";
	}

	if($sector!='ALL' && $manzana=='ALL'){
		$sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio WHERE sm_lotes_socio.sector = '$sector' ORDER BY $orden";
	}

	if($sector=='ALL' && $manzana=='ALL'){
		$sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio ORDER BY $orden";
	}

	$socios = $conexion->query($sql);
	$i=1;
	$totalMetros = 0;

	while ($socio = $socios->fetch_assoc()) {
		$codigoSocio     = $socio['codigoSocio'];
		$dni             = $socio['dni'];
		$tratamiento     = $socio['tratamiento'];
		$nombre          = $socio['nombre'];
		$apPaterno       = $socio['apPaterno'];
		$apMaterno       = $socio['apMaterno'];
		$genero          = $socio['genero'];
		$fechaNacimiento = $socio['fechaNacimiento'];
		$fotoSocio       = $socio['fotoSocio'];
		$nacionalidad    = $socio['nacionalidad'];
		$estadoCivil     = $socio['estadoCivil'];
		$direccion       = $socio['direccion'];
		$departamento    = $socio['departamento'];
		$provincia       = $socio['provincia'];
		$distrito        = $socio['distrito'];
		$telefono        = $socio['telefono'];
		$celular         = $socio['celular'];
		$observaciones   = $socio['observaciones'];
		$sector          = $socio['sector'];
		$manzana         = $socio['manzana'];
		$lote            = $socio['lote'];
		$metraje         = $socio['metraje'];
		$infoMetraje     = $metraje.' m²';

		$cantidadLotes   = infoSocios($codigoSocio,'cantidadLotes');
		$nroObservaciones= infoSocios($codigoSocio,'observaciones');
		$nombreSocio     = trim($nombre.' '.$apPaterno.' '.$apMaterno);
		if($nroObservaciones>0){ $btn_lista=""; }else{ $btn_lista="disabled"; }

		$lotesSocio='<span class="label label-warning textoNegrita">'.ceros($cantidadLotes,2).' LOTES</span>';
		$infoSector= '<span class="label label-info">'.$sector.'</span>';
		$infoManzana= '<span class="label label-info">'.$manzana.'</span>';
		$infoLote= '<span class="label label-info">'.$lote.'</span>';

		$menuOpciones='
			<a href="detalles-socio.php?codigoSocio='.$codigoSocio.'&opcion=detalles" class="btn btn-xs btn-icon bg-brown" data-bs-toggle="tooltip" title="PERFIL DE SOCIO"><i class="icon-user-check"></i></a>
			<button type="button" class="btn btn-xs btn-icon bg-grey btnAgregaNota" data-bs-toggle="tooltip" title="AGREGA OBSERVACION" data-socio="'.$codigoSocio.'"><i class="icon-pencil5"></i></button>
			<button type="button" class="btn btn-xs btn-icon bg-warning-300 btnListarNotas" data-bs-toggle="tooltip" title="VER OBSERVACIONES" data-socio="'.$codigoSocio.'"><i class="icon-comments"></i></button>
		';

		$datos[] = array(
			'Nro'              => ceros($j,2),
			'codigoSocio'      => $codigoSocio,
			'nombreSocio'      => $nombreSocio,
			'infoDNI'          => $dni,
			'infoCelular'      => $celular,
			'lotesSocio'       => $lotesSocio,
			'sector'           => $infoSector,
			'manzana'          => $infoManzana,
			'lote'             => $infoLote,
			'metraje'          => $infoMetraje,
			'botoneraOpciones' => $menuOpciones,
		);

		$totalMetros=bcdiv(($totalMetros+$metraje), '1', 2);

		$j++;
	}
	$conexion->close();

	$data=array(
		'data'         => $datos,
		'totalMetraje' => $totalMetros
	);

	echo json_encode($data);
	exit;
?>