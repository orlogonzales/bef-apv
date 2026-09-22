<?php
	include '../php/funciones.php';

	$idJuntaDirectiva = $_POST['idJuntaDirectiva'] ?? '';
	$codigoCuota      = $_POST['codigoCuota'] ?? '';
	$estadoPago       = $_POST['estadoPago'] ?? '';
	$codigoCuenta     = $_POST['codigoCuenta'] ?? '';
	$fechaInicio      = isset($_POST['fechaInicio']) ? fechaSQL($_POST['fechaInicio']) : '';
	$fechaFin         = isset($_POST['fechaFin']) ? fechaSQL($_POST['fechaFin']) : '';

?>
<div class="panel">
	<div class="panel-heading bg-teal">
		<h6 class="panel-title textoNegrita">RESULTADOS DE CONSULTA</h6>
		<div class="pull-right" style="margin: -20px 0px 0px 0px !important;">
			<a href="../documentos/reporte-jd-cuotas.php?idJuntaDirectiva=<?= urlencode($idJuntaDirectiva) ?>&codigoCuota=<?= urlencode($codigoCuota) ?>&estadoPago=<?= urlencode($estadoPago) ?>&codigoCuenta=<?= urlencode($codigoCuenta) ?>&fechaInicio=<?= urlencode($fechaInicio) ?>&fechaFin=<?= urlencode($fechaFin) ?>" 
				class="btn btn-xs btn-dark">IMPRIMIR CONSULTA</a>
		</div>
	</div>
	<div class="panel-body info">
		<table id="dataTableJDCuotas" class="table table-bordered table-hover table-striped table-td-valign-middle mb-15">
			<thead class="bg-dark">
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">CODIGO CUOTA</th>
					<th class="text-left">CONCEPTO CUOTA</th>
					<th class="text-center">CODIGO SOCIO</th>
					<th class="text-left">SOCIO</th>
					<th class="text-center">LTS</th>
					<th class="text-right">CUOTA</th>
					<th class="text-center">F. PAGO</th>
					<th class="text-right">PAGO</th>
					<th class="text-center">CAJA</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>

		<div id="infoEstadoFinanciero" style="display: none;">
			<div class="row">
				<div class="col-md-8"></div>
				<div class="col-md-4">
					<table class="table table-bordered table-striped">
						<tbody>
							<tr>
								<td><strong>TOTAL CUOTA</strong></td>
								<td class="text-right"><span id="info_total_cuota">S/D</span></td>
							</tr>
							<tr>
								<td><strong>TOTAL CUOTA PAGADOS</strong></td>
								<td class="text-right"><span id="info_total_cuota_pagos">S/D</span></td>
							</tr>
							<tr>
								<td><strong>TOTAL CUOTA POR PAGAR</strong></td>
								<td class="text-right"><span id="info_total_cuota_deuda">S/D</span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		const urlDT = '../modulo/dt-cuotas-jd.php';

		const dataTableJDCuotas = $('#dataTableJDCuotas').DataTable({
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
		        data: {
		            idJuntaDirectiva: "<?= htmlspecialchars($idJuntaDirectiva) ?>",
		            codigoCuota: "<?= htmlspecialchars($codigoCuota) ?>",
		            estadoPago: "<?= htmlspecialchars($estadoPago) ?>",
		            codigoCuenta: "<?= htmlspecialchars($codigoCuenta) ?>",
		            fechaInicio: "<?= htmlspecialchars($fechaInicio) ?>",
		            fechaFin: "<?= htmlspecialchars($fechaFin) ?>"
		        },
		        error: function(xhr, error, code) {
		            console.error('Error en la carga de datos:', xhr.responseText);
		        },
		        dataSrc: function (json) {
					if (json.montoCuotas > 0) {
						$('#infoEstadoFinanciero').show();
						$('#info_total_cuota').text(json.totalCuotas);
						$('#info_total_cuota_pagos').text(json.montosPagados);
						$('#info_total_cuota_deuda').text(json.montosPorCobrar);
					}
		            return json.data;
		        }
		    },
			"columns": [
				{ data: 'Nro', className: "align-middle text-center" },
				{ data: 'codigoCuota', className: "align-middle text-center" },
				{ data: 'conceptoCuota', className: "align-middle text-center" },
				{ data: 'codigoSocio', className: "align-middle text-center" },
				{ data: 'nombreSocio', className: "align-middle text-left text-strong" },
				{ data: 'lotesSocio', className: "align-middle text-center" },
				{ data: 'montoCuota', className: "align-middle text-right text-strong" },
				{ data: 'fechaPago', className: "align-middle text-center text-uppercase" },
				{ data: 'infoPago', className: "align-middle text-center" },
				{ data: 'infoCaja', className: "align-middle text-center" },
			],
		});
	});
</script>