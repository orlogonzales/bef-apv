<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$conexion      =conexionDB();
	$tipoActividad =$_GET['tipoActividad'];

	if($tipoActividad=="ASA"){
		$titulotabla="LISTA DE ASAMBLEAS";
		$descActividad="asamblea";
		$cantidadASAFAE =infoActividad($idJuntaDirectiva,$codigoActividad,'','cantidadASA');
		$mensaje=cajaAlerta('SIN ASAMBLEAS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN CONCEPTO DE ASAMBLEA...','text-center','bg-warning-300');
	}
	if($tipoActividad=="FAE"){
		$titulotabla="LISTA DE FAENAS";
		$descActividad="faena";
		$cantidadASAFAE =infoActividad($idJuntaDirectiva,$codigoActividad,'','cantidadFAE');
		$mensaje=cajaAlerta('SIN FAENAS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN CONCEPTO DE FAENA...','text-center','bg-warning-300');
	}
	
	if($cantidadASAFAE>0){
?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CONFIGURACION DE TABLAS
			///////////////////////////////////////////////////
			$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 3, 4, 5, 6 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
			var lastIdx = null;
			var table = $('.tabla-lista-faenas').DataTable({
				"bSort": false,
				'pageLength': 20
			});
			$('.tabla-lista-faenas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
			$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
			$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

			///////////////////////////////////////////////////
			/// ELIMINAR ITEM
			///////////////////////////////////////////////////
			$('.eliminar_actividad').on('click', function() {
				var ruta            ='../';
				var tipoActividad   ='<?= $tipoActividad ?>';
				var codigoActividad =$(this).attr('id');
				var operacion       ='ELIMINAR_ACTIVIDADA';
				var datos           ='tipoActividad='+tipoActividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
				if(tipoActividad=="ASA"){ var concepto="asamblea"; }
				if(tipoActividad=="FAE"){ var concepto="faena"; }
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
							url: ruta+'php/mantenimiento-actividades.php',
							data: datos,
							dataType:'json',
							beforeSend: function(){
								$('.info').fadeOut("slow");
								$('#procesando').fadeIn("slow").html('<div class="pull-right procesando"><i class="icon-spinner3 spinner"></i> Eliminando '+concepto+' de socios...&nbsp;&nbsp;</div>');
							},
							success: function(respuesta){
								if(respuesta.mensaje=="ACTIVIDAD_ELIMINADA"){
									new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
									location.reload();
								}
							}
						});
					}
				});
			});

			$('.eliminar_actividad_total').on('click', function() {
				var ruta            ='../';
				var tipoActividad   ='<?= $tipoActividad ?>';
				var codigoActividad =$(this).attr('id');
				var operacion       ='ELIMINAR_ACTIVIDADA';
				var datos           ='tipoActividad='+tipoActividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
				if(tipoActividad=="ASA"){ var concepto="asamblea"; }
				if(tipoActividad=="FAE"){ var concepto="faena"; }
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
							url: ruta+'php/mantenimiento-actividades.php',
							data: datos,
							dataType:'json',
							beforeSend: function(){
								$('.info').fadeOut("slow");
								$('#procesando').fadeIn("slow").html('<div class="pull-right procesando"><i class="icon-spinner3 spinner"></i> Eliminando '+concepto+' de socios...&nbsp;&nbsp;</div>');
							},
							success: function(respuesta){
								if(respuesta.mensaje=="ACTIVIDAD_ELIMINADA"){
									new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
									location.reload();
								}
							}
						});
					}
				});
			});

			///////////////////////////////////////////////////
			/// CONTROL DE ASISTENCIA MANUAL
			///////////////////////////////////////////////////			
			$('.btn_control_asistencia').on('click', function() {
				var codigoActividad = $(this).attr('id');
				var proceso = 'CONTROL_ASISTENCIA';
				var datos = 'codigoActividad=' + codigoActividad + '&proceso=' + proceso;
				console.log("Código de actividad: " + codigoActividad);
				$('#myModal').data('ajaxData', datos).modal('show');
			});

			$('#myModal').on('show.bs.modal', function () {
			    var modal = $(this);
			    var datos = modal.data('ajaxData');
			    modal.find('#modalContent').html('<p>Cargando contenido...</p>');
			    $.ajax({
			        url: '../modulo/control-asistencia.php',
			        method: 'POST',
			        data: datos,
			        success: function(response) {
			            modal.find('#modalContent').html(response);
			        },
			        error: function() {
			            modal.find('#modalContent').html('<p class="text-danger">Error al cargar el contenido.</p>');
			        }
			    });
			});
		});
	</script>

	<div class="panel">
		<div class="panel-heading bg-slate-600">
			<div id="procesando"></div>
			<h6 class="panel-title"><?= $titulotabla ?></h6>
		</div>
		<div class="panel-body info">
			<table class="table tabla table-bordered table-hover tabla-lista-faenas ajustar">
				<thead>
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-left"><?= $titulotabla ?></th>
						<th class="text-left">JUNTA DIRECTIVA</th>
						<th class="text-left">CUENTA PAGO</th>
						<th class="text-center">FECHA Y HORA</th>
						<th class="text-center">REG</th>
						<th class="text-center">CON</th>
						<th class="text-center">SOCIOS</th>
						<th class="text-center">FUERON</th>
						<th class="text-center">FALTAS</th>
						<th class="text-right">PENALIDAD</th>
						<th class="text-center">FECHA</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT idJuntaDirectiva, codigoCuenta, tipoActividad, codigoActividad, temaActividad, formaActividad, formaControl, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta, mPenalidad, fechaInicioPenalidad FROM sm_mod_actividades WHERE tipoActividad = '$tipoActividad' ORDER BY id DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoActividad      = $n['codigoActividad'];
							$tipoActividad        = $n['tipoActividad'];
							$temaActividad        = $n['temaActividad'];
							$formaActividad       = $n['formaActividad'];
							$formaControl         = $n['formaControl'];
							$fechaActividad       = $n['fechaActividad'];
							$horaActividad        = $n['horaActividad'];
							$lugarActividad       = $n['lugarActividad'];
							$idJuntaDirectiva     = $n['idJuntaDirectiva'];
							$codigoCuenta         = $n['codigoCuenta'];
							$mPenalidad           = $n['mPenalidad'];
							$fechaInicioPenalidad = $n['fechaInicioPenalidad'];

							$query = "SELECT sm_junta_directiva.fechaPeriodo, sm_junta_directiva_vigencia.vigenciaJunta, sm_junta_directiva.fechaFinPeriodo FROM sm_junta_directiva INNER JOIN sm_junta_directiva_vigencia ON sm_junta_directiva.idVigencia = sm_junta_directiva_vigencia.idVigencia WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
							$row=mysqli_query($conexion,$query);
							$dato=mysqli_fetch_array($row);
							$fechaPeriodo = $dato['fechaPeriodo'];
							$vigenciaJunta = $dato['vigenciaJunta'];
							$fechaFinPeriodo=$dato['fechaFinPeriodo'];

							$query="SELECT CONCAT(sm_socios.nombre, ' ',sm_socios.apPaterno) AS nombrePresidente FROM sm_junta_directiva_integrantes INNER JOIN sm_socios ON sm_junta_directiva_integrantes.codigoSocio = sm_socios.codigoSocio WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND idCargoJunta = '1'";
							$row=mysqli_query($conexion,$query);
							$dato=mysqli_fetch_array($row);
							$nombrePresidente=$dato['nombrePresidente'];

							$periodoInicio=infoFecha($fechaPeriodo,'year');
							$periodoFin=infoFecha($fechaFinPeriodo,'year');

							if(strlen($nombrePresidente)>0){
								$infoJuntaDirectiva =$nombrePresidente.', '.$periodoInicio.' - '.$periodoFin;
							}else{
								$infoJuntaDirectiva="";
							}

							$query="SELECT sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_banco_cuentas INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE codigoCuenta = '$codigoCuenta'";
							$row=mysqli_query($conexion,$query);
							$dato=mysqli_fetch_array($row);
							$entidad=$dato['entidad'];
							$numeroCuenta=$dato['numeroCuenta'];
							$detalle=$dato['detalle'];

							if(strlen($idJuntaDirectiva)>0){
								$infoCuentaJuntaDirectiva=$entidad." | ".$numeroCuenta;
							}else{
								$infoCuentaJuntaDirectiva='';
							}
							
							$aforo           =infoActividad($idJuntaDirectiva,$codigoActividad,'','aforo');
							$asistio         =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
							$falto           =infoActividad($idJuntaDirectiva,$codigoActividad,'','falto');

							if($mPenalidad>0){
								$infoPenalidad ='S/. '.numero($mPenalidad);
							}else{
								$infoPenalidad ="";
							}

							if($fechaInicioPenalidad!='0000-00-00'){
								$infoFechaInicioPenalidad =infoFecha($fechaInicioPenalidad,'resultados');
							}else{
								$infoFechaInicioPenalidad ="";
							}

							if($formaActividad==0){
								$asistencia="JSO";
							}else{
								$asistencia="INM";
							}
							
							if($formaControl==0){
								$control="I/S";
							}else{
								$control="SAL";
							}

							if($asistio>0){
								$boton='';
							}else{
								$boton='<li><a id="'.$codigoActividad.'" class="eliminar_actividad"><i class="icon-trash"></i> Eliminar '.$descActividad.'</a></li>';
							}
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-left"><?= texto($temaActividad) ?></td>
						<td class="text-left"><?= texto($infoJuntaDirectiva) ?></td>
						<td class="text-left"><?= texto($infoCuentaJuntaDirectiva) ?></td>
						<td class="text-center textoMayuscula"><?= infoFecha($fechaActividad,'normal').' - '.infoHora(horaCorta($horaActividad)) ?></td>
						<td class="text-center text-danger"><strong><?= $asistencia ?></strong></td>
						<td class="text-center text-danger"><strong><?= $control ?></strong></td>
						<td class="text-center"><span class="label bg-brown"><?= ceros($aforo,4) ?> SOCIOS</span></td>
						<td class="text-center"><span class="label bg-success"><?= ceros($asistio,4) ?> SOCIOS</span></td>
						<td class="text-center"><span class="label bg-warning"><?= ceros($falto,4) ?> SOCIOS</span></td>
						<td class="text-right"><?= $infoPenalidad ?></td>
						<td class="text-center"><?= $infoFechaInicioPenalidad ?></td>
						<td class="text-center">
							<div class="btn-group">
								<button type="button" class="btn btn-icon btn-xs bg-brown dropdown-toggle" data-toggle="dropdown"><i class="icon-menu7"></i></button>
								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=detalles&temaActividad=<?= $temaActividad ?>"><i class="icon-plus-circle2"></i> Ver detalles de <?= $descActividad ?></a></li>
									<?php if($rolUsuario=='ADM' || $rolUsuario=='JDP'){ ?>
										<li><a class="btn_control_asistencia" id="<?= $codigoActividad ?>"><i class="icon-plus-circle2"></i> Control de Asistencia</a></li>
									<?php } ?>
									<?= $boton ?>
									<?php 
										if(ROL=='ADM' && $asistio>0){
											echo '<li><a id="'.$codigoActividad.'" class="eliminar_actividad_total"><i class="icon-trash"></i> Forzar Eliminado de '.$descActividad.'</a></li>';
										}
									?>
								</ul>
							</div>
						</td>
					</tr>
					<?php $i++; } cerrarDB() ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header bg-slate-600">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h6 class="modal-title textoNegrita">CONTROL DE ASISTENCIA</h6>
				</div>				
				<div id="modalContent">
					<p>Cargando contenido...</p>
				</div>
			</div>
		</div>
	</div>
<?php
	}else{
		echo $mensaje;
	}
?>