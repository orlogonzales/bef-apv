<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina ="PERIODOS DE JUNTA DIRECTIVA";
	$menuActual   ="periodosJuntaDirectiva";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>
	<div class="panel">
		<div class="panel-heading bg-teal">
			<h6 class="panel-title textoNegrita">PERIODOS</h6>
			<div class="pull-right" style="margin: -20px 0px 0px 0px !important;">
				<div class="dropdown">
					<button class="btn btn-xs btn-dark dropdown-toggle" type="button" data-toggle="dropdown">OPCIONES <span class="caret"></span></button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="#" id="btnGestionCargos">GESTION DE CARGOS</a></li>
						<li><a href="#" id="btnGestionVigencias">GESTION DE VIGENCIAS</a></li>
						<li><a href="#" id="btnFinPeriodoRatificacion">FIN DE PERIODO - RATIFICACION</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div class="panel-body info">
			<table id="jdDataTable" class="table table-bordered table-hover table-striped table-td-valign-middle mb-15">
				<thead class="bg-dark">
					<tr>
						<th class="text-center">#</th>
						<th class="text-left">CODIGO</th>
						<th class="text-left">PRESIDENTE</th>
						<th class="text-left">CUENTA BANCO</th>
						<th class="text-center">INICIO</th>
						<th class="text-left">PERIODO</th>
						<th class="text-left">AMPLIADO</th>
						<th class="text-center">FIN</th>
						<th class="text-center">RATIFICADO</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>

	<!-- Modal Junta Directiva-->
	<div id="modalJuntaDirectiva" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content modal-lg">
				<div class="modal-header bg-brown">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
					<h5 class="modal-title">VISUALIZAR JUNTA DIRECTIVA</h5>
				</div>
				<div class="modal-body">
					<div id="boxJuntaDirectiva">
						<div class="content-loader">
							<img src="../assets/images/preloader-md.svg" alt="Cargando...">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

		<!-- Modal Cuentas Junta Directiva-->
	<div id="modalCuentasJuntaDirectiva" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content modal-lg">
				<div class="modal-header bg-brown">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
					<h5 class="modal-title">VISUALIZAR CUENTAS BANCARIAS DE JUNTA DIRECTIVA</h5>
				</div>
				<div class="modal-body">
					<div id="boxCuentasJuntaDirectiva">
						<div class="content-loader">
							<img src="../assets/images/preloader-md.svg" alt="Cargando...">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Junta Directiva-->
	<div id="modalModificaJuntaDirectiva" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content modal-lg">
				<div class="modal-header bg-brown">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
					<h5 class="modal-title">MODIFICA JUNTA DIRECTIVA</h5>
				</div>
				<div class="modal-body">
					<div id="boxModificaDatos">
						<div class="content-loader">
							<img src="../assets/images/preloader-md.svg" alt="Cargando...">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Gestion de Cargos -->
	<div id="modalGestionCargos" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content modal-lg">
				<div class="modal-header bg-brown">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
					<h5 class="modal-title">GESTION DE CARGOS</h5>
				</div>
				<div class="modal-body m-0 p-0">
					<div id="boxGestionCargos">
						<div class="content-loader">
							<img src="../assets/images/preloader-md.svg" alt="Cargando...">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Gestion de Vigencias -->
	<div id="modalGestionVigencias" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content modal-md">
				<div class="modal-header bg-brown">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
					<h5 class="modal-title">GESTION DE VIGENCIA DE JUNTA DIRECTIVA</h5>
				</div>
				<div class="modal-body m-0 p-0">
					<div id="boxGestionVigencias">
						<div class="content-loader">
							<img src="../assets/images/preloader-md.svg" alt="Cargando...">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Fin de Periodo para Ratificacion de Junta Directiva -->
	<div id="modalFinPeriodoRatificacion" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content modal-md">
				<div class="modal-header bg-brown">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
					<h5 class="modal-title">DEFINIR FIN DE PERIODO</h5>
				</div>
				<div class="modal-body m-0 p-0">
					<div id="boxFinPeriodoRatificacion">
						<div class="content-loader">
							<img src="../assets/images/preloader-md.svg" alt="Cargando...">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Ratifica Junta Directiva -->
	<div id="modalRatificaJuntaDirectiva" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content modal-lg">
				<div class="modal-header bg-brown">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
					<h5 class="modal-title">RATIFICA JUNTA DIRECTIVA</h5>
				</div>
				<div class="modal-body m-0 p-0">
					<div id="boxRatificaJuntaDirectiva">
						<div class="content-loader">
							<img src="../assets/images/preloader-md.svg" alt="Cargando...">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal Ratifica Junta Directiva -->
	<div id="modalAmpliarJuntaDirectiva" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-brown">
					<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
					<h5 class="modal-title">AMPLIAR PERIODO DE JUNTA DIRECTIVA</h5>
				</div>
				<div class="modal-body m-0 p-0">
					<div id="boxAmpliarJuntaDirectiva">
						<div class="content-loader">
							<img src="../assets/images/preloader-md.svg" alt="Cargando...">
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function(){
			////////////////////////////////////////////////////////////
			/// VARIABLES
			////////////////////////////////////////////////////////////
			const urlModifica      = 'modifica-junta-directiva.php';
			const urlRatifica      = 'ratifica-junta-directiva.php';
			const urlDTPeriodos    = '../modulo/dt-periodos-jd.php';
			const urlDTCargos      = '../modulo/dt-cargos-jd.php';
			const urlProceso       = '../modulo/modulo-jd-registro.php';
			const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';

			////////////////////////////////////////////////////////////
			/// GESTION DE CARGOS
			////////////////////////////////////////////////////////////
			$('#btnGestionCargos').click(function (e) {
				e.preventDefault();
				const proceso      = 'gestionCargos';
				const datos        = 'proceso='+proceso;
				$('#modalGestionCargos').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalGestionCargos").modal("show");
				$.ajax({
					url: urlProceso,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxGestionCargos').html(response);
					},
					error: function () {
						$('#boxGestionCargos').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});
			});

			////////////////////////////////////////////////////////////
			/// GESTION DE VIGENCIAS
			////////////////////////////////////////////////////////////
			$('#btnGestionVigencias').click(function (e) {
				e.preventDefault();
				const proceso      = 'gestionVigencias';
				const datos        = 'proceso='+proceso;
				$('#modalGestionVigencias').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalGestionVigencias").modal("show");
				$.ajax({
					url: urlProceso,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxGestionVigencias').html(response);
					},
					error: function () {
						$('#boxGestionVigencias').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});
			});

			////////////////////////////////////////////////////////////
			/// GESTION DE VIGENCIAS
			////////////////////////////////////////////////////////////
			$('#btnFinPeriodoRatificacion').click(function (e) {
				e.preventDefault();
				const proceso = 'definirFinPeriodoRatificacion';
				const modal   = '#modalFinPeriodoRatificacion';
				const datos   = 'modal='+modal+'&proceso='+proceso;
				$('#modalFinPeriodoRatificacion').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalFinPeriodoRatificacion").modal("show");
				$.ajax({
					url: urlProceso,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxFinPeriodoRatificacion').html(response);
					},
					error: function () {
						$('#boxFinPeriodoRatificacion').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});
			});

			////////////////////////////////////////////////////////////
			/// DATA TABLE - JUNTA DIRECTIVA
			////////////////////////////////////////////////////////////
			const jdDataTable = $('#jdDataTable').DataTable({
				"paging"       : true,
				"pageLength"   : 10,
				"lengthChange" : false,
				"info"         : false,
				"bPaginate"    : false,
				"bSort"        : false,
				"processing"   : true,
				'serverMethod' : 'POST',
				"ajax"         : urlDTPeriodos,
				"columns"      : [
					{ data: 'Nro' },
					{ data: 'codigoJunta' },
					{ data: 'nombrePresidente' },
					{ data: 'cuentaBancaria' },
					{ data: 'fechaInicio' },
					{ data: 'periodo' },
					{ data: 'ampliacion' },
					{ data: 'fechaFin' },
					{ data: 'ratificado' },
					{ data: 'menuOpciones' },
				],
				"columnDefs": [
					{ "targets": 0,  "className": "align-middle text-center" },
					{ "targets": 1,  "className": "align-middle text-center" },
					{ "targets": 2,  "className": "align-middle text-left text-strong" },
					{ "targets": 3,  "className": "align-middle text-center text-strong" },
					{ "targets": 4,  "className": "align-middle text-center text-uppercase" },
					{ "targets": 5,  "className": "align-middle text-center" },
					{ "targets": 6,  "className": "align-middle text-center" },
					{ "targets": 7,  "className": "align-middle text-center text-uppercase" },
					{ "targets": 8,  "className": "align-middle text-center" },
					{ "targets": 9,  "className": "align-middle text-center" },
				],
			});

			jdDataTable.on('draw', function() {
		        $('[data-toggle="tooltip"]').tooltip();
		    });

			$('#jdDataTable').on('click','.btnVerCuentasJD',function(){
				const idJuntaDirectiva = $(this).data('id');
				const proceso      = 'verCuentasJuntaDirectiva';
				const datos        = 'idJuntaDirectiva='+idJuntaDirectiva+'&proceso='+proceso;
				$('#modalCuentasJuntaDirectiva').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalCuentasJuntaDirectiva").modal("show");
				$.ajax({
					url: urlProceso,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxCuentasJuntaDirectiva').html(response);
					},
					error: function () {
						$('#boxCuentasJuntaDirectiva').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});
			});

			$('#jdDataTable').on('click','.btnVerJD',function(){
				const idJuntaDirectiva = $(this).data('id');
				const proceso      = 'verJuntaDirectiva';
				const datos        = 'idJuntaDirectiva='+idJuntaDirectiva+'&proceso='+proceso;
				$('#modalJuntaDirectiva').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalJuntaDirectiva").modal("show");
				$.ajax({
					url: urlProceso,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxJuntaDirectiva').html(response);
					},
					error: function () {
						$('#boxJuntaDirectiva').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});
			});

			$('#jdDataTable').on('click','.btnAmpliarJD',function(){
				const idJuntaDirectiva = $(this).data('id');
				const modal            = '#modalAmpliarJuntaDirectiva';
				const proceso          = 'ampliarPeriodo';
				const datos            = 'idJuntaDirectiva='+idJuntaDirectiva+'&modal='+modal+'&proceso='+proceso;
				$('#modalAmpliarJuntaDirectiva').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalAmpliarJuntaDirectiva").modal("show");
				$.ajax({
					url: urlProceso,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxAmpliarJuntaDirectiva').html(response);
					},
					error: function () {
						$('#boxAmpliarJuntaDirectiva').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});
			});

			$('#jdDataTable').on('click','.btnModificaJD',function(){
				const idJuntaDirectiva = $(this).data('id');
				const datos            = 'idJuntaDirectiva='+idJuntaDirectiva;
				const form = $('<form action="' + urlModifica + '" method="GET"></form>');
			    $('<input>').attr({
			        type: 'hidden',
			        name: 'idJuntaDirectiva',
			        value: idJuntaDirectiva
			    }).appendTo(form);
			    $('body').append(form);
			    form.submit();
			});

			$('#jdDataTable').on('click','.btnRatificaJD',function(){
				const idJuntaDirectiva = $(this).data('id');
				const modal            = 'modalRatificaJuntaDirectiva';
				const proceso          = 'ratificaJuntaDirectiva';
				const datos            = 'idJuntaDirectiva='+idJuntaDirectiva+'&modal='+modal+'&proceso='+proceso;
				$('#modalRatificaJuntaDirectiva').modal({
					backdrop: 'static',
					keyboard: true,
				});
				$("#modalRatificaJuntaDirectiva").modal("show");
				$.ajax({
					url: urlProceso,
					type: 'POST',
					data: datos,
					success: function (response) {
						$('#boxRatificaJuntaDirectiva').html(response);
					},
					error: function () {
						$('#boxRatificaJuntaDirectiva').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
					}
				});
			});

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
		});
	</script>
<?php include($ruta.'template/footer.tpl'); ?>