<?php
	$conexion      =conexionDB();
	$tipoActividad ="CUO";

	if($tipoActividad=="CUO"){
		$titulotabla   ="LISTA DE CUOTAS";
		$titulolista   ="DETALLE DE CUOTA";
		$descActividad ="cuota";
		$rotActMay     ="CUOTA";
		$cantidadCUO   =infoCuota($idJuntaDirectiva,$codigoCuota,'cantidadCuotas');
		$mensaje       =cajaAlerta('SIN CUOTAS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN CONCEPTO DE CUOTA...','text-center','bg-warning-300');;
	}

	if($cantidadCUO>0){
		$totalGlobalCUO     =infoCuota($idJuntaDirectiva,'','totalGlobalCUO');
		$totalGlobalCUO_SP  =infoCuota($idJuntaDirectiva,'','totalGlobalCUO_SP');
		$totalGlobalCUO_FP  =infoCuota($idJuntaDirectiva,'','totalGlobalCUO_FP');
		$totalGlobalCUO_PGD =$totalGlobalCUO_SP+$totalGlobalCUO_FP;
		$totalGlobalCUO_DEU =$totalGlobalCUO-$totalGlobalCUO_PGD;

		if($totalGlobalCUO>0){ $infoTotalGlobalCUO='S/. '.moneda($totalGlobalCUO); }else{ $infoTotalGlobalCUO='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalGlobalCUO_PGD>0){ $infoTotalGlobalCUO_PGD='- S/. '.moneda($totalGlobalCUO_PGD); }else{ $infoTotalGlobalCUO_PGD='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalGlobalCUO_DEU>0){ $infoTotalGlobalCUO_DEU='S/. '.moneda($totalGlobalCUO_DEU); }else{ $infoTotalGlobalCUO_DEU='<i class="fa fa-ellipsis-h"></i>'; }
?>
	<div class="panel">
		<div class="panel-heading bg-slate-600">
			<h6 class="panel-title textoNegrita"><?= $titulotabla ?></h6>
			<div class="heading-elements">
				<a href="../documentos/conceptos-pago.php?concepto=<?= $tipoActividad ?>" class="btn btn-success heading-btn btn-labeled text-bold"><b><i class="icon-printer2"></i></b> IMPRIMIR</a>
			</div>
		</div>
		<div class="totalMonto">
			<div class="row">
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalGlobalCUO ?></span></li>
					</ul>
				</div>
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item"><span>CAJA</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalGlobalCUO_PGD ?></span></li>
					</ul>
				</div>
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item"><span>DEBEN</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalGlobalCUO_DEU ?></span></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="panel-body info">
			<table class="table tabla table-bordered table-hover lista-actividad">
				<thead>
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-left"><?= $titulolista ?></th>
						<th class="text-left">FECHA</th>
						<th class="text-center">TOTAL</th>
						<th class="text-center">CAJA</th>
						<th class="text-center">DEBEN</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT codigoCuota, conceptoCuota, montoCuota, fechaPago FROM sm_mod_cuotas ORDER BY id DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoCuota   =$n[codigoCuota];
							$temaActividad =$n[conceptoCuota];
							$fechaPago     =$n[fechaPago];
							$aforo         =infoCuota($idJuntaDirectiva,$codigoCuota,'aforo');
							$pagaron       =infoCuota($idJuntaDirectiva,$codigoCuota,'pagaron');
							$pagaronFP      =infoCuota($idJuntaDirectiva,$codigoCuota,'totalFechasProgramadas');
							$programados   =infoCuota($idJuntaDirectiva,$codigoCuota,'fechasProgramadas');
							$deben         =infoCuota($idJuntaDirectiva,$codigoCuota,'deben');
							$totalCuotas   =infoCuota($idJuntaDirectiva,$codigoCuota,'totalCuotas');
							$totalPagados  =infoCuota($idJuntaDirectiva,$codigoCuota,'totalPagados');
							$totalDeben    =infoCuota($idJuntaDirectiva,$codigoCuota,'totalDeben');
							$totalPorpagar =$totalCuotas-($totalPagados+$pagaronFP);

							if($totalCuotas>0){ $infoTotalCuotas='<span class="label bg-blue-800">S/.'.moneda($totalCuotas).'</span>'; $DE_infoTotalCuotas='S/.'.moneda($totalCuotas); }else{ $infoTotalCuotas='<i class="fa fa-ellipsis-h"></i>'; $DE_infoTotalCuotas='<i class="fa fa-ellipsis-h"></i>'; }
							if($totalPagados>0){ $infoTotalPagados='<span class="label bg-success">S/.'.moneda($totalPagados+$pagaronFP).'</span>'; $DE_infoTotalPagados='S/.'.moneda($totalPagados); }else{ $infoTotalPagados='<i class="fa fa-ellipsis-h"></i>'; $DE_infoTotalPagados='<i class="fa fa-ellipsis-h"></i>'; }
							if($totalPorpagar>0){ $infoTotalPorpagar='<span class="label bg-danger">S/.'.moneda($totalPorpagar).'</span>'; $DE_infoTotalPorpagar='S/.'.moneda($totalPorpagar); }else{ $infoTotalPorpagar='<i class="fa fa-ellipsis-h"></i>'; $DE_infoTotalPorpagar='<i class="fa fa-ellipsis-h"></i>'; }

							if($aforo>0){ $infoAforo=$aforo; }else{ $infoAforo='<i class="fa fa-ellipsis-h"></i>'; }
							if($pagaron>0){ $infoNroSociosPagaron=$pagaron; } else{ $infoNroSociosPagaron='<i class="fa fa-ellipsis-h"></i>'; }
							if($programados>0){ $infoNroSociosFechasProgramas=$programados; } else{ $infoNroSociosFechasProgramas='<i class="fa fa-ellipsis-h"></i>'; }
							if($deben>0){ $infoNroSociosDeben=$deben; } else{ $infoNroSociosDeben='<i class="fa fa-ellipsis-h"></i>'; }
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-left"><?= texto($temaActividad) ?></td>
						<td class="text-left textoMayuscula"><?= infoFecha($fechaPago,'normal') ?></td>
						<td class="text-right"><?= $infoTotalCuotas ?></td>
						<td class="text-right"><?= $infoTotalPagados ?></td>
						<td class="text-right"><?= $infoTotalPorpagar ?></td>
						<td class="text-center"><button type="button" data-target="#informe_economico_<?= $codigoCuota ?>" data-toggle="modal" class="btn btn-xs btn-default btn-icon textoMayuscula"><i class="icon-info22 text-brown"></i></button></td>
					</tr>
					<!-- MODAL INFORME ECONOMICO -->
					<div id="informe_economico_<?= $codigoCuota ?>" class="modal fade">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header bg-brown">
									<h5 class="modal-title">REPORTE ECONOMICO DE <?= $rotActMay ?></h5>
									<div class="heading-elements">
										<ul class="icons-list">
											<li><a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=detalles&conceptoCuota=<?= texto($temaActividad) ?>"><i class="icon-link"></i></a></li>
										</ul>
									</div>
								</div>
								<div class="modal-body">
									<div class="row mb-20">
										<div class="col-sm-12">
											<ul class="list-group">
												<li class="list-group-item"><?= texto($temaActividad) ?></li>
											</ul>
										</div>
										<div class="col-sm-4">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">TOTAL EN CUOTA</span> <span class="pull-right text-warning"><?= $DE_infoTotalCuotas ?></span></li>
											</ul>
										</div>
										<div class="col-sm-4">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">EN CAJA</span> <span class="pull-right text-warning"><?= $DE_infoTotalPagados ?></span></li>
											</ul>
										</div>
										<div class="col-sm-4">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">POR PAGAR</span> <span class="pull-right text-warning"><?= $DE_infoTotalPorpagar ?></span></li>
											</ul>
										</div>
										<div class="col-sm-12">
											<ul class="list-group bg-warning-300">
												<li class="list-group-item text-center textoNegrita">ESTADISTICAS DE SOCIOS</li>
											</ul>
										</div>
										<div class="col-sm-3">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">CUOTA</span> <span class="pull-right text-warning"><?= $infoAforo ?> <small>Socios</small></span></li>
											</ul>
										</div>
										<div class="col-sm-3">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">PAGARON</span> <span class="pull-right text-warning"><?= $infoNroSociosPagaron ?> <small>Socios</small></span></li>
											</ul>
										</div>
										<div class="col-sm-3">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">EN PROCESO</span> <span class="pull-right text-warning"><?= $infoNroSociosFechasProgramas ?> <small>Socios</small></span></li>
											</ul>
										</div>
										<div class="col-sm-3">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">DEBEN</span> <span class="pull-right text-warning"><?= $infoNroSociosDeben ?> <small>Socios</small></span></li>
											</ul>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php $i++; } cerrarDB() ?>
				</tbody>
			</table>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CONFIGURACION DE TABLAS
			///////////////////////////////////////////////////
			$.extend( $.fn.dataTable.defaults, { autoWidth: true, dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Filtro:</span> _INPUT_', lengthMenu: '<span>Mostrar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' } }, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function(){ $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
			$('.lista-actividad').DataTable();
			$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
			$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
		});
	</script>
<?php }else{ echo $mensaje; } ?>