<?php
	$bancosRegistrados=infoBancos($codigoBanco,'bancosRegistrados');
	$cuentasRegistradas=infoCuentas('','','cuentasRegistradas');
?>

<?php if($bancosRegistrados>0){ ?>
	<script type="text/javascript">
		///////////////////////////////////////////////////
		/// VARIABLES DEL SISTEMA
		///////////////////////////////////////////////////
		var ruta='<?= $ruta ?>';

		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// AGREGAR CHEQUERA
			///////////////////////////////////////////////////
			$("button#bt_almacenar_cuenta").click(function(){
				var datos  =$('#form_registra_cuenta').serialize();
				var valida =$('.form_valida_cuenta').valid();
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-bancos.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){ $('button#bt_almacenar_cuenta').prop('disabled', true); },
						success: function(respuesta){
							if(respuesta.mensaje=="CUENTA_EXISTE"){
								$('button#bt_almacenar_cuenta').prop('disabled', false);
								new PNotify({title: 'ADVERTENCIA', text: 'La cuenta ya existe en el sistema.', addclass: 'bg-warning'});
							}
							if(respuesta.mensaje=="CUENTA_REGISTRADA"){
								new PNotify({title: 'CONFIRMACION', text: 'La cuenta fue registrada en el sistema.', addclass: 'bg-success'});
								location.reload();
							}
							if(respuesta.mensaje=="ERROR_REGISTRO"){
								new PNotify({title: 'ERROR', text: 'Por favor intentelo nuevamente.', addclass: 'bg-warning'});
							}
						}
					});
				}
			});
			
			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO - CHEQUERA
			///////////////////////////////////////////////////
			var validator = $(".form_valida_cuenta").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
				validClass: "validation-valid-label",
				rules: {
					codigoBanco: { required: true },
					numeroCuenta: { required: true },
					detalle: { required: true },
				},
				messages: {
					codigoBanco: { required: "Seleccione entidad bancaria", },
					numeroCuenta: { required: "Ingrese nro. de cuenta", },
					detalle: { required: "Detalle de cuenta", },
				}
			});
		});
	</script>

	<div class="panel">
		<div class="panel-heading bg-slate"><h6 class="panel-title">REGISTRO DE CUENTAS BANCARIAS</h6></div>
		<div class="panel-body info">
			<form id="form_registra_cuenta" class="form_valida_cuenta">
				<input type="hidden" name="operacion" value="REGISTRA_CUENTA">
				<div class="row">
					<div class="col-md-6 col-xs-6">
						<div class="form-group">
							<select id="codigoBanco" name="codigoBanco" required="required" class="form-control input-lg">
								<option value="" selected>SELECCIONE ENTIDAD BANCARIA</option>
								<?php
									$sql="SELECT codigoBanco, entidad FROM sm_bancos ORDER BY entidad ASC";
									$rs=mysqli_query($conexion,$sql);
									while($datos=mysqli_fetch_array($rs)){
										$codigoBanco =$datos[codigoBanco];
										$entidad     =$datos[entidad];
										echo '<option value="'.$codigoBanco.'">'.texto($entidad).'</option>';
									}
								?>
							</select>
							<span class="label label-block bg-grey-300 text-left">Entidad bancaria</span>
						</div>
					</div>
					<div class="col-md-6 col-xs-6">
						<div class="form-group">
							<input type="text" id="numeroCuenta" name="numeroCuenta" required="required" class="form-control input-lg textoMayuscula" placeholder="Numero de cuenta" onblur="mayusculas(event, this)">
							<span class="label label-block bg-grey-300 text-left">Numero de cuenta</span>
						</div>
					</div>
					<div class="col-md-12 col-xs-12">
						<div class="form-group">
							<input type="text" id="detalle" name="detalle" required="required" class="form-control input-lg textoMayuscula" placeholder="Detalle de cuenta" onblur="mayusculas(event, this)">
						</div>
					</div>
					<div class="col-md-12 col-xs-12">
							<button type="button" id="bt_almacenar_cuenta" class="btn btn-lg bg-slate btn-block">Agregar Cuenta</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?php if($cuentasRegistradas>0){ ?>
		<script type="text/javascript">
			///////////////////////////////////////////////////
			/// VARIABLES DEL SISTEMA
			///////////////////////////////////////////////////
			var ruta='<?= $ruta ?>';
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CAMBIAR ESTADO DE CHEQUERA
				///////////////////////////////////////////////////
				$(".bt_cambiar_estado").click(function(){
					var operacion ='CAMBIAR_ESTADO_CUENTA';
					var datos     =$(this).attr('id')+'&operacion='+operacion;
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-bancos.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){ $('.bt_cambiar_estado').fadeOut('fast'); },
						success: function(respuesta){
							if(respuesta.mensaje=="ESTADO_CAMBIADO"){ location.reload(); }
							if(respuesta.mensaje=="ERROR_ESTADO_CAMBIADO"){
								new PNotify({title: 'ERROR', text: 'Por favor intentelonuevamente.', addclass: 'bg-success'});
							}
						}
					});
				});

				///////////////////////////////////////////////////
				/// ELIMINAR ITEM
				///////////////////////////////////////////////////
				$('.bt_eliminar_cuenta').on('click', function() {
					var operacion ='ELIMINAR_CUENTA';
					var datos     =$(this).attr('id')+'&operacion='+operacion;
					swal({
						title: "Eliminar",
						text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Eliminar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-bancos.php',
								data: datos,
								dataType:'json',
								beforeSend: function(){ $('button.bt_eliminar_cuenta').prop('disabled', true); },
								success: function(respuesta){
									if(respuesta.mensaje=="CUENTA_ELIMINADA"){
										new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
										location.reload();
									}
								}
							});
						}
					});
				});
			});
		</script>

		<div class="panel panel-flat border-top-xlg border-top-slate">
			<div class="table-responsive">
				<table class="table tabla table-bordered table-hover">
					<thead>
						<tr class="success">
							<th class="text-center">#</th>
							<th class="text-left">ENTIDAD</th>
							<th class="text-center">NUMERO DE CUENTA</th>
							<th class="text-left">DETALLE DE CUENTA</th>
							<th class="text-center">CHEQUERAS</th>
							<th class="text-center">CHEQUES</th>
							<th class="text-center">ESTADO</th>
							<th class="text-center">USUARIO</th>
							<th class="text-center"><i class="fa fa-align-justify"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT codigoCuenta, codigoBanco, numeroCuenta, detalle, fecha, hora, usuario, estado FROM sm_banco_cuentas ORDER BY id ASC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$codigoCuenta    =$n[codigoCuenta];
								$codigoBanco     =$n[codigoBanco];
								$numeroCuenta    =$n[numeroCuenta];
								$detalle         =$n[detalle];
								$fecha           =$n[fecha];
								$hora            =$n[hora];
								$usuario         =$n[usuario];
								$estado          =$n[estado];
								$banco           =infoBancos($codigoBanco,'detalleEntidad');
								$chequerasCTA    =infoCuentas($codigoCuenta,$codigoBanco,'chequerasEmitidasCTA');
								$chequesEmitidos =infoCuentas($codigoCuenta,$codigoBanco,'chequesEmitidosCTA');
								$infoUsuario     =registradoPor($usuario,$fecha,$hora,'SI','label-default');

								if($chequerasCTA>0){
									$infoChequeras=ceros($chequerasCTA,2)." CHEQUES";
									$boton='<button type="button" class="btn btn-icon btn-xs bg-warning disabled"><i class="icon-trash"></i></button>';
								}else{
									$infoChequeras="NINGUNO";
									$boton='<button type="button" id="codigoBanco='.$codigoBanco.'&codigoCuenta='.$codigoCuenta.'" class="btn btn-icon btn-xs bg-warning bt_eliminar_cuenta"><i class="icon-trash"></i></button>';
								}

								if($chequesEmitidos>0){ $infoChequesEmitidos=ceros($chequesEmitidos,2)." CHEQUES"; }else{ $infoChequesEmitidos="NINGUNO"; }
								if($estado=="ACT"){ $infoEstado='<a href="javascript:;>" id="codigoBanco='.$codigoBanco.'&codigoCuenta='.$codigoCuenta.'&estado=INC" class="bt_cambiar_estado"><span class="label label-success">ACTIVO</span></a>'; }
								if($estado=="INC"){ $infoEstado='<a href="javascript:;>" id="codigoBanco='.$codigoBanco.'&codigoCuenta='.$codigoCuenta.'&estado=ACT" class="bt_cambiar_estado"><span class="label label-warning">INACTIVO</span></a>'; }
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-left"><?= texto($banco) ?></td>
							<td class="text-center text-danger text-bold"><?= $numeroCuenta ?></td>
							<td class="text-left"><?= texto($detalle) ?></td>
							<td class="text-center"><?= $infoChequeras ?></td>
							<td class="text-center"><?= $infoChequesEmitidos ?></td>
							<td class="text-center"><?= $infoEstado ?></td>
							<td class="text-center"><?= $infoUsuario ?></td>
							<td class="text-center"><?= $boton ?></td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CONFIGURACION DE TABLAS
				///////////////////////////////////////////////////
				$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 4 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
				var lastIdx = null;
				var table = $('.tabla-operaciones').DataTable({ 'pageLength': 20 });
				$('.tabla-operaciones tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
				$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
				$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
			});
		</script>
		<div class="panel panel-flat border-top-xlg border-top-warning">
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tabla-info table-bordered table-hover tabla-operaciones">
						<thead>
							<tr class="warning">
								<th class="textoNegrita text-center">#</th>
								<th class="textoNegrita text-center">CHEQUERA</th>
								<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
								<th class="textoNegrita text-center">FECHA</th>
								<th class="textoNegrita text-center">HORA</th>
								<th class="textoNegrita text-center">USUARIO</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="SELECT codigoCuenta, proceso, fecha, hora, usuario FROM sm_banco_cuentas_operaciones ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$codigoCuenta =$n[codigoCuenta];
									$proceso =$n[proceso];
									$fecha   =$n[fecha];
									$hora    =$n[hora];
									$usuario =$n[usuario];
							?>					
							<tr>
								<td class="text-center"><?= ceros($i,2) ?></td>
								<td class="text-center"><?= $codigoCuenta ?></td>
								<td class="text-left"><?= texto($proceso) ?></td>
								<td class="text-center textoMayuscula"><?= infoFecha($fecha,'normal') ?></td>
								<td class="text-center"><?= horaCorta($hora) ?></td>
								<td class="text-center"><?= texto(datoUsuario($usuario,'nombre')) ?></td>
							</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php }else{ echo alerta('SIN CUENTAS BANCARIAS REGISTRADAS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUNA CUENTA BANCARIA POR NINGUN CONCEPTO DE DEPOSITO...','text-center','bg-warning-300'); } ?>
<?php }else{ echo alerta('IMPOSIBLE REGISTRAR CHEQUERAS','text-center textoNegrita','PARA AGREGAR CHEQUERAS PRIMERO DEBE REGISTRAR ENTIDADES BANCARIAS....','text-center','bg-warning-300'); } ?>