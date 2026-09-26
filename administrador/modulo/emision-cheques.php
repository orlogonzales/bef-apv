<?php
	session_start();
	$conexion         =conexionDB();
	$consultaFechaIni =infoTiempo('fecha');
	$consultaFechaFin =infoTiempo('fecha');
	$usuario          =$_SESSION['dni_apv'];
	$chequesEmitidos  =infoCheques($consultaFechaIni,$consultaFechaFin,'ALL','ALL','ALL','ALL','chequesEmitidosHOY');
	$totalEmitidos    =infoCheques($consultaFechaIni,$consultaFechaFin,'ALL','ALL','ALL','ALL','totalChequesHOY');
?>

<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// VALOS POR DEFAULT
		///////////////////////////////////////////////////
		$('#baseRegistrado').show();
		$('#registrado').hide();
		$('#noRegistrado').hide();
		$('#bt_oculta_formulario').hide();
		$('#bt_registra_cheque').hide();
		$('#formulario_registro_cheques').hide();

		///////////////////////////////////////////////////
		/// MUESTRA FORMULARIO DE REGISTRO
		///////////////////////////////////////////////////
		$("button#boton_agregar_cheques").click(function(){
			$('#agregar_registro_cheques').hide();
			$('#formulario_registro_cheques').show();
			$('#bt_registra_cheque').show();
			$('#bt_oculta_formulario').show();
		});

		///////////////////////////////////////////////////
		/// OCULTA FORMULARIO DE REGISTRO
		///////////////////////////////////////////////////
		$("button#bt_oculta_formulario").click(function(){
			$('#agregar_registro_cheques').show();
			$('#formulario_registro_cheques').hide();
			$('#bt_registra_cheque').hide();
			$('#bt_oculta_formulario').hide();
		});

		///////////////////////////////////////////////////
		/// REGISTRO DE DATOS DE FORMULARIO
		///////////////////////////////////////////////////
		$("button#bt_registra_cheque").click(function(){
			var ruta   ='../';
			var valida =$('.form_valida_cheque').valid();
			var datos  =$('#form_registra_cheque').serialize();
			if(valida){
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-cheques.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('button#bt_registra_cheque').prop('disabled', true);
					},
					success: function(respuesta){
						if(respuesta.mensaje=="CHEQUE_REGISTADO"){
							new PNotify({title: 'CONFIRMACION', text: 'El Cheque emitido fue registrado en el sistema.', addclass: 'bg-success'});
							location.reload();
						}
						if(respuesta.mensaje=="ERROR_CHEQUE_REGISTADO"){
							new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
						}
					}
				});
			}
		});

		///////////////////////////////////////////////////
		/// VALIDAR FORMULARIO INGRESO GASTOS DE PARTIDA
		///////////////////////////////////////////////////
		var validator = $(".form_valida_cheque").validate({
			ignore: 'input[type=hidden], .select2-input',
			errorClass: 'validation-error-label',
			highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
			unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#DDDDDD'); }); },
			validClass: "validation-valid-label",
			rules: {
				RC_codigoChequera: { required: true },
				RC_nroCheque: { required: true },
				RC_fechaEmision: { required: true },
				RC_montoEmitido: { required: true },
				RC_tipoBeneficiario: { required: true },
				//RC_usuarioBeneficiario: { required: true },
				//RC_dniBeneficiario: { required: true },
				//RC_nombreBeneficiario: { required: true },
				RC_conceptoCheque: { required: true },
			},
			RC_messages: {
				RC_codigoChequera: { required: 'Seleccione chequera' },
				RC_nroCheque: { required: 'Nro de cheque' },
				RC_fechaEmision: { required: 'Fecha de emisión' },
				RC_montoEmitido: { required: 'Monto emitido' },
				RC_tipoBeneficiario: { required: 'Seleccione tipo de beneficiario' },
				//RC_usuarioBeneficiario: { required: 'Beneficiario de cheque' },
				//RC_dniBeneficiario: { required: 'DNI beneficiario' },
				//RC_nombreBeneficiario: { required: 'Nombre beneficiario' },
				RC_conceptoCheque: { required: 'Concepto de cheque' },
			}
		});
		
		///////////////////////////////////////////////////
		/// VALIDAR COMPOS NUMERICOS
		///////////////////////////////////////////////////
		$(function(){ $('#montoEmitido, #dniBeneficiario').validar('0123456789.'); });
		$(function(){ $('#fechaEmision').validar('0123456789/'); });

		///////////////////////////////////////////////////
		/// TIPO DE RESPONSABLE DE PARTIDA
		///////////////////////////////////////////////////
		$("#RC_tipoBeneficiario").change(function(event){
			var responsable = $("#RC_tipoBeneficiario").val();

			if(responsable==''){
				$('#baseRegistrado').show();
				$('#registrado').hide();
				$('#noRegistrado').hide();
			}

			if(responsable=='USR'){
				$('#baseRegistrado').hide();
				$('#registrado').show();
				$('#noRegistrado').hide();

				$('button#bt_registra_cheque').prop('disabled', true);
				$("#RC_usuarioBeneficiario").html('<option value="" selected>CARGANDO DATOS...</option>');
				$("#RC_usuarioBeneficiario").load('../php/lista-usuario-sistema.php');

			}
			if(responsable=='SOC'){
				$('#baseRegistrado').hide();
				$('#registrado').show();
				$('#noRegistrado').hide();

				$('button#bt_registra_cheque').prop('disabled', true);
				$("#RC_usuarioBeneficiario").html('<option value="" selected>CARGANDO DATOS...</option>');
				$("#RC_usuarioBeneficiario").load('../php/lista-socios.php');
			}
			if(responsable=='NOR'){
				$('#baseRegistrado').hide();
				$('#registrado').hide();
				$('#noRegistrado').show();
			}
		});

		$("#RC_usuarioBeneficiario").change(function(event){
			var responsable = $("#RC_usuarioBeneficiario").val();
			if(responsable!=""){
				$('button#bt_registra_cheque').prop('disabled', false);
			}
		});

		///////////////////////////////////////////////////
		/// DATEPICKER
		///////////////////////////////////////////////////
		$(".fechas").datepicker({
			showButtonPanel: true,
			format: 'dd/mm/yyyy',
			altFormat: "DD, d MM, yy"
		});
	});

	///////////////////////////////////////////////////
	/// AUTOCOMPLETAR NOMBRE DE BENEFICIARIO
	///////////////////////////////////////////////////
	function beneficiario(){
		var ruta      ='../';
		var documento =$('input#RC_dniBeneficiario').val();
		var operacion ='NOMBRE_BENEFICIARIO';
		var datos     ='dniBeneficiario='+documento+'&operacion='+operacion;
		if($.trim(documento).length>0){
			$.ajax({
				type: "POST",
				url: ruta+'php/mantenimiento-cheques.php',
				data: datos,
				dataType:'json',
				success: function(respuesta){
					$('input#RC_nombreBeneficiario').val(respuesta.nombreBeneficiario);
				}
			});
		}
	}
</script>

<form id="form_registra_cheque" class="form_valida_cheque">
	<input type="hidden" name="operacion" id="operacion" value="REGISTRA_CHEQUE">
	<div class="panel">
		<div class="panel-heading bg-brown">
			<h6 class="panel-title textoNegrita">REGISTRO DE EMISION DE CHEQUES</h6>
			<div class="heading-elements">
				<button type="button" id="bt_oculta_formulario" class="btn btn-xs btn-warning btn-labeled textoNegrita"><b><i class="fa fa-minus-square-o"></i></b>OCULTA FORMULARIO</button>
				<button type="button" id="bt_registra_cheque" class="btn btn-xs btn-success btn-labeled textoNegrita"><b><i class="fa fa-floppy-o"></i></b>REGISTRAR CHEQUE</button>
			</div>
		</div>
		<div class="panel-body">
			<div id="agregar_registro_cheques" class="text-center">
				<button type="button" id="boton_agregar_cheques" class="btn btn-warning btn-labeled btn-xlg mt-20 mb-20"><b><i class="fa fa-plus-square-o"></i></b> REGISTRAR NUEVO CHEQUE</button>
			</div>
			<div id="formulario_registro_cheques">
				<div class="form-group">
					<div class="row">
						<div class="col-md-5 col-xs-6">
							<div class="form-group">
								<select name="RC_codigoChequera" id="RC_codigoChequera" class="form-control input-lg" required="required" autofocus tabindex="1">
									<option value="" selected>SELECCIONE</option>
									<?php
										$sql="SELECT codigoChequera, codigoBanco, codigoCuenta, detalle FROM sm_chequera WHERE estado='ACT'";
										$rs=mysqli_query($conexion,$sql);
										while($datos=mysqli_fetch_array($rs)){
											$codigoChequera =$datos['codigoChequera'];
											$codigoBanco    =$datos['codigoBanco'];
											$codigoCuenta   =$datos['codigoCuenta'];
											$detalle        =$datos['detalle'];
											$detalleCTA     =infoCuentas($codigoCuenta,'','detalleCuenta');
											$numeroCuenta   =infoCuentas($codigoCuenta,'','numeroCuenta');
											$infoChequera   =infoBancos($codigoBanco,'detalleEntidad').' - '.$detalleCTA.' ('.$numeroCuenta.') - '.texto($detalle);
											$estadoCuenta   =infoCuentas($codigoCuenta,$codigoBanco,'estadoCuenta');

											if($estadoCuenta=="ACT"){ echo '<option value="'.$codigoChequera.'">'.$infoChequera.'</option>'; }
										}
									?>
								</select>
								<span class="label label-block bg-grey-300 mt-5 text-left">Chequera</span>
							</div>
						</div>

						<div class="col-md-3 col-xs-6">
							<div class="form-group">
								<input type="text" name="RC_nroCheque" id="RC_nroCheque" class="form-control input-lg" required="required" tabindex="2">
								<span class="label label-block bg-grey-300 mt-5 text-left">Nro. cheque</span>
							</div>
						</div>					

						<div class="col-md-2 col-xs-6">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									<input type="text" name="RC_fechaEmision" id="RC_fechaEmision" class="form-control fechas input-lg" required="required" tabindex="3">
								</div>
								<span class="label label-block bg-grey-300 mt-5 text-left">Fecha de emision</span>
							</div>
						</div>

						<div class="col-md-2 col-xs-6">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon">S/.</span>
									<input type="text" name="RC_montoEmitido" id="RC_montoEmitido" class="form-control input-lg" required="required" tabindex="4">
								</div>
								<span class="label label-block bg-grey-300 mt-5 text-left">Monto emitido</span>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-sm-4">
							<select name="RC_tipoBeneficiario" id="RC_tipoBeneficiario" required="required" class="form-control input-lg" tabindex="5" autofocus>
								<option value="" selected>SELECCIONE</option>
								<option value="USR">USUARIO DE SISTEMA</option>
								<option value="SOC">SOCIO DE LA APV</option>
								<option value="NOR">NO REGISTRADO EN EL SISTEMA</option>
							</select>
							<span class="label label-block bg-grey-300 mt-5 text-left">Emitir cheque a</span>
						</div>
						<div class="col-sm-8">
							<div id="baseRegistrado">
								<input disabled="disabled" class="form-control input-lg">
								<span class="label label-block bg-grey-300 mt-5 text-left">Beneficiario o responsable</span>
							</div>
							<div id="registrado">
								<select name="RC_usuarioBeneficiario" id="RC_usuarioBeneficiario" class="form-control input-lg" tabindex="6">
									<option value="" selected>SELECCIONE</option>
								</select>
								<span class="label label-block bg-grey-300 mt-5 text-left">Beneficiario o responsable</span>
							</div>
							<div id="noRegistrado">
								<div class="col-sm-3">
									<input type="text" name="RC_dniBeneficiario" id="RC_dniBeneficiario" class="form-control input-lg" onblur="beneficiario();" tabindex="6">
									<span class="label label-block bg-grey-300 mt-5 text-left">DNI beneficiario</span>
								</div>
								<div class="col-sm-9">
									<input type="text" name="RC_nombreBeneficiario" id="RC_nombreBeneficiario" class="form-control input-lg textoMayuscula" onblur="mayusculas(event, this)" tabindex="7">
									<span class="label label-block bg-grey-300 mt-5 text-left">Nombre beneficiario</span>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-md-12 col-xs-12">
							<div class="form-group">
								<input type="text" name="RC_conceptoCheque" id="RC_conceptoCheque" class="form-control input-lg textoMayuscula" required="required" onblur="mayusculas(event, this)" tabindex="8">
								<span class="label label-block bg-grey-300 mt-5 text-left">Concepto de cheque</span>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group">
					<div class="row">
						<div class="col-md-12 col-xs-12">
							<div class="form-group">
								<textarea rows="4" cols="5" name="RC_observaciones" id="RC_observaciones" class="form-control textoMayuscula" onblur="mayusculas(event, this)" placeholder="Observaciones" tabindex="9"></textarea>
								<span class="label label-block bg-grey-300 mt-5 text-left">Observaciones o detalles de cheque</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php if($chequesEmitidos>0){ ?>
	<script type="text/javascript">
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
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span>CANTIDAD CHEQUES EMITIDOS</span> <span class="pull-right text-danger textoNegrita"><?= ceros($chequesEmitidos,2) ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span>TOTAL EN CHEQUES EMITIDOS</span> <span class="pull-right text-danger textoNegrita"><?= 'S/. '.moneda($totalEmitidos) ?></span></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="table-responsive">
			<table class="table tablaT table-bordered table-hover tabla-caja-ing">
				<thead>
					<tr class="bg-brown-300">
						<th class="textoNegrita text-center">#</th>
						<th class="textoNegrita text-center">FECHA</th>
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
						$sql="SELECT codigoBanco, codigoCuenta, codigoChequera, nroCheque, codigoCheque, fechaEmision, monto, tipoBeneficiario, beneficiario, concepto, observaciones, codigoOperacion, fecha, hora, usuario FROM sm_cheques WHERE tipoCheque='VAR' AND fecha='$consultaFechaIni'";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoBanco      =$n['codigoBanco'];
							$entidadBancaria  =infoBancos($codigoBanco,'detalleEntidad');
							$codigoCuenta     =$n['codigoCuenta'];
							$codigoChequera   =$n['codigoChequera'];
							$nroCheque        =$n['nroCheque'];
							$codigoCheque     =$n['codigoCheque'];
							$fechaEmision     =infoFecha($n['fechaEmision'],'normal');
							$monto            =$n['monto'];
							$tipoBeneficiario =$n['tipoBeneficiario'];
							$beneficiario     =$n['beneficiario'];
							$nombre           =infocheque('','','','','','',$beneficiario,'nombreBeneficiario');
							$concepto         =texto($n['concepto']);
							$observaciones    =texto($n['observaciones']);
							$codigoOperacion  =$n['codigoOperacion'];
							$fecha            =$n['fecha'];
							$hora             =$n['hora'];
							$usuario          =$n['usuario'];
							$chequera         =texto($entidadBancaria.' '.infoChequeras($codigoChequera,$codigoBanco,'','detalleChequera'));
							$registradoPor    =registradoPor($usuario,$fecha,$hora,'SI','label-default');
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-center textoMayuscula"><?= $fechaEmision ?></td>
						<td class="text-left"><?= $concepto ?></td>
						<td class="text-left"><?= $nombre ?></td>
						<td class="text-left"><?= $chequera ?></td>
						<td class="text-left"><?= $nroCheque ?></td>
						<td class="text-right text-danger textoNegrita"><?= 'S/.&nbsp;'.moneda($monto) ?></span></td>
						<td class="text-center"><?= $registradoPor ?></td>
						<td class="text-center"><button type="button" onclick="eliminaCheque('<?= 'codigoBanco='.$codigoBanco.'&codigoCuenta='.$codigoCuenta.'&codigoChequera='.$codigoChequera.'&nroCheque='.$nroCheque.'&monto='.$monto.'&beneficiario='.$beneficiario.'&codigoOperacion='.$codigoOperacion ?>')" class="btn btn-xs bg-warning-300"><i class="fa fa-trash"></i></button></td>
					</tr>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>
	</div>
<?php }else{ echo cajaAlerta('SIN CHEQUES','text-center textoNegrita','No se ha encontrado, ningún registro de cheques emitidos para hoy.','text-center','bg-warning-300'); } ?>