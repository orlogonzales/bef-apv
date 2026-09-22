<?php 
	$codigoPartida   =$_GET[codigoPartida];
	$verificaPartida =infoPartida($codigoPartida,'verificaPartida');
	if($codigoPartida==$verificaPartida){
		$sql="SELECT usuarioPartida, concepto, monto, codigoChequera, nroCheque, observaciones, fechaPartida, fechaCierre, estado, fecha, hora, usuario  FROM sm_partidas WHERE codigoPartida='$codigoPartida'";
		$row=mysqli_query($conexion,$sql);
		$n=mysqli_fetch_array($row);
		$usuarioPartida  =$n[usuarioPartida];
		$concepto        =texto($n[concepto]);
		$monto           =$n[monto];
		$codigoChequera  =$n[codigoChequera];
		$nroCheque       =$n[nroCheque];
		$observaciones   =texto($n[observaciones]);
		$fechaPartida    =$n[fechaPartida];
		$fechaCierre     =$n[fechaCierre];
		$estado          =$n[estado];
		$fecha           =$n[fecha];
		$hora            =$n[hora];
		$usuario         =$n[usuario];
		$entidadBancaria =infoBancos(infoChequeras($codigoChequera,'','','entidadBancaria'),'detalleEntidad');
		$detalleChequera =texto(infoChequeras($codigoChequera,'','','detalleChequera'));
		$infoChequera    =$entidadBancaria.' - '.$detalleChequera;

		$responsable=texto(datoUsuario($usuarioPartida,'nombreFull'));
		if($fechaPartida!="0000-00-00"){ $infoFechaPartida=infoFecha($fechaPartida,'larga'); }else{ $infoFechaPartida="SIN FECHA DE GASTO"; }
		if($fechaCierre!="0000-00-00"){ $infoFechaCierre=infoFecha($fechaCierre,'larga'); }else{ $infoFechaCierre="SIN FECHA DE CIERRE"; }
		
		$nombreUP     =texto(datoUsuario($usuarioPartida,'nombreCorto'));
		$infoRegistro =datoUsuario($usuario,'nombre').'&nbsp|&nbsp'.infoFecha($fecha,'info').'&nbsp|&nbsp'.horaCorta($hora);

		if($estado=="OPN"){ $infoEstado='<span class="label label-success">ABIERTO</span>'; $opcionesPartida='<a href="#registraGastosPartida" data-toggle="modal" class="list-group-item"><i class=" icon-square-right"></i> REGISTRO GASTOS DE PARTIDA</a><a href="#cerrarPartida" data-toggle="modal" class="list-group-item"><i class=" icon-square-right"></i> CERRAR / FINALIZAR PARTIDA</a>'; }
		if($estado=="CLS"){ $infoEstado='<span class="label label-warning">CERRADO</span>'; $opcionesPartida=''; }
		if($estado=="USO"){ $infoEstado='<span class="label label-info">EN USO</span>'; $opcionesPartida='<a href="#registraGastosPartida" data-toggle="modal" class="list-group-item"><i class=" icon-square-right"></i> REGISTRO GASTOS DE PARTIDA</a><a href="#cerrarPartida" data-toggle="modal" class="list-group-item"><i class=" icon-square-right"></i> CERRAR / FINALIZAR PARTIDA</a> <a href="../documentos/reporte-partida.php?codigoPartida='.$codigoPartida.'" class="list-group-item"><i class="icon-printer2"></i> REGISTRO GASTOS DE PARTIDA</a>'; }

		$montoDispuesto =infoPartida($codigoPartida,'totalSAL');
		$totalING       =infoPartida($codigoPartida,'totalING');
		$saldoPartida   =$monto-$montoDispuesto;
		$totalPartida   =infoPartida($codigoPartida,'montoPartida');
		$totalCierre    =$montoDispuesto+$totalING;
		if($totalING>0){ $montoIngresos='S/. '.moneda($totalING); }else{ $montoIngresos='----'; }

		$infoObservaciones=$observaciones;
		$reportePartida='<a href="../documentos/reporte-partida.php?codigoPartida='.$codigoPartida.'" class="list-group-item"><i class="icon-printer2"></i> REGISTRO GASTOS DE PARTIDA</a>';
?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// REGISTRO DE GASTOS DE PARTIDA
			///////////////////////////////////////////////////
			$("button#bt_registra_gastos_partida").click(function(){
				var ruta            ='../';
				var concepto        ='PAR';
				var codigoPartida   ='<?= $codigoPartida ?>';
				var movimiento      =$('input#movimientoGAS').val();
				var codigoConcepto  =$('input#codigoConceptoGAS').val();
				var detalleConcepto =$('input#detalleConceptoGAS').val();
				var fechaOperacion  =$('input#fechaOperacionGAS').val();
				var monto           =$('input#montoGAS').val();
				var tipoDocumento   =$('select#tipoDocumentomontoGAS').val();
				var nroDocumento    =$('input#nroDocumentomontoGAS').val();
				var observaciones   =$('textarea#observacionesGAS').val();
				var operacion       ='REGISTRA_GASTOS_PARTIDA';
				var opcion          ='<?= $opcion ?>';
				var datos           ='concepto='+concepto+'&codigoPartida='+codigoPartida+'&movimiento='+movimiento+'&codigoConcepto='+codigoConcepto+'&detalleConcepto='+detalleConcepto+'&fechaOperacion='+fechaOperacion+'&monto='+monto+'&tipoDocumento='+tipoDocumento+'&nroDocumento='+nroDocumento+'&observaciones='+observaciones+'&operacion='+operacion;
				var valida          = $('.form_valida_gato_partida').valid();
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-caja.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('button#bt_registra_gastos_partida').prop('disabled', true);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="GASTOS_REGISTRADOS"){
								new PNotify({title: 'CONFIRMACION', text: 'Los datos de gasto ha sido registrado.', addclass: 'bg-success'});
								$("#registraGastosPartida").modal('hide');
								location.reload();
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// REGISTRO DE CIERRE DE PARTIDA
			///////////////////////////////////////////////////
			$("button#bt_cerrar_partida").click(function(){
				var ruta            ='../';
				var concepto        ='PAR';
				var codigoPartida   ='<?= $codigoPartida ?>';
				var movimiento      =$('input#movimientoCierre').val();
				var codigoConcepto  =$('input#codigoConceptoCierre').val();
				var detalleConcepto =$('input#detalleConceptoCierre').val();
				var tipoDocumento   =$('select#tipoDocumentoCierre').val();
				var nroDocumento    =$('input#nroDocumentoCierre').val();
				var fechaOperacion  =$('input#fechaOperacionCierre').val();
				var monto           =$('input#montoCierre').val();
				var observaciones   =$('textarea#observacionesCierre').val();
				var operacion       ='REGISTRA_CIERRE_PARTIDA';					
				var datos           ='concepto='+concepto+'&codigoPartida='+codigoPartida+'&movimiento='+movimiento+'&codigoConcepto='+codigoConcepto+'&detalleConcepto='+detalleConcepto+'&fechaOperacion='+fechaOperacion+'&monto='+monto+'&tipoDocumento='+tipoDocumento+'&nroDocumento='+nroDocumento+'&observaciones='+observaciones+'&operacion='+operacion;
				var valida          = $('.form_valida_cierre').valid();
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-caja.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('button#bt_cerrar_partida').prop('disabled', true);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="PARTIDA_CERRADA"){
								new PNotify({title: 'PARTIDA CERRADA', text: 'La partida fue cerrada o finalizada.', addclass: 'bg-success'});
								$("#cerrarPartida").modal('hide');
								location.reload();
							}
						}
					});
				}
			});
			
			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO INGRESO GASTOS DE PARTIDA
			///////////////////////////////////////////////////
			var validator = $(".form_valida_gato_partida").validate({
				ignore: 'input[type=hidden], .select2-input',
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },

				validClass: "validation-valid-label",
				rules: {
					detalleConceptoGAS: { required: true },
					fechaOperacionGAS: { required: true },
		 			montoGAS: { number: true, min: 1, max: <?= $saldoPartida ?> },
		 			tipoDocumentomontoGAS: { required: true },
					nroDocumentomontoGAS: { required: true },
				},
				messages: {
					detalleConceptoGAS: { required: "Ingrese detalles de gasto", },
					fechaOperacionGAS: { required: "Fecha de gasto", },
					montoGAS: { required: "Monto de gasto", number: "Monto de gasto", min: "Minimo S/. 1", max: "Maximo S/."+<?= $saldoPartida ?>, },
					tipoDocumentomontoGAS: { required: "Seleccione tipo de documento", },
					nroDocumentomontoGAS: { required: "Ingrese Nro. de documento", },
				}
			});

			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO CIERRE DE PARTIDA
			///////////////////////////////////////////////////
			var validator = $(".form_valida_cierre").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },

				validClass: "validation-valid-label",
				rules: {
					nroDocumento: { minlength: 2 },
					fechaOperacion: { required: true },
				},
				messages: {
					nroDocumento: { required: "Ingrese Nro. de documento", minlength: "Ingrese documento valido", },
					fechaOperacion: { required: "Fecha de gasto", },
				}
			});

			///////////////////////////////////////////////////
			/// VALIDAR CAMPOS NUMERICOS
			///////////////////////////////////////////////////
			$(function(){ $('#monto, #montoGasto').validar('0123456789.'); });
			$(function(){ $('#fechaOperacion, #fechaOperacionCierre').validar('0123456789/'); });
		});
	</script>

	<div class="row">
		<div class="col-sm-9">
			<div class="panel">
				<div class="panel-heading bg-slate-600">
					<h6 class="panel-title">DETALLES DE PARTIDA</h6>
				</div>
				<div class="panel-body">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-slate"><?= $concepto ?></span></li>
					</ul>
					<div class="row">
						<div class="col-sm-7">
							<ul class="list-group">
								<li class="list-group-item"><span class="textoNegrita">RESPONSABLE GASTO</span> <span class="pull-right text-slate"><?= $responsable ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">CHEQUERA</span> <span class="pull-right text-slate textoMayuscula"><?= $infoChequera ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">FECHA DE GASTO</span> <span class="pull-right text-slate textoMayuscula"><?= $infoFechaPartida ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">ESTADO</span> <span class="pull-right text-slate textoMayuscula"><?= $infoEstado ?></span></li>
							</ul>
						</div>
						<div class="col-sm-5">
							<ul class="list-group">
								<li class="list-group-item"><span class="textoNegrita">MONTO DE PARTIDA</span> <span class="pull-right text-slate"><?= 'S/. '.moneda($monto) ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">NRO DE CHEQUE</span> <span class="pull-right text-slate textoMayuscula"><?= $nroCheque ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">FECHA DE CIERRE</span> <span class="pull-right text-slate textoMayuscula"><?= $infoFechaCierre ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">GENERADO POR</span> <span class="pull-right text-slate"><?= $infoRegistro ?></span></li>
							</ul>
						</div>
					</div>
					<div class="row mt-20">
						<div class="col-md-12"><p><strong class="text-danger">OBSERVACIONES:</strong> <?= $infoObservaciones ?></p></div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-3">
			<div class="panel">
				<div class="panel-heading bg-warning-300">
					<h6 class="panel-title">ESTADISTICAS</h6>
				</div>
				<div class="panel-body">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">PRESPUESTO PARTIDA</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($monto) ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">MONTO DISPUESTO</span> <span class="pull-right text-danger textoMayuscula"><?= '- S/. '.moneda($montoDispuesto) ?></span></li>
						<?php if($totalING==$saldoPartida){ ?>
							<li class="list-group-item"><span class="textoNegrita">SALDO DISPONIBLE</span> <span class="pull-right text-danger textoMayuscula"><?= 'S/. '.moneda(0) ?></span></li>
							<li class="list-group-item"><span class="textoNegrita">MONTO REPUESTO A CAJA</span> <span class="pull-right text-danger textoMayuscula"><?= $montoIngresos ?></span></li>
						<?php }else{ ?>
							<li class="list-group-item"><span class="textoNegrita">SALDO DISPONIBLE</span> <span class="pull-right text-danger textoMayuscula"><?= 'S/. '.moneda($saldoPartida) ?></span></li>
						<?php } ?>
					</ul>
				</div>
			</div>
			<div class="panel panel-flat border-top-xlg border-top-slate">
				<div class="panel-body">
					<div class="list-group list-group-borderless no-padding-top">
						<?php if($estado=="OPN"){ echo $opcionesPartida; } ?>
						<?php if($estado=="USO"){ echo $opcionesPartida; } ?>
						<?php if($estado=="CLS"){ echo $reportePartida; } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php if($montoDispuesto>0){ ?>
		<div class="panel">
			<div class="panel-heading bg-warning-300"><h6 class="panel-title">PARTIDA - DETALLES DE MOVIENTO EN CAJA</h6></div>
			<div class="table-responsive">
				<table class="table tabla-info table-bordered table-hover">
					<thead>
						<tr class="success">
							<th class="textoNegrita text-center">#</th>
							<th class="textoNegrita text-left">MOV</th>
							<th class="textoNegrita text-left">FECHA OPERACION</th>
							<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
							<th class="textoNegrita text-left">MONTO</th>
							<th class="textoNegrita text-center">DOC</th>
							<th class="textoNegrita text-center">REGISTRO</th>
							<th class="textoNegrita text-center">OBS</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT movimiento, fechaOperacion, tipoDocumento, nroDocumento, monto, detalleConcepto, observaciones, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE (movimiento='SAL' OR movimiento='ING') AND codigoConcepto='$codigoPartida' ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$movimiento      =$n[movimiento];
								$fechaOperacion  =$n[fechaOperacion];
								$tipoDocumento   =$n[tipoDocumento];
								$nroDocumento    =$n[nroDocumento];
								$monto           =$n[monto];
								$detalleConcepto =$n[detalleConcepto];
								$observaciones   =$n[observaciones];
								$codigoOperacion =$n[codigoOperacion];
								$fecha           =$n[fecha];
								$hora            =$n[hora];
								$usuario         =$n[usuario];
								$infoRegistro    ='<span class="label label-default">'.datoUsuario($usuario,'nombre').'&nbsp|&nbsp'.infoFecha($fecha,'info').'&nbsp|&nbsp'.horaCorta($hora).'</span>';
								if($observaciones!=""){ $boton='<a href="#infoObserva'.$codigoOperacion.'" class="btn btn-xs btn-icon bg-grey" data-toggle="modal" data-popup="tooltip" data-placement="left" title="Observaciones"><i class="icon-file-text2"></i></a>'; }else{ $boton="";}
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-left"><?= $movimiento ?></td>
							<td class="text-left textoMayuscula"><?= infoFecha($fechaOperacion,'corta') ?></td>
							<td class="text-left"><?= texto($detalleConcepto) ?></td>
							<td class="text-right text-danger textoNegrita"><?= 'S/. '.moneda($monto) ?></td>
							<td class="text-left"><?= infoTipoDOC($tipoDocumento).' N°: <strong>'.$nroDocumento.'</strong>' ?></td>
							<td class="text-center"><?= $infoRegistro ?></td>
							<td class="text-center"><?= $boton ?></td>
						</tr>
						<!-- MODAL - OBSERVACIONES -->
						<div id="infoObserva<?= $codigoOperacion ?>" class="modal fade">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header bg-brown">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h5 class="modal-title">OBSERVACIONES DE GASTO</h5>
									</div>
									<div class="modal-body">
										<label class="text-danger textoNegrita">DETALLES DE OBSERVACION</label>
										<p><?= $detalleConcepto ?></p>
										<div class="form-group">
											<div class="row text-center">
												<div class="col-sm-12">
													<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php $i++; } ?>
						<tr>
							<td colspan="8" class="text-center totalMonto"><span class="textoBig"><?= '<strong>TOTAL EGRESOS:</strong> S/. '.moneda($montoDispuesto) ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?= '<strong>TOTAL INGRESOS:</strong> '.$montoIngresos ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?= '<strong>TOTAL CIERRE:</strong> S/. '.moneda($totalCierre) ?></span></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	<?php } ?>

	<div class="panel">
		<div class="panel-heading bg-slate-600"><h6 class="panel-title">REGISTRO DE OPERACIONES PROCESADAS</h6></div>
		<div class="panel-body">
			<div class="table-responsive">
				<table class="table tabla-info table-bordered table-hover">
					<thead>
						<tr class="success">
							<th class="textoNegrita text-center">#</th>
							<th class="textoNegrita text-left">CODIGO OPERACION</th>
							<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
							<th class="textoNegrita text-left">MONTO</th>
							<th class="textoNegrita text-center">FECHA</th>
							<th class="textoNegrita text-center">HORA</th>
							<th class="textoNegrita text-center">USUARIO</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT codigoPartida, proceso, monto, fecha, hora, usuario FROM sm_procesos_partidas WHERE codigoPartida='$codigoPartida' ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$codigoPartida =$n[codigoPartida];
								$proceso       =$n[proceso];
								$monto         =$n[monto];
								$fecha         =$n[fecha];
								$hora          =$n[hora];
								$usuario       =$n[usuario];
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-left"><?= $codigoPartida ?></td>
							<td class="text-left"><?= texto($proceso) ?></td>
							<td class="text-left"><?= 'S/. '.moneda($monto) ?></td>
							<td class="text-center textoMayuscula"><?= infoFecha($fecha,'normal') ?></td>
							<td class="text-center"><?= horaCorta($hora) ?></td>
							<td class="text-center"><?= texto(datoUsuario($usuario,'nombre')) ?></td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- MODAL DE INGRESO DE GASTOS DE PARTIDA -->
	<div id="registraGastosPartida" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-brown">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title">REGISTRA GASTO DE PARTIDA</h5>
				</div>
				<form id="form_partida" class="form_valida_gato_partida">
					<input type="hidden" id="movimientoGAS" value="SAL">
					<input type="hidden" id="codigoConceptoGAS" value="<?= $codigoPartida ?>">
					<div class="modal-body">
						<div class="alert alert-styled-left alert-arrow-left alpha-warning textoNegrita"><?= infoPartida($codigoPartida,'conceptoPartida') ?></div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label class="text-brown textoNegrita">DETALLE</label>
									<input type="text" name="detalleConceptoGAS" id="detalleConceptoGAS" required="required" class="form-control input-lg textoMayuscula" onblur="mayusculas(event, this)" placeholder="DETALLES DE GASTO" tabindex="1" autofocus>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-3">
									<label class="text-brown textoNegrita">FECHA DE GASTO</label>
									<input type="text" name="fechaOperacionGAS" id="fechaOperacionGAS" required="required" class="form-control fechas input-lg" tabindex="2">
								</div>
								<div class="col-sm-2">
									<label class="text-brown textoNegrita">MONTO</label>
									<input type="text" name="montoGAS" id="montoGAS" required="required" class="form-control input-lg" tabindex="3">
								</div>
								<div class="col-sm-4">
									<label class="text-brown textoNegrita">TIPO DE DOCUMENTO</label>
									<select name="tipoDocumentomontoGAS" id="tipoDocumentomontoGAS" required="required" class="form-control input-lg" tabindex="4">
										<option value="" selected>SELECCIONE</option>
										<?php
											$sql="SELECT * FROM sm_a_doc_pago";
											$rs=mysqli_query($conexion,$sql);
											while($datos=mysqli_fetch_array($rs)){
												echo '<option value="'.$datos[0].'">'.$datos[1].'</option>';
											}
										?>
									</select>
								</div>
								<div class="col-sm-3">
									<label class="text-brown textoNegrita">NRO DOCUMENTO</label>
									<input type="text" name="nroDocumentomontoGAS" id="nroDocumentomontoGAS" required="required" class="form-control input-lg" tabindex="5">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label class="text-brown textoNegrita">OBSERVACIONES</label>
									<textarea rows="5" cols="5" id="observacionesGAS" class="form-control textoMayuscula" onblur="mayusculas(event, this)" placeholder="OBSERVACIONES" tabindex="5"></textarea>
								</div>
							</div>
						</div>
						<div class="form-group_">
							<div class="row text-center">
								<div class="col-sm-12">
									<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
									<button type="button" id="bt_registra_gastos_partida" class="btn bg-grey">ALAMACENAR GASTO DE PARTIDA</button>
								</div>							
							</div>
						</div>
					</div>
					<div class="modal-footer">
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- MODAL DE CIERRE DE PARTIDA -->
	<div id="cerrarPartida" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-brown">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title">REGISTRA CIERRE DE PARTIDA</h5>
				</div>
				<form id="form_partida" class="form_valida_cierre">
					<input type="hidden" id="movimientoCierre" value="ING">
					<input type="hidden" id="codigoConceptoCierre" value="<?= $codigoPartida ?>">
					<div class="modal-body">
						<div class="alert alert-styled-left alert-arrow-left alpha-warning textoNegrita"><?= infoPartida($codigoPartida,'conceptoPartida') ?></div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label class="text-brown textoNegrita">DETALLE</label>
									<input type="hidden" id="detalleConceptoCierre" value="CIERRE DE PARTIDA Y REINGRESO DE SALDO DE PARTIDA A CAJA">
									<input disabled="disabled" class="form-control input-lg textoMayuscula" value="CIERRE DE PARTIDA Y REINGRESO DE SALDO DE PARTIDA A CAJA">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4">
									<label class="text-brown textoNegrita">TIPO DE DOCUMENTO</label>
									<select name="tipoDocumento" id="tipoDocumentoCierre" required="required" class="form-control input-lg" tabindex="2">
										<option value="" selected>SELECCIONE</option>
										<?php
											$sql="SELECT * FROM sm_a_doc_pago";
											$rs=mysqli_query($conexion,$sql);
											while($datos=mysqli_fetch_array($rs)){
												if($datos[0]=="DJU"){
													echo '<option value="'.$datos[0].'" selected>'.$datos[1].'</option>';
												}
											}
										?>
									</select>
								</div>
								<div class="col-sm-3">
									<label class="text-brown textoNegrita">NRO DOCUMENTO</label>
									<input type="text" name="nroDocumento" id="nroDocumentoCierre" required="required" class="form-control input-lg" tabindex="1" autofocus>
								</div>
								<div class="col-sm-3">
									<label class="text-brown textoNegrita">FECHA DE CIERRE</label>
									<input type="text" name="fechaOperacion" id="fechaOperacionCierre" required="required" class="form-control fechas input-lg" tabindex="1">
								</div>
								<div class="col-sm-2">
									<label class="text-brown textoNegrita">MONTO</label>
									<input type="hidden" id="montoCierre" value="<?= $saldoPartida ?>">
									<input disabled="disabled" value="<?= $saldoPartida ?>" class="form-control input-lg">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-sm-12">
									<label class="text-brown textoNegrita">OBSERVACIONES</label>
									<textarea rows="5" cols="5" id="observacionesCierre" class="form-control textoMayuscula" onblur="mayusculas(event, this)" placeholder="OBSERVACIONES" tabindex="5"></textarea>
								</div>
							</div>
						</div>
						<div class="form-group_">
							<div class="row text-center">
								<div class="col-sm-12">
									<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
									<button type="button" id="bt_cerrar_partida" class="btn bg-grey">CERRAR PARTIDA</button>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
					</div>
				</form>
			</div>
		</div>
	</div>

<?php
	}else{
		echo cajaAlerta('ERROR EN PARTIDA','text-center textoNegrita','la partida seleccionada no existe.','text-center','bg-danger-300');
	}
?>
