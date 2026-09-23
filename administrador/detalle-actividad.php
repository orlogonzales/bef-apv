<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES
	/////////////////////////////////////////////////////////////////////
	$codigoActividad =$_GET['codigoActividad'];
	$opcion          =$_GET['opcion'];
	$tipoActividad   =$_GET['tipoActividad'];
	$temaActividad   =$_GET['temaActividad'];
	$ruta            ='../';
	$documentos      ='../assets/images/docs/';

	if($tipoActividad=="ASA"){
		$rotActMay="ASAMBLEA";
		$rotActMin="asamblea";
		$menuActual="listaAsambleas";
		$rotuloBTpago="asamblea";
	}
	if($tipoActividad=="FAE"){
		$rotActMay="FAENA";
		$rotActMin="faena";
		$menuActual="listaFaenas";
		$rotuloBTpago="faena";
	}

	/////////////////////////////////////////////////////////////////////
	/// MENU HEADER PAGINA
	/////////////////////////////////////////////////////////////////////
	$menuTop="menu-actividades.php";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina=$rotActMay." &rarr; ".$temaActividad;

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	include($ruta.'template/header.tpl');

	$existeACT=infoActividad($idJuntaDirectiva,$codigoActividad,'','verifica');
	if($existeACT){
		$fechaHoy=infoTiempo('fechaHoy');

		/////////////////////////////////////////////////////////////////////
		/// CONECCION CON LA INTERFACE JSON DEL SISTEMA DE VENTAS
		/////////////////////////////////////////////////////////////////////
		$servidor=$_SERVER['HTTP_HOST'];
		if($servidor=='app.bef'){
			$urlSistema="http://{$_SERVER['HTTP_HOST']}/ventas";
			$urlSocios="http://{$_SERVER['HTTP_HOST']}/apv";
		}else{
			$urlSistema="https://{$_SERVER['HTTP_HOST']}/ventas";
			$urlSocios="https://{$_SERVER['HTTP_HOST']}/apv";
		}
?>

	<?php if($opcion=="detalles"){ ?>
		<?php
			$conexion=conexionDB();
			$sql="SELECT codigoActividad, tipoActividad, temaActividad, contenidoActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta, codigoCuenta, fecha, hora, usuario FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'";
			$row=mysqli_query($conexion,$sql);
			$dato=mysqli_fetch_array($row);
			$temaActividad            =$dato['temaActividad'];
			$contenidoActividad       =$dato['contenidoActividad'];
			$fechaActividad           =$dato['fechaActividad'];
			$horaActividad            =$dato['horaActividad'];
			$lugarActividad           =$dato['lugarActividad'];
			$mTardanza                =$dato['mTardanza'];
			$mFalta                   =$dato['mFalta'];
			$codigoCuenta             =$dato['codigoCuenta'];
			$fecha                    =$dato['fecha'];
			$hora                     =$dato['hora'];
			$usuario                  =$dato['usuario'];
			$aforo                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','aforo');
			$asistio                  =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
			$tarde                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','tarde');
			$justifico                =infoActividad($idJuntaDirectiva,$codigoActividad,'','justifico');
			$falto                    =infoActividad($idJuntaDirectiva,$codigoActividad,'','falto');
			$mTardanzas               =infoActividad($idJuntaDirectiva,$codigoActividad,'','mtarde');
			$mFaltas                  =infoActividad($idJuntaDirectiva,$codigoActividad,'','mFalta');
			$mJUS                     =infoActividad($idJuntaDirectiva,$codigoActividad,'','mJUS');
			$totalMultas              =$mTardanzas+$mFaltas;
			$multasPagadas            =infoActividad($idJuntaDirectiva,$codigoActividad,'','mPagadas');
			$porPagar                 =$totalMultas-$multasPagadas;
			$totalNeto                =$mJUS+$totalMultas;
			$transportado             =infoActividad($idJuntaDirectiva,$codigoActividad,'','archivoACT');
			$nroSociosPagaron         =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosPagaron');
			$nroSociosFechasProgramas =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosFechasProgramas');
			$nroSociosDeben           =infoActividad($idJuntaDirectiva,$codigoActividad,'','nroSociosDeben');

			if ($codigoCuenta){
				$codigoBanco     =infoCuentas($codigoCuenta,'','codigoBanco');
				$banco           =infoBancos($codigoBanco,'detalleEntidad');
				$detalleCuenta   =infoCuentas($codigoCuenta,'','detalleCuenta');
				$cuentaBancaria  =infoCuentas($codigoCuenta,'','numeroCuenta');
				$infoCuentaBanco =$banco.' - '.texto($detalleCuenta).' ('.$cuentaBancaria.')';
			}else{
				$infoCuentaBanco="SIN CUENTA BANCARIA ASIGNADA";
			}

			if($asistio>0){ $asistio=ceros($asistio,4); }else{ $asistio='----'; }
			if($tarde>0){ $tarde=ceros($tarde,4); }else{ $tarde='----'; }
			if($justifico>0){ $justifico=ceros($justifico,4); }else{ $justifico='----'; }
			if($falto>0){ $falto=ceros($falto,4); }else{ $falto='----'; }
			if($mTardanzas>0){ $mTardanzas='S/. '.moneda($mTardanzas,4); }else{ $mTardanzas='----'; }
			if($mFaltas>0){ $mFaltas='S/. '.moneda($mFaltas,4); }else{ $mFaltas='----'; }
			if($mJUS>0){ $mJUS='S/. - '.moneda($mJUS,4); }else{ $mJUS='----'; }
			if($totalMultas>0){ $totalMultas='S/. '.moneda($totalMultas,4); $totalNeto='S/. '.moneda($totalNeto); }else{ $totalMultas='----'; $totalNeto='----'; }
			if($multasPagadas>0){ $multasPagadas='S/. '.moneda($multasPagadas,4); }else{ $multasPagadas='----'; }
			if($porPagar>0){ $porPagar='S/. '.moneda($porPagar,4); }else{ $porPagar='----'; }
		?>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CAMBIO COMBO SELECT
				///////////////////////////////////////////////////
				$('.seleccionar').select2();
				
				///////////////////////////////////////////////////
				/// CAMBIO DE ESTILO DE INPUT FILE
				///////////////////////////////////////////////////
				$(".inputSubir").uniform({
					wrapperClass: 'bg-brown',
					fileButtonHtml: '<i class="icon-plus2"></i>'
				});

				///////////////////////////////////////////////////
				/// CONFIGURACION DE DATEPICKER
				///////////////////////////////////////////////////
				$(".fechasACT").datepicker({
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
					var ruta            ="<?= $ruta ?>";
					var datos           =$('#form_actividades').serialize();
					var valida          =$('.form_valida_actividad').valid();
					if(valida){
						$.ajax({
							type: "POST",
							url: ruta+'php/mantenimiento-actividades.php',
							data: datos,
							dataType:'json',
							success: function(respuesta){
								if(respuesta.mensaje=="ACTIVIDAD_MODIFICADA"){
									new PNotify({title: 'ACTUALIZADO', text: 'Los datos fueron actualizados correctamente.', addclass: 'bg-success'});
									location.reload();
								}
								if(respuesta.mensaje=="ERROR_ACTIVIDAD_MODIFICADA"){
									new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
								}
							}
						});
					}
				});

				///////////////////////////////////////////////////
				/// BT ALMACENAR JUSTIFICACION
				///////////////////////////////////////////////////
				$("button#bt_almacenar_justificacion").click(function(){
					var ruta            ="<?= $ruta ?>";
					var operacion       ='REGISTRA_JUSTIFICACION';
					var codigoActividad ='<?= $codigoActividad ?>';
					var datos           =new FormData($("#form_justificaciones")[0]);
					var codigoSocio     =$('select#codigoSocio').val();
					var razon           =$('select#razon').val();
					var observacion     =$('textarea#observacion').val();
					var tipoActividad   ="<?= $tipoActividad ?>";
					var variables       ='?codigoActividad='+codigoActividad+'&codigoSocio='+codigoSocio+'&razon='+razon+'&observacion='+observacion+'&tipoActividad='+tipoActividad+'&operacion='+operacion
					var valida          =$('.form_valida').valid();
					//if(codigoSocio){ }else{ $("#codigoSocio").select2("open"); }
					if(valida){
						$.ajax({
							url: ruta+'php/mantenimiento-actividades.php'+variables,
							type: "POST",
							dataType:'json',
							data: datos,
							contentType: false,
							processData: false,
							beforeSend: function(){
								$('#formulario').hide();
								$('#procesando').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>PROCESANDO JUSTIFICACION</div></div>').fadeIn(1000).delay(1000);
							},
							success: function(respuesta){
								if(respuesta.mensaje=="PROCESADO"){
									new PNotify({title: 'CONFIRMACION', text: 'Justificación procesada.', addclass: 'bg-success'});
									location.reload();
								}
								if(respuesta.mensaje=="ERRORFILE"){
									$('#formulario').show();
									$('#procesando').hide();
									new PNotify({title: 'ADVERTENCIA', text: 'La imagen seleccionada tiene que ser en formato JPG o ser menor que 400Kb.', addclass: 'bg-warning'});
								}
								if(respuesta.mensaje=="ERRORSERVER"){
									$('#formulario').show();
									$('#procesando').hide();
									new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
								}
							}
						});
					}
				});

				///////////////////////////////////////////////////
				/// COMBO DINAMICO DE CUENTAS DE BANCO
				///////////////////////////////////////////////////
				$("select#codigoSocio").change(function(event){
					var ruta            ="<?= $ruta ?>";
					var operacion       ='CONSIGUE_RAZON';
					var codigoActividad ='<?= $codigoActividad ?>';
					var codigoSocio     =$("select#codigoSocio").val();
					var datos           ='codigoSocio='+codigoSocio+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-actividades.php',
						data: datos,
						dataType:'json',
						success: function(respuesta){
							$('select#razon option[value='+respuesta.razon+']').attr('selected','selected');
							$("textarea#observacion").focus();
						}
					});
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
						codigoSocio: { required: true },
			 			razon: { required: true },
			 			observacion: { required: true },
					},
					messages: {
						codigoSocio: { required: "Seleccione socio", },
						razon: { required: "Seleccione razon", },
						observacion: { required: "Observaciones de justificación", },
					}
				});

				var validator = $(".form_valida_actividad").validate({
					errorClass: 'validation-error-label',
					highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
					unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
					validClass: "validation-valid-label",
					rules: {
						temaActividad: { required: true },
						lugarActividad: { required: true },
						horaActividad: { required: true },
						codigoCuenta: { required: true },
			 			mTardanza: { number: true, min: 10 },
						mFalta: { number: true, min: 10 },
						contenidoActividad: { required: true },
					},
					messages: {
						temaActividad: { required: "Tema de <?= $rotuloACTmi ?>", },
						lugarActividad: { required: "Lugar de <?= $rotuloACTmi ?>", },
						fechaActividad: { required: "Fecha de <?= $rotuloACTmi ?>", },
						horaActividad: { required: "Hora de <?= $rotuloACTmi ?>", },
						codigoCuenta: { required: "Seleccione Cuenta de banco <?= $rotuloACTmi ?>", },
						mTardanza: { required: "Multa por tardanza", number: "Solo numeros", min: "minimo S/. 10", },
						mFalta: { required: "Multa por inasistencia", number: "Solo numeros", min: "minimo S/. 10", },
						contenidoActividad: { required: "Detalles adicionales...", },
					}
				});

				
			});
		</script>

		<?php if($transportado==""){ ?>
			<div id="editarActividad" class="modal fade">
				<div class="modal-dialog modal-full">
					<div class="modal-content">
						<div class="modal-header bg-slate-600">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h6 class="modal-title textoNegrita">MODIFICAR INFORMACION DE <?= $rotActMay ?></h6>
						</div>
						<div class="modal-body">
							<form id="form_actividades" class="form_valida_actividad">
								<input type="hidden" name="tipoActividad" value="<?= $tipoActividad ?>">
								<input type="hidden" name="codigoActividad" value="<?= $codigoActividad ?>">
								<input type="hidden" name="operacion" value="EDITA_ACTIVIDAD">
								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<input type="text" id="temaActividad" name="temaActividad" required="required" class="form-control input-lg textoMayuscula" value="<?= $temaActividad ?>" onblur="mayusculas(event, this)" tabindex="1" autofocus>
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<input type="text" id="lugarActividad" name="lugarActividad" required="required" class="form-control input-lg textoMayuscula" value="<?= $lugarActividad ?>" onblur="mayusculas(event, this)" tabindex="2">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-5">
										<div class="row">
											<div class="col-md-6 col-xs-6">
												<div class="form-group">
													<label class="text-brown textoNegrita">Fecha de <?= $rotuloACTmi ?></label>
													<input type="text" id="fechaActividad" name="fechaActividad" required="required" class="form-control input-lg fechasACT textoMayuscula" value="<?= infoFecha($fechaActividad,'resultados') ?>" tabindex="3">
												</div>
											</div>

											<div class="col-md-6 col-xs-6">
												<div class="form-group">
													<label class="text-brown textoNegrita">Hora de <?= $rotuloACTmi ?></label>
													<select id="horaActividad" name="horaActividad" required="required" class="seleccionar select-lg">
														<option value="" selected>SELECCIONE HORA</option>
														<?php
															$conexion=conexionDB();
															$sql="SELECT horas FROM sm_a_horas";
															$rs=mysqli_query($conexion,$sql);
															while($datos=mysqli_fetch_array($rs)){
																if($datos[0]==$horaActividad){
																	echo '<option value="'.$datos[0].'" selected>'.$datos[0].'</option>';
																}else{
																	echo '<option value="'.$datos[0].'">'.$datos[0].'</option>';
																}
															}
														?>
													</select>
												</div>
											</div>

											<div class="col-md-12">
												<div class="form-group">
													<label class="text-brown textoNegrita">Cuenta de pago <?= $rotuloACTmi ?></label>
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

											<div class="col-md-6 col-xs-6">
												<div class="form-group">
													<label class="text-brown textoNegrita">Multa por Tardanza</label>
													<input type="text" id="mTardanza" name="mTardanza" required="required" class="form-control input-lg" value="<?= $mTardanza ?>" tabindex="5">
												</div>
											</div>

											<div class="col-md-6 col-xs-6">
												<div class="form-group">
													<label class="text-brown textoNegrita">Multa por Inasistencia</label>
													<input type="text" id="mFalta" name="mFalta" required="required" class="form-control input-lg" value="<?= $mFalta ?>" tabindex="6">
												</div>
											</div>

											<div class="col-md-12">
												<div class="form-group">
													<button type="button" id="bt_almacenar_actividad" class="btn btn-lg btn-success btn-block">Almacenar <?= $rotuloACTmi ?></button>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-7">
										<div class="form-group">
											<textarea cols="18" rows="14" id="contenidoActividad" name="contenidoActividad" required="required" class="form-control textoMayuscula" onblur="mayusculas(event, this)"><?= $contenidoActividad ?></textarea>
										</div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

		<div class="row">
			<div class="col-lg-7">
				<div class="panel">
					<div class="panel-heading bg-teal">
						<h6 class="panel-title textoNegrita">DETALLES DE <?= $rotActMay ?>
						<?php if($transportado==""){ ?>
							<div class="heading-elements">
								<button type="button" data-target="#editarActividad" data-toggle="modal" class="btn bg-teal heading-btn">EDITAR <?= $rotActMay ?></button>
							</div>
						<?php } ?>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-12">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">TEMA DE <?= $rotActMay ?></span> <span class="pull-right text-slate"><?= texto($temaActividad) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita textoMayuscula">FECHA DE <?= $rotActMay ?></span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaActividad,'corta').' - '.infoHora(horaCorta($horaActividad)) ?></span></li>
								</ul>
							</div>
							<div class="col-sm-7">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">LUGAR</span> <span class="pull-right text-slate"><?= texto($lugarActividad) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">CODIGO</span> <span class="pull-right text-slate"><?= $codigoActividad ?></span></li>
								</ul>
							</div>
							<div class="col-sm-5">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">MULTA TARDANZA</span> <span class="pull-right text-slate">S/. <?= moneda($mTardanza) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">MULTA FALTA</span> <span class="pull-right text-slate">S/. <?= moneda($mFalta) ?></span></li>
								</ul>
							</div>
							<div class="col-sm-12">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">CUENTA DE PAGOS</span> <span class="pull-right text-slate"><?= $infoCuentaBanco ?></span></li>
								</ul>
							</div>
							<div class="col-sm-12">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?> GENERADA POR</span> <span class="pull-right text-slate textoMayuscula"><?= texto(datoUsuario($usuario,'nombreCorto')).' - '.infoFecha($fecha,'normal').' - '.horaCorta($hora) ?> Hrs.</span></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<div class="panel">
					<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">ESTADISTICAS DE <?= $rotActMay ?></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-5">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">AFORO</span> <span class="pull-right text-warning"><?= ceros($aforo,4) ?> <small>Socios</small></span></li>
									<li class="list-group-item"><span class="textoNegrita">ASISTENTES</span> <span class="pull-right text-warning"><?= $asistio ?> <small>Socios</small></span></li>
									<li class="list-group-item"><span class="textoNegrita">TARDANZAS</span> <span class="pull-right text-warning"><?= $tarde ?> <small>Socios</small></span></li>
									<li class="list-group-item"><span class="textoNegrita">INASISTENTES</span> <span class="pull-right text-warning"><?= $falto ?> <small>Socios</small></span></li>
									<li class="list-group-item"><span class="textoNegrita">JUSTIFICADOS</span> <span class="pull-right text-warning"><?= $justifico ?> <small>Socios</small></span></li>
									<li class="list-group-item"><span class="textoNegrita">TOTAL JUSTIFICADOS</span> <span class="pull-right text-warning"><?= $mJUS ?></span></li>
								</ul>
							</div>
							<div class="col-sm-7">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">M. TARDANZAS</span> <span class="pull-right text-warning"><?= $mTardanzas ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">M. FALTAS</span> <span class="pull-right text-warning"><?= $mFaltas ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">TOTAL MULTAS</span> <span class="pull-right text-warning"><?= $totalMultas ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">TOTAL PAGADO</span> <span class="pull-right text-warning"><?= $multasPagadas ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">TOTAL POR PAGAR</span> <span class="pull-right text-warning"><?= $porPagar ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">TOTAL NETO</span> <span class="pull-right text-warning"><?= $totalNeto ?></span></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="panel">
					<div class="panel-heading bg-slate">
						<h6 class="panel-title">REGISTRO DE JUSTIFICACIONES A <?= $rotActMay?></h6>
					</div>
					<div class="panel-body">
						<div id="procesando"></div>
						<div id="formulario">
							<form id="form_justificaciones" class="form_valida" method="POST" enctype="multipart/form-data">
								<div class="row">
									<div class="form-group">
										<select id="codigoSocio" name="codigoSocio" required="required" class="seleccionar select-lg">
											<option value="" selected>SELECCIONE SOCIO</option>
											<?php
												$conexion=conexionDB();
												$sql="SELECT sm_mod_asistencia.multa, sm_socios.codigoSocio, CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) as nombre FROM sm_socios, sm_mod_asistencia WHERE sm_socios.codigoSocio=sm_mod_asistencia.codigoSocio AND sm_mod_asistencia.codigoActividad='$codigoActividad' AND (sm_mod_asistencia.asistio='NO' OR (sm_mod_asistencia.asistio='SI' AND sm_mod_asistencia.retraso>0.15)) ORDER BY nombre ASC";
												$rs=mysqli_query($conexion,$sql);
												while($datos=mysqli_fetch_array($rs)){
													$codigoSocio    =$datos['codigoSocio'];
													$multa          =$datos['multa'];
													$nombre         =$datos['nombre'];
													$infoAsistencia =infoAsistencia($codigoActividad,$codigoSocio);
													$porcentajePago =porcentajePago($codigoSocio,$codigoActividad,$multa);
													if($porcentajePago>0){}else{
														echo '<option value="'.$codigoSocio.'">'.texto($nombre).' &rarr; '.$infoAsistencia.'</option>';
													}
												}
												cerrarDB();
											?>
										</select>
									</div>

									<div class="form-group">
										<select id="razon" name="razon" required="required" class="form-control input-lg">
											<option value="" selected>SELECCIONE RAZON</option>
											<option value="FALTA">INASISTENCIA A <?= $rotActMay ?></option>
											<option value="TARDE">TARDANZA A <?= $rotActMay ?></option>
										</select>
									</div>
									
									<div class="form-group">
										<textarea rows="13" cols="5" id="observacion" name="observacion" required="required" class="form-control textoMayuscula" placeholder="Observaciones..." onblur="mayusculas(event, this)"></textarea>
									</div>
									
									<div class="form-group">
										<div class="input-group">
											<span class="input-group-addon">JUSTIFICACION</span>
											<input type="file" name="documento" id="documento" class="inputSubir input-lg">
										</div>
									</div>
									<button type="button" id="bt_almacenar_justificacion" class="btn btn-lg bg-brown btn-block">ALMACENAR JUSTIFICACION</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if($asistencia>0){ ?>
		<?php if($opcion=="asistentes"){ ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 5, 6, 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-inasistentes').DataTable({ 'pageLength': 20 });
					$('.tabla-inasistentes tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>
			<div class="panel">
				<div class="panel-heading bg-slate-600"><h6 class="panel-title textoNegrita">SOCIOS ASISTENTES A <?= $rotActMay ?></h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-inasistentes">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO SOCIO</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-left">NOMBRE DE SOCIO</th>
									<th class="textoNegrita text-center">DNI</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="textoNegrita text-center">TARDANZA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$conexion=conexionDB();
									$sql="SELECT sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.retraso, sm_mod_asistencia.multa, sm_mod_asistencia.estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_asistencia, sm_socios WHERE sm_mod_asistencia.codigoSocio=sm_socios.codigoSocio AND sm_mod_asistencia.asistio='SI' AND sm_mod_asistencia.codigoActividad='$codigoActividad' ORDER BY sm_socios.apPaterno ASC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$codigoSocio   =$n['codigoSocio'];
										$lotes         =$n['lotes'];
										$temaActividad =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
										$multaTarde    =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
										$multaFalta    =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
										$retraso       =$n['retraso'];
										$infoAsistencia=infoAsistencia($codigoActividad,$codigoSocio);
										$multa         =$n['multa'];
										$estadoPago    =$n['estadoPago'];
										$apPaterno     =$n['apPaterno'];
										$apMaterno     =$n['apMaterno'];
										$nombre        =$n['nombre'];
										$dni           =$n['dni'];
										$nombre        =texto($apPaterno.' '.$apMaterno.' '.$nombre);
										$razonBTpago   ='tardanzas';
										$rotuloCM      ='MULTA TARDANZA A '.$rotActMay;
										$estado        ='<span class="label label-success">ASISTIO</span>';

										if($retraso>0.15){ $infoRetraso='<span class="label label-warning">'.$infoAsistencia.'</span>'; }else{ $infoRetraso=''; }
								?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoSocio ?></td>
									<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-left"><?= texto($nombre) ?></td>
									<td class="text-center"><?= $dni ?></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center"><?= $infoRetraso ?></td>
									<td class="text-center"><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=detalles" class="btn btn-icon btn-xs bg-brown"><i class="icon-user-check"></i></a></td>
								</tr>
								<?php $i++; } cerrarDB(); ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if($opcion=="tardanzas"){ ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 6, 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-inasistentes').DataTable({ 'pageLength': 20 });
					$('.tabla-inasistentes tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>

			<div class="panel">
				<div class="panel-heading bg-slate-600"><h6 class="panel-title textoNegrita">SOCIOS CON TARDANZA A <?= $rotActMay ?></h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-inasistentes">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO SOCIO</th>
									<th class="textoNegrita text-left">NOMBRE DE SOCIO</th>
									<th class="textoNegrita text-center">DNI</th>
									<th class="textoNegrita text-center">MULTA</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-center">TOTAL</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="textoNegrita text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$conexion=conexionDB();
									$sql="SELECT sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.multa, sm_mod_asistencia.estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_asistencia, sm_socios WHERE sm_mod_asistencia.codigoSocio=sm_socios.codigoSocio AND sm_mod_asistencia.asistio='SI' AND sm_mod_asistencia.retraso>0.15 AND sm_mod_asistencia.codigoActividad='$codigoActividad' ORDER BY sm_socios.apPaterno ASC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$codigoSocio    =$n['codigoSocio'];
										$lotes          =$n['lotes'];
										$temaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
										$multaTarde     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
										$multaFalta     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
										$multa          =$n['multa'];
										$estadoPago     =$n['estadoPago'];
										$apPaterno      =$n['apPaterno'];
										$apMaterno      =$n['apMaterno'];
										$nombre         =$n['nombre'];
										$dni            =$n['dni'];
										$nombre         =texto($apPaterno.' '.$apMaterno.' '.$nombre);
										$porcentajePago =porcentajePago($codigoSocio,$codigoActividad,$multa);
										$razonBTpago    ='tardanzas';
										$rotuloCM       ='MULTA TARDANZA A '.$rotActMay;

										$programado   =conceptoProgramado($codigoSocio,$codigoActividad);

										if($estadoPago=="NP" and $programado==0){
											$estado       ='<span class="label label-danger">PENDIENTE</span>';
											$verificaPago ="--";
										}

										if($estadoPago=="MP" and $programado>=2){
											$estado       ='<span class="label label-danger">PENDIENTE | '.ceros($programado,2).' F</span>';
											$verificaPago ="PEN";
											$pagoProgramado ="SI";
										}

										if($estadoPago=="SP" and $programado==0){
											$estado       ='<span class="label label-success">PAGADO</span>';
											$verificaPago =verificaPago($codigoActividad,$codigoSocio,$multa);
											$pagoProgramado ="NO";
										}

										if($estadoPago=="SP" and $programado>=2){
											$estado       ='<span class="label label-success">PAGADO | '.ceros($programado,2).' F</span>';
											$verificaPago =verificaPagosProgramados($codigoActividad,$codigoSocio,$multa);
											$pagoProgramado ="SI";
										}

										if($verificaPago=="--"){ $caja=''; $boton=''; }
										if($verificaPago=="PEN"){ $caja='<span class="label bg-grey">'.$porcentajePago.'%</span>'; $boton='<li class="dosabled"><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>'; }
										if($verificaPago=="OK"){ $caja='<span class="label label-info">CAJA</span>'; $boton='<li><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>'; }
										if($verificaPago=="ERROR"){ $caja='<span class="label label-warning">ERROR</span>'; $boton='<li class="disabled"><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>'; }
										if($estadoPago=="SP"){
											$btonDP='<li><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i>Ver información de pago</a></li>';
										}else{
											$btonDP='<li><a href="#pagarMonto-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-coin-dollar"></i> Pagar multa por '.$razonBTpago.' a '.$rotuloBTpago.'</a></li>';
										}
								?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoSocio ?></td>
									<td class="text-left"><?= texto($nombre) ?></td>
									<td class="text-center"><?= $dni ?></td>
									<td class="text-center text-danger textoNegrita"><?= 'S/. '.moneda($multaTarde) ?></td>
									<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-center text-danger textoNegrita"><?= 'S/. '.moneda($multa) ?></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center">
										<div class="btn-group">
											<button type="button" class="btn btn-icon btn-xs bg-brown dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=detalles" ><i class="icon-user-check"></i> Ver perfil de socio</a></li>
												<?= $btonDP ?>
											</ul>
										</div>
									</td>
									<!-- MODAL PAGAR CUOTA -->
									<script type="text/javascript">
										$(document).ready(function(){
											///////////////////////////////////////////////////
											/// MODULO DE PAGO
											///////////////////////////////////////////////////
											var codigoSocio    ='<?= $codigoSocio ?>';
											$('#pagarMonto-'+codigoSocio).on('shown.bs.modal', function(){
												var ruta           ='../';
												var opcion         ='modulo_pago_monto';
												var conceptoPago   ='<?= $tipoActividad ?>';
												var codigoConcepto ='<?= $codigoActividad ?>';
												var razon          ='TARDE';
												var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&razon='+razon;
												$('#modulo_pago-'+codigoSocio).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
												$("#modulo_pago-"+codigoSocio).fadeIn("slow").load('modulo/modulo-pagos.php?'+datos).fadeIn(1000).delay(1000);
											});
										});
									</script>
									<div id="pagarMonto-<?= $codigoSocio ?>" class="modal fade">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header bg-slate-600">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title textoNegrita">PAGO DE MULTA DE <?= $rotActMay ?> &rarr; <?= texto($nombre) ?></h6>
												</div>
												<div class="modal-body">
													<div id="modulo_pago-<?= $codigoSocio ?>"></div>
													<div id="modulo_fechas_pago-<?= $codigoSocio ?>"></div>
												</div>
											</div>
										</div>
									</div>
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
												var conceptoPago   ='<?= $tipoActividad ?>';
												var codigoConcepto ='<?= $codigoActividad ?>';
												var razon          ='TARDE';
												var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&razon='+razon;
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
																	<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-5">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $rotuloCM ?></span></li>
																</ul>
															</div>
															<div class="col-sm-4">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-3">
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
									<?php } cerrarDB(); ?>
									<?php if($pagoProgramado=="NO"){ ?>
										<div id="infoPago-<?= $codigoSocio ?>" class="modal fade">
											<div class="modal-dialog modal-lg">
												<div class="modal-content">
													<div class="modal-header bg-brown">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h6 class="modal-title textoNegrita">INFORMACION DE PAGO <?= $pagoProgramado ?></h6>
													</div>
													<div class="modal-body">
														<?php
															$conexion=conexionDB();
															$sql="SELECT fechaOperacion, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND  codigoConcepto='$codigoActividad'";
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
															cerrarDB();
														?>
														<div class="row mb-20">
															<div class="col-sm-12">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $rotuloCM ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">DOCUMENTO</span> <span class="pull-right text-danger"><?= infoTipoDOC($tipoDocumento) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">INGRESADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
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
								</tr>
								<?php $i++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if($opcion=="justificaciones"){ ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 5 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-inasistentes').DataTable({ 'pageLength': 20 });
					$('.tabla-inasistentes tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

					///////////////////////////////////////////////////
					/// ELIMINAR ITEM
					///////////////////////////////////////////////////
					$('.eliminar_justificacion').on('click', function() {
						var ruta            ='../';
						var tipoActividad   ='<?= $tipoActividad ?>';
						var codigoActividad ='<?= $codigoActividad ?>';
						var infoSocioRazon  =$(this).attr('id');
						var operacion       ='ELIMINAR_JUSTIFICACION';
						var datos           =infoSocioRazon+'&tipoActividad='+tipoActividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
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
									url: ruta+'php/mantenimiento-actividades.php',
									data: datos,
									dataType:'json',
									success: function(respuesta){
										if(respuesta.mensaje=="JUSTIFICACION_ELIMINADA"){
											new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminadp del sistema.', addclass: 'bg-success'});
											location.reload();
										}
									}
								});
							}
						});
					});
				});
			</script>

			<div class="panel">
				<div class="panel-heading bg-slate-600"><h6 class="panel-title textoNegrita">SOCIOS CON JUSTIFICACION A <?= $rotActMay ?></h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-inasistentes">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO SOCIO</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-left">NOMBRE DE SOCIO</th>
									<th class="textoNegrita text-center">DNI</th>
									<th class="textoNegrita text-center">MULTA</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$conexion=conexionDB();
									$sql="SELECT sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.asistio, sm_mod_asistencia.retraso, sm_mod_asistencia.multa, sm_mod_asistencia.estadoPago, sm_mod_asistencia.observacion, sm_mod_asistencia.documento, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_asistencia, sm_socios WHERE sm_mod_asistencia.codigoSocio=sm_socios.codigoSocio AND sm_mod_asistencia.asistio='JU' AND sm_mod_asistencia.codigoActividad='$codigoActividad' ORDER BY sm_socios.apPaterno ASC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$codigoSocio    =$n['codigoSocio'];
										$lotes          =$n['lotes'];
										$temaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
										$multaTarde     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
										$multaFalta     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
										$asistio        =$n['asistio'];
										$multa          =$n['multa'];
										$retraso        =$n['retraso'];
										$estadoPago     =$n['estadoPago'];
										$apPaterno      =$n['apPaterno'];
										$apMaterno      =$n['apMaterno'];
										$nombre         =$n['nombre'];
										$dni            =$n['dni'];
										$observacion    =$n['observacion'];
										$documento      =$n['documento'];
										$nombre         =texto($apPaterno.' '.$apMaterno.' '.$nombre);
										$infoAsistencia =infoAsistencia($codigoActividad,$codigoSocio);

										if($asistio=="JU" and $multa>0){ 
											$estado='<span class="label label-warning">JUSTIFICADO - '.$infoAsistencia.'</span>';
											$razon="FALTA";
										}
										if($asistio=="JU" and $retraso>0.15){
											$estado='<span class="label label-success">JUSTIFICADO - '.$infoAsistencia.'</span>';
											$razon="TARDE";
										}

										if($documento){ $imagenJUS =$documentos.$documento; }else{ $imagenJUS=''; }
								?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoSocio ?></td>
									<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-left"><?= texto($nombre) ?></td>
									<td class="text-center"><?= $dni ?></td>
									<td class="text-right text-danger textoNegrita"><?= 'S/. - '.moneda($multa) ?></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center">
										<div class="btn-group">
											<button type="button" class="btn btn-icon btn-xs bg-brown dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=detalles" ><i class="icon-user-check"></i> Ver perfil de socio</a></li>
												<li><a href="#justificacion-<?= $codigoSocio ?>" data-toggle="modal"><i class="icon-info22"></i> Ver detalles de justificación</a></li>
												<li><a href="javascript:;" id="codigoSocio=<?= $codigoSocio ?>&razon=<?= $razon ?>" class="eliminar_justificacion" ><i class="icon-trash"></i> Eliminar justificación</a></li>
											</ul>
										</div>
									</td>
								</tr>
								<!-- MODAL JUSTIFICACION -->
								<div id="justificacion-<?= $codigoSocio ?>" class="modal fade">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header bg-slate-600">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h6 class="modal-title textoNegrita">DETALLES DE JUSTIFICACION A <?= $rotActMay ?> &rarr; <?= texto($nombre) ?></h6>
											</div>
											<div class="modal-body">
												<form>
													<div class="form-group">
														<div class="row">
															<div class="col-sm-12">
																<div class="observaciones"><p><?= texto($observacion) ?></p></div>
																<?php if($documento){ ?><div class="docJUS"><img src="<?= $imagenJUS ?>" class="centrado img-responsive"></div><?php } ?>
															</div>
														</div>
													</div>
													<div class="form-group">
														<div class="row">
															<div class="col-sm-12 text-center">
																<a href="<?= $ruta.'documentos/info-justificacion.php?codigoActividad='.$codigoActividad.'&codigoSocio='.$codigoSocio.'&tipoActividad='.$tipoActividad ?>" class="btn btn-lg bg-brown" ><i class="icon-printer2"></i> Imprimir</a>
																<button type="button" class="btn btn-lg btn-warning" data-dismiss="modal"><i class="icon-close2"></i> Cerrar</button>
															</div>
														</div>
													</div>
												</form>
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

		<?php if($opcion=="inasistentes"){ ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 6, 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-inasistentes').DataTable({ 'pageLength': 20 });
					$('.tabla-inasistentes tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>

			<div class="panel">
				<div class="panel-heading bg-slate-600"><h6 class="panel-title textoNegrita">SOCIOS QUE FALTARON A <?= $rotActMay ?></h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-inasistentes">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO SOCIO</th>
									<th class="textoNegrita text-left">NOMBRE DE SOCIO</th>
									<th class="textoNegrita text-center">DNI</th>
									<th class="textoNegrita text-center">MULTA</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-center">TOTAL</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="textoNegrita text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$conexion=conexionDB();
									$sql="SELECT sm_mod_asistencia.codigoSocio, sm_mod_asistencia.lotes, sm_mod_asistencia.multa, sm_mod_asistencia.estadoPago, sm_socios.apPaterno AS apPaterno, sm_socios.apMaterno AS apMaterno, sm_socios.nombre AS nombre, sm_socios.dni AS dni FROM sm_mod_asistencia, sm_socios WHERE sm_mod_asistencia.codigoSocio=sm_socios.codigoSocio AND sm_mod_asistencia.asistio='NO' AND sm_mod_asistencia.codigoActividad='$codigoActividad' ORDER BY sm_socios.apPaterno ASC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$codigoSocio    =$n[codigoSocio];
										$lotes          =$n[lotes];
										$temaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
										$multaTarde     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
										$multaFalta     =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
										$multa          =$n[multa];
										$estadoPago     =$n[estadoPago];
										$apPaterno      =$n[apPaterno];
										$apMaterno      =$n[apMaterno];
										$nombre         =$n[nombre];
										$dni            =$n[dni];
										$nombre         =texto($apPaterno.' '.$apMaterno.' '.$nombre);
										$porcentajePago =porcentajePago($codigoSocio,$codigoActividad,$multa);
										$razonBTpago    ='inasistencia';
										$rotuloCM       ='MULTA INASISTENCIA A '.$rotActMay;
										$programado     =conceptoProgramado($codigoSocio,$codigoActividad);

										$squery="SELECT COUNT(id) AS verificaPago FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoActividad'";
										$row=mysqli_query($conexion,$squery);
										$dato=mysqli_fetch_array($row);
										$verificaPago=$dato['verificaPago'];
										/*
										if($estadoPago=="NP" && $programado==0){
											$estado       ='<span class="label label-danger">PENDIENTE</span>';
											$verificaPago ="--";
										}

										if($estadoPago=="MP" && $programado>=2){
											$estado       ='<span class="label label-danger">PENDIENTE | '.ceros($programado,2).' F</span>';
											$verificaPago ="PEN";
											$pagoProgramado ="SI";
										}

										if($estadoPago=="SP" && $programado==0){
											$estado       ='<span class="label label-success">PAGADO</span>';
											$verificaPago =verificaPago($codigoActividad,$codigoSocio,$multa);
											$pagoProgramado ="NO";
										}

										if($estadoPago=="SP" && $programado>=2){
											$estado       ='<span class="label label-success">PAGADO | '.ceros($programado,2).' F</span>';
											$verificaPago =verificaPagosProgramados($codigoActividad,$codigoSocio,$multa);
											$pagoProgramado ="SI";
										}

										if($verificaPago=="--"){ $caja=''; $boton=''; }
										if($verificaPago=="PEN"){ $caja='<span class="label bg-grey">'.$porcentajePago.'%</span>'; $boton='<li class="dosabled"><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>'; }
										if($verificaPago=="OK"){ $caja='<span class="label label-info">CAJA</span>'; $boton='<li><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>'; }
										if($verificaPago=="ERROR"){ $caja='<span class="label label-warning">ERROR</span>'; $boton='<li class="disabled"><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>'; }
										*/

										if($verificaPago>0){
											if($programado>0 && $porcentajePago<100){
												$estado       ='<span class="label label-danger">PEN | '.ceros($programado,2).' F</span>';
												$verificaPago ="PEN";
												$pagoProgramado ="SI";
												$caja='<span class="label bg-grey">'.$porcentajePago.'%</span>'; $boton='<li class="dosabled"><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>';
											}else{
												$estado       ='<span class="label label-success">PAGADO</span>';
												$verificaPago =verificaPago($codigoActividad,$codigoSocio,$multa);
												$pagoProgramado ="NO";
												$caja='<span class="label label-info">CAJA</span>'; $boton='<li><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>';
											}
										}else{
											if($programado>0){
												$estado       ='<span class="label label-danger">PENDIENTE | '.ceros($programado,2).' F</span>';
												$verificaPago ="--";
												$caja='<span class="label bg-grey">'.$porcentajePago.'%</span>'; $boton='<li class="dosabled"><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i> Ver información de pago</a></li>';
											}else{
												$estado       ='<span class="label label-danger">PENDIENTE</span>';
												$pagoProgramado ="NO";
												$caja='';
											}
										}

										if($estadoPago=="SP"){
											$btonDP='<li><a href="#infoPago-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-info22"></i>Ver información de pago</a></li>';
										}else{
											$btonDP='<li><a href="#pagarMonto-'.$codigoSocio.'" data-toggle="modal"><i class=" icon-coin-dollar"></i> Pagar multa por '.$razonBTpago.' a '.$rotuloBTpago.'</a></li>';
										}
								?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoSocio ?></td>
									<td class="text-left"><?= texto($nombre) ?></td>
									<td class="text-center"><?= $dni ?></td>
									<td class="text-center text-danger textoNegrita"><?= 'S/. '.moneda($multaFalta) ?></td>
									<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-center text-danger textoNegrita"><?= 'S/. '.moneda($multa) ?></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center">
										<div class="btn-group">
											<button type="button" class="btn btn-icon btn-xs bg-brown dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></button>
											<ul class="dropdown-menu dropdown-menu-right">
												<li><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=detalles" ><i class="icon-user-check"></i> Ver perfil de socio</a></li>
												<?= $btonDP ?>
											</ul>
										</div>
									</td>
									<!-- MODAL PAGAR CUOTA -->
									<script type="text/javascript">
										$(document).ready(function(){
											///////////////////////////////////////////////////
											/// MODULO DE PAGO
											///////////////////////////////////////////////////
											var codigoSocio    ='<?= $codigoSocio ?>';
											$('#pagarMonto-'+codigoSocio).on('shown.bs.modal', function(){
												var ruta           ='../';
												var opcion         ='modulo_pago_monto';
												var conceptoPago   ='<?= $tipoActividad ?>';
												var codigoConcepto ='<?= $codigoActividad ?>';
												var razon          ='FALTA';
												var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&razon='+razon;
												$('#modulo_pago-'+codigoSocio).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
												$("#modulo_pago-"+codigoSocio).fadeIn("slow").load('modulo/modulo-pagos.php?'+datos).fadeIn(1000).delay(1000);
											});
										});
									</script>
									<div id="pagarMonto-<?= $codigoSocio ?>" class="modal fade">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header bg-slate-600">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title textoNegrita">PAGO DE MULTA DE <?= $rotActMay ?> &rarr; <?= texto($nombre) ?></h6>
												</div>
												<div class="modal-body">
													<div id="modulo_pago-<?= $codigoSocio ?>"></div>
													<div id="modulo_fechas_pago-<?= $codigoSocio ?>"></div>
												</div>
											</div>
										</div>
									</div>
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
												var conceptoPago   ='<?= $tipoActividad ?>';
												var codigoConcepto ='<?= $codigoActividad ?>';
												var razon          ='FALTA';
												var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&razon='+razon;
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
																	<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-5">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $rotuloCM ?></span></li>
																</ul>
															</div>
															<div class="col-sm-4">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-3">
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
														<h6 class="modal-title textoNegrita">INFORMACION DE PAGO <?= $pagoProgramado ?></h6>
													</div>
													<div class="modal-body">
														<?php
															$conexion=conexionDB();
															$sql="SELECT fechaOperacion, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND  codigoConcepto='$codigoActividad'";
															$infopago=mysqli_query($conexion,$sql);
															$dato=mysqli_fetch_array($infopago);
															$fechaOperacion  =$dato[fechaOperacion];
															$concepto        =$dato[concepto];
															$tipoDocumento   =$dato[tipoDocumento];
															$nroDocumento    =$dato[nroDocumento];
															$monto           =$dato[monto];
															$detalleConcepto =$dato[detalleConcepto];
															$codigoOperacion =$dato[codigoOperacion];
															$fecha           =$dato[fecha];
															$hora            =$dato[hora];
															$usuario         =$dato[usuario];
															cerrarDB();
														?>
														<div class="row mb-20">
															<div class="col-sm-12">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $rotuloCM ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">DOCUMENTO</span> <span class="pull-right text-danger"><?= infoTipoDOC($tipoDocumento) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">INGRESADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
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
								</tr>
								<?php $i++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if($opcion=="operaciones"){ ?>
			<div class="panel">
				<div class="panel-heading bg-slate-600"><h6 class="panel-title">REGISTRO DE OPERACIONES PROCESADAS</h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-aforo">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO SOCIO</th>
									<th class="textoNegrita text-left">NOMBRE SOCIO</th>
									<th class="textoNegrita text-left">PROCESO</th>
									<th class="textoNegrita text-center">FECHA</th>
									<th class="textoNegrita text-center">HORA</th>
									<th class="textoNegrita text-center">USUARIO</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$conexion=conexionDB();
									$sql="SELECT codigoActividad, codigoSocio, proceso, fecha, hora, usuario FROM sm_procesos_actividades WHERE codigoActividad='$codigoActividad' AND tipoActividad='$tipoActividad' ORDER BY id DESC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$codigoSocio=$n[codigoSocio];
										$proceso=$n[proceso];
										$fecha=$n[fecha];
										$hora=$n[hora];
										$usuario=$n[usuario];
								?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoSocio ?></td>
									<td class="text-left"><?= infoSocios($codigoSocio,'nombreCorto') ?></td>
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
			</div>
		<?php } ?>
	<?php } ?>

	<?php if($opcion=="procesarAsistencia"){ ?>
		<?php
			$conexion=conexionDB();
			$sql="SELECT codigoActividad, tipoActividad, temaActividad, contenidoActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta, fecha, hora, usuario FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'";
			$row=mysqli_query($conexion,$sql);
			$dato=mysqli_fetch_array($row);
			$temaActividad       =$dato[temaActividad];
			$contenidoActividad  =$dato[contenidoActividad];
			$fechaActividad      =$dato[fechaActividad];
			$horaActividad       =$dato[horaActividad];
			$lugarActividad      =$dato[lugarActividad];
			$mTardanza           =$dato[mTardanza];
			$mFalta              =$dato[mFalta];
			$fecha               =$dato[fecha];
			$hora                =$dato[hora];
			$usuario             =$dato[usuario];
			$aforo               =infoActividad($idJuntaDirectiva,$codigoActividad,'','aforo');
			$asistio             =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
			$tarde               =infoActividad($idJuntaDirectiva,$codigoActividad,'','tarde');
			$justifico           =infoActividad($idJuntaDirectiva,$codigoActividad,'','justifico');
			$falto               =infoActividad($idJuntaDirectiva,$codigoActividad,'','falto');
			$mTardanzas          =infoActividad($idJuntaDirectiva,$codigoActividad,'','mtarde');
			$mFaltas             =infoActividad($idJuntaDirectiva,$codigoActividad,'','mFalta');
			$totalMultas         =$mTardanzas+$mFaltas;
			$multasPagadas       =infoActividad($idJuntaDirectiva,$codigoActividad,'','mPagadas');
			$porPagar            =$totalMultas-$multasPagadas;
			$subidosServer       =infoTerminal($codigoActividad,'','','subidosServer');
			$asistenciaProcesada =infoActividad($idJuntaDirectiva,$codigoActividad,'','procesado');

			if($asistio>0){ $asistio=ceros($asistio,4); }else{ $asistio='----'; }
			if($tarde>0){ $tarde=ceros($tarde,4); }else{ $tarde='----'; }
			if($justifico>0){ $justifico=ceros($justifico,4); }else{ $justifico='----'; }
			if($falto>0){ $falto=ceros($falto,4); }else{ $falto='----'; }
			if($mTardanzas>0){ $mTardanzas='S/. '.moneda($mTardanzas,4); }else{ $mTardanzas='----'; }
			if($mFaltas>0){ $mFaltas='S/. '.moneda($mFaltas,4); }else{ $mFaltas='----'; }
			if($totalMultas>0){ $totalMultas='S/. '.moneda($totalMultas,4); }else{ $totalMultas='----'; }
			if($multasPagadas>0){ $multasPagadas='S/. '.moneda($multasPagadas,4); }else{ $multasPagadas='----'; }
			if($porPagar>0){ $porPagar='S/. '.moneda($porPagar,4); }else{ $porPagar='----'; }
			if($subidosServer>0){ $estadoBoton=""; }else{ $estadoBoton="disabled"; }
			cerrarDB();
		?>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CAMBIO DE ESTILO DE INPUT FILE
				///////////////////////////////////////////////////
				$(".inputSubir").uniform({
					wrapperClass: 'bg-brown',
					fileButtonHtml: '<i class="icon-plus2"></i>'
				});

				///////////////////////////////////////////////////
				/// SUBIR JSON ASISTENCIA DE TERMINALES
				///////////////////////////////////////////////////
				$("button#subir_asistencias").click(function(){
					var ruta            ="<?= $ruta ?>";
					var urlSocios       ="<?= $urlSocios ?>";
					var codigoActividad = '<?= $codigoActividad ?>';
					var operacion       ="SUBIR_ARCHIVO_ASISTENCIAS";
					var datos           =new FormData($("#subirAsistencias")[0]);
					var urlProcesos     = urlSocios+'/php/mantenimiento-actividades.php?codigoActividad='+codigoActividad+'&operacion='+operacion;
					$.ajax({
						url: urlProcesos,
						type: "POST",
						dataType:'json',
						data: datos,
						contentType: false,
						processData: false,
						beforeSend: function(){
							$('#subir').hide();
							$('#procesando').fadeIn("slow").html('<div class="row mb-15"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>TRANSFIRIENDO DATOS</div></div>').fadeIn(1000).delay(1000);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="ARCHIVO_SUBIDO_ACTUALIZADO"){
								location.reload();
							}
							if(respuesta.mensaje=="ARCHIVOINCORRECTO"){
								$('button#subir_json').prop('disabled', false);
								$('#aviso').html('<div class="alert alert-danger alert-styled-left alert-bordered">Error el archivo subido tiene <strong>'+respuesta.socios+' socios</strong> y actualmente existen <strong>'+socios+' socios</strong>.</div>');
								$('#procesando').hide();
								$('#subir').show();
							}
							if(respuesta.mensaje=="SINFILE"){
								$('button#subir_json').prop('disabled', false);
								$.jGrowl('Seleccione archivo <strong>socios.json</strong>', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
								$('#procesando').hide();
								$('#subir').show();
							}
							if(respuesta.mensaje=="ERRORFORMATO"){
								$('button#subir_json').prop('disabled', false);
								$.jGrowl('Seleccione archivo <strong>socios.json</strong>.', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
								$('#procesando').hide();
								$('#subir').show();
							}
							if(respuesta.mensaje=="ERRORSERVER"){
								$('button#subir_json').prop('disabled', false);
								$.jGrowl('Ha ocurrido un error en el servidor.', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
								$('#procesando').hide();
								$('#subir').show();
							}
						}
					});
				});

				///////////////////////////////////////////////////
				/// FINALIZAR PROCESO DE ASISTENCIA DE SOCIOS
				///////////////////////////////////////////////////
				$("button#bt_procesar_asistencia").click(function(){
					var ruta='../';
					var codigoActividad ='<?= $codigoActividad ?>';
					var operacion       ='PROCESA_ASISTENCIAS_ACTIVIDAD';
					var datos           ='codigoActividad='+codigoActividad+'&operacion='+operacion;
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-actividades.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('#botonprocesa').hide();
							$('#procesarAsistencia').fadeIn("slow").html('<hr><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>PROCESANDO ASISTENCIAS</div></div><hr>').fadeIn(1000).delay(1000);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="PROCESAMIENTO_ASISTENCIA_COMPLETADO"){
								location.reload();									
							}
						}
					});
				});
			});
		</script>

		<div class="row">
			<div class="col-lg-7">
				<div class="panel">
					<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">PROCESAR ASISTENCIA</div>
					<div class="panel-body">
						<?php  if($archivoACT=="NO_EXISTE"){ ?>Antes de iniciar con el proceso de control de asistencia, por favor genere el archivo de transferencia de <?= $rotActMin ?>, descárguelo  en un dispositivo USB para que pueda importarlo en las terminales de control, antes de iniciarse la <?= $rotActMin ?>, es recomendable hacer este proceso 24 horas antes o en caso de urgencia una hora antes de dar inicio a <?= $rotActMin ?>,. Recomendacion aparte hacer el mismo proceso de transferencia de datos de socios.<?php } ?>
							
						<?php if(($asistenciaProcesada=="NO" or $asistenciaProcesada=="") and ($archivoACT=="EXISTE")){ ?>
							<div id="subir">
								<form id="subirAsistencias" method="post" enctype="multipart/form-data">
									<div class="form-group">
										<div class="row">
											<div class="col-sm-5">
												<input type="file" name="archivo" class="inputSubir" required="required">
											</div>
											<div class="col-sm-3">
												<button type="button" id="subir_asistencias" class="btn bg-brown btn-block">SUBIR ARCHIVO</button>
											</div>
											<div class="col-sm-4">
												<button type="button" data-toggle="modal" data-target="#procesar_json_asistencia" class="btn btn-success btn-block <?= $estadoBoton ?>">PROCESAR ASISTENCIA</button>
											</div>
										</div>
									</div>
								</form>								
							</div>
							<div id="procesando"></div>
						<?php }else{} ?>

						<?php if($subidosServer>0){ ?>
							<div class="table-responsive">
								<table class="table table-bordered table-framed">
									<thead>
										<tr class="success">
											<th class="text-center text-bold"><i class="icon-screen3"></i></th>
											<th class="text-center text-bold"><i class="icon-users"></i></th>
											<th class="text-left text-bold">ARCHIVO</th>
											<th class="text-left text-bold">USUARIO</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$conexion=conexionDB();
											$sql="SELECT terminal, socios, archivo, fecha, hora, usuario FROM sm_mod_asistencia_json WHERE codigoActividad='$codigoActividad' ORDER BY terminal ASC";
											$rs=mysqli_query($conexion,$sql);
											while($n=mysqli_fetch_array($rs)){
												$terminal   =$n[terminal];
												$socios     =$n[socios];
												$archivo    =$n[archivo];
												$fecha      =$n[fecha];
												$hora       =$n[hora];
												$usuario    =$n[usuario];
												$registro   =registradoPor($usuario,$fecha,$hora,'SI','bg-grey-300');
												$asistentes =infoTerminal($codigoActividad,'','','totalAsistentes');
										?>
										<tr>
											<td class="text-center"><?= ceros($terminal,2) ?></td>
											<td class="text-left"><?= $socios ?></td>
											<td class="text-left"><span class="label bg-brown-300"><?= $archivo ?></span></td>
											<td class="text-left"><?= $registro ?></td>
										</tr>
										<?php } cerrarDB() ?>
										<tr>
											<td class="text-center text-bold" colspan="4">SOCIOS ASISTENTES &rarr; <?= numero($asistentes) ?></td>
										</tr>
									</tbody>
								</table>
							</div>

							<!-- MODAL DE PROCESAMIENTO DE ASIWTENCIA -->
							<div id="procesar_json_asistencia" class="modal fade">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header bg-danger">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h5 class="modal-title">PROCESAMIENTO DE DATOS DE ASISTENCIA</h5>
										</div>

										<div class="modal-body">
											<h6 class="text-semibold text-danger">Importante</h6>
											<p>Antes de continuar con el procedimiento, <strong>por favor verifique que todos los archivos hayan sidos subidos al sistema</strong>, de tal modo que todos los socios asistentes sean considerados en la <strong>Lista de Asistencia a la Faena</strong>, de otra forma la relación de asistentes se vera perjudicada por inasistencias.</p>
											<div id="botonprocesa" class="text-center"><hr><button type="button" id="bt_procesar_asistencia" class="btn btn-danger btn-float btn-float-lg"><i class="icon-database-refresh"></i> <span>Procesar</span></button><hr></div>
											<div id="procesarAsistencia"></div>
										</div>
										<div class="modal-footer text-center">
											<button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="col-lg-5">
				<div class="panel">
					<div class="panel-heading bg-info"><h6 class="panel-title textoNegrita">ESTADISTICAS DE <?= $rotActMay ?></div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-5">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">AFORO</span> <span class="pull-right text-warning"><?= ceros($aforo,4) ?> <small>Socios</small></span></li>
									<li class="list-group-item"><span class="textoNegrita">ASISTENTES</span> <span class="pull-right text-warning"><?= $asistio ?> <small>Socios</small></span></li>
								</ul>
							</div>
							<div class="col-sm-5">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">TARDANZAS</span> <span class="pull-right text-warning"><?= $tarde ?> <small>Socios</small></span></li>
									<li class="list-group-item"><span class="textoNegrita">INASISTENTES</span> <span class="pull-right text-warning"><?= $falto ?> <small>Socios</small></span></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

<?php
	}else{
		if($tipoActividad=="ASA"){ include($ruta.'template/404-asamblea.tpl'); }
		if($tipoActividad=="FAE"){ include($ruta.'template/404-faena.tpl'); }
	}
	include($ruta.'template/footer.tpl');
?>