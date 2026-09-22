<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// MODULO DE GENERACION DE PARTIDAS
		///////////////////////////////////////////////////
		$('#crearPartida').on('shown.bs.modal', function(){
			var ruta           ='../';
			$('#modulo_genera_partidas').fadeIn("slow").html('<div class="form-group mt-20"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>').fadeIn(1000).delay(1000);
			$("#modulo_genera_partidas").fadeIn("slow").load('modulo/genera-partidas.php').fadeIn(1000).delay(1000);
		});

		///////////////////////////////////////////////////
		/// MODULO DE SALIDA DE CAJA
		///////////////////////////////////////////////////
		$('#registroSalidas').on('shown.bs.modal', function(){
			var ruta           ='../';
			$('#modulo_salidas_caja').fadeIn("slow").html('<div class="form-group mt-20"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>').fadeIn(1000).delay(1000);
			$("#modulo_salidas_caja").fadeIn("slow").load('modulo/registra-salidas-caja.php').fadeIn(1000).delay(1000);
		});
	});
</script>

<div id="crearPartida" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content bg-modal">
			<div id="modulo_genera_partidas"></div>
		</div>
	</div>
</div>

<div id="registroSalidas" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content bg-modal">
			<div class="modal-header bg-brown">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">REGISTRO DE SALIDAS DE CAJA</h5>
			</div>
			<div id="modulo_salidas_caja"></div>
		</div>
	</div>
</div>