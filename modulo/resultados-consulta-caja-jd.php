<?php
	include '../php/funciones.php';

	$idJuntaDirectiva = $_POST['idJuntaDirectiva'] ?? '';
	$movimiento       = $_POST['movimiento'] ?? '';
	$tipoActividad    = $_POST['tipoActividad'] ?? '';
	$codigoConcepto   = $_POST['codigoConcepto'] ?? '';
	$codigoCuenta     = $_POST['codigoCuenta'] ?? '';
	$fechaInicio      = isset($_POST['fechaInicio']) ? fechaSQL($_POST['fechaInicio']) : '';
	$fechaFin         = isset($_POST['fechaFin']) ? fechaSQL($_POST['fechaFin']) : '';
?>

<div class="panel">
	<div class="panel-heading bg-teal">
		<h6 class="panel-title textoNegrita">RESULTADOS DE CONSULTA</h6>
		<div class="pull-right" style="margin: -20px 0px 0px 0px !important;">
			<a href="../documentos/reporte-jd-caja.php?idJuntaDirectiva=<?= urlencode($idJuntaDirectiva) ?>&movimiento=<?= urlencode($movimiento) ?>&tipoActividad=<?= urlencode($tipoActividad) ?>&codigoConcepto=<?= urlencode($codigoConcepto) ?>&codigoCuenta=<?= urlencode($codigoCuenta) ?>&fechaInicio=<?= urlencode($fechaInicio) ?>&fechaFin=<?= urlencode($fechaFin) ?>" class="btn btn-xs btn-dark">IMPRIMIR CONSULTA</a>
		</div>
	</div>
	<div class="panel-body info">
		<table id="dataTableJDCaja" class="table table-bordered table-hover table-striped table-td-valign-middle mb-15">
			<thead class="bg-dark">
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">TIPO</th>
					<th class="text-left">CONCEPTO</th>
					<th class="text-center">MOV</th>
					<th class="text-center">F. OPERACION</th>
					<th class="text-center">CODIGO SOCIO</th>
					<th class="text-left">SOCIO</th>
					<th class="text-center">LTS</th>
					<th class="text-right">MONTO</th>
					<th class="text-center">REGISTRO</th>
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
								<td><strong>TOTAL CONSULTA</strong></td>
								<td class="text-right"><span id="info_total_consulta">S/D</span></td>
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
		const urlDT = '../modulo/dt-caja-jd.php';

		const dataTableJDCaja = $('#dataTableJDCaja').DataTable({
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
		            movimiento: "<?= htmlspecialchars($movimiento) ?>",
		            tipoActividad: "<?= htmlspecialchars($tipoActividad) ?>",
		            codigoConcepto: "<?= htmlspecialchars($codigoConcepto) ?>",
		            codigoCuenta: "<?= htmlspecialchars($codigoCuenta) ?>",
		            fechaInicio: "<?= htmlspecialchars($fechaInicio) ?>",
		            fechaFin: "<?= htmlspecialchars($fechaFin) ?>"
		        },
		        error: function(xhr, error, code) {
		            console.error('Error en la carga de datos:', xhr.responseText);
		        },
		        dataSrc: function (json) {
					if (json.totalConsulta > 0) {
						$('#infoEstadoFinanciero').show();
						$('#info_total_consulta').text(json.infoTotalConsulta);
					}
		            return json.data;
		        }
		    },
			"columns": [
				{ data: 'Nro', className: "align-middle text-center" },
				{ data: 'tipoOperacion', className: "align-middle text-center" },
				{ data: 'conceptoMovimiento', className: "align-middle text-left" },
				{ data: 'tipoMovimiento', className: "align-middle text-center" },
				{ data: 'fechaOperacion', className: "align-middle text-center text-strong" },
				{ data: 'codigoSocio', className: "align-middle text-center" },
				{ data: 'nombreSocio', className: "align-middle text-left text-strong" },
				{ data: 'lotesSocio', className: "align-middle text-center text-uppercase" },
				{ data: 'montoCaja', className: "align-middle text-right text-strong" },
				{ data: 'infoRegistro', className: "align-middle text-center" },
			],
		});
	});
</script>