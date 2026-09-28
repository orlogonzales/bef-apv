<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="LISTA DE SOCIOS";
	$menuActual="listaSocios";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>

<div class="panel">
	<div class="panel-body">
		<form id="form_consulta">
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
				<div class="col-md-2">
					<div class="form-group">
						<select id="ordenar" name="ordenar" required="required" class="seleccionar select-lg text-uppercase">
							<option value="LOTE" selected="">ORDENAR POR LOTE</option>}
							<option value="NOMBRE">ORDENAR POR NOMBRE</option>}
							<option value="APELLIDO">ORDENAR POR APELLIDO PATERNO</option>}
						</select>
					</div>
				</div>
				<div class="col-md-2">
					<button type="button" id="bt_consulta" class="btn btn-lg btn-success btn-block"><strong>CONSULTA SOCIOS</strong></button>
				</div>
				<div class="col-md-2">
					<button type="button" id="btnImprmirReporte" class="btn btn-block btn-info btn-lg"><i class="fa fa-print" aria-hidden="true"></i> <strong>IMPRIMIR</strong></button>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="listaSocios"></div>
<div id="loader" style="display:none;">
	<div class="cargando"><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><p>CARGANDO CONSULTA</p></div></div></div></div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// VARIABLES DEL SISTEMA
		///////////////////////////////////////////////////
		var ruta          ='<?= $ruta ?>';
		$('#btnImprmirReporte').prop('disabled',true);

		///////////////////////////////////////////////////
		/// LIVE VALIDATION
		///////////////////////////////////////////////////
		//$('#form_consulta').on('focusout', 'select', function () {
		$('#form_consulta').on('change', function () {
	        $(this).valid();
	        const sector = $('#sector').val();
	        const manzana = $('#manzana').val();
	        if(sector!='' && manzana!=''){
	        	$('#btnImprmirReporte').prop('disabled',false);
	        }
	    });

		///////////////////////////////////////////////////
		/// GENERAR ACTIVIDAD
		///////////////////////////////////////////////////
		$("button#bt_consulta").click(function(){
			var datos  =$('#form_consulta').serialize();
			var valida =$('#form_consulta').valid();
			var urlProceso= "modulo/lista-socios.php";
			if(valida){
				$.ajax({
					url: urlProceso,
					type: 'POST',
					data: datos,
					beforeSend: function() {
						$(loader).show();
					},
					success: function (response) {
						$(loader).hide();
						$('#listaSocios').html(response);
					},
					error: function (xhr, status, error) {
	                    console.error('Error:', error);
	                }
				});
			}
		});

		///////////////////////////////////////////////////
		/// IMPRIMIR REPORTE
		///////////////////////////////////////////////////
		$('#btnImprmirReporte').click(function (e) {
			e.preventDefault();
			const sector = $('#sector').val();
	        const manzana = $('#manzana').val();
	        const ordenar = $('#ordenar').val();
	        const datos = 'sector='+sector+'&manzana='+manzana+'&ordenar='+ordenar;
	        const urlProceso = '../documentos/reporte-socios.php?'+datos;
	        window.location.href = urlProceso;
		});

		///////////////////////////////////////////////////
		/// SELECT2
		///////////////////////////////////////////////////
		$('.seleccionar').select2({
            allowClear: true
        });

		///////////////////////////////////////////////////
		/// SECTORES
		///////////////////////////////////////////////////
	    $('#sector').change(function (e) {
	    	e.preventDefault();
	    	var valida =$('#form_consulta').valid();
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
		var validator = $("#form_consulta").validate({
			errorClass: 'validation-error-label',
			highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
			unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
			validClass: "validation-valid-label",
			rules: {
				sector:{ required: true },
				manzana: { required: true },
				ordenar: { required: true },
			},
			messages: {
				sector:{ required: "Seleccione Sector" },
				manzana: { required: "Seleccione Manzana", },
				ordenar: { required: "Seleccione como ordenar la consulta", },
			},
			errorPlacement: function (error, element) {
                // Coloca los mensajes de error
                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2-container').find('.select2-selection').addClass('error');
                } else {
                    $(element).addClass('error');
                }
            },
            unhighlight: function (element) {
                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).next('.select2-container').find('.select2-selection').removeClass('error');
                } else {
                    $(element).removeClass('error');
                }
            },
		});
	});
</script>
<?php include($ruta.'template/footer.tpl'); ?>