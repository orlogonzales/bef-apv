<?php
	session_start();
?>

<?php if ($_POST['proceso'] === 'inicioRegistro') { ?>
	<?php
		include '../php/conexion.php';
		$sql="SELECT sm_junta_directiva_vigencia.idVigencia, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva_vigencia.activo FROM sm_junta_directiva_vigencia ORDER BY sm_junta_directiva_vigencia.vigenciaJunta ASC";
		$consulta = $conexion->query($sql);
	?>
	<div class="panel">
		<div class="panel-heading bg-teal">
			<h6 class="panel-title">REGISTRO JUNTA DIRECTIVA</h6>
		</div>
		<div class="panel-body mb-0 pb-0">
			<form id="formInicioRegistroJuntaDirectiva">
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<input type="text" class="form-control input-lg" name="fechaConsulta" id="fechaConsulta" autocomplete="off" placeholder="FECHA DE INICIO DE PERIODO">
							<label class="error-validacion" for="fechaConsulta" style="display:none;"></label>
						</div>
					</div>
					<div class="col-md-1">
						<button type="button" id="btnValidafecha" class="btn btn-lg bg-gray-3 btn-block">VALIDA</button>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<div class="form-control input-lg text-uppercase text-strong" id="infoFecha">...</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<select class="form-control input-lg" name="idVigencia" id="idVigencia">
								<?php
									while ($fila = $consulta->fetch_assoc()) {
										$activo        = $fila['activo'];
										$idVigencia    = $fila['idVigencia'];
										$vigenciaJunta = $fila['vigenciaJunta'];
										if($idVigencia>1){
											if($activo==1){
												echo '<option value="'.$idVigencia.'" selected>'.$vigenciaJunta.' MESES (VIGENCIA DE JUNTA DIRECTIVA)</option>';
											}else{
												echo '<option value="'.$idVigencia.'">'.$vigenciaJunta.' MESES (VIGENCIA DE JUNTA DIRECTIVA)</option>';
											}
										}else{
											if($activo==1){
												echo '<option value="'.$idVigencia.'" selected>'.$vigenciaJunta.' MES (VIGENCIA DE JUNTA DIRECTIVA)</option>';
											}else{
												echo '<option value="'.$idVigencia.'">'.$vigenciaJunta.' MES (VIGENCIA DE JUNTA DIRECTIVA)</option>';
											}
										}
									}
								?>
							</select>
							<label class="error-validacion" for="idVigencia" style="display:none;"></label>
						</div>
					</div>
					<div class="col-md-2 d-flex align-items-end">
						<input type="hidden" name="fechaPeriodo" id="fechaPeriodo">
						<input type="hidden" name="proceso" id="proceso" value="asignarIntegrantes">
						<input type="hidden" name="operacion" id="operacion" value="INICIA_SESION_JUNTA_DIRECTIVA">
						<button type="button" id="btnAsignaSociosJuntaDirectiva" class="btn btn-lg bg-gray-3 btn-block">ASIGNA SOCIOS</button>
					</div>
				</div>
			</form>
			<div id="infoJunta" style="display: none;">
				<div class="alert alert-danger" role="alert">
					<h4 class="alert-heading">JUNTA DIRECTIVA REGISTRADA</h4>
					<p class="mb-0" id="boxInfoJuntaDirectiva"></p>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function () {
			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';
			const urlBase          = '../modulo/modulo-jd-registro.php';
			
			////////////////////////////////////////////////////////////
			/// SELECT 2
			////////////////////////////////////////////////////////////
			$('.seleccionar').select2();

			////////////////////////////////////////////////////////////
			/// DATEPICKER
			////////////////////////////////////////////////////////////
			$('#fechaConsulta').datepicker({
				showButtonPanel: true,
				format: 'dd/mm/yyyy',
				maxDate: "+7D"
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FECHA Y VERIFICA SI ESTA OCUPADO - JUNTA DIRECTIVA
			////////////////////////////////////////////////////////////
			$('#btnValidafecha').click(function (e) {
				e.preventDefault();
		        const fechaConsulta = $('#fechaConsulta').val();
		        const operacion = "VERIFICA_FECHA";
		        const valida = $('#formInicioRegistroJuntaDirectiva').valid();
		        if (valida) {
		            $.ajax({
		                url: urlMantenimiento,
		                type: 'POST',
		                data: { fechaConsulta: fechaConsulta, operacion: operacion },
		                dataType: 'json',
		                success: function(respuesta) {
		                	console.log(respuesta);
		                	const validaFecha = respuesta.validaFecha;
		                	const infoFechaPeriodo = respuesta.infoFechaPeriodo;
		                	const informacionJunta = respuesta.informacionJunta;

		                	if(validaFecha=='FECHA_INVALIDA'){
		                		$('#fechaPeriodo').val('');
		                		$('#infoFecha').html(infoFechaPeriodo);
		                		$('#infoJunta').slideUp('fast');
		                		$('#boxInfoJuntaDirectiva').html('');
		                	}

		                	if(validaFecha=='FECHA_OCUPADA'){
		                		$('#fechaPeriodo').val('');
		                		$('#infoFecha').html(infoFechaPeriodo);
		                		$('#infoJunta').slideDown('fast');
		                		$('#boxInfoJuntaDirectiva').html(respuesta.informacionJunta);
		                	}

		                	if(validaFecha=='FECHA_VALIDA'){
		                		$('#fechaPeriodo').val(fechaConsulta);
		                		$('#btnValidafecha').prop('disabled',true);
		                		$('#idVigencia').prop('disabled',false);
		                		$('#btnAsignaSociosJuntaDirectiva').prop('disabled',false);
		                		$('#infoFecha').html(infoFechaPeriodo);
		                		$('#infoJunta').slideUp('fast');
		                		$('#boxInfoJuntaDirectiva').html('');
		                	}		                	
		                }
		            });
		        }
		    });

			////////////////////////////////////////////////////////////
			/// PROCESA FORMULARIO
			////////////////////////////////////////////////////////////
			$('#idVigencia').prop('disabled',true);
			$('#btnAsignaSociosJuntaDirectiva').prop('disabled',true);
			$('#btnAsignaSociosJuntaDirectiva').click(function (e) {
				e.preventDefault();
				const datos = $('#formInicioRegistroJuntaDirectiva').serialize();
				const valida = $('#formInicioRegistroJuntaDirectiva').valid();
				if(valida){
					$.ajax({
                        url: urlMantenimiento,
                        type: 'POST',
                        data: datos,
                        dataType: 'json',
                        beforeSend: function(){
							$('#overlay').show();
						},
						complete: function(){
							$('#overlay').hide();
						},
                        success: function(respuesta) {
                            if(respuesta.resultado=='SESION_JD_CREADA'){
                            	new PNotify({title: 'Vigencia de Junta',text: 'Se ha registrado la sesión para crear la junta directiva.',type: 'success',styling: 'bootstrap3',delay: 3000});
                            	$('#overlay').show();
								const proceso = 'asignarIntegrantes';
								const url = urlBase;
								$.ajax({
									url: url,
									type: 'POST',
									data: { proceso: proceso },
									success: function (response) {
										$('#overlay').hide();
										$('#boxRegistroJuntaDirectiva').html(response);					
									}
								});
                            }
                        }
                    });
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formInicioRegistroJuntaDirectiva').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					fechaConsulta: { required: true },
					idVigencia: { required: true },
				},
				messages: {
					fechaConsulta: "Por favor, seleccione fecha inicio de periodo.",
					idVigencia: "Por favor, seleccione vigencia de periodo.",
				}
			});
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'asignarIntegrantes') { ?>
	<?php
		include '../php/conexion.php';
		include '../php/funciones.php';
		
		if($_SESSION['crearJD']){
			$fechaPeriodo = $_SESSION['crearJD']['baseJD']['fechaPeriodo'];
			$idVigencia   = $_SESSION['crearJD']['baseJD']['idVigencia'];
			$codigoBanco  = $_SESSION['crearJD']['cuentaBancoJunta']['codigoBanco'];
			$codigoCuenta = $_SESSION['crearJD']['cuentaBancoJunta']['codigoCuenta'];
			$cargosSocios = $_SESSION['crearJD']['cargosSocios'];
			$cargosOcupados=count($cargosSocios);
		}
		
		$query="SELECT vigenciaJunta FROM sm_junta_directiva_vigencia WHERE idVigencia='$idVigencia'";
		$consultaSocio = $conexion->query($query);
		$resultado = $consultaSocio->fetch_assoc();
		$vigenciaJunta=$resultado[vigenciaJunta];

		if($vigenciaJunta>1){
			$infoDuracionPeriodo=($vigenciaJunta/12).' AÑOS';
		}else{
			$infoDuracionPeriodo=($vigenciaJunta/12).' AÑO';
		}

		$fechaInicioPeriodo=infoFecha($fechaPeriodo,'year');
		$objFechaFin = new DateTime($fechaPeriodo);
		$intervalo = new DateInterval('P'.$vigenciaJunta.'M');
		$objFechaFin->add($intervalo);
		$finPeriodo=$objFechaFin->format('Y-m-d');
		$fechaFinPeriodo=infoFecha($finPeriodo,'year');

		if(strlen($codigoBanco)>0 && strlen($codigoCuenta)>0){
			$query="SELECT sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_banco_cuentas INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE sm_bancos.codigoBanco = '$codigoBanco' AND sm_banco_cuentas.codigoCuenta = '$codigoCuenta'";
			$consultaSocio = $conexion->query($query);
			$resultado = $consultaSocio->fetch_assoc();
			$entidad=$resultado[entidad];
			$numeroCuenta=$resultado[numeroCuenta];
			$detalleCuenta=$resultado[detalle];
		}

		$query="SELECT COUNT(idCargoJunta) AS cargosJD FROM sm_junta_directiva_cargos WHERE cargoJunta!=''";
		$consultaSocio = $conexion->query($query);
		$resultado = $consultaSocio->fetch_assoc();
		$cargosJD=$resultado[cargosJD];

		$sql="SELECT sm_bancos.codigoBanco, sm_bancos.entidad FROM sm_bancos ORDER BY sm_bancos.entidad ASC";
		$consultaBancos = $conexion->query($sql);
		$conexion->close();
	?>
	<div class="row">
		<div class="col-md-5">
			<ul class="list-group mt-0 mb-10 bg-gray-3">
				<li class="list-group-item"><strong>PERIODO DE JUNTA DIRECTIVA:</strong> <span class="pull-right text-strong text-uppercase"><?= $fechaInicioPeriodo ?> <span class="text-yellow">AL</span> <?= $fechaFinPeriodo ?></span></li>
				<li class="list-group-item"><strong>DURACION DE PERIODO:</strong> <span class="pull-right text-strong text-uppercase"><?= $infoDuracionPeriodo ?></span></li>
				<?php if(strlen($codigoBanco)>0 && strlen($codigoCuenta)>0){ ?>
					<li class="list-group-item"><strong>CUENTA EN BANCO:</strong> <span class="pull-right text-strong text-uppercase"><?= $entidad ?></span></li>
					<li class="list-group-item"><strong>NUMERO DE CUENTA:</strong> <span class="pull-right text-strong text-uppercase"><?= $numeroCuenta ?></span></li>
					<li class="list-group-item"><strong>DESCRIPCION DE CUENTA:</strong> <span class="pull-right text-strong text-uppercase"><?= $detalleCuenta ?></span></li>
				<?php } ?>
			</ul>

			<?php if((strlen($codigoBanco)>0 && strlen($codigoCuenta)>0) && ($cargosJD==$cargosOcupados)){ ?>
				<div class="row">
					<div class="col-md-12 text-center">
						<button type="button" id="btnRegistraJuntaDirectiva" class="btn bg-success btn-lg text-strong">REGISTRAR JUNTA DIRECTIVA</button>
					</div>
				</div>
			<?php } ?>

			<?php if(strlen($codigoBanco)==0 && strlen($codigoCuenta)==0){ ?>
				<div class="panel">
					<div class="panel-heading bg-teal">
						<h6 class="panel-title">ASIGNAR CUENTA BANCARIA</h6>
					</div>
					<div class="panel-body">
						<div id="overlay" style="display: none;"></div>
						<form id="formRegistraCuentaBancoJD" class="form_valida_cuenta">
							<div class="row">
								<div class="col-md-6 col-xs-6">
									<div class="form-group">
										<select name="codigoBanco" id="codigoBanco" class="seleccion">
											<option value="" selected>SELECCIONE ENTIDAD BANCARIA</option>
											<?php
												while ($banco = $consultaBancos->fetch_assoc()) {
													$codigoBanco =$banco['codigoBanco'];
													$entidad     =$banco['entidad'];
													echo '<option value="'.$codigoBanco.'">'.$entidad.'</option>';
												}
											?>
										</select>
										<label class="error-validacion" for="codigoBanco" style="display:none;"></label>
									</div>
								</div>
								<div class="col-md-6 col-xs-6">
									<div class="form-group">
										<select name="detalleCuenta" id="detalleCuenta" class="seleccion">
											<option value="" selected>SELECCIONE CUENTA</option>
										</select>
										<label class="error-validacion" for="detalleCuenta" style="display:none;"></label>
									</div>
								</div>
								<div class="col-md-12 col-xs-12">
									<div class="form-group">							
										<input type="hidden" id="codigoCuenta" name="codigoCuenta">
										<input type="hidden" name="operacion" value="INICIA_SESION_CUENTA_BANCO_JUNTA_DIRECTIVA">
										<div  class="form-control input-lg textoMayuscula" id="infoCodigoCuenta">ESPERANDO NUMERO DE CUENTA</div>
									</div>
								</div>
								<div class="col-md-3 col-xs-12">
									<button type="button" id="btAgregarCuentaBanco" class="btn btn-lg bg-slate-600 btn-block"><strong>AGREGA CUENTA</strong></button>
								</div>
								<div class="col-md-6 col-xs-12">
									<button type="button" id="btAsignarCuentaBancoJuntaDirectiva" class="btn btn-lg bg-slate-800 btn-block"><strong>ASIGNAR CUENTA J.D.</strong></button>
								</div>
								<div class="col-md-3 col-xs-12">
									<button type="button" id="btCancelaCreacionJuntaDirectiva" class="btn btn-lg btn-danger btn-block"><strong>ANULAR REGISTRO</strong></button>
								</div>
							</div>
						</form>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="col-md-7">
			<div class="panel">
				<div class="panel-heading bg-teal">
					<h6 class="panel-title">ASIGNAR CARGOS A JUNTA DIRECTIVA</h6>
				</div>
				<div class="panel-body m-0 pt-10 pl-10 pr-10 pb-10">
					<div id="overlay" style="display: none;"></div>
					<table id="crearJuntaDirectivaDataTable" class="table table-bordered table-hover table-striped table-td-valign-middle">
						<thead class="bg-dark">
							<tr>
								<th class="text-center">#</th>
								<th class="text-left">CARGO</th>
								<th class="text-center"><i class="fa fa-align-justify"></i></th>
								<th class="text-left">NOMBRE DE SOCIO</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal -->
	<div id="modalAsignarCargo" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Contenido del Modal -->
			<div class="modal-content">
				<div id="boxAsignarCargo">
					<div class="content-loader">
						<img src="../assets/images/preloader-md.svg" alt="Cargando...">
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Agregar Cuenta Bancaria -->
	<div id="modalAgregarCuentaBanco" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Contenido del Modal -->
			<div class="modal-content">
				<div id="boxAgregarCuentaBanco">
					<div class="content-loader">
						<img src="../assets/images/preloader-md.svg" alt="Cargando...">
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const urlBase          = '../modulo/modulo-jd-registro.php';
			const urlDTCrearJunta  = '../modulo/dt-crear-junta-directiva.php';
			const urlProceso       = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';
			const preloader        = '<div class="content-loader"><img src="../assets/images/preloader-sm.svg" alt="Cargando..."></div>';

			////////////////////////////////////////////////////////////
			/// DATA TABLE - JUNTA DIRECTIVA
			////////////////////////////////////////////////////////////
			const crearJuntaDirectivaDataTable = $('#crearJuntaDirectivaDataTable').DataTable({
				"paging"       : false,
				"pageLength"   : 20,
				"searching"    : false,
				"lengthChange" : false,
				"info"         : false,
				"bSort"        : false,
				"processing"   : true,
				'serverMethod' : 'POST',
				"ajax"         : urlDTCrearJunta,
				"columns"      : [
					{ data: 'Nro' },
					{ data: 'cargo' },
					{ data: 'menuOpciones' },
					{ data: 'nombreSocio' },
				],
				"columnDefs": [
					{ "targets": 0,  "className": "align-middle text-center" },
					{ "targets": 1,  "className": "align-middle text-left text-uppercase text-strong" },
					{ "targets": 2,  "className": "align-middle text-center" },
					{ "targets": 3,  "className": "align-middle text-left text-strong" },
				],
			});

			$('#crearJuntaDirectivaDataTable').on('click','.asignaCargoJunta',function(){
				const idCargoJunta = $(this).data('id');
				const modal        = '#modalAsignarCargo';
				const proceso      = 'asignaCargo';
				const datos        = 'idCargoJunta='+idCargoJunta+'&modal='+modal+'&proceso='+proceso;
				$('#modalAsignarCargo').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalAsignarCargo").modal("show");
				$('#boxAsignarCargo').html(preloader);
				$.ajax({
					url: urlBase,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxAsignarCargo').html(response);
					},
					error: function () {
						$('#boxAsignarCargo').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});
			});			

			$('#crearJuntaDirectivaDataTable').on('click','.quitaCargoJunta',function(){
				const idCargoJunta = $(this).data('id');
				const operacion = 'ELIMINA_SESION_SOCIO_CARGO_JUNTA_DIRECTIVA';
				const datos ='idCargoJunta='+idCargoJunta+'&operacion='+operacion;
				$.ajax({
					url  : urlMantenimiento,
					type : 'POST',
					data : datos,
					dataType: 'json',
					success: function(respuesta){ 
						console.log(respuesta);
						if(respuesta.resultado=='ELIMINA_VARIABLE_SESION_CARGO'){
							new PNotify({title: 'Socio Eliminado',text: 'Se elimino socio de junta directiva.',type: 'success',styling: 'bootstrap3',delay: 3000});
							cargaRegistroMiembros();
						}
					}
				});
			});

			////////////////////////////////////////////////////////////
			/// AGREGAR CUENTA DE BANCO
			////////////////////////////////////////////////////////////
			$('#btAgregarCuentaBanco').click(function (e) {
				e.preventDefault();
				const modal        = '#modalAgregarCuentaBanco';
				const proceso      = 'agregarCuentaBanco';
				const datos        = 'modal='+modal+'&proceso='+proceso;
				$('#modalAgregarCuentaBanco').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalAgregarCuentaBanco").modal("show");
				$('#boxAsignarCargo').html(preloader);
				$.ajax({
					url: urlBase,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxAgregarCuentaBanco').html(response);
					},
					error: function () {
						$('#boxAgregarCuentaBanco').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});

			});

			////////////////////////////////////////////////////////////
			/// ASIGNA CUENTA A JUNTA DIRECTIVA
			////////////////////////////////////////////////////////////
			const codigoBanco='<?= $codigoBanco ?>';
			const codigoCuenta='<?= $codigoCuenta ?>';
			if(codigoBanco.length>0 && codigoCuenta.length>0){
				$('#formRegistraCuentaBancoJD *').prop("disabled",true);
			}

			$('.seleccion').select2();
			$('#detalleCuenta').prop('disabled',true);
		    $('#codigoBanco').on('change', function() {
		        const codigoBanco = $(this).val();
		        const codigoCuenta = $('#detalleCuenta').val();

		        if(codigoBanco.length>0 && codigoCuenta.length>0){
		        	$('#formRegistraCuentaBancoJD').trigger("reset");
		        	$('#detalleCuenta option[value=""]').prop('selected', 'selected').change();
		        	$('#infoCodigoCuenta').text('');
		        	$('#detalleCuenta').prop('disabled',true);
		        }
		        $('#detalleCuenta').empty().append('<option value="" selected>SELECCIONE CUENTA</option>');
		        if (codigoBanco) {
		        	$('#detalleCuenta').prop('disabled',false);
		            $.ajax({
		                url: '../modulo/combo-obtener-cuentas.php',
		                type: 'POST',
		                data: { codigoBanco: codigoBanco },
		                dataType: 'json',
		                success: function(response) {
		                    if (response.length > 0) {
		                        $.each(response, function(index, cuenta) {
		                            $('#detalleCuenta').append('<option value="' + cuenta.codigoCuenta + '">' + cuenta.detalle + '</option>');
		                        });
		                    }
		                }
		            });
		        }else{
		        	$('#formRegistraCuentaBancoJD').trigger("reset");
		        	$('#detalleCuenta option[value=""]').prop('selected', 'selected').change();
		        	$('#infoCodigoCuenta').text('');
		        	$('#detalleCuenta').prop('disabled',true);
		        }
		    });

		    $('#detalleCuenta').on('change', function() {
		        const codigoCuenta = $(this).val();
		        if (codigoCuenta) {
		            $('#codigoCuenta').val(codigoCuenta);
		            $('#infoCodigoCuenta').text(codigoCuenta);
		        } else {
		            $('#codigoCuenta').val('');
		            $('#infoCodigoCuenta').text('ESPERANDO NUMERO DE CUENTA');
		        }
		    });

		    $('#btAsignarCuentaBancoJuntaDirectiva').click(function (e) {
		    	e.preventDefault();
				const datos            = $('#formRegistraCuentaBancoJD').serialize();
				const valida           = $('#formRegistraCuentaBancoJD').valid();
				if(valida){
					$.ajax({
						url  : urlMantenimiento,
						type : 'POST',
						data : datos,
						dataType: 'json',
						beforeSend: function(){
							console.log(urlMantenimiento);
							$('#overlay').show();
						},
						complete: function(){
							$('#overlay').hide();
						},
						success: function(respuesta){ 
							if(respuesta.resultado=='CUENTA_BANCO_ASIGNADO'){
								new PNotify({title: 'Cuenta de Banco',text: 'Se asigno Centa de Banco a periodo de junta directiva.',type: 'success',styling: 'bootstrap3',delay: 3000});
								cargaRegistroMiembros();
							}
						}
					});
				}
		    });

		    ////////////////////////////////////////////////////////////
			/// REGISTRA JUNTA DIRECTIVA
			////////////////////////////////////////////////////////////
			$('#btCancelaCreacionJuntaDirectiva').click(function(e) {
		    	e.preventDefault();
		    	$(this).prop('disabled',true);
		    	const urlJD     = 'junta-directiva-lista-periodos.php';
		    	const operacion = 'ELIMINA_SESION_REGISTRO_JUNTA_DIRECTIVA';
		    	const datos     = 'operacion='+operacion;
		    	$.ajax({
					url  : urlMantenimiento,
					type : 'POST',
					data : datos,
					dataType: 'json',
					beforeSend: function(){
						$('#overlay').show();
					},
					complete: function(){
						$('#overlay').hide();
					},
					success: function(respuesta){
						if(respuesta.resultado=='SESION_ELIMINADA_OK'){
							location.reload();
						}else{
							$(this).prop('disabled',false);
							cargaRegistroMiembros();
						}
					}
				});
		    });

		    $('#btnRegistraJuntaDirectiva').click(function(e) {
		    	e.preventDefault();
		    	$(this).prop('disabled',true);
		    	const urlJD     = 'junta-directiva-lista-periodos.php';
		    	const operacion = 'REGISTRA_JUNTA_DIRECTIVA';
		    	const datos     = 'operacion='+operacion;
		    	$.ajax({
					url  : urlMantenimiento,
					type : 'POST',
					data : datos,
					dataType: 'json',
					beforeSend: function(){
						$('#overlay').show();
					},
					complete: function(){
						$('#overlay').hide();
					},
					success: function(respuesta){
						if(respuesta.resultado=='JUNTA_DIRECTIVA_REGISTRO_OK'){
							window.location = urlJD;
						}
					}
				});
		    });

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formRegistraCuentaBancoJD').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					codigoBanco: { required: true },
					detalleCuenta: { required: true },
				},
				messages: {
					codigoBanco: "Por favor, seleccione entidad bancaria.",
					detalleCuenta: "Por favor, seleccione detalle de cuenta.",
				}
			});

			////////////////////////////////////////////////////////////
			/// RELOAD MODULO
			////////////////////////////////////////////////////////////
			function cargaRegistroMiembros(){
				const proceso = 'asignarIntegrantes';
				const url = urlBase;
				$.ajax({
					url: url,
					type: 'POST',
					data: { proceso: proceso },
					success: function (response) {
						$('#overlay').hide();
						$('#boxRegistroJuntaDirectiva').html(response);
					}
				});
			}
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'agregarCuentaBanco') { ?>
	<?php
		$modal=$_POST['modal'];
		include '../php/conexion.php';

		$sql="SELECT sm_bancos.codigoBanco, sm_bancos.entidad FROM sm_bancos";
		$bancos = $conexion->query($sql);
		$conexion->close();
	?>
	<div class="modal-header bg-brown">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h5 class="modal-title">AGREGAR CUENTA DE BANCO</h5>
	</div>
	<form id="formRegistraCuentaBancaria">
		<div class="modal-body m-0 p-0">
			<div class="overlay_RegistraCuentaBancaria" id="overlay" style="display: none;"></div>
			<div class="mt-10 ml-10 mr-10 mb-10">
				<div class="form-group mb-10 p-0">
					<select class="seleccionar" name="codigoBanco" id="codigoBanco">
						<option value="" selected="">SELECCIONE BANCO</option>
						<?php
							while ($banco = $bancos->fetch_assoc()) {
								if (!in_array($banco['codigoBanco'], $cargosSocios)){
									echo '<option value="'.$banco['codigoBanco'].'">'.$banco['entidad'].'</option>';
								}
							}
						?>
					</select>
					<label class="error-validacion" for="codigoBanco" style="display:none;"></label>
				</div>
				<div class="form-group mb-10 p-0">
					<input type="text" class="form-control text-uppercase input-lg" name="numeroCuenta" id="numeroCuenta" placeholder="NUMERO DE CUENTA" onblur="mayusculas(event, this)">
					<label class="error-validacion" for="numeroCuenta" style="display:none;"></label>
				</div>
				<div class="form-group m-0 p-0">
					<input type="text" class="form-control text-uppercase input-lg" name="detalle" id="detalle" placeholder="DESCRIPCION DE LA CUENTA" onblur="mayusculas(event, this)">
					<label class="error-validacion" for="detalle" style="display:none;"></label>
				</div>
			</div>
		</div>
		<div class="modal-footer m-0 pt-10 pb-10 text-center border-top-gray-1">
			<input type="hidden" name="operacion" value="REGISTRA_CUENTA">
			<button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
			<button type="button" class="btn btn-success" id="btnRegistraCuentaBancaria">REGISTRAR CUENTA</button>
		</div>
	</form>

	<script type="text/javascript">
		$(document).ready(function () {
			const urlBase          = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-bancos.php';

			$('.seleccionar').select2();

			$('#btnRegistraCuentaBancaria').click(function (e) {
				e.preventDefault();
				const overlay = '.overlay_RegistraCuentaBancaria';
				const datos = $('#formRegistraCuentaBancaria').serialize();
				const valida = $('#formRegistraCuentaBancaria').valid();
				const modal = '<?= $modal ?>';

				if(valida){
					$.ajax({
                        url: urlMantenimiento,
                        type: 'POST',
                        data: datos,
                        dataType: 'json',
                        beforeSend: function(){
							$(overlay).show();
						},
						complete: function(){
							$(overlay).hide();
						},
                        success: function(respuesta) {
                        	console.log(respuesta);
                            if(respuesta.mensaje=='CUENTA_REGISTRADA'){
                    			$(modal).modal('hide');
                    			$('.modal-backdrop').remove();
                            	new PNotify({title: 'Cuenta Bancaria',text: 'Se ha registrado la cuenta de banco.',type: 'success',styling: 'bootstrap3',delay: 3000});
                    			const proceso = 'asignarIntegrantes';
								const url = urlBase;
								$.ajax({
									url: url,
									type: 'POST',
									data: { proceso: proceso },
									success: function (response) {
										$('#overlay').hide();
										$('#boxRegistroJuntaDirectiva').html(response);					
									}
								});
                            }

                            if(respuesta.mensaje=='ERROR_REGISTRO'){
                            	new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
                            }
                        },
                        error: function() {
                            $('#resultado').html('<p style="color: red;">Hubo un error al consultar el socio.</p>');
                        }
                    });
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA ENTRADA DE CARACTERES
			////////////////////////////////////////////////////////////
			$(function(){ $('#numeroCuenta').validar('0123456789-'); });

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formRegistraCuentaBancaria').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					codigoBanco  : { required: true },
					numeroCuenta : { required: true },
					detalle      : { required: true },
				},
				messages: {
					codigoBanco  : "Por favor, elija Banco.",
					numeroCuenta : "Por favor, ingrese Numero de Cuenta.",
					detalle      : "Por favor, ingrese descripcion de cuenta.",
				}
			});
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'asignaCargo' && isset($_POST['idCargoJunta'])) { ?>
	<?php
		if($_SESSION['crearJD']){
			$cargosSocios = $_SESSION['crearJD']['cargosSocios'];
		}
		ksort($cargosSocios);
		$idCargoJunta=$_POST['idCargoJunta'];
		$modal=$_POST['modal'];
		include '../php/conexion.php';
		$sql="SELECT sm_junta_directiva_cargos.cargoJunta FROM sm_junta_directiva_cargos WHERE sm_junta_directiva_cargos.idCargoJunta = '$idCargoJunta'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$cargoJunta=$resultado[cargoJunta];

		$sql="SELECT sm_socios.codigoSocio, CONCAT(sm_socios.nombre,' ', sm_socios.apPaterno,' ', sm_socios.apMaterno) AS nombreSocio FROM sm_socios ORDER BY sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
		$socios = $conexion->query($sql);
		$conexion->close();
	?>
	<div class="modal-header bg-brown">
		<button type="button" class="close" data-dismiss="modal">&times;</button>
		<h5 class="modal-title">ASIGNAR CARGO A SOCIO</h5>
	</div>
	<form id="formAsignaSocioCargo">
		<div class="modal-body m-0 p-0">
			<div id="overlay" style="display: none;"></div>
			<ul class="list-group bg-gray-1 mt-10 ml-10 mr-10 mb-10">
			  <li class="list-group-item">CARGO <span class="pull-right text-uppercase"><?= $cargoJunta ?></span></li>
			</ul>
			<div class="mt-10 ml-10 mr-10 mb-10">
					<div class="form-group m-0 p-0">
						<input type="hidden" name="idCargoJunta" id="idCargoJunta" value="<?= $idCargoJunta ?>">
						<select class="seleccionar" name="codigoSocio" id="codigoSocio">
							<option value="" selected="">SELECCIONE SOCIO</option>
							<?php
								while ($fila = $socios->fetch_assoc()) {
									if (!in_array($fila['codigoSocio'], $cargosSocios)){
										echo '<option value="'.$fila['codigoSocio'].'">'.$fila['nombreSocio'].'</option>';
									}
								}
							?>
						</select>
						<label class="error-validacion" for="codigoSocio" style="display:none;"></label>
					</div>
			</div>
		</div>
		<div class="modal-footer m-0 pt-10 pb-10 text-center border-top-gray-1">
			<input type="hidden" name="operacion" value="ASIGNA_SESION_SOCIO_CARGO_JUNTA_DIRECTIVA">
			<button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
			<button type="button" class="btn btn-success" id="btnAsignaSocioCargo">ASIGNAR</button>
		</div>
	</form>

	<script type="text/javascript">
		$(document).ready(function () {
			const urlBase          = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';

			$('.seleccionar').select2();

			$('#btnAsignaSocioCargo').click(function (e) {
				e.preventDefault();
				const datos = $('#formAsignaSocioCargo').serialize();
				const valida = $('#formAsignaSocioCargo').valid();
				const modal = '<?= $modal ?>';
				const crearJuntaDirectivaDataTable = $('#crearJuntaDirectivaDataTable').DataTable();

				if(valida){
					$.ajax({
                        url: urlMantenimiento,
                        type: 'POST',
                        data: datos,
                        dataType: 'json',
                        beforeSend: function(){
							$('#overlay').show();
						},
						complete: function(){
							$('#overlay').hide();
						},
                        success: function(respuesta) {
                            if(respuesta.resultado=='SOCIO_ASIGNADO_OK'){
                    			$(modal).modal('hide');
                    			$('.modal-backdrop').remove();
                            	new PNotify({title: 'Socio en Cola',text: 'Se ha registrado en  nuevo integrante de junta directiva.',type: 'success',styling: 'bootstrap3',delay: 3000});
                    			const proceso = 'asignarIntegrantes';
								const url = urlBase;
								$.ajax({
									url: url,
									type: 'POST',
									data: { proceso: proceso },
									success: function (response) {
										$('#overlay').hide();
										$('#boxRegistroJuntaDirectiva').html(response);					
									}
								});
                            }
                        },
                        error: function() {
                            $('#resultado').html('<p style="color: red;">Hubo un error al consultar el socio.</p>');
                        }
                    });
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formAsignaSocioCargo').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					codigoSocio: { required: true },
				},
				messages: {
					codigoSocio: "Por favor, seleccione socio para cargo.",
				}
			});
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'buscarSocio' && isset($_POST['codigoSocio'])) { ?>
	<?php
		include '../php/conexion.php';
		$codigoSocio=$_POST['codigoSocio'];
		$idCargoJunta=$_POST['idCargoJunta'];		
		$sql="SELECT CONCAT(sm_socios.nombre,' ', sm_socios.apPaterno,' ', sm_socios.apMaterno ) AS nombreSocio FROM sm_socios WHERE sm_socios.codigoSocio = '$codigoSocio'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombreSocio=$resultado[nombreSocio];

		if ($consulta->num_rows > 0) {
			$_SESSION['crearJD'][$idCargoJunta] = $codigoSocio;
			$resultados='SI';
			$nombre=$nombreSocio;
		}else{
			$resultados='NO';
			$nombre='S/D';
		}
		$respuesta->resultados=$resultados;
		$respuesta->nombreSocio=$nombreSocio;
		echo json_encode($respuesta);
		$conexion->close();
	?>
<?php } ?>

<?php if ($_POST['proceso'] === 'quitaCargo' && isset($_POST['codigoSocio'])) { ?>
	<?php
		$idCargoJunta=$_POST['idCargoJunta'];
		$codigoSocio=$_POST['codigoSocio'];

		if (isset($_SESSION['crearJD'][$idCargoJunta])) {
			unset($_SESSION['crearJD'][$idCargoJunta]);
			$eliminado='SI';
		}else{
			$eliminado='NO';
		}

		$respuesta->eliminado=$eliminado;
		$respuesta->idCargoJunta=$idCargoJunta;
		$respuesta->codigoSocio=$codigoSocio;
		echo json_encode($respuesta);
	?>
<?php } ?>

<?php if ($_POST['proceso'] === 'verCuentasJuntaDirectiva' && isset($_POST['idJuntaDirectiva'])) { ?>
	<?php
		include '../php/funciones.php';
		include '../php/conexion.php';
		$idJuntaDirectiva=$_POST['idJuntaDirectiva'];
		$sql="SELECT sm_junta_directiva.fechaPeriodo, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva.extensionJuntaDirectiva, sm_junta_directiva.fechaFinPeriodo, sm_junta_directiva.fechaRegistro, sm_junta_directiva.horaRegistro, sm_junta_directiva.usuario FROM sm_junta_directiva INNER JOIN sm_junta_directiva_vigencia ON sm_junta_directiva.idVigencia = sm_junta_directiva_vigencia.idVigencia WHERE sm_junta_directiva.idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta                = $conexion->query($sql);
		$resultado               = $consulta->fetch_assoc();
		$fechaPeriodo            = $resultado[fechaPeriodo];
		$vigenciaJunta           = $resultado[vigenciaJunta];
		$extensionJuntaDirectiva = $resultado[extensionJuntaDirectiva];		
		$fechaFinPeriodo         = $resultado[fechaFinPeriodo];
		$fechaRegistro           = $resultado[fechaRegistro];
		$horaRegistro            = $resultado[horaRegistro];
		$usuario                 = $resultado[usuario];

		$infoFechaInicio= infoFecha($fechaPeriodo, 'normal');
		$infoFechaFin= infoFecha($fechaFinPeriodo, 'normal');
		$infoFecharegistro = infoFecha($fechaRegistro, 'larga');

		if($extensionJuntaDirectiva==1){
			$sql="SELECT sm_junta_directiva_extension.fechaFinPeriodo, sm_junta_directiva_extension.fechaextensionPeriodo FROM sm_junta_directiva_extension WHERE sm_junta_directiva_extension.idJuntaDirectiva = '$idJuntaDirectiva' AND ID = (SELECT MAX(sm_junta_directiva_extension.id) FROM sm_junta_directiva_extension WHERE sm_junta_directiva_extension.idJuntaDirectiva = '$idJuntaDirectiva')";
			$consulta = $conexion->query($sql);
			$resultado = $consulta->fetch_assoc();
			$fechaFinPeriodoPre=$resultado[fechaFinPeriodo];
			$fechaextensionPeriodo=$resultado[fechaextensionPeriodo];
			$infoExtensionJD='<span class="text-yellow">DESDE</span> '.strtoupper(infoFecha($fechaFinPeriodoPre,'normal')).' <span class="text-yellow">HASTA EL</span> '.strtoupper(infoFecha($fechaextensionPeriodo,'normal'));
		}else{
			$infoExtensionJD="";
		}

		$sql="SELECT CONCAT(sm_usuarios.nombre,' ',sm_usuarios.paterno) AS nombreUsuario FROM sm_usuarios WHERE sm_usuarios.dni = '$usuario'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombreUsuario=$resultado[nombreUsuario];

		$sql="SELECT sm_junta_directiva_integrantes.codigoSocio, sm_junta_directiva_cargos.cargoJunta, CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio INNER JOIN sm_junta_directiva_cargos ON sm_junta_directiva_integrantes.idCargoJunta = sm_junta_directiva_cargos.idCargoJunta WHERE idJuntaDirectiva = '$idJuntaDirectiva' ORDER BY sm_junta_directiva_integrantes.idCargoJunta ASC";
		$juntaDirectiva = $conexion->query($sql);
		$i=1;

		$sql="SELECT sm_bancos.codigoBanco, sm_bancos.entidad FROM sm_bancos ORDER BY sm_bancos.entidad ASC";
		$consultaBancos = $conexion->query($sql);
		$conexion->close();
	?>
	<ul class="list-group bg-gray-2 mb-10">
		<li class="list-group-item"><strong>PERIODO:</strong> <span class="pull-right text-uppercase"><span class="text-yellow">DESDE</span> <?= $infoFechaInicio ?> <span class="text-yellow">AL</span> <?= $infoFechaFin ?></span></li>
		<?php if($extensionJuntaDirectiva==1){ ?>
			<li class="list-group-item"><strong>AMPLIADO:</strong> <span class="pull-right text-uppercase"><?= $infoExtensionJD ?></span></li>
		<?php } ?>
		<li class="list-group-item"><strong>REGISTRO:</strong> <span class="pull-right text-uppercase"><span class="text-yellow">POR</span> <?= $nombreUsuario ?>, <span class="text-yellow">EL DIA</span> <?= $infoFecharegistro ?></span></li>
	</ul>

	<div class="panel">
		<div class="panel-heading bg-teal">
			<h6 class="panel-title">ASIGNAR CUENTA BANCARIA</h6>
		</div>
		<div class="panel-body">
			<div id="overlay" style="display: none;"></div>
			<form id="formRegistraCuentaBancoJD" class="form_valida_cuenta">
				<div class="row">
					<div class="col-md-6 col-xs-6">
						<div class="form-group">
							<select name="codigoBanco" id="codigoBanco" class="seleccion">
								<option value="" selected>SELECCIONE ENTIDAD BANCARIA</option>
								<?php
									while ($banco = $consultaBancos->fetch_assoc()) {
										$codigoBanco =$banco['codigoBanco'];
										$entidad     =$banco['entidad'];
										echo '<option value="'.$codigoBanco.'">'.$entidad.'</option>';
									}
								?>
							</select>
							<label class="error-validacion" for="codigoBanco" style="display:none;"></label>
						</div>
					</div>
					<div class="col-md-6 col-xs-6">
						<div class="form-group">
							<select name="detalleCuenta" id="detalleCuenta" class="seleccion">
								<option value="" selected>SELECCIONE CUENTA</option>
							</select>
							<label class="error-validacion" for="detalleCuenta" style="display:none;"></label>
						</div>
					</div>
					<div class="col-md-8 col-xs-12">
						<div class="form-group">
							<input type="hidden" id="codigoCuenta" name="codigoCuenta">
							<input type="hidden" name="idJuntaDirectiva" value="<?= $idJuntaDirectiva ?>">							
							<input type="hidden" name="operacion" value="REGISTRA_CUENTA_BANCO_JUNTA_DIRECTIVA">
							<div  class="form-control input-lg textoMayuscula" id="infoCodigoCuenta">ESPERANDO NUMERO DE CUENTA</div>
						</div>
					</div>
					<div class="col-md-4 col-xs-12">
						<button type="button" id="btAsignarCuentaBancoJuntaDirectiva" class="btn btn-lg bg-slate-600 btn-block"><strong>ASIGNAR CUENTA</strong></button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<table id="cuentasBancoJDDataTable" class="table table-sm table-hover table-bordered table-striped table-td-valign-middle mb-20">
		<thead class="bg-gray-2">
			<tr>
				<th class="text-center">#</th>
				<th class="text-left">ENTIDAD BANCARIA</th>
				<th class="text-left">NRO. CUENTA</th>
				<th class="text-left">DETALLE</th>
				<th class="text-center"></th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>

	<script type="text/javascript">
		$(document).ready(function(){
			//btnEliminaCuentaJD
			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const idJuntaDirectiva = '<?= $idJuntaDirectiva ?>';
			const urlDTCargos = '../modulo/dt-cuentas-banco-jd.php';
			const urlProceso    = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';
			$('#cargoJunta').focus();

			////////////////////////////////////////////////////////////
			/// DATA TABLE - GESTION DE CARGOS
			////////////////////////////////////////////////////////////
			const cuentasBancoJD = $('#cuentasBancoJDDataTable').DataTable({
				"info"         : false,
				"bSort"        : false,
				"processing"   : true,
				'serverMethod' : 'POST',
				"ajax": {
					url: urlDTCargos,
					type: 'POST',
					data: function(d) {
						d.idJuntaDirectiva = idJuntaDirectiva;
					}
				},
				"columns"      : [
					{ data: 'Nro' },
					{ data: 'banco' },
					{ data: 'nroCuenta' },
					{ data: 'detalleCuenta' },
					{ data: 'opciones' },
				],
				"columnDefs": [
					{ "targets": 0,  "className": "align-middle text-left" },
					{ "targets": 1,  "className": "align-middle text-left text-strong" },
					{ "targets": 2,  "className": "align-middle text-left" },
					{ "targets": 3,  "className": "align-middle text-center" },
					{ "targets": 4,  "className": "align-middle text-center" },
				],
			});

			$('#cuentasBancoJDDataTable_filter').hide();
    		$('#cuentasBancoJDDataTable_filter_length').hide();
    		$('.dataTables_paginate.paging_simple_numbers').hide();
    		$('.dataTables_length').hide();

			////////////////////////////////////////////////////////////
			/// ELIMINA - CARGOS
			////////////////////////////////////////////////////////////
			$('#cuentasBancoJDDataTable').on('click','.btnEliminaCuentaJD',function(){
				const codigoCuenta = $(this).data('cuenta');
				const operacion      = 'ELIMINA_CUENTA_JUNTA_DIRECTIVA';
				const datos        = 'idJuntaDirectiva='+idJuntaDirectiva+'&codigoCuenta='+codigoCuenta+'&operacion='+operacion;
				swal({
					title: "¿Eliminar Cuenta?",
					text: "Eliminar cuenta "+codigoCuenta+" de la Junta Directiva",
					type: "warning",
					showCancelButton: true,
					cancelButtonText: "No, Cancelar",
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Si, Eliminar",
					closeOnConfirm: false
				},
				function(){
					$.ajax({
			            url: urlMantenimiento,
			            type: 'POST',
			            data: datos,
			            dataType: 'json',
			            success: function (respuesta) {
			            	console.log(respuesta);
			            	if(respuesta.resultado=='CUENTA_BANCO_ELIMINADA_JUNTA_DIRECTIVA'){
								swal("¡Eliminado!", "Cargo seleccionado fue eliminado.", "success");
								cuentasBancoJD.ajax.reload();
			            	}

			            	if(respuesta.resultado=='CUENTA_BANCO_ELIMINADA_JUNTA_DIRECTIVA_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
			            	}
			            }
			        });
				});
			});

			
			////////////////////////////////////////////////////////////
			/// ASIGNA CUENTA A JUNTA DIRECTIVA
			////////////////////////////////////////////////////////////
			const codigoBanco='<?= $codigoBanco ?>';
			const codigoCuenta='<?= $codigoCuenta ?>';
			if(codigoBanco.length>0 && codigoCuenta.length>0){
				$('#formRegistraCuentaBancoJD *').prop("disabled",true);
			}

			$('.seleccion').select2();
			$('#detalleCuenta').prop('disabled',true);
		    $('#codigoBanco').on('change', function() {
		        const codigoBanco = $(this).val();
		        const codigoCuenta = $('#detalleCuenta').val();

		        if(codigoBanco.length>0 && codigoCuenta.length>0){
		        	$('#formRegistraCuentaBancoJD').trigger("reset");
		        	$('#detalleCuenta option[value=""]').prop('selected', 'selected').change();
		        	$('#infoCodigoCuenta').text('');
		        	$('#detalleCuenta').prop('disabled',true);
		        }
		        $('#detalleCuenta').empty().append('<option value="" selected>SELECCIONE CUENTA</option>');
		        if (codigoBanco) {
		        	$('#detalleCuenta').prop('disabled',false);
		            $.ajax({
		                url: '../modulo/combo-obtener-cuentas.php',
		                type: 'POST',
		                data: { codigoBanco: codigoBanco },
		                dataType: 'json',
		                success: function(response) {
		                    if (response.length > 0) {
		                        $.each(response, function(index, cuenta) {
		                            $('#detalleCuenta').append('<option value="' + cuenta.codigoCuenta + '">' + cuenta.detalle + '</option>');
		                        });
		                    }
		                }
		            });
		        }else{
		        	$('#formRegistraCuentaBancoJD').trigger("reset");
		        	$('#detalleCuenta option[value=""]').prop('selected', 'selected').change();
		        	$('#infoCodigoCuenta').text('');
		        	$('#detalleCuenta').prop('disabled',true);
		        }
		    });

		    $('#detalleCuenta').on('change', function() {
		        const codigoCuenta = $(this).val();
		        if (codigoCuenta) {
		            $('#codigoCuenta').val(codigoCuenta);
		            $('#infoCodigoCuenta').text(codigoCuenta);
		        } else {
		            $('#codigoCuenta').val('');
		            $('#infoCodigoCuenta').text('ESPERANDO NUMERO DE CUENTA');
		        }
		    });

		    $('#btAsignarCuentaBancoJuntaDirectiva').click(function (e) {
		    	e.preventDefault();
				const datos            = $('#formRegistraCuentaBancoJD').serialize();
				const valida           = $('#formRegistraCuentaBancoJD').valid();
				if(valida){
					$.ajax({
						url  : urlMantenimiento,
						type : 'POST',
						data : datos,
						dataType: 'json',
						beforeSend: function(){
							$('#overlay').show();
						},
						complete: function(){
							$('#overlay').hide();
						},
						success: function(respuesta){ 
							console.log(respuesta);
							if(respuesta.resultado=='CUENTA_EN_USO'){
								new PNotify({title: 'Error en Cuenta',text: 'Lo siento la cuenta que ha seleccionado ya esta en uso.',type: 'error',styling: 'bootstrap3',delay: 3000});
								
							}
							if(respuesta.resultado=='CUENTA_BANCO_ASIGNADO'){
								$("#codigoBanco").select2("val", "");
								$("#detalleCuenta").select2("val", "");
								$("#infoCodigoCuenta").text("ESPERANDO NUMERO DE CUENTA");
								new PNotify({title: 'Cuenta de Banco',text: 'Se asigno Centa de Banco a periodo de junta directiva.',type: 'success',styling: 'bootstrap3',delay: 3000});
								cuentasBancoJD.ajax.reload();
							}
						}
					});
				}
		    });
		});
	</script>

<?php } ?>

<?php if ($_POST['proceso'] === 'verJuntaDirectiva' && isset($_POST['idJuntaDirectiva'])) { ?>
	<?php
		include '../php/funciones.php';
		include '../php/conexion.php';
		$idJuntaDirectiva=$_POST['idJuntaDirectiva'];
		$sql="SELECT sm_junta_directiva.fechaPeriodo, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva.extensionJuntaDirectiva, sm_junta_directiva.fechaFinPeriodo, sm_junta_directiva.fechaRegistro, sm_junta_directiva.horaRegistro, sm_junta_directiva.usuario FROM sm_junta_directiva INNER JOIN sm_junta_directiva_vigencia ON sm_junta_directiva.idVigencia = sm_junta_directiva_vigencia.idVigencia WHERE sm_junta_directiva.idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta                = $conexion->query($sql);
		$resultado               = $consulta->fetch_assoc();
		$fechaPeriodo            = $resultado[fechaPeriodo];
		$vigenciaJunta           = $resultado[vigenciaJunta];
		$extensionJuntaDirectiva = $resultado[extensionJuntaDirectiva];		
		$fechaFinPeriodo         = $resultado[fechaFinPeriodo];
		$fechaRegistro           = $resultado[fechaRegistro];
		$horaRegistro            = $resultado[horaRegistro];
		$usuario                 = $resultado[usuario];

		$infoFechaInicio= infoFecha($fechaPeriodo, 'normal');
		$infoFechaFin= infoFecha($fechaFinPeriodo, 'normal');
		$infoFecharegistro = infoFecha($fechaRegistro, 'larga');

		if($extensionJuntaDirectiva==1){
			$sql="SELECT sm_junta_directiva_extension.fechaFinPeriodo, sm_junta_directiva_extension.fechaextensionPeriodo FROM sm_junta_directiva_extension WHERE sm_junta_directiva_extension.idJuntaDirectiva = '$idJuntaDirectiva' AND ID = (SELECT MAX(sm_junta_directiva_extension.id) FROM sm_junta_directiva_extension WHERE sm_junta_directiva_extension.idJuntaDirectiva = '$idJuntaDirectiva')";
			$consulta = $conexion->query($sql);
			$resultado = $consulta->fetch_assoc();
			$fechaFinPeriodoPre=$resultado[fechaFinPeriodo];
			$fechaextensionPeriodo=$resultado[fechaextensionPeriodo];
			$infoExtensionJD='<span class="text-yellow">DESDE</span> '.strtoupper(infoFecha($fechaFinPeriodoPre,'normal')).' <span class="text-yellow">HASTA EL</span> '.strtoupper(infoFecha($fechaextensionPeriodo,'normal'));
		}else{
			$infoExtensionJD="";
		}

		$sql="SELECT CONCAT(sm_usuarios.nombre,' ',sm_usuarios.paterno) AS nombreUsuario FROM sm_usuarios WHERE sm_usuarios.dni = '$usuario'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombreUsuario=$resultado[nombreUsuario];

		$sql="SELECT sm_junta_directiva_integrantes.codigoSocio, sm_junta_directiva_cargos.cargoJunta, CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio INNER JOIN sm_junta_directiva_cargos ON sm_junta_directiva_integrantes.idCargoJunta = sm_junta_directiva_cargos.idCargoJunta WHERE idJuntaDirectiva = '$idJuntaDirectiva' ORDER BY sm_junta_directiva_integrantes.idCargoJunta ASC";
		$juntaDirectiva = $conexion->query($sql);
		$i=1;
	?>
	<ul class="list-group bg-gray-2 mb-10">
		<li class="list-group-item"><strong>PERIODO:</strong> <span class="pull-right text-uppercase"><span class="text-yellow">DESDE</span> <?= $infoFechaInicio ?> <span class="text-yellow">AL</span> <?= $infoFechaFin ?></span></li>
		<?php if($extensionJuntaDirectiva==1){ ?>
			<li class="list-group-item"><strong>AMPLIADO:</strong> <span class="pull-right text-uppercase"><?= $infoExtensionJD ?></span></li>
		<?php } ?>
		<li class="list-group-item"><strong>REGISTRO:</strong> <span class="pull-right text-uppercase"><span class="text-yellow">POR</span> <?= $nombreUsuario ?>, <span class="text-yellow">EL DIA</span> <?= $infoFecharegistro ?></span></li>
	</ul>
	<table class="table table-sm table-hover table-bordered table-striped table-td-valign-middle mb-20">
		<thead class="bg-gray-2">
			<tr>
				<th class="text-center">#</th>
				<th class="text-center">SOCIO</th>
				<th class="text-left">CARGO</th>
				<th class="text-left">NOMBRE SOCIO</th>
			</tr>
		</thead>
		<tbody>
			<?php while ($cargo = $juntaDirectiva->fetch_assoc()) { ?>
				<?php
					$codigoSocio=$cargo['codigoSocio'];
					$cargoJunta=$cargo['cargoJunta'];
					$nombreSocio=$cargo['nombreSocio'];
				?>
				<tr>
					<td class="text-center"><?= $i ?></td>
					<td class="text-center"><?= $codigoSocio ?></td>
					<td class="text-left text-uppercase"><?= $cargoJunta ?></td>
					<td class="text-left"><?= $nombreSocio ?></td>
				</tr>
		<?php $i++; } ?>
		</tbody>
	</table>
<?php } ?>

<?php if ($_POST['proceso'] === 'ampliarPeriodo' && isset($_POST['idJuntaDirectiva'])) { ?>
	<?php
		include '../php/funciones.php';
		include '../php/conexion.php';
		$idJuntaDirectiva=$_POST['idJuntaDirectiva'];
		$modal=$_POST['modal'];
		$sql="SELECT sm_junta_directiva.fechaPeriodo, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva.fechaFinPeriodo, sm_junta_directiva.fechaRegistro, sm_junta_directiva.horaRegistro, sm_junta_directiva.usuario FROM sm_junta_directiva INNER JOIN sm_junta_directiva_vigencia ON sm_junta_directiva.idVigencia = sm_junta_directiva_vigencia.idVigencia WHERE sm_junta_directiva.idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$fechaPeriodo=$resultado[fechaPeriodo];
		$vigenciaJunta=$resultado[vigenciaJunta];
		$fechaFinPeriodo=$resultado[fechaFinPeriodo];
		$fechaRegistro=$resultado[fechaRegistro];
		$horaRegistro=$resultado[horaRegistro];
		$usuario=$resultado[usuario];

		$infoFechaInicio= infoFecha($fechaPeriodo, 'muycorta');
		$infoFechaFin= infoFecha($fechaFinPeriodo, 'muycorta');
		$infoFecharegistro = infoFecha($fechaRegistro, 'larga');

		$sql="SELECT sm_junta_directiva_vigencia.idVigencia, sm_junta_directiva_vigencia.vigenciaJunta FROM sm_junta_directiva_vigencia ORDER BY sm_junta_directiva_vigencia.vigenciaJunta ASC";
		$juntaDirectiva = $conexion->query($sql);
	?>
	<ul class="list-group bg-gray-3 mb-0">
		<li class="list-group-item"><strong>PERIODO:</strong> <span class="pull-right text-uppercase"><?= $infoFechaInicio ?> <span class="text-yellow">AL</span> <?= $infoFechaFin ?></span></li>
	</ul>

	<div class="panel formulario p-0 m-0">
		<div class="panel-body pt-20 pl-10 pr-10 pb-0 bg-slate-300">
			<form id="formAmpliarPeriodoJD">
				<div class="form-row">
    				<div class="form-group col-md-8">
						<select name="idVigencia" id="idVigencia" class="form-control">
							<option value="" selected="">SELECCIONE PERIODO DE AMPLIACION</option>
							<?php
								while ($junta = $juntaDirectiva->fetch_assoc()) {
									$idVigencia    = $junta[idVigencia];
									$vigenciaJunta = $junta[vigenciaJunta];
									if($vigenciaJunta>1){
										echo '<option value="'.$idVigencia.'">'.$vigenciaJunta.' MESES</option>';
									}else{
										echo '<option value="'.$idVigencia.'">'.$vigenciaJunta.' MES</option>';
									}
								}
							?>
						</select>
    				</div>
    				<div class="form-group col-md-4">
    					<input type="hidden" name="idJuntaDirectiva" value="<?= $idJuntaDirectiva ?>">
    					<input type="hidden" name="fechaFinPeriodo" value="<?= $fechaFinPeriodo ?>">
    					<input type="hidden" name="operacion" value="AMPLIA_FIN_PERIODO_JUNTA_DIRECTIVA">
						<button type="button" id="btnAmpliaPeriodo" class="btn btn-md btn-block btn-dark-2">AMPLIAR</button>
    				</div>
    			</div>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){
			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const urlProceso       = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';
			const fechaPeriodo     = '<?= $fechaPeriodo ?>';

			////////////////////////////////////////////////////////////
			/// REGISTRA
			////////////////////////////////////////////////////////////
			$('#btnAmpliaPeriodo').click(function (e) {
				e.preventDefault();
				const modal       = '<?= $modal ?>';
				const datos       = $('#formAmpliarPeriodoJD').serialize();
				const valida      = $('#formAmpliarPeriodoJD').valid();
				const jdDataTable = $('#jdDataTable').DataTable();
				if(valida){
					$.ajax({
						url  : urlMantenimiento,
						type : 'POST',
						data : datos,
						dataType: 'json',
						beforeSend: function(){
							$('.formulario').hide();
							$('#overlay').show();
						},
						complete: function(){
							$('.formulario').show();
							$('#overlay').hide();
						},
						success: function(respuesta){
							console.log(respuesta);
							if(respuesta.resultado=='REGISTRA_ENTENSION_PERIODO_OK'){
								$('#formAmpliarPeriodoJD').trigger("reset");
								new PNotify({title: 'Periodo Extendido',text: 'Se ha extendido el periodo de la Junta.',type: 'success',styling: 'bootstrap3',delay: 3000});
								$(modal).modal('hide');
								jdDataTable.ajax.reload(null,true);
							}

							if(respuesta.resultado=='REGISTRA_ENTENSION_PERIODO_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
							}
						}
					});
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formAmpliarPeriodoJD').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					idVigencia : { required: true },
				},
				messages: {
					idVigencia : "Por favor, seleccione periodo de ampliación.",
				}
			});
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'gestionCargos') { ?>
	<div id="overlay" style="display: none;"></div>
	<div class="panel formulario p-0 m-0">
		<div class="panel-body pt-20 pl-10 pr-10 pb-0 bg-slate-300">
			<form id="formAgregarCargo">
				<div class="form-row">
    				<div class="form-group col-md-8">
						<input type="text" class="form-control" name="cargoJunta" id="cargoJunta" placeholder="Ingrese Cargo Nuevo" autocomplete="off">
    				</div>
    				<div class="form-group col-md-4">
    					<input type="hidden" name="operacion" value="REGISTRA_CARGO_JUNTA_DIRECTIVA">
						<button type="button" id="btnRegistraCargo" class="btn btn-md btn-block btn-dark-2">REGISTRAR</button>
    				</div>
    			</div>
			</form>
		</div>
	</div>

	<div class="mt-10 ml-10 mr-10 mb-0">
		<table id="cargosDataTable" class="table table-bordered table-hover table-striped table-td-valign-middle mb-15">
			<thead class="bg-dark">
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">CARGO</th>
					<th class="text-center">ASIGNADOS</th>
					<th class="text-center"><i class="fa fa-align-justify"></i></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const urlDTCargos = '../modulo/dt-cargos-jd.php';
			const urlProceso    = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';
			$('#cargoJunta').focus();

			////////////////////////////////////////////////////////////
			/// DATA TABLE - GESTION DE CARGOS
			////////////////////////////////////////////////////////////
			const cargosDataTable = $('#cargosDataTable').DataTable({
				"bSort"        : false,
				"processing"   : true,
				'serverMethod' : 'POST',
				"ajax"         : urlDTCargos,
				"columns"      : [
					{ data: 'Nro' },
					{ data: 'cargo' },
					{ data: 'asignados' },
					{ data: 'menuOpciones' },
				],
				"columnDefs": [
					{ "targets": 0,  "className": "align-middle text-center" },
					{ "targets": 1,  "className": "align-middle text-left text-strong" },
					{ "targets": 2,  "className": "align-middle text-center" },
					{ "targets": 3,  "className": "align-middle text-center" },
				],
			});

			////////////////////////////////////////////////////////////
			/// ELIMINA - CARGOS
			////////////////////////////////////////////////////////////
			$('#cargosDataTable').on('click','.btnEliminaCargo',function(){
				const idCargoJunta = $(this).data('id');
				const cargoJunta = $(this).data('cargo');
				const operacion      = 'ELIMINA_CARGO_JUNTA_DIRECTIVA';
				const datos        = 'idCargoJunta='+idCargoJunta+'&cargoJunta='+cargoJunta+'&operacion='+operacion;
				swal({
					title: "¿Eliminar Cargo?",
					text: cargoJunta,
					type: "warning",
					showCancelButton: true,
					cancelButtonText: "No, Cancelar",
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Si, Eliminar",
					closeOnConfirm: false
				},
				function(){
					$.ajax({
			            url: urlMantenimiento,
			            type: 'POST',
			            data: datos,
			            dataType: 'json',
			            success: function (respuesta) {
			            	if(respuesta.resultado=='CARGO_ELIMINADO_OK'){
								swal("¡Eliminado!", "Cargo seleccionado fue eliminado.", "success");
								cargosDataTable.ajax.reload(null,false);
			            	}

			            	if(respuesta.resultado=='CARGO_ELIMINADO_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
			            	}
			            }
			        });
				});
			});

			////////////////////////////////////////////////////////////
			/// AGREGA - CARGOS
			////////////////////////////////////////////////////////////
			$('#btnRegistraCargo').click(function (e) {
				e.preventDefault();
				const datos = $('#formAgregarCargo').serialize();
				const valida = $('#formAgregarCargo').valid();

				if(valida){
					$.ajax({
						url  : urlMantenimiento,
						type : 'POST',
						data : datos,
						dataType: 'json',
						beforeSend: function(){
							$('.formulario').hide();
							$('#overlay').show();
						},
						complete: function(){
							$('.formulario').show();
							$('#overlay').hide();
						},
						success: function(respuesta){ 
							if(respuesta.resultado=='CARGO_EXISTE'){
								new PNotify({title: 'Duplicado',text: 'El cargo ingresado ya existe.',type: 'warning',styling: 'bootstrap3',delay: 3000});
								$('#cargoJunta').focus();
								$('#formAgregarCargo').trigger("reset");
							}

							if(respuesta.resultado=='CARGO_REGISTRADO_OK'){
								$('#formAgregarCargo').trigger("reset");
								new PNotify({title: 'Registrado',text: 'Cargo fue registrado.',type: 'success',styling: 'bootstrap3',delay: 3000});
								cargosDataTable.ajax.reload(null,false);
							}

							if(respuesta.resultado=='CARGO_REGISTRADO_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
							}
						}
					});
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formAgregarCargo').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					cargoJunta: { required: true },
				},
				messages: {
					cargoJunta: "Por favor, ingrese cargo nuevo.",
				}
			});
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'gestionVigencias') { ?>
	<?php
		include '../php/conexion.php';
		$vigenciasRegistradas=array();
		$query="SELECT vigenciaJunta FROM sm_junta_directiva_vigencia";
		$consultaVigencias = $conexion->query($query);
		while ($vigencias = $consultaVigencias->fetch_assoc()) {
			$vigencia=$vigencias['vigenciaJunta'];
			array_push($vigenciasRegistradas,$vigencia);
		}
	?>
	<div id="overlay" style="display: none;"></div>
	<div class="panel formulario p-0 m-0">
		<div class="panel-body pt-20 pl-10 pr-10 pb-0 bg-slate-300">
			<form id="formAgregarVigencia">
				<div class="form-row">
    				<div class="form-group col-md-8">
						<select name="vigenciaJunta" id="vigenciaJunta" class="form-control">
							<option value="" selected="">SELECCIONE PERIODO DE VIGENCIA</option>
							<?php
								for ($i=1; $i < 10; $i++) {
									if (!in_array($i, $vigenciasRegistradas)){
										if($i==1){
											echo '<option value="'.$i.'">'.$i.' AÑO</option>';
										}else{
											echo '<option value="'.$i.'">'.$i.' AÑOS</option>';
										}
									}

								}
							?>
						</select>
    				</div>
    				<div class="form-group col-md-4">
    					<input type="hidden" name="operacion" value="REGISTRA_CARGO_VIGENCIA_JUNTA_DIRECTIVA">
						<button type="button" id="btnRegistraVigencia" class="btn btn-md btn-block btn-dark-2">REGISTRAR</button>
    				</div>
    			</div>
			</form>
		</div>
	</div>

	<div class="mt-10 ml-10 mr-10 mb-0">
		<table id="vigenciasDataTable" class="table table-bordered table-hover table-striped table-td-valign-middle mb-15">
			<thead class="bg-dark">
				<tr>
					<th class="text-center">VIGENCIA</th>
					<th class="text-center">ASIGNADOS</th>
					<th class="text-center">ACTIVO</th>
					<th class="text-center"><i class="fa fa-align-justify"></i></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const urlDTVigencias = '../modulo/dt-vigencias-jd.php';
			const urlProceso    = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';
			$('#cargoJunta').focus();

			////////////////////////////////////////////////////////////
			/// DATA TABLE - GESTION DE VIGENCIAS
			////////////////////////////////////////////////////////////
			const vigenciasDataTable = $('#vigenciasDataTable').DataTable({
				"bSort"        : false,
				"processing"   : true,
				'serverMethod' : 'POST',
				"ajax"         : urlDTVigencias,
				"columns"      : [
					{ data: 'vigenciaJunta' },
					{ data: 'vigenciaAsignada' },
					{ data: 'activo' },
					{ data: 'menuOpciones' },
				],
				"columnDefs": [
					{ "targets": 0,  "className": "align-middle text-left text-strong" },
					{ "targets": 1,  "className": "align-middle text-center" },
					{ "targets": 2,  "className": "align-middle text-center" },
					{ "targets": 3,  "className": "align-middle text-center" },
				],
			});

			$('#vigenciasDataTable_filter').hide();
    		$('.dataTables_length').hide();

			////////////////////////////////////////////////////////////
			/// ACTIVA - VIGENCIA
			////////////////////////////////////////////////////////////
			$('#vigenciasDataTable').on('click','.btnActivaVigencia',function(){
				const idVigencia   = $(this).data('id');
				const infoVigencia = $(this).data('vigencia');
				const operacion    = 'PREDEFINIR_VIGENCIA_JUNTA_DIRECTIVA';
				const datos        = 'idVigencia='+idVigencia+'&infoVigencia='+infoVigencia+'&operacion='+operacion;
				swal({
					title: "¿Predefinir Vigencia?",
					text: infoVigencia,
					type: "warning",
					showCancelButton: true,
					cancelButtonText: "No, Cancelar",
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Si, Continuar",
					closeOnConfirm: false
				},
				function(){
					$.ajax({
			            url: urlMantenimiento,
			            type: 'POST',
			            data: datos,
			            dataType: 'json',
			            success: function (respuesta) {
							console.log(respuesta);
			            	if(respuesta.resultado=='VIGENCIA_ACTIVA_OK'){
								swal("¡Predefinido!", "La vigencia seleccionada fue predefinida.", "success");
								vigenciasDataTable.ajax.reload(null,false);
			            	}

			            	if(respuesta.resultado=='VIGENCIA_ACTIVA_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
			            	}
			            }
			        });
				});
			});

			////////////////////////////////////////////////////////////
			/// AGREGA - CARGOS
			////////////////////////////////////////////////////////////
			$('#btnRegistraVigencia').click(function (e) {
				e.preventDefault();
				const datos = $('#formAgregarVigencia').serialize();
				const valida = $('#formAgregarVigencia').valid();

				if(valida){
					$.ajax({
						url  : urlMantenimiento,
						type : 'POST',
						data : datos,
						dataType: 'json',
						beforeSend: function(){
							$('.formulario').hide();
							$('#overlay').show();
						},
						complete: function(){
							$('.formulario').show();
							$('#overlay').hide();
						},
						success: function(respuesta){ 
							if(respuesta.resultado=='VIGENCIA_EXISTE'){
								new PNotify({title: 'Duplicado',text: 'La vigencia seleccionada ya existe.',type: 'warning',styling: 'bootstrap3',delay: 3000});
								$('#formAgregarVigencia').trigger("reset");
							}

							if(respuesta.resultado=='VIGENCIA_REGISTRADO_OK'){
								$('#formAgregarVigencia').trigger("reset");
								new PNotify({title: 'Registrado',text: 'Vigencia fue registrada.',type: 'success',styling: 'bootstrap3',delay: 3000});
								vigenciasDataTable.ajax.reload(null,false);
							}

							if(respuesta.resultado=='VIGENCIA_REGISTRADO_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
							}
						}
					});
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formAgregarVigencia').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					vigenciaJunta: { required: true },
				},
				messages: {
					vigenciaJunta: "Por favor, seleccione vigencia.",
				}
			});
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'definirFinPeriodoRatificacion') { ?>
	<?php
		include '../php/funciones.php';
		include '../php/conexion.php';
		$modal=$_POST['modal'];
		$sql="SELECT sm_junta_directiva_fin_periodo.tiempo FROM sm_junta_directiva_fin_periodo WHERE id='1'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$tiempo=$resultado[tiempo];
	?>
	<div id="overlay" style="display: none;"></div>
	<div class="panel formulario p-0 m-0">
		<div class="panel-body pt-20 pl-10 pr-10 pb-0 bg-slate-300">
			<form id="formFinPeriodoRatificacion">
				<div class="form-row">
    				<div class="form-group col-md-8">
						<select name="tiempo" id="tiempo" class="form-control">
							<option value="" selected="">SELECCIONE FIN DE PERIODO</option>
							<?php
								for ($i=1; $i <= 3; $i++) {
									if($tiempo!=$i){
										if($i==1){
											echo '<option value="'.$i.'">'.$i.' MES</option>';
										}else{
											echo '<option value="'.$i.'">'.$i.' MESES</option>';
										}
									}
								}
							?>
						</select>
    				</div>
    				<div class="form-group col-md-4">
    					<input type="hidden" name="operacion" value="ACTUALIZAR_FIN_PERIODO_RATIFICAR_JUNTA_DIRECTIVA">
						<button type="button" id="btnActualizaPeriodo" class="btn btn-md btn-block btn-dark-2">ACTUALIZAR</button>
    				</div>
    			</div>
			</form>
		</div>
	</div>

	<div class="mt-10 ml-10 mr-10 mb-0">
		<table id="finPeriodoRatificarDataTable" class="table table-bordered table-td-valign-middle mb-15">
			<thead class="bg-gray-3">
				<tr>
					<th class="text-left">MESES (PREDEFINIDO)</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const urlDTFinPeriodo  = '../modulo/dt-fin-periodo-jd.php';
			const urlProceso       = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';
			const modal            = '<?= $modal ?>';

			////////////////////////////////////////////////////////////
			/// DATA TABLE - GESTION DE VIGENCIAS
			////////////////////////////////////////////////////////////
			const finPeriodoRatificarDataTable = $('#finPeriodoRatificarDataTable').DataTable({
				"paging"       : true,
				"pageLength"   : 1,
				"lengthChange" : false,
				"info"         : false,
				"bSort"        : false,
				"processing"   : true,
				'serverMethod' : 'POST',
				"ajax"         : urlDTFinPeriodo,
				"columns"      : [
					{ data: 'tiempo' },
				],
				"columnDefs": [
					{ "targets": 0,  "className": "align-middle text-left text-strong" },
				],
			});

			$('#finPeriodoRatificarDataTable_filter').hide();
    		$('#finPeriodoRatificarDataTable_filter_length').hide();
    		$('.dataTables_paginate.paging_simple_numbers').hide();

			////////////////////////////////////////////////////////////
			/// AGREGA - CARGOS
			////////////////////////////////////////////////////////////
			$('#btnActualizaPeriodo').click(function (e) {
				e.preventDefault();
				const datos = $('#formFinPeriodoRatificacion').serialize();
				const valida = $('#formFinPeriodoRatificacion').valid();
				const jdDataTable = $('#jdDataTable').DataTable();

				if(valida){
					$.ajax({
						url  : urlMantenimiento,
						type : 'POST',
						data : datos,
						dataType: 'json',
						beforeSend: function(){
							$('.formulario').hide();
							$('#overlay').show();
						},
						complete: function(){
							$('.formulario').show();
							$('#overlay').hide();
						},
						success: function(respuesta){
							if(respuesta.resultado=='PERIODO_REGISTRADO_OK'){
								$('#formFinPeriodoRatificacion').trigger("reset");
								new PNotify({title: 'Registrado',text: 'Periodo seleccionado fue predefinido.',type: 'success',styling: 'bootstrap3',delay: 3000});
								$(modal).modal('hide');
								jdDataTable.ajax.reload(null,false);
							}

							if(respuesta.resultado=='PERIODO_REGISTRADO_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
							}
						}
					});
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formFinPeriodoRatificacion').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					tiempo: { required: true },
				},
				messages: {
					tiempo: "Por favor, seleccione fin periodo para ratificar Junta Directiva.",
				}
			});
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'cambiarIntegranteJD') { ?>
	<?php
		include '../php/conexion.php';
		$idJuntaDirectiva=$_POST['idJuntaDirectiva'];
		$codigoSocio=$_POST['codigoSocio'];
		$idCargoJunta=$_POST['idCargoJunta'];
		$modal=$_POST['modal'];
		
		$sql="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio, sm_junta_directiva_cargos.cargoJunta FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio INNER JOIN sm_junta_directiva_cargos ON sm_junta_directiva_integrantes.idCargoJunta = sm_junta_directiva_cargos.idCargoJunta WHERE sm_junta_directiva_integrantes.idJuntaDirectiva = '$idJuntaDirectiva' AND sm_junta_directiva_integrantes.idCargoJunta = '$idCargoJunta' AND sm_junta_directiva_integrantes.codigoSocio = '$codigoSocio'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombreSocio=$resultado[nombreSocio];
		$cargoJunta=$resultado[cargoJunta];

		$sql="SELECT sm_socios.codigoSocio, CONCAT(sm_socios.nombre,' ', sm_socios.apPaterno,' ', sm_socios.apMaterno) AS nombreSocio FROM sm_socios ORDER BY sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
		$socios = $conexion->query($sql);
		$sociosRegistrados=array();
		$sql="SELECT sm_junta_directiva_integrantes.codigoSocio FROM sm_junta_directiva_integrantes WHERE sm_junta_directiva_integrantes.idJuntaDirectiva = '$idJuntaDirectiva'";
		$consultaCargos = $conexion->query($sql);
		while ($cargo = $consultaCargos->fetch_assoc()) {
			$codigoSocios=$cargo['codigoSocio'];
			array_push($sociosRegistrados,$codigoSocios);
		}
		$conexion->close();
	?>
	<div id="overlay" style="display: none;"></div>
	<div class="p-5">
		<ul class="list-group mt-0 mb-10 bg-gray">
			<li class="list-group-item"><strong>CARGO:</strong> <span class="pull-right text-uppercase"><strong><?= $cargoJunta ?></strong></span></li>
			<li class="list-group-item"><strong>NOMBRE:</strong> <span class="pull-right text-uppercase"><strong><?= $nombreSocio ?></strong></span></li>
			<li class="list-group-item"><strong>CODIGO SOCIO:</strong> <span class="pull-right text-uppercase"><strong><?= $codigoSocio ?></strong></span></li>
		</ul>

		<div class="panel bg-gray-5">
			<div class="panel-body pb-0">
				<form id="formRegistraRenuncia">
					<div class="form-group">
						<textarea name="motivoRenuncia" class="form-control text-uppercase" rows="4" placeholder="RAZON O JUSTIFICACION DE RENUNCIA" onblur="mayusculas(event, this)" required></textarea>
						<label class="error-validacion" for="motivoRenuncia" style="display:none;"></label>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" name="fechaRenuncia" id="fechaRenuncia" class="form-control" placeholder="FECHA DE RENUNCIA" required>
								<label class="error-validacion" for="fechaRenuncia" style="display:none;"></label>
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								<select class="seleccionar" name="codigoSocio" required>
									<option value="" selected="">SELECCIONE SOCIO</option>
									<?php
										while ($fila = $socios->fetch_assoc()) {
											if (!in_array($fila['codigoSocio'], $sociosRegistrados)){
												echo '<option value="'.$fila['codigoSocio'].'">'.$fila['nombreSocio'].'</option>';
											}
										}
									?>
								</select>
								<label class="error-validacion" for="codigoSocio" style="display:none;"></label>
							</div>
						</div>
					</div>
					
					<div class="form-group mb-15 pb-0">
						<input type="hidden" name="idJuntaDirectiva" value="<?= $idJuntaDirectiva ?>">
						<input type="hidden" name="idCargoJunta" value="<?= $idCargoJunta ?>">
						<input type="hidden" name="codigoSocioRenuncia" value="<?= $codigoSocio ?>">
						<input type="hidden" name="operacion" value="REGISTRA_RENUNCIA_JUNTA_DIRECTIVA">
						<button type="submit" class="btn btn-dark-1 btn-block btn-lg" id="btnRegistraRenuncia"><strong>REGISTRAR CAMBIO DE SOCIO</strong></button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			$('.seleccionar').select2();
			$('#motivoRenuncia').focus();
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';

			////////////////////////////////////////////////////////////
			/// DATEPICKER
			////////////////////////////////////////////////////////////
			$('#fechaRenuncia').datepicker({
				format: 'dd/mm/yyyy',
				maxDate: "+7D"
			});

			////////////////////////////////////////////////////////////
			/// AGREGA - CARGOS
			////////////////////////////////////////////////////////////
			$('#btnRegistraRenuncia').click(function (e) {
				e.preventDefault();
				const datos = $('#formRegistraRenuncia').serialize();
				const valida = $('#formRegistraRenuncia').valid();
				const modal = '<?= $modal ?>';
				const jdDataTable = $('#jdDataTable').DataTable();
				const jdCambiosDataTable = $('#jdCambiosDataTable').DataTable();
				if(valida){
					$.ajax({
						url  : urlMantenimiento,
						type : 'POST',
						data : datos,
						dataType: 'json',
						beforeSend: function(){
							$('#overlay').show();
						},
						complete: function(){
							$('#overlay').hide();
						},
						success: function(respuesta){ 
							console.log(respuesta);
							if(respuesta.resultado=='REGISTRA_RENUNCIA_OK'){
								$('#formRegistraRenuncia').trigger("reset");
								$(modal).modal('hide');
								new PNotify({title: 'Registrado',text: 'Se registro nuevo integrante de junta directiva.',type: 'success',styling: 'bootstrap3',delay: 3000});
								jdDataTable.ajax.reload(null,false);
								jdCambiosDataTable.ajax.reload(null,false);
							}

							if(respuesta.resultado=='REGISTRA_RENUNCIA_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
							}
						}
					});
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formRegistraRenuncia').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					motivoRenuncia: { required: true, minlength: 50 },
					fechaRenuncia: { required: true },
					codigoSocio: { required: true },
				},
				messages: {
					motivoRenuncia: "Por favor, ingrese razon de renuncia, como minimo de 50 caracteres.",
					fechaRenuncia: "Por favor, elija fecha de renuncia.",
					codigoSocio: "Por favor, seleccione socio.",
				}
			});
		});
	</script>
<?php } ?>

<?php if ($_POST['proceso'] === 'detallesRenuncia') { ?>
	<?php
		include '../php/funciones.php';
		include '../php/conexion.php';
		$idRenunciante=$_POST['idRenunciante'];
		
		$sql="SELECT sm_junta_directiva_renuncia_integrantes.idJuntaDirectiva, sm_junta_directiva_renuncia_integrantes.idCargoJunta, sm_junta_directiva_cargos.cargoJunta, sm_junta_directiva_renuncia_integrantes.codigoSocio, CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocio, sm_junta_directiva_renuncia_integrantes.codigoSocioRemplazo, sm_junta_directiva_renuncia_integrantes.motivoRenuncia, sm_junta_directiva_renuncia_integrantes.fechaRenuncia, sm_junta_directiva_renuncia_integrantes.fechaRegistro, sm_junta_directiva_renuncia_integrantes.horaRegistro, sm_junta_directiva_renuncia_integrantes.usuario, CONCAT(sm_usuarios.nombre,' ',sm_usuarios.paterno) AS nombreUsuario FROM sm_junta_directiva_renuncia_integrantes INNER JOIN sm_junta_directiva_cargos ON sm_junta_directiva_renuncia_integrantes.idCargoJunta = sm_junta_directiva_cargos.idCargoJunta INNER JOIN sm_socios ON sm_junta_directiva_renuncia_integrantes.codigoSocio = sm_socios.codigoSocio INNER JOIN sm_usuarios ON sm_junta_directiva_renuncia_integrantes.usuario = sm_usuarios.dni WHERE sm_junta_directiva_renuncia_integrantes.idRenunciante = '$idRenunciante'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$idJuntaDirectiva    = $resultado[idJuntaDirectiva];
		$idCargoJunta        = $resultado[idCargoJunta];
		$cargoJunta          = $resultado[cargoJunta];
		$codigoSocio         = $resultado[codigoSocio];
		$nombreSocio         = $resultado[nombreSocio];
		$codigoSocioRemplazo = $resultado[codigoSocioRemplazo];
		$motivoRenuncia      = $resultado[motivoRenuncia];
		$fechaRenuncia       = $resultado[fechaRenuncia];
		$fechaRegistro       = $resultado[fechaRegistro];
		$horaRegistro        = $resultado[horaRegistro];
		$usuario             = $resultado[usuario];
		$nombreUsuario       = $resultado[nombreUsuario];
		$infoFechaRenuncia   = infoFecha($fechaRenuncia,'larga');
		$infoFecharegistro   = infoFecha($fechaRegistro,'larga');
		$infoHoraRegistro    = horaCorta($horaRegistro);

		$sql="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombreSocioReemplazante FROM sm_socios WHERE codigoSocio='$codigoSocioRemplazo'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombreSocioReemplazante    = $resultado[nombreSocioReemplazante];
		$conexion->close();
	?>
	<div class="p-0 m-0">
		<ul class="list-group mt-10 ml-10 mr-10 mb-10 bg-gray-4">
			<li class="list-group-item"><strong>CARGO:</strong> <span class="pull-right text-uppercase"><strong><?= $cargoJunta ?></strong></span></li>
			<li class="list-group-item"><strong>NOMBRE:</strong> <span class="pull-right text-uppercase"><strong><?= $nombreSocio ?></strong></span></li>
			<li class="list-group-item"><strong>CODIGO SOCIO:</strong> <span class="pull-right text-uppercase"><strong><?= $codigoSocio ?></strong></span></li>
			<li class="list-group-item"><strong>FECHA DE RENUNCIA:</strong> <span class="pull-right text-uppercase"><strong><?= $infoFechaRenuncia ?></strong></span></li>
			<li class="list-group-item"><strong>CARGO REEMPLAZADO POR:</strong> <span class="pull-right text-uppercase"><strong><?= $nombreSocioReemplazante ?></strong></span></li>
			<li class="list-group-item"><strong>CODIGO DE SOCIO DE REEMPLAZANTE:</strong> <span class="pull-right text-uppercase"><strong><?= $codigoSocioRemplazo ?></strong></span></li>
			<li class="list-group-item"><strong>REGISTRO:</strong> <span class="pull-right text-uppercase"><strong><?= $infoFecharegistro.' A LAS '.$infoHoraRegistro.' POR '.$nombreUsuario ?></strong></span></li>
		</ul>
		<div class="bg-gray-4 mt-10 ml-10 mr-10 mb-10 p-0">
			<p class="p-10"><?= $motivoRenuncia ?></p>
		</div>
	</div>
<?php } ?>

<?php if ($_POST['proceso'] === 'ratificaJuntaDirectiva') { ?>
	<?php
		include '../php/conexion.php';
		include '../php/funciones.php';
		$idJuntaDirectiva=$_POST['idJuntaDirectiva'];
		$modal=$_POST['modal'];

		$sql="SELECT sm_junta_directiva.fechaPeriodo, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva.fechaFinPeriodo, sm_junta_directiva.fechaRegistro, sm_junta_directiva.horaRegistro, sm_junta_directiva.usuario FROM sm_junta_directiva INNER JOIN sm_junta_directiva_vigencia ON sm_junta_directiva.idVigencia = sm_junta_directiva_vigencia.idVigencia WHERE sm_junta_directiva.idJuntaDirectiva = '$idJuntaDirectiva'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$fechaPeriodo=$resultado[fechaPeriodo];
		$vigenciaJunta=$resultado[vigenciaJunta];
		$fechaFinPeriodo=$resultado[fechaFinPeriodo];
		$fechaRegistro=$resultado[fechaRegistro];
		$horaRegistro=$resultado[horaRegistro];
		$usuario=$resultado[usuario];

		$sql="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombrePresidente=$resultado[nombrePresidente];

		$infoFechaInicio= infoFecha($fechaPeriodo, 'muycorta');
		$infoFechaFin= infoFecha($fechaFinPeriodo, 'muycorta');
		$infoFecharegistro = infoFecha($fechaRegistro, 'larga');
		$fechaInputInicioPeriodo = infoFecha($fechaFinPeriodo,'resultados');

		$sql="SELECT CONCAT(sm_usuarios.nombre,' ',sm_usuarios.paterno) AS nombreUsuario FROM sm_usuarios WHERE sm_usuarios.dni = '$usuario'";
		$consulta = $conexion->query($sql);
		$resultado = $consulta->fetch_assoc();
		$nombreUsuario=$resultado[nombreUsuario];

		$sql="SELECT sm_junta_directiva_vigencia.idVigencia, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva_vigencia.activo FROM sm_junta_directiva_vigencia ORDER BY sm_junta_directiva_vigencia.vigenciaJunta ASC";
		$consulta = $conexion->query($sql);

		$sql="SELECT sm_banco_cuentas.codigoCuenta, sm_bancos.entidad, sm_banco_cuentas.detalle FROM sm_banco_cuentas INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco";
		$cuentasBanco = $conexion->query($sql);

		$cuentasUsadasJD=array();
		$sql="SELECT sm_junta_directiva_cuenta_banco.codigoCuenta FROM sm_junta_directiva_cuenta_banco";
		$cuentasUsadas = $conexion->query($sql);
		while ($cuentaBanco = $cuentasUsadas->fetch_assoc()) {
			$codigoCuenta = $cuentaBanco['codigoCuenta'];
			array_push($cuentasUsadasJD,$codigoCuenta);
		}
		$conexion->close();
	?>
	<div class="m-0 bg-gray-2">
		<ul class="list-group mt-0 mb-10 bg-gray-3">
			<li class="list-group-item"><strong>PERIODO:</strong> <span class="pull-right text-strong text-uppercase"><?= $infoFechaInicio ?> <span class="text-yellow">AL</span> <?= $infoFechaFin ?></span></li>
			<li class="list-group-item"><strong>PRESIDENTE:</strong> <span class="pull-right text-strong text-uppercase"><?= $nombrePresidente ?></span></li>
		</ul>
		<form id="formRatificaJuntaDirectiva">
			<div class="row m-0 pt-10 pl-15 pr-15 pb-0">
				<div class="col-md-3">
					<div class="form-group">
						<input type="text" class="form-control" name="fechaPeriodo" id="fechaPeriodo" value="<?= $fechaInputInicioPeriodo ?>" autocomplete="off" placeholder="FECHA DE INICIO DE PERIODO" required>
						<label class="error-validacion" for="fechaPeriodo" style="display:none;"></label>
					</div>
				</div>
				<div class="col-md-9">
					<div class="form-group">
						<select class="seleccion" name="idVigencia" id="idVigencia" required>
							<?php
								while ($fila = $consulta->fetch_assoc()) {
									$activo        = $fila['activo'];
									$idVigencia    = $fila['idVigencia'];
									$vigenciaJunta = $fila['vigenciaJunta'];
									if($idVigencia>1){
										if($activo==1){
											echo '<option value="'.$idVigencia.'" selected>'.$vigenciaJunta.' MESES (VIGENCIA DE JUNTA DIRECTIVA)</option>';
										}else{
											echo '<option value="'.$idVigencia.'">'.$vigenciaJunta.' MESES (VIGENCIA DE JUNTA DIRECTIVA)</option>';
										}
									}else{
										if($activo==1){
											echo '<option value="'.$idVigencia.'" selected>'.$vigenciaJunta.' MES (VIGENCIA DE JUNTA DIRECTIVA)</option>';
										}else{
											echo '<option value="'.$idVigencia.'">'.$vigenciaJunta.' MES (VIGENCIA DE JUNTA DIRECTIVA)</option>';
										}
									}
								}
							?>
						</select>
						<label class="error-validacion" for="idVigencia" style="display:none;"></label>
					</div>
				</div>
				<div class="col-md-8">
					<div class="form-group">
						<select class="seleccion" name="codigoCuenta" id="codigoCuenta" required>
							<option value="">SELECCIONE CUENTA BANCARIA</option>
							<?php
								while ($cuentaBanco = $cuentasBanco->fetch_assoc()) {
									$codigoCuenta = $cuentaBanco['codigoCuenta'];
									$entidad      = $cuentaBanco['entidad'];
									$detalle      = $cuentaBanco['detalle'];
									if(!in_array($codigoCuenta,$cuentasUsadasJD)){
										echo '<option value="'.$codigoCuenta.'">'.$entidad.' '.$detalle.'</option>';
									}
								}
							?>
						</select>
						<label class="error-validacion" for="codigoCuenta" style="display:none;"></label>
					</div>
				</div>
				<div class="col-md-4 d-flex align-items-end">
				 	<input type="hidden" name="idJuntaDirectiva" value="<?= $idJuntaDirectiva ?>">
					<input type="hidden" name="operacion" value="RATIFICA_ACTUAL_JUNTA_DIRECTIVA">
					<button type="submit" id="btnRatificaJuntaDirectiva" class="btn btn-lg btn-success btn-block">RATIFICA JUNTA DIRECTIVA</button>
				</div>
			</div>
		</form>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';

			////////////////////////////////////////////////////////////
			/// SELECT2
			////////////////////////////////////////////////////////////
			$('.seleccion').select2();

			////////////////////////////////////////////////////////////
			/// DATEPICKER
			////////////////////////////////////////////////////////////
			$('#fechaPeriodo').datepicker({
				format: 'dd/mm/yyyy',
				maxDate: "+7D"
			});

			////////////////////////////////////////////////////////////
			/// RATIFICA JD
			////////////////////////////////////////////////////////////
			$('#btnRatificaJuntaDirectiva').click(function (e) {
				e.preventDefault();
				const datos = $('#formRatificaJuntaDirectiva').serialize();
				const valida = $('#formRatificaJuntaDirectiva').valid();
				const modal = '#<?= $modal ?>';
				const jdDataTable = $('#jdDataTable').DataTable();
				if(valida){
					$.ajax({
						url  : urlMantenimiento,
						type : 'POST',
						data : datos,
						dataType: 'json',
						beforeSend: function(){
							$('#overlay').show();
						},
						complete: function(){
							$('#overlay').hide();
						},
						success: function(respuesta){ 
							console.log(respuesta);
							if(respuesta.resultado=='REGISTRA_RATIFICACION_OK'){
								$(modal).modal('hide');
								new PNotify({title: 'Ratificado',text: 'Se ratifico a la actual junta directiva.',type: 'success',styling: 'bootstrap3',delay: 3000});
								jdDataTable.ajax.reload(null,false);
							}

							if(respuesta.resultado=='REGISTRA_RATIFICACION_ERROR'){
								new PNotify({title: 'Advertencia',text: 'Error en conexion con la DB.',type: 'warning',styling: 'bootstrap3',delay: 3000});
							}
						}
					});
				}
			});

			////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO DE CARGOS NUEVOS
			////////////////////////////////////////////////////////////
			$('#formRatificaJuntaDirectiva').validate({
				errorClass: 'validation-error-label',
				validClass: "validation-valid-label",
				highlight: function (element) {
					$(element).css('border-color', '#D65C4F').fadeIn(500);
				},
				unhighlight: function (element) {
					$(element).css('border-color', '#D5D5D5').fadeIn(500);
				},
				rules: {
					fechaPeriodo: { required: true },
					codigoCuenta: { required: true },					
				},
				messages: {
					fechaPeriodo: "Por favor, elija fecha.",
					codigoCuenta: "Seleccione Cuenta Bancaria.",
				}
			});
		});
	</script>
<?php } ?>
