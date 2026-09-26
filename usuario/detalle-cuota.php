<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES
	/////////////////////////////////////////////////////////////////////
	$codigoCuota   =$_GET['codigoCuota'];
	$opcion        =$_GET['opcion'];
	$pagina        =$_GET['pagina'];
	$conceptoCuota =$_GET['conceptoCuota'];
	$ruta          ='../';

	/////////////////////////////////////////////////////////////////////
	/// MENU HEADER PAGINA
	/////////////////////////////////////////////////////////////////////
	$menuTop="menu-cuota.php";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="CUOTA &rarr; ".$conceptoCuota;
	$menuActual="listaCuotas";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	include($ruta.'template/header.tpl');
	$existeCuota=infoCuota($idJuntaDirectiva,$codigoCuota,'verifica');
	if($existeCuota){
		$fechaHoy=infoTiempo('fechaHoy');
?>

		<?php if($opcion=="detalles"){ ?>
			<?php
				$conexion=conexionDB();
				$sql="SELECT conceptoCuota, montoCuota, codigoCuenta, fechaPago, observacion, fecha, hora, usuario FROM sm_mod_cuotas WHERE codigoCuota='$codigoCuota'";
				$row=mysqli_query($conexion,$sql);
				$dato=mysqli_fetch_array($row);
				$conceptoCuota =texto($dato['conceptoCuota']);
				$montoCuota    =$dato['montoCuota'];
				$codigoCuenta  =$dato['codigoCuenta'];
				$fechaPago     =$dato['fechaPago'];
				$observacion   =$dato['observacion'];
				$fecha         =$dato['fecha'];
				$hora          =$dato['hora'];
				$usuario       =$dato['usuario'];
				$aforo         =infoCuota($idJuntaDirectiva,$codigoCuota,'aforo');
				$pagaron       =infoCuota($idJuntaDirectiva,$codigoCuota,'pagaron');
				$deben         =infoCuota($idJuntaDirectiva,$codigoCuota,'deben');
				$totalCuotas   =infoCuota($idJuntaDirectiva,$codigoCuota,'totalCuotas');
				$totalPagados  =infoCuota($idJuntaDirectiva,$codigoCuota,'totalPagados');
				$totalDeben    =infoCuota($idJuntaDirectiva,$codigoCuota,'totalDeben');
				$totalPorpagar =$totalCuotas-$totalPagados;
				$urlPaginacion ='detalle-cuota.php?codigoCuota='.$codigoCuota.'&opcion=detalles';
				$pagina        =$_GET['pagina'];

				if ($codigoCuenta){
					$codigoBanco     =infoCuentas($codigoCuenta,'','codigoBanco');
					$banco           =infoBancos($codigoBanco,'detalleEntidad');
					$detalleCuenta   =infoCuentas($codigoCuenta,'','detalleCuenta');
					$cuentaBancaria  =infoCuentas($codigoCuenta,'','numeroCuenta');
					$infoCuentaBanco =$banco.' - '.texto($detalleCuenta).' ('.$cuentaBancaria.')';
				}else{
					$infoCuentaBanco="SIN CUENTA BANCARIA ASIGNADA";
				}

				if($pagaron>0){ $nPagaron=ceros($pagaron,4); }else{ $nPagaron='----'; }
				if($deben>0){ $nDeben=ceros($deben,4); }else{ $nDeben='----'; }
				if($totalPagados>0){ $mTotalPagado='S/. '.moneda($totalPagados); }else{ $mTotalPagado='----'; }
				if($totalDeben>0){ $mTotalDeben='S/. '.moneda($totalDeben); }else{ $mTotalDeben='----'; }
				cerrarDB();
			?>

			<?php if($pagaron==0){ ?>
				<script type="text/javascript">
					$(document).ready(function(){
						///////////////////////////////////////////////////
						/// CAMBIO COMBO SELECT
						///////////////////////////////////////////////////
						$('.seleccionar').select2();
						
						///////////////////////////////////////////////////
						/// CONFIGURACION DE DATEPICKER
						///////////////////////////////////////////////////
						$(".fecha").datepicker({
							showButtonPanel: true,
							format: 'dd/mm/yyyy',
							altField: "#infoFecha",
							altFormat: "DD, d MM, yy",
							minDate: "-1D",
							maxDate: "+3M"
						});

						///////////////////////////////////////////////////
						/// BT ALMACENAR CAMBIOS EN ACTIVIDAD
						///////////////////////////////////////////////////
						$("button#bt_almacenar_actividad").click(function(){
							var ruta   ="<?= $ruta ?>";
							var datos  = $('#form_cuotas').serialize();
							var valida = $('.form_valida').valid();
							if(valida){
								$.ajax({
									type: "POST",
									url: ruta+'php/mantenimiento-cuotas.php',
									data: datos,
									dataType:'json',
									success: function(respuesta){
										if(respuesta.mensaje=="CUOTA_ALMACENADA"){
											new PNotify({title: 'ACTUALIZADO', text: 'Los datos fueron actualizados correctamente.', addclass: 'bg-success'});
											location.reload();
										}
										if(respuesta.mensaje=="ERROR_CUOTA_ALMACENADA"){
											new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
										}
									}
								});
							}
						});

						///////////////////////////////////////////////////
						/// VALIDAR FORMULARIO
						///////////////////////////////////////////////////
						var validator = $(".form_valida").validate({
							errorClass: 'validation-error-label',
							highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
							unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
							validClass: "validation-valid-label",
							rules: {
								conceptoCuota: { required: true },
					 			fechaPago: { required: true },
					 			montoCuota: { number: true, min: 5 },
					 			codigoCuenta: { required: true },
					 			observacion: { required: true },
							},
							messages: {
								conceptoCuota: { required: "Concepto de Cuota", },
								fechaPago: { required: "Fecha de pago", },
								montoCuota: { required: "Monto de Cuota", min: "Minimo S/. 5", },
								codigoCuenta: { required: "Seleccione Cuenta de banco para cuota", },
								observacion: { required: "Detalles adicionales de cuota", },
							}
						});
					});
				</script>

				<div id="editarCuota" class="modal fade">
					<div class="modal-dialog modal-full">
						<div class="modal-content">
							<div class="modal-header bg-slate-600">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h6 class="modal-title textoNegrita">MODIFICAR INFORMACION DE CUOTA</h6>
							</div>
							<div class="modal-body">
								<form id="form_cuotas" class="form_valida">
									<input type="hidden" name="codigoCuota" value="<?= $codigoCuota ?>">
									<input type="hidden" name="operacion" value="EDITA_CUOTA">
									<div class="row">
										<div class="col-md-5">
											<div class="row">
												<div class="col-md-12">
													<div class="form-group">
														<label class="text-brown textoNegrita">Concepto de cuota</label>
														<textarea rows="3" cols="5" id="conceptoCuota" name="conceptoCuota" required="required" class="form-control textoMayuscula" onblur="mayusculas(event, this)" tabindex="1" autofocus><?= $conceptoCuota ?></textarea>
													</div>
												</div>

												<div class="col-md-6 col-xs-6">
													<div class="form-group">
														<label class="text-brown textoNegrita">Monto de cuota S/.</label>
														<input type="text" id="montoCuota" name="montoCuota" required="required" class="form-control input-lg textoMayuscula" value="<?= $montoCuota ?>" tabindex="2">
													</div>
												</div>

												<div class="col-md-6 col-xs-6">
													<div class="form-group">
														<label class="text-brown textoNegrita">Fecha de pago</label>
														<input type="text" id="fechaPago" name="fechaPago" required="required" class="form-control input-lg fecha textoMayuscula" value="<?= infoFecha($fechaPago,'resultados') ?>" tabindex="3">
													</div>
												</div>

												<div class="col-md-12">
													<div class="form-group">
														<label class="text-brown textoNegrita">Cuenta de pago para cuotas</label>
														<select id="codigoCuenta" name="codigoCuenta" required="required" class="seleccionar select-lg">
															<option value="" selected>SELECCIONE DE BANCO</option>
															<?php
																$sql="SELECT codigoBanco, codigoCuenta, numeroCuenta, detalle FROM sm_banco_cuentas WHERE estado='ACT'";
																$rs=mysqli_query($conexion,$sql);
																while($datos=mysqli_fetch_array($rs)){
																	$codigoBanco  =$datos['codigoBanco'];
																	$codCuenta    =$datos['codigoCuenta'];
																	$numeroCuenta =$datos['numeroCuenta'];
																	$detalle      =$datos['detalle'];
																	$infoCuenta   =infoBancos($codigoBanco,'detalleEntidad').' - '.texto($detalle).' ('.$numeroCuenta.')';
																	if($codCuenta==$codigoCuenta){ 
																		echo '<option value="'.$codCuenta.'" selected>'.$infoCuenta.'</option>';
																	}else{
																		echo '<option value="'.$codCuenta.'">'.$infoCuenta.'</option>';
																	}
																}
															?>
														</select>
													</div>
												</div>

												<div class="col-md-12">
													<div class="form-group">
														<button type="button" id="bt_almacenar_actividad" class="btn btn-lg btn-success btn-block">Almacenar Cuota</button>
													</div>
												</div>
											</div>
										</div>
										<div class="col-md-7">
											<div class="form-group">
												<label class="text-brown textoNegrita">Detalles de cuota</label>
												<textarea cols="18" rows="14" id="observacion" name="observacion" class="form-control textoMayuscula" required="required" onblur="mayusculas(event, this)"><?= $observacion ?></textarea>
											</div>
										</div>
									</div>
								</form>	
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="panel">
				<div class="panel-heading bg-teal">
					<h6 class="panel-title textoNegrita">DETALLES DE CUOTA
					<?php if($pagaron==0){ ?>
						<div class="heading-elements">
							<button type="button" data-target="#editarCuota" data-toggle="modal" class="btn bg-teal heading-btn">EDITAR <?= $rotActMay ?></button>
						</div>
					<?php } ?>
				</div>
				<div class="panel-body">
					<ul class="list-group">
						<li class="list-group-item text-danger"><?= $conceptoCuota ?></li>
						<li class="list-group-item"><span class="textoNegrita">MONTO DE CUOTA</span> <span class="pull-right text-slate">S/. <?= moneda($montoCuota) ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">CUENTA DE PAGOS</span> <span class="pull-right text-slate"><?= $infoCuentaBanco ?></span></li>
						<li class="list-group-item"><span class="textoNegrita textoMayuscula">FECHA DE PAGO</span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaPago,'corta') ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">CUOTA GENERADA POR</span> <span class="pull-right text-slate textoMayuscula"><?= texto(datoUsuario($usuario,'nombreCorto')).' - '.infoFecha($fecha,'normal').' - '.horaCorta($hora) ?> Hrs.</span></li>
					</ul>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-8">
					<div class="panel">
						<div class="panel-heading bg-info"><h6 class="panel-title textoNegrita">ESTADISTICAS DE CUOTA</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-lg-4">
									<ul class="list-group">
										<li class="list-group-item"><span class="textoNegrita">SOCIOS</span> <span class="pull-right text-warning"><?= ceros($aforo,4) ?></span></li>
									</ul>
								</div>
								<div class="col-lg-4">
									<ul class="list-group">
										<li class="list-group-item"><span class="textoNegrita">PAGARON</span> <span class="pull-right text-warning"><?= $nPagaron ?></span></li>
									</ul>
								</div>
								<div class="col-lg-4">
									<ul class="list-group">
										<li class="list-group-item"><span class="textoNegrita">DEBEN</span> <span class="pull-right text-warning"><?= $nDeben ?></span></li>
									</ul>
								</div>
							</div>
							<ul class="list-group">
								<li class="list-group-item"><span class="textoNegrita">MONTO TOTAL EN CUOTAS</span> <span class="pull-right text-warning"><?= 'S/. '.moneda($totalCuotas) ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">TOTAL CUOTAS PAGADAS</span> <span class="pull-right text-warning"><?= $mTotalPagado ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">TOTAL POR PAGAR</span> <span class="pull-right text-warning"><?= $mTotalDeben ?></span></li>
							</ul>
						</div>
					</div>
				</div>

				<div class="col-lg-4">
					<div class="panel panel-flat">
						<div class="list-group list-group-borderless no-padding-top">
							<?php if($rolUsuario=='ADM' || $rolUsuario=='JDP'){ ?>
								<a href="#actualizarDB" data-toggle="modal" class="list-group-item"><i class="icon-reload-alt"></i> ACTUALIZAR BASE DE DATOS DE SOCIOS CON CUOTA</a>
							<?php } ?>
							<?php if($pagaron==0){ ?><a href="#editarCuota" data-toggle="modal" class="list-group-item"><i class="icon-pencil5"></i> EDITAR DATOS DE CUOTA</a><?php } ?>
							<a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=pagados&conceptoCuota=<?= $tituloPagina ?>" class="list-group-item"><i class="icon-info22"></i> LISTA DE SOCIOS CON CUOTAS PAGADAS</a>
							<a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=deben&conceptoCuota=<?= $tituloPagina ?>" class="list-group-item"><i class="icon-info22"></i> LISTA DE SOCIOS CON DEUDA POR CUOTA</a>
							<?php if($rolUsuario=='ADM'){ ?>
								<a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=deben&operaciones=<?= $tituloPagina ?>" class="list-group-item"><i class="icon-versions"></i> BITACORA DE OPERACIONES EN CUOTA</a>
							<?php } ?>
							<?php if($rolUsuario=='ADM' || $rolUsuario=='JDP'){ ?>
								<a href="generar-cuota.php" class="list-group-item"><i class="icon-cash3"></i> GENERAR NUEVA CUOTA PARA SOCIOS</a>
							<?php } ?>
						</div>
					</div>
					
					<!-- MODAL ACTUALIZAR DB SOCIOS -->
					<script type="text/javascript">
						$(document).ready(function(){
							///////////////////////////////////////////////////
							/// MODULO DE PAGO
							///////////////////////////////////////////////////
							$('#actualizarDB').on('shown.bs.modal', function(){
								var ruta           ='../';
								var opcion         ='cuota';
								var codigoConcepto ='<?= $codigoCuota ?>';
								var datos          ='codigoConcepto='+codigoConcepto+'&opcion='+opcion;
								$('#modulo-actualizar').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>').fadeIn(1000).delay(1000);
								$("#modulo-actualizar").fadeIn("slow").load('modulo/actualizar-socios.php?'+datos).fadeIn(1000).delay(1000);
							});
						});
					</script>
					<div id="actualizarDB" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-brown">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">ACTUALIZAR BASE DE DATOS DE SOCIOS</h6>
								</div>
								<div class="modal-body bg-modal">
									<div id="modulo-actualizar"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if($opcion=="pagados"){ ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 9 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-pagados').DataTable({ 'pageLength': 20 });
					$('.tabla-pagados tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

					///////////////////////////////////////////////////
					/// TOOLTIP
					///////////////////////////////////////////////////
					$('[data-popup="tooltip"]').tooltip();
				});
			</script>

			<?php
				$pagaron      =infoCuota($idJuntaDirectiva,$codigoCuota,'pagaron');
				$totalPagados =infoCuota($idJuntaDirectiva,$codigoCuota,'totalPagados');
			?>

			<div class="panel">
				<div class="panel-heading bg-slate"><h6 class="panel-title">CUOTAS PAGADAS</h6></div>
				<div class="totalMonto">
					<div class="row">
						<div class="col-sm-6">
							<ul class="list-group">
								<li class="list-group-item"><span>PAGARON</span> <span class="pull-right text-danger textoNegrita"><?= ceros($pagaron,4) ?> SOCIOS</span></li>
							</ul>
						</div>
						<div class="col-sm-6">
							<ul class="list-group">
								<li class="list-group-item"><span>MONTO RECAUDADO</span> <span class="pull-right text-danger textoNegrita">S/. <?= moneda($totalPagados) ?></span></li>
							</ul>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-pagados">
							<thead>
								<tr class="success">
									<th class="text-center">#</th>
									<th class="text-center">CODIGO SOCIO</th>
									<th class="text-left">NOMBRE DE SOCIO</th>
									<th class="text-center">DNI</th>
									<th class="text-center">LOTES</th>
									<th class="text-center">CUOTA</th>
									<th class="text-center">TOTAL</th>
									<th class="text-center">ESTADO</th>
									<th class="text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$conexion=conexionDB();
									$sql="SELECT sm_mod_cuotas_socios.codigoCuota AS codigoCuota, sm_mod_cuotas_socios.codigoSocio AS codigoSocio, sm_mod_cuotas_socios.lotes AS lotes, sm_mod_cuotas_socios.montoCuota AS montoCuota, sm_mod_cuotas_socios.montoPago AS montoPago, sm_mod_cuotas_socios.estadoPago AS estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_cuotas_socios, sm_socios WHERE codigoCuota='$codigoCuota' AND estadoPago='SP' AND sm_mod_cuotas_socios.codigoSocio=sm_socios.codigoSocio ORDER BY sm_socios.apPaterno ASC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$codigoCuota  =$n['codigoCuota'];
										$codigoSocio  =$n['codigoSocio'];
										$lotes        =$n['lotes'];
										$montoCuota   =$n['montoCuota'];
										$montoPago    =$n['montoPago'];
										$estadoPago   =$n['estadoPago'];
										$dni          =$n['dni'];
										$nombre       =$n['nombre'];
										$apPaterno    =$n['apPaterno'];
										$apMaterno    =$n['apMaterno'];
										$nombre       =texto($apPaterno.' '.$apMaterno.' '.$nombre);
										$programado   =conceptoProgramado($codigoSocio,$codigoCuota);
										$conceptoPago ="CUO";
										
										if($estadoPago=="NP"){ $estado='<span class="label label-danger">PENDIENTE</span>'; }
										if($estadoPago=="SP"){ $estado='<span class="label label-success">PAGADO</span>'; }
											
										if($programado>=2){
											$verificaPago   =verificaPagosProgramados($codigoCuota,$codigoSocio,$montoPago);
											$estado='<span class="label label-success">'.ceros($programado,2).' PAGOS</span>';
											$pagoProgramado ="SI";

										}else{
											$verificaPago   =verificaPago($codigoCuota,$codigoSocio,$montoPago);
											$estado='<span class="label label-success">PAGADO</span>';
											$pagoProgramado ="NO";
										}

										if($verificaPago=="OK"){ $caja='<span class="label label-info">CAJA</span>'; $boton='<li><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>'; }
										if($verificaPago=="ERROR"){ $caja='<span class="label label-warning">ERROR</span>'; $boton='<li class="disabled"><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>'; }
								?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoSocio ?></td>
									<td class="text-left"><?= $nombre ?></td>
									<td class="text-center"><?= $dni ?></td>
									<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-right"><?= 'S/. '.moneda($montoCuota) ?></td>
									<td class="text-right"><span class="text-danger textoNegrita"><?= 'S/. '.moneda($montoPago) ?></span></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center">
										<div class="btn-group">
											<button type="button" class="btn btn-icon btn-xs bg-brown dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=detalles"><i class="icon-user-check"></i> Ver perfil de socio</a></li>
												<?= $boton ?>
											</ul>
										</div>
									</td>
								</tr>
								<!-- MODAL INFORMACIO DE PAGO DE CUOTA -->
								<?php if($pagoProgramado=="SI"){ ?>
									<script type="text/javascript">
										$(document).ready(function(){
											///////////////////////////////////////////////////
											/// MODULO DE PAGO
											///////////////////////////////////////////////////
											var ruta           ='../';
											var opcion         ='pagar_fechas_programadas';
											var codigoSocio    ='<?= $codigoSocio ?>';
											var conceptoPago   ='<?= $conceptoPago ?>';
											var codigoConcepto ='<?= $codigoCuota ?>';
											var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto;
											$('#infoPago-'+codigoSocio).on('shown.bs.modal', function(){
												$('#modulo_fechas_pago_'+codigoSocio).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
												$("#modulo_fechas_pago_"+codigoSocio).fadeIn("slow").load('modulo/modulo-pagos.php?'+datos).fadeIn(1000).delay(1000);
											});
										});
									</script>

									<div id="infoPago-<?= $codigoSocio ?>" class="modal fade">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header bg-brown">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title textoNegrita">INFORMACION DE PAGO</h6>
												</div>
												<div class="modal-body">
													<div class="row mb-20">
														<div class="col-sm-12">
															<ul class="list-group">
																<li class="list-group-item"><span class="textoNegrita">DETALLE</span> <span class="pull-right text-danger"><?= texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota')) ?></span></li>
															</ul>
														</div>
														<div class="col-sm-4">
															<ul class="list-group">
																<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= texto(infoPago('','',$concepto,'conceptoPago')) ?></span></li>
															</ul>
														</div>
														<div class="col-sm-4">
															<ul class="list-group">
																<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($montoCuota) ?></span></li>
															</ul>
														</div>
														<div class="col-sm-4">
															<ul class="list-group">
																<li class="list-group-item"><span class="textoNegrita">FECHAS DE PAGO</span> <span class="pull-right text-danger"><?= ceros($programado,2) ?></span></li>
															</ul>
														</div>
													</div>
													<div id="modulo_fechas_pago_<?= $codigoSocio ?>"></div>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>
								<?php if($pagoProgramado=="NO"){ ?>
									<div id="infoPago-<?= $codigoSocio ?>" class="modal fade">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header bg-brown">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title textoNegrita">INFORMACION DE PAGO</h6>
												</div>
												<div class="modal-body">
													<?php
														$sql="SELECT fechaOperacion, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND  codigoConcepto='$codigoCuota'";
														$infopago=mysqli_query($conexion,$sql);
														$dato=mysqli_fetch_array($infopago);
														$fechaOperacion  =$dato['fechaOperacion'];
														$concepto        =$dato['concepto'];
														$tipoDocumento   =$dato['tipoDocumento'];
														$nroDocumento    =$dato['nroDocumento'];
														$monto           =$dato['monto'];
														$detalleConcepto =$dato['detalleConcepto'];
														$codigoOperacion =$dato['codigoOperacion'];
														$fecha           =$dato['fecha'];
														$hora            =$dato['hora'];
														$usuario         =$dato['usuario'];
													?>
													<div class="row mb-20">
														<div class="col-sm-12">
															<ul class="list-group">
																<li class="list-group-item"><span class="textoNegrita">DETALLE</span> <span class="pull-right text-danger"><?= texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota')) ?></span></li>
															</ul>
														</div>
														<div class="col-sm-6">
															<ul class="list-group">
																<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= texto(infoPago('','',$concepto,'conceptoPago')) ?></span></li>
																<li class="list-group-item"><span class="textoNegrita">DOCUMENTO</span> <span class="pull-right text-danger"><?= infoTipoDOC($tipoDocumento) ?></span></li>
																<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
																<li class="list-group-item"><span class="textoNegrita">INGRESADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
															</ul>
														</div>
														<div class="col-sm-6">
															<ul class="list-group">
																<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($monto) ?></span></li>
																<li class="list-group-item"><span class="textoNegrita">NRO DOCUMENTO</span> <span class="pull-right text-danger"><?= $nroDocumento ?></span></li>
																<li class="list-group-item"><span class="textoNegrita">CODIGO OPERACION</span> <span class="pull-right text-danger"><?= $codigoOperacion ?></span></li>
																<li class="list-group-item"><span class="textoNegrita">INGRESO</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fecha,'larga').' - '.horaCorta($hora) ?></span></li>
															</ul>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>
								<?php $i++; } cerrarDB(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if($opcion=="deben"){ ?>
			<?php
				$pagina        =$_GET['pagina'];
				$deben         =infoCuota($idJuntaDirectiva,$codigoCuota,'deben');
				$totalDeben    =infoCuota($idJuntaDirectiva,$codigoCuota,'totalDeben');
				$urlPaginacion ='detalle-cuota.php?codigoCuota='.$codigoCuota.'&opcion=deben';
			?>

			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 8 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-pagados').DataTable({ 'pageLength': 20 });
					$('.tabla-pagados tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>

			<div class="panel">
				<div class="panel-heading bg-slate"><h6 class="panel-title">CUOTAS NO PAGADAS</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-pagados">
							<thead>
								<tr class="success">
									<th class="text-center">#</th>
									<th class="text-center">CODIGO SOCIO</th>
									<th class="text-left">NOMBRE DE SOCIO</th>
									<th class="text-center">DNI</th>
									<th class="text-center">LOTES</th>
									<th class="text-center">CUOTA</th>
									<th class="text-center">TOTAL</th>
									<th class="text-center">ESTADO</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$conexion=conexionDB();
									$sql="SELECT sm_mod_cuotas_socios.codigoCuota AS codigoCuota, sm_mod_cuotas_socios.codigoSocio AS codigoSocio, sm_mod_cuotas_socios.lotes AS lotes, sm_mod_cuotas_socios.montoCuota AS montoCuota, sm_mod_cuotas_socios.montoPago AS montoPago, sm_mod_cuotas_socios.estadoPago AS estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_cuotas_socios, sm_socios WHERE codigoCuota='$codigoCuota' AND (estadoPago='NP' OR estadoPago='MP') AND sm_mod_cuotas_socios.codigoSocio=sm_socios.codigoSocio ORDER BY sm_socios.apPaterno ASC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$codigoCuota  =$n['codigoCuota'];
										$codigoSocio  =$n['codigoSocio'];
										$lotes        =$n['lotes'];
										$montoCuota   =$n['montoCuota'];
										$montoPago    =$n['montoPago'];
										$estadoPago   =$n['estadoPago'];
										$dni          =$n['dni'];
										$nombre       =$n['nombre'];
										$apPaterno    =$n['apPaterno'];
										$apMaterno    =$n['apMaterno'];
										$nombre       =texto($apPaterno.' '.$apMaterno.' '.$nombre);

										if($estadoPago=="NP"){ $estado='<span class="label label-danger">PENDIENTE</span>'; }
										if($estadoPago=="SP"){ $estado='<span class="label label-success">PAGADO</span>'; }
										if($estadoPago=="MP"){ $estado='<span class="label label-info">PROGRAMADO</span>'; }
								?>
								<tr>
									<td class="text-center"><?php echo ceros(($inicio+1),2) ?></td>
									<td class="text-center"><?= $codigoSocio ?></td>
									<td class="text-left"><?= $nombre ?></td>
									<td class="text-center"><?= $dni ?></td>
									<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-right"><?= 'S/. '.moneda($montoCuota) ?></td>
									<td class="text-right text-danger textoNegrita"><?= 'S/. '.moneda($montoPago) ?></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center">
										<div class="btn-group">
											<button type="button" class="btn btn-icon btn-xs bg-brown dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=detalles" ><i class="icon-user-check"></i> Ver perfil de socio</a></li>
												<?php if($rolUsuario=='ADM' || $rolUsuario=='JDT'){ ?>
													<li><a href="#pagarCuota-<?= $codigoSocio ?>" data-toggle="modal"><i class=" icon-coin-dollar"></i> Pagar monto de cuota</a></li>
												<?php } ?>
											</ul>
										</div>
									</td>
								</tr>
								<!-- MODAL PAGAR CUOTA -->
								<script type="text/javascript">
									$(document).ready(function(){
										///////////////////////////////////////////////////
										/// MODULO DE PAGO
										///////////////////////////////////////////////////
										var codigoSocio    ='<?= $codigoSocio ?>';
										var i='<?= $i ?>';
										$('#pagarCuota-'+codigoSocio).on('shown.bs.modal', function(){
											var ruta           ='../';
											var opcion         ='modulo_pago_monto';
											var codigoSocio    ='<?= $codigoSocio ?>';
											var conceptoPago   ='CUO';
											var codigoConcepto ='<?= $codigoCuota ?>';
											var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto;
											$('#modulo_pago-'+codigoSocio).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
											$("#modulo_pago-"+codigoSocio).fadeIn("slow").load('modulo/modulo-pagos.php?'+datos).fadeIn(1000).delay(1000);
										});
									});
								</script>
								<div id="pagarCuota-<?= $codigoSocio ?>" class="modal fade">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header bg-slate-600">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h6 class="modal-title textoNegrita">PAGO DE CUOTA &rarr; <?= texto($nombre) ?></h6>
											</div>
											<div class="modal-body">
												<div id="modulo_pago-<?= $codigoSocio ?>"></div>
												<div id="modulo_fechas_pago-<?= $codigoSocio ?>"></div>
											</div>
										</div>
									</div>
								</div>
								<?php $i++; } cerrarDB(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if($opcion=="operaciones"){ ?>
			<div class="panel">
				<div class="panel-heading bg-teal"><h6 class="panel-title">REGISTRO DE OPERACIONES PROCESADAS</h6></div>
				<div class="table-responsive">
					<table class="table tabla table-bordered table-hover tabla-aforo">
						<thead>
							<tr class="success">
								<th class="textoNegrita text-center">#</th>
								<th class="textoNegrita text-center">CODIGO SOCIO</th>
								<th class="textoNegrita text-left">PROCESO</th>
								<th class="textoNegrita text-center" colspan="3">DATOS DE REGISTRO</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$conexion=conexionDB();
								$sql="SELECT codigoSocio, proceso, fecha, hora, usuario FROM sm_procesos_cuotas WHERE codigoCuota='$codigoCuota' ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$codigoSocio=$n['codigoSocio'];
									$proceso=$n['proceso'];
									$fecha=$n['fecha'];
									$hora=$n['hora'];
									$usuario=$n['usuario'];
							?>
							<tr>
								<td class="text-center"><?= ceros($i,2) ?></td>
								<td class="text-center"><?= $codigoSocio ?></td>
								<td class="text-left"><?= texto($proceso) ?></td>
								<td class="text-center textoMayuscula"><?= infoFecha($fecha,'normal') ?></td>
								<td class="text-center"><?= horaCorta($hora) ?></td>
								<td class="text-center"><span class="label label-default"><?= texto(datoUsuario($usuario,'nombre')) ?></span></td>
							</tr>
							<?php $i++; } cerrarDB(); ?>
						</tbody>
					</table>
				</div>
			</div>
		<?php } ?>

<?php
	}else{ include($ruta.'template/404-cuota.tpl'); }
	include($ruta.'template/footer.tpl');
?>