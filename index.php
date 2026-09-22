<?php 
	include_once "php/funciones.php";
	session_start();
	if(!empty($_SESSION)){
		$dniUsuario = $_SESSION['dni_apv'];
		$sesion     = verificaEstadoActividad($dniUsuario);

		if($sesion=='SESION_ACTIVA'){
			if($_SESSION['rol_apv']=='ADM'){ header('Location: administrador/'); }
			if($_SESSION['rol_apv']=='TES'){ header('Location: usuario/'); }
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
	<title>LOGIN :: <?php echo nombreSistema() ?></title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/minified/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/minified/core.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/minified/components.min.css" rel="stylesheet" type="text/css">
	<link href="assets/css/minified/colors.min.css" rel="stylesheet" type="text/css">
	<link href="assets/js/plugins/sweetalert/sweet-alert.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="assets/js/plugins/loaders/blockui.min.js"></script>	
	<script type="text/javascript" src="assets/js/plugins/sweetalert/sweet-alert.min.js"></script>
	<script type="text/javascript" src="assets/js/core/app.js"></script>
</head>
<body>
	<script type="text/javascript">
		$(document).ready(function(){
			var sesion='<?= $sesion ?>';
			if(sesion=='SESION_CERRADA'){
				const formulario = $('#form-login');
				$('#login').click(function(){
					var usuario =$("#usuario").val();
					var clave   =$("#clave").val();
					var datos   ='usuario='+usuario+'&clave='+clave;
					if($.trim(usuario).length>0 && $.trim(clave).length>0){
						$.ajax({
							type: "POST",
							url: "php/login.php",
							data: datos,
							cache: false,
							dataType:'json',
							beforeSend: function(){
								$("#procesandoLogin").fadeIn('slow').html('<div class="form-group"><div class="row"><div class="col-xs-12 text-center"><img src="assets/images/loader.gif"></div></div></div>');
							},
							success: function(respuesta){
								if(respuesta.estadoLogin=="LOGIN_OK"){
									$('#form-login *').prop('disabled',true);
									$('#login').hide();
									$("#procesandoLogin").fadeIn('slow').html('<div class="form-group"><div class="row"><div class="col-xs-12 text-center"><img src="assets/images/loader.gif"><br>CARGANDO INTERFACE</div></div></div>');
									$('#infoLogin').html('<div class="alert alert-info text-center" role="alert">HOLA '+respuesta.nombreUsuario+'<br><strong>'+respuesta.descripcionRol+'</strong></div>');
									setTimeout(function() {
								        if (respuesta.rol == "ADM" || respuesta.rol == "JDP" || respuesta.rol == "JDT") {
								            window.location.replace("administrador/index.php");
								        }
								        if (respuesta.rol == "TES") {
								            window.location.replace("usuario/index.php");
								        }
								    }, 2000);
								}
								if(respuesta.estadoLogin=="USUARIO_INACTIVO"){
									$("#procesandoLogin").fadeOut('slow').html('<div class="form-group"><div class="row"><div class="col-xs-12 text-center"><img src="assets/images/loader.gif"></div></div></div>');
									swal({
										title: "USUARIO INACTIVO",
										text: "EL USUARIO "+respuesta.nombreUsuario+" ESTA INACTIVO",
										type: "warning",
										confirmButtonText: "Entiendo",
									});
									$("#usuario").val('');
									$("#clave").val('');
									$('#usuario').focus();
								}
								if(respuesta.estadoLogin=="USUARIO_NO_EXISTE"){
									$("#infoLogin").fadeOut('slow').html('<div class="form-group"><div class="row"><div class="col-xs-12 text-center"><img src="assets/images/loader.gif"></div></div></div>');
									swal({
										title: "CREDENCIALES INCORRECTAS",
										text: "LOS DATOS DE ACCESO SON INCORRECTOS",
										type: "warning",
										confirmButtonText: "Entiendo",
									});
									$("#usuario").val('');
									$("#clave").val('');
									$('#usuario').focus();
								}
							}
						});
					}else{
						$.jGrowl('Ingrese datos de acceso e intentelo nuevamente.', { life: 3000, theme: 'growl-warning', header: '<strong>ERROR<strong>' });
						$('#usuario').focus();
					}
					return false;
				});
			}
		});
	</script>

	<div class="page-container login-container">
		<div class="page-content">
			<div class="content-wrapper">
				<div class="content">
					<form id="form-login">
						<div class="panel panel-body login-form">
							<div class="text-center">
								<img src="assets/images/logoAPV.png" class="img-responsive">
							</div>

							<div class="form-group has-feedback has-feedback-left">
								<input type="text" id="usuario" class="form-control input-lg" placeholder="Usuario" autofocus tabindex="1">
								<div class="form-control-feedback"><i class="icon-user text-muted"></i></div>
							</div>
							<div class="form-group has-feedback has-feedback-left">
								<input type="password" id="clave" class="form-control input-lg" placeholder="Contraseña" tabindex="2">
								<div class="form-control-feedback"><i class="icon-lock2 text-muted"></i></div>
							</div>
							<div id="infoLogin"></div>
							<div id="procesandoLogin"></div>
							<div class="form-group">
								<button type="button" id="login" class="btn btn-lg bg-brown btn-block">INGRESAR <i class="icon-circle-right2 position-right"></i></button>
							</div>
						</div>
					</form>
					<div class="footer text-muted">&copy; 2015 - <?= date('Y') ?> <?= nombreSistema().' '.versionSistema() ?> | <i class="fa fa-code"></i> <strong>OG - Estudio Creativo</strong> | <i class="fa fa-phone"></i> 950373240 | <i class="fa fa-map-marker"></i> Cusco - Perú</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>