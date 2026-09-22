<?php
	session_start();
	include '../php/funciones.php';
	include '../php/conexion.php';
	$proceso=$_POST['proceso'];
	$codigoActividad=$_POST['codigoActividad'];
	$sql="SELECT tipoActividad, temaActividad, fechaActividad, lugarActividad FROM sm_mod_actividades WHERE codigoActividad = '$codigoActividad'";
	$consulta = $conexion->query($sql);
	$resultado = $consulta->fetch_assoc();
	$tipoActividad=$resultado['tipoActividad'];
	$temaActividad=$resultado['temaActividad'];
	$fechaActividad=$resultado['fechaActividad'];
	$lugarActividad=$resultado['lugarActividad'];
	$idJuntaDirectiva=$_SESSION['idJDActual'];
	$infoFechaActividad=infoFecha($fechaActividad,'normal');

	if($tipoActividad='ASA'){
		$rotuloTipoActividad='ASAMBLEA';
	}else{
		$rotuloTipoActividad='FAENA';
	}
?>
<?php if($_POST['proceso']=='CONTROL_ASISTENCIA' && !empty($_POST['codigoActividad'])){ ?>
	<div class="modal-body">
    	<div class="row bg-teal-300 mb-15" style="margin-top: -10px !important;">
			<div class="col-md-12" style="padding: 10px 10px 10px 10px; font-size: 15px;">
				<strong>ACTIVIDAD</strong><span class="pull-right"><?= $temaActividad ?></span>
			</div>
		</div>
	    <form id="form_control_asistencia" class="border-bottom">
	    	<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<input type="number" id="dniSocio" name="dniSocio" class="form-control input-lg" placeholder="INGRESE DNI">
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
	    				<button type="button" id="btn_registra_asistencia" class="btn btn-lg btn-dark btn-block">REGISTRA</button>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<input type="hidden" name="codigoActividad" value="<?= $codigoActividad ?>">
						<input type="hidden" name="operacion" value="REGISTRA_ASISTENCIA_ACTIVIDAD">
	    				<button type="button" class="btn btn-lg btn-danger btn-block" data-dismiss="modal">CERRAR</button>
					</div>
				</div>
			</div>
	    </form>
		<table id="jdDataTable" class="table table-bordered table-hover table-striped table-td-valign-middle ajustar mb-15">
			<thead class="bg-dark">
				<tr>
					<th class="text-center">#</th>
					<th class="text-left">CODIGO</th>
					<th class="text-left">NOMBRE DE SOCIO</th>
					<th class="text-left">DNI</th>
					<th class="text-left">LTS</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			////////////////////////////////////////////////////////////
			/// REGISTRO
			////////////////////////////////////////////////////////////
			$("input#dniSocio").focus();
			$("button#btn_registra_asistencia").click(function(){
				var datos  =$('#form_control_asistencia').serialize();
				var valida =$('#form_control_asistencia').valid();
				var urlProceso= "../php/mantenimiento-socios.php";
				if(valida){
					$.ajax({
						url: urlProceso,
						type: 'POST',
						data: datos,
						dataType:'json',
						beforeSend: function() {
							$('#form_control_asistencia *').prop('disabled',true);
						},
						success: function (respuesta) {
							console.log(respuesta);
							$('#form_control_asistencia *').prop('disabled',false);

							if(respuesta.resultado=='SOCIO_CON_MULTA'){
								$("input#dniSocio").focus();
								swal({
									title: "SOCIO CON MULTA",
									text: "EL SOCIO INGRESADO, YA FUE PROCESADO Y POSEE UNA MULTA POR INASISTENCIA",
									type: "error",
									confirmButtonText: "ENTIENDO"
								});
							}

							if(respuesta.resultado=='ASISTENCIA_SOCIO_REGISTRADO'){
								$('#form_control_asistencia').trigger("reset");
								jdDataTable.ajax.reload();
								swal({
									title: "REGISTRADO",
									text: "EL SOCIO FUE REGISTRADO EN ASISTENCIA A ACTIVIDAD",
									type: "success",
									confirmButtonText: "ENTIENDO"
								});
							}

							if(respuesta.resultado=='SOCIO_NO_EXISTE'){
								$("input#dniSocio").focus();
								swal({
									title: "ERROR EN DNI",
									text: "AL PARECER EL DNI INGRESADO NO CORRESPONDE A NINGUN SOCIO",
									type: "error",
									confirmButtonText: "ENTIENDO"
								});
							}

							if(respuesta.resultado=='SOCIO_YA_REGISTRADO'){
								$("input#dniSocio").focus();
								swal({
									title: "SOCIO YA REGISTRADO",
									text: "AL PARECER EL SOCIO YA FUE REGISTRADO EN LA ACTIVIDAD",
									type: "warning",
									confirmButtonText: "ENTIENDO"
								});
							}
						},
						error: function (xhr, status, error) {
							console.error('Error:', error);
						}
					});
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA ENTRADA DE CARACTERES
			////////////////////////////////////////////////////////////
			$(function(){ $('#dniSocio').validar('0123456789'); });

			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const urlDTAsistencia = '../modulo/dt-asistencia-actividad.php';

			////////////////////////////////////////////////////////////
			/// DATA TABLE - JUNTA DIRECTIVA
			////////////////////////////////////////////////////////////
			const jdDataTable = $('#jdDataTable').DataTable({
			    "paging": true,
			    "pageLength": 5,
			    "lengthChange": false,
			    "info": true,
			    "bPaginate": true,
			    "bSort": false,
			    "processing": true,
			    'serverMethod': 'POST',
			    "ajax": {
			        url: urlDTAsistencia,
			        type: 'POST',
			        data: function (d) {
			            d.tipoActividad = '<?php echo $tipoActividad ?>';
			            d.codigoActividad = '<?php echo $codigoActividad ?>';
			        },
			        error: function (xhr, error, code) {
			            console.error('Error en la carga de datos:', xhr.responseText);
			        }
			    },
			    "columns": [
			        { data: 'Nro' },
			        { data: 'codigoSocio' },
			        { data: 'nombreSocio' },
			        { data: 'dni' },
			        { data: 'lotes' },
			    ],
			    "columnDefs": [
			        { "targets": 0, "className": "align-middle text-center" },
			        { "targets": 1, "className": "align-middle text-center" },
			        { "targets": 2, "className": "align-middle text-left text-strong" },
			        { "targets": 3, "className": "align-middle text-center" },
			        { "targets": 4, "className": "align-middle text-center" },
			    ]
			});
		});

		///////////////////////////////////////////////////
		/// VALIDAR FORMULARIO
		///////////////////////////////////////////////////
		var validator = $("#form_control_asistencia").validate({
			errorClass: 'validation-error-label',
			highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
			unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
			validClass: "validation-valid-label",
			rules: {
				dniSocio:{ required: true },
			},
			messages: {
				dniSocio:{ required: "Ingrese número de DNI de socio" },
			}
		});
	</script>
<?php } ?>