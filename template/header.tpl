<?php 
	include_once $ruta."php/funciones.php";
	session_start();
	set_time_limit(300);
	ini_set("memory_limit","512M");
	$conexion          = conexionDB();
	$login             = $_SESSION['login_apv'];
	$dniUsuario        = $_SESSION['dni_apv'];
	$rolUsuario        = $_SESSION['rol_apv'];
	$descripcionRolAPV = $_SESSION['descripcionRol'];
	$infoJDAPV         = $_SESSION['infoJDAPV'];

	if(!isset($_SESSION['login_apv'])){
		header("location: ../index.php");
	}

	if($rolUsuario=='ADM' || $rolUsuario=='JDP'){
		$permitido="SI";
	}
	
	$detalleRol    =datoTabla($rolUsuario,"detalleRol");
	$nombreUsuario =datoUsuario($dniUsuario,"nombre");
	$nombrePaterno =datoUsuario($dniUsuario,"nombrePaterno");
	$fotoUsuario   =datoUsuario($dniUsuario,"foto");
	$sesion        =verificaEstadoActividad($dniUsuario);
	$URlSistema    =RUTA;
	$directorio_actual = explode('\\', getcwd());
	$directio_usuario=$directorio_actual[count($directorio_actual)-1];
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo nombreSistema().' '.versionSistema() ?></title>
	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/icons/fontawesome/styles.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/meeting.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/minified/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/minified/core.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/minified/components.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/minified/colors.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/css/add.css" rel="stylesheet" type="text/css">
	<!-- Core JS files -->
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/loaders/pace.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/core/libraries/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/loaders/blockui.min.js"></script>
	<!-- Theme JS files -->
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/visualization/d3/d3.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/visualization/d3/d3_tooltip.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/styling/switchery.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/styling/uniform.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/selects/bootstrap_multiselect.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/ui/moment/moment.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/media/fancybox.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/tables/datatables/datatables.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/selects/select2.min.js"></script>
	<!-- Theme JS files -->
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/notifications/bootbox.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/notifications/sweet_alert.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/notifications/pnotify.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/notifications/jgrowl.min.js"></script>

	<!-- DATEPICKER -->
	<script type="text/javascript" src="<?= $ruta ?>assets/js/core/libraries/jquery_ui/datepicker.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/pickers/pickadate/picker.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/pickers/pickadate/picker.date.js"></script>

	<!-- VALIDACION DE FORMULARIOS -->
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/validation/validate.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/inputs/touchspin.min.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/forms/styling/switch.min.js"></script>
	
	<!-- ALERTAS -->
	<link href="<?= $ruta ?>assets/js/plugins/alertifyjs/css/alertify.min.css" rel="stylesheet" type="text/css">
	<link href="<?= $ruta ?>assets/js/plugins/alertifyjs/css/themes/bootstrap.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/alertifyjs/alertify.min.js"></script>

	<!-- SWEET ALERT -->
	<link href="<?= $ruta ?>assets/js/plugins/sweetalert/sweet-alert.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="<?= $ruta ?>assets/js/plugins/sweetalert/sweet-alert.min.js"></script>

	<!-- SCRIPTS DEL SISTEMA -->
	<script type="text/javascript" src="<?= $ruta ?>assets/js/core/sistema.js"></script>
	<script type="text/javascript" src="<?= $ruta ?>assets/js/core/app.js"></script>
</head>
<body class="navbar-top sidebar-xs">
	<?php include($ruta.'template/menu-top.tpl'); ?>
	<div class="page-container">
		<div class="page-content">
			<?php include($ruta.'template/sidebar.tpl'); ?>
			<div class="content-wrapper">
				<?php include($ruta.'template/pagina-header.tpl'); ?>
				<div class="content">