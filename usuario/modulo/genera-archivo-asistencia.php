<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$tipoActividad   =$_GET[tipoActividad];
	$codigoActividad =$_GET[codigoActividad];
	$archivoGenerado =infoActividad($idJuntaDirectiva,$codigoActividad,'','archivoACT');
	$temaActividad   =texto(infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad'));
	
	if($tipoActividad=="ASA"){ $rotulo="ASAMBLEA"; $rotuloM="Asamblea"; }
	if($tipoActividad=="FAE"){ $rotulo="FAENA"; $rotuloM="Faena"; }

	$infoActividad=$rotulo." - ".$temaActividad;
	if($archivoGenerado==""){ $archivoACT="NO_EXISTE"; }else{ $archivoACT="EXISTE"; }
?>
<?php if($archivoACT=="NO_EXISTE"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("button#bt_genera_archivo").click(function(){
				var ruta            ='../';
				var tipoActividad   ='<?= $tipoActividad ?>';
				var codigoActividad ='<?= $codigoActividad ?>';
				var operacion       ='GENERA_ARCHIVO_ACTIVIDAD';
				var datos           ='tipoActividad='+tipoActividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-actividades.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('button#bt_genera_archivo').prop('disabled', true);
						$('#generar').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>GENERANDO ARCHIVO</div></div></div>').fadeIn(1000).delay(1000);
					},
					success: function(respuesta){
						console.log(respuesta);
						if(respuesta.mensaje=="ARCHIVO_GENERADO"){
							new PNotify({title: 'CONFIRMACION', text: 'El archivo fue generado, puede empezar a subir a las terminales de asistencia.', addclass: 'bg-success'});
							var datos='tipoActividad='+tipoActividad+'&codigoActividad='+codigoActividad;
							$('#modulo_generar_archivo').fadeIn("slow").html('<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>').fadeIn(1000).delay(1000);
							$("#modulo_generar_archivo").fadeIn("slow").load('modulo/genera-archivo-asistencia.php?'+datos).fadeIn(1000).delay(1000);
						}
					}
				});
			});
		});
	</script>
<?php } ?>
<div class="modal-body">
	<div class="form-group">
		<div class="row text-center">
			<div class="col-sm-12">
				<?php if($archivoACT=="NO_EXISTE"){ ?>
					<div class="observaciones">Este módulo genera un archivo que será descargado, en el o los terminales (puntos de registro de asistencia), para cada <strong><?= $rotuloM ?></strong>, el archivo sirve para la activación de asistencia de los socios y contiene la información de tema o concepto, hora de inicio y otros parámetros de <strong><?= $infoActividad ?></strong>; para el control de asistencia de los socios, sin la utilización de este archivo no podrá ser usado el sistema de registro de asistencia.</div>
				<?php }else{ ?>
					<div class="observaciones">El archivo fue generado, <strong class="text-danger">por favor descarguelo</strong> y luego puede subirlo en los terminales, para el inicio de control de asistencia de <strong><?= $infoActividad ?></strong>.</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php if($archivoACT=="NO_EXISTE"){ ?><div id="generar"></div><?php }else{ ?>
		<div class="modal-footer text-center">
			<a class="btn btn-success btn-labeled btn-xlg" href="../generados/<?= $archivoGenerado ?>" download="<?= $archivoGenerado ?>"><b><i class="icon-download"></i></b> Descargar archivo</a>
			<button type="button" class="btn btn-warning btn-labeled btn-xlg" data-dismiss="modal"><b><i class="icon-cross"></i></b> Cerrar</button>
		</div>
	<?php } ?>
</div>
<?php if($archivoACT=="NO_EXISTE"){ ?>
	<div class="modal-footer text-center">
		<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
		<button type="button" id="bt_genera_archivo" class="btn bg-grey <?= $estadoBT ?>">INICIAR PROCESO</button>
	</div>
<?php } ?>