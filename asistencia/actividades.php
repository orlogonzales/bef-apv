<?php
	$ruta ="../";
	include_once ($ruta."/php/funciones.php");

	if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'){
		$urlSistema   = 'https://'.$_SERVER['SERVER_NAME'].'/apv/';
	}else{
		$urlSistema   = 'http://'.$_SERVER['SERVER_NAME'].'/apv/';
	}

	$urlSistemaActividades=$urlSistema.'asistencia/';
	$urlSistemaActividadesAsistencia=$urlSistema.'asistencia/asistencia.php';
	$urlSistemaTerminarActividad=$urlSistema.'asistencia/terminar-asistencia.php';

	session_start();
	if(empty($_SESSION)){
		header('Location: '.$urlSistemaActividades);
	}else{
		if($_SESSION['codigoActividad']!='' && $_SESSION['estadoActividad']=='abierto'){
			header('Location: '.$urlSistemaActividadesAsistencia);
		}
		if($_SESSION['codigoActividad']!='' && $_SESSION['estadoActividad']=='terminado'){
			header('Location: '.$urlSistemaTerminarActividad);
		}
	}

	$hoy = infoTiempo('fecha');
	$hora=infoTiempo('hora');
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
		$(document).ready(function(){
			$('#salirActividades').click(function(){
				event.preventDefault();
				var operacion = "salirActividades";
				var datos     = 'operacion='+operacion;
				var ruta      = '<?= $urlSistema ?>api/index.php';
				swal(
					{
						title: "¿Deseas salir?",
						text: "lista de Activades...",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "No, Salir",
						cancelButtonText: "Si, salir",
						closeOnConfirm: true,
						closeOnCancel: false
					},

					function(isConfirm){
						console.log(isConfirm);
						if (!isConfirm) {
							$.ajax({
								type: "POST",
								url: ruta,
								data: datos,
								cache: false,
								dataType:'json',
								success: function(respuesta){
									console.log(respuesta);
									var urlSistemaActividades = '<?= $urlSistemaActividades ?>';
									if(respuesta.resultado=='OK'){
										window.location.replace(urlSistemaActividades);
									}
									swal("¡Hecho!","Acabas de cerrar sesión","success");
								}
							});
						}
					}
				);
			});

			$('#btn_iniciar_control').click(function(){
				event.preventDefault();
				var codigoActividad = $(this).data("id");
				var actividad = $(this).data("actividad");
				var formaActividad= $(this).data("forma");
				var operacion = "inicarControl";
				var datos     = 'codigoActividad='+codigoActividad+'&formaActividad='+formaActividad+'&operacion='+operacion;
				var ruta      = '<?= $urlSistema ?>api/index.php';
				swal(
					{
						title: "¿INICIAR CONTROL?",
						text: actividad,
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#DD6B55",
						confirmButtonText: "No, Cancelar",
						cancelButtonText: "Si, Continuar",
						closeOnConfirm: true,
						closeOnCancel: false
					},

					function(isConfirm){
						console.log(isConfirm);
						if (!isConfirm) {
							$.ajax({
								type: "POST",
								url: ruta,
								data: datos,
								cache: false,
								dataType:'json',
								success: function(respuesta){
									console.log(respuesta);
									var urlSistemaActividades = '<?= $urlSistemaActividades ?>';
									if(respuesta.resultado=='OK'){
										window.location.replace(urlSistemaActividades);
									}
									swal("¡Hecho!","Acabas de cerrar sesión","success");
								}
							});
						}
					}
				);
			});

			$('.activa').prop('disabled', false);
			$('.desactiva').prop('disabled', true);
		});
	</script>
</head>
<body style="overflow: hidden;">
<div id="fullscreen">
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			<div class="navbar-brand"><img src="<?= $urlSistema ?>assets/images/logo_light.png" alt=""></div>
		</div>
		<button type="button" id="salirActividades" class="btn btn-xs btn-danger pull-right" style="margin-top: 5px !important;">SALIR</button>
	</div>
	<div class="login-container">
		<div class="page-content">
			<div class="content-wrapper">
				<div class="content">
					<div class="row" style="margin: 0; position: absolute; top: 50%; width: 100%; -ms-transform: translateY(-50%); transform: translateY(-50%);">
						<div class="col-lg-8 col-lg-offset-2">
							<div class="panel">
								<div class="panel-heading bg-slate-600">
									<div id="procesando"></div>
									<h6 class="panel-title"><strong>LISTA DE ACTIVIDADES PROGRAMADAS</strong></h6>
								</div>
								<div class="panel-body info">
									<?php
										$conexion=conexionDB();
										//$sql="SELECT tipoActividad, codigoActividad, temaActividad, formaActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta FROM sm_mod_actividades WHERE fechaActividad >= CURDATE() AND horaActividad >= CURTIME() ORDER BY fechaActividad DESC, horaActividad DESC";
										$sql="SELECT tipoActividad, codigoActividad, temaActividad, formaActividad, fechaActividad, horaActividad, lugarActividad, mTardanza, mFalta FROM sm_mod_actividades WHERE fechaActividad >= CURDATE()";
										$rs=mysqli_query($conexion,$sql);
										$cantActividades=mysqli_num_rows($rs);
										if($cantActividades>0){
									?>
									<table class="table tabla table-bordered table-hover tabla-lista-faenas text-bold">
										<thead>
											<tr class="success">
												<th class="text-center">#</th>
												<th class="text-left">ACTIVIDAD</th>
												<th class="text-center">FECHA</th>
												<th class="text-center">INICIO</th>
												<th class="text-center">REGISTRO</th>
												<th class="text-center">AFORO</th>
												<th class="text-center"><i class="fa fa-align-justify"></i></th>
											</tr>
										</thead>
										<tbody>
											<?php
												$i=1;
												while($n=mysqli_fetch_array($rs)){
													$codigoActividad =$n[codigoActividad];
													$tipoActividad   =$n[tipoActividad];
													$temaActividad   =$n[temaActividad];
													$formaActividad  =$n[formaActividad];
													$fechaActividad  =$n[fechaActividad]; 
													$horaActividad   =$n[horaActividad]; 
													$lugarActividad  =$n[lugarActividad]; 
													$aforo           =infoActividad($idJuntaDirectiva,$codigoActividad,'','aforo');

													if($formaActividad==1){
														$asistencia='<span class="label bg-success">AUTOMATICO</span>';
													}else{
														$asistencia='<span class="label bg-warning">OFFLINE - JSON</span>';
													}

													if($fechaActividad==$hoy){
														$estado="activa";
													}else{
														$estado="desactiva";
													}
											?>
											<tr>
												<td class="text-center"><?= ceros($i,2) ?></td>
												<td class="text-left"><?= texto($temaActividad) ?></td>
												<td class="text-center textoMayuscula"><?= infoFecha($fechaActividad,'normal') ?></td>
												<td class="text-center textoMayuscula"><?= infoHora(horaCorta($horaActividad)) ?></td>
												<td class="text-center text-danger"><strong><?= $asistencia ?></strong></td>
												<td class="text-center"><span class="label bg-brown"><?= ceros($aforo,4) ?> SOCIOS</span></td>
												<td class="text-center">
													<?php if($formaActividad==1){ ?>
														<button type="button" id="btn_iniciar_control" data-id="<?= $codigoActividad ?>" data-actividad="<?= $temaActividad ?>" data-forma="<?= $formaActividad ?>" class="btn btn-xs btn-secondary <?= $estado ?>">INICIAR</button>
													<?php }else{ ?>
														<button type="button" id="btn_carga_asistentes_iniciar_control" class="btn btn-xs btn-secondary <?= $estado ?>">INICIAR</button>
													<?php } ?>
												</td>
											</tr>
											<?php $i++; } cerrarDB() ?>
										</tbody>
									</table>
									<?php }else{ ?>
										<div class="alert alert-danger mb-0">
											<strong>¡SIN ACTIVIDADES!</strong> AUN NO HAY ACTIVIDADES PARA SER LISTADOS.
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
<?php include('template/footer.tpl') ?>