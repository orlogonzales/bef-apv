<?php 
	if($contarPartidas>0){
		$totalPartidas          =infoPartida('','totalPartidas');
		$totalPartidasDispuesto =infoPartida('','totalPartidasDispuesto');
		$totalPartidasCierre    =infoPartida('','totalPartidasCierre');
		$totalPartidasSaldo     =$totalPartidas-($totalPartidasDispuesto+$totalPartidasCierre);

		if($totalPartidas>0){ $infoTotalPartidas='S/. '.moneda($totalPartidas);}else{ $infoTotalPartidas='----'; }
		if($totalPartidasDispuesto>0){ $infoTotalPartidasDispuesto='- S/. '.moneda($totalPartidasDispuesto);}else{ $infoTotalPartidasDispuesto='----'; }
		if($totalPartidasSaldo>0){ $infoTotalPartidasSaldo='S/. '.moneda($totalPartidasSaldo);}else{ $infoTotalPartidasSaldo='----'; }
		if($totalPartidasCierre>0){ $infoTotalPartidasCierre='+ S/. '.moneda($totalPartidasCierre);}else{ $infoTotalPartidasCierre='----'; }
?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// ELIMINAR ITEM
			///////////////////////////////////////////////////
			$('.bt_eliminar_partida').on('click', function() {
				var ruta          ='<?= $ruta ?>';
				var codigoPartida =$(this).attr('id');
				var operacion     ='ELIMINAR_PARTIDA';
				var datos         = 'codigoPartida='+codigoPartida+'&operacion='+operacion;
				swal({
					title: "Eliminar",
					text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#EF5350",
					confirmButtonText: "Si, Eliminar",
					cancelButtonText: "No, Cancelar",
					closeOnConfirm: false,
					closeOnCancel: true
				},
				function(isConfirm){
					if (isConfirm) { 
						swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
						$.ajax({
							type: "POST",
							url: ruta+'php/mantenimiento-partidas.php',
							data: datos,
							dataType:'json',
							success: function(respuesta){
								if(respuesta.mensaje=="PARTIDA_ELIMINADA"){
									new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminadp del sistema.', addclass: 'bg-success'});
									location.reload();
								}
							}
						});
					}
				});
			});
			
			///////////////////////////////////////////////////
			/// EDITAR ITEM
			///////////////////////////////////////////////////
			$('.bt_editar_partida').on('click', function() {
				var ruta            ='<?= $ruta ?>';
				var codigoPartida   =$(this).attr('id');
				var operacion       ='EDITAR_PARTIDA';
				var conceptoPartida =$('input#concepto'+codigoPartida).val();
				var usuarioPartida  =$('select#usuarioPartida'+codigoPartida).val();
				var monto           =$('input#monto'+codigoPartida).val();
				var fechaPartida    =$('input#fechaPartida'+codigoPartida).val();
				var estado          =$('input#estado'+codigoPartida).val();
				var datos           ='codigoPartida='+codigoPartida+'&usuarioPartida='+usuarioPartida+'&conceptoPartida='+conceptoPartida+'&monto='+monto+'&fechaPartida='+fechaPartida+'&estado='+estado+'&operacion='+operacion;
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-partidas.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('button#bt_editar_partida').prop('disabled', true);
					},
					success: function(respuesta){
						if(respuesta.mensaje=="PARTIDA_MODIFICADA"){
							new PNotify({title: 'CONFIRMACION', text: 'La partida de gasto fue modificada.', addclass: 'bg-success'});
							$("#editarItem"+codigoPartida).modal('hide');
							location.reload();
						}
					}
				});
			});
		});
	</script>

	<div class="panel">
		<div class="panel-heading bg-slate-600">
			<h6 class="panel-title">LISTA DE PARTIDAS</h6>
		</div>

		<div class="totalMonto">
			<div class="row">
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL PARTIDAS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalPAR ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL EGRESOS</span> <span class="pull-right text-danger textoNegrita"><?= $infoToPARSAL ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL DEV CAJA</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalCLS ?></span></li>
					</ul>
				</div>
				<div class="col-sm-3">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL DISPONIBLE</span> <span class="pull-right text-danger textoNegrita"><?= $infoToPARPEN ?></span></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="panel-body">
			<table class="table tablaT table-bordered table-hover tabla-caja-ing">
				<thead>
					<tr class="success">
						<th class="textoNegrita text-center">#</th>
						<th class="textoNegrita text-center">CODIGO PARTIDA</th>
						<th class="textoNegrita text-left">RESPONSABLE</th>
						<th class="textoNegrita text-left">CONCEPTO</th>
						<th class="textoNegrita text-left">PARTIDA</th>
						<th class="textoNegrita text-center">GASTO</th>
						<th class="textoNegrita text-center">ESTADO</th>
						<th class="textoNegrita text-center">F. GASTO</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT codigoPartida, usuarioPartida, concepto, monto, fechaPartida, fechaCierre, estado, fecha, hora, usuario  FROM sm_partidas ORDER BY id DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoPartida  =$n[codigoPartida];
							$usuarioPartida =$n[usuarioPartida];
							$concepto       =texto($n[concepto]);
							$monto          =$n[monto];
							$fechaPartida   =$n[fechaPartida];
							$fechaCierre    =$n[fechaCierre];
							$estado         =$n[estado];
							$fecha          =$n[fecha];
							$hora           =$n[hora];
							$usuario        =$n[usuario];
							$nombreUP       =texto(datoUsuario($usuarioPartida,'nombreCorto'));
							if($nombreUP==""){
								$nombreUP=texto(infoSocios($usuarioPartida,'nombre'));
							}else{
								$nombreUP=$nombreUP;
							}
							$infoRegistro   ='<span class="label label-default">'.datoUsuario($usuario,'nombre').'&nbsp|&nbsp'.infoFecha($fecha,'info').'&nbsp|&nbsp'.horaCorta($hora).'</span>';

							if($estado=="OPN"){ $infoEstado='<span class="label label-success">ABIERTO</span>'; $opciones='<li><a href="tesoreria.php?codigoPartida='.$codigoPartida.'&opcion=verPartida"><i class="icon-diff-added"></i> Ver Partida</a></li><li><a href="javascript:;" id="'.$codigoPartida.'" class="bt_eliminar_partida"><i class="icon-trash"></i> Eliminar Partida</a></li> <li><a href="#editarItem'.$codigoPartida.'" data-toggle="modal"><i class="icon-pencil5"></i> Editar partida</a></li>'; }
							if($estado=="CLS"){ $infoEstado='<span class="label label-warning">CERRADO</span>'; $opciones='<li><a href="tesoreria.php?codigoPartida='.$codigoPartida.'&opcion=verPartida"><i class="icon-diff-added"></i> Ver Partida</a></li>'; }
							if($estado=="USO"){ $infoEstado='<span class="label label-info">EN USO</span>'; $opciones='<li><a href="tesoreria.php?codigoPartida='.$codigoPartida.'&opcion=verPartida"><i class="icon-diff-added"></i> Ver Partida</a></li>'; }

							$montoDispuesto =infoPartida($codigoPartida,'totalSAL');
							$totalING       =infoPartida($codigoPartida,'totalING');
							$saldoPartida   =$monto-$montoDispuesto;

					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-center"><?= $codigoPartida ?></td>
						<td class="text-left"><?= $nombreUP ?></td>
						<td class="text-left"><?= $concepto ?></td>
						<td class="text-center text-success textoNegrita"><?= "S/. ".moneda($monto) ?></td>
						<td class="text-center text-danger textoNegrita"><?= "S/. ".moneda($montoDispuesto) ?></td>
						<td class="text-center"><?= $infoEstado ?></td>
						<td class="text-center"><?= haceTiempo($fechaPartida) ?></td>
						<td class="text-center">
							<ul class="icons-list">
								<li class="dropdown">
									<a href="#" class="dropdown-toggle label label-default" data-toggle="dropdown">MENU <span class="caret"></span></a>
									<ul class="dropdown-menu dropdown-menu-right">
										<?= $opciones ?>
									</ul>
								</li>
							</ul>
						</td>
					</tr>
					<!-- MODAL - EDITAR PARTIDA -->
					<div id="editarItem<?= $codigoPartida ?>" class="modal fade">
						<div class="modal-dialog modal-sm">
							<div class="modal-content">
								<div class="modal-header bg-brown">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h5 class="modal-title">EDITAR PARTIDA DE GASTO</h5>
								</div>
								<?php if($estado!="CLS"){ ?>
									<form id="form_partida">
										<div class="modal-body">
											<div class="alert alert-styled-left alert-arrow-left alpha-teal textoNegrita textoBig"><?= 'DISPONIBLE S/. '.moneda($montoDisponible) ?></div>
											<div class="form-group">
												<div class="row">
													<div class="col-sm-12">
														<select id="usuarioPartida<?= $codigoPartida ?>" class="form-control text-danger input-lg" autofocus tabindex="1">
															<option value="" selected>SELECCIONE USUARIO</option>
															<?php
																$sql="SELECT dni FROM sm_usuarios";
																$info=mysqli_query($conexion,$sql);
																while($datos=mysqli_fetch_array($info)){
																	if($datos[0]==$usuarioPartida){
																		echo '<option value="'.$datos[0].'" selected>'.texto(datoUsuario($datos[0],'nombreFull')).'</option>';
																	}else{
																		echo '<option value="'.$datos[0].'">'.texto(datoUsuario($datos[0],'nombreFull')).'</option>';
																	}
																}
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="row">
													<div class="col-sm-12">
														<label>CONCEPTO</label>
														<input type="text" id="concepto<?= $codigoPartida ?>" value="<?= $concepto ?>" class="form-control text-danger input-lg textoMayuscula" onblur="mayusculas(event, this)" tabindex="2">
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="row">
													<div class="col-sm-5">
														<label>MONTO</label>
														<input type="text" id="monto<?= $codigoPartida ?>" value="<?= $monto ?>" class="form-control text-danger input-lg" tabindex="3">
													</div>
													<div class="col-sm-7">
														<label>FECHA DE GASTO</label>
														<input type="text" id="fechaPartida<?= $codigoPartida ?>" value="<?= infoFecha($fechaPartida,'info') ?>" class="form-control text-danger fechas input-lg" tabindex="4">
														<input type="hidden" id="estado<?= $codigoPartida ?>" value="<?= $estado ?>">
													</div>
												</div>
											</div>
											<div class="form-group_">
												<div class="row text-center">
													<div class="col-sm-12">
														<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
														<button type="button" id="<?= $codigoPartida ?>" class="btn bg-grey bt_editar_partida">ALAMACENAR PARTIDA</button>
													</div>							
												</div>
											</div>
										</div>
										<div class="modal-footer">
										</div>
									</form>
								<?php }else{ echo cajaAlerta('NO SE PUEDE EDITAR','text-center textoNegrita','Partida seleccionada esta con el estado CERRADO y no se puede modificar.','text-center','bg-teal-300'); }?>
							</div>
						</div>
					</div>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>
	</div>
<?php
	}else{
		echo cajaAlerta('SIN DATOS','text-center textoNegrita','No se ha encontrado, ningún registro en el sistema de partidas.','text-center','bg-teal-300');
	}
?>