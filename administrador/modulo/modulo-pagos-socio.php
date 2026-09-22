<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$conexion       =conexionDB();
	$opcion         =$_GET[opcion];
	$codigoSocio    =$_GET[codigoSocio];
	$conceptoPago   =$_GET[conceptoPago];
	$codigoConcepto =$_GET[codigoConcepto];
	$lotes          =infoSocios($codigoSocio,'cantidadLotes');
	$fechaHoy       =infoTiempo('fechaHoy');
	if($conceptoPago=="CUO"){
		$montoPago  =infoPago($codigoSocio,$codigoConcepto,'','montoCuotaSocio');
		$estadoPago =infoPago($codigoSocio,$codigoConcepto,'','estadoPagoCuotaSocio');
	}
	if(($conceptoPago=="ASA") or ($conceptoPago=="FAE")){
		$razon      =$_GET[razon];
		$montoPago  =infoPago($codigoSocio,$codigoConcepto,'','totalPagoActividadSocio');
		$estadoPago =infoPago($codigoSocio,$codigoConcepto,'','estadoPagoActividadSocio');
	}
?>

<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// CONFIGURACION DE DATEPICKER
		///////////////////////////////////////////////////
		$(".fechas").datepicker({
			showButtonPanel: true,
			format: 'dd/mm/yyyy',
			minDate: "-1M"
		});
	});
</script>

<?php if($opcion=="modulo_pago_monto"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// VARIBLE DE CONCEPTO DE PAGO
			///////////////////////////////////////////////////
			var ruta           ='../';
			var codigoSocio    ='<?= $codigoSocio ?>';
			var conceptoPago   ='<?= $conceptoPago ?>';
			var codigoConcepto ='<?= $codigoConcepto ?>';
			var montoPago      ='<?= $montoPago ?>';
			var razon          ='<?= $razon ?>';
			if(conceptoPago=="ASA" || conceptoPago=="FAE"){ var razon='&razon=<?= $razon ?>'; }else{ var razon=''; }
			$('#documentoPago').focus();

			///////////////////////////////////////////////////
			/// PAGO DE MONTO TOTAL
			///////////////////////////////////////////////////
			$("button#bt_pagar_monto"+codigoConcepto).click(function(){
				var operacion     ='PAGO_MONTO_COMPLETO';
				var documentoPago =$('input#documentoPago').val();
				var fechaPago     =$('input#fechaPago').val();
				var valida        =$('.form_valida_pago_completo').valid();
				var datos         ='operacion='+operacion+'&codigoSocio='+codigoSocio+'&tipoActividad='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&documentoPago='+documentoPago+'&fechaPago='+fechaPago+'&montoPago='+montoPago+razon;
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-pagos.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('button#bt_pagar_monto'+codigoConcepto).prop('disabled', true);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="MONTO_PAGADO"){
								new PNotify({title: 'CONFIRMACION', text: 'Monto de pago fue cancelado.', addclass: 'bg-success'});
								location.reload();
							}
							if(respuesta.mensaje=="ERROR_MONTO_PAGADO"){
								new PNotify({title: 'ADVERTENCIA', text: 'Ocurrido un error, por favor intentelo nuevamente.', addclass: 'bg-warning'});
								$('#documentoPago').focus();
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// PROGRAMAR PAGO
			///////////////////////////////////////////////////
			$("button#bt_programar_cuota"+codigoConcepto).click(function(){
				var opcion         ='programar_pago';
				var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+razon;
				$('#modulo_pago-'+codigoConcepto).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
				$('#modulo_pago-'+codigoConcepto).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
			});

			///////////////////////////////////////////////////
			/// BOTON PAGAR EN CASO DE FECHAS PROGRAMADAS
			///////////////////////////////////////////////////
			var estadoPago     ='<?= $estadoPago ?>';
			if(estadoPago=='MP' || estadoPago=='SP'){
				var opcion         ='pagar_fechas_programadas';
				var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+razon;
				$('#modulo_pago-'+codigoConcepto).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
				$("#modulo_pago-"+codigoConcepto).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
				$('button#bt_programar_cuota'+codigoConcepto).prop('disabled', true);
			}
			
			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO
			///////////////////////////////////////////////////
			var validator = $(".form_valida_pago_completo").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },

				validClass: "validation-valid-label",
				rules: {
					documentoPago: { required: true, },
					fechaPago: { required: true, },
				},
				messages: {
					documentoPago: { required: "Voucher de pago", },
					fechaPago: { required: "Fecha de pago", },
				}
			});
		});

		///////////////////////////////////////////////////
		/// VERIFICA VOUCHER
		///////////////////////////////////////////////////
		function verificaVOU(){
			var ruta           ='../';
			var codigoConcepto ='<?= $codigoConcepto ?>';
			var nroDoc         =$('input#documentoPago').val();
			var datos          ='documentoPago='+nroDoc;
			
			if($.trim(nroDoc).length>0){
				$.ajax({
					type: "POST",
					url: ruta+'php/verifica-voucher.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('#infoVOU').html('<i class=" icon-spinner3"></i>');
					},
					success: function(respuesta){
						if(respuesta.mensaje=="EXISTE"){
							$('button#bt_pagar_monto'+codigoConcepto).prop('disabled', true);
							$('#infoVOU').html('<i class="icon-alert text-danger"></i>');
							$('input#documentoPago').focus();
							$('input#documentoPago').val('');
							new PNotify({title: 'ADVERTENCIA', text: 'El voucher Nro. '+nroDoc+', ya esta registrado...', addclass: 'bg-warning'});
						}
						if(respuesta.mensaje=="NOEXISTE"){
							$('button#bt_pagar_monto'+codigoConcepto).prop('disabled', false);
							$('#infoVOU').html('OK');
							$('#infoVOU').html('<i class="icon-check text-success"></i>');
						}
					}
				});
			}else{
				$('button#bt_pagar_monto'+codigoConcepto).prop('disabled', true);	
				$('input#documentoPago').focus();
			}
		}
	</script>

	<form class="form_valida_pago_completo">
		<div class="form-group">
			<div class="row">
				<div class="col-sm-2">
					<div class="input-group">
						<span class="input-group-addon textoNegrita">S/.</span>
						<input type="hidden" id="montoPago" value="<?= $montoPago ?>">
						<input disabled="disabled" value="<?= $montoPago ?>" class="form-control input-lg textoNegrita">
					</div>
					<span class="label label-block label-default text-left tope2">Monto a pagar</span>
				</div>
				<div class="col-sm-3">
					<div class="input-group">
					<span class="input-group-addon textoNegrita"><div id="infoVOU"><i class="icon-barcode2"></i></div></span>
						<input type="text" name="documentoPago" id="documentoPago" required="required" class="form-control input-lg textoNegrita" onChange="verificaVOU('1');" onblur="verificaVOU('1');" autofocus >
					</div>
					<span class="label label-block label-default text-left tope2">N° de voucher de deposito</span>
				</div>
				<div class="col-sm-3">
					<div class="input-group">
						<span class="input-group-addon"><i class="icon-calendar"></i></span>
						<input type="text" name="fechaPago" id="fechaPago" class="form-control input-lg textoNegrita fechas" value="<?= $fechaHoy ?>" >
					</div>
					<span class="label label-block label-default text-left tope2">Fecha de pago</span>
				</div>
				<div class="col-sm-4">
					<button type="button" id="bt_pagar_monto<?= $codigoConcepto ?>" class="btn btn-lg bg-brown">Pagar</button>
					<button type="button" id="bt_programar_cuota<?= $codigoConcepto ?>" class="btn btn-lg btn-success">Programar</button>
					<button type="button" class="btn btn-lg btn-warning" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</form>
<?php } ?>

<?php if($opcion=="programar_pago"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// VARIBLE DE CONCEPTO DE PAGO
			///////////////////////////////////////////////////
			var ruta           ='../';
			var codigoSocio    ='<?= $codigoSocio ?>';
			var conceptoPago   ='<?= $conceptoPago ?>';
			var codigoConcepto ='<?= $codigoConcepto ?>';
			var montoPago      ='<?= $montoPago ?>';
			var razon          ='<?= $razon ?>';
			if(conceptoPago=="ASA" || conceptoPago=="FAE"){ var razon='&razon=<?= $razon ?>'; }else{ var razon=''; }

			///////////////////////////////////////////////////
			/// DESACTIVAR BOTON DE NRO DE CUOTAS
			///////////////////////////////////////////////////
			$('button#bt_programar_nro_cuotas').prop('disabled', true);

			///////////////////////////////////////////////////
			/// REGISTRO DE GASTOS DE PARTIDA
			///////////////////////////////////////////////////
			$("button#bt_volver_pagos").click(function(){
				var opcion ='modulo_pago_monto';
				var datos  ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+razon;
				$('#modulo_pago-'+codigoConcepto).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
				$("#modulo_pago-"+codigoConcepto).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
				$("#modulo_fechas_pago-"+codigoConcepto).hide();
			});

			///////////////////////////////////////////////////
			/// REGISTRO DE GASTOS DE PARTIDA
			///////////////////////////////////////////////////
			$("button#bt_programar_nro_cuotas").click(function(){
				var opcion         ='programar_fechas';
				var cuotasPago     =$('select#nroCuotas').val();
				var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&cuotasPago='+cuotasPago+razon;
				$('#modulo_fechas_pago-'+codigoConcepto).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
				$("#modulo_fechas_pago-"+codigoConcepto).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
			});

			///////////////////////////////////////////////////
			/// SELECTOR DE CANTIDAD DE FECHAS PARA MONTO
			///////////////////////////////////////////////////
			$("#nroCuotas").change(function(event){
				var cuotas = $("#nroCuotas").val();
				if(cuotas>0){
					$('button#bt_programar_nro_cuotas').prop('disabled', false);
					if(cuotas==2){ var texto="02"; }
					if(cuotas==3){ var texto="03"; }
					if(cuotas==4){ var texto="04"; }
					if(cuotas==5){ var texto="05"; }
					$("#nFechas").html(texto);
				}else{
					$('button#bt_programar_nro_cuotas').prop('disabled', true);
					$("#nFechas").html("");
				}
			});
		});
	</script>

	<form>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-3">
					<div class="input-group">
						<span class="input-group-addon textoNegrita">S/.</span>
						<input type="hidden" id="montoPago" value="<?= $montoPago ?>">
						<input disabled="disabled" value="<?= $montoPago ?>" class="form-control input-lg textoNegrita">
					</div>
				</div>
				<div class="col-sm-5">
					<div class="input-group">
						<span class="input-group-addon textoNegrita"><i class="icon-list"></i></span>
						<select id="nroCuotas" class="form-control input-lg textoMayuscula">
							<option value="">Seleccione Nro cuotas</option>
							<?php $i=2; while($i<=5){ ?>
								<option value="<?= $i ?>">Programar cuota en &rarr; <?= ceros($i,2) ?> Cuotas</option>
							<?php $i++; } ?>
						</select>
					</div>
				</div>
				<div class="col-sm-4 text-center">
					<button type="button" id="bt_volver_pagos" class="btn btn-icon btn-lg bg-brown"><i class=" icon-arrow-left8"></i></button>
					<button type="button" id="bt_programar_nro_cuotas" class="btn btn-lg btn-success">Programar &rarr; <span id="nFechas"></span> fechas</button>
					<button type="button" class="btn btn-icon btn-lg btn-warning" data-dismiss="modal"><i class=" icon-close2"></i></button>
				</div>
			</div>
		</div>
	</form>
<?php } ?>

<?php if($opcion=="programar_fechas"){ $cuotasPago=$_GET[cuotasPago]; ?>
	<hr>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// REGISTRAR FECHAS PROGRAMADAS
			///////////////////////////////////////////////////
			$("button#bt_finalizar_programacion").click(function(){
				var ruta           ='../';
				var cuotasPago     ='<?= $cuotasPago ?>';
				var codigoSocio    ='<?= $codigoSocio ?>';
				var conceptoPago   ='<?= $conceptoPago ?>';
				var codigoConcepto ='<?= $codigoConcepto ?>';
				var operacion      ='PROGRAMAR_FECHAS';
				var valida         =$('.form_valida_fechas').valid();
				var razon          ='<?= $razon ?>';
				if(conceptoPago=="ASA" || conceptoPago=="FAE"){ var razon='&razon=<?= $razon ?>'; }else{ var razon=''; }

				if(cuotasPago==2){
					var montoCuota1 =$('input#montoCuota1').val();
					var fechaPago1  =$('input#fechaPago1').val();
					var montoCuota2 =$('input#montoCuota2').val();
					var fechaPago2  =$('input#fechaPago2').val();
					var datosCuotas ='montoCuota1='+montoCuota1+'&fechaPago1='+fechaPago1+'&montoCuota2='+montoCuota2+'&fechaPago2='+fechaPago2;

				}

				if(cuotasPago==3){
					var montoCuota1 =$('input#montoCuota1').val();
					var fechaPago1  =$('input#fechaPago1').val();
					var montoCuota2 =$('input#montoCuota2').val();
					var fechaPago2  =$('input#fechaPago2').val();
					var montoCuota3 =$('input#montoCuota3').val();
					var fechaPago3  =$('input#fechaPago3').val();
					var datosCuotas ='montoCuota1='+montoCuota1+'&fechaPago1='+fechaPago1+'&montoCuota2='+montoCuota2+'&fechaPago2='+fechaPago2+'&montoCuota3='+montoCuota3+'&fechaPago3='+fechaPago3;
				}

				if(cuotasPago==4){
					var montoCuota1 =$('input#montoCuota1').val();
					var fechaPago1  =$('input#fechaPago1').val();
					var montoCuota2 =$('input#montoCuota2').val();
					var fechaPago2  =$('input#fechaPago2').val();
					var montoCuota3 =$('input#montoCuota3').val();
					var fechaPago3  =$('input#fechaPago3').val();
					var montoCuota4 =$('input#montoCuota4').val();
					var fechaPago4  =$('input#fechaPago4').val();
					var datosCuotas ='montoCuota1='+montoCuota1+'&fechaPago1='+fechaPago1+'&montoCuota2='+montoCuota2+'&fechaPago2='+fechaPago2+'&montoCuota3='+montoCuota3+'&fechaPago3='+fechaPago3+'&montoCuota4='+montoCuota4+'&fechaPago4='+fechaPago4;
				}

				if(cuotasPago==5){
					var montoCuota1 =$('input#montoCuota1').val();
					var fechaPago1  =$('input#fechaPago1').val();
					var montoCuota2 =$('input#montoCuota2').val();
					var fechaPago2  =$('input#fechaPago2').val();
					var montoCuota3 =$('input#montoCuota3').val();
					var fechaPago3  =$('input#fechaPago3').val();
					var montoCuota4 =$('input#montoCuota4').val();
					var fechaPago4  =$('input#fechaPago4').val();
					var montoCuota5 =$('input#montoCuota5').val();
					var fechaPago5  =$('input#fechaPago5').val();
					var datosCuotas ='montoCuota1='+montoCuota1+'&fechaPago1='+fechaPago1+'&montoCuota2='+montoCuota2+'&fechaPago2='+fechaPago2+'&montoCuota3='+montoCuota3+'&fechaPago3='+fechaPago3+'&montoCuota4='+montoCuota4+'&fechaPago4='+fechaPago4+'&montoCuota5='+montoCuota5+'&fechaPago5='+fechaPago5;
				}
				var datos=datosCuotas+'&cuotasPago='+cuotasPago+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&operacion='+operacion+razon;				
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-fechas-pago.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('button#bt_finalizar_programacion').prop('disabled', true);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="PROGRAMACION_FINALIZADA"){
								new PNotify({title: 'CONFIRMACION', text: 'La cuenta fue programda en '+cuotasPago+' cuotas de pago.', addclass: 'bg-success'});
								var opcion='modulo_pago_monto';
								var datos='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+razon;
								$('#modulo_pago-'+codigoConcepto).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
								$("#modulo_pago-"+codigoConcepto).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
								$("#modulo_fechas_pago-"+codigoConcepto).hide();
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO
			///////////////////////////////////////////////////
			var validator = $(".form_valida_fechas").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },

				validClass: "validation-valid-label",
				rules: {
					montoCuota1: { number: true, min: 1, },
					montoCuota2: { number: true, min: 1, },
					montoCuota3: { number: true, min: 1, },
					montoCuota4: { number: true, min: 1, },
					montoCuota5: { number: true, min: 1, },
					fechaPago1: { required: true },
					fechaPago2: { required: true },
					fechaPago3: { required: true },
					fechaPago4: { required: true },
					fechaPago5: { required: true },
					
				},
				messages: {
					montoCuota1: { required: "Monto 01", number: "Monto 01", min: "Minimo S/. 1", },
					montoCuota2: { required: "Monto 02", number: "Monto 02", min: "Minimo S/. 1", },
					montoCuota3: { required: "Monto 03", number: "Monto 03", min: "Minimo S/. 1", },
					montoCuota4: { required: "Monto 04", number: "Monto 04", min: "Minimo S/. 1", },
					montoCuota5: { required: "Monto 05", number: "Monto 05", min: "Minimo S/. 1", },
					fechaPago1: { required: "Fecha pago 01", },
					fechaPago2: { required: "Fecha pago 02", },
					fechaPago3: { required: "Fecha pago 03", },
					fechaPago4: { required: "Fecha pago 04", },
					fechaPago5: { required: "Fecha pago 05", },
				}
			});
		});
	</script>
	<form class="form_valida_fechas">
		<?php
			$montoMensual   =floor($montoPago/$cuotasPago);
			$totalMonto     =$montoMensual*$cuotasPago;
			$montoAdiconal  =$montoPago-$totalMonto;
			$cuotas         =1;
			
			while($cuotas<=$cuotasPago){
				if($cuotas==$cuotasPago){
					$montoMensual=$montoMensual+$montoAdiconal;
				}else{
					$montoMensual=$montoMensual;
				}
		?>
			<script type="text/javascript">
				$('input#totalMonto').val('0.00');

				$(document).ready(function(){
					var info='<?= $cuotas ?>';
					$('#montoCuota-1').focus();
					$(".fechasCuotas-"+info).datepicker({
						showButtonPanel: true,
						format: 'dd/mm/yyyy',
						minDate: "-1D",
						maxDate: "+3M",
						altField: "#infoFecha-"+info,
						altFormat: "DD, d MM, yy"
					});

					$(function(){ $('#montoCuota-1, #montoCuota-2, #montoCuota-3, #montoCuota-4, #montoCuota-5').validar('0123456789.'); });
				});

				function verificaMonto(){
					var cuotas    ='<?= $cuotasPago ?>';
					var montoPago ='<?= $montoPago ?>';
					var monto_01  =$('input#montoCuota1').val();
					var monto_02  =$('input#montoCuota2').val();	
					var monto_03  =$('input#montoCuota3').val();
					var monto_04  =$('input#montoCuota4').val();
					var monto_05  =$('input#montoCuota5').val();

					if($.trim(monto_01).length>0){ }else{ var monto_01   = 0; }
					if($.trim(monto_02).length>0){ }else{ var monto_02   = 0; }
					if($.trim(monto_03).length>0){ }else{ var monto_03   = 0; }
					if($.trim(monto_04).length>0){ }else{ var monto_04   = 0; }
					if($.trim(monto_05).length>0){ }else{ var monto_05   = 0; }

					var totalMonto  =parseFloat(monto_01)+parseFloat(monto_02)+parseFloat(monto_03)+parseFloat(monto_04)+parseFloat(monto_05);
					totalProgramado =Math.round(totalMonto*Math.pow(10,2))/Math.pow(10,2);

					if(totalProgramado>0){ $('input#totalMonto').val(totalProgramado); }else{ $('input#totalMonto').val('0.00'); }
					if(totalProgramado==montoPago){ $('button#bt_finalizar_programacion').prop('disabled', false); }else{ $('button#bt_finalizar_programacion').prop('disabled', true); }
				}
			</script>

			<div class="form-group">
				<div class="row">
					<div class="col-sm-2">
						<span class="btn btn-block no-cursor bg-grey textoMayuscula textoNegrita">Cuota <?= ceros($cuotas,2) ?></span>
					</div>
					<div class="col-sm-2">
						<div class="input-group">
							<span class="input-group-addon textoNegrita">S/.</span>
							<input type="text" name="montoCuota<?= $cuotas ?>" id="montoCuota<?= $cuotas ?>" required="required" placeholder="<?= moneda($montoMensual) ?>" class="form-control input-lg text-danger textoMayuscula textoNegrita" onChange="verificaMonto();" onblur="verificaMonto();">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="input-group">
							<span class="input-group-addon textoNegrita"><i class="icon-calendar"></i></span>
							<input type="text" name="fechaPago<?= $cuotas ?>" id="fechaPago<?= $cuotas ?>" required="required" class="form-control input-lg fechasCuotas-<?= $cuotas ?> text-danger textoMayuscula textoNegrita">
						</div>
					</div>
					<div class="col-sm-5">
						<div class="input-group">
							<span class="input-group-addon textoNegrita"><i class="icon-info3"></i></span>
							<input disabled="disabled" id="infoFecha-<?= $cuotas ?>" class="form-control input-lg text-danger textoMayuscula textoNegrita">
						</div>
					</div>
				</div>
			</div>
		<?php $cuotas++; } ?>
	<form>
	<hr>
	<div class="form-group">
		<div class="row">
			<div class="col-sm-7">
				<div class="input-group">
					<span class="input-group-addon bg-brown-300 textoNegrita">Total monto programdo de las <?= ceros($cuotasPago,2) ?> cuotas &rarr; <strong>S/.</strong></span>
					<input disabled="disabled" id="totalMonto" value="<?= $montoMensual ?>" class="form-control input-lg text-danger textoMayuscula textoNegrita">
				</div>
			</div>
			<div class="col-sm-5">
				<button type="button" id="bt_finalizar_programacion" class="btn btn-lg btn-block bg-brown">Finalizar programación</button>
			</div>
		</div>
	</div>
<?php } ?>

<?php if($opcion=="pagar_fechas_programadas"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// VARIBLE DE CONCEPTO DE PAGO
			///////////////////////////////////////////////////
			var ruta           ='../';
			var codigoSocio    ='<?= $codigoSocio ?>';
			var tipoActividad  ='<?= $conceptoPago ?>';
			var conceptoPago   ='<?= $conceptoPago ?>';
			var codigoConcepto ='<?= $codigoConcepto ?>';
			var razon          ='<?= $razon ?>';
			if(conceptoPago=="ASA" || conceptoPago=="FAE"){ var razon='&razon=<?= $razon ?>'; }else{ var razon=''; }

			///////////////////////////////////////////////////
			/// DATEPICKER
			///////////////////////////////////////////////////
			$(".fechaPago").datepicker({
				showButtonPanel: true,
				format: 'dd/mm/yyyy'
			});

			///////////////////////////////////////////////////
			/// BOTON PAGAR MONTO DE FECHA
			///////////////////////////////////////////////////
			$("button.bt_pagar_cuota_fecha").click(function(){
				var cuota          =$(this).attr('id');
				var operacion      ='PAGO_MONTO_FECHA';
				var montoPago      =$('input#montoPago'+cuota).val();
				var fechaPago      =$('input#fechaPago'+cuota).val();
				var documentoPago  =$('input#documentoPago'+cuota).val();
				var valida         =$('.form_valida_pago_fechas_'+cuota).valid();
				var datos          ='nroCuota='+cuota+'&montoPago='+montoPago+'&fechaPago='+fechaPago+'&documentoPago='+documentoPago+'&codigoConcepto='+codigoConcepto+'&codigoSocio='+codigoSocio+'&operacion='+operacion+'&tipoActividad='+tipoActividad+razon;
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-pagos.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('button#bt_pagar_cuota_fecha_'+cuota).prop('disabled', true);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="FECHA_MONTO_PAGADO"){
								new PNotify({title: 'CONFIRMACION', text: 'Monto de fecha programada cancelado.', addclass: 'bg-success'});
								var opcion='modulo_pago_monto';
								var datos='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+razon;
								$('#modulo_pago-'+codigoSocio).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
								$("#modulo_pago-"+codigoSocio).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
								location.reload();
							}
						}
					});
				}
			});
		});
	</script>

	<?php if($estadoPago=="MP" OR $estadoPago=="SP"){ echo '<div class="text-center"><a href="../documentos/cronograma-pagos.php?conceptoPago='.$conceptoPago.'&codigoConcepto='.$codigoConcepto.'&codigoSocio='.$codigoSocio.'" class="btn bg-teal"><i class="icon-printer2"></i> Imprimir cronograma de pagos</a></div><hr>'; }else echo ''; ?>

	<?php
		$fechasPagadas      =infoPagoFechas($codigoSocio,$codigoConcepto,'contarFechasPagadas');
		$totalFechasPagadas =infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasPagadas');
		$totalFechasSaldo   =infoPagoFechas($codigoSocio,$codigoConcepto,'totalFechasSaldo');
		if($totalFechasPagadas>0){ $infoTotalFechasPagadas='<strong>TOTAL PAGADO:</strong> S/. '.moneda($totalFechasPagadas).'</strong>'; }else{ $infoTotalFechasPagadas =""; }
		if($totalFechasSaldo>0){ $infoTotalFechasSaldo='&nbsp;&nbsp;|&nbsp;&nbsp;<strong>TOTAL POR PAGAR:</strong> S/. '.moneda($totalFechasSaldo).'</strong>'; }else{ $infoTotalFechasSaldo =""; }
		$infoPagos=$infoTotalFechasPagadas.$infoTotalFechasSaldo;

		
		$sql="SELECT cuota, fechaProgramada, montoPago, tipoDocumento, nroDocumento, fechaPago, estadoPago, codigoOperacion FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto' ORDER BY cuota ASC";
		$rs=mysqli_query($conexion,$sql);
		while($n=mysqli_fetch_array($rs)){
			$cuota           =$n[cuota];
			$fechaProgramada =$n[fechaProgramada];
			$montoPago       =$n[montoPago];
			$tipoDocumento   =$n[tipoDocumento];
			$nroDocumento    =$n[nroDocumento];
			$fechaPago       =$n[fechaPago];
			$estadoPago      =$n[estadoPago];
			$codigoOperacion =$n[codigoOperacion];
			if($estadoPago=="PEN"){ $boton='<button type="button" id="'.$cuota.'" class="btn btn-lg btn-icon bg-brown bt_pagar_cuota_fecha"><i class=" icon-coin-dollar"></i></button>'; }else{ $boton='<button type="button" id="bt_pagar_cuota_fecha_'.$cuota.'" class="btn btn-lg btn-icon bg-brown disabled"><i class=" icon-coin-dollar"></i></button>'; }
	?>
		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// NRO DE CUOTA ACTIVO
				///////////////////////////////////////////////////
				var cuota='<?= $cuota ?>';

				///////////////////////////////////////////////////
				/// VALIDAR FORMULARIO
				///////////////////////////////////////////////////
				var validator = $(".form_valida_pago_fechas_"+cuota).validate({
					errorClass: 'validation-error-label',
					highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
					unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },

					validClass: "validation-valid-label",
					rules: {
						fechaPago<?= $cuota ?>: { required: true },
						documentoPago<?= $cuota ?>: { required: true },
					},
					messages: {
						fechaPago<?= $cuota ?>: { required: "Fecha pago 0<?= $cuota ?>", },
						documentoPago<?= $cuota ?>: { required: "Voucher pago - fecha 0<?= $cuota ?>", },
					}
				});
			});

			///////////////////////////////////////////////////
			/// VERIFICA VOUCHER
			///////////////////////////////////////////////////
			function verificaVOU(nCuota){
				var ruta   ='../';
				var nroDoc =$('input#documentoPago'+nCuota).val();
				var datos  ='documentoPago='+nroDoc;
				if($.trim(nroDoc).length>0){
					$.ajax({
						type: "POST",
						url: ruta+'php/verifica-voucher.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('#infoVOU'+nCuota).html('<i class=" icon-spinner3"></i>');
						},
						success: function(respuesta){
							if(respuesta.mensaje=="EXISTE"){
								$('#infoVOU'+nCuota).html('<i class="icon-alert text-danger"></i>');
								$('input#documentoPago'+nCuota).focus();
								$('input#documentoPago'+nCuota).val('');
								new PNotify({ text: 'El voucher Nro. '+nroDoc+', ya esta registrado...', addclass: 'bg-warning alert-styled-right', type: 'warning' });
							}
							if(respuesta.mensaje=="NOEXISTE"){
								$('#infoVOU'+nCuota).html('OK');
								$('#infoVOU'+nCuota).html('<i class="icon-check text-success"></i>');
							}
						}
					});
				}else{
					$('input#documentoPago'+nCuota).focus();
				}
			}
		</script>
		<?php
			if($estadoPago=="PEN"){
				$mensaje='<div class="form-control input-lg textoMayuscula"><span class="label label-warning">Fecha '.ceros($cuota,2).'</span> S/.'.moneda($montoPago).' &rarr; '.infoFecha($fechaProgramada,'corta').'</div>';
		?>
			<form class="form_valida_pago_fechas_<?= $cuota ?>">
				<input type="hidden" id="montoPago<?= $cuota ?>" value="<?= $montoPago ?>" >
				<div class="form-group">
					<div class="row">
						<div class="col-sm-5">
							<?= $mensaje ?>
						</div>
						<div class="col-sm-3">
							<div class="input-group">
								<span class="input-group-addon textoNegrita"><div id="infoVOU<?= $cuota ?>"><i class="icon-barcode2"></i></div></span>
								<input type="text" name="documentoPago<?= $cuota ?>" id="documentoPago<?= $cuota ?>" class="form-control input-lg text-danger textoNegrita" onChange="verificaVOU('<?= $cuota ?>');" onblur="verificaVOU('<?= $cuota ?>');">
							</div>
						</div>
						<div class="col-sm-3">
							<div class="input-group">
								<span class="input-group-addon"><i class="icon-calendar"></i></span>
								<input type="text" name="fechaPago<?= $cuota ?>" id="fechaPago<?= $cuota ?>" class="form-control input-lg text-danger textoNegrita fechaPago<?= $cuota ?>" value="<?= $fechaHoy ?>" >
							</div>
						</div>
						<div class="col-sm-1">
							<?= $boton ?>
						</div>
					</div>
				</div>
			</form>
		<?php }	?>
	<?php }	?>

	<?php if($fechasPagadas>0){ ?>
		<div class="form-group">
			<table class="table tabla table-bordered">
				<thead>
					<tr class="success">
						<th class="text-center">CUOTA</th>
						<th class="text-center">COMPROMISO</th>
						<th class="text-center">MONTO</th>
						<th class="text-center">VOUCHER</th>
						<th class="text-center">F. DE PAGO</th>
						<th class="text-center" colspan="2">ESTADO DE PAGO</th>
					</tr>
				</thead>
				<tbody>
				<?php
					$sql="SELECT cuota, fechaProgramada, montoPago, tipoDocumento, nroDocumento, fechaPago, estadoPago, codigoOperacion FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto' AND estadoPago='PGD' ORDER BY cuota ASC";
					$rs=mysqli_query($conexion,$sql);
					while($n=mysqli_fetch_array($rs)){
						$cuota           =$n[cuota];
						$fechaProgramada =$n[fechaProgramada];
						$montoPago       =$n[montoPago];
						$tipoDocumento   =$n[tipoDocumento];
						$nroDocumento    =$n[nroDocumento];
						$fechaPago       =$n[fechaPago];
						$estadoPago      =$n[estadoPago];
						$codigoOperacion =$n[codigoOperacion];
						if($estadoPago=="PEN"){
							$boton='<button type="button" id="bt_pagar_cuota_fecha_'.$cuota.'" class="btn btn-lg btn-icon bg-brown"><i class=" icon-coin-dollar"></i></button>';
							$infoEstado='<span class="label bg-warning-400">PENDIENTE</span>';
							$infoTiempoPago='<span class="label bg-warning-400">Sin pago</span>';
						}
						if($estadoPago=="PGD"){
							$boton='<button type="button" id="bt_pagar_cuota_fecha_'.$cuota.'" class="btn btn-lg btn-icon bg-brown disabled"><i class=" icon-coin-dollar"></i></button>';
							$infoEstado='<span class="label label-success">PAGADO</span>';
							$infoTiempoPago='<span class="label bg-success-400">'.haceTiempo($fechaPago).'</span>';
						}
				?>

					<form class="form_valida_pago_fechas_<?= $cuota ?>">
						<input type="hidden" id="montoPago<?= $cuota ?>" value="<?= $montoPago ?>" >
						<tr>
							<td class="text-center"><span class="label bg-grey"><?= 'Fecha '.ceros($cuota,2) ?></span></td>
							<td class="text-center textoMayuscula"><?= infoFecha($fechaProgramada,'normal') ?></td>
							<td class="text-right text-danger textoNegrita"><?= '&nbsp;&nbsp;S/.'.moneda($montoPago) ?></td>
							<td class="text-center"><?= $nroDocumento ?></td>
							<td class="text-center textoMayuscula"><?= infoFecha($fechaPago,'corta') ?></td>
							<td class="text-center"><?= $infoEstado ?></td>
							<td class="text-center"><?= $infoTiempoPago ?></td>
						</tr>
					</form>
				<?php }	?>
					<tr>
						<td class="text-center bg-grey-300" colspan="7"><?= $infoPagos ?></td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>
<?php } ?>