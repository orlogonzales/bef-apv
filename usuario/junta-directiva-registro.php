<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES
	/////////////////////////////////////////////////////////////////////
	$ruta        ='../';
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="REGISTRO DE JUNTA DIRECTIVA";
	$menuActual="registroJuntaDirectiva";
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - HEADER
	/////////////////////////////////////////////////////////////////////
	include($ruta.'template/header.tpl');
	include($ruta.'php/conexion.php');

	session_start();
	if($_SESSION['crearJD']){
		$sesion_fechaPeriodo=$_SESSION['crearJD']['baseJD']['fechaPeriodo'];
		$sesion_idVigencia=$_SESSION['crearJD']['baseJD']['idVigencia'];
	}else{
		$sesion_fechaPeriodo="";
		$sesion_idVigencia=0;
	}
?>
	<div id="boxRegistroJuntaDirectiva"></div>
	<div id="overlay" style="display: none;"></div>
	<div id="preloader" style="display: none;">
		<div class="content-loader">
			<img src="../assets/images/preloader-md.svg" alt="Cargando...">
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function () {
			const fechaPeriodo  = '<?= $sesion_fechaPeriodo ?>';
			const idVigencia    = '<?= $sesion_idVigencia ?>';
			const urlBase       = '../modulo/modulo-jd-registro.php';
			const urlProceso    = '../php/mantenimiento-junta-directiva.php';
			const preloader     = '<div class="content-loader"><img src="../assets/images/preloader-sm.svg" alt="Cargando..."></div>';

			if(fechaPeriodo.length>0 && idVigencia>0){
				cargarAsignarIntegrantes();
			}else{
				cargarInicioRegistro();
			}

			function cargarInicioRegistro(){
				$('#overlay').show();
				const proceso = 'inicioRegistro';
				const url = urlBase;
				$.ajax({
					url: url,
					type: 'POST',
					data: { proceso: proceso },
					success: function (response) {
						$('#overlay').hide();
						$('#boxRegistroJuntaDirectiva').html(response);					
					}
				});
			}

			function cargarAsignarIntegrantes() {
				$('#overlay').show();
				const proceso = 'asignarIntegrantes';
				const url = urlBase;
				$.ajax({
					url: url,
					type: 'POST',
					data: { proceso: proceso },
					success: function (response) {
						$('#overlay').hide();
						$('#boxRegistroJuntaDirectiva').html(response);					
					}
				});
			}	
		});
	</script>
<?php include($ruta.'template/footer.tpl'); ?>