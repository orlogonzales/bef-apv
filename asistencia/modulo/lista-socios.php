<?php
	include('../php/funciones.php');
?>
<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// CONFIGURACION DE DATATABLES
		///////////////////////////////////////////////////
		$('.datatable-basic').DataTable();
		$.extend( $.fn.dataTable.defaults, {autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 3 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Filtro:</span> _INPUT_', lengthMenu: '<span>Mostrar:</span> _MENU_', paginate: { 'Primero': 'Primero', 'Ultimo': 'Ultimo', 'Siguiente': '&rarr;', 'Anterior': '&larr;' } }, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
		$('.dataTables_filter input[type=search]').attr('placeholder','Type to filter...');
		$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
	});
</script>

<div class="table-responsive">
	<table class="table datatable-basic table-bordered table-striped table-hover mb-20">
		<thead>
			<tr class="success">
				<th class="text-bold text-center">#</th>
				<th class="text-bold text-center">CODIGO</th>
				<th class="text-bold text-left">NOMBRE</th>
				<th class="text-bold text-center">LOTES</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$conexion=conexionDB();
			$sql="SELECT codigoSocio, nombre, apPaterno, apMaterno, lotes FROM sm_terminal_socios ORDER BY apPaterno DESC";
			$rs=mysqli_query($conexion,$sql);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				$codigoSocio =$n[codigoSocio];
				$nombre      =utf8_encode($n[nombre]);
				$apPaterno   =utf8_encode($n[apPaterno]);
				$apMaterno   =utf8_encode($n[apMaterno]);
				$lotes       =$n[lotes]; 
				$nombreSocio =$apMaterno.' '.$apMaterno.' '.$nombre;
		?>
			<tr>
				<td class="text-center"><?= ceros($i,3) ?></td>
				<td class="text-center"><?= $codigoSocio ?></td>
				<td class="text-left"><?= $nombreSocio ?></td>
				<td class="text-center"><?= $lotes ?> LOTES</td>
			</tr>
		<?php $i++; } cerrarDB(); ?>
		</tbody>
	</table>
</div>