<?php
	include ('funciones.php');
	$conexion=conexionDB();
	date_default_timezone_set("America/Lima");
	$fecha=infoTiempo('fecha');
	$timestamp = strtotime($fecha);
	$new_date = date("dmY", $timestamp);
	$nombre_archivo='Lista_Sicios_'.$new_date;

	require '../vendor/autoload.php';
	$objPHPExcel = new \PHPExcel();
	$objPHPExcel->getProperties()->setCreator("OGG-Labs")
								 ->setLastModifiedBy("OGG-Labs")
								 ->setTitle("Reporte de socios de la APV")
								 ->setSubject("Reporte de socios de la APV")
								 ->setDescription("Reporte de socios de la APV")
								 ->setKeywords("reporte socios apv")
								 ->setCategory("Reporte ZEBRA");
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'CODIGO SOCIO')
				->setCellValue('B1', 'LOTES')
				->setCellValue('C1', 'DNI')
				->setCellValue('D1', 'NOMBRE')
				->setCellValue('E1', 'A. PATERNO')
				->setCellValue('F1', 'A.  MATERNO')
				->setCellValue('G1', 'GENERO')
				->setCellValue('H1', 'F. NACIMIENTO')
				->setCellValue('I1', 'EDAD')
				->setCellValue('J1', 'FOTO')
				->setCellValue('K1', 'E. CIVIL')
				->setCellValue('L1', 'DIRECCION')
				->setCellValue('M1', 'DEPARTAMENTO')
				->setCellValue('N1', 'PROVINCIA')
				->setCellValue('O1', 'DISTRITO')
				->setCellValue('P1', 'ADJUDICADO')
				->setCellValue('Q1', 'EXPEDIENTE')
				->setCellValue('R1', 'COSOCIO')
				->setCellValue('S1', 'DNI')
				->setCellValue('T1', 'NOMBRE - COSOCIO');

		$i = 2;
		$sql="SELECT codigoSocio, dni, nombre, apPaterno, apMaterno, genero, fechaNacimiento, direccion, departamento, provincia, distrito, fechaAdjudica, recibo, estadoCivil FROM sm_socios";
		$rs=mysqli_query($conexion,$sql);
		while($n=mysqli_fetch_array($rs)){
			$codigoSocio     = $n['codigoSocio'];
			$dni             = $n['dni'];
			$nombre          = utf8_decode($n['nombre']);
			$apPaterno       = utf8_decode($n['apPaterno']);
			$apMaterno       = utf8_decode($n['apMaterno']);
			$idGenero        = $n['genero'];
			$fechaNacimiento = infoFecha($n['fechaNacimiento'],'resultados');
			$edad            = calculaEdad($n['fechaNacimiento']);
			$fotoSocio       = $codigoSocio.".jpg";
			$direccion       = utf8_decode($n['direccion']);
			$idDepartamento  = $n['departamento'];
			$provincia       = $n['provincia'];
			$distrito        = $n['distrito'];
			$fechaAdjudica   = infoFecha($n['fechaAdjudica'],'resultados');
			$recibo          = $n['recibo'];
			$idCivil         = $n['estadoCivil'];

			$query="SELECT COUNT(id) AS totalLotes FROM sm_lotes_socio WHERE codigoSocio = '$codigoSocio'";
			$row = mysqli_query($conexion,$query);
			$dato = mysqli_fetch_array($row);
			$lotes = $dato[totalLotes];

			$query="SELECT detalle FROM sm_a_genero WHERE id='$idGenero'";
			$row=mysqli_query($conexion,$query);
			$dato=mysqli_fetch_array($row);
			$genero=$dato['detalle'];

			$query="SELECT detalle FROM sm_a_departamento WHERE id='$idDepartamento'";
			$row=mysqli_query($conexion,$query);
			$dato=mysqli_fetch_array($row);
			$departamento=$dato['detalle'];

			$query="SELECT detalle FROM sm_a_civil WHERE id='$idCivil'";
			$row=mysqli_query($conexion,$query);
			$dato=mysqli_fetch_array($row);
			$estadoCivil=$dato['detalle'];

			$query="SELECT relacion FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			$dato=mysqli_fetch_array($row);
			$cosocio=$dato['relacion'];

			$query="SELECT detalle FROM sm_a_cosocio WHERE id='$cosocio'";
			$row=mysqli_query($conexion,$query);
			$dato=mysqli_fetch_array($row);
			$relacion=$dato['detalle'];

			if($cosocio==""){
				$cs_dni='';
				$cs_nombre='';
			}else{
				$query="SELECT dni FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
				$row=mysqli_query($conexion,$query);
				$dato=mysqli_fetch_array($row);
				$cs_dni=$dato['dni'];

				$query="SELECT CONCAT(nombre,' ',apPaterno,' ',apMaterno) AS CSnombre FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
				$row=mysqli_query($conexion,$query);
				$dato=mysqli_fetch_array($row);
				$cs_nombre=$dato['CSnombre'];
			}

			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A'.$i,$codigoSocio)
						->setCellValue('B'.$i,$lotes)
						->setCellValue('C'.$i,$dni)
						->setCellValue('D'.$i,utf8_encode($nombre))
						->setCellValue('E'.$i,utf8_encode($apPaterno))
						->setCellValue('F'.$i,utf8_encode($apMaterno))
						->setCellValue('G'.$i,$genero)
						->setCellValue('H'.$i,$fechaNacimiento)
						->setCellValue('I'.$i,$edad)
						->setCellValue('J'.$i,$fotoSocio)
						->setCellValue('K'.$i,$estadoCivil)
						->setCellValue('L'.$i,utf8_encode($direccion))
						->setCellValue('M'.$i,$departamento)
						->setCellValue('N'.$i,$provincia)
						->setCellValue('O'.$i,$distrito)
						->setCellValue('P'.$i,$fechaAdjudica)
						->setCellValue('Q'.$i,$recibo)
						->setCellValue('R'.$i,$relacion)
						->setCellValue('S'.$i,$cs_dni)
						->setCellValue('T'.$i,utf8_encode($cs_nombre));
			$i++;
		}

	for($i = 'A'; $i <= 'T'; $i++){
		$objPHPExcel->setActiveSheetIndex(0)
					->getColumnDimension($i)
					->setAutoSize(TRUE);
	}

	$objPHPExcel->getActiveSheet()->setTitle('socios-apv-xls');
	$objPHPExcel->setActiveSheetIndex(0);
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'.$nombre_archivo.'.xls"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header ('Cache-Control: cache, must-revalidate');
	header ('Pragma: public');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;