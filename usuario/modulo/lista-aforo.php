<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$conexion=conexionDB();
	$codigoActividad=$_GET[codigoActividad];
	$tipoActividad=$_GET[tipoActividad];
	if($tipoActividad=="ASA"){ $titulo="ASAMBLEA"; }
	if($tipoActividad=="FAE"){ $titulo="FAENA"; }
?>
<script type="text/javascript">
	$(document).ready(function(){
		
		///////////////////////////////////////////////////
		/// ACCIONES DE BOTONES
		///////////////////////////////////////////////////
		$("button.bt_salvarJustificacion").click(function(){
			var ruta            ='../';
			var codigoActividad ='<?= $codigoActividad ?>';
			var codigoSocio     =$(this).attr('id');
			var justificacion   =$('textarea#detalleJustifica-'+codigoSocio).val();
			var tipoActividad   ='<?= $tipoActividad ?>';
			var operacion       ='REGISTRA_JUSTIFICACION';
			var datos           ='codigoSocio='+codigoSocio+'&justificacion='+justificacion+'&codigoActividad='+codigoActividad+'&tipoActividad='+tipoActividad+'&operacion='+operacion;
			if(justificacion!=''){
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-actividades.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){						
						$('button.bt_salvarJustificacion').prop('disabled', true);
					},
					success: function(respuesta){
						if(respuesta.mensaje=="JUSTIFICACION_ALMACENADA"){
							new PNotify({title: 'CONFIRMACION', text: 'Justificacion de ingresada.', addclass: 'bg-success'});
							$("#justifica-"+codigoSocio).modal('hide');

							if(tipoActividad=="ASA"){
								window.location.replace("detalle-asamblea.php?codigoActividad="+codigoActividad+'&tipoActividad='+tipoActividad+'&opcion=detalles');
							}
							if(tipoActividad=="FAE"){
								window.location.replace("detalle-faena.php?codigoActividad="+codigoActividad+'&tipoActividad='+tipoActividad+'&opcion=detalles');
							}
						}
					}
				});
			}else{
				$('textarea#detalleJustifica-'+codigoSocio).focus();
				new PNotify({ text: 'Ingrese justificación...', addclass: 'bg-danger alert-styled-right', type: 'error' });
			}
		});
		
		///////////////////////////////////////////////////
		/// CONFIGURACION DE TABLAS
		///////////////////////////////////////////////////
		$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 6 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
		var lastIdx = null;
		var table = $('.tabla-aforo').DataTable({ 'pageLength': 20 });
		$('.tabla-aforo tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
		$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
		$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

		///////////////////////////////////////////////////
		/// TOOLTIP
		///////////////////////////////////////////////////
		$('[data-popup="tooltip"]').tooltip();
	});
</script>

<div class="panel">
	<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">LISTA DE SOCIOS A <?= $titulo ?></h6></div>
	<div class="panel-body">
		<div class="table-responsive">
			<table class="table tabla table-bordered table-hover tabla-aforo">
				<thead>
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-center">CODIGO SOCIO</th>
						<th class="text-left">NOMBRE DE SOCIO</th>
						<th class="text-center">DNI</th>
						<th class="text-center">LOTES</th>
						<th class="text-center">ESTADO</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT codigoSocio, asistio, lotes, observacion FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' ORDER BY id DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoSocio =$n[codigoSocio];
							$asistio     =$n[asistio];
							$dni         =infoSocios($codigoSocio,'dni');
							$nombre      =infoSocios($codigoSocio,'nombre');
							$lotes       =$n[lotes];
							$observacion =$n[observacion];

							if($asistio=="IN"){ $estado='<span class="label label-info">INVITADO</span>'; }
							if($asistio=="SI"){ $estado='<span class="label label-success">ASISTIO</span>'; }
							if($asistio=="NO"){ $estado='<span class="label label-danger">FALTO</span>'; }
							if($asistio=="JU"){ $estado='<span class="label bg-brown">JUSTIFICO</span>'; }
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-center"><?= $codigoSocio ?></td>
						<td class="text-left"><?= utf8_encode($nombre) ?></td>
						<td class="text-center"><?= $dni ?></td>
						<td class="text-center"><span class="label label-success"><?= ceros($lotes,2) ?> LOTES</span></td>
						<td class="text-center"><?= $estado ?></td>
						<td class="text-center">
							<a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=detalles" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Perfil Socio"><i class="icon-user-check"></i></a>
							<a href="#justifica-<?= $codigoSocio ?>" data-toggle="modal" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Justificacion"><i class="fa fa-file-text"></i></a>
						</td>
					</tr>
					<!-- MODAL JUSTICAR ASISTENCIA A REUNION -->
					<div id="justifica-<?= $codigoSocio ?>" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content modal-lg">
								<div class="modal-header bg-slate-600">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title textoNegrita">JUSTIFICAR INASISTENCIA DE SOCIO &rarr; <?= utf8_encode($nombre) ?></h6>
								</div>
								<div class="modal-body">
									<form>
										<div class="form-group">
											<div class="row">
												<div class="col-sm-12">
													<textarea rows="10" cols="5" id="detalleJustifica-<?= $codigoSocio ?>" class="form-control textoMayuscula" placeholder="Detalles de justicacion..." onblur="mayusculas(event, this)" autofocus><?php if($observacion){ echo utf8_encode($observacion); } ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="row">
												<div class="col-sm-12 text-center">
													<button type="button" class="btn btn-lg btn-warning" data-dismiss="modal"><i class="icon-close2"></i> Cerrar</button>
													<button type="button"  id="<?= $codigoSocio ?>" class="btn btn-lg btn-success bt_salvarJustificacion"><i class=" icon-floppy-disk"></i> Grabar</button>
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