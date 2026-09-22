<?php if($totalCLS>0){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CONFIGURACION DE TABLAS
			///////////////////////////////////////////////////
			$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
			var lastIdx = null;
			var table = $('.tabla-caja-ing').DataTable({ 'pageLength': 20 });
			$('.tabla-caja-ing tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
			$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
			$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
		});
	</script>

	<div class="panel">
		<div class="panel-heading bg-slate-600"><h6 class="panel-title">INGRESOS A CAJA POR CONCEPTO DE PARTIDAS</h6></div>
		<div class="totalMonto">
			<div class="row">
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL PARTIDAS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalPAR ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL DEVOLUCION A CAJA</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalCLS ?></span></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table tablaT table-bordered table-hover tabla-caja-ing">
					<thead>
						<tr class="success">
							<th class="textoNegrita text-center">#</th>
							<th class="textoNegrita text-center">FECHA OP</th>
							<th class="textoNegrita text-left">PARTIDA</th>
							<th class="textoNegrita text-left">DETALLE</th>
							<th class="textoNegrita text-left">RESPONSABLE</th>
							<th class="textoNegrita text-center">ASIGNADO</th>
							<th class="textoNegrita text-center">GASTADO</th>
							<th class="textoNegrita text-center">DEVUELTO</th>
							<th class="textoNegrita text-center">USUARIO</th>
							<th class="text-center"><i class="fa fa-align-justify"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE movimiento='ING' AND concepto='PAR' AND (fechaOperacion BETWEEN '$consultaFechaIni' AND '$consultaFechaFin') $consultaUsuario ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$fechaOperacion  =$n[fechaOperacion];
								$tipoActividad   =$n[tipoActividad];
								$concepto        =$n[concepto];
								$codigoSocio     =$n[codigoSocio];
								$codigoConcepto  =$n[codigoConcepto];
								$tipoDocumento   =$n[tipoDocumento];
								$nroDocumento    =$n[nroDocumento];
								$monto           =$n[monto];
								$detalleConcepto =$n[detalleConcepto];
								$codigoOperacion =$n[codigoOperacion];
								$fecha           =$n[fecha];
								$hora            =$n[hora];
								$usuario         =$n[usuario];

								$infofecha=infoFecha($fecha,'normal');
								$nombreResponsable=texto(datoUsuario($codigoSocio,'nombrePaterno'));
								$infoMonto='<span class="text-danger textoNegrita">S/. '.moneda($monto).'</span>';
								$montoPartida='<span class="text-danger textoNegrita">S/. '.infoPartida($codigoConcepto,'montoPartida').'</span>';
								$montoGastado='<span class="text-danger textoNegrita">S/. '.infoPartida($codigoConcepto,'montoGastado').'</span>';
								

								$conceptoPartida='<span class="label bg-brown">'.texto(infoPartida($codigoConcepto,'conceptoPartida')).'</span>';
								$infoUsuario='<span class="label label-default">'.datoUsuario($usuario,'nombre').'</span>';
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-center textoMayuscula"><?= $infofecha ?></td>
							<td class="text-left"><?= $conceptoPartida ?></td>
							<td class="text-left"><?= texto($detalleConcepto) ?></td>
							<td class="text-left"><?= $nombreResponsable ?></td>
							<td class="text-right"><?= $montoPartida ?></span></td>
							<td class="text-right"><?= $montoGastado ?></span></td>
							<td class="text-right"><?= $infoMonto ?></span></td>
							<td class="text-center"><?= $infoUsuario ?></td>
							<td class="text-center"><a href="#infoItem<?= $codigoOperacion ?>" data-toggle="modal" class="btn btn-xs bg-grey" data-popup="tooltip" data-placement="left" title="Informacion de item"><i class="icon-info22"></i></a></td>
							<!-- MODAL PAGO DE MULTA -->
							<div id="infoItem<?= $codigoOperacion ?>" class="modal fade">
								<div class="modal-dialog modal-lg">
									<div class="modal-content">
										<div class="modal-header bg-brown">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h6 class="modal-title textoNegrita">INFORMACION DE ITEM</h6>
										</div>
										<div class="modal-body">
											<div class="row mb-20">
												<div class="col-sm-12">
													<ul class="list-group">
														<li class="list-group-item"><span class="textoNegrita">PARTIDA</span> <span class="pull-right text-danger"><?= $conceptoPartida ?></span></li>
													</ul>
												</div>
												<div class="col-sm-12">
													<ul class="list-group">
														<li class="list-group-item"><span class="textoNegrita">DETALLE DE PAGO</span> <span class="pull-right text-danger"><?= texto($detalleConcepto) ?></span></li>
													</ul>
												</div>
												<div class="col-sm-6">
													<ul class="list-group">
														<li class="list-group-item"><span class="textoNegrita">MONTO INGRESO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($monto) ?></span></li>
														<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= texto(infoPago('','',$concepto,'conceptoPago')) ?></span></li>
														<li class="list-group-item"><span class="textoNegrita">COBRADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
													</ul>
												</div>
												<div class="col-sm-6">
													<ul class="list-group">
														<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
														<li class="list-group-item"><span class="textoNegrita">CODIGO OPERACION</span> <span class="pull-right text-danger"><?= $codigoOperacion ?></span></li>
														<li class="list-group-item"><span class="textoNegrita">FECHA DE COBRO</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fecha,'larga').' - '.horaCorta($hora) ?></span></li>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php
	}else{
		echo cajaAlerta('SIN DATOS','text-center textoNegrita','No se ha encontrado, ningún registro en el sistema con los datos de consulta ingresados.','text-center','bg-teal-300');
	}
?>
