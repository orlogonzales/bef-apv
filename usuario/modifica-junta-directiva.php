<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES
	/////////////////////////////////////////////////////////////////////
	$ruta        ='../';
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="MODIFICA DE JUNTA DIRECTIVA";
	$menuActual="periodosJuntaDirectiva";
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - HEADER
	/////////////////////////////////////////////////////////////////////
	include($ruta.'template/header.tpl');
	include($ruta.'php/conexion.php');

	$idJuntaDirectiva=$_GET['idJuntaDirectiva'];

	$sql="SELECT sm_junta_directiva.fechaPeriodo, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva.fechaFinPeriodo, sm_junta_directiva.fechaRegistro, sm_junta_directiva.horaRegistro, sm_junta_directiva.usuario FROM sm_junta_directiva INNER JOIN sm_junta_directiva_vigencia ON sm_junta_directiva.idVigencia = sm_junta_directiva_vigencia.idVigencia WHERE sm_junta_directiva.idJuntaDirectiva = '$idJuntaDirectiva'";
	$consulta = $conexion->query($sql);
	$resultado = $consulta->fetch_assoc();
	$fechaPeriodo=$resultado['fechaPeriodo'];
	$vigenciaJunta=$resultado['vigenciaJunta'];
	$fechaFinPeriodo=$resultado['fechaFinPeriodo'];
	$fechaRegistro=$resultado['fechaRegistro'];
	$horaRegistro=$resultado['horaRegistro'];
	$usuario=$resultado['usuario'];

	$sql="SELECT CONCAT(sm_socios.nombre,' ',sm_socios.apPaterno,' ',sm_socios.apMaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
	$consulta = $conexion->query($sql);
	$resultado = $consulta->fetch_assoc();
	$nombrePresidente=$resultado['nombrePresidente'];

	$infoFechaInicio= infoFecha($fechaPeriodo, 'muycorta');
	$infoFechaFin= infoFecha($fechaFinPeriodo, 'muycorta');
	$infoFecharegistro = infoFecha($fechaRegistro, 'larga');

	$sql="SELECT CONCAT(sm_usuarios.nombre,' ',sm_usuarios.paterno) AS nombreUsuario FROM sm_usuarios WHERE sm_usuarios.dni = '$usuario'";
	$consulta = $conexion->query($sql);
	$resultado = $consulta->fetch_assoc();
	$nombreUsuario=$resultado['nombreUsuario'];
?>
<div class="panel">
	<div class="panel-heading bg-teal">
		<h6 class="panel-title textoNegrita">DETALLES DE JUNTA DIRECTIVA</h6>
	</div>
	<div class="panel-body pb-0">
		<ul class="list-group mt-0 mb-10 bg-gray-3">
			<li class="list-group-item"><strong>PERIODO:</strong> <span class="pull-right text-strong text-uppercase"><?= $infoFechaInicio ?> <span class="text-yellow">AL</span> <?= $infoFechaFin ?></span></li>
			<li class="list-group-item"><strong>PRESIDENTE:</strong> <span class="pull-right text-strong text-uppercase"><?= $nombrePresidente ?></span></li>
			<li class="list-group-item"><strong>REGISTRO:</strong> <span class="pull-right text-strong text-uppercase"><?= $infoFecharegistro ?> <span class="text-yellow">POR</span> <?= $nombreUsuario ?></span></li>
		</ul>
		<table id="jdDataTable" class="table table-bordered table-hover table-striped table-td-valign-middle mb-20">
			<thead class="bg-gray-4">
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">SOCIO</th>
					<th class="text-left">CARGO</th>
					<th class="text-center">RENUNCIA</th>
					<th class="text-left">NOMBRE SOCIO</th>
					<th class="text-center"><i class="fa fa-align-justify"></i></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		
		<!-- EX MIEMBROS DE JUSTA DIRECTIVA -->
		<table id="jdCambiosDataTable" class="table table-bordered table-hover table-striped table-td-valign-middle mb-15">
			<thead class="bg-warning-300">
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">SOCIO</th>
					<th class="text-left">CARGO</th>
					<th class="text-left">NOMBRE SOCIO</th>
					<th class="text-center">RENUNCIA</th>
					<th class="text-center"><i class="fa fa-align-justify"></i></th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
	</div>
</div>

<!-- Modal - Cambiar Integrantes / Renuncia -->
<div id="modalCambiarIntegrante" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content modal-lg">
			<div class="modal-header bg-brown">
				<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
				<h5 class="modal-title">RENUNCIA DE INTEGRANTE</h5>
			</div>
			<div class="modal-body m-0 p-0">
				<div id="boxCambioIntegrante">
					<div class="content-loader">
						<img src="../assets/images/preloader-md.svg" alt="Cargando...">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal - Detalles de Renuncia -->
<div id="modalDetallesRenuncia" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content modal-lg">
			<div class="modal-header bg-brown">
				<button type="button" class="btn btn-xs btn-danger pull-right" data-dismiss="modal">CERRAR</button>
				<h5 class="modal-title">DETALLES DE RENUNCIA</h5>
			</div>
			<div class="modal-body m-0 p-0">
				<div id="boxDetallesRenuncia">
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
		const idJuntaDirectiva           = '<?= $idJuntaDirectiva ?>';
		const urlDTJuntaDirectiva        = '../modulo/dt-junta-directiva.php';
		const urlDTCambiosJuntaDirectiva = '../modulo/dt-cambios-junta-directiva.php';
		const urlProceso                 = '../modulo/modulo-jd-registro.php';

		////////////////////////////////////////////////////////////
		/// DATA TABLE - JUNTA DIRECTIVA
		////////////////////////////////////////////////////////////
		const jdDataTable = $('#jdDataTable').DataTable({
			"paging": true,
			"pageLength": 20,
			"lengthChange": false,
		    "info": false,
		    "bPaginate": false,
		    "bSort": false,
		    "processing": true,
		    'serverMethod': 'POST',
		    "ajax": {
		        "url": urlDTJuntaDirectiva,
		        "type": "POST",
		        "data": function(d) {
		            d.idJuntaDirectiva = idJuntaDirectiva;
		        }
		    },
		    "columns": [
		        { data: 'Nro' },
		        { data: 'socio' },
		        { data: 'cargo' },
		        { data: 'renuncia' },
		        { data: 'nombreSocio' },
		        { data: 'menuOpciones' },
		    ],
		    "columnDefs": [
		        { "targets": 0, "className": "align-middle text-center" },
		        { "targets": 1, "className": "align-middle text-center" },
		        { "targets": 2, "className": "align-middle text-left text-uppercase text-strong" },
		        { "targets": 3, "className": "align-middle text-center" },
		        { "targets": 4, "className": "align-middle text-left text-strong" },
		        { "targets": 5, "className": "align-middle text-center" },
		    ],
		});
		$('#jdDataTable_filter').hide();
    	$('#jdDataTable_length').hide();
    	$('.dataTables_paginate.paging_simple_numbers').hide();

    	$('#jdDataTable').on('click','.btnCambiarIntegrante',function(){
			const codigoSocio = $(this).data('socio');
			const idCargoJunta = $(this).data('idcargo');
			const modal        = '#modalCambiarIntegrante';
			const proceso      = 'cambiarIntegranteJD';
			const datos        = 'idJuntaDirectiva='+idJuntaDirectiva+'&codigoSocio='+codigoSocio+'&idCargoJunta='+idCargoJunta+'&modal='+modal+'&proceso='+proceso;
			$(modal).modal({
				backdrop: 'static',
				keyboard: true,
			});
			$(modal).modal("show");
			$.ajax({
				url: urlProceso,
				type: 'POST',
				data: datos,
				success: function (response) {
					$('#boxCambioIntegrante').html(response);
				},
				error: function () {
					$('#boxCambioIntegrante').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
				}
			});
		});

		////////////////////////////////////////////////////////////
		/// DATA TABLE - CAMBIOS EN JUNTA DIRECTIVA
		////////////////////////////////////////////////////////////
		const jdCambiosDataTable = $('#jdCambiosDataTable').DataTable({
			"paging": true,
			"pageLength": 20,
			"lengthChange": false,
		    "info": false,
		    "bPaginate": false,
		    "bSort": false,
		    "processing": true,
		    'serverMethod': 'POST',
		    "ajax": {
		        "url": urlDTCambiosJuntaDirectiva,
		        "type": "POST",
		        "data": function(d) {
		            d.idJuntaDirectiva = idJuntaDirectiva;
		        }
		    },
		    "columns": [
		        { data: 'Nro' },
		        { data: 'socio' },
		        { data: 'cargo' },
		        { data: 'nombreSocio' },
		        { data: 'renuncia' },
		        { data: 'menuOpciones' },
		    ],
		    "columnDefs": [
		        { "targets": 0, "className": "align-middle text-center" },
		        { "targets": 1, "className": "align-middle text-center" },
		        { "targets": 2, "className": "align-middle text-left text-uppercase text-strong" },
		        { "targets": 3, "className": "align-middle text-left text-strong" },
		        { "targets": 4, "className": "align-middle text-center text-uppercase" },
		        { "targets": 5, "className": "align-middle text-center" },
		    ],
		});
		$('#jdCambiosDataTable_filter').hide();
		$('#jdCambiosDataTable_length').hide();
		$('.dataTables_paginate.paging_simple_numbers').hide();

		
		$('#jdCambiosDataTable').on('click','.btnDetallesRenuncia',function(){
			const idRenunciante = $(this).data('id');
			const proceso      = 'detallesRenuncia';
			const datos        = 'idRenunciante='+idRenunciante+'&proceso='+proceso;
			$('#modalDetallesRenuncia').modal({
				backdrop: 'static',
				keyboard: true,
			});
			$('#modalDetallesRenuncia').modal("show");
			$.ajax({
				url: urlProceso,
				type: 'POST',
				data: datos,
				success: function (response) {
					$('#boxDetallesRenuncia').html(response);
				},
				error: function () {
					$('#boxDetallesRenuncia').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">Error al cargar el contenido: <strong>' + error + '</strong></p></div>');
				}
			});
		});
	});
</script>
<?php include($ruta.'template/footer.tpl'); ?>