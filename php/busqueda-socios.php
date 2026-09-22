<?php
	include ('funciones.php');
	$operacion =$_POST[operacion];
	$conexion  =conexionDB();

	if($operacion=="BUSCA_SOCIO_COD"){
		$codigoSocio=$_POST[codigoSocio];

		$sql="SELECT dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, fotoSocio, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular, observaciones, sincronizado, fecha, hora FROM sm_socios WHERE codigoSocio='$codigoSocio'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$contar=mysqli_num_rows($row);
		$dni             =$dato[dni];
		$tratamiento     =$dato[tratamiento];
		$nombre          =$dato[nombre];
		$apPaterno       =$dato[apPaterno];
		$apMaterno       =$dato[apMaterno];
		$genero          =$dato[genero];
		$fechaNacimiento =$dato[fechaNacimiento];
		$fotoSocio       =$dato[fotoSocio];
		$nacionalidad    =$dato[nacionalidad];
		$estadoCivil     =$dato[estadoCivil];
		$direccion       =$dato[direccion];
		$departamento    =$dato[departamento];
		$provincia       =$dato[provincia];
		$distrito        =$dato[distrito];
		$telefono        =$dato[telefono];
		$celular         =$dato[celular];
		$observaciones   =$dato[observaciones];
		$sincronizado    =$dato[sincronizado];
		$fecha           =$dato[fecha];
		$hora            =$dato[hora];
		$cantidadLotes   =infoSocios($codigoSocio,'cantidadLotes');

		if($contar>0){
			$resultado='<div class="form-group"><div class="row"><div class="col-sm-12"><div class="table-responsive"><table class="table tabla table-bordered table-hover"><thead><tr class="success"><th class="text-left">CODIGO SOCIO</th><th class="text-center">LOTES</th><th class="text-left">NOMBRE DE SOCIOS</th><th class="text-center">DNI</th><th class="text-left">CELULAR</th><th class="text-center"><i class="fa fa-align-justify"></i></th></tr></thead><tbody><tr><td class="text-left">'.$codigoSocio.'</td><td class="text-center"><span class="label label-warning textoNegrita">'.ceros($cantidadLotes,2).' Lotes</span></td><td class="text-left">'.utf8_encode($nombre.' '.$apPaterno.' '.$apMaterno).'</td><td class="text-center">'.$dni.'</td><td class="text-left">'.$celular.'</td><td class="text-center"><a href="detalles-socio.php?codigoSocio='.$codigoSocio.'&opcion=detalles" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Perfil socio"><i class="icon-user-check"></i></a></td></tr></tbody></table></div></div></div></div>';
		}else{
			$resultado='<div class="row"><div class="col-sm-12"><div class="alert alert-danger no-border text-center">La busqueda no tubo resultados, intentelo nuevamente...</div></div></div>';
		}

		$respuesta->mensaje = $resultado;
	}

	if($operacion=="BUSCA_SOCIO_NOM"){
		$nombreSocio    =$_POST[nombreSocio];
		$apPaternoSocio =$_POST[apPaternoSocio];
		$apMaternoSocio =$_POST[apMaternoSocio];

		if($nombreSocio=="" AND $apPaternoSocio=="" AND $apMaternoSocio==""){
			$resultado='<div class="row"><div class="col-sm-12"><div class="alert alert-danger no-border text-center">La busqueda no tubo resultados, intentelo nuevamente...</div></div></div>';
		}else{

			$sql="SELECT codigoSocio, dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, fotoSocio, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular, observaciones, sincronizado, fecha, hora FROM sm_socios WHERE nombre LIKE '%$nombreSocio%' AND  apPaterno LIKE '%$apPaternoSocio%' AND apMaterno LIKE '%$apMaternoSocio%'";
			$row=mysqli_query($conexion,$sql);
			$contar=mysqli_num_rows($row);
			$i=1;
			if($contar>0){
				$resultado='<div class="form-group"><div class="row"><div class="col-sm-12"><div class="table-responsive"><table class="table tabla table-bordered table-hover"><thead><tr class="success"><th class="text-left">CODIGO SOCIO</th><th class="text-center">LOTES</th><th class="text-left">NOMBRE DE SOCIOS</th><th class="text-center">DNI</th><th class="text-left">CELULAR</th><th class="text-center"><i class="fa fa-align-justify"></i></th></tr></thead><tbody>';
				while($dato=mysqli_fetch_array($row)){
					$codigoSocio     =$dato[codigoSocio];
					$dni             =$dato[dni];
					$tratamiento     =$dato[tratamiento];
					$nombre          =$dato[nombre];
					$apPaterno       =$dato[apPaterno];
					$apMaterno       =$dato[apMaterno];
					$genero          =$dato[genero];
					$fechaNacimiento =$dato[fechaNacimiento];
					$fotoSocio       =$dato[fotoSocio];
					$nacionalidad    =$dato[nacionalidad];
					$estadoCivil     =$dato[estadoCivil];
					$direccion       =$dato[direccion];
					$departamento    =$dato[departamento];
					$provincia       =$dato[provincia];
					$distrito        =$dato[distrito];
					$telefono        =$dato[telefono];
					$celular         =$dato[celular];
					$observaciones   =$dato[observaciones];
					$sincronizado    =$dato[sincronizado];
					$fecha           =$dato[fecha];
					$hora            =$dato[hora];
					$cantidadLotes   =infoSocios($codigoSocio,'cantidadLotes');

					if($_SESSION['rol_apv']!='ADM'){
						$desactiva='disabled';
					}else{
						$desactiva='';
					}

					$resultado.='<tr><td class="text-left">'.$codigoSocio.'</td><td class="text-center"><span class="label label-warning textoNegrita">'.ceros($cantidadLotes,2).' Lotes</span></td><td class="text-left">'.($nombre.' '.$apPaterno.' '.$apMaterno).'</td><td class="text-center">'.$dni.'</td><td class="text-left">'.$celular.'</td><td class="text-center"><a href="detalles-socio.php?codigoSocio='.$codigoSocio.'&opcion=detalles" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Perfil socio"><i class="icon-user-check"></i></a> <button type="button" id="'.$codigoSocio.'" class="btn btn-xs btn-icon bg-danger-300 btn_eliminaSocio '.$desactiva.'"><i class="icon-trash"></i></button></td></tr>';

					$i++;
				}
				$resultado.='</tbody></table></div></div></div></div>';

				$resultado.='
					<script type="text/javascript">
						$(document).ready(function(){
							///////////////////////////////////////////////////
							/// ELIMINAR SOCIO CSARP451410521
							///////////////////////////////////////////////////
							$(".disabled").prop("disabled",true);
							$(".btn_eliminaSocio").click(function(){
								var ruta        ="../";
								var codigoSocio = $(this).attr("id");
								var operacion   ="ELIMINA_SOCIO";
								var datos       ="codigoSocio="+codigoSocio+"&operacion="+operacion;
								swal({
									title: "Eliminar",
									text: "Se va ha eliminar el socio de codigo -> "+codigoSocio,
									type: "warning",
									showCancelButton: true,
									confirmButtonColor: "#EF5350",
									confirmButtonText: "Si, Eliminar",
									cancelButtonText: "No, Cancelar",
									closeOnConfirm: true,
									closeOnCancel: true
								},
								function(isConfirm){
									if (isConfirm) { 
										swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
										$.ajax({
											type: "POST",
											url: ruta+"php/mantenimiento-socios.php",
											data: datos,
											dataType:"json",
											success: function(respuesta){
												if(respuesta.mensaje=="SOCIO_ELIMINADO"){
													new PNotify({title: "ELIMINADO", text: "El item seleccionado fue eliminadO del sistema.", addclass: "bg-success"});
													$("#resultadoNOM").fadeIn("slow").html("");
													$("#nombreSocio").val("");
													$("#apPaternoSocio").val("");
													$("#apMaternoSocio").val("");
												}
												if(respuesta.mensaje=="ERROR_OBSERVACION_ELIMINADA"){
													new PNotify({title: "ERROR", text: "Por favor intentelo nuevamente.", addclass: "bg-warning"});
												}
											}
										});
									}
								});
							});
						});
					</script>
				';

			}else{
				$resultado='<div class="row"><div class="col-sm-12"><div class="alert alert-danger no-border text-center">La busqueda no tubo resultados, intentelo nuevamente...</div></div></div>';
			}
		}
		$respuesta->mensaje = $resultado;
	}
	
	cerrarDB();
	echo json_encode($respuesta);
?>

