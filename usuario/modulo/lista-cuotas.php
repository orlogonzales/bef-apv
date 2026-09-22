<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$conexion       =conexionDB();
	$cantidadCuotas =infoCuota($idJuntaDirectiva,$codigoCuota,'cantidadCuotas');

	if($cantidadCuotas>0){
?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CONFIGURACION DE TABLAS
			///////////////////////////////////////////////////
			$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 4, 5, 6, 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
			var lastIdx = null;
			var table = $('.tabla-listaSocios').DataTable({ 'pageLength': 20 });
			$('.tabla-listaSocios tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
			$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
			$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

			///////////////////////////////////////////////////
			/// ELIMINAR ITEM
			///////////////////////////////////////////////////
			$('.eliminar_cuota').on('click', function() {
				var ruta        ='../';
				var codigoCuota =$(this).attr('id');
				var operacion   ='ELIMINAR_CUOTA';
				var datos       = 'codigoCuota='+codigoCuota+'&operacion='+operacion;
				swal({
					title: "Eliminar",
					text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#EF5350",
					confirmButtonText: "Si, Eliminar",
					cancelButtonText: "No, Cancelar",
					closeOnConfirm: true,
					closeOnCancel: true
				},
				function(isConfirm){
					if (isConfirm) { 
						swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
						$.ajax({
							type: "POST",
							url: ruta+'php/mantenimiento-cuotas.php',
							data: datos,
							dataType:'json',
							beforeSend: function(){
								$('.info').fadeOut("slow");
								$('#procesando').fadeIn("slow").html('<div class="pull-right procesando"><i class="icon-spinner3 spinner"></i> Eliminando cuota de socios...&nbsp;&nbsp;</div>');
							},
							success: function(respuesta){
								if(respuesta.mensaje=="CUOTA_ELIMINADA"){
									new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
									location.reload();
								}
							}
						});
					}
				});
			});

			///////////////////////////////////////////////////
			/// ELIMINAR ITEM POR MERGENCIA
			///////////////////////////////////////////////////
			$('.eliminar_cuota_emergencia').on('click', function() {
				var ruta        ='../';
				var codigoCuota =$(this).attr('id');
				var operacion   ='ELIMINAR_CUOTA_EMERGENCIA';
				var datos       = 'codigoCuota='+codigoCuota+'&operacion='+operacion;
				swal({
					title: "Eliminar",
					text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#EF5350",
					confirmButtonText: "Si, Eliminar",
					cancelButtonText: "No, Cancelar",
					closeOnConfirm: true,
					closeOnCancel: true
				},
				function(isConfirm){
					if (isConfirm) { 
						swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
						$.ajax({
							type: "POST",
							url: ruta+'php/mantenimiento-cuotas.php',
							data: datos,
							dataType:'json',
							beforeSend: function(){
								$('.info').fadeOut("slow");
								$('#procesando').fadeIn("slow").html('<div class="pull-right procesando"><i class="icon-spinner3 spinner"></i> Eliminando cuota de socios...&nbsp;&nbsp;</div>');
							},
							success: function(respuesta){
								if(respuesta.mensaje=="CUOTA_ELIMINADA"){
									new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
									location.reload();
								}
							}
						});
					}
				});
			});
		});
	</script>

	<div class="panel">
		<div class="panel-heading bg-slate-600">
			<div id="procesando"></div>
			<h6 class="panel-title">LISTA DE CUOTAS</h6>
		</div>
		<div class="panel-body info">
			<table class="table tabla table-bordered table-hover tabla-listaSocios ajustar">
				<thead>
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-left">CONCEPTO DE CUOTA</th>
						<th class="text-left">JUNTA DIRECTIVA</th>
						<th class="text-left">CUENTA PAGO</th>
						<th class="text-left">CUOTA</th>
						<th class="text-left">FECHA PAGO</th>
						<th class="text-center">SOCIOS</th>
						<th class="text-center">PAGARON</th>
						<th class="text-center">DEBEN</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT codigoCuota, codigoCuenta, conceptoCuota, montoCuota, idJuntaDirectiva, fechaPago, codigoCuenta FROM sm_mod_cuotas ORDER BY id DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoCuota      = $n[codigoCuota];
							$conceptoCuota    = utf8_encode($n[conceptoCuota]);
							$montoCuota       = $n[montoCuota];
							$idJuntaDirectiva = $n[idJuntaDirectiva];
							$codigoCuenta     = $n[codigoCuenta];
							$fechaPago        = $n[fechaPago];
							$aforo            = infoCuota($idJuntaDirectiva,$codigoCuota,'aforo');
							$totalPagados     = infoCuota($idJuntaDirectiva,$codigoCuota,'totalPagados');
							$pagaron          = infoCuota($idJuntaDirectiva,$codigoCuota,'pagaron');
							$deben            = infoCuota($idJuntaDirectiva,$codigoCuota,'deben');

							$query = "SELECT sm_junta_directiva.fechaPeriodo, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva.fechaFinPeriodo FROM sm_junta_directiva INNER JOIN sm_junta_directiva_vigencia ON sm_junta_directiva.idVigencia = sm_junta_directiva_vigencia.idVigencia WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
							$row=mysqli_query($conexion,$query);
							$dato=mysqli_fetch_array($row);
							$fechaPeriodo = $dato[fechaPeriodo];
							$vigenciaJunta = $dato[vigenciaJunta];
							$fechaFinPeriodo=$dato[fechaFinPeriodo];

							$query="SELECT CONCAT(sm_socios.nombre, ' ',sm_socios.apPaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
							$row=mysqli_query($conexion,$query);
							$dato=mysqli_fetch_array($row);
							$nombrePresidente=$dato[nombrePresidente];

							$periodoInicio=infoFecha($fechaPeriodo,'year');
							$periodoFin=infoFecha($fechaFinPeriodo,'year');

							$query="SELECT sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_banco_cuentas INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE codigoCuenta = '$codigoCuenta'";
							$row=mysqli_query($conexion,$query);
							$dato=mysqli_fetch_array($row);
							$entidad=$dato[entidad];
							$numeroCuenta=$dato[numeroCuenta];
							$detalle=$dato[detalle];

							if(strlen($nombrePresidente)>0){
								$infoJuntaDirectiva =$nombrePresidente.', '.$periodoInicio.' - '.$periodoFin;
							}else{
								$infoJuntaDirectiva="";
							}
							
							if(strlen($idJuntaDirectiva)>0){
								$infoCuentaJuntaDirectiva=$entidad." | ".$numeroCuenta;
							}else{
								$infoCuentaJuntaDirectiva='';
							}

							if($totalPagados>0){
								$boton='';
							}else{
								$totalPagados=infoPagoFechas('',$codigoCuota,'totalPagadosConcepto');
								$boton='<li><a id="'.$codigoCuota.'" class="eliminar_cuota"><i class="icon-trash"></i> Eliminar cuota</a></li>';
							}

							if($rolUsuario=='ADM' || $rolUsuario=='JDP'){
								$emergencia='<li><a id="'.$codigoCuota.'" class="eliminar_cuota_emergencia"><i class="icon-trash"></i> Eliminar cuota por emergencia</a></li>';
							}
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-left"><?= $conceptoCuota ?></td>
						<td class="text-left"><?= $infoJuntaDirectiva ?></td>
						<td class="text-left"><?= $infoCuentaJuntaDirectiva ?></td>
						<td class="text-right text-danger textoNegrita">S/. <?= moneda($montoCuota) ?></td>
						<td class="text-left textoMayuscula"><?= infoFecha($fechaPago,'normal') ?></td>
						<td class="text-center"><span class="label bg-brown textoNegrita"><?= ceros($aforo,4) ?> SOCIOS</span></td>
						<td class="text-center"><span class="label bg-success textoNegrita"><?= ceros($pagaron,4) ?> SOCIOS</span></td>
						<td class="text-center"><span class="label bg-warning textoNegrita"><?= ceros($deben,4) ?> SOCIOS</span></td>
						<td class="text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-icon btn-xs bg-brown dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=detalles&conceptoCuota=<?= texto($conceptoCuota) ?>"><i class="icon-plus-circle2"></i> Ver detalles de cuota</a></li>
									<?= $boton ?>
									<?= $emergencia ?>
								</ul>
							</div>
						</td>
					</tr>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>
	</div>
<?php
	}else{
		echo cajaAlerta('SIN CUOTAS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN CONCEPTO DE CUOTA...','text-center','bg-warning-300');
	}
?>