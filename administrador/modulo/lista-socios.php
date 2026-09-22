<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$sector=$_POST['sector'];
	$manzana=$_POST['manzana'];
	$ordenar=$_POST['ordenar'];

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
	<div class="panel-heading bg-slate-600">
		<h6 class="panel-title textoNegrita">RESULTADOS DE CONSULTA</h6>
	</div>
	<div class="panel-body">
		<div class="row" style="margin-bottom: 20px;">
			<div class="col-md-3">
				<ul class="list-group">
					<li class="list-group-item"><strong>SECTOR:</strong> <span class="pull-right"><?= $infoSector ?></span></li>
				</ul>
			</div>
			<div class="col-md-3">
				<ul class="list-group">
					<li class="list-group-item"><strong>MANZANA:</strong> <span class="pull-right"><?= $infoManzana ?></span></li>
				</ul>
			</div>
			<div class="col-md-3">
				<ul class="list-group">
					<li class="list-group-item"><strong>ORDENADO POR:</strong> <span class="pull-right"><?= $infoOrden ?></span></li>
				</ul>				
			</div>
			<div class="col-md-3">
				<ul class="list-group">
					<li class="list-group-item"><strong>TOTAL METRAJE: <span class="pull-right"><span id="infoMetraje" class="text-danger"></span></span></strong></li>
				</ul>
			</div>
		</div>

		<div class="table-responsive">
			<table id="dtSocios" class="table tabla table-bordered table-hover">
				<thead>
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-left">CODIGO SOCIO</th>
						<th class="text-left">NOMBRE DE SOCIOS</th>
						<th class="text-center">DNI</th>
						<th class="text-left">CELULAR</th>
						<th class="text-center">LOTES</th>
						<th class="text-center">S</th>
						<th class="text-center">M</th>
						<th class="text-center">L</th>
						<th class="text-right">Metraje</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
	</div>
</div>

<div id="modalAgregaObservacion" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-slate-600">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h6 class="modal-title textoNegrita">REGISTRA OBSERVACIONS</h6>
			</div>
			<div class="modal-body">
				<div id="boxModalAgregaObservacion"></div>
				<div id="loader" style="display: none">
					<div class="text-center"><img src="../assets/images/loader.gif"><p>CARGANDO MODULO</p></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modalObservaciones" class="modal fade">
	<div class="modal-dialog modal-full">
		<div class="modal-content">
			<div class="modal-header bg-slate-600">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h6 class="modal-title textoNegrita">OBSERVACIONES DE SOCIO</h6>
			</div>
			<div class="modal-body">
				<div id="boxModalObservaciones"></div>
				<div id="loader" style="display: none">
					<div class="text-center"><img src="../assets/images/loader.gif"><p>CARGANDO MODULO</p></div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		////////////////////////////////////////////////////////////
		/// DATA TABLE - LISTA PERSONAL
		////////////////////////////////////////////////////////////
		const urlDTable = '../modulo/dt-lista-socios.php';
		const dataTable = $('#dtSocios').DataTable({
		    "paging": true,
		    "pageLength": 15,
		    "lengthChange": true,
		    "info": true,
		    "bPaginate": true,
		    "bSort": false,
		    "processing": false,
		    "language": {
		        "url": "../assets/js/plugins/tables/datatables/spanish.json"
		    },
		    "serverMethod": "POST",
		    "ajax": {
		        url: urlDTable,
		        type: 'POST',
		        data: function(d) {
		            d.sector = "<?php echo $sector ?>";
		            d.manzana = "<?php echo $manzana ?>";
		            d.ordenar = "<?php echo $ordenar ?>";
		        },
		        error: function(xhr, error, code) {
		            console.error('Error en la carga de datos:', xhr.responseText);
		        },
		        dataSrc: function (json) {
		            totalMetros = json.totalMetraje;
		            $('#infoMetraje').text(totalMetros+'m²');
		            return json.data;
		        }
		    },
		    "columns": [
		        { data: 'Nro' },
		        { data: 'codigoSocio' },
		        { data: 'nombreSocio' },
		        { data: 'infoDNI' },
		        { data: 'infoCelular' },
		        { data: 'lotesSocio' },
		        { data: 'sector' },
		        { data: 'manzana' },
		        { data: 'lote' },
		        { data: 'metraje' },
		        { data: 'botoneraOpciones' },
		    ],
		    "columnDefs": [
		        { "targets": 0, "className": "text-center" },
		        { "targets": 1, "className": "text-center" },
		        { "targets": 2, "className": "text-left" },
		        { "targets": 3, "className": "text-left" },
		        { "targets": 4, "className": "text-center" },
		        { "targets": 5, "className": "text-center" },
		        { "targets": 6, "className": "text-center" },
		        { "targets": 7, "className": "text-center" },
		        { "targets": 8, "className": "text-center" },
		        { "targets": 9, "className": "text-right" },
		        { "targets": 10, "className": "text-center" },
		    ]
		});

		dataTable.on('draw', function() {
			$('[data-bs-toggle="tooltip"]').tooltip();
		});

		////////////////////////////////////////////////////////////
		/// DATA TABLE - AGREGA OBSERVACIONES
		////////////////////////////////////////////////////////////
		$('#dtSocios').on('click','.btnAgregaNota',function(){
			const loader       = '#loader';
			const codigoSocio  = $(this).data('socio');
			const urlProceso   = '../modulo/modulo-procesos-socios.php';
			const dataTable    = '#dtSocios';
			const ventanaModal = '#modalAgregaObservacion';
			const proceso      = 'AGREGA_NOTA';
			const datos        = 'dataTable='+dataTable+'&ventanaModal='+ventanaModal+'&codigoSocio='+codigoSocio+'&proceso='+proceso;
			
			$(ventanaModal).modal({
		      backdrop: true,
		      keyboard: false
		    });

			$.ajax({
				url: urlProceso,
				type: 'POST',
				data: datos,
				beforeSend: function() {
					$(loader).show();
				},
				success: function (response) {
					$(loader).hide();
					$('#boxModalAgregaObservacion').html(response);
				},
				error: function () {
					$(loader).hide();
					$('#boxModalAgregaObservacion').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">LO SIENTO, OCURRIO UN ERROR AL CARGAR EL MODULO, POR FAVOR INTENTELO NUEVAMENTE.</p></div>');
				}
			});
		});

		////////////////////////////////////////////////////////////
		/// DATA TABLE - LISTA OBSERVACIONES
		////////////////////////////////////////////////////////////
		$('#dtSocios').on('click','.btnListarNotas',function(){
			const loader       = '#loader';
			const codigoSocio  = $(this).data('socio');
			const urlProceso   = '../modulo/modulo-procesos-socios.php';
			const dataTable    = '#dtSocios';
			const ventanaModal = '#modalObservaciones';
			const proceso      = 'LISTA_NOTAS';
			const datos        = 'dataTable='+dataTable+'&ventanaModal='+ventanaModal+'&codigoSocio='+codigoSocio+'&proceso='+proceso;
			
			$(ventanaModal).modal({
		      backdrop: true,
		      keyboard: false
		    });

			$.ajax({
				url: urlProceso,
				type: 'POST',
				data: datos,
				beforeSend: function() {
					$(loader).show();
				},
				success: function (response) {
					$(loader).hide();
					$('#boxModalObservaciones').html(response);
				},
				error: function () {
					$(loader).hide();
					$('#boxModalObservaciones').html('<div class="alert alert-danger" role="alert"><h4 class="alert-heading">¡ERROR!</h4><p class="mb-0">LO SIENTO, OCURRIO UN ERROR AL CARGAR EL MODULO, POR FAVOR INTENTELO NUEVAMENTE.</p></div>');
				}
			});
		});
	});
</script>