<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$json        ='generados/';
	$archivoJSON ='socios.json';
	$archivoXLS  ='socios.xls';
	$JSON        =$ruta.$json.$archivoJSON;
	$XLS         =$ruta.$json.$archivoXLS;
	$opcion      =$_GET[opcion];
?>
<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// PROCESOS
		///////////////////////////////////////////////////
		$("button#exporta_socios_sistema").click(function(){
			var datos='opcion=GENERA_JSON';
			$('#moduloProcesos').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
			$("#moduloProcesos").fadeIn("slow").load('../administrador/modulo/exportar.php?'+datos).fadeIn(1000).delay(1000);
		});

		$("button#exporta_socios_zebra").click(function(){
			var datos='opcion=GENERA_XLS';
			$('#moduloProcesos').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
			$("#moduloProcesos").fadeIn("slow").load('../administrador/modulo/exportar.php?'+datos).fadeIn(1000).delay(1000);
		});
	});
</script>

<div class="modal-body">
	<div class="row mb-20">
		<div class="col-sm-6">
			<button type="button" id="exporta_socios_sistema" class="btn bg-warning-300 btn-float btn-float-lg btn-block text-semibold textoMayuscula"><i class=" icon-hour-glass"></i> <span>Exportar Socios<br>para <strong>sistema de Asistencia</strong></span></button>
		</div>
		<div class="col-sm-6">
			<button type="button" id="exporta_socios_zebra" class="btn bg-warning-300 btn-float btn-float-lg btn-block text-semibold textoMayuscula"><i class="icon-credit-card2"></i> <span>Exportar Socios<br>para <strong>Software Zebra Card</strong></span></button>
		</div>
	</div>
</div>

<div id="moduloProcesos"></div>

<?php if($opcion=="ELIMINA_JSON"){
	unlink($JSON);
	unlink($XLS);
} ?>