<?php
	include '../php/funciones.php';

	$idJuntaDirectiva = $_POST['idJuntaDirectiva'] ?? '';
	$concepto      = $_POST['concepto'] ?? '';
	$sector       = $_POST['sector'] ?? '';
	$manzana     = $_POST['manzana'] ?? '';
	$ordenar     = $_POST['ordenar'] ?? '';

	if($concepto=='ASA'){
        $infoConcepto='ASAMBLEA';
    }

    if($concepto=='FAE'){
        $infoConcepto='FAENA';
    }

    if($concepto=='CUO'){
        $infoConcepto='CUOTA';
    }

    if($concepto=='ALL'){
        $infoConcepto='TODOS';
    }

	if($sector=='ALL'){
		$infoSector='TODOS';
	}else{
		$infoSector=$sector;
	}

	if($manzana=='ALL'){
		$infoManzana='TODOS';
	}else{
		$infoManzana=$manzana;
	}

	if($ordenar=='LOTE'){
		$infoOrden='POR LOTE';
	}else if($ordenar=='NOMBRE'){
		$infoOrden='POR NOMBRE';
	}else{
		$infoOrden='POR APELLIDOS';
	}
?>
<div class="panel">
	<div class="panel-heading bg-teal">
		<h6 class="panel-title textoNegrita">RESULTADOS DE CONSULTA</h6>
		<div class="pull-right" id="btn_reporte" style="margin: -20px 0px 0px 0px !important;">
			<a href="../documentos/reporte-jd-deudas.php?idJuntaDirectiva=<?= urlencode($idJuntaDirectiva) ?>&concepto=<?= urlencode($concepto) ?>&sector=<?= urlencode($sector) ?>&manzana=<?= urlencode($manzana) ?>&ordenar=<?= urlencode($ordenar) ?>" 
			target="_blank" class="btn btn-xs btn-dark">IMPRIMIR CONSULTA</a>
		</div>
	</div>
	<div class="panel-body info">
		<div class="row" style="margin-bottom: 20px;">
			<div class="col-md-3">
				<ul class="list-group">
					<li class="list-group-item font-15 text-danger">CONCEPTO:<span class="pull-right"><strong><?= $infoConcepto ?></strong></span></li>
				</ul>
			</div>
			<div class="col-md-3">
				<ul class="list-group">
					<li class="list-group-item font-15 text-danger">SECTOR:<span class="pull-right"><strong><?= $infoSector ?></strong></span></li>
				</ul>
			</div>
			<div class="col-md-3">
				<ul class="list-group">
					<li class="list-group-item font-15 text-danger">MANZANA:<span class="pull-right"><strong><?= $infoManzana ?></strong></span></li>
				</ul>
			</div>
			<div class="col-md-3">
				<ul class="list-group">
					<li class="list-group-item font-15 text-danger">ORDENADO POR:<span class="pull-right"><strong><?= $infoOrden ?></strong></span></li>
				</ul>
			</div>
		</div>

		<table id="dataTableJDDeudas" class="table table-bordered table-hover table-striped table-td-valign-middle mb-15">
			<thead class="bg-dark">
				<tr>
					<th class="text-center">#</th>
					<th class="text-center">SECTOR</th>
					<th class="text-center">MANZANA</th>
					<th class="text-center">LOTE</th>
					<th class="text-center">CODIGO SOCIO</th>
					<th class="text-left">SOCIO</th>
					<th class="text-center">LTS</th>
					<th class="text-center">CONCEPTO</th>
					<th class="text-right">DEUDA</th>
				</tr>
			</thead>
			<tbody></tbody>
		</table>

		<div id="infoEstadoFinanciero" style="display: none;">
			<div class="row">
				<div class="col-md-8"></div>
				<div class="col-md-4">
					<table class="table table-bordered table-striped font-15">
						<tbody>
							<tr>
								<td><strong>TOTAL DEUDAS</strong></td>
								<td class="text-right"><span id="info_total_deudas">S/D</span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modalInfoDeuda" class="modal fade">
	<div class="modal-dialog modal-full">
		<div class="modal-content">
			<div class="modal-header bg-slate-600">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h6 class="modal-title textoNegrita">INFORMACION DE DEUDAS</h6>
			</div>
			<div class="modal-body">
				<div id="infoDeuda"></div>
			</div>
			 <div class="modal-footer border-top border-secondary pt-20 d-flex" style="border-top: 1px solid #D5D5D5 !important;">
		        <a id="btnImprimirDeuda" href="#" class="btn btn-dark">IMPRIMIR</a>
		        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
		    </div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#btn_reporte').hide();
		const urlDT = '../modulo/dt-deudas-jd.php';
		const dataTableJDDeudas = $('#dataTableJDDeudas').DataTable({
			"paging"       : true,
			"pageLength"   : 10,
			"lengthChange" : true,
			"info"         : true,
			"bPaginate"    : true,
			"bSort"        : false,
			"processing"   : false,
			"language": {
		        "url": "../assets/js/plugins/tables/datatables/spanish.json"
		    },
			'serverMethod' : 'POST',
			"ajax": {
		        url: urlDT,
		        type: 'POST',
		        data: {
		            idJuntaDirectiva: "<?= htmlspecialchars($idJuntaDirectiva) ?>",
		            concepto: "<?= htmlspecialchars($concepto) ?>",
		            sector: "<?= htmlspecialchars($sector) ?>",
		            manzana: "<?= htmlspecialchars($manzana) ?>",
		            ordenar: "<?= htmlspecialchars($ordenar) ?>",
		        },
		        error: function(xhr, error, code) {
		            console.error('Error en la carga de datos:', xhr.responseText);
		        },
		        dataSrc: function (json) {
					if (json.totalDeudas > 0) {
						$('#btn_reporte').show();
						$('#infoEstadoFinanciero').show();
						$('#info_total_deudas').text(json.infoMontoDeudas);
					}
		            return json.data;
		        }
		    },
			"columns": [
				{ data: 'Nro', className: "align-middle text-center" },
				{ data: 'sector', className: "align-middle text-center" },
				{ data: 'manzana', className: "align-middle text-center" },
				{ data: 'lote', className: "align-middle text-center" },
				{ data: 'codigoSocio', className: "align-middle text-center" },
				{ data: 'nombreSocio', className: "align-middle text-left text-strong" },
				{ data: 'lotesSocio', className: "align-middle text-center" },
				{ data: 'concepto', className: "align-middle text-center text-uppercase" },
				{ data: 'montoDeuda', className: "align-middle text-right text-strong" },
			],
		});

		//
		////////////////////////////////////////////////////////////
		/// DATA TABLE - EDITA ITEM
		////////////////////////////////////////////////////////////
		dataTableJDDeudas.on('click','.btn_infoDeuda',function(){
			const codigoSocio   = $(this).data('id');
			const nombreSocio = $(this).data('socio');
			const concepto   = $(this).data('concepto');
			const idJuntaDirectiva = $(this).data('jd');


			$('#modalInfoDeuda').modal({
			  backdrop: 'static',
			  keyboard: false
			});
			$('#modalInfoDeuda').modal('show');
			
			const urlProceso      = '../modulo/deudas-socio.php';
			const loaderModal     = 'CARGANDO';
			const contenedorModal = $('#infoDeuda');
			const proceso         = 'MUESTRA_LISTA_DEUDAS_SOCIO';

			const datos = {
				idJuntaDirectiva : idJuntaDirectiva,
				codigoSocio      : codigoSocio,
				nombreSocio      : nombreSocio,
				concepto         : concepto,
				proceso          : proceso,
			};

			const baseUrl = '../documentos/reporte-deudas-socio.php';
			const queryString = `?idJuntaDirectiva=${encodeURIComponent(idJuntaDirectiva)}&concepto=${encodeURIComponent(concepto)}&codigoSocio=${encodeURIComponent(codigoSocio)}`;
			const fullUrl = baseUrl + queryString;

			// Asignar href al botón
			$('#btnImprimirDeuda').attr('href', fullUrl);

			$.ajax({
				url  : urlProceso,
				type : 'POST',
				data : datos,
				beforeSend: function(){
					$(contenedorModal).slideDown().html(loaderModal);
				},
				success: function (content) {
					$(contenedorModal).html(content);
				},
				error: function (xhr, status, error) {
					console.log(xhr);
					$(contenedorModal).html('<div class="alert alert-danger text-center" role="alert">LO SIENTO, SE ENCONTRO UN ERROR EN MODAL</div>');
				}
			});
		});
	});
</script>