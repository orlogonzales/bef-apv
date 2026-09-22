
					<div id="infoLogin"></div>
					<div id="controlActividad"></div>

					<script type="text/javascript">
						/////////////////////////////////////////////////////////////////////
						/// CERRAR SESIÓN POR INACTIVIDAD
						/////////////////////////////////////////////////////////////////////
						const minutosInactividad = 60;
						let tiempoInactividad = minutosInactividad * 60 * 1000; // Tiempo en milisegundos
						let tiempoTranscurrido = 0; // Tiempo de inactividad acumulado
						let tiempoLimite;
						let contadorInactividad;

						function cerrarSesion() {
							$.ajax({
								dataType : 'json',
								url      : '../php/salir.php',
								success: function(respuesta){
									if(respuesta.resultado=='SESION_CERRADA'){
										location.reload();
									}
								}
							});	
						}

						function iniciarContador() {
							contadorInactividad = setInterval(() => {
								tiempoTranscurrido += 1000;
								//console.log(`TIEMPO DE INACTIVIDAD: ${tiempoTranscurrido / 1000} SEGUNDOS`);

								if (tiempoTranscurrido >= tiempoInactividad) {
									clearInterval(contadorInactividad);
									cerrarSesion();
								}
							}, 1000);
						}

						function reiniciarTemporizador() {
							clearTimeout(tiempoLimite);
							clearInterval(contadorInactividad);
							tiempoTranscurrido = 0;
							iniciarContador();
							tiempoLimite = setTimeout(cerrarSesion, tiempoInactividad);
						}

						// Detectar actividad del usuario para reiniciar el temporizador
						$(document).on("mousemove keydown scroll", reiniciarTemporizador);

						// Iniciar el contador de inactividad
						iniciarContador();

						// SALIR DEL SISTEMA
						$("a#btn-salir").click(function(){
							swal({
								title: "Salir del Sistema",
								text: "¿Esta seguro que deseas cerrar sesion?",
								type: "warning",
								showCancelButton: true,
								cancelButtonText: "Cancelar",
								confirmButtonColor: "#DD6B55",
								confirmButtonText: "Salir",
								closeOnConfirm: false
							},
							function(){
								$.ajax({
									dataType : 'json',
									url      : '../php/salir.php',
									success: function(respuesta){
										console.log(respuesta);
										if(respuesta.resultado=='SESION_CERRADA'){
											location.reload();
										}
									}
								});	
							});
						});
					</script> 
					<div class="footer text-muted">
						&copy; 2015 - <?php echo date('Y') ?> <?php echo nombreSistema().' '.versionSistema() ?> | <i class="fa fa-code"></i> <strong>OG - Estudio Creativo</strong> | <i class="fa fa-phone"></i> 950373240 | <i class="fa fa-map-marker"></i> Cusco - Perú
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>