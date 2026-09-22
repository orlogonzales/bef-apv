<?php
	include ('funciones.php');
	$conexion=conexionDB();
	$sql="SELECT codigoSocio, dni, nombre, apPaterno, apMaterno, genero, fechaNacimiento, fotoSocio, estadoCivil, direccion, departamento, provincia, distrito, fechaAdjudica, recibo FROM sm_socios";
	$row=mysqli_query($conexion,$sql);
	$resultado=mysqli_num_rows($row);
	if($resultado>0){
		ini_set("memory_limit","512M");
		set_time_limit(600);
		date_default_timezone_set("America/Lima");

		if (PHP_SAPI == 'cli')
			die('Este archivo solo se puede ver desde un navegador web');

		require_once '../PHPExcel/PHPExcel.php';

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("OGG-Labs")
							 ->setLastModifiedBy("OGG-Labs")
							 ->setTitle("Reporte de socios de la APV")
							 ->setSubject("Reporte de socios de la APV")
							 ->setDescription("Reporte de socios")
							 ->setKeywords("reporte socios apv")
							 ->setCategory("Reporte ZEBRA");

		$titulosColumnas = array('CODIGO SOCIO', 'LOTES', 'DNI', 'NOMBRE', 'A. PATERNO', 'A.  MATERNO', 'GENERO', 'F. NACIMIENTO', 'EDAD', 'FOTO', 'E. CIVIL', 'DIRECCION', 'DEPARTAMENTO', 'PROVINCIA', 'DISTRITO', 'ADJUDICADO', 'EXPEDIENTE', 'COSOCIO', 'DNI', 'NOMBRE - COSOCIO');
		
		$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('A1',  $titulosColumnas[0])
		            ->setCellValue('B1',  $titulosColumnas[1])
        		    ->setCellValue('C1',  $titulosColumnas[2])
        		    ->setCellValue('D1',  $titulosColumnas[3])
        		    ->setCellValue('E1',  $titulosColumnas[4])
        		    ->setCellValue('F1',  $titulosColumnas[5])
        		    ->setCellValue('G1',  $titulosColumnas[6])
            		->setCellValue('H1',  $titulosColumnas[7])
            		->setCellValue('I1',  $titulosColumnas[8])
            		->setCellValue('J1',  $titulosColumnas[9])
            		->setCellValue('K1',  $titulosColumnas[10])
            		->setCellValue('L1',  $titulosColumnas[11])
            		->setCellValue('M1',  $titulosColumnas[12])
            		->setCellValue('N1',  $titulosColumnas[13])
            		->setCellValue('O1',  $titulosColumnas[14])
            		->setCellValue('P1',  $titulosColumnas[15])
            		->setCellValue('Q1',  $titulosColumnas[16])
            		->setCellValue('R1',  $titulosColumnas[17])
            		->setCellValue('S1',  $titulosColumnas[18])
            		->setCellValue('T1',  $titulosColumnas[19]);
		
		$i = 2;
		while($n=mysqli_fetch_array($row)){
			$codigoSocio     =$n[codigoSocio];
			$lotes           =ceros(infoSocios($codigoSocio,'cantidadLotes'),2);
			$dni             =$n[dni];
			$nombre          =$n[nombre];
			$apPaterno       =$n[apPaterno];
			$apMaterno       =$n[apMaterno];
			$genero          =infoGenero($n[genero]);
			$fechaNacimiento =infoFecha($n[fechaNacimiento],'resultados');
			$edad            =calculaEdad($n[fechaNacimiento]);
			$fotoSocio       =$codigoSocio.".jpg";
			$estadoCivil     =estadoCivil($n[estadoCivil]);
			$direccion       =$n[direccion];
			$departamento    =infoDepartamento($n[departamento]);
			$provincia       =$n[provincia];
			$distrito        =$n[distrito];
			$fechaAdjudica   =$n[fechaAdjudica];
			$recibo          =$n[recibo];
			$cosocio         =infoSocios($codigoSocio,'cosocio');
			$relacion        =infoRelacion($cosocio);

			if($cosocio==""){
				$cs_dni='';
				$cs_nombre='';
			}else{
				$cs_dni=infoSocios($codigoSocio,'CSdni');
				$cs_nombre=infoSocios($codigoSocio,'CSnombre');
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

		$estiloTituloColumnas = array(
			'font' => array(
				'name'      => 'Arial',
				'bold'      => true,                          
				'size' =>10,
				'color'     => array(
					'rgb' => 'FFFFFF'
				)
			),
			'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'		=> array('argb' => '333333')
			),
			'borders' => array(
				'top'     => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
					'color' => array(
						'rgb' => '333333'
					)
				),
				'bottom'     => array(
					'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
					'color' => array(
						'rgb' => '333333'
					)
				)
			),
			'alignment' =>  array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
					'wrap'          => TRUE
			));
			
		$estiloInformacion = new PHPExcel_Style();
		$estiloInformacion->applyFromArray(
			array(
				'font' => array(
				'name'      => 'Arial',
				'size' =>10,        
				'color'     => array(
					'rgb' => '000000'
				)
			),
			'fill' 	=> array(
				'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
				'color'		=> array('argb' => 'CCCCCC')
			),
			'borders' => array(
				'left'     => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN ,
					'color' => array(
						'rgb' => '333333'
					)
				)             
			)
		));
		
		$objPHPExcel->getActiveSheet()->getStyle('A1:T1')->applyFromArray($estiloTituloColumnas);
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A2:T".($i-1));
				
		for($i = 'A'; $i <= 'T'; $i++){
			$objPHPExcel->setActiveSheetIndex(0)			
				->getColumnDimension($i)->setAutoSize(TRUE);
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('Socios');
		$objPHPExcel->setActiveSheetIndex(0);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="socios-apv-zebra.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
		
	}
	else{
		print_r('No hay resultados para mostrar');
	}
?>