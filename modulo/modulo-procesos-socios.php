<?php
	$ruta             ='../';
	include_once $ruta."php/funciones.php";
	include_once $ruta."php/conexion.php";
	$proceso          = $_POST[proceso];
	$codigoSocio      = $_POST[codigoSocio];
	$dni              = infoSocios($codigoSocio,'dni');
	$nombreSocio      = infoSocios($codigoSocio,'nombre');
	$nroObservaciones = infoSocios($codigoSocio,'observaciones');
	$mensaje          = cajaAlerta('SIN OBSERVACIONES','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUNA OBSERVACION PARA '.$nombreSocio.'.','text-center','bg-warning-300');
?>
<?php if($proceso=="AGREGA_NOTA"){ ?>
	<?php
		$dataTable     = $_POST[dataTable];
		$ventanaModal  = $_POST[ventanaModal];
	?>
	<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
		<div class="visible-lg visible-sm"><strong>SOCIO:</strong> <?= $nombreSocio ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
	</div>
	<form id="frm_observaciones" class="validar">
		<div class="form-group">
			<div class="row">
				<div class="col-sm-9">
					<div id="campoFoto">
						<textarea rows="10" cols="5" name="observacion" id="observacion" class="form-control textoMayuscula" placeholder="Ingrese texto de observacion..." onblur="mayusculas(event, this)" autofocus></textarea>
					</div>
				</div>
				<div class="col-sm-3">
					<button type="button" id="bt_salvar_observacion" class="btn btn-success btn-block btn-icon">Almacenar</button>
					<button type="button" class="btn btn-warning btn-block" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</form>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// AGREGAR OBSERVACIONES
			///////////////////////////////////////////////////
			$("button#bt_salvar_observacion").click(function(){
				var ruta           = "../";
				var codigoSocio    = "<?= $codigoSocio ?>";
				var observacion    = $("textarea#observacion").val();
				var operacion      = "AGREGA_OBSERVACION";
				var datos          = "codigoSocio="+codigoSocio+"&observacion="+observacion+"&operacion="+operacion;
				const ventanaModal = "<?= $ventanaModal ?>";
				const valida       = $('#frm_observaciones').valid();

				if(valida){
					$.ajax({
						type: 'POST',
						url: ruta+'./php/mantenimiento-socios.php',
						data: datos,
						dataType:'json',
						success:function(respuesta){
							if(respuesta.mensaje=="OBSERVACION_AGREGADA"){
								$(ventanaModal).modal('hide');
								new PNotify({title: 'ALMACENADO', text: 'Observación agregada a socio.', addclass: 'bg-success'});
							}
							if(respuesta.mensaje=="ERROR_OBSERVACION_AGREGADA"){
								new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO
			///////////////////////////////////////////////////
			var validator = $("#frm_observaciones").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
				validClass: "validation-valid-label",
				rules: {
					observacion:{ required: true, minlength: 15 },
				},
				messages: {
					observacion:{ required: "Ingrese obserción", minlength: "Debe ingresar texto de almenos {0} caracteres de longitud" },
				}				
			});
		});
	</script>
<?php } ?>

<?php if($proceso=="LISTA_NOTAS"){ ?>
	<?php if($nroObservaciones>0){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg visible-sm"><strong>SOCIO:</strong> <?= $nombreSocio ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>
		<div class="table-responsive">
			<table id="dtListaNotas" class="table tabla-info table-bordered table-hover">
				<thead>
					<tr class="success">
						<th class="textoNegrita text-center">#</th>
						<th class="textoNegrita text-left">OBSERVACION</th>
						<th class="textoNegrita text-center">REGISTRADO</th>
						<th class="textoNegrita text-center"><i class="icon-menu7"><i/></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				////////////////////////////////////////////////////////////
				/// DATA TABLE - LISTA
				////////////////////////////////////////////////////////////
				const urlModalDTable = '../modulo/dt-lista-notas-socios.php';
				const dataTable = $('#dtListaNotas').DataTable({
				    "paging": true,
				    "pageLength": 10,
				    "lengthChange": true,
				    "info": true,
				    "bPaginate": true,
				    "bSort": false,
				    "processing": true,
				    "language": {
				        "url": "../assets/js/plugins/tables/datatables/spanish.json"
				    },
				    "serverMethod": "POST",
				    "ajax": {
				        url: urlModalDTable,
				        type: 'POST',
				        data: function(d) {
				            d.codigoSocio = "<?php echo $codigoSocio ?>";
				        },
				        error: function(xhr, error, code) {
				            console.error('Error en la carga de datos:', xhr.responseText);
				        }
				    },
				    "columns": [
				        { data: 'Nro' },
				        { data: 'observacion' },
				        { data: 'infoRegistro' },
				        { data: 'botoneraOpciones' },
				    ],
				    "columnDefs": [
				        { "targets": 0, "className": "text-center" },
				        { "targets": 1, "className": "text-left" },
				        { "targets": 2, "className": "text-center" },
				        { "targets": 3, "className": "text-center" },
				    ]
				});


				////////////////////////////////////////////////////////////
				/// DATA TABLE - AGREGA OBSERVACIONES
				////////////////////////////////////////////////////////////
				$('#dtListaNotas').on('click','.btnEliminaNota',function(){
					var ruta          = '../';
					const observacion = $(this).data('id');
					const codigoSocio = $(this).data('socio');
					var operacion     = 'ELIMINAR_OBSERVACION';
					var datos         = 'observacion='+observacion+'&codigoSocio='+codigoSocio+'&operacion='+operacion;
					swal({
						title: "Eliminar",
						text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
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
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="OBSERVACION_ELIMINADA"){
										new PNotify({title: 'ELIMINADO', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
										dataTable.ajax.reload();
									}
									if(respuesta.mensaje=="ERROR_OBSERVACION_ELIMINADA"){
										new PNotify({title: 'ERROR', text: 'Por favor intentelo nuevamente.', addclass: 'bg-warning'});
									}
								}
							});
						}
					});
				});
			});
		</script>
	<?php }else{ echo $mensaje; } ?>
<?php } ?>