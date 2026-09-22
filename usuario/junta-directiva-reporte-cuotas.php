<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina ="REPORTE CUOTAS DE JUNTA DIRECTIVA";
	$menuActual   ="reporteCuotasJD";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>
	<div class="panel">
		<div class="panel-heading bg-dark">
			<h6 class="panel-title textoNegrita">GENERA CONSULTA</h6>
		</div>
		<div class="panel-body">
			<form id="formConsulta">
			    <div class="row">
			        <div class="col-md-5">
			            <div class="form-group">
			                <div class="input-group">
			                    <select name="idJuntaDirectiva" id="idJuntaDirectiva" class="form-control text-uppercase">
			                        <option value="" selected>-- SELECCIONE JUNTA DIRECTIVA --</option>
			                        <?php
			                            $sql = "SELECT idJuntaDirectiva, fechaPeriodo, fechaFinPeriodo FROM sm_junta_directiva";
			                            $rs = mysqli_query($conexion, $sql);
			                            while ($datos = mysqli_fetch_array($rs)) {
			                                $idJuntaDirectiva  = $datos["idJuntaDirectiva"];
			                                $fechaPeriodo      = $datos["fechaPeriodo"];
			                                $fechaFinPeriodo   = $datos["fechaFinPeriodo"];
			                                $infoFechaInicio   = infoFecha($fechaPeriodo, 'year');
			                                $infoFechaFin      = infoFecha($fechaFinPeriodo, 'year');

			                                $query = "SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente 
			                                          FROM sm_junta_directiva_integrantes 
			                                          INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio 
			                                          WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";

			                                $consulta = $conexion->query($query);
			                                $resultado = $consulta->fetch_assoc();
			                                $nombrePresidente = $resultado["nombrePresidente"];
			                                $infoJuntaDirectiva = "$nombrePresidente | $infoFechaInicio - $infoFechaFin";

			                                echo '<option value="'.$idJuntaDirectiva.'">'.$infoJuntaDirectiva.'</option>';
			                            }
			                        ?>
			                    </select>
			                    <span class="input-group-addon bg-danger">
			                        <span id="infoGestion" class="text-uppercase">GESTION</span>
			                    </span>
			                </div>
			            </div>
			        </div>

			        <div class="col-md-5">
			            <div class="form-group">
			                <select name="codigoCuota" id="codigoCuota" class="form-control text-uppercase">
			                    <option value="ALL">TODAS LAS CUOTAS</option>
			                </select>
			            </div>
			        </div>

			        <div class="col-md-2">
			            <div class="form-group">
			                <select name="estadoPago" id="estadoPago" class="form-control text-uppercase">
			                    <option value="">-- ESTADO PAGO --</option>
			                    <option value="ALL">TODOS (PAGADOS Y NO PAGADOS)</option>
			                    <option value="SI">SI PAGADOS</option>
			                    <option value="NO">NO PAGADOS</option>
			                </select>
			            </div>
			        </div>
			    </div>

			    <div class="row">
			        <div class="col-md-6">
			            <div class="form-group">
			                <select name="codigoCuenta" id="codigoCuenta" class="form-control text-uppercase">
			                    <option value="">-- SELECCIONE CUENTA --</option>
			                    <option value="ALL">-- TODAS LAS CUENTAS --</option>
			                </select>
			            </div>
			        </div>

			        <div class="col-md-2">
			            <div class="form-group">
			                <input type="text" name="fechaInicio" id="fechaInicio" class="form-control" placeholder="FECHA INICIO">
			            </div>
			        </div>

			        <div class="col-md-2">
			            <div class="form-group">
			                <input type="text" name="fechaFin" id="fechaFin" class="form-control" placeholder="FECHA FIN">
			            </div>
			        </div>

			        <div class="col-md-2">
			            <div class="form-group">
			                <button type="button" id="generarConsulta" class="btn btn-block btn-dark btn-lg"><strong>GENERAR CONSULTA</strong></button>
			            </div>
			        </div>
			    </div>
			</form>

		</div>
	</div>

	<div id="boxResultados"></div>

	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// VARIABLES DEL SISTEMA
			///////////////////////////////////////////////////
			const ruta          ='<?= $ruta ?>';

			///////////////////////////////////////////////////
			/// DESACTIVA CAMPOS Y BOTON
			///////////////////////////////////////////////////
			$('#codigoCuota').prop('disabled', true);
			$('#estadoPago').prop('disabled', true);
			$('#codigoCuenta').prop('disabled', true);
			$('#fechaInicio').prop('disabled', true);
			$('#fechaFin').prop('disabled', true);
			$('#generarConsulta').prop('disabled', true);			
			
			///////////////////////////////////////////////////
			/// CAMBIO DE ESTILO DE INPUT FILE
			///////////////////////////////////////////////////
			$('.seleccionar').select2();

			///////////////////////////////////////////////////
			/// GENERAR ACTIVIDAD
			///////////////////////////////////////////////////
			$("button#generarConsulta").click(function(){
				const datos  =$('#formConsulta').serialize();
				const valida =$('#formConsulta').valid();
				if(valida){
					$.ajax({
						url: ruta+'modulo/resultados-consulta-cuotas-jd.php',
						type: 'POST',
						data: datos,
						beforeSend: function() {
							$('#procesando').fadeIn("slow").html('<div class="pull-right procesando"><i class="icon-spinner3 spinner"></i> Asignando <?= $rotuloACTmi ?> a socios...&nbsp;&nbsp;</div>');
						},
						success: function (response) {
							$('#procesando').fadeOut("slow").html('<div class="pull-right procesando"><i class="icon-spinner3 spinner"></i> Asignando <?= $rotuloACTmi ?> a socios...&nbsp;&nbsp;</div>');
							$('#boxResultados').html(response);
						},
						error: function (xhr, status, error) {
		                    console.error('Error:', error);
		                }
					});
				}
			});

			///////////////////////////////////////////////////
			/// CONFIGURACION DE DATEPICKER
			///////////////////////////////////////////////////
			const today = new Date();

		    $("#fechaInicio").datepicker({
		        showButtonPanel: true,
		        dateFormat: 'dd/mm/yy',  // Formato correcto
		        maxDate: "+1D",
		        changeYear: true,
		        showOtherMonths: true
		    });

		    $("#fechaFin").datepicker({
		        showButtonPanel: true,
		        dateFormat: 'dd/mm/yy',
		        maxDate: "+1D",
		        changeYear: true,
		        showOtherMonths: true
		    });

			///////////////////////////////////////////////////
			/// OBTENCION DE FECHAS DE PERIODO JUNTA DIRETIVA
			///////////////////////////////////////////////////
			$('#idJuntaDirectiva').change(function (e) {
			    e.preventDefault();
			    const idJuntaDirectiva = $('#idJuntaDirectiva').val();

			    if (idJuntaDirectiva.length>0) {
        			$('#codigoCuota').val(null).trigger('change');
        			$('#estadoPago').val(null).trigger('change');
        			$('#codigoCuenta').val(null).trigger('change');
					$('#codigoCuota').prop('disabled', false);
					$('#estadoPago').prop('disabled', false);
					$('#codigoCuenta').prop('disabled', false);
					$('#fechaInicio').prop('disabled', false);
					$('#fechaFin').prop('disabled', false);
					$('#generarConsulta').prop('disabled', false);
			        $.ajax({
			            url: '../modulo/obtener-periodo-junta-directiva.php',
			            type: 'POST',
			            data: { idJuntaDirectiva: idJuntaDirectiva },
			            dataType: 'json',
			            success: function(respuesta) {
			                if (respuesta.error) {
			                    alert(respuesta.error);
			                } else {
			                    const fechaInicio = respuesta.fechaInicioPeriodo;
			                    const fechaFin = respuesta.fechaFinPeriodo;
			                    const infoGestion = respuesta.infoGestion;			                    
			                    $("#infoGestion").html(infoGestion);
			                    $("#fechaInicio").datepicker("option", "minDate", fechaInicio);
			                    $("#fechaInicio").datepicker("option", "maxDate", fechaFin);			                    
			                    $("#fechaFin").datepicker("option", "minDate", fechaInicio);
			                    $("#fechaFin").datepicker("option", "maxDate", fechaFin);
			                }
			            },
			            error: function(xhr, status, error) {
			                console.error('Error en la solicitud AJAX:', error);
			            }
			        });

			        $.ajax({
			            url: '../modulo/combo-obtener-cuotas-junta-directiva.php',
			            type: 'POST',
			            data: { idJuntaDirectiva: idJuntaDirectiva },
			            dataType: 'json',
			            success: function (respuesta) {
			                if (respuesta.cuotas.length > 0) {
			                    let opciones = '<option value="" selected>-- SELECCIONE CONCEPTO DE CUOTA --</option><option value="ALL">TODAS LAS CUOTAS</option>';			                    
			                    respuesta.cuotas.forEach(function (cuota) {
								    opciones += `<option value="${cuota.codigoCuota}">${cuota.nombreCuota}</option>`;
								});
			                    $('#codigoCuota').html(opciones);
			                } else {
			                    $('#codigoCuota').html('<option value="">-- NO HAY CUOTAS DISPONIBLES --</option>');
			                }
			            },
			            error: function (xhr, status, error) {
			                console.error('Error en la solicitud AJAX:', error);
			            }
			        });
			    }else{
        			$('#codigoCuota').val(null).trigger('change');
        			$('#estadoPago').val(null).trigger('change');
        			$('#codigoCuenta').val(null).trigger('change');
        			$('#fechaInicio').val('');
					$('#fechaFin').val('');
					$('#codigoCuota').prop('disabled', true);
					$('#estadoPago').prop('disabled', true);
					$('#codigoCuenta').prop('disabled', true);
					$('#fechaInicio').prop('disabled', true);
					$('#fechaFin').prop('disabled', true);
					$('#generarConsulta').prop('disabled', true);
			    }
			});

			///////////////////////////////////////////////////
			/// OBTENER CUENTAS DE LA ACTIVIDAD
			///////////////////////////////////////////////////
			$('#codigoCuota').change(function (e) {
			    const idJuntaDirectiva = $('#idJuntaDirectiva').val();
			    const codigoCuota = $('#codigoCuota').val();

			    if (idJuntaDirectiva.length>0 && codigoCuota!='ALL') {
			        $.ajax({
			            url: '../modulo/combo-obtener-cuentas-cuotas-junta-directiva.php',
			            type: 'POST',
			            data: { idJuntaDirectiva: idJuntaDirectiva, codigoCuota: codigoCuota },
			            dataType: 'json',
			            success: function (respuesta) {
			                if (respuesta.cuentasBancarias.length > 0) {
			                    let opciones = '<option value="">-- SELECCIONE CUENTA --</option>';
			                    respuesta.cuentasBancarias.forEach(function (cuenta) {
			                        opciones += `<option value="${cuenta.codigoCuenta}">${cuenta.infoCuenta}</option>`;
			                    });
			                    $('#codigoCuenta').html(opciones);
			                } else {
			                    $('#codigoCuenta').html('<option value="">-- NO HAY CUENTAS DISPONIBLES --</option>');
			                }
			            },
			            error: function (xhr, status, error) {
			                console.error('Error en la solicitud AJAX:', error);
			            }
			        });
			    }else{
			    	let opciones = '<option value="ALL" selected>TODAS LAS CUENTAS</option>';
			    	$('#codigoCuenta').html(opciones);
			    }
			});

			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO
			///////////////////////////////////////////////////
			const validator = $("#formConsulta").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
				validClass: "validation-valid-label",
				rules: {
					idJuntaDirectiva: { required: true },
					codigoCuota: { required: true },
					estadoPago: { required: true },
					codigoCuenta: { required: true },
					fechaInicio: { required: true },
					fechaFin: { required: true },
				},
				messages: {
					idJuntaDirectiva: { required: "Seleccionar Junta Directiva" },
					codigoCuota: { required: "Tipo Actidad" },
					estadoPago: { required: "Estado de Pago" },
					codigoCuenta: { required: "Seleccione Cuenta Bancaria" },
					fechaInicio: { required: "Fecha de Inicio", },
					fechaFin: { required: "Fecha de Fin", },
				}
			});
		});
	</script>
<?php include($ruta.'template/footer.tpl'); ?>