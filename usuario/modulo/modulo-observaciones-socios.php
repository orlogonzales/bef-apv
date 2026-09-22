<?php
	$ruta             ='../../';
	include_once $ruta."php/funciones.php";
	$conexion         =conexionDB();
	$opcion           =$_GET[opcion];
	$codigoSocio      =$_GET[codigoSocio];
	$dni              =infoSocios($codigoSocio,'dni');
	$nombreSocio      =infoSocios($codigoSocio,'nombre');
	$nroObservaciones =infoSocios($codigoSocio,'observaciones');
	$mensaje=cajaAlerta('SIN OBSERVACIONES','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUNA OBSERVACION PARA '.$nombreSocio.'.','text-center','bg-warning-300');
?>

<?php if($opcion=="agrega_observacion"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// AGREGAR OBSERVACIONES
			///////////////////////////////////////////////////
			$("button#bt_salvar_observacion").click(function(){
				var ruta        ="../";
				var codigoSocio ="<?= $codigoSocio ?>";
				var observacion =$("textarea#observacion").val();
				var operacion   ="AGREGA_OBSERVACION";
				var datos       ="codigoSocio="+codigoSocio+"&observacion="+observacion+"&operacion="+operacion;
				if($.trim(observacion).length>0){
					$.ajax({
						type: 'POST',
						url: ruta+'php/mantenimiento-socios.php',
						data: datos,
						dataType:'json',
						success:function(respuesta){
							if(respuesta.mensaje=="OBSERVACION_AGREGADA"){
								var ruta   ='../';
								var opcion ='agrega_observacion';
								var datos  ='opcion='+opcion+'&codigoSocio='+codigoSocio;
								$('#agregar'+codigoSocio).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
								$("#agregar"+codigoSocio).fadeIn("slow").load('modulo/modulo-observaciones-socios.php?'+datos).fadeIn(1000).delay(1000);
								$("textarea#observacion").focus();
								new PNotify({title: 'ALMACENADO', text: 'Observación agregada a socio.', addclass: 'bg-success'});
							}
							if(respuesta.mensaje=="ERROR_OBSERVACION_AGREGADA"){
								new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
							}
						}
					});
				}else{
					$("textarea#observacion").focus();
					new PNotify({title: 'ERROR', text: 'Por favor ingrese observación.', addclass: 'bg-warning'});
				}
			});
		});
	</script>
	
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
<?php } ?>


<?php if($opcion=="lista_observaciones"){ ?>
	<?php if($nroObservaciones>0){ ?>
		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CONFIGURACION DE TABLAS
				///////////////////////////////////////////////////
				$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{  orderable: false, width: '100px', targets: [ 3 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Filtro:</span> _INPUT_', lengthMenu: '<span>Mostrar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' } }, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function(){ $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
				$('.lista-observaciones').DataTable();
				$('.dataTables_filter input[type=search]').attr('placeholder','Buscar observación...');
				$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

				///////////////////////////////////////////////////
				/// ELIMINA OBSERVACION
				///////////////////////////////////////////////////
				$('.bt_elimina_observacion').on('click', function() {
					var ruta        ='../';
					var observacion = $(this).attr('id');
					var codigoSocio ='<?= $codigoSocio ?>';
					var operacion   ='ELIMINAR_OBSERVACION';
					var datos       ='observacion='+observacion+'&codigoSocio='+codigoSocio+'&operacion='+operacion;
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
							swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="OBSERVACION_ELIMINADA"){
										new PNotify({title: 'ELIMINADO', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
										var ruta   ='../';
										var opcion ='lista_observaciones';
										var datos  ='opcion='+opcion+'&codigoSocio='+codigoSocio;
										$('#listaobservaciones'+codigoSocio).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
										$("#listaobservaciones"+codigoSocio).fadeIn("slow").load('modulo/modulo-observaciones-socios.php?'+datos).fadeIn(1000).delay(1000);
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

		<div class="table-responsive">
			<table class="table tabla-info table-bordered table-hover lista-observaciones">
				<thead>
					<tr class="success">
						<th class="textoNegrita text-center">#</th>
						<th class="textoNegrita text-left">OBSERVACION</th>
						<th class="textoNegrita text-center">REGISTRADO</th>
						<th class="textoNegrita text-center"><i class="icon-menu7"><i/></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT id, observacion, fecha, hora, usuario FROM sm_socios_observacion WHERE codigoSocio='$codigoSocio' ORDER BY id DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$id          =$n[id];
							$observacion =$n[observacion];
							$fecha       =$n[fecha];
							$hora        =$n[hora];
							$usuario     =$n[usuario];
							$infoRegistro=registradoPor($usuario,$fecha,$hora,'NO','');
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-left"><?= texto($observacion) ?></td>
						<td class="text-center"><span class="label label-default"><?= $infoRegistro.' | '.horaCorta($hora) ?></span></td>
						<td class="text-center">
							<button type="button" id="<?= $id ?>" class="btn btn-xs btn-warning btn-icon btn-rounded bt_elimina_observacion"><i class="icon-trash"></i></button>
						</td>
					</tr>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>
	<?php }else{ echo $mensaje; } ?>
<?php } ?>