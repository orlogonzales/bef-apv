<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina ="GENERAR CUOTA";
	$menuActual   ="generarCuota";
	$operacion    ="REGISTRA_CUOTA";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>
<div class="panel">
	<div class="panel-heading bg-teal">
		<div id="procesando"></div>
		<h6 class="panel-title textoNegrita">AGREGAR CUOTA</h6>
	</div>
	<div class="panel-body info">
		<form id="form_cuotas" class="form_valida">
			<input type="hidden" name="operacion" value="<?= $operacion ?>">
			<div class="row">
				<div class="col-md-7">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<input type="tetx" id="conceptoCuota" name="conceptoCuota" required="required" class="form-control textoMayuscula" placeholder="Concepto de cuota" onblur="mayusculas(event, this)" tabindex="1" autofocus>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 col-xs-6">
							<div class="form-group">
								<input type="text" id="montoCuota" name="montoCuota" required="required" class="form-control input-lg textoMayuscula" placeholder="Monto cuota" tabindex="2">
							</div>
						</div>
						<div class="col-md-6 col-xs-6">
							<div class="form-group">
								<input type="text" id="fechaPago" name="fechaPago" required="required" class="form-control input-lg fecha textoMayuscula" placeholder="Fecha limite de pago" tabindex="3">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<select id="idJuntaDirectiva" name="idJuntaDirectiva" required="required" class="seleccionar select-lg text-uppercase">
									<option value="" selected>SELECCIONE JUNTA DIRECTIVA</option>
									<?php
										$sql="SELECT idJuntaDirectiva, fechaPeriodo, fechaFinPeriodo FROM sm_junta_directiva";
										$rs=mysqli_query($conexion,$sql);
										while($datos=mysqli_fetch_array($rs)){
											$idJuntaDirectiva  =$datos[idJuntaDirectiva];
											$fechaPeriodo    =$datos[fechaPeriodo];
											$fechaFinPeriodo =$datos[fechaFinPeriodo];
											$infoFechaInicio= infoFecha($fechaPeriodo, 'year');
											$infoFechaFin= infoFecha($fechaFinPeriodo, 'year');

											$query="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
											$consulta = $conexion->query($query);
											$resultado = $consulta->fetch_assoc();
											$nombrePresidente=$resultado[nombrePresidente];
											$infoJuntaDirectiva=$nombrePresidente.'&nbsp;&nbsp;|&nbsp;&nbsp;'.$infoFechaInicio.' - '.$infoFechaFin;
											
											echo '<option value="'.$idJuntaDirectiva.'">'.$infoJuntaDirectiva.'</option>';
										}
									?>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<select id="codigoCuenta" name="codigoCuenta" required="required" class="seleccionar select-lg">
									<option value="" selected>CUENTA DE PAGO - SELECCIONE BANCO</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="input-group">
									<span class="input-group-addon textoNegrita"><i class="icon-info3"></i></span>
									<input disabled="disabled" id="infoFecha-<?= $cuotas ?>" class="form-control input-lg textoMayuscula">
								</div>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<button type="button" id="bt_almacenar_actividad" class="btn btn-lg btn-success btn-block">Almacenar Cuota</button>
							</div>
						</div>
					</div>		
				</div>
				<div class="col-md-5">
					<div class="form-group">
						<textarea cols="18" rows="13" id="observacion" name="observacion" class="form-control textoMayuscula" required="required" onblur="mayusculas(event, this)" placeholder="Detalles adicionales de cuota..."></textarea>
					</div>
				</div>
			</div>
		</form>		
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// CAMBIO DE ESTILO DE INPUT FILE
		///////////////////////////////////////////////////
		$('.seleccionar').select2();

		$(".fecha").datepicker({
			showButtonPanel: true,
			format: 'dd/mm/yyyy',
			minDate: "-1D",
			maxDate: "+3M",
			altField: "#infoFecha-",
			altFormat: "DD, d MM, yy"
		});

		var ruta='<?= $ruta ?>';
		$("button#bt_almacenar_actividad").click(function(){
			var datos = $('#form_cuotas').serialize();
			var valida = $('.form_valida').valid();
			console.log(datos);
			if(valida){
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-cuotas.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('.info').fadeOut("slow");
						$('#procesando').fadeIn("slow").html('<div class="pull-right procesando"><i class="icon-spinner3 spinner"></i> Asignando cuota a socios...&nbsp;&nbsp;</div>');
					},
					success: function(respuesta){
						console.log(respuesta);
						if(respuesta.mensaje=="CUOTA_ALMACENADA"){
							window.location.replace("lista-cuotas.php");
						}
						
						if(respuesta.mensaje=="ERROR_CUOTA_ALMACENADA"){
							new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
						}
					}
				});
			}
		});

		///////////////////////////////////////////////////
		/// CUENTAS DE BANCO DE JUNTA DIRECTIVA
		///////////////////////////////////////////////////
	    $('#idJuntaDirectiva').change(function (e) {
	    	e.preventDefault();
	    	const idJuntaDirectiva = $('#idJuntaDirectiva').val();
	        $('#codigoCuenta').empty().append('<option value="" selected>SELECCIONE CUENTA</option>');
	        if (idJuntaDirectiva) {
	            $.ajax({
	                url: '../modulo/combo-obtener-cuentas-junta-directiva.php',
	                type: 'POST',
	                data: { idJuntaDirectiva: idJuntaDirectiva },
	                dataType: 'json',
	                success: function(response) {
	                    if (response.length > 0) {
	                        $.each(response, function(index, cuenta) {
	                            $('#codigoCuenta').append('<option value="' + cuenta.codigoCuenta + '">' + cuenta.infoCuenta + '</option>');
	                        });
	                    }
	                }
	            });
	        }
	    });

		// VALIDA CAMPOS DE FORMULARIO
		var validator = $(".form_valida").validate({
			errorClass: 'validation-error-label',
			highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
			unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
			validClass: "validation-valid-label",
			rules: {
				conceptoCuota: { required: true },
	 			fechaPago: { required: true },
	 			montoCuota: { number: true, min: 5 },
	 			idJuntaDirectiva:{ required: true },
	 			codigoCuenta: { required: true },
	 			codigoCuenta: { required: true },
	 			observacion: { required: true },
			},
			messages: {
				conceptoCuota: { required: "Concepto de Cuota", },
				fechaPago: { required: "Fecha de pago", },
				montoCuota: { required: "Monto de Cuota", min: "Minimo S/. 5", },
				idJuntaDirectiva:{ required: "Seleccione Junta Directiva" },
				codigoCuenta: { required: "Seleccione Cuenta de banco", },
				codigoCuenta: { required: "Seleccione Cuenta de banco para cuota", },
				observacion: { required: "Detalles adicionales de cuota", },
			}
		});
	});

	// VALIDA FORMULARIO SOLO NUMEROS
	$(function(){ $('#montoCuota').validar('0123456789.'); });
</script>
<?php include($ruta.'template/footer.tpl'); ?>