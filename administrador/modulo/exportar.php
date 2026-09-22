<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$json        ='generados/';
	$archivoJSON ='socios.json';
	$JSON        =$ruta.$json.$archivoJSON;
	$opcion      =$_GET[opcion];
	if (file_exists($JSON)){ $proceso="descargar"; }else{ $proceso="generar"; }
?>
<?php if($opcion=="GENERA_JSON"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// OCULTA O MUESTRA DIVS
			///////////////////////////////////////////////////
			var proceso="<?= $proceso ?>";
			if(proceso=='generar'){
				$('#generaJSON').show();
				$('#descargaJSON').hide();
				$('#descarga_archivo').hide();
			}

			if(proceso=='descargar'){
				$('#generaJSON').hide();
				$('#genera_archivo').hide();
				$('#descargaJSON').show();
				$('#descarga_archivo').show();
			}


			///////////////////////////////////////////////////
			/// GENERAR ARCHIVO
			///////////////////////////////////////////////////
			$("button#genera_archivo").click(function(){
				var ruta='../';
				var operacion='GENERAR_ARCHIVO_JSON';
				var datos='operacion='+operacion;
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-socios.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('button#genera_archivo').prop('disabled', true);
						$('#generar').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>GENERANDO ARCHIVO</div></div></div>').fadeIn(1000).delay(1000);
					},
					success: function(respuesta){
						if(respuesta.mensaje=="ARCHIVO_GENERADO"){
							$('#generaJSON').hide();
							$('#genera_archivo').hide();
							$('#descargaJSON').show();
							$('#descarga_archivo').show();
							$('#generar').hide();
						}
					}
				});
			});
		});
	</script>

	<div class="modal-body">
		<div class="form-group">
			<div class="row text-center">
				<div id="generaJSON"><div class="observaciones">Este módulo genera un archivo que será descargado, en el o los terminales (puntos de registro de asistencia), la función que cumple este archivo es la de mantener <strong class="text-danger">actualizada la base de datos de socios</strong> y es muy importante su actualización antes de realizarse ya sea una Asamblea o faena o cada vez que el sistema sincronice nuevos socios, para el control efectivo del total de socios registrados en el sistema.</div></div>
				<div id="descargaJSON"><div class="observaciones">El archivo fue generado, <strong class="text-danger">por favor descarguelo</strong> y luego puede subirlo en los terminales, <strong>para mantener actualizada la base de datos de socios</strong>.</div></div>
			</div>
		</div>
	</div>

	<div id="generar"></div>

	<div class="modal-footer text-center">
		<a href="../generados/<?= $archivoJSON ?>" download="<?= $archivoJSON ?>" id="descarga_archivo" class="btn bg-teal btn-labeled btn-xlg"><b><i class="icon-download"></i></b> DESCARGAR ARCHIVO</a>
		<button type="button" id="genera_archivo" class="btn btn-success btn-labeled btn-xlg"><b><i class="icon-users"></i></b> GENERAR ARCHIVO JSON</button>
		<button type="button" class="btn btn-warning btn-labeled btn-xlg" data-dismiss="modal"><b><i class="icon-close2"></i></b>CERRAR</button>
	</div>
<?php } ?>

<?php if($opcion=="GENERA_XLS"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			$("#genera_archivo_xls").click(function(){
				var ruta='../';
				$('#genera_archivo_xls').fadeOut(1000);
				$('#generar').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>GENERANDO ARCHIVO</div></div></div>').fadeIn(1000).delay(1000);
			});
		});
	</script>


	<div class="modal-body">
		<div class="form-group">
			<div class="row text-center">
				<div class="observaciones">Este módulo genera un archivo de formato <strong class="text-danger">Microsoft Excel</strong> como Base de Datos de Socios para el <strong class="text-danger">Zebra Card Studio</strong>, para la implementacion de las <strong>Tarjetas de Socio</strong>.</div>
			</div>
		</div>
	</div>

	<div id="generar"></div>
	
	<div class="modal-footer text-center">
		<a href="../php/socios-xls.php" id="genera_archivo_xls" class="btn btn-success btn-labeled btn-xlg"><b><i class="icon-users"></i></b> GENERAR ARCHIVO XLS</a>
		<button type="button" class="btn btn-warning btn-labeled btn-xlg" data-dismiss="modal"><b><i class="icon-close2"></i></b>CERRAR</button>
	</div>
<?php } ?>