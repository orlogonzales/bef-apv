<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>REGISTRO DE ASISTENCIA</title>
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/minified/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/minified/core.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/minified/components.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/minified/colors.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/meeting.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/loaders/blockui.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/velocity/velocity.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/velocity/velocity.ui.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/buttons/spin.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/buttons/ladda.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/selects/select2.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/core/app.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/fullscreen/jquery.fullscreen.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/notifications/pnotify.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/notifications/noty.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/notifications/jgrowl.min.js"></script>
	<script type="text/javascript">
		$(function() {
			$('#fullscreen #exitfullscreen').hide();

			$('#support').text($.fullscreen.isNativelySupported() ? 'supports' : 'doesn\'t support');

			$('#fullscreen #requestfullscreen').click(function() {
				$('#fullscreen').fullscreen();
				return false;
			});

			$('#fullscreen #exitfullscreen').click(function() {
				$.fullscreen.exit();
				return false;
			});

			$(document).bind('fscreenchange', function(e, state, elem) {
				if ($.fullscreen.isFullScreen()) {
					$('#fullscreen #requestfullscreen').hide();
					$('#fullscreen #exitfullscreen').show();
				} else {
					$('#fullscreen #requestfullscreen').show();
					$('#fullscreen #exitfullscreen').hide();
				}
				$('#state').text($.fullscreen.isFullScreen() ? '' : 'not');
			});
		});
	</script>
</head>
<body>
<div id="fullscreen">
	<div class="navbar navbar-inverse">
		<button type="button" id="requestfullscreen" class="btn btn-lg btn-link pull-right"><i class="icon-screen3"></i></button>
		<button type="button" id="exitfullscreen" class="btn btn-lg btn-link pull-right"><i class="icon-screen3"></i></button>

		<a href="socios.php?modulo=controlAsistencia" class="btn btn-lg btn-link pull-right"><i class="icon-users"></i></a>
		<a href="reporte.php?codigoActividad=<?= $codigoActividad ?>&modulo=controlAsistencia" class="btn btn-lg btn-link pull-right"><i class="icon-cog3"></i></a>
		<?php if($_GET[modulo]=="controlAsistencia"){ ?>
			<a href="index.php?opcion=asistencia" class="btn btn-lg btn-link pull-right"><i class="icon-esc"></i></a>
		<?php }else{} ?>
		<div class="navbar-header">
			<div class="navbar-brand"><img src="<?= $ruta ?>assets/images/logo_light.png" alt=""></div>
		</div>
		
	</div>
	<div class="login-container">
		<div class="page-content">
			<div class="content-wrapper">
				<div class="content"><br><br><br><br><br>