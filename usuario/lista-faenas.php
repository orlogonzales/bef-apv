<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="LISTA DE FAENAS";
	$menuActual="listaFaenas";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>

<script type="text/javascript">
	$(document).ready(function(){
		var ruta='<?= $ruta ?>';
		$('#listaFaenas').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
		$("#listaFaenas").fadeIn("slow").load('modulo/lista-actividades.php?tipoActividad=FAE').hide().fadeIn(1000).delay(1000);
	});
</script>

<div id="listaFaenas"></div>

<?php
	include($ruta.'template/footer.tpl');
?>