<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina ="REPORTE DE <strong>DEUDAS DE JUNTA DIRECTIVA</strong>";
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
			        <div class="col-md-7">
			            <div class="form-group">
			                <div class="input-group">
			                    <select name="idJuntaDirectiva" id="idJuntaDirectiva" class="form-control text-uppercase">
			                        <option value="" selected>-- SELECCIONE JUNTA DIRECTIVA --</option>
			                        <option value="ALL">TODAS LAS JUNTAS DIRECTIVAS</option>
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
			                <select name="concepto" id="concepto" class="form-control text-uppercase">
			                	<option value="" selected>SELECCIONE CONCEPTO</option>
			                    <option value="ALL">TODOS LOS CONCEPTOS</option>
			                    <option value="CUO">CUOTAS</option>
			                    <option value="ASA">ASAMBLEAS</option>
			                    <option value="FAE">FAENAS</option>
			                </select>
			            </div>
			        </div>
			    </div>

			    <div class="row">
			        <div class="col-md-3">
			            <div class="form-group">
			                <select id="sector" name="sector" required="required" class="seleccionar select-lg text-uppercase">
							<option value="" selected>SELECCIONE SECTOR</option>
							<option value="ALL">TODOS LOS SECTORES</option>
							<?php
								$sql="SELECT sector FROM sm_lotes_socio GROUP BY sector ORDER BY sector ASC";
								$rs=mysqli_query($conexion,$sql);
								while($datos=mysqli_fetch_array($rs)){
									$sector  =$datos['sector'];
									echo '<option value="'.$sector.'"> SECTOR - '.$sector.'</option>';
								}
							?>
						</select>
			            </div>
			        </div>

			        <div class="col-md-3">
			            <div class="form-group">
							<select id="manzana" name="manzana" required="required" class="seleccionar select-lg">
								<option value="" selected>SELECCIONE MANZANA</option>
								<option value="ALL">TODOS LAS MANZANAS</option>
							</select>
						</div>
			        </div>

			        <div class="col-md-3">
						<div class="form-group">
							<select id="ordenar" name="ordenar" required="required" class="seleccionar select-lg text-uppercase">
								<option value="LOTE" selected="">ORDENAR POR LOTE</option>}
								<option value="NOMBRE">ORDENAR POR NOMBRE</option>}
								<option value="APELLIDO">ORDENAR POR APELLIDO PATERNO</option>}
							</select>
						</div>
					</div>

			        <div class="col-md-3">
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
			$('#concepto').prop('disabled', true);
			$('#sector').prop('disabled', true);
			$('#manzana').prop('disabled', true);
			$('#ordenar').prop('disabled', true);
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
						url: ruta+'modulo/resultados-consulta-deudas-jd.php',
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
			/// OBTENCION DE FECHAS DE PERIODO JUNTA DIRETIVA
			///////////////////////////////////////////////////
			$('#idJuntaDirectiva').change(function (e) {
			    e.preventDefault();
			    const idJuntaDirectiva = $('#idJuntaDirectiva').val();

			    if (idJuntaDirectiva.length>0) {
        			$('#concepto').prop('disabled', false);
					$('#sector').prop('disabled', false);
					$('#manzana').prop('disabled', false);
					$('#ordenar').prop('disabled', false);
					$('#generarConsulta').prop('disabled', false);

					if(idJuntaDirectiva!='ALL'){
						$.ajax({
				            url: '../modulo/obtener-periodo-junta-directiva.php',
				            type: 'POST',
				            data: { idJuntaDirectiva: idJuntaDirectiva },
				            dataType: 'json',
				            success: function(respuesta) {
				                if (respuesta.error) {
				                    alert(respuesta.error);
				                } else {
				                    const infoGestion = respuesta.infoGestion;
				                    $("#infoGestion").html(infoGestion);
				                }
				            },
				            error: function(xhr, status, error) {
				                console.error('Error en la solicitud AJAX:', error);
				            }
				        });
					}else{
						$("#infoGestion").html('TODAS LAS GESTIONES');
					}
			    }else{
        			$('#concepto').val(null).trigger('change');
        			$('#sector').val(null).trigger('change');
        			$('#manzana').val(null).trigger('change');
					$('#concepto').prop('disabled', true);
					$('#sector').prop('disabled', true);
					$('#manzana').prop('disabled', true);
					$('#ordenar').prop('disabled', true);
					$('#generarConsulta').prop('disabled', true);
			    }
			});

			///////////////////////////////////////////////////
			/// SECTORES
			///////////////////////////////////////////////////
		    $('#sector').change(function (e) {
		    	e.preventDefault();
		    	var valida =$('#formConsulta').valid();
		    	const sector = $('#sector').val();
				$('#manzana').val(null).trigger('change');
		        $('#manzana').empty().append('<option value="" selected>SELECCIONE MANZANA</option><option value="ALL">TODAS LAS MANZANAS</option>');
		        if (sector) {
		            $.ajax({
		                url: '../modulo/combo-obtener-manzanas.php',
		                type: 'POST',
		                data: { sector: sector },
		                dataType: 'json',
		                success: function(response) {
		                    if (response.length > 0) {
		                        $.each(response, function(index, manzanas) {
		                            $('#manzana').append('<option value="' + manzanas.manzana + '">' + manzanas.manzana + '</option>');
		                        });
		                    }
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
					idJuntaDirectiva: { required: true },
					concepto: { required: true },
					sector: { required: true },
					manzana: { required: true },
					ordenar: { required: true },
				},
				messages: {
					idJuntaDirectiva: { required: "Seleccionar Junta Directiva" },
					concepto: { required: "Seleccione Concepto" },
					sector: { required: "Seleccone Sector" },
					manzana: { required: "Seleccione Manzana" },
					ordenar: { required: "Fecha de Fin", },
				}
			});
		});
	</script>
<?php include($ruta.'template/footer.tpl'); ?>