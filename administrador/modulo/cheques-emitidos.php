<?php
	$totalEmitidosFechasPAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'totalEmitidosFechasPAR');
	$totalEmitidosFechasVAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'totalEmitidosFechasVAR');
	$totalEmitidosFechas    =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'totalEmitidosFechas');

	if($tipoCheque=="VAR"){
		$nroEmitidosFechasVAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'nroEmitidosFechasVAR');
		$nroEmitidosFechasPAR =0;
		if($totalEmitidosFechasVAR>0){ $infoTotalEmitidosFechasVAR='S/. '.moneda($totalEmitidosFechasVAR); }else{ $infoTotalEmitidosFechasVAR='<i class="fa fa-ellipsis-h"></i>'; }
		$infoTotalEmitidosFechasPAR='<i class="fa fa-ellipsis-h"></i>';
		$infoTotalEmitidosFechas='<i class="fa fa-ellipsis-h"></i>';
	}

	if($tipoCheque=="PAR"){
		$nroEmitidosFechasPAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'nroEmitidosFechasPAR');
		$nroEmitidosFechasVAR =0;
		if($totalEmitidosFechasPAR>0){ $infoTotalEmitidosFechasPAR='S/. '.moneda($totalEmitidosFechasPAR); }else{ $infoTotalEmitidosFechasPAR='<i class="fa fa-ellipsis-h"></i>'; }
		$infoTotalEmitidosFechasVAR='<i class="fa fa-ellipsis-h"></i>';
		$infoTotalEmitidosFechas='<i class="fa fa-ellipsis-h"></i>';
	}

	if($tipoCheque=="ALL"){
		$nroEmitidosFechasVAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'nroEmitidosFechasVAR');
		$nroEmitidosFechasPAR =infoCheques($consultaFechaIni,$consultaFechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuarioConsulta,'nroEmitidosFechasPAR');
		if($totalEmitidosFechasPAR>0){ $infoTotalEmitidosFechasPAR='S/. '.moneda($totalEmitidosFechasPAR); }else{ $infoTotalEmitidosFechasPAR='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalEmitidosFechasVAR>0){ $infoTotalEmitidosFechasVAR='S/. '.moneda($totalEmitidosFechasVAR); }else{ $infoTotalEmitidosFechasVAR='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalEmitidosFechas>0){ $infoTotalEmitidosFechas='S/. '.moneda($totalEmitidosFechas); }else{ $infoTotalEmitidosFechas='<i class="fa fa-ellipsis-h"></i>'; }
	}

	$chequesEmitidos=$nroEmitidosFechasVAR+$nroEmitidosFechasPAR;
	if($chequesEmitidos>0){ $infoChequesEmitidos=ceros($chequesEmitidos,2); }else{ $infoChequesEmitidos='<i class="fa fa-ellipsis-h"></i>'; }
?>

<?php if($chequesEmitidos>0){ ?>
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

			
			$("button.btn_eliminar_cheque").click(function(){
				var datos=$(this).attr('id');
				alert(datos);
			});
		});

		///////////////////////////////////////////////////
		/// ELIMINAR CHEQUE
		///////////////////////////////////////////////////
		function eliminaCheque(cheque){
			var ruta      ='../';
			var operacion ="ELIMINA_CHEQUE";
			var datos     =cheque+'&operacion='+operacion;
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
						url: ruta+'php/mantenimiento-cheques.php',
						data: datos,
						dataType:'json',
						success: function(respuesta){
							if(respuesta.mensaje=="CHEQUE_ELIMINADO"){
								new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
								location.reload();
							}

							if(respuesta.mensaje=="ERROR_CHEQUE_ELIMINADO"){
								new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
							}
						}
					});
				}
			});
		}
	</script>

	<div class="panel">
		<div class="panel-heading bg-slate-600"><h6 class="panel-title">CHEQUES EMITIDOS</h6></div>
		<div class="totalMonto">
			<div class="row">
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>EMITIDOS</span> <span class="pull-right text-danger textoNegrita"><?= $infoChequesEmitidos ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>PARTIDAS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalEmitidosFechasPAR ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>VARIOS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalEmitidosFechasVAR ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalEmitidosFechas ?></span></li>
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
							<th class="textoNegrita text-center">FECHA</th>
							<th class="textoNegrita text-center">TIPO</th>
							<th class="textoNegrita text-left">CONCEPTO</th>
							<th class="textoNegrita text-left">EMITIDO A</th>
							<th class="textoNegrita text-center">CHEQUERA</th>
							<th class="textoNegrita text-center">CHEQUE</th>
							<th class="textoNegrita text-center">MONTO</th>
							<th class="textoNegrita text-center">USUARIO</th>
							<th class="text-center"><i class="fa fa-align-justify"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if($usuarioConsulta=="ALL"){ $infoUsuario=""; }else{ $infoUsuario="AND beneficiario='$usuarioConsulta'"; }
							if($tipoCheque=="ALL"){ $infoTipoCheque=""; }else{ $infoTipoCheque="AND tipoCheque='$tipoCheque'"; }
							if($entidadBancaria=="ALL"){ $infoEntidadBancaria=""; }else{ $infoEntidadBancaria="AND codigoBanco='$entidadBancaria'"; }
							if($codigoCuenta=="ALL"){ $infoCodigoCuenta=""; }else{ $infoCodigoCuenta="AND codigoCuenta='$codigoCuenta'"; }
							if($codigoChequera=="ALL"){ $infoCodigoChequera=""; }else{ $infoCodigoChequera="AND codigoChequera='$codigoChequera'";}

							$sql="SELECT tipoCheque, codigoBanco, codigoCuenta, codigoChequera, nroCheque, codigoCheque, fechaEmision, monto, tipoBeneficiario, beneficiario, concepto, observaciones, codigoOperacion, fecha, hora, usuario FROM sm_cheques WHERE fechaEmision BETWEEN '$consultaFechaIni' AND '$consultaFechaFin' $infoTipoCheque $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$tipoCheque        =$n[tipoCheque];
								$codigoBanco       =$n[codigoBanco];
								$entidadBancaria   =infoBancos($codigoBanco,'detalleEntidad');
								$codigoCuenta      =$n[codigoCuenta];
								$nroCuenta         =infoCuentas($codigoCuenta,$codigoBanco,'numeroCuenta');
								$codigoChequera    =$n[codigoChequera];
								$nroCheque         =$n[nroCheque];
								$codigoCheque      =$n[codigoCheque];
								$fechaEmision      =infoFecha($n[fechaEmision],'normal');
								$monto             =$n[monto];
								$tipoBeneficiario  =$n[tipoBeneficiario];
								$beneficiario      =$n[beneficiario];
								$nombre            =infocheque('','','','','','',$beneficiario,'nombreBeneficiario');
								$concepto          =texto($n[concepto]);
								$observaciones     =texto($n[observaciones]);
								$codigoOperacion   =$n[codigoOperacion];
								$fecha             =$n[fecha];
								$hora              =$n[hora];
								$usuario           =$n[usuario];
								$chequera          =texto($entidadBancaria.' '.infoChequeras($codigoChequera,$codigoBanco,'','detalleChequera'));
								$registradoPor     =registradoPor($usuario,$fecha,$hora,'SI','label-default');
								$infoRegistradoPor =registradoPor($usuario,$fecha,$hora,'NO','');
								$codigoPartida     =infoPartida($codigoOperacion,'codigoPartida');
								if($tipoBeneficiario=="SOC"){ $infoBeneficiario=' <small class="text-danger">(SOCIO DE LA APV)</small>'; }
								if($tipoBeneficiario=="USR"){ $infoBeneficiario=' <small class="text-danger">(USUARIO DEL SISTEMA)</small>'; }
								if($tipoBeneficiario=="NOR"){ $infoBeneficiario=' <small class="text-danger">(PERSONA EXTERNA A LA APV)</small>'; }
								
								if($tipoCheque=="PAR"){
									$btTipoCheque='<a href="tesoreria.php?codigoPartida='.$codigoPartida.'&opcion=verPartida" class="btn btn-xs bg-grey textoNegrita">P</a>';
									$btEliminaCHB='<button type="button" class="btn btn-xs disabled bg-warning-300"><i class="fa fa-trash"></i></button>';
								}
								if($tipoCheque=="VAR"){
									$btTipoCheque='<button type="button" class="btn bg-brown-300 btn-xs" data-toggle="modal" data-target="#modal_detalle_cheque_'.$codigoCheque.'"><i class="fa fa-info"></i></button>';
									$datos='codigoBanco='.$codigoBanco.'&codigoCuenta='.$codigoCuenta.'&codigoChequera='.$codigoChequera.'&nroCheque='.$nroCheque.'&monto='.$monto.'&beneficiario='.$beneficiario.'&codigoOperacion='.$codigoOperacion.'&operacion=ELIMINA_CHEQUE';
									$btEliminaCHB='<button type="button" id='.$datos.'  class="btn btn-xs bg-warning-300 btn_eliminar_cheque"><i class="fa fa-trash"></i></button>';
								}
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-center textoMayuscula"><?= $fechaEmision ?></td>
							<td class="text-left"><?= $tipoCheque ?></td>
							<td class="text-left"><?= $concepto ?></td>
							<td class="text-left"><?= $nombre ?></td>
							<td class="text-left"><?= $chequera ?></td>
							<td class="text-left"><?= $nroCheque ?></td>
							<td class="text-right text-danger textoNegrita"><?= 'S/.&nbsp;'.moneda($monto) ?></span></td>
							<td class="text-center"><?= $registradoPor ?></td>
							<td class="text-center">
								<?= $btTipoCheque ?>
								<?= $btEliminaCHB ?>
							</td>
						</tr>

						<!-- MODAL DETALLE DE CHEQUE -->
						<div id="modal_detalle_cheque_<?= $codigoCheque ?>" class="modal fade">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">
									<div class="modal-header bg-brown"><h6 class="modal-title textoNegrita">DETALLE DE CHEQUE</h6><button type="button" class="close" data-dismiss="modal">&times;</button></div>
									<div class="modal-body">
										<ul class="list-group">
											<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-slate"><?= $concepto ?></span></li>
											<li class="list-group-item"><span class="textoNegrita">BENEFICIARIO O RESPONSABLE</span> <span class="pull-right text-slate"><?= $nombre.$infoBeneficiario ?></span></li>
										</ul>
										<div class="row mb-20">
											<div class="col-sm-6">
												<ul class="list-group">
													<li class="list-group-item"><span class="textoNegrita">TIPO DE CHEQUE EMITIDO</span> <span class="pull-right text-slate">VARIOS</span></li>
													<li class="list-group-item"><span class="textoNegrita">ENTIDAD BANCARIA</span> <span class="pull-right text-slate"><?= $entidadBancaria ?></li>
													<li class="list-group-item"><span class="textoNegrita">CHEQUERA</span> <span class="pull-right text-slate"><?= $chequera ?></li>
													<li class="list-group-item"><span class="textoNegrita">MONTO EM ITIDO EN CHEQUE</span> <span class="pull-right text-slate"><?= 'S/.&nbsp;'.moneda($monto) ?></li>
												</ul>
											</div>
											<div class="col-sm-6">
												<ul class="list-group">
													<li class="list-group-item"><span class="textoNegrita">FECHA DE EMISION</span> <span class="pull-right text-slate textoMayuscula"><?= $fechaEmision ?></span></li>
													<li class="list-group-item"><span class="textoNegrita">NRO DE CUENTA</span> <span class="pull-right text-slate"><?= $nroCuenta ?></li>
													<li class="list-group-item"><span class="textoNegrita">NRO DE CHEQUE</span> <span class="pull-right text-slate"><?= $nroCheque ?></li>
													<li class="list-group-item"><span class="textoNegrita">REGISTRADO POR</span> <span class="pull-right text-slate textoMayuscula"><?= $infoRegistradoPor ?></li>
												</ul>
											</div>
										</div>
									</div>
									<div class="modal-footer text-center"><button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal"><b><i><i class="fa fa-times"></i></i></b>CERRAR</button></div>
								</div>
							</div>
						</div>

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