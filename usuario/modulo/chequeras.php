<?php
	$bancosRegistrados=infoBancos($codigoBanco,'bancosRegistrados');
	$chequerasRegistrados=infoChequeras($codigoBanco,'','','chequerasRegistrados');
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
			$("button#bt_almacenar_chequera").click(function(){
				var datos  =$('#form_registro_chequera').serialize();
				var valida =$('.form_valida_chequera').valid();
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-bancos.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){ $('button#bt_almacenar_chequera').prop('disabled', true); },
						success: function(respuesta){
							if(respuesta.mensaje=="CHEQUERA_EXISTE"){
								$('button#bt_almacenar_chequera').prop('disabled', false);
								new PNotify({title: 'ADVERTENCIA', text: 'La chequera ya existe en el sistema.', addclass: 'bg-warning'});
							}
							if(respuesta.mensaje=="CHEQUERA_REGISTRADA"){
								new PNotify({title: 'CONFIRMACION', text: 'Chequera fue registrado en el sistema.', addclass: 'bg-success'});
								location.reload();
							}
							if(respuesta.mensaje=="ERROR_CHEQUERA_REGISTRADA"){
								$('button#bt_almacenar_chequera').prop('disabled', false);
								new PNotify({title: 'ERROR', text: 'No se pudro registrar chequera, intentelo nuevamente.', addclass: 'bg-warning'});
							}
						}
					});
				}
			});
			
			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO - CHEQUERA
			///////////////////////////////////////////////////
			var validator = $(".form_valida_chequera").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
				validClass: "validation-valid-label",
				rules: {
					codigoBanco: { required: true },
					codigoCuenta: { required: true },
					detalle: { required: true },
				},
				messages: {
					codigoBanco: { required: "Seleccione entidad bancaria", },
					codigoCuenta: { required: "Seleccione cuenta bancaria", },
					detalle: { required: "Detalle de chequera", },
				}
			});

			///////////////////////////////////////////////////
			/// COMBO DINAMICO DE CUENTAS DE BANCO
			///////////////////////////////////////////////////
			$("select#codigoBanco").change(function(event){
				var codigoBanco=$("select#codigoBanco").val();
				$("#codigoCuenta").html('<option value="" selected>CARGANDO DATOS...</option>');
				$("#codigoCuenta").load(ruta+'php/lista-cuentas-banco.php?codigoBanco='+codigoBanco);
			});

			///////////////////////////////////////////////////
			/// COMBO DINAMICO DE CUENTAS DE CHEQUERAS DE CTAS
			///////////////////////////////////////////////////
			$("select#codigoCuenta").change(function(event){
				var codigoCuenta =$("select#codigoCuenta").val();
				var datos        ='codigoCuenta='+codigoCuenta;
				$.ajax({
					type: "POST",
					url: ruta+'php/lista-chequeras.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){$("#chequera").val('ASIGNANDO...'); },
					success: function(respuesta){
						$("#nroChequera").val(respuesta.chequera);
						$("#chequera").val(respuesta.mensaje);						
					}
				});
			});
		});
	</script>

	<div class="panel">
		<div class="panel-heading bg-slate"><h6 class="panel-title">REGISTRO DE CHEQUERAS</h6></div>
		<div class="panel-body info">
			<form id="form_registro_chequera" class="form_valida_chequera">
				<input type="hidden" name="operacion" value="REGISTRA_CHEQUERA">
				<div class="row">
					<div class="col-md-3 col-xs-6">
						<select id="codigoBanco" name="codigoBanco" required="required" class="form-control input-lg">
							<option value="" selected>BANCO</option>
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
					<div class="col-md-3 col-xs-6">
						<select id="codigoCuenta" name="codigoCuenta" required="required" class="form-control input-lg">
							<option value="" selected>NRO CUENTA</option>
						</select>
						<span class="label label-block bg-grey-300 text-left">Cuenta bancaria</span>
					</div>
					<div class="col-md-1 col-xs-6">
						<input type="hidden" id="nroChequera" name="nroChequera" required="required" placeholder="TALONARIO" class="form-control input-lg">
						<input disabled="disabled" id="chequera" placeholder="NRO CHEQUERA" class="form-control input-lg">
						<span class="label label-block bg-grey-300 text-left">Talonario</span>
					</div>
					<div class="visible-xs">&nbsp;</div>
					<div class="col-md-4 col-xs-9">
						<input type="text" id="detalle" name="detalle" required="required" class="form-control input-lg textoMayuscula" placeholder="Detalle de chequera" onblur="mayusculas(event, this)">
						<span class="label label-block bg-grey-300 text-left">Descripcion de chequera</span>
					</div>
					<div class="col-md-1 col-xs-3">
						<button type="button" id="bt_almacenar_chequera" class="btn btn-lg bg-slate btn-block">Agregar</button>
					</div>
				</div>
			</form>		
		</div>
	</div>
	<?php if($chequerasRegistrados>0){ ?>
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
					var operacion ='CAMBIAR_ESTADO_CHEQUERA';
					var datos     =$(this).attr('id')+'&operacion='+operacion;
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-bancos.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){ $('.bt_cambiar_estado').fadeOut('fast'); },
						success: function(respuesta){
							if(respuesta.mensaje=="ESTADO_CAMBIADO"){ location.reload(); }
						}
					});
				});

				///////////////////////////////////////////////////
				/// ELIMINAR ITEM
				///////////////////////////////////////////////////
				$('.bt_eliminar_chequera').on('click', function() {
					var operacion ='ELIMINAR_CHEQUERA';
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
								beforeSend: function(){ $('button.bt_eliminar_chequera').prop('disabled', true); },
								success: function(respuesta){
									if(respuesta.mensaje=="CHEQUERA_ELIMINADA"){
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
							<th class="text-center">COD</th>
							<th class="text-left">ENTIDAD BANCARIA</th>
							<th class="text-left">CHEQUERA</th>
							<th class="text-center">EMITIDOS</th>
							<th class="text-center">TOTAL</th>
							<th class="text-center">ESTADO</th>
							<th class="text-center">USUARIO</th>
							<th class="text-center"><i class="fa fa-align-justify"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT codigoChequera, codigoBanco, detalle, fecha, hora, usuario, estado FROM sm_chequera ORDER BY codigoChequera ASC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$codigoChequera =$n[codigoChequera];
								$codigoBanco    =$n[codigoBanco];
								$detalle        =$n[detalle];
								$fecha          =$n[fecha];
								$hora           =$n[hora];
								$usuario        =$n[usuario];
								$estado         =$n[estado];
								$banco          =infoBancos($codigoBanco,'detalleEntidad');
								$infoUsuario    =registradoPor($usuario,$fecha,$hora,'SI','label-default');
								$chequesEmitidos=infoChequeras($codigoChequera,'','','emitidosChequera');
								$totalChequera  =infoChequeras($codigoChequera,'','','totalChequera');
								
								if($chequesEmitidos>0){
									$boton='<button type="button" class="btn btn-icon btn-xs bg-warning disabled"><i class="icon-trash"></i></button>';
									$infoEmitidos='<span class="label label-success">'.ceros($chequesEmitidos,3).' CHEQUES</span>';
									$infoTotalChequera='S/.'.moneda($totalChequera);
								}else{
									$boton='<button type="button" id="codigoBanco='.$codigoBanco.'&codigoChequera='.$codigoChequera.'" class="btn btn-icon btn-xs bg-warning bt_eliminar_chequera"><i class="icon-trash"></i></button>';
									$infoEmitidos='';
									$infoTotalChequera='';
								}

								if($estado=="ACT"){ $infoEstado='<a href="javascript:;>" id="codigoBanco='.$codigoBanco.'&codigoChequera='.$codigoChequera.'&estado=INC" class="bt_cambiar_estado"><span class="label label-success">ACTIVO</span></a>'; }
								if($estado=="INC"){ $infoEstado='<a href="javascript:;>" id="codigoBanco='.$codigoBanco.'&codigoChequera='.$codigoChequera.'&estado=ACT" class="bt_cambiar_estado"><span class="label label-warning">INACTIVO</span></a>'; }
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-center"><?= $codigoChequera ?></td>
							<td class="text-left"><?= texto($banco) ?></td>
							<td class="text-left"><?= texto($detalle) ?></td>
							<td class="text-center"><?= $infoEmitidos ?></td>
							<td class="text-center text-danger"><?= $infoTotalChequera ?></td>
							<td class="text-center"><?= $infoUsuario ?></td>
							<td class="text-center"><?= $infoEstado ?></td>
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
								$sql="SELECT codigoChequera, proceso, fecha, hora, usuario FROM sm_chequera_operaciones ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$codigoChequera =$n[codigoChequera];
									$proceso =$n[proceso];
									$fecha   =$n[fecha];
									$hora    =$n[hora];
									$usuario =$n[usuario];
							?>					
							<tr>
								<td class="text-center"><?= ceros($i,2) ?></td>
								<td class="text-center"><?= $codigoChequera ?></td>
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
	<?php }else{ echo alerta('SIN CHEQUERAS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUNA CHEQUERA...','text-center','bg-warning-300'); } ?>
<?php }else{ echo alerta('IMPOSIBLE REGISTRAR CHEQUERAS','text-center textoNegrita','PARA AGREGAR CHEQUERAS PRIMERO DEBE REGISTRAR ENTIDADES BANCARIAS....','text-center','bg-warning-300'); } ?>