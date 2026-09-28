<?php
	include ('funciones.php');
	$conexion=conexionDB();
	date_default_timezone_set("America/Lima");
	$fecha=infoTiempo('fecha');
	$timestamp = strtotime($fecha);
	$new_date = date("dmY", $timestamp);
	$nombre_archivo='Lista_Sicios_'.$new_date;

	if (!class_exists('SimpleXlsxWriter')) {
		class SimpleXlsxWriter {
			private $sheetName = 'socios-apv-xls';
			private $rows = [];

			public function __construct($sheetName = 'socios-apv-xls') {
				$this->sheetName = $sheetName;
			}

			public function addRow(array $row) {
				$this->rows[] = $row;
			}

			public function writeToString() {
				$tmpFile = tempnam(sys_get_temp_dir(), 'xlsx_');
				$zip = new ZipArchive();
				if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
					throw new Exception("Cannot create temporary zip archive");
				}

				// 1. [Content_Types].xml
				$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
					. '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
					. '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
					. '<Default Extension="xml" ContentType="application/xml"/>'
					. '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
					. '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
					. '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
					. '</Types>';
				$zip->addFromString('[Content_Types].xml', $contentTypes);

				// 2. _rels/.rels
				$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
					. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
					. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
					. '</Relationships>';
				$zip->addFromString('_rels/.rels', $rels);

				// 3. xl/_rels/workbook.xml.rels
				$wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
					. '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
					. '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
					. '<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
					. '</Relationships>';
				$zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

				// 4. xl/workbook.xml
				$safeSheetName = htmlspecialchars($this->sheetName, ENT_XML1, 'UTF-8');
				$workbook = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
					. '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
					. '<bookViews><workbookView xWindow="0" yWindow="0" windowWidth="20480" windowHeight="10240"/></bookViews>'
					. '<sheets><sheet name="' . $safeSheetName . '" sheetId="1" r:id="rId1"/></sheets>'
					. '</workbook>';
				$zip->addFromString('xl/workbook.xml', $workbook);

				// 5. xl/styles.xml
				$styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
					. '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
					. '<fonts count="2">'
					. '<font><sz val="10"/><color rgb="000000"/><name val="Arial"/></font>'
					. '<font><b/><sz val="10"/><color rgb="000000"/><name val="Arial"/></font>'
					. '</fonts>'
					. '<fills count="2">'
					. '<fill><patternFill patternType="none"/></fill>'
					. '<fill><patternFill patternType="gray125"/></fill>'
					. '</fills>'
					. '<borders count="1">'
					. '<border><left/><right/><top/><bottom/><diagonal/></border>'
					. '</borders>'
					. '<cellStyleXfs count="1">'
					. '<xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>'
					. '</cellStyleXfs>'
					. '<cellXfs count="2">'
					. '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
					. '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
					. '</cellXfs>'
					. '</styleSheet>';
				$zip->addFromString('xl/styles.xml', $styles);

				// 6. xl/worksheets/sheet1.xml
				$sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n"
					. '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
					. '<sheetData>';

				foreach ($this->rows as $rIdx => $row) {
					$rowNum = $rIdx + 1;
					$sheetXml .= '<row r="' . $rowNum . '">';
					$colLetterIdx = 0;
					foreach ($row as $val) {
						$colRef = self::colIndexToLetters($colLetterIdx) . $rowNum;
						$colLetterIdx++;

						if ($val === null || $val === '') {
							continue;
						}

						$styleAttr = ($rIdx === 0) ? ' s="1"' : '';
						$strVal = (string)$val;

						if (is_int($val) || (is_numeric($val) && !preg_match('/^0[0-9]+/', $strVal) && strlen($strVal) < 12)) {
							$sheetXml .= '<c r="' . $colRef . '"' . $styleAttr . '><v>' . $val . '</v></c>';
						} else {
							$cleaned = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $strVal);
							$safeStr = htmlspecialchars($cleaned, ENT_XML1, 'UTF-8');
							$sheetXml .= '<c r="' . $colRef . '"' . $styleAttr . ' t="inlineStr"><is><t>' . $safeStr . '</t></is></c>';
						}
					}
					$sheetXml .= '</row>';
				}

				$sheetXml .= '</sheetData></worksheet>';
				$zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);

				$zip->close();

				$content = file_get_contents($tmpFile);
				@unlink($tmpFile);
				return $content;
			}

			public static function colIndexToLetters($idx) {
				$result = '';
				$idx += 1;
				while ($idx > 0) {
					$remainder = ($idx - 1) % 26;
					$result = chr(65 + $remainder) . $result;
					$idx = (int)(($idx - $remainder) / 26);
				}
				return $result;
			}
		}
	}

	$writer = new SimpleXlsxWriter('socios-apv-xls');
	$writer->addRow([
		'CODIGO SOCIO', 'LOTES', 'DNI', 'NOMBRE', 'A. PATERNO', 'A.  MATERNO',
		'GENERO', 'F. NACIMIENTO', 'EDAD', 'FOTO', 'E. CIVIL', 'DIRECCION',
		'DEPARTAMENTO', 'PROVINCIA', 'DISTRITO', 'ADJUDICADO', 'EXPEDIENTE',
		'COSOCIO', 'DNI', 'NOMBRE - COSOCIO'
	]);

	$sql="SELECT codigoSocio, dni, nombre, apPaterno, apMaterno, genero, fechaNacimiento, direccion, departamento, provincia, distrito, fechaAdjudica, recibo, estadoCivil FROM sm_socios";
	$rs=mysqli_query($conexion,$sql);
	while($n=mysqli_fetch_array($rs)){
		$codigoSocio     = $n['codigoSocio'];
		$dni             = $n['dni'];
		$nombre          = $n['nombre'];
		$apPaterno       = $n['apPaterno'];
		$apMaterno       = $n['apMaterno'];
		$idGenero        = $n['genero'];
		$fechaNacimiento = infoFecha($n['fechaNacimiento'],'resultados');
		$edad            = calculaEdad($n['fechaNacimiento']);
		$fotoSocio       = $codigoSocio.".jpg";
		$direccion       = $n['direccion'];
		$idDepartamento  = $n['departamento'];
		$provincia       = $n['provincia'];
		$distrito        = $n['distrito'];
		$fechaAdjudica   = infoFecha($n['fechaAdjudica'],'resultados');
		$recibo          = $n['recibo'];
		$idCivil         = $n['estadoCivil'];

		$query="SELECT COUNT(id) AS totalLotes FROM sm_lotes_socio WHERE codigoSocio = '$codigoSocio'";
		$row = mysqli_query($conexion,$query);
		$dato = mysqli_fetch_array($row);
		$lotes = $dato['totalLotes'] ?? 0;

		$query="SELECT detalle FROM sm_a_genero WHERE id='$idGenero'";
		$row=mysqli_query($conexion,$query);
		$dato=mysqli_fetch_array($row);
		$genero=$dato['detalle'] ?? '';

		$query="SELECT detalle FROM sm_a_departamento WHERE id='$idDepartamento'";
		$row=mysqli_query($conexion,$query);
		$dato=mysqli_fetch_array($row);
		$departamento=$dato['detalle'] ?? '';

		$query="SELECT detalle FROM sm_a_civil WHERE id='$idCivil'";
		$row=mysqli_query($conexion,$query);
		$dato=mysqli_fetch_array($row);
		$estadoCivil=$dato['detalle'] ?? '';

		$query="SELECT relacion FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
		$row=mysqli_query($conexion,$query);
		$dato=mysqli_fetch_array($row);
		$cosocio=$dato['relacion'] ?? '';

		$query="SELECT detalle FROM sm_a_cosocio WHERE id='$cosocio'";
		$row=mysqli_query($conexion,$query);
		$dato=mysqli_fetch_array($row);
		$relacion=$dato['detalle'] ?? '';

		if($cosocio==""){
			$cs_dni='';
			$cs_nombre='';
		}else{
			$query="SELECT dni FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			$dato=mysqli_fetch_array($row);
			$cs_dni=$dato['dni'] ?? '';

			$query="SELECT CONCAT(nombre,' ',apPaterno,' ',apMaterno) AS CSnombre FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$query);
			$dato=mysqli_fetch_array($row);
			$cs_nombre=$dato['CSnombre'] ?? '';
		}

		$writer->addRow([
			$codigoSocio,
			$lotes,
			$dni,
			$nombre,
			$apPaterno,
			$apMaterno,
			$genero,
			$fechaNacimiento,
			$edad,
			$fotoSocio,
			$estadoCivil,
			$direccion,
			$departamento,
			$provincia,
			$distrito,
			$fechaAdjudica,
			$recibo,
			$relacion,
			$cs_dni,
			$cs_nombre
		]);
	}

	$xlsxData = $writer->writeToString();
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$nombre_archivo.'.xlsx"');
	header('Cache-Control: max-age=0');
	header('Cache-Control: max-age=1');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
	header('Cache-Control: cache, must-revalidate');
	header('Pragma: public');
	echo $xlsxData;
	exit;