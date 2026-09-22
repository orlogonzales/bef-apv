<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="SINCRONIZAR LISTA DE SOCIOS";
	$menuActual="sincronizarSocios";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>

<script type="text/javascript">
	$(document).ready(function(){
		$("#bitacoraSync").fadeIn(1000).delay(1000).load('modulo/bitacora-sync.php');
		$("#btSincrionizar").click(function(){
			console.log("ejecutando");
			$('button#btSincrionizar').fadeOut(500).delay(500);
			var ruta="<?= $ruta ?>";
			$.ajax({
				url: ruta+'php/sync.php',
				dataType: 'json',
				beforeSend: function(){
					console.log("iniciando -> Paso 1");
					$('#sincronizarSocios').html('<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>ACTUALIZADO BASE DE DATOS</div></div></div>').fadeIn(1000).delay(1000);
				},
				success: function(respuesta){
					console.log(respuesta);
					console.log("iniciando -> Paso 2");
					if(respuesta.mensaje=="FINZALIZADO"){
						$('#sincronizarSocios').fadeOut(500).delay(500);
						$("#bitacoraSync").fadeIn(1000).delay(1000).load('modulo/bitacora-sync.php');

						$.ajax({
							url: ruta+'php/sync-socios-actividades.php',
							dataType: 'json',
							beforeSend: function(){
								console.log("iniciando -> Paso 3");
								$('#sincronizarSocios').html('<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>ACTUALIZADO ACTIVIDADES</div></div></div>').fadeIn(1000).delay(1000);
							},
							success: function(respuesta){
								console.log("iniciando -> Paso 4");
								console.log(respuesta.consulta);
								console.log(respuesta);
								if(respuesta.mensaje=="FINZALIZADO_SYNC_SOCIOS"){
									$.ajax({
										url: ruta+'php/sync-socios-cuotas.php',
										dataType: 'json',
										beforeSend: function(){
											console.log("iniciando -> Paso 4");
											$('#sincronizarSocios').html('<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>ACTUALIZADO CUOTAS</div></div></div>').fadeIn(1000).delay(1000);
										},
										success: function(respuesta){
											console.log("iniciando -> Paso 5");
											console.log(respuesta.consulta);
											console.log(respuesta);
											location.reload(true);
										}
									});
								}
							}
						});
					}
				}
			});
		});
	});
</script>
<div class="panel panel-body border-top-primary text-center">
	<h6 class="no-margin text-semibold">SINCRONIZAR SOCIOS</h6>
	<p class="text-muted content-group-sm">Este Modulo le permite <strong>mantener actualizada la Base de datos</strong> con nuevos socios que hayan cancelado el total de su(s) lotes, para luego dinamizar su ionformacion con los datos del sistema de Socios, para realizar esta operacion, por favor haga click en el boton <strong class="text-warning">Sincronizar</strong>.</p>
	<button type="button" id="btSincrionizar" class="btn btn-danger btn-float btn-float-lg"><i class=" icon-sync"></i> <span>Sincronizar</span></button>
	<div id="sincronizarSocios"></div>
</div>
<div id="bitacoraSync"></div>
<?php
	include($ruta.'template/footer.tpl');
?>