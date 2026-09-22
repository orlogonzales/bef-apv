<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$conexion       =conexionDB();
	$opcion         =$_GET[opcion];
	$codigoConcepto =$_GET[codigoConcepto];
?>

<?php if($opcion=="cuota"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// ACTUALIZAR BASE DE DATOS DE SOCIOS
			///////////////////////////////////////////////////
			$("button#bt_procesar").click(function(){
				var ruta           ='../';
				var codigoConcepto = '<?= $codigoConcepto ?>';
				var operacion      ='ACTUALIZA_SOCIOS_CUOTA';
				var datos          ='codigoCuota='+codigoConcepto+'&operacion='+operacion;
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-cuotas.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('#procesar').fadeOut(300);
						$('#procesar').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>ACTUALIZANDO BASE DE DATOS...</div></div></div>').fadeIn(1000).delay(1000);
					},
					success: function(respuesta){
						if(respuesta.mensaje=="SOCIOS_CUOTA_ACTUALIZADO"){
							new PNotify({title: 'CONFIRMACION', text: 'Base de datos de cuota actualizada con socios.', addclass: 'bg-success'});
							location.reload();
						}
					}
				});
			});
		});
	</script>

	<div id="test"></div>

	<div class="row pb-20 text-center">
		<div id="procesar">
			<div class="observaciones mb-15">Este módulo actualiza la base de datos de socios agregando el concepto y monto de cuota por socio, dependiendo de la cantidadd e lotes que posea a su titularidad.</div>
			<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
			<button type="button" id="bt_procesar" class="btn bg-success">INICIAR PROCESO</button>
		</div>
	</div>
<?php } ?>

<?php if($opcion=="asamblea"){ ?>
<?php } ?>

<?php if($opcion=="faena"){ ?>
<?php } ?>