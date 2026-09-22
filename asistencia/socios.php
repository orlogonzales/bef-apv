<?php 
	include('template/header.tpl');
	$terminal=terminal();
?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// LISTA DE SOCIOS
			///////////////////////////////////////////////////
			$('#listaSocios').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>CARGANDO SOCIOS</div></div></div>');
			$("#listaSocios").fadeIn("slow").load('modulo/lista-socios.php').fadeIn(1000).delay(1000);

			///////////////////////////////////////////////////
			/// ACCIONES AL ABRIR MODAL
			///////////////////////////////////////////////////
		    $('#updateSocios').on('click', function() {
		        $('#actualizaSocios').on('shown.bs.modal', function() {
					$('#moduloActualiza').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>');
					$("#moduloActualiza").fadeIn("slow").load('modulo/socios.php').fadeIn(1000).delay(1000);
		        });
		    });
		});
	</script>

	<div class="row">
		<div class="col-lg-8 col-lg-offset-2">
			<div class="panel">
				<div class="panel-heading bg-brown">
					<h6 class="panel-title"><mark>TERMINAL - <?= ceros($terminal,2) ?></mark>&nbsp;&nbsp;&nbsp;LISTA DE SOCIOS</h6>
					<div class="heading-elements">
						<button type="button" id="updateSocios" data-target="#actualizaSocios" data-toggle="modal" class="btn bg-brown-700 btn-labeled"><b><i class="icon-sort"></i></b> ACTUALIZAR SOCIOS</button>
					</div>
				</div>
				<div class="panel-body">
					<div id="listaSocios"></div>
				</div>
			</div>
		</div>
	</div>

	<div id="actualizaSocios" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-brown">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h6 class="modal-title">MODULO DE ACTUALIZACION DE SOCIOS</h6>
				</div>
				<div class="modal-body">
					<div id="moduloActualiza"></div>
				</div>
			</div>
		</div>
	</div>
<?php include('template/footer.tpl') ?>