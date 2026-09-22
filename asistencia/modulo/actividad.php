<?php
	session_start();
	include('../php/funciones.php');
	$opcion          =$_GET[opcion];
	$codigoActividad =$_GET[codigoActividad];
	$tipoActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','tipoActividad');
	$horaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','horaActividad');

	if($tipoActividad=="ASA"){ $actividad="ASAMBLEA"; }
	if($tipoActividad=="FAE"){ $actividad="FAENA"; }
?>


<?php if($opcion=="LISTA_ASISTENTES"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CONFIGURACION DE TABLA
			///////////////////////////////////////////////////
			//$('.datatable').DataTable();
			$('.datatable').DataTable({ autoWidth: true, scrollY: 300 });
			 
			$.extend( $.fn.dataTable.defaults, {autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 8 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Filtro:</span> _INPUT_', lengthMenu: '<span>Mostrar:</span> _MENU_', paginate: { 'Primero': 'Primero', 'Ultimo': 'Ultimo', 'Siguiente': '&rarr;', 'Anterior': '&larr;' } }, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
			$('.dataTables_filter input[type=search]').attr('placeholder','Type to filter...');
			$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
		});
	</script>

	<table class="table table-bordered table-striped datatable" width="100%">
		<thead>
			<tr class="success">
				<th class="text-center text-bold">#</th>
				<th class="text-center text-bold">CODIGO SOCIO</th>
				<th class="text-left text-bold">NOMBRE SOCIO</th>
				<th class="text-center text-bold">INGRESO</th>
				<th class="text-center text-bold">TARDANZA</th>
				<th class="text-center text-bold">SALIDA</th>
				<th class="text-center text-bold">MULTA</th>
				<th class="text-center text-bold">ESTADO</th>
			</tr>
		</thead>
		<tbody>

			<?php
				$conexion=conexionDB();
				$sql="SELECT sm_terminal_asistencia.codigoSocio AS codigoSocio, sm_terminal_asistencia.ingreso AS ingreso, sm_terminal_asistencia.retraso AS retraso, sm_terminal_asistencia.salida AS salida, sm_terminal_socios.nombre AS nombre, sm_terminal_socios.apPaterno AS apPaterno, sm_terminal_socios.apMaterno AS apMaterno, sm_terminal_socios.lotes AS lotes FROM sm_terminal_asistencia, sm_terminal_socios WHERE sm_terminal_asistencia.codigoSocio=sm_terminal_socios.codigoSocio AND codigoActividad='$codigoActividad' ORDER BY sm_terminal_socios.apPaterno ASC";
				$rs=mysqli_query($conexion,$sql);
				$i=1;
				while($n=mysqli_fetch_array($rs)){
					$codigoSocio =$n[codigoSocio];
					$nombre      =$n[nombre];
					$apPaterno   =$n[apPaterno];
					$apMaterno   =$n[apMaterno];
					$lotes       =$n[lotes];
					$ingreso     =$n[ingreso];
					$retraso     =$n[retraso];
					$salida      =$n[salida];
					$nombreSocio =$apPaterno." ".$apMaterno."".$nombre;
					$multaTarde  =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');

					if($retraso>0.15){
						$retrasoHoras=infoRetraso($retraso,'hora');
						$retrasoMinutos=infoRetraso($retraso,'minuto');
						$infoRetraso=ceros($retrasoHoras,2).':'.$retrasoMinutos.' MIN';
						$estado='<span class="label label-warning">TARDE</span>';
						$infoMulta="S/. ".moneda($lotes*$multaTarde);
					}else{
						$infoRetraso='';
						$estado='<span class="label label-success">PUNTUAL</span>';
						$infoMulta="";
					}
			?>
			<tr>
				<td class="text-center"><?= ceros($i,3) ?></td>
				<td class="text-center"><?= $codigoSocio ?></td>
				<td class="text-left"><?= $nombreSocio ?></td>
				<td class="text-center"><?= $ingreso ?></td>
				<td class="text-center text-danger text-bold"><?= $infoRetraso ?></td>
				<td class="text-center"><?= $salida ?></td>
				<td class="text-center text-danger text-bold"><?= $infoMulta ?></td>
				<td class="text-center"><?= $estado ?></td>
			</tr>
			<?php $i++; } cerrarDB(); ?>
		</tbody>
	</table>
<?php } ?>

<?php if($opcion=="CAMBIO_HORA"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CAMBIO DE ESTILO DE INPUT FILE
			///////////////////////////////////////////////////
			$('.seleccionar').select2();

			///////////////////////////////////////////////////
			/// CERRAR VENTANA OCULTO
			///////////////////////////////////////////////////
			$('#cerrarVentana').hide();

			///////////////////////////////////////////////////
			/// BOTON ACTUALIZAR HORA DE ACTIVIDAD
			///////////////////////////////////////////////////
			$("button#actualizarHora").click(function(){
				var actividad       ='<?= $actividad ?>';
				var operacion       ='ACTUALIZAR_HORA';
				var codigoActividad ='<?= $codigoActividad ?>';
				var horaActividad   =$('select#horaActividad').val();
				var datos           ='codigoActividad='+codigoActividad+'&horaActividad='+horaActividad+'&operacion='+operacion;
				$.ajax({
					type: "POST",
					url: 'php/mantenimiento-actividad.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){
						$('#moduloHora').fadeIn("slow").html('<div class="row mb-15"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>ACTUALIZANDO HORA</div></div>').fadeIn(1000).delay(1000);
					},
					success: function(respuesta){
						if(respuesta.mensaje=='HORA_ACTUALIZADA'){
							$('#cerrarVentana').fadeIn(600);
							$('#moduloHora').fadeIn("slow").html('<div class="alert alert-success alert-styled-left alert-arrow-left alert-bordered">LA HORA DE '+actividad+' FUE CAMBIADA</div>').fadeIn(1000).delay(1000);
						}
						if(respuesta.mensaje=='ERROR_REGISTRO'){
							$.jGrowl('Error, imposible actualizar por favor intentelo nuevamente.', { header: 'Error', theme: 'alert-styled-left bg-danger-300' });
						}
					}
				});
			});
		});

	</script>
	<div class="row">
		<div class="col-lg-6 col-xs-6">
			<div class="form-group">
				<select id="horaActividad" class="seleccionar select-lg">
					<option value="" selected>SELECCIONE HORA</option>
					<?php
						$conexion=conexionDB();
						$sql="SELECT horas FROM sm_a_horas WHERE horas>='$horaActividad'";
						$rs=mysqli_query($conexion,$sql);
						while($datos=mysqli_fetch_array($rs)){
							if($datos[0]==$horaActividad){
								echo '<option value="'.$datos[0].'" selected>'.$datos[0].'</option>';
							}else{
								echo '<option value="'.$datos[0].'">'.$datos[0].'</option>';
							}
						}
						 cerrarDB();
					?>
				</select>
			</div>
		</div>
		<div class="col-lg-6 col-xs-6">
			<div class="form-group">
				<button type="button" id="actualizarHora" class="btn btn-lg btn-success btn-block">ACTUALIZAR</button>
			</div>
		</div>
	</div>
<?php } ?>