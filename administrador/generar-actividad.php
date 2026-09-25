<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$tipoActividad=isset($_GET['tipoActividad']) ? $_GET['tipoActividad'] : '';
	$operacion="REGISTRA_ACTIVIDAD";
	$rotuloACTma="";
	$rotuloACTmi="";
	$menuActual="";
	
	if($tipoActividad=="ASA"){
		$rotuloACTma="ASAMBLEA";
		$rotuloACTmi="asamblea";
		$menuActual="generarAsamblea";
	}

	if($tipoActividad=="FAE"){
		$rotuloACTma="FAENA";	
		$rotuloACTmi="faena";
		$menuActual="generarFaena";
	}
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="GENERAR ".$rotuloACTma;

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>

	<div class="panel">
		<div class="panel-heading bg-teal">
			<div id="procesando"></div>
			<h6 class="panel-title">REGISTRO DE NUEVA <?= $rotuloACTma ?></h6>
		</div>
		<div class="panel-body info">
			<form id="form_actividades" class="form_valida">
				<input type="hidden" name="tipoActividad" value="<?= $tipoActividad ?>">
				<input type="hidden" name="operacion" value="<?= $operacion ?>">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<input type="text" id="temaActividad" name="temaActividad" required="required" class="form-control input-lg textoMayuscula" placeholder="Tema <?= $rotuloACTmi ?>" onblur="mayusculas(event, this)" autofocus>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<input type="text" id="lugarActividad" name="lugarActividad" required="required" class="form-control input-lg textoMayuscula" placeholder="Lugar <?= $rotuloACTmi ?>" onblur="mayusculas(event, this)">
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<select id="formaActividad" name="formaActividad" required="required" class="seleccionar select-lg textoMayuscula">
								<option value="" selected>REGISTRO DE ASISTENCIA</option>
								<option value="1">POR INTERNET</option>
								<option value="0">POR ARCHIVO JSON</option>
							</select>
						</div>
					</div>
					<div class="col-md-2 col-xs-2">
						<div class="form-group">
							<select id="formaControl" name="formaControl" required="required" class="seleccionar select-lg textoMayuscula">
								<option value="" selected>FORMA DE CONTROL</option>
								<option value="1">AL FINALIZAR</option>
								<option value="0">INGRESO / SALIDA</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="row">
							<div class="col-md-3 col-xs-3">
								<div class="form-group">
									<label class="text-brown textoNegrita">Fecha de <?= $rotuloACTmi ?></label>
									<input type="text" id="fechaActividad" name="fechaActividad" required="required" class="form-control input-lg fechasACT textoMayuscula" placeholder="Fecha <?= $rotuloACTmi ?>">
								</div>
							</div>

							<div class="col-md-3 col-xs-3">
								<div class="form-group">
									<label class="text-brown textoNegrita">Hora de <?= $rotuloACTmi ?></label>
									<select id="horaActividad" name="horaActividad" required="required" class="seleccionar select-lg">
										<option value="" selected>SELECCIONE HORA</option>
										<?php
											$conexion=conexionDB();
											$sql="SELECT horas FROM sm_a_horas";
											$rs=mysqli_query($conexion,$sql);
											while($datos=mysqli_fetch_array($rs)){
												if($datos[0]=='10:00:00'){
													echo '<option selected value="'.$datos[0].'">'.$datos[0].'</option>';
												}else{
													echo '<option value="'.$datos[0].'">'.$datos[0].'</option>';
												}
											}
										?>
									</select>
								</div>
							</div>

							<div class="col-md-3 col-xs-3">
								<div class="form-group">
									<label class="text-brown textoNegrita">Multa por Tardanza</label>
									<input type="text" id="mTardanza" name="mTardanza" required="required" class="form-control input-lg" placeholder="Multa S/.">
								</div>
							</div>

							<div class="col-md-3 col-xs-3">
								<div class="form-group">
									<label class="text-brown textoNegrita">Multa por Inasistencia</label>
									<input type="text" id="mFalta" name="mFalta" required="required" class="form-control input-lg" placeholder="Multa S/.">
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
									<label class="text-brown textoNegrita">Junta Directiva</label>
									<select id="idJuntaDirectiva" name="idJuntaDirectiva" required="required" class="seleccionar select-lg text-uppercase">
										<option value="" selected>SELECCIONE JUNTA DIRECTIVA</option>
										<?php
											$sql="SELECT idJuntaDirectiva, fechaPeriodo, fechaFinPeriodo FROM sm_junta_directiva";
											$rs=mysqli_query($conexion,$sql);
											while($datos=mysqli_fetch_array($rs)){
												$idJuntaDirectiva  =$datos['idJuntaDirectiva'];
												$fechaPeriodo    =$datos['fechaPeriodo'];
												$fechaFinPeriodo =$datos['fechaFinPeriodo'];
												$infoFechaInicio= infoFecha($fechaPeriodo, 'year');
												$infoFechaFin= infoFecha($fechaFinPeriodo, 'year');

												$query="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
												$consulta = $conexion->query($query);
												$resultado = $consulta->fetch_assoc();
												$nombrePresidente=isset($resultado['nombrePresidente']) ? $resultado['nombrePresidente'] : '';
												$infoJuntaDirectiva=$nombrePresidente.'&nbsp;&nbsp;|&nbsp;&nbsp;'.$infoFechaInicio.' - '.$infoFechaFin;
												
												echo '<option value="'.$idJuntaDirectiva.'">'.$infoJuntaDirectiva.'</option>';
											}
										?>
									</select>
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
									<label class="text-brown textoNegrita">Cuenta de pago <?= $rotuloACTmi ?></label>
									<select id="codigoCuenta" name="codigoCuenta" required="required" class="seleccionar select-lg">
										<option value="" selected>SELECCIONE BANCO</option>										
									</select>
								</div>
							</div>

							<div class="col-md-6 col-xs-6">
								<div class="form-group">
									<label class="text-brown textoNegrita">Penalidad por Incumplimineto de Pago</label>
									<input type="text" id="mPenalidad" name="mPenalidad" required="required" class="form-control input-lg" placeholder="Multa S/.">
								</div>
							</div>

							<div class="col-md-6 col-xs-6">
								<div class="form-group">
									<label class="text-brown textoNegrita">Fecha de Inicio de Penalidad</label>
									<input type="text" id="fechaInicioPenalidad" name="fechaInicioPenalidad" required="required" class="form-control textoMayuscula input-lg" placeholder="Fecha">
								</div>
							</div>

							<div class="col-md-12">
								<div class="form-group">
									<label class="text-brown textoNegrita">Confirmación de fecha</label>
									<div class="input-group">
										<span class="input-group-addon textoNegrita"><i class="icon-info3"></i></span>
										<input disabled="disabled" id="infoFecha" class="form-control input-lg text-danger textoMayuscula textoNegrita">
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-group">
									<button type="button" id="bt_almacenar_actividad" class="btn btn-lg btn-success btn-block">Almacenar <?= $rotuloACTmi ?></button>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<textarea cols="18" rows="19" id="contenidoActividad" name="contenidoActividad" required="required" class="form-control textoMayuscula" onblur="mayusculas(event, this)" placeholder="Detalles adicionales de <?= $rotuloACTmi ?>..."></textarea>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// VARIABLES DEL SISTEMA
			///////////////////////////////////////////////////
			var ruta          ='<?= $ruta ?>';
			var tipoActividad ='<?= $tipoActividad ?>';

			///////////////////////////////////////////////////
			/// GENERAR ACTIVIDAD
			///////////////////////////////////////////////////
			$("button#bt_almacenar_actividad").click(function(){
				var datos  =$('#form_actividades').serialize();
				var valida =$('.form_valida').valid();
				console.log(datos);
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-actividades.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){
							$('.info').fadeOut("slow");
							$('#procesando').fadeIn("slow").html('<div class="pull-right procesando"><i class="icon-spinner3 spinner"></i> Asignando <?= $rotuloACTmi ?> a socios...&nbsp;&nbsp;</div>');
						},
						success: function(respuesta){
							console.log(respuesta);
							if(respuesta.mensaje=="ACTIVIDAD_ALMACENADA"){
								if(tipoActividad=="ASA"){ window.location.replace("lista-asambleas.php"); }
								if(tipoActividad=="FAE"){ window.location.replace("lista-faenas.php"); }
							}
							if(respuesta.mensaje=="ERROR_ACTIVIDAD_ALMACENADA"){
								new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// CONFIGURACION DE DATEPICKER
			///////////////////////////////////////////////////
			$(".fechasACT, #fechaInicioPenalidad").datepicker({
				showButtonPanel: true,
				format: 'dd/mm/yyyy',
				altField: "#infoFecha",
				altFormat: "DD, d MM, yy",
				minDate: "+0D",
				maxDate: "+3M"
			});

			///////////////////////////////////////////////////
			/// CAMBIO DE ESTILO DE INPUT FILE
			///////////////////////////////////////////////////
			$('.seleccionar').select2();

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

			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO
			///////////////////////////////////////////////////
			var validator = $(".form_valida").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
				validClass: "validation-valid-label",
				rules: {
					temaActividad: { required: true },
					formaActividad: { required: true },
					formaControl: { required: true },
					lugarActividad: { required: true },
					fechaActividad: { required: true },
					horaActividad: { required: true },
					mTardanza: { required: true, number: true, min: 0 },
					mFalta: { required: true, number: true, min: 0 },
					idJuntaDirectiva:{ required: true },
					codigoCuenta: { required: true },
					mPenalidad: { required: true, number: true, min: 0 },
					fechaInicioPenalidad: { required: true },
					contenidoActividad: { required: true },
				},
				messages: {
					temaActividad: { required: "Tema de <?= $rotuloACTmi ?>", },
					formaActividad: { required: "Forma de registro de", },
					formaControl: { required: "Forma control asistencia", },
					lugarActividad: { required: "Lugar de <?= $rotuloACTmi ?>", },
					fechaActividad: { required: "Fecha de <?= $rotuloACTmi ?>", },
					horaActividad: { required: "Hora de <?= $rotuloACTmi ?>", },
					mTardanza: { required: "Multa por tardanza", number: "Solo numeros", min: "Mínimo S/. 0", },
					mFalta: { required: "Multa por inasistencia", number: "Solo numeros", min: "Mínimo S/. 0", },
					idJuntaDirectiva:{ required: "Seleccione Junta Directiva" },
					codigoCuenta: { required: "Seleccione Cuenta de banco <?= $rotuloACTmi ?>", },
					mPenalidad: { required: "Monto de penalidad", number: "Solo numeros", min: "Mínimo S/. 0", },
					fechaInicioPenalidad: { required: "Fecha de inicio de penalidad" },
					contenidoActividad: { required: "Detalles adicionales...", },
				}
			});
		});

		// VALIDA FORMULARIO SOLO NUMEROS
		$(function(){ $('#mTardanza, #mFalta, #mPenalidad').validar('0123456789.'); });
	</script>
<?php include($ruta.'template/footer.tpl'); ?>