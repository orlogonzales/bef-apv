<?php
	include '../php/funciones.php';
	$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
	$tipoActividad    = $_POST['tipoActividad'];
	$codigoActividad  = $_POST['codigoActividad'];
	$codigoCuenta     = $_POST['codigoCuenta'];
	$fechaInicio      = fechaSQL($_POST['fechaInicio']);
	$fechaFin         = fechaSQL($_POST['fechaFin']);

	if($tipoActividad=='FAE'){
		$rotulo="FAENA";
	}else if($tipoActividad=='ASA'){
		$rotulo="ASAMBLEA";
	}else if($tipoActividad=='ALL'){
		$rotulo="TODOS";
	}else{
		$rotulo="ERROR";
	}

	//echo '<pre>'; var_dump($_POST); echo '</pre>';
?>
<div class="panel">
	<div class="panel-heading bg-teal">
		<h6 class="panel-title textoNegrita">RESULTADOS DE CONSULTA</h6>
		<div class="pull-right" style="margin: -20px 0px 0px 0px !important;">
			<a href="../documentos/reporte-jd-actividades.php?idJuntaDirectiva=<?= $idJuntaDirectiva ?>&tipoActividad=<?= $tipoActividad ?>&codigoActividad=<?= $codigoActividad ?>&codigoCuenta=<?= $codigoCuenta ?>&fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>" class="btn btn-xs btn-dark">IMPRIMIR CONSULTA</a>
		</div>
	</div>
	<div class="panel-body info">
		<table id="dataTableJDActividades" class="table table-bordered table-hover table-striped table-td-valign-middle mb-15">
			<thead class="bg-dark">
				<tr>
					<th class="text-center">#</th>
					<th class="text-left">CODIGO</th>
					<th class="text-left">TIPO</th>
					<th class="text-left">ACTIVIDAD</th>
					<th class="text-left">FECHA</th>
					<th class="text-left">SOCIO</th>
					<th class="text-center">LTS</th>
					<th class="text-left">ASISITIO</th>
					<th class="text-left">MULTA</th>
					<th class="text-center">PAGO</th>
					<th class="text-center">CAJA</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>

		<div id="infoEstadoFinanciero">
			<div class="row">
				<div class="col-md-8"></div>
				<div class="col-md-4">
					<table class="table table-bordered table-striped">
						<tbody>
							<tr>
								<td><strong>TOTAL MULTAS</strong></td>
								<td class="text-right"><span id="info_total_multas">S/D</span></td>
							</tr>
							<tr>
								<td><strong>TOTAL PAGADOS</strong></td>
								<td class="text-right"><span id="info_total_pagos">S/D</span></td>
							</tr>
							<tr>
								<td><strong>TOTAL POR PAGAR</strong></td>
								<td class="text-right"><span id="info_total_deuda">S/D</span></td>
							</tr>
						</tbody>
					</table>
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

<script type="text/javascript">
	$(document).ready(function(){
		////////////////////////////////////////////////////////////
		/// VARIABLES
		////////////////////////////////////////////////////////////
		const urlDT            = '../modulo/dt-actividades-jd.php';
		const urlProceso       = '../modulo/modulo-jd-registro.php';
		const urlMantenimiento = '../php/mantenimiento-junta-directiva.php';
		$('#infoEstadoFinanciero').hide();

		////////////////////////////////////////////////////////////
		/// DATA TABLE - JUNTA DIRECTIVA
		////////////////////////////////////////////////////////////
		const dataTableJDActividades = $('#dataTableJDActividades').DataTable({
			"paging"       : true,
			"pageLength"   : 15,
			"lengthChange" : true,
			"info"         : true,
			"bPaginate"    : true,
			"bSort"        : false,
			"processing"   : true,
			'serverMethod' : 'POST',
			"ajax": {
		        url: urlDT,
		        type: 'POST',
		        data: function(d) {
		            d.idJuntaDirectiva = "<?php echo $idJuntaDirectiva ?>";
		            d.tipoActividad    = "<?php echo $tipoActividad ?>";
		            d.codigoActividad  = "<?php echo $codigoActividad ?>";
		            d.codigoCuenta     = "<?php echo $codigoCuenta ?>";
		            d.fechaInicio      = "<?php echo $fechaInicio ?>";
		            d.fechaFin         = "<?php echo $fechaFin ?>";
		        },
		        error: function(xhr, error, code) {
		            console.error('Error en la carga de datos:', xhr.responseText);
		        },
		        dataSrc: function (json) {
					montoMultas = json.montoMultas;
					totalMultas = json.totalMultas;
					montosPagados = json.montosPagados;
					montosPorCobrar = json.montosPorCobrar;

					if(montoMultas>0){
						$('#infoEstadoFinanciero').show();
						$('#info_total_multas').text(totalMultas);
						$('#info_total_deuda').text(montosPorCobrar);
						$('#info_total_pagos').text(montosPagados);
					}

		            return json.data;
		        }
		    },
			"columns"      : [
				{ data: 'Nro' },
				{ data: 'codigoActividad' },
				{ data: 'infoTipoActividad' },
				{ data: 'nombreActividad' },
				{ data: 'fechaActividad' },
				{ data: 'nombreSocio' },
				{ data: 'lotesSocio' },
				{ data: 'asistenciActividad' },
				{ data: 'infoMulta' },
				{ data: 'infoPago' },
				{ data: 'infoCaja' },
			],
			"columnDefs": [
				{ "targets": 0,  "className": "align-middle text-center" },
				{ "targets": 1,  "className": "align-middle text-center" },
				{ "targets": 2,  "className": "align-middle text-center" },
				{ "targets": 3,  "className": "align-middle text-left text-strong" },
				{ "targets": 4,  "className": "align-middle text-center text-uppercase" },
				{ "targets": 5,  "className": "align-middle text-left text-strong text-uppercase" },
				{ "targets": 6,  "className": "align-middle text-center" },
				{ "targets": 7,  "className": "align-middle text-center" },
				{ "targets": 8,  "className": "align-middle text-center text-uppercase" },
				{ "targets": 9,  "className": "align-middle text-center" },
				{ "targets": 10, "className": "align-middle text-center" },
			],
		});

		dataTableJDActividades.on('draw', function() {
	        $('[data-toggle="tooltip"]').tooltip();
	    });

		$('#dataTableJDActividades').on('click','.btnVerCuentasJD',function(){
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
	});
</script>