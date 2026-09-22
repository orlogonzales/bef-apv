<?php
	$ruta ="../";
	include_once ($ruta."/php/funciones.php");

	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
		$urlSistema   = 'https://'.$_SERVER['SERVER_NAME'].'/apv/';
	}else{
		$urlSistema   = 'http://'.$_SERVER['SERVER_NAME'].'/apv/';
	}

	$urlSistemaActividades=$urlSistema.'asistencia/';
	$urlSistemaTerminarActividad=$urlSistema.'asistencia/terminar-asistencia.php';
	$urlSistemaActividadesAsistenciaDNI=$urlSistema.'asistencia/terminar-asistencia-dni.php';

	session_start();
	//var_dump($_SESSION);
	if(empty($_SESSION)){
		header('Location: '.$urlSistema);
	}else{
		if($_SESSION['codigoActividad']=='' && $_SESSION['estadoActividad']==''){
			header('Location: '.$urlSistemaActividades);
		}
	}

	$codigoActividad = $_SESSION['codigoActividad'];
	$formaActividad  = $_SESSION['formaActividad'];
	$hoy             = infoTiempo('fecha');
	$infoActividad   = infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
	$fechaActividad  = infoActividad($idJuntaDirectiva,$codigoActividad,'','fechaActividad');
	$horaActividad   = infoActividad($idJuntaDirectiva,$codigoActividad,'','horaActividad');
	$diasEntre       = diasEntre($hoy,$fechaActividad);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>APV :: ACTIVIDADES PROGRAMADAS</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/minified/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/minified/core.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/minified/components.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/minified/colors.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/meeting.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/add.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/js/plugins/sweetalert/sweet-alert.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/loaders/blockui.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/velocity/velocity.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/velocity/velocity.ui.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/buttons/spin.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/buttons/ladda.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/forms/selects/select2.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/core/app.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/fullscreen/jquery.fullscreen.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/notifications/pnotify.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/notifications/noty.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/sweetalert/sweet-alert.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			var meses = [ "ENERO", "FEBRERO", "MARZO", "ABRIL", "MAYO", "JUNIO", "JULIO", "AGOSTO", "SETIEBRE", "OCTUBRE", "NOVIEMBRE", "DICIEMBRE" ]; 
			var dias= ["DOMINGO","LUNES","MARTES","MIERCOLES","JUEVES","VIERNES","SABADO"]
			var newDate = new Date();
			newDate.setDate(newDate.getDate());
			$('#infoTiempo').html(dias[newDate.getDay()] + " " + newDate.getDate() + ' ' + meses[newDate.getMonth()] + ' ' + newDate.getFullYear());
			setInterval( function() { var segundos = new Date().getSeconds(); $("#segundos").html(( segundos < 10 ? "0" : "" ) + segundos); },1000);
			setInterval( function() { var minutos = new Date().getMinutes(); $("#minutos").html(( minutos < 10 ? "0" : "" ) + minutos); },1000);
			setInterval( function() { var horas = new Date().getHours(); $("#horas").html(( horas < 10 ? "0" : "" ) + horas); },1000);

			$('#cerrarActividad').click(function(){
				//$(this).prop('disabled',true);
				event.preventDefault();
				var operacion = "cerrarActividad";
				var datos     = 'operacion='+operacion;
				var ruta      = '<?= $urlSistema ?>api/index.php';
				swal(
					{
						title: "¿Terminar Actividad?",
						text: "<?= $infoActividad ?>",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "Si, Terminar",
						cancelButtonText: "No, Terminar",
						closeOnConfirm: true,
						closeOnCancel: false
					},
					function(isConfirm){
						if (isConfirm) {
							$.ajax({
								type: "POST",
								url: ruta,
								data: datos,
								cache: false,
								dataType:'json',
								beforeSend: function(){
									$('.cancel').prop('disabled',true);
									$('.cancel').prop('confirm',true);
									$('#registro').slideUp();
									$('#procesando').slideDown();
								},
								complete: function(){
									$('#procesando').slideUp();
									$('#finalizado').slideDown();
								},
								success: function(respuesta){
									swal("¡Hecho!","Acabas de terminar la actividad","success");
									window.location.replace("actividades.php");
								}
							});
						}
					}
				);
			});
		}); 
	</script>
</head>
<body style="overflow: hidden;">
<div id="fullscreen">
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<div class="navbar-brand"><img src="<?= $urlSistema ?>assets/images/logo_light.png" alt=""></div>
		</div>
		<button type="button" id="cerrarActividad" class="btn btn-xs btn-danger pull-right" style="margin-top: 5px !important;">CERRAR ACTIVIDAD</button>
	</div>
	<div class="login-container">
		<div class="page-content">
			<div class="content-wrapper">
				<div class="content">
					<div class="row" style="margin: 0; position: absolute; top: 50%; width: 100%; -ms-transform: translateY(-50%); transform: translateY(-50%);">
						<?php if($diasEntre==0){ ?>
							<div id="registro">
								<div class="col-lg-6 col-lg-offset-3">
									<div class="panel panel-body border-top-xlg border-top-warning-800 text-center">
										<div class="row">
											<div class="col-sm-12 text-center text-brown">
												<strong>FECHA ACTIVIDAD &rarr;</strong> <?= strtoupper(infoFecha($fechaActividad,'corta')) ?>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp; <strong>HORA DE INICIO &rarr;</strong> <?= infoHora($horaActividad) ?>
											</div>
										</div>
										<h6 class="no-margin text-warning-800 infoActividad"><?= $infoActividad ?></h6>
										<h5 style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #CCC;"><a href="<?= $urlSistemaActividadesAsistenciaDNI ?>" class="btn btn-danger btn-md" style="font-size: 15px;">REGISTRAR CON <strong style="font-weight: bolder;">DNI</strong></a></h5>
										<div class="text-center text-brown">
											<div id="reloj">
												<ul>
													<li id="horas">00</li>
													<li id="dosPuntos">:</li>
													<li id="minutos">00</li>
													<li id="dosPuntos">:</li>
													<li id="segundos">00</li>
												</ul>
											</div>
											<div id="infoTiempo"></div>
										</div>

										<script type="text/javascript">
											$(document).ready(function(){
												///////////////////////////////////////////////////
												/// FOCUS INFIINITO A CODIGO DE SOCIO
												///////////////////////////////////////////////////
												$('#codigoSocio').focus();

												///////////////////////////////////////////////////
												/// ENVIAR CODIGO DE BARRA A REGISTRO
												///////////////////////////////////////////////////
												$("#codigoSocio").on( "keypress", function(event) {
													if (event.which == 13 && !event.shiftKey) {
														event.preventDefault();
														var codigoActividad = '<?= $codigoActividad ?>';
														var formaActividad  = '<?= $formaActividad ?>';
														var operacion       = 'REGISTRA_SALIDA';
														var codigoSocio     = $('input#codigoSocio').val();
														var datos           = 'codigoActividad='+codigoActividad+'&codigoSocio='+codigoSocio+'&operacion='+operacion;
														if(formaActividad==1){
															var ruta= '<?= $urlSistema ?>api/index.php';
															$.ajax({
																type: "POST",
																url: ruta,
																data: datos,
																dataType:'json',
																success: function(respuesta){
																	$('#codigoSocio').val('');
																	$('#codigoSocio').focus();
																	if(respuesta.estado=="registrado"){
																		$.jGrowl('SOCIO REGISTRO SU SALIDA.', { header: 'CONFIRMACION', theme: 'alert-styled-left bg-success-300' });
																	}

																	if(respuesta.estado=="documento_no_valido"){
																		$.jGrowl('DOCUMENTO NO VALIDO.', { header: 'ERROR', theme: 'alert-styled-left bg-warning-300' });
																	}

																	if(respuesta.estado=="ya_registro_salida"){
																		$.jGrowl('YA REGISTRO SU SALIDA.', { header: 'CONFIRMACION', theme: 'alert-styled-left bg-warning-300' });
																	}

																	if(respuesta.estado=="falto_por_tardanza"){
																		$.jGrowl('SOCIO FALTO A ACTIVIDAD.', { header: 'ADVERTENCIA', theme: 'alert-styled-left bg-danger-300' });
																	}
																}
															});
														}else{
															$.ajax({
																type: "POST",
																url: 'php/mantenimiento-actividad.php',
																data: datos,
																dataType:'json',
																success: function(respuesta){
																	if(respuesta.estado=="INGRESO_REGISTRADO"){
																		location.reload();
																	}

																	if(respuesta.estado=="SALIDA_REGISTRADA"){
																		location.reload();
																	}
																	
																	if(respuesta.estado=="ERROR_REGISTRO"){
																		$.jGrowl('Error en registro.', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
																	}

																	if(respuesta.estado=="SOCIO_REGISTRADO"){
																		$.jGrowl('Socio ya registró su salida...', { header: 'Registrado', theme: 'alert-styled-left bg-danger-300' });
																		$('input#codigoSocio').val('');
																	}
																}
															});
														}
													}
												});
											});
										</script>
										<div class="form-group mt-20">
											<input type="text" id="codigoSocio" maxlength="14" class="form-control text-center registroCodigo text-danger" placeholder="Ingrese codigo de socio" onblur="this.focus();">
											<div id="print"></div>
										</div>
									</div>
								</div>
							</div>

							<div id="procesando" style="display:none">
								<div class="text-center">
									<img src="../assets/images/procesando.png" height="150px" style="margin:0px 0px 10px 0px;">
									<h6 class="no-margin text-semibold">PROCESANDO ASISTENCIA</h6>
								</div>
							</div>

							<div id="finalizado" style="display:none">
								<div class="text-center">
									<img src="../assets/images/confirmacion.png">
								</div>
							</div>
						<?php }else{ ?>
							<div class="col-lg-6 col-lg-offset-3">
								<div class="panel panel-body border-top-xlg border-top-warning-800 text-center">
									<div class="infoActividad text-danger"><?= $infoActividad ?></div>
									<div class="infoTexto">FALTAN <?= ceros($diasEntre,2) ?> DIAS</div>
								</div>
							</div>
						<?php } ?>
					</div>
<?php include('template/footer.tpl') ?>