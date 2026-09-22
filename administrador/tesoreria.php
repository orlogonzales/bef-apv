<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES
	/////////////////////////////////////////////////////////////////////
	$opcion          =isset($_GET['opcion']) ? $_GET['opcion'] : '';
	$ruta            ='../';

	/////////////////////////////////////////////////////////////////////
	/// MENU HEADER PAGINA
	/////////////////////////////////////////////////////////////////////
	$menuTop="menu-tesoreria.php";
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="MODULO DE TESORERIA";
	$menuActual="modTesoreria";
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	include($ruta.'template/header.tpl');
?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// TOOLTIP
			///////////////////////////////////////////////////
			$('[data-popup="tooltip"]').tooltip();
		});
	</script>

	<?php
		if($opcion=="cajaING"){
			include('modulo/info-caja.php');
			include('modulo/flujo-caja.php');
			include($ruta.'formularios/consultas.php');
			include('modulo/caja-ingresos.php');
		}
	?>

	<?php
		if($opcion=="cajaINGCUO"){
			include('modulo/info-caja.php');
			include('modulo/flujo-caja.php');
			include($ruta.'formularios/consultas.php');
			include('modulo/caja-ingresos-cuotas.php');
		}
	?>

	<?php
		if($opcion=="cajaINGASA"){
			include('modulo/info-caja.php');
			include('modulo/flujo-caja.php');
			include($ruta.'formularios/consultas.php');
			include('modulo/caja-ingresos-asambleas.php');
		}
	?>

	<?php
		if($opcion=="cajaINGFAE"){
			include('modulo/info-caja.php');
			include('modulo/flujo-caja.php');
			include($ruta.'formularios/consultas.php');
			include('modulo/caja-ingresos-faenas.php');
		}
	?>

	<?php
		if($opcion=="cajaINGPAR"){
			include('modulo/info-caja.php');
			include('modulo/flujo-caja.php');
			include($ruta.'formularios/consultas.php');
			include('modulo/caja-ingresos-partidas.php');
		}
	?>

	<?php
		if($opcion=="partidas"){
			include('modulo/info-caja.php');
			include($ruta.'formularios/consultas.php');
			include('modulo/modulo-partidas.php');
		}
	?>

	<?php
		if($opcion=="verPartida"){
			include($ruta.'formularios/consultas.php');
			include('modulo/modulo-partidas-detalles.php');
		}
	?>

	<?php
		if($opcion=="cajaSAL"){
			include('modulo/info-caja.php');
			include('modulo/flujo-caja.php');
			include($ruta.'formularios/consultas.php');
			include('modulo/caja-salidas.php');
		}
	?>

	<?php if($opcion=="bancos"){ include('modulo/bancos.php'); } ?>

	<?php if($opcion=="cuentasBancarias"){ include('modulo/cuentas-bancarias.php'); } ?>

	<?php if($opcion=="chequeras"){ include('modulo/chequeras.php'); } ?>

	<?php if($opcion=="emisionCheques"){ include('modulo/emision-cheques.php'); } ?>

	<?php
		if($opcion=="cheques"){
			include($ruta.'formularios/consultas.php');
			include('modulo/cheques-emitidos.php');
		}
	?>

	<?php if($opcion=="conceptoASA"){ include('modulo/concepto-asa.php'); } ?>

	<?php if($opcion=="conceptoFAE"){ include('modulo/concepto-fae.php'); } ?>

	<?php if($opcion=="conceptoCUO"){ include('modulo/concepto-cuo.php'); } ?>

	<?php if($opcion=="operacionesCaja"){ ?>
		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CONFIGURACION DE TABLAS
				///////////////////////////////////////////////////
				$.extend( $.fn.dataTable.defaults, { autoWidth: true, dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Filtro:</span> _INPUT_', lengthMenu: '<span>Mostrar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' } }, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function(){ $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
				$('.lista-procesos').DataTable();
				$('.dataTables_filter input[type=search]').attr('placeholder','Buscar socio...');
				$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
			});
		</script>

		<div class="panel">
			<div class="panel-heading bg-slate-600"><h6 class="panel-title">REGISTRO DE OPERACIONES PROCESADAS</h6></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tabla-info table-bordered table-hover lista-procesos">
						<thead>
							<tr class="success">
								<th class="textoNegrita text-center">#</th>
								<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
								<th class="textoNegrita text-center">MONTO</th>
								<th class="textoNegrita text-center">PROCESO</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="SELECT codigoOperacion, proceso, monto, fecha, hora, usuario FROM sm_procesos_caja ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$codigoOperacion =$n['codigoOperacion'];
									$proceso         =$n['proceso'];
									$monto           =$n['monto'];
									$fecha           =$n['fecha'];
									$hora            =$n['hora'];
									$usuario         =$n['usuario'];
									$infoProceso=registradoPor($usuario,$fecha,$hora,'SI','label-default');
							?>
							<tr>
								<td class="text-center"><?= ceros($i,2) ?></td>
								<td class="text-left"><?= texto($proceso) ?></td>
								<td class="text-right"><span class="label label-danger"><?= 'S/. '.moneda($monto) ?></span></td>
								<td class="text-center textoMayuscula"><?= $infoProceso ?></td>
							</tr>
							<?php $i++; } cerrarDB(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if($opcion=="operacionespartidas"){ ?>
		<div class="panel">
			<div class="panel-heading bg-slate-600"><h6 class="panel-title">REGISTRO DE OPERACIONES PROCESADAS</h6></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tabla-info table-bordered table-hover">
						<thead>
							<tr class="success">
								<th class="textoNegrita text-center">#</th>
								<th class="textoNegrita text-left">CODIGO OPERACION</th>
								<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
								<th class="textoNegrita text-left">MONTO</th>
								<th class="textoNegrita text-center">FECHA</th>
								<th class="textoNegrita text-center">HORA</th>
								<th class="textoNegrita text-center">USUARIO</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="SELECT codigoPartida, proceso, monto, fecha, hora, usuario FROM sm_procesos_partidas ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$codigoPartida =$n['codigoPartida'];
									$proceso       =$n['proceso'];
									$monto         =$n['monto'];
									$fecha         =$n['fecha'];
									$hora          =$n['hora'];
									$usuario       =$n['usuario'];
							?>
							<tr>
								<td class="text-center"><?= ceros($i,2) ?></td>
								<td class="text-left"><?= $codigoPartida ?></td>
								<td class="text-left"><?= texto($proceso) ?></td>
								<td class="text-left"><?= 'S/. '.moneda($monto) ?></td>
								<td class="text-center textoMayuscula"><?= infoFecha($fecha,'normal') ?></td>
								<td class="text-center"><?= horaCorta($hora) ?></td>
								<td class="text-center"><?= texto(datoUsuario($usuario,'nombre')) ?></td>
							</tr>
							<?php $i++; } cerrarDB(); ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php include('modulo/modulo-modales-caja.php'); ?>

<?php include($ruta.'template/footer.tpl'); ?>