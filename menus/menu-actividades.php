<?php
	if($opcion=="detalles"){ $detalles='class="active"'; }
	if($opcion=="asistentes"){ $asistentes='class="active"'; }
	if($opcion=="tardanzas"){ $tardanzas='class="active"'; }
	if($opcion=="justificaciones"){ $justificaciones='class="active"'; }
	if($opcion=="inasistentes"){ $inasistentes='class="active"'; }
	if($opcion=="procesarAsistencia"){ $procesarAsistencia='class="active"'; }
	if($opcion=="operaciones"){ $operaciones='class="active"'; }
	
	$asistencia      =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
	$asistio         =infoActividad($idJuntaDirectiva,$codigoActividad,'','asistio');
	$tarde           =infoActividad($idJuntaDirectiva,$codigoActividad,'','tarde');
	$justifico       =infoActividad($idJuntaDirectiva,$codigoActividad,'','justifico');
	$falto           =infoActividad($idJuntaDirectiva,$codigoActividad,'','falto');
	$archivoGenerado =infoActividad($idJuntaDirectiva,$codigoActividad,'','archivoACT');
	$subidosServer   =infoTerminal($codigoActividad,'','','subidosServer');

	if($asistio>0){ $asistio='<span class="badge badge-success badge-inline position-right">'.$asistio.'</span>'; }
	if($tarde>0){ $tarde='<span class="badge badge-success badge-inline position-right">'.$tarde.'</span>'; }
	if($justifico>0){ $justifico='<span class="badge badge-success badge-inline position-right">'.$justifico.'</span>'; }
	if($falto>0){ $falto='<span class="badge badge-success badge-inline position-right">'.$falto.'</span>'; }
	if($archivoGenerado==""){ $archivoACT="NO_EXISTE"; }else{ $archivoACT="EXISTE"; }

	$sql="SELECT formaActividad FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'";
	$row = mysqli_query($conexion,$sql);
	$dato = mysqli_fetch_array($row);
	$formaActividad = $dato[formaActividad];
?>
<div class="navbar navbar-default navbar-xs">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="icon-menu7"></i></a></li>
	</ul>

	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav element-active-slate-400 textoMayuscula">
			<li <?= $detalles ?>><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=detalles&temaActividad=<?= $temaActividad?>">Detalles</a></li>
			<?php if($formaActividad==0){ ?>
				<?php if($permitido=="SI"){ ?><?php if($subidosServer>0){ }else{ ?><li><a href="#generaAsistencia" data-toggle="modal">Transferir <?= $rotActMin ?></a></li><?php } ?><?php } ?>
			<?php } ?>
			<?php if($permitido=="SI"){ ?><?php if($archivoACT=="EXISTE" AND $asistencia==0){ ?><li <?= $procesarAsistencia ?>><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=procesarAsistencia&temaActividad=<?= $temaActividad?>">Procesar Asistencia</a></li><?php } ?><?php } ?>
			<?php if($asistencia>0){ ?>
				<li <?= $asistentes ?>><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=asistentes&temaActividad=<?= $temaActividad?>">Asistentes</a></li>
				<li <?= $tardanzas ?>><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=tardanzas&temaActividad=<?= $temaActividad?>">Tardanzas</a></li>
				<li <?= $justificaciones ?>><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=justificaciones&temaActividad=<?= $temaActividad?>">Justificaciones</a></li>
				<li <?= $inasistentes ?>><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=inasistentes&temaActividad=<?= $temaActividad?>">Inasistentes</a></li>
				<?php if($permitido=="SI"){ ?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">Reportes <span class="caret"></span></a>
						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="../documentos/reporte-actividades.php?tipoActividad=<?= $tipoActividad ?>&codigoActividad=<?= $codigoActividad ?>&opcion=reporte_socios">Reporte de Socios - <?= $rotActMin ?></a></li>
							<li><a href="../documentos/reporte-actividades.php?tipoActividad=<?= $tipoActividad ?>&codigoActividad=<?= $codigoActividad ?>&opcion=reporte_tardanza">Reporte de Socios - con tardanza</a></li>
							<li><a href="../documentos/reporte-actividades.php?tipoActividad=<?= $tipoActividad ?>&codigoActividad=<?= $codigoActividad ?>&opcion=reporte_falta">Reporte de Socios - con falta</a></li>
							<li><a href="../documentos/reporte-actividades.php?tipoActividad=<?= $tipoActividad ?>&codigoActividad=<?= $codigoActividad ?>&opcion=reporte_justificacion">Reporte de Socios - con justificacion</a></li>
						</ul>
					</li>
				<?php } ?>
				<?php if($permitido=="SI"){ ?><li <?= $procesarAsistencia ?>><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=procesarAsistencia&temaActividad=<?= $temaActividad?>">Control</a></li><?php } ?>
				<?php if($permitido=="SI"){ ?><li <?= $operaciones ?>><a href="detalle-actividad.php?codigoActividad=<?= $codigoActividad ?>&tipoActividad=<?= $tipoActividad ?>&opcion=operaciones&temaActividad=<?= $temaActividad?>">Bitacora</a></li><?php } ?>
			<?php } ?>
		</ul>
	</div>
</div>

<?php if($asistencia==0){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// MODULO DE PAGO
			///////////////////////////////////////////////////
			$('#generaAsistencia').on('shown.bs.modal', function(){
				var ruta            ='../';
				var tipoActividad   ='<?= $tipoActividad ?>';
				var codigoActividad ='<?= $codigoActividad ?>';
				var datos           ='tipoActividad='+tipoActividad+'&codigoActividad='+codigoActividad;
				$('#modulo_generar_archivo').fadeIn("slow").html('<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>').fadeIn(1000).delay(1000);
				$("#modulo_generar_archivo").fadeIn("slow").load('modulo/genera-archivo-asistencia.php?'+datos).fadeIn(1000).delay(1000);
			});
			$('#generaAsistencia').on('hidden.bs.modal', function(){
				location.reload();
			});
		});
	</script>
	<div id="generaAsistencia" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-brown">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title">GENERA ASISTENCIA</h5>
				</div>
				<div id="modulo_generar_archivo"></div>
			</div>
		</div>
	</div>
<?php } ?>