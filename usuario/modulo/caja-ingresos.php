<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// CONFIGURACION DE TABLAS
		///////////////////////////////////////////////////
		$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 8 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
		var lastIdx = null;
		var table = $('.tabla-caja-ing').DataTable({ 'pageLength': 20 });
		$('.tabla-caja-ing tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
		$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
		$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
	});
</script>

<?php if($totalING>0){ ?>
	<div class="panel">
		<div class="panel-heading bg-slate-600"><h6 class="panel-title">INGRESOS A CAJA</h6></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table tablaT table-bordered table-hover tabla-caja-ing">
					<thead>
						<tr class="success">
							<th class="textoNegrita text-center">#</th>
							<th class="textoNegrita text-center">FECHA OP</th>
							<th class="textoNegrita text-left">CTO</th>
							<th class="textoNegrita text-left">DETALLE</th>
							<th class="textoNegrita text-left">SOCIO</th>
							<th class="textoNegrita text-center">LT</th>
							<th class="textoNegrita text-center">MONTO</th>
							<th class="textoNegrita text-center">DOCUMENTO</th>
							<th class="textoNegrita text-center">USUARIO</th>
							<th class="text-center"><i class="fa fa-align-justify"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT fechaOperacion, tipoActividad, concepto, codigoSocio, codigoConcepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE movimiento='ING' AND concepto!='PAR' AND (fechaOperacion BETWEEN '$consultaFechaIni' AND '$consultaFechaFin') $consultaUsuario ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$fechaOperacion  =$n['fechaOperacion'];
								$tipoActividad   =$n['tipoActividad'];
								$concepto        =$n['concepto'];
								$codigoSocio     =$n['codigoSocio'];
								$codigoConcepto  =$n['codigoConcepto'];
								$tipoDocumento   =$n['tipoDocumento'];
								$nroDocumento    =$n['nroDocumento'];
								$monto           =$n['monto'];
								$detalleConcepto =$n['detalleConcepto'];
								$codigoOperacion =$n['codigoOperacion'];
								$fecha           =$n['fecha'];
								$hora            =$n['hora'];
								$usuario         =$n['usuario'];
								$infofecha       =infoFecha($fecha,'normal');
								$nombreSocio     =texto(infoSocios($codigoSocio,'nombreCorto'));
								$infoLotes       ='<span class="label label-default">'.ceros(infoSocios($codigoSocio,'cantidadLotes'),2).'</span>';
								$conceptopago    =infoPago('','',$concepto,'conceptoPago');
								$infoUsuario     ='<span class="label label-default">'.datoUsuario($usuario,'nombre').'</span>';
								$infoDocumento   =$tipoDocumento.'&nbsp;'.$nroDocumento;

								if($tipoActividad=="ASA"){ $infoConcepto=texto(infoActividad($idJuntaDirectiva,$codigoConcepto,'','temaActividad')); $rotuloActividad="ASAMBLEA"; }
								if($tipoActividad=="FAE"){ $infoConcepto=texto(infoActividad($idJuntaDirectiva,$codigoConcepto,'','temaActividad')); $rotuloActividad="FAENA"; }
								if($tipoActividad=="CUO"){ $infoConcepto=texto(infoCuota($idJuntaDirectiva,$codigoConcepto,'conceptoCuota')); $rotuloActividad="CUOTA"; }

								if($concepto=="PAR"){
									$conceptoPartida =texto(infoPartida($codigoConcepto,'conceptoPartida'));
									$infoConcepto    ='<span class="label label-success">'.$conceptoPartida.'</span> '.$detalleConcepto;
									$nombreSocio     =datoUsuario($codigoSocio,'nombreCorto');
									$infoLotes       ="";
									$rotuloActividad ="CONCEPTO DE PARTIDAS";
								}
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-center textoMayuscula"><?= $infofecha ?></td>
							<td class="text-left"><?= $tipoActividad ?></td>
							<td class="text-left"><?= texto($detalleConcepto) ?></td>
							<td class="text-left"><?= $nombreSocio ?></td>
							<td class="text-center"><?= $infoLotes ?></td>
							<td class="text-right text-danger textoNegrita"><?= 'S/.&nbsp;'.moneda($monto) ?></span></td>
							<td class="text-left"><?= $infoDocumento ?></td>
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
														<li class="list-group-item"><span class="textoNegrita"><?= $rotuloActividad ?></span> <span class="pull-right text-danger"><?= $infoConcepto ?></span></li>
													</ul>
												</div>
												<div class="col-sm-12">
													<ul class="list-group">
														<li class="list-group-item"><span class="textoNegrita">DETALLE DE PAGO</span> <span class="pull-right text-danger"><?= texto($detalleConcepto) ?></span></li>
													</ul>
												</div>
												<div class="col-sm-6">
													<ul class="list-group">
														<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($monto) ?></span></li>
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