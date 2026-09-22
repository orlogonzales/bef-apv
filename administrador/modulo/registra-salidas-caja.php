<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$montoDisponible =montoDisponible('','');
	$conexion=conexionDB();
?>

<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// CONFIGURACION OCULTA TIPO DE RESPONSABLE
		///////////////////////////////////////////////////
		$("#usuario").hide();
		$("#socio").hide();

		///////////////////////////////////////////////////
		/// REGISTRO DE GASTOS DE PARTIDA
		///////////////////////////////////////////////////
		$("button#bt_registra_salidas").click(function(){
			var ruta            ='../';
			var movimiento      =$('input#movimiento').val();
			var responsableSAL  =$('select#responsableSAL').val();
			var concepto        =$('input#concepto').val();
			var detalleConcepto =$('input#detalleConceptoSAL').val();
			var fechaOperacion  =$('input#fechaOperacionSAL').val();
			var monto           =$('input#montoSAL').val();
			var tipoDocumento   =$('select#tipoDocumentoSAL').val();
			var nroDocumento    =$('input#nroDocumentoSAL').val();
			var observaciones   =$('textarea#observacionesSAL').val();
			var operacion       ='REGISTRA_SALIDA_CAJA';
			var datos           ='movimiento='+movimiento+'&tipoResposanble='+tipoResposanble+'&responsableSAL='+responsableSAL+'&concepto='+concepto+'&detalleConcepto='+detalleConcepto+'&fechaOperacion='+fechaOperacion+'&monto='+monto+'&tipoDocumento='+tipoDocumento+'&nroDocumento='+nroDocumento+'&observaciones='+observaciones+'&operacion='+operacion;
			var valida          =$('.form_valida_salidas').valid();
			if(valida){
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-caja.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('button#bt_registra_salidas').prop('disabled', true);
					},
					success: function(respuesta){
						if(respuesta.mensaje=="SALIDA_REGISTRADA"){
							new PNotify({title: 'CONFIRMACION', text: 'Los datos de salida de caja han sido registrados.', addclass: 'bg-success'});
							$("#registroSalidas").modal('hide');
							location.reload();
						}
					}
				});
			}
		});

		///////////////////////////////////////////////////
		/// VALIDAR FORMULARIO DE SALIDAS DE CAJA
		///////////////////////////////////////////////////
		var validator = $(".form_valida_salidas").validate({
			errorClass: 'validation-error-label',
			highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
			unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },

			validClass: "validation-valid-label",
			rules: {
				tipoResposanble: { required: true },
				detalleConceptoSAL: { required: true },
				fechaOperacionSAL: { required: true },
				montoSAL: { number: true, min: 1, max: <?= $montoDisponible ?> },
				tipoDocumentoSAL: { required: true },
				nroDocumentoSAL: { required: true },
				tipoResposanble: { required: true },
				responsableSAL: { required: true },
			},
			messages: {
				tipoResposanble: { required: "Tipo de responsable", },
				detalleConceptoSAL: { required: "Ingrese detalle de salida", },
				fechaOperacionSAL: { required: "Seleccione fecha de salida", },
				montoSAL: { required: "Monto de salida", number: "Monto de salida", min: "Minimo S/. 1", max: "Maximo S/."+<?= $montoDisponible ?>, },
				tipoDocumentoSAL: { required: "Seleccione tipo de documento", },
				nroDocumentoSAL: { required: "Ingrese Nro. de documento", },
				tipoResposanble: { required: "Seleccione responsable", },
				responsableSAL: { required: "Seleccione responsable de salida", },
			}
		});

		///////////////////////////////////////////////////
		/// VALIDAR CAMPOS NUMERICOS
		///////////////////////////////////////////////////
		$(function(){ $('#montoSAL').validar('0123456789.'); });
		$(function(){ $('#fechaOperacionSAL').validar('0123456789/'); });

		///////////////////////////////////////////////////
		/// TIPO DE RESPONSABLE DE CAJA
		///////////////////////////////////////////////////
		$("#tipoResposanble").change(function(event){
			var responsable = $("#tipoResposanble").val();

			if(responsable=='USR'){
				$('button#bt_almacenar_partida').prop('disabled', true);
				$("#responsableSAL").html('<option value="" selected>CARGANDO DATOS...</option>');
				$("#responsableSAL").load('../php/lista-usuario-sistema.php');

			}
			if(responsable=='SOC'){
				$('button#bt_almacenar_partida').prop('disabled', true);
				$("#responsableSAL").html('<option value="" selected>CARGANDO DATOS...</option>');
				$("#responsableSAL").load('../php/lista-socios.php');
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

<form id="form_partida" class="form_valida_salidas">
	<input type="hidden" id="movimiento" value="SAL">
	<input type="hidden" id="concepto" value="VAR">
	<div class="modal-body">
		<div class="form-group">
			<div class="row">
				<div class="col-sm-4">
					<select name="tipoResposanble" id="tipoResposanble" required="required" class="form-control input-lg" tabindex="1" autofocus>
						<option value="" selected>SELECCIONE</option>
						<option value="USR">USUARIO DE SISTEMA</option>
						<option value="SOC">SOCIO DE LA APV</option>
					</select>
					<span class="label label-block bg-grey-300 text-left">Responsable de gasto</span>
				</div>
				<div class="col-sm-8">
					<select name="responsableSAL" id="responsableSAL" required="required" class="form-control input-lg" tabindex="2">
						<option value="" selected>SELECCIONE</option>
					</select>
					<span class="label label-block bg-grey-300 text-left">Responsable</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<input type="text" name="detalleConceptoSAL" id="detalleConceptoSAL" required="required" class="form-control input-lg textoMayuscula" onblur="mayusculas(event, this)" placeholder="DETALLES DE GASTO" tabindex="3" autofocus>
					<span class="label label-block bg-grey-300 text-left">Detalle de salida de caja</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-3">
					<input type="text" name="fechaOperacionSAL" id="fechaOperacionSAL" required="required" class="form-control fechas input-lg" tabindex="4">
					<span class="label label-block bg-grey-300 text-left">Fecha de salida</span>
				</div>
				<div class="col-sm-2">
					<input type="text" name="montoSAL" id="montoSAL" required="required" class="form-control input-lg" tabindex="5">
					<span class="label label-block bg-grey-300 text-left">Monto de salida</span>
				</div>
				<div class="col-sm-4">
					<select name="tipoDocumentoSAL" id="tipoDocumentoSAL" required="required" class="form-control input-lg" tabindex="6">
						<option value="" selected>SELECCIONE</option>
						<?php
							$sql="SELECT * FROM sm_a_doc_pago";
							$rs=mysqli_query($conexion,$sql);
							while($datos=mysqli_fetch_array($rs)){
								echo '<option value="'.$datos[0].'">'.$datos[1].'</option>';
							}
						?>
					</select>
					<span class="label label-block bg-grey-300 text-left">Tipo de documento</span>
				</div>
				<div class="col-sm-3">
					<input type="text" name="nroDocumentoSAL" id="nroDocumentoSAL" required="required" class="form-control input-lg" tabindex="7">
					<span class="label label-block bg-grey-300 text-left">Numero de documento</span>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="row">
				<div class="col-sm-12">
					<textarea rows="5" cols="5" name="observacionesSAL" id="observacionesSAL" class="form-control textoMayuscula" onblur="mayusculas(event, this)" placeholder="OBSERVACIONES" tabindex="8"></textarea>
					<span class="label label-block bg-grey-300 text-left">Detalles / observaciones de salida de caja</span>
				</div>
			</div>
		</div>
		<div class="form-group_">
			<div class="row text-center">
				<div class="col-sm-12">
					<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
					<button type="button" id="bt_registra_salidas" class="btn bg-success">ALAMACENAR PARTIDA</button>
				</div>
			</div>
		</div>
	</div>
	<div class="modal-footer">
	</div>
</form>