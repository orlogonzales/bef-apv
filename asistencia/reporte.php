<?php 
	include('template/header.tpl');
	$codigoActividad =$_GET[codigoActividad];
	$tipoActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','tipoActividad');
	$infoActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
	$terminal        =terminal();
	$asistentes      =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistentes');
	$puntuales       =infoActividad($idJuntaDirectiva,$codigoActividad,'','puntuales');
	$tardanza        =infoActividad($idJuntaDirectiva,$codigoActividad,'','tardanza');
	$estado          =infoActividad($idJuntaDirectiva,$codigoActividad,'','estado');
	$hoy             =infoTiempo('fecha');
	$fechaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','fechaActividad');
	$diasEntre       =diasEntre($hoy,$fechaActividad);
	
	$ruta            ="asistencias/";
	if($tipoActividad=="ASA"){ $actividad="ASAMBLEA"; }
	if($tipoActividad=="FAE"){ $actividad="FAENA"; }
	$archivo         ="T".ceros($terminal,2)."-ASISTENCIA-".$actividad."-".infoFecha($fechaActividad,'file').".json";

	if($estado=="ACT"){ $infoEstado='<span class="label label-success">ACTIVO</span>'; }else{ $infoEstado='<span class="label label-warning">INACTIVO</span>';}
	if($diasEntre>0){ $rotulo="FALTAN";  $infoDiasEntre=ceros($diasEntre,2)." DIAS"; }else{ $infoDiasEntre="NINGUNO"; }
	if($diasEntre<0){ $rotulo="REALIZADO"; $infoDiasEntre='<span class="text-danger text-semibold">HACE '.ceros(($diasEntre*-1),2).' DIAS</span>'; }

	if($_SESSION['$codigoActividad'] or $codigoActividad!=""){ $opcion="informe_actividad"; }else{ $opcion="lista_ctividades"; }
?>

<?php if($opcion=="informe_actividad"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// BOTON TRANSFERIR ASISTENCIA DE TERMINAL
			///////////////////////////////////////////////////
			$("a#transferir").click(function(){
				var ruta           ='../';
				var operacion       ='TRANSFERIR_ASISTENCIA';
				var codigoActividad ='<?= $codigoActividad ?>';
				var datos           ='codigoActividad='+codigoActividad+'&operacion='+operacion;
				$.ajax({
					type: "POST",
					url: 'php/mantenimiento-actividad.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('button#menuTransferencias').prop('disabled', true);
						$('#procesando').fadeIn("slow").html('<div class="row mb-15"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>GENERANDO ARCHIVO</div></div>').fadeIn(1000).delay(1000);
					},
					success: function(respuesta){
						if(respuesta.mensaje=='TRANSFERENCIA_FINALIZADA'){
							location.reload();
						}
						if(respuesta.mensaje=='ERROR_GENERAR_ARCHIVO'){
							$.jGrowl('Error al generar archivo.', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
						}
					}
				});
			});

			///////////////////////////////////////////////////
			/// BOTON INICIAR CONTROL DE ASISTENCIA
			///////////////////////////////////////////////////
			$("a#controlAsistencia").click(function(){
				var ruta           ='../';
				var operacion       ='INICIAR_CONTROL_ASISTENCIA';
				var codigoActividad ='<?= $codigoActividad ?>';
				var datos           ='codigoActividad='+codigoActividad+'&operacion='+operacion;
				$.ajax({
					type: "POST",
					url: 'php/mantenimiento-actividad.php',
					data: datos,
					dataType:'json',
					success: function(respuesta){
						if(respuesta.mensaje=='INCIAR_CONTROL'){
							window.location.replace("asistencia.php");
						}
						if(respuesta.mensaje=='ACTIVIDAD_INACTIVA'){
							window.location.replace("reporte.php?codigoActividad="+codigoActividad+"&modulo=controlAsistencia");
						}
					}
				});
			});

			///////////////////////////////////////////////////
			/// BOTON VOLVER A SUBIR ARCHIVO
			///////////////////////////////////////////////////
			$("button#volver_subir_json").click(function(){
				$('#subir').fadeIn(600);
				$('#procesar').hide();
			});

			///////////////////////////////////////////////////
			/// ACCIONES AL ABRIR MODAL DE ASISNTES
			///////////////////////////////////////////////////
			$('#listaAsistentes').on('click', function() {
				$('#listaAsistencia').on('shown.bs.modal', function() {
					var codigoActividad='<?= $codigoActividad ?>';
					var opcion='LISTA_ASISTENTES';
					var datos='codigoActividad='+codigoActividad+'&opcion='+opcion;
					$('#moduloAsistentes').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
					$("#moduloAsistentes").fadeIn("slow").load('modulo/actividad.php?'+datos).fadeIn(1000).delay(1000);
				});
			});

			///////////////////////////////////////////////////
			/// ACCIONES AL ABRIR MODAL DE MODIFICAR HORA
			///////////////////////////////////////////////////
			$('#horaActividad').on('click', function() {
				$('#modificarInicio').on('shown.bs.modal', function() {
					var codigoActividad='<?= $codigoActividad ?>';
					var opcion='CAMBIO_HORA';
					var datos='codigoActividad='+codigoActividad+'&opcion='+opcion;
					$('#moduloHora').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
					$("#moduloHora").fadeIn("slow").load('modulo/actividad.php?'+datos).fadeIn(1000).delay(1000);
				});
			});
		});
	</script>

	<div class="row">
		<div class="col-lg-6 col-lg-offset-3">
			<div class="panel panel-body border-top-xlg border-top-brown text-center">
				<h6 class="no-margin text-brown"><mark class="infoTexto bg-brown">&nbsp;&nbsp;&raquo;&nbsp;TERMINAL - <?= ceros($terminal,2) ?>&nbsp;&laquo;&nbsp;&nbsp;</mark><br><strong><?= $infoActividad ?></strong><br><small class="text-bold text-brown-300 textoMayuscula"><?= $infofechaACT ?></small></h6>
				<hr>

				<div class="text-center mb-20 textoMayuscula">

					<div class="btn-group">
						<button type="button" class="btn btn-danger btn-labeled dropdown-toggle" data-toggle="dropdown"><b><i class="icon-menu7"></i></b> <?= $actividad ?> <span class="caret"></span></button>
						<ul class="dropdown-menu dropdown-menu-right">
							<?php if($estadoActividad!="INC"){ if($diasEntre<0){}else{ ?>
								<li><a href="javascript:;" id="controlAsistencia"><i class=" icon-hour-glass"></i> Controlar asistencia</a></li>
								<li><a href="#modificarInicio" data-toggle="modal" id="horaActividad"><i class="icon-pencil5"></i> Modificar hora inicio</a></li>
							<?php }} ?>
							<?php if($asistentes>0){ ?><li><a href="#listaAsistencia" data-toggle="modal" id="listaAsistentes"><i class="icon-users"></i> Lista de asistentes</a></li><?php } ?>
						</ul>
					</div>

					<div class="btn-group">
						<button type="button" id="menuTransferencias" class="btn bg-success btn-labeled dropdown-toggle" data-toggle="dropdown"><b><i class="icon-menu7"></i></b> TRANSFERIR <?= $actividad ?> <span class="caret"></span></button>
						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="javasript:;" id="transferir"><i class="icon-transmission"></i> Finalizar <?= $actividad ?> y Transferir datos de terminal</a></li>
							<?php if (file_exists($ruta.$archivo)){ ?><li><a href="<?= $ruta.$archivo ?>" download="<?= $archivo ?>"><i class="icon-download"></i> Descargar asistencia de terminal</a></li><?php } ?>
						</ul>
					</div>
				</div>

				<div id="procesando"></div>
				
				<div class="table-responsive">
					<table class="table table-bordered table-framed">
						<thead>
							<tr class="success">
								<th class="text-center text-bold">ASISTENTES</th>
								<th class="text-center text-bold">PUNTUALES</th>
								<th class="text-center text-bold">TARDANZAS</th>
								<th class="text-center text-bold">ESTADO</th>
								<th class="text-center text-bold"><?= $rotulo ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-center"><?= numero($asistentes) ?> SOCIOS</td>
								<td class="text-center"><?= numero($puntuales) ?> SOCIOS</td>
								<td class="text-center"><?= numero($tardanza) ?> SOCIOS</td>
								<td class="text-center"><?= $infoEstado ?></td>
								<td class="text-center"><?= $infoDiasEntre ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div id="modificarInicio" class="modal fade">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header bg-brown">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h6 class="modal-title"><?= $actividad ?> &rarr; HORA DE INICIO</h6>
				</div>
				<div class="modal-body">
					<div id="moduloHora"></div>
					<div id="cerrarVentana">
						<div class="modal-footer text-center">
							<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<?php if($asistentes>0){ ?>
		<div id="listaAsistencia" class="modal fade">
			<div class="modal-dialog modal-full">
				<div class="modal-content">
					<div class="modal-header bg-brown">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h6 class="modal-title"><?= $actividad ?> &rarr; LISTA DE ASISTENTES</h6>
					</div>
					<div class="modal-body">
						<div id="moduloAsistentes"></div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
<?php } ?>

<?php if($opcion=="lista_ctividades"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CAMBIO DE ESTILO DE INPUT FILE
			///////////////////////////////////////////////////
			$('.seleccionar').select2();
		});
	</script>
	<div class="row">
		<div class="col-lg-10 col-lg-offset-1">
			<div class="panel panel-body border-top-xlg border-top-brown text-center">
				<h6 class="no-margin text-brown text-bold">SELECCIONE ASAMBLEA O FAENA</h6>
				<div class="mt-15">
					<form action="reporte.php" method="GET">
						<fieldset class="content-group">
							<div class="form-group">
								<div class="col-lg-2">
									<div class="form-control input-lg text-danger text-bold">TERMINAL <?= ceros($terminal,2) ?></div>
								</div>
								<div class="col-lg-8">
									<select name="codigoActividad" class="seleccionar select-lg">
										<option value="" selected>SELECCIONE ACTIVIDAD</option>
										<?php
											$conexion=conexionDB();
											$sql="SELECT codigoActividad, temaActividad, fechaActividad FROM sm_terminal_actividades ORDER BY fechaActividad DESC";
											$rs=mysqli_query($conexion,$sql);
											while($datos=mysqli_fetch_array($rs)){
												echo '<option value="'.$datos[0].'">'.infoFecha($datos[2],'normal').' &rarr; '.$datos[1].'</option>';
											}
										?>
									</select>
								</div>
								<div class="col-lg-2">
									<input type="hidden" name="modulo" value="controlAsistencia">
									<button type="submit" class="btn btn-lg btn-block bg-brown">SELECCIONAR</button>
								</div>
							</div>
						</fieldset>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php include('template/footer.tpl') ?>
				