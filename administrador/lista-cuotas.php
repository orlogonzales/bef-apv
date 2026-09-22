<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="LISTA DE CUOTAS";
	$menuActual="listaCuotas";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>

<script type="text/javascript">
	$(document).ready(function(){
		var ruta='<?= $ruta ?>';
		$('#listaCuotas').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
		$("#listaCuotas").fadeIn("slow").load('modulo/lista-cuotas.php').hide().fadeIn(1000).delay(1000);
	});
</script>

<div id="listaCuotas"></div>

<?php
	include($ruta.'template/footer.tpl');
?>