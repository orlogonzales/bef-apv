<?php
	$conexion      =conexionDB();
	$tipoActividad ="FAE";

	if($tipoActividad=="FAE"){
		$titulotabla    ="LISTA DE FAENAS";
		$titulolista    ="DETALLE DE FAENA";
		$descActividad  ="faena";
		$rotActMay      ="FAENA";
		$cantidadASAFAE =infoActividad($idJuntaDirectiva,$codigoActividad,'','cantidadFAE');
		$mensaje        =cajaAlerta('SIN FAENAS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN CONCEPTO DE FAENA...','text-center','bg-warning-300');
	}
	
	if($cantidadASAFAE>0){
		$totalGlobalFAE=infoActividad($idJuntaDirectiva,'','','totalGlobalFAE');
		$totalGlobalFAE_JUS=infoActividad($idJuntaDirectiva,'','','totalGlobalFAE_JUS');
		$totalGlobalFAE_SP=infoActividad($idJuntaDirectiva,'','','totalGlobalFAE_SP');
		$totalGlobalFAE_FP=infoActividad($idJuntaDirectiva,'','','totalGlobalFAE_FP');
		$totalGlobalFAE_PGD=$totalGlobalFAE_SP+$totalGlobalFAE_FP;
		$totalGlobalFAE_DEU=$totalGlobalFAE-($totalGlobalFAE_PGD+$totalGlobalFAE_JUS);
		
		if($totalGlobalFAE>0){ $infoTotalGlobalFAE='S/. '.moneda($totalGlobalFAE); }else{ $infoTotalGlobalFAE='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalGlobalFAE_JUS>0){ $infoTotalGlobalFAE_JUS='- S/. '.moneda($totalGlobalFAE_JUS); }else{ $infoTotalGlobalFAE_JUS='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalGlobalFAE_PGD>0){ $infoTotalGlobalFAE_PGD='- S/. '.moneda($totalGlobalFAE_PGD); }else{ $infoTotalGlobalFAE_PGD='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalGlobalFAE_DEU>0){ $infoTotalGlobalFAE_DEU='S/. '.moneda($totalGlobalFAE_DEU); }else{ $infoTotalGlobalFAE_DEU='<i class="fa fa-ellipsis-h"></i>'; }
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
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalGlobalFAE ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>JUSTIFICADOS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalGlobalFAE_JUS ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>CAJA</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalGlobalFAE_PGD ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>DEBEN</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalGlobalFAE_DEU ?></span></li>
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
						$sql="SELECT tipoActividad, codigoActividad, temaActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta FROM sm_mod_actividades WHERE tipoActividad='$tipoActividad' ORDER BY id DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoActividad          =$n[codigoActividad];
							$tipoActividad            =$n[tipoActividad];
							$temaActividad            =$n[temaActividad]; 
							$fechaActividad           =$n[fechaActividad]; 
							$horaActividad            =$n[horaActividad]; 
							$lugarActividad           =$n[lugarActividad]; 
							$aforo                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','aforo');
							$asistio                  =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
							$tarde                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','tarde');
							$falto                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','falto');
							$justifico                =infoActividad($idJuntaDirectiva,$codigoActividad,'','justifico');
							$mTardanzas               =infoActividad($idJuntaDirectiva,$codigoActividad,'','mtarde');
							$mFaltas                  =infoActividad($idJuntaDirectiva,$codigoActividad,'','mFalta');
							$totalMultas              =$mTardanzas+$mFaltas;
							$multasPagadas            =infoActividad($idJuntaDirectiva,$codigoActividad,'','mPagadas');
							$porPagar                 =$totalMultas-$multasPagadas;
							$montoJustificado         =infoActividad($idJuntaDirectiva,$codigoActividad,'','mJUS');
							$totalNeto                =$montoJustificado+$totalMultas;
							$nroSociosPagaron         =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosPagaron');
							$nroSociosFechasProgramas =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosFechasProgramas');
							$nroSociosDeben           =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosDeben');

							if($mTardanzas>0){ $infoMTardanzas="S/. ".moneda($mTardanzas); }else{  $infoMTardanzas='<i class="fa fa-ellipsis-h"></i>'; }
							if($mFaltas>0){ $infoMFaltas="S/. ".moneda($mFaltas); }else{  $infoMFaltas='<i class="fa fa-ellipsis-h"></i>'; }
							
							if($totalMultas>0){
								$infoTotalMultas='<span class="label bg-blue-800">S/. '.moneda($totalMultas).'</span>';
								$RE_infoTotalMultas='S/. '.moneda($totalMultas);
							}else{
								$infoTotalMultas='<i class="fa fa-ellipsis-h"></i>';
								$RE_infoTotalMultas='<i class="fa fa-ellipsis-h"></i>';
							}
							
							if($multasPagadas>0){
								$infoMultasPagadas='<span class="label bg-success">S/. '.moneda($multasPagadas).'</span>';
								$RE_infoMultasPagadas='S/. '.moneda($multasPagadas);
							}else{
								$infoMultasPagadas='<i class="fa fa-ellipsis-h"></i>';
								$RE_infoMultasPagadas='<i class="fa fa-ellipsis-h"></i>';
							}
							
							if($porPagar>0){
								$infoPorPagar='<span class="label bg-danger">S/. '.moneda($porPagar).'</span>';
								$RE_infoPorPagar='S/. '.moneda($porPagar).'</span>';
							}else{
								$infoPorPagar='<i class="fa fa-ellipsis-h"></i>';
								$RE_infoPorPagar='<i class="fa fa-ellipsis-h"></i>';
							}

							if($montoJustificado>0){ $infoMontoJustificado="- S/. ".moneda($montoJustificado); }else{  $infoMontoJustificado='<i class="fa fa-ellipsis-h"></i>'; }
							if($totalNeto>0){ $infoTotalNeto="S/. ".moneda($totalNeto); }else{  $infoTotalNeto='<i class="fa fa-ellipsis-h"></i>'; }

							if($nroSociosPagaron>0){ $infoNroSociosPagaron=$nroSociosPagaron; }else{ $infoNroSociosPagaron='<i class="fa fa-ellipsis-h"></i>'; }
							if($nroSociosFechasProgramas>0){ $infoNroSociosFechasProgramas=$nroSociosFechasProgramas; }else{ $infoNroSociosFechasProgramas='<i class="fa fa-ellipsis-h"></i>'; }
							if($nroSociosDeben>0){ $infoNroSociosDeben=$nroSociosDeben; }else{ $infoNroSociosDeben='<i class="fa fa-ellipsis-h"></i>'; }
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-left"><?= texto($temaActividad) ?></td>
						<td class="text-left textoMayuscula"><?= infoFecha($fechaActividad,'normal') ?></td>
						<td class="text-right"><?= $infoTotalMultas ?></td>
						<td class="text-right"><?= $infoMultasPagadas ?></td>
						<td class="text-right"><?= $infoPorPagar ?></td>
						<td class="text-center"><button type="button" data-target="#informe_economico_<?= $codigoActividad ?>" data-toggle="modal" class="btn btn-xs btn-default btn-icon textoMayuscula"><i class="icon-info22 text-brown"></i></button></td>
					</tr>
					<!-- MODAL INFORME ECONOMICO -->
					<div id="informe_economico_<?= $codigoActividad ?>" class="modal fade">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header bg-brown">
									<h5 class="modal-title">REPORTE ECONOMICO DE <?= $rotActMay ?></h5>
									<div class="heading-elements">
										<ul class="icons-list">
											<li><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=detalles&temaActividad=<?= $temaActividad ?>"><i class="icon-link"></i></a></li>
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
										<div class="col-sm-6">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">AFORO</span> <span class="pull-right text-warning"><?= $aforo ?> <small>Socios</small></span></li>
												<li class="list-group-item"><span class="textoNegrita">ASISTENTES</span> <span class="pull-right text-warning"><?= $asistio ?> <small>Socios</small></span></li>
												<li class="list-group-item"><span class="textoNegrita">TARDANZAS</span> <span class="pull-right text-warning"><?= $tarde ?> <small>Socios</small></span></li>
												<li class="list-group-item"><span class="textoNegrita">INASISTENTES</span> <span class="pull-right text-warning"><?= $falto ?> <small>Socios</small></span></li>
												<li class="list-group-item"><span class="textoNegrita">JUSTIFICADOS</span> <span class="pull-right text-warning"><?= $justifico ?> <small>Socios</small></span></li>
												<li class="list-group-item"><span class="textoNegrita">TOTAL JUSTIFICADOS</span> <span class="pull-right text-warning"><?= $infoMontoJustificado ?></span></li>
											</ul>
										</div>
										<div class="col-sm-6">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">M. TARDANZAS</span> <span class="pull-right text-warning"><?= $infoMTardanzas ?></span></li>
												<li class="list-group-item"><span class="textoNegrita">M. FALTAS</span> <span class="pull-right text-warning"><?= $infoMFaltas ?></span></li>
												<li class="list-group-item"><span class="textoNegrita">TOTAL MULTAS</span> <span class="pull-right text-warning"><?= $RE_infoTotalMultas ?></span></li>
												<li class="list-group-item"><span class="textoNegrita">TOTAL PAGADO</span> <span class="pull-right text-warning"><?= $RE_infoMultasPagadas ?></span></li>
												<li class="list-group-item"><span class="textoNegrita">TOTAL POR PAGAR</span> <span class="pull-right text-warning"><?= $RE_infoPorPagar ?></span></li>
												<li class="list-group-item"><span class="textoNegrita">TOTAL NETO</span> <span class="pull-right text-warning"><?= $infoTotalNeto ?></span></li>
											</ul>
										</div>
										<div class="col-sm-12">
											<ul class="list-group bg-warning-300">
												<li class="list-group-item text-center textoNegrita">ESTADISTICAS DE SOCIOS</li>
											</ul>
										</div>
										<div class="col-sm-4">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">PAGARON</span> <span class="pull-right text-warning"><?= $infoNroSociosPagaron ?> <small>Socios</small></span></li>
											</ul>
										</div>
										<div class="col-sm-4">
											<ul class="list-group">
												<li class="list-group-item"><span class="textoNegrita">EN PROCESO</span> <span class="pull-right text-warning"><?= $infoNroSociosFechasProgramas ?> <small>Socios</small></span></li>
											</ul>
										</div>
										<div class="col-sm-4">
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