<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="LISTA DE ASAMBLEAS";
	$menuActual="listaAsambleas";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>

<script type="text/javascript">
	$(document).ready(function(){
		var ruta='<?= $ruta ?>';
		$('#listaAsambleas').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
		$("#listaAsambleas").fadeIn("slow").load('modulo/lista-actividades.php?tipoActividad=ASA').hide().fadeIn(1000).delay(1000);
	});
</script>

<div id="listaAsambleas"></div>

<?php
	include($ruta.'template/footer.tpl');
?>