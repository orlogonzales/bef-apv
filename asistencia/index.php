<?php 
	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
		$urlSistema   = 'https://'.$_SERVER['SERVER_NAME'].'/apv/';
	}else{
		$urlSistema   = 'http://'.$_SERVER['SERVER_NAME'].'/apv/';
	}

	$urlSistemaActividadesAsistencia=$urlSistema.'asistencia/asistencia.php';

	session_start();
	if(!empty($_SESSION)){
		if($_SESSION['login_apv']=='LOGUEADO' && $_SESSION['usuario_apv']!='' && $_SESSION['dni_apv']!='' && $_SESSION['rol_apv']!='' &&$_SESSION['codigoActividad']==''){
			header('Location: actividades.php');
		}

		if($_SESSION['login_apv']=='LOGUEADO' && $_SESSION['usuario_apv']!='' && $_SESSION['dni_apv']!='' && $_SESSION['rol_apv']!='' &&$_SESSION['codigoActividad']!=''){
			header('Location: '.$urlSistemaActividadesAsistencia);
		}
	}else{
		$sesion = 'SESION_CERRADA';
	}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>LOGIN :: REGISTRO DE ASISTENCIA</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/minified/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/minified/core.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/minified/components.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/minified/colors.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $urlSistema ?>assets/css/add.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/loaders/blockui.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/notifications/pnotify.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/plugins/jquery.validate/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?= $urlSistema ?>assets/js/core/app.js"></script>
</head>
<body class="bg-brown">
	<script type="text/javascript">
		$(document).ready(function(){
			$('#login').click(function(){
				event.preventDefault();
				var usuario   = $("#usuario").val();
				var clave     = $("#clave").val();
				var operacion = $("#operacion").val();
				var datos     = 'usuario='+usuario+'&clave='+clave+'&operacion='+operacion;
				var ruta      = '<?= $urlSistema ?>api/index.php';
				var valida    = $('#form_login').valid();
				if(valida){
					$.ajax({
						type: "POST",
						url: "<?= $urlSistema ?>api/index.php",
						data: datos,
						cache: false,
						dataType:'json',
						beforeSend: function(){
							$('#login').prop('disabled', true);
							$('#login').slideUp();
							$("#procesandoLogin").fadeIn('slow').html('<div class="form-group"><div class="row"><div class="col-xs-12 text-center"><img src="<?= $urlSistema ?>assets/images/loader.gif"></div></div></div>');
						},
						success: function(respuesta){
							console.log(respuesta);
							if(respuesta.estado=="ERROR" && respuesta.usuario=="NOEXISTE"){
								$("#procesandoLogin").fadeOut('slow').html('<div class="form-group"><div class="row"><div class="col-xs-12 text-center"><img src="<?= $urlSistema ?>assets/images/loader.gif"></div></div></div>');
								$("#procesandoLogin").html('<div class="alert alert-danger fade in block text-center">VERIFIQUE SUS DATOS DE ACCESO.</div>').hide().slideDown().delay(4000).slideUp();
								$('#login').prop('disabled', false);
								$('#login').slideDown();
							}
							if(respuesta.estado=="ERROR" && respuesta.usuario=="INACTIVO"){
								$("#procesandoLogin").html('<div class="alert alert-danger fade in block text-center"> USUARIO ESTA INACTIVO.</div>').hide().fadeIn(1500).delay(6000);
								$("#usuario").val('');
								$("#clave").val('');
								$('#usuario').focus();
								$('#login').prop('disabled', false);
								$('#login').slideDown();
							}
							if(respuesta.estado=="OK" && (respuesta.login!="NOEXISTE" || respuesta.login!="INACTIVO" || respuesta.login!="" || respuesta.login!=NULL)){
								$("#procesandoLogin").fadeOut('slow').html('<div class="form-group"><div class="row"><div class="col-xs-12 text-center"><img src="<?= $urlSistema ?>assets/images/loader.gif"></div></div></div>');
								window.location.replace("actividades.php");
							}
						}
					});
				}
			});

			/////////////////////////////////////////////////////////////////////
			/// VALIDA FORMULARIO
			/////////////////////////////////////////////////////////////////////
			$("#form_login").validate({
				onkeyup: false,
				onfocusout: false,
				rules: {
					usuario : { required: true },
					clave   : { required: true },
				},
				messages: {
					usuario : 'Campo necesario',
					clave   : 'Campo necesario',
				},
				highlight: function(element, errorClass, validClass){
					$( element ).addClass( "invalido" ).removeClass( "valido" );
					$(element.form).find("label[for=" + element.id + "]").addClass(errorClass);
				},
				unhighlight: function(element, errorClass){
					$( element ).addClass( "valido" ).removeClass( "invalido" );
				},
				invalidHandler: function(form, validator) {
					var errors = validator.numberOfInvalids();
					if(errors){
						validator.errorList[0].element.focus();
					}
				}
			});
		});
	</script>
	<div class="page-container login-container">
		<div class="page-content">
			<div class="content-wrapper">
				<div class="content">
					<form id="form_login">
						<div class="panel panel-body login-form">
							<div class="text-center">
								<h5 class="content-group text-brown"><storng>INGRESO ACTIVIDADES</storng></h5>
							</div>

							<div class="form-group has-feedback has-feedback-left">
								<input type="text" id="usuario" name="usuario" class="form-control input-lg" placeholder="Usuario" autofocus tabindex="1">
								<label class="error-campo" for="usuario"></label>
							</div>
							<div class="form-group has-feedback has-feedback-left">
								<input type="password" id="clave" name="clave" class="form-control input-lg" placeholder="Contraseña" tabindex="2">
								<label class="error-campo" for="clave"></label>
							</div>
							<div id="procesandoLogin"></div>
							<div class="form-group">
								<input type="hidden" id="operacion" value="login">
								<button type="submit" id="login" class="btn btn-lg bg-brown btn-block">INGRESAR</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>
</html>