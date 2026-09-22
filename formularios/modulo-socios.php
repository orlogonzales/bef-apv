<?php
	include ('../php/funciones.php');
	$ventana_modal    = $_POST['ventana_modal'];
	$error_modal      = $_POST['error_modal'];
	$loader_modal     = $_POST['loader_modal'];
	$contenedor_modal = $_POST['contenedor_modal'];
	$operacion        = $_POST['operacion'];
?>

<?php if($operacion=='buscar_socio_dni'){ ?>
	<form id="form_busqueda_dni">
		<div class="form-group">
			<div class="row">
				<div class="col-sm-6">
					<input type="text" name="dni" id="dni" class="form-control input-lg text-danger textoNegrita textoMayuscula" onblur="mayusculas(event, this)" placeholder="Ingrese DNI" autofocus>
				</div>
				<div class="col-sm-3">
					<button type="button" id="bt_buscar" class="btn btn-block btn-lg bg-grey">BUSCAR SOCIO</button>
				</div>
				<div class="col-sm-3">
					<button type="button" class="btn btn-block btn-lg btn-warning" data-dismiss="modal">CERRAR VENTANA</button>
				</div>
			</div>
			<div id="resultados"></div>
		</div>
	</form>

	<script type="text/javascript">
		$(document).ready(function(){
			/////////////////////////////////////////////////////////////////////
			/// VALIDA DATOS DE CAMPOS
			/////////////////////////////////////////////////////////////////////
			$(function(){ $('#dni').validar('0123456789'); });

			///////////////////////////////////////////////////////////////////////////////////////////////
			/// INICALIZAR FORMULARIO
			///////////////////////////////////////////////////////////////////////////////////////////////
			$('#dni').focus();

			///////////////////////////////////////////////////////////////////////////////////////////////
			/// BOTON REGISTRO DE TOURS - PASO 2
			///////////////////////////////////////////////////////////////////////////////////////////////
			$('#bt_buscar').click(function(){
				var valida= $("#form_busqueda_dni").valid();
				if(valida){
					var loader_circular  = '<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center text-danger"><i class="icon-spinner3 spinner"></i> BUSCANDO SOCIO</div></div></div>';
					var ventana_modal    = '<?= $ventana_modal ?>';
					var error_modal      = '<?= $error_modal ?>';
					var loader_modal     = '<?= $loader_modal ?>';
					var contenedor_modal = '<?= $contenedor_modal ?>';
					var dni              = $('#dni').val();
					var operacion        = 'resultados_busqueda_dni';
					var datos            = 'ventana_modal='+ventana_modal+'&error_modal='+error_modal+'&loader_modal='+loader_modal+'&contenedor_modal='+contenedor_modal+'&dni='+dni+'&operacion='+operacion;
					var modulo           = '../formularios/modulo-socios.php';
					console.log(datos);
					$.ajax({
						url  : modulo,
						type : 'POST',
						data : datos,
						beforeSend: function(){
							$('#resultados').fadeIn().html(loader_circular);
						},
						success: function(response){ 
							$('#resultados').fadeIn().html(response);
						}
					});
				}
			});
		});

		///////////////////////////////////////////////////////////////////////////////////////////////
		/// VALILDAR FORMULARIO
		///////////////////////////////////////////////////////////////////////////////////////////////
		$("#form_busqueda_dni").validate({
			onkeyup    : false,
			onfocusout : false,
			rules: {
				dni  : { required: true, },
			},
			messages: {
				dni  : { required: 'Ingrese numero de DNI', },
			},
			highlight: function(element, errorClass, validClass){
				$( element ).addClass( "invalido" ).removeClass( "valido" );
				$(element.form).find("label[for=" + element.id + "]").addClass(errorClass);
			},
			unhighlight: function(element, errorClass){
				$( element ).addClass( "valido" ).removeClass( "invalido" );
			},
			invalidHandler: function(form, validator) {
				var errors = validator.numberOfInvalids();
				if(errors){
					validator.errorList[0].element.focus();
				}
			}
		});
	</script>
<?php } ?>

<?php if($operacion=='resultados_busqueda_dni'){ ?>
	<?php
		$ventana_modal    = $_POST['ventana_modal'];
		$error_modal      = $_POST['error_modal'];
		$loader_modal     = $_POST['loader_modal'];
		$contenedor_modal = $_POST['contenedor_modal'];
		$dni              = $_POST['dni'];
		$conexion         = conexionDB();

		$sql="SELECT codigoSocio, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, fotoSocio, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular, observaciones, sincronizado, fecha, hora FROM sm_socios WHERE dni = '$dni'";
		$row=mysqli_query($conexion,$sql);
		$contar=mysqli_num_rows($row);

		$i=1;
		if($contar>0){
			$resultado='<hr><div class="form-group"><div class="row"><div class="col-sm-12"><div class="table-responsive"><table class="table tabla table-bordered table-hover"><thead><tr class="success"><th class="text-left">CODIGO SOCIO</th><th class="text-center">LOTES</th><th class="text-left">NOMBRE DE SOCIO</th><th class="text-left">CELULAR</th><th class="text-center"><i class="fa fa-align-justify"></i></th></tr></thead><tbody>';
			while($dato=mysqli_fetch_array($row)){
				$codigoSocio     =$dato[codigoSocio];
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

				$query="SELECT COUNT(lotes) AS cantidadLotes FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
				$rs=mysqli_query($conexion,$query);
				$n=mysqli_fetch_array($rs);
				$cantidadLotes=$n['cantidadLotes'];

				if($_SESSION['rol_apv']!='ADM'){
					$desactiva='disabled';
				}else{
					$desactiva='';
				}

				$resultado.='<tr><td class="text-left">'.$codigoSocio.'</td><td class="text-center"><span class="label label-warning textoNegrita">'.ceros($cantidadLotes,2).' Lotes</span></td><td class="text-left">'.($nombre.' '.$apPaterno.' '.$apMaterno).'</td><td class="text-left">'.$celular.'</td><td class="text-center"><a href="detalles-socio.php?codigoSocio='.$codigoSocio.'&opcion=detalles" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Perfil socio"><i class="icon-user-check"></i></a> <button type="button" id="'.$codigoSocio.'" class="btn btn-xs btn-icon bg-danger-300 btn_eliminaSocio '.$desactiva.'"><i class="icon-trash"></i></button></td></tr>';

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
			$resultado='<hr><div class="row"><div class="col-sm-12"><div class="alert alert-danger no-border text-center">La busqueda no tubo resultados, intentelo nuevamente...</div></div></div>';
		}

		echo $resultado;
		cerrarDB();
	?>
<?php } ?>

