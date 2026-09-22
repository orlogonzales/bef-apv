<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$montoDisponible  =montoDisponible('','');
	$conexion=conexionDB();
?>

<?php if($montoDisponible>0){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// GENERAR PARTIDA
			///////////////////////////////////////////////////
			$("button#bt_almacenar_partida").click(function(){
				var ruta            ='../';
				var valida          =$('.form_valida_partida').valid();
				var operacion       =$('#operacion').val();
				var tipoResposanble =$('#tipoResposanblePAR').val();
				var usuarioPartida  =$('#usuarioPartida').val();
				var conceptoPartida =$('#conceptoPartida').val();
				var montoPartida    =$('#montoPartida').val();
				var codigoChequera  =$('#codigoChequera').val();
				var nroCheque       =$('#nroCheque').val();
				var fechaPartida    =$('#fechaPartida').val();
				var observaciones   =$('#observaciones').val();
				var datos           ='operacion='+operacion+'&tipoResposanble='+tipoResposanble+'&usuarioPartida='+usuarioPartida+'&conceptoPartida='+conceptoPartida+'&montoPartida='+montoPartida+'&codigoChequera='+codigoChequera+'&nroCheque='+nroCheque+'&fechaPartida='+fechaPartida+'&observaciones='+observaciones;
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-partidas.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('button#bt_almacenar_partida').prop('disabled', true);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="PARTIDA_ALMACENADA"){
								new PNotify({title: 'CONFIRMACION', text: 'La partida de gasto fue registrada.', addclass: 'bg-success'});
								$("#crearPartida").modal('hide');
								location.reload();
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO INGRESO GASTOS DE PARTIDA
			///////////////////////////////////////////////////
			var validator = $(".form_valida_partida").validate({
				ignore: 'input[type=hidden], .select2-input',
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); $(element).fadeIn(5000).css('margin-bottom','10px!important'); }); },

				validClass: "validation-valid-label",
				rules: {
					tipoResposanblePAR: { required: true },
					usuarioPartida: { required: true },
		 			conceptoPartida: { required: true },
		 			montoPartida: { number: true, min: 1, max: <?= $montoDisponible ?> },
					codigoChequera: { required: true },
					nroCheque: { required: true },
					fechaPartida: { required: true },
				},
				messages: {
					tipoResposanblePAR: { required: "Tipo de responsable", },
					usuarioPartida: { required: "Seleccione usuario", },
					conceptoPartida: { required: "Ingrese concepto de partida", },
					montoPartida: { required: "Monto de partida", number: "Monto de gasto", min: "Minimo S/. 1", max: "Maximo S/."+<?= $montoDisponible ?>, },
					tipoDocumento: { required: "Seleccione tipo de documento", },
					codigoChequera: { required: "Seleccione chequera", },
					nroCheque: { required: "Ingrese nro. de cheque", },
					fechaPartida: { required: "Fecha de gasto", },
				}
			});

			///////////////////////////////////////////////////
			/// VALIDAR COMPOS NUMERICOS
			///////////////////////////////////////////////////
			$(function(){ $('#montoPartida').validar('0123456789.'); });
			$(function(){ $('#fechaPartida').validar('0123456789/'); });

			///////////////////////////////////////////////////
			/// TIPO DE RESPONSABLE DE PARTIDA
			///////////////////////////////////////////////////
			$("#tipoResposanblePAR").change(function(event){
				var responsable = $("#tipoResposanblePAR").val();
				if(responsable==''){
					$('button#bt_almacenar_partida').prop('disabled', false);
					$("#usuarioPartida").html('');
				}
				if(responsable=='USR'){
					$('button#bt_almacenar_partida').prop('disabled', true);
					$("#usuarioPartida").html('<option value="" selected>CARGANDO DATOS...</option>');
					$("#usuarioPartida").load('../php/lista-usuario-sistema.php');

				}
				if(responsable=='SOC'){
					$('button#bt_almacenar_partida').prop('disabled', true);
					$("#usuarioPartida").html('<option value="" selected>CARGANDO DATOS...</option>');
					$("#usuarioPartida").load('../php/lista-socios.php');
				}
			});

			$("#usuarioPartida").change(function(event){
				var responsable = $("#usuarioPartida").val();
				if(responsable!=""){
					$('button#bt_almacenar_partida').prop('disabled', false);
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
	</script>

	<form class="form_valida_partida">
		<input type="hidden" name="operacion" id="operacion" value="REGISTRA_PARTIDA">
		<div class="modal-body">
			<div class="alert alert-styled-left alert-arrow-left alpha-teal text-semibold"><?= 'MONTO DISPONIBLE PARA PARTIDAS &rarr; <span class="text-danger text-bold">S/. '.moneda($montoDisponible).'</span>' ?></div>

			<div class="form-group">
				<div class="row">
					<div class="col-sm-4">
						<select name="tipoResposanblePAR" id="tipoResposanblePAR" required="required" class="form-control input-lg" tabindex="1" autofocus>
							<option value="" selected>SELECCIONE</option>
							<option value="USR">USUARIO DE SISTEMA</option>
							<option value="SOC">SOCIO DE LA APV</option>
						</select>
						<span class="label label-block bg-grey-300 mt-5 text-left">Responsable de gasto</span>
					</div>
					<div class="col-sm-8">
						<select name="usuarioPartida" id="usuarioPartida" required="required" class="form-control input-lg" tabindex="2">
							<option value="" selected>SELECCIONE</option>
						</select>
						<span class="label label-block bg-grey-300 mt-5 text-left">Responsable</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<input type="text" name="conceptoPartida" id="conceptoPartida" class="form-control input-lg textoMayuscula" required="required" onblur="mayusculas(event, this)" tabindex="2">
							<span class="label label-block bg-grey-300 mt-5 text-left">Concepto de partida</span>
						</div>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="row">
					<div class="col-md-2 col-xs-6">
						<div class="form-group">
							<input type="text" name="montoPartida" id="montoPartida" class="form-control input-lg" required="required" tabindex="3">
							<span class="label label-block bg-grey-300 mt-5 text-left">Monto partida</span>
						</div>
					</div>

					<div class="col-md-5 col-xs-6">
						<div class="form-group">
							<select name="codigoChequera" id="codigoChequera" class="form-control input-lg" required="required" autofocus tabindex="4">
								<option value="" selected>SELECCIONE</option>
								<?php
									$sql="SELECT codigoChequera, codigoBanco, codigoCuenta, detalle FROM sm_chequera WHERE estado='ACT'";
									$rs=mysqli_query($conexion,$sql);
									while($datos=mysqli_fetch_array($rs)){
										$codigoChequera =$datos[codigoChequera];
										$codigoBanco    =$datos[codigoBanco];
										$codigoCuenta   =$datos[codigoCuenta];
										$detalle        =$datos[detalle];
										$detalleCTA     =infoCuentas($codigoCuenta,'','detalleCuenta');
										$numeroCuenta   =infoCuentas($codigoCuenta,'','numeroCuenta');
										$infoChequera   =infoBancos($codigoBanco,'detalleEntidad').' - '.$detalleCTA.' ('.$numeroCuenta.') - '.texto($detalle);
										$estadoCuenta   =infoCuentas($codigoCuenta,$codigoBanco,'estadoCuenta');

										if($estadoCuenta=="ACT"){ echo '<option value="'.$codigoChequera.'">'.$infoChequera.'</option>'; }
									}
								?>
							</select>
							<span class="label label-block bg-grey-300 mt-5 text-left">Chequera para partida</span>
						</div>
					</div>

					<div class="col-md-3 col-xs-6">
						<div class="form-group">
							<input type="text" name="nroCheque" id="nroCheque" class="form-control input-lg" required="required" tabindex="5">
							<span class="label label-block bg-grey-300 mt-5 text-left">Nro. de cheque</span>
						</div>
					</div>

					<div class="col-md-2 col-xs-6">
						<div class="form-group">
							<input type="text" name="fechaPartida" id="fechaPartida" class="form-control fechas input-lg" required="required" tabindex="6">
							<span class="label label-block bg-grey-300 mt-5 text-left">Fecha de gasto</span>
						</div>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<textarea rows="4" cols="5" name="observaciones" id="observaciones" class="form-control textoMayuscula" onblur="mayusculas(event, this)" placeholder="Observaciones" tabindex="7"></textarea>
							<span class="label label-block bg-grey-300 mt-5 text-left">Observaciones o detalles de partida</span>
						</div>
					</div>
				</div>
			</div>

			<div class="row text-center">
				<div class="col-sm-12">
					<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
					<button type="button" id="bt_almacenar_partida" class="btn bg-success">ALAMACENAR PARTIDA</button>
				</div>
			</div>
		</div>
		<div class="modal-footer">
		</div>
	</form>
<?php }else{ echo cajaAlerta('SIN FONDOS','text-center textoNegrita','Tesoreria no dispone de fondos en Caja para generar mas partidas.','text-center','bg-teal-300'); } ?>