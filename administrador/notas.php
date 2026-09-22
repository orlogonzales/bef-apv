<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES
	/////////////////////////////////////////////////////////////////////
	$ruta        ='../';

	/////////////////////////////////////////////////////////////////////
	/// MENU HEADER PAGINA
	/////////////////////////////////////////////////////////////////////
	$menuTop="menu-notas.php";
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="NOTAS U OBSERVACIONES";
	$menuActual="modNotas";
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	include($ruta.'template/header.tpl');
?>
	<?php
		$sql="SELECT id, observacion, fecha, hora, usuario FROM sm_notas ORDER BY id DESC";
		$rs=mysqli_query($conexion,$sql);
		$contar=mysqli_num_rows($rs);
		if($contar>0){
	?>
		<div class="panel">
			<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">NOTAS</h6></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tabla-info table-bordered table-hover tabla-lista-notas">
						<thead>
							<tr class="success">
								<th class="textoNegrita text-center">#</th>
								<th class="textoNegrita text-left">OBSERVACION</th>
								<th class="textoNegrita text-left">CATEGORIA</th>
								<th class="textoNegrita text-center">REGISTRADO</th>
								<th class="textoNegrita text-center"><i class="icon-menu7"><i/></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="SELECT id, idCategoria, observacion, fecha, hora, usuario FROM sm_notas ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$id          =$n[id];
									$categoria   =infoNotas($n[idCategoria]);
									$observacion =$n[observacion];
									$fecha       =$n[fecha];
									$hora        =$n[hora];
									$usuario     =$n[usuario];
									$infoRegistro=registradoPor($usuario,$fecha,$hora,'NO','');
							?>
							<tr>
								<td class="text-center"><?= ceros($i,2) ?></td>
								<td class="text-left"><?= texto($observacion) ?></td>
								<td class="text-left"><?= $categoria ?></td>
								<td class="text-center"><span class="label label-default"><?= $infoRegistro.' | '.horaCorta($hora) ?></span></td>
								<td class="text-center">
									<button type="button" id="<?= $id ?>" data-toggle="modal" data-target="#editaNota-<?= $id ?>" class="btn btn-xs btn-success btn-icon btn-rounded bt_edita_observacion"><i class="icon-pencil"></i></button>
									<button type="button" id="<?= $id ?>" class="btn btn-xs btn-warning btn-icon btn-rounded bt_elimina_nota"><i class="icon-trash"></i></button>
								</td>
							</tr>
							<!-- MODAL EDITA OBSERVACIONES -->
							<div id="editaNota-<?= $id ?>" class="modal fade">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header bg-slate-600">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h6 class="modal-title">EDITA OBSERVACION</h6>
										</div>
										<div class="modal-body">
											<form id="frm_observaciones" class="validar">
												<div class="form-group">
													<div class="row">
														<div class="col-sm-9">
															<div id="campoFoto">
																<textarea rows="10" cols="5" name="detalleObservacion-<?= $id ?>" id="detalleObservacion-<?= $id ?>" class="form-control textoMayuscula" placeholder="Ingrese texto de observacion..." onblur="mayusculas(event, this)" autofocus><?= $observacion ?></textarea>
															</div>
														</div>
														<div class="col-sm-3">
															<button type="button" id="<?= $id ?>" class="btn btn-success btn-block btn-icon bt_editar_observacion">Actualizar</button>
															<button type="button" class="btn btn-warning btn-block" data-dismiss="modal">Cerrar</button>
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// EDITA OBSERVACIONES
				///////////////////////////////////////////////////
				$("button.bt_editar_observacion").click(function(){
					var ruta          ="<?= $ruta ?>";
					var idObservacion =$(this).attr('id');
					var observacion   =$("textarea#detalleObservacion-"+idObservacion).val();
					var operacion     ="EDITA_NOTA";
					var datos         ='observacion='+observacion+'&id='+idObservacion+'&operacion='+operacion;
					if($.trim(observacion).length>0){
						$.ajax({
							type: 'POST',
							url: ruta+'php/mantenimiento-usuarios.php',
							data: datos,
							dataType:'json',
							success:function(respuesta){
								if(respuesta.mensaje=="OBSERVACION_MODIFICADA"){
									new PNotify({title: 'ALMACENADO', text: 'Observación modificada.', addclass: 'bg-success'});
									location.reload();
								}
								if(respuesta.mensaje=="ERROR_OBSERVACION_MODIFICADA"){
									new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
								}
							}
						});
					}else{
						$("textarea#observacion").focus();
						new PNotify({title: 'ERROR', text: 'Por favor ingrese observación.', addclass: 'bg-warning'});
					}
				});

				///////////////////////////////////////////////////
				/// ELIMINA OBSERVACION
				///////////////////////////////////////////////////
				$('.bt_elimina_nota').on('click', function() {
					var ruta        ='../';
					var observacion = $(this).attr('id');
					var operacion   ='ELIMINAR_NOTA';
					var datos       ='observacion='+observacion+'&operacion='+operacion;
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
								url: ruta+'php/mantenimiento-usuarios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="OBSERVACION_ELIMINADA"){
										new PNotify({title: 'ELIMINADO', text: 'El item seleccionado fue eliminadO del sistema.', addclass: 'bg-success'});
										location.reload();
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

			///////////////////////////////////////////////////
			/// CONFIGURACION DE TABLAS
			///////////////////////////////////////////////////
			$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [  ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
			var lastIdx = null;
			var table = $('.tabla-lista-notas').DataTable({ 'pageLength': 20 });
			$('.tabla-lista-notas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
			$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
			$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
		</script>
<?php 
	}else{
		echo cajaAlerta('SIN DATOS','text-center textoNegrita','No se ha encontrado, ningúna nota registrado en el sistema.','text-center','bg-danger');
	}
	include($ruta.'template/footer.tpl');
?>