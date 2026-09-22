<?php
	$ruta='../';
	include_once $ruta."php/funciones.php";

	/////////////////////////////////////////////////////////////////////
	/// CONECCION CON LA INTERFACE JSON DEL SISTEMA DE VENTAS
	/////////////////////////////////////////////////////////////////////
	$servidor=$_SERVER['HTTP_HOST'];
	if($servidor=='app.bef'){
		$urlSistema="http://{$_SERVER['HTTP_HOST']}/ventas";
		$urlSocios="http://{$_SERVER['HTTP_HOST']}/apv";
	}else{
		$urlSistema="https://{$_SERVER['HTTP_HOST']}/ventas";
		$urlSocios="https://{$_SERVER['HTTP_HOST']}/apv";
	}

	$codigoLote=$_GET[codigoLote];
	$codigoSocio=$_GET[codigoSocio];
	$direccion=informacionLote($codigoSocio, $codigoLote, 'direccion');
?>
<div class="modal-body">
	<form id="form_direccion_lote">
		<div class="form-group">
			<div class="row">
				<div class="col-lg-8 col-xs-12">
					<input type="text" name="direccion" placeholder="Dirección" value="<?= $direccion ?>" class="form-control textoMayuscula" onblur="mayusculas(event, this)" autofocus>
					<input type="hidden" name="codigoLote" value="<?= $codigoLote ?>">
					<input type="hidden" name="codigoSocio" value="<?= $codigoSocio ?>">
					<input type="hidden" name="operacion" value="ACTUALIZA_DIRECCION">
				</div>
				<div class="col-lg-2 col-xs-6">
					<button type="button" class="btn btn-primary btn-block" id="bt_actualizar_direccion">Actualiza</button>
				</div>
				<div class="col-lg-2 col-xs-6">
					<button type="button" class="btn btn-warning btn-block" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</form>
</div>
<!-- PROCESOS DE LOTES -->
<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// AGREGA DIRECCION A LOTE
		///////////////////////////////////////////////////
		$("#bt_actualizar_direccion").click(function(){
			var datos          = $('#form_direccion_lote').serialize();
			var urlSistema     = '<?= $urlSocios ?>';
			var urlProceso     = urlSistema+'/php/mantenimiento-socios.php';
		 	$.ajax({
				type: "POST",
				url: urlProceso,
				data: datos,
				dataType:'json',
				success: function(respuesta){
					if(respuesta.mensaje=="PROCESADO"){
						location.reload(true);
					}else{
						new PNotify({title: 'ADVERTENCIA', text: 'Ocurrio un error en el registro a la base de datos, por favor intentelo nuevamente.', addclass: 'bg-warning'});
					}
				}
			});
		});
	});

	function mayusculas(e, elemento) {
		tecla=(document.all) ? e.keyCode : e.which; 
		elemento.value = elemento.value.toUpperCase();
	}

	function minusculas(e, elemento) {
		tecla=(document.all) ? e.keyCode : e.which; 
		elemento.value = elemento.value.toLowerCase();
	}
</script>