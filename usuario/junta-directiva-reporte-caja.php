<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina ="REPORTE CAJA DE JUNTA DIRECTIVA";
	$menuActual   ="reporteCajaJD";

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
			        <div class="col-md-4">
			            <div class="form-group">
			                <div class="input-group">
			                    <select name="idJuntaDirectiva" id="idJuntaDirectiva" class="seleccionar text-uppercase">
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

			        <div class="col-md-1">
			            <div class="form-group">
			                <select name="movimiento" id="movimiento" class="seleccionar text-uppercase">
			                    <option value="">-- MOVIMIENTO --</option>
			                    <option value="ING">INGRESOS</option>
			                    <option value="SAL">EGRESOS</option>
			                </select>
			            </div>
			        </div>

			        <div class="col-md-2">
			            <div class="form-group">
			                <select name="tipoActividad" id="tipoActividad" class="seleccionar text-uppercase">
			                    <option value="">-- TIPO ACTIVIDAD --</option>
			                    <option value="CUO">CUOTAS</option>
			                    <option value="ASA">ASAMBLEAS</option>
			                    <option value="FAE">FAENAS</option>
			                </select>
			            </div>
			        </div>

			    	<div class="col-md-5">
			            <div class="form-group">
			                <select name="codigoConcepto" id="codigoConcepto" class="seleccionar text-uppercase">
			                    <option value="">-- CONCEPTO --</option>
			                    <option value="ALL">TODOS LOS CONCEPTOS</option>
			                </select>
			            </div>
			        </div>
			    </div>

			    <div class="row">
			        <div class="col-md-6">
			            <div class="form-group">
			                <select name="codigoCuenta" id="codigoCuenta" class="seleccionar text-uppercase">
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
			/// DESACTIVA CAMPOS Y BOTON
			///////////////////////////////////////////////////
			$('#movimiento').prop('disabled', true);
			$('#tipoActividad').prop('disabled', true);
			$('#codigoConcepto').prop('disabled', true);
			$('#codigoCuenta').prop('disabled', true);
			$('#fechaInicio').prop('disabled', true);
			$('#fechaFin').prop('disabled', true);
			$('#generarConsulta').prop('disabled', true);
			
			///////////////////////////////////////////////////
			/// CAMBIO DE ESTILO DE INPUT FILE
			///////////////////////////////////////////////////
			$('.seleccionar').select2();

			///////////////////////////////////////////////////
			/// OBTENCION DE FECHAS DE PERIODO JUNTA DIRETIVA
			///////////////////////////////////////////////////
			$('#idJuntaDirectiva').change(function (e) {
			    e.preventDefault();
			    const idJuntaDirectiva = $('#idJuntaDirectiva').val();

			    if (idJuntaDirectiva.length>0) {
        			$('#movimiento').val(null).trigger('change');
					$('#movimiento').prop('disabled', false);
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
			    }else{
					$('#movimiento').val(null).trigger('change');
        			$('#tipoActividad').val(null).trigger('change');
        			$('#codigoConcepto').val(null).trigger('change');
        			$('#codigoCuenta').val(null).trigger('change');
        			$('#fechaInicio').val('');
					$('#fechaFin').val('');
					$('#movimiento').prop('disabled', true);
					$('#tipoActividad').prop('disabled', true);
					$('#codigoConcepto').prop('disabled', true);
					$('#codigoCuenta').prop('disabled', true);
					$('#fechaInicio').prop('disabled', true);
					$('#fechaFin').prop('disabled', true);
					$('#generarConsulta').prop('disabled', true);
			    }
			});

			///////////////////////////////////////////////////
			/// SEGUN TIPO DE MOVIMIENTO INGRESO O EGRESO
			///////////////////////////////////////////////////
			$('#movimiento').change(function (e) {
				const idJuntaDirectiva = $('#idJuntaDirectiva').val();
			    const movimiento = $('#movimiento').val();

			    if(movimiento.length>0){
				    if (idJuntaDirectiva.length>0 && movimiento=='ING'){
		    			$('#tipoActividad').val(null).trigger('change');
		    			$('#codigoConcepto').val(null).trigger('change');
		    			$('#codigoCuenta').val(null).trigger('change');
				    	$('#tipoActividad').prop('disabled', false);
				    	$('#codigoConcepto').prop('disabled', false);
						$('#codigoCuenta').prop('disabled', false);
						$('#fechaInicio').prop('disabled', false);
						$('#fechaFin').prop('disabled', false);
						$('#generarConsulta').prop('disabled', false);
				    }else{
				    	$('#tipoActividad').val(null).trigger('change');
		    			$('#codigoConcepto').val(null).trigger('change');
		    			$('#codigoCuenta').val(null).trigger('change');
				    	$('#tipoActividad').prop('disabled', true);
				    	$('#codigoConcepto').prop('disabled', true);
						$('#codigoCuenta').prop('disabled', true);
				    	$('#codigoCuenta').prop('disabled', false);
				    	$('#fechaInicio').prop('disabled', false);
						$('#fechaFin').prop('disabled', false);
				    	$('#generarConsulta').prop('disabled', false);
				    }
			    }
			});

			///////////////////////////////////////////////////
			/// SEGUN TIPO DE ACTIVIDAD
			///////////////////////////////////////////////////
			$('#tipoActividad').change(function (e) {
				const idJuntaDirectiva = $('#idJuntaDirectiva').val();
			    const tipoActividad = $('#tipoActividad').val();
			    
			    $('#codigoConcepto').val(null).trigger('change');
			    $('#codigoCuenta').val(null).trigger('change');
			    $('#fechaInicio').val('');
				$('#fechaFin').val('');

			    if(idJuntaDirectiva.length>0 &&tipoActividad.length>0){
			    	if(tipoActividad!='CUO'){
			    		$.ajax({
			                url: '../modulo/combo-obtener-actividades-junta-directiva.php',
			                type: 'POST',
			                data: { idJuntaDirectiva: idJuntaDirectiva, tipoActividad: tipoActividad },
			                dataType: 'json',
			                success: function (respuesta) {
			                    if (respuesta.actividades.length > 0) {
			                        let opciones = '<option value="" selected>-- SELECCIONE ACTIVIDAD --</option><option value="ALL">TODOS LOS CONCEPTOS</option>';
			                        respuesta.actividades.forEach(function (actividad) {
			                            opciones += `<option value="${actividad.codigoActividad}">${actividad.nombreActividad}</option>`;
			                        });
			                        $('#codigoConcepto').html(opciones);
			                    } else {
			                        $('#codigoConcepto').html('<option value="">-- NO HAY ACTIVIDADES DISPONIBLES --</option>');
			                    }
			                },
			                error: function (xhr, status, error) {
			                    console.error('Error en la solicitud AJAX:', error);
			                }
			            });
			    	}else{
			    		$.ajax({
				            url: '../modulo/combo-obtener-cuotas-junta-directiva.php',
				            type: 'POST',
				            data: { idJuntaDirectiva: idJuntaDirectiva },
				            dataType: 'json',
				            success: function (respuesta) {
				                if (respuesta.cuotas.length > 0) {
				                    let opciones = '<option value="" selected>-- SELECCIONE CONCEPTO DE CUOTA --</option><option value="ALL" selected>TODAS LAS CUOTAS</option>';
				                    respuesta.cuotas.forEach(function (cuota) {
									    opciones += `<option value="${cuota.codigoCuota}">${cuota.nombreCuota}</option>`;
									});
				                    $('#codigoConcepto').html(opciones);
				                } else {
				                    $('#codigoConcepto').html('<option value="">-- NO HAY CUOTAS DISPONIBLES --</option>');
				                }
				            },
				            error: function (xhr, status, error) {
				                console.error('Error en la solicitud AJAX:', error);
				            }
				        });
			    	}
			    }
			});

			
			$('#codigoConcepto').change(function (e) {
				const idJuntaDirectiva = $('#idJuntaDirectiva').val();
			    const tipoActividad = $('#tipoActividad').val();
			    const codigoConcepto = $('#codigoConcepto').val();
			    
			    if(idJuntaDirectiva.length>0 && tipoActividad.length>0 && codigoConcepto.length>0){
			    	if(tipoActividad!='CUO'){
			    		if(codigoConcepto!='ALL'){
			    			$.ajax({
					            url: '../modulo/combo-obtener-cuentas-actividades-junta-directiva.php',
					            type: 'POST',
					            data: { idJuntaDirectiva: idJuntaDirectiva, tipoActividad: tipoActividad, codigoActividad: codigoConcepto },
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
			    			$('#codigoCuenta').val(null).trigger('change');
			    			$('#codigoCuenta').prop('disabled', true);
			    		}
			    	}else{
			    		if(codigoConcepto!='ALL'){
			    			$.ajax({
					            url: '../modulo/combo-obtener-cuentas-cuotas-junta-directiva.php',
					            type: 'POST',
					            data: { idJuntaDirectiva: idJuntaDirectiva, codigoCuota: codigoConcepto },
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
			    			$('#codigoCuenta').val(null).trigger('change');
			    			$('#codigoCuenta').prop('disabled', true);
			    		}
			    	}
			    }
			});

			///////////////////////////////////////////////////
			/// GENERAR ACTIVIDAD
			///////////////////////////////////////////////////
			$("button#generarConsulta").click(function(){
				const datos  =$('#formConsulta').serialize();
				const valida =$('#formConsulta').valid();
				if(valida){
					$.ajax({
						url: ruta+'modulo/resultados-consulta-caja-jd.php',
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
			/// VALIDAR FORMULARIO
			///////////////////////////////////////////////////
			const validator = $("#formConsulta").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
				validClass: "validation-valid-label",
				rules: {
					idJuntaDirectiva : { required: true },
					movimiento       : { required: true },
					tipoActividad    : { required: true },
					codigoConcepto   : { required: true },
					codigoCuenta     : { required: true },
					fechaInicio      : { required: true },
					fechaFin         : { required: true },
				},
				messages: {
					idJuntaDirectiva : { required: "Seleccionar Junta Directiva" },
					movimiento       : { required: "Tipo Movimiento" },
					tipoActividad    : { required: "Tipo Actividad" },
					codigoConcepto   : { required: "Elija Actividad" },
					codigoCuenta     : { required: "Seleccione Cuenta Bancaria" },
					fechaInicio      : { required: "Fecha de Inicio", },
					fechaFin         : { required: "Fecha de Fin", },
				}
			});
		});
	</script>
<?php include($ruta.'template/footer.tpl'); ?>