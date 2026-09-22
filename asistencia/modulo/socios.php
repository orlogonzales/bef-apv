<?php
	include('../php/funciones.php');
	$socios=infoSocio($codigoSocio,'socios');
?>

<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// CAMBIO DE INPUT FILE
		///////////////////////////////////////////////////
		$(".file-upload").uniform({ wrapperClass: 'bg-brown-300', fileButtonHtml: '<i class="icon-plus2"></i>' });

		///////////////////////////////////////////////////
		/// BOTON SUBIR ARCHIVO
		///////////////////////////////////////////////////
		$("button#subir_json").click(function(){
			var socios    ='<?= $socios ?>';
			var operacion ="SUBIR_ARCHIVO";
			var datos     =new FormData($("#actividad")[0]);
			$.ajax({
				url: 'php/mantenimiento-socios.php?operacion='+operacion,
				type: "POST",
				dataType:'json',
				data: datos,
				contentType: false,
				processData: false,
				beforeSend: function(){
					$('#subir').hide();
					$('button#subir_json').prop('disabled', true);
					$('#procesando').fadeIn("slow").html('<div class="row mb-15"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>ACTUALIZANDO SOCIOS</div></div>').fadeIn(1000).delay(1000);
				},
				success: function(respuesta){
					if(respuesta.mensaje=="ACTUALIZADO"){
						location.reload();
					}
					if(respuesta.mensaje=="ARCHIVOINCORRECTO"){
						$('button#subir_json').prop('disabled', false);
						$('#aviso').html('<div class="alert alert-danger alert-styled-left alert-bordered">Error el archivo subido tiene <strong>'+respuesta.socios+' socios</strong> y actualmente existen <strong>'+socios+' socios</strong>.</div>');
						$('#procesando').hide();
						$('#subir').show();
					}
					if(respuesta.mensaje=="SINFILE"){
						$('button#subir_json').prop('disabled', false);
						$.jGrowl('Seleccione archivo <strong>socios.json</strong>', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
						$('#procesando').hide();
						$('#subir').show();
					}
					if(respuesta.mensaje=="ERRORFORMATO"){
						$('button#subir_json').prop('disabled', false);
						$.jGrowl('Seleccione archivo <strong>socios.json</strong>.', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
						$('#procesando').hide();
						$('#subir').show();
					}
					if(respuesta.mensaje=="ERRORSERVER"){
						$('button#subir_json').prop('disabled', false);
						$.jGrowl('Ha ocurrido un error en el servidor.', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
						$('#procesando').hide();
						$('#subir').show();
					}
				}
			});
		});
	});
</script>

<div class="text-center">
	<h6 class="no-margin text-brown text-bold">ACTUALIZAR BASE DE DATOS DE SOCIOS</h6>
	<p class="content-group-sm pb-10">SUBIR EL ARCHIVO GENERADO POR EL SISTEMA, PARA ACTUALIZAR LA BASE DE DATOS DE SOCIOS.</p>
	<div id="subir">
		<form id="actividad" method="post" enctype="multipart/form-data">
			<fieldset class="content-group">
				<div class="form-group">
					<div class="col-lg-8">
						<input type="file" name="archivo" class="file-upload">
					</div>
					<div class="col-lg-4">
						<button type="button" id="subir_json" class="btn btn-block btn-labeled bg-brown"><b><i class="icon-upload4"></i></b> SUBIR ARCHIVO</button>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	<div id="procesando"></div>
	<div id="aviso"></div>
	<div class="table-responsive mb-20">
		<table class="table table-bordered table-framed">
			<thead>
				<tr class="success">
					<th class="text-left text-bold">SOCIOS REGISTRADOS EN EL SISTEMA</th>
					<th class="text-center text-danger text-bold"><?= $socios ?> SOCIOS</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
