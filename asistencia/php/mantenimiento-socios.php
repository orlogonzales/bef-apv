<?php
	include('funciones.php');
	$conexion=conexionDB();
	session_start();

	if($_GET[operacion]=="SUBIR_ARCHIVO"){
		$archivoSocios =$_FILES['archivo']['name'];
		$archivo       =explode(".", $_FILES['archivo']['name']);
		$tipoFile      =end($archivo);
		$nombre        =$_FILES['archivo']['name'];
		$upload        ='../json/'.$nombre;
		$socios        =infoSocio($codigoSocio,'socios');

		if (file_exists($upload)){ unlink($upload); }

		if($archivoSocios){
			if ($tipoFile=='json'){
				$resultado = @move_uploaded_file($_FILES['archivo']["tmp_name"], $upload);
				if ($resultado){
					$json_file = file_get_contents($upload);
					$jfo = json_decode($json_file);
					$contar=count($jfo);
					if($contar>$socios){
						foreach($jfo as $obj){
							$codigoSocio =$obj->codigoSocio;
							$nombre      =$obj->nombre;
							$apPaterno   =$obj->apPaterno;
							$apMaterno   =$obj->apMaterno;
							$lotes       =$obj->lotes;
							$verifica    =infoSocio($codigoSocio,'verifica');
							mysqli_set_charset($conexion, "utf8");

							if($verifica==""){
								$sql="INSERT INTO sm_terminal_socios(codigoSocio, nombre, apPaterno, apMaterno, lotes) VALUES('$codigoSocio', '$nombre', '$apPaterno', '$apMaterno', '$lotes')";
								$rs=mysqli_query($conexion,$sql);
							}else{
								$sql="UPDATE terminal_socios SET nombre='$nombre', apPaterno='$apPaterno', apMaterno='$apMaterno', lotes='$lotes' WHERE codigoSocio='$codigoSocio'";
								$rs=mysqli_query($conexion,$sql);
							}
						}
						$respuesta->mensaje = "ACTUALIZADO";
					}else{
						$respuesta->mensaje = "ARCHIVOINCORRECTO";
						$respuesta->socios = $contar;
					}
				} else {
					$respuesta->mensaje = "ERRORSERVER";
				}
			} else { $respuesta->mensaje = "ERRORFORMATO"; }
		}else{ $respuesta->mensaje = "SINFILE"; }
	}

	echo json_encode($respuesta);
?>