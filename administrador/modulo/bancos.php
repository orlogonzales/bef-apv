<?php
	$bancosRegistrados=infoBancos($codigoBanco,'bancosRegistrados');
?>

<script type="text/javascript">
	///////////////////////////////////////////////////
	/// VARIABLES DEL SISTEMA
	///////////////////////////////////////////////////
	var ruta='<?= $ruta ?>';

	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// AGREGAR BANCOS
		///////////////////////////////////////////////////
		$("button#bt_almacenar_entidad").click(function(){
			var datos  =$('#form_registro_bancos').serialize();
			var valida =$('.form_valida_banco').valid();
			if(valida){
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-bancos.php',
					data: datos,
					dataType:'json',
					beforeSend: function(){ $('button#bt_almacenar_entidad').prop('disabled', true); },
					success: function(respuesta){ if(respuesta.mensaje=="ENTIDAD_REGISTRADA"){ location.reload(); }}
				});
			}
		});

		///////////////////////////////////////////////////
		/// VALIDAR FORMULARIO - BANCOS
		///////////////////////////////////////////////////
		var validator = $(".form_valida_banco").validate({
			errorClass: 'validation-error-label',
			highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
			unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
			validClass: "validation-valid-label",
			rules: {
				entidad: { required: true },
			},
			messages: {
				entidad: { required: "Ingrese entidad bancaria", },
			}
		});
	});
</script>

<div class="panel">
	<div class="panel-heading bg-teal">
		<div id="procesando"></div>
		<h6 class="panel-title">REGISTRO DE BANCOS</h6>
	</div>
	<div class="panel-body info">
		<form id="form_registro_bancos" class="form_valida_banco">
			<input type="hidden" name="operacion" value="REGISTRA_BANCO">
			<div class="row">
				<div class="col-md-9 col-xs-12">
					<input type="text" id="entidad" name="entidad" required="required" class="form-control input-lg textoMayuscula" placeholder="Nombre entidad bancaria" onblur="mayusculas(event, this)" autofocus>
				</div>
				<div class="visible-xs clearfix">&nbsp;</div>
				<div class="col-md-3 col-xs-12">
					<button type="button" id="bt_almacenar_entidad" class="btn btn-lg bg-teal btn-block">Agregar</button>
				</div>
			</div>
		</form>		
	</div>
</div>

<?php if($bancosRegistrados>0){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// ELIMINAR ITEM
			///////////////////////////////////////////////////
			$('.bt_eliminar_banco').on('click', function() {
				var codigoBanco =$(this).attr('id');
				var operacion       ='ELIMINAR_BANCO';
				var datos           ='codigoBanco='+codigoBanco+'&operacion='+operacion;
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
							success: function(respuesta){
								$("#informacion").html(respuesta.informacion);
								if(respuesta.mensaje=="BANCO_ELIMINADO"){
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
	<div class="panel panel-flat border-top-xlg border-top-teal">
		<div class="table-responsive">
			<table class="table tabla table-bordered table-hover">
				<thead>
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-center">COD</th>
						<th class="text-left">ENTIDAD BANCARIA</th>
						<th class="text-center">CUENTAS</th>
						<th class="text-center">CHEQUERAS</th>
						<th class="text-center">CHEQUES</th>
						<th class="text-center">USUARIO</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT codigoBanco, entidad, fecha, hora, usuario FROM sm_bancos ORDER BY entidad ASC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoBanco     =$n[codigoBanco];
							$entidad         =$n[entidad];
							$fecha           =$n[fecha];
							$hora            =$n[hora];
							$usuario         =$n[usuario];
							$cuentasenBanco  =infoBancos($codigoBanco,'cuentasenBanco');
							$infoChequeras   =infoBancos($codigoBanco,'chequerasEmitidosBanco');
							$infoCheques     =infoBancos($codigoBanco,'chequesEmitidosBanco');
							$infoUsuario     =registradoPor($usuario,$fecha,$hora,'SI','label-default');

							if($cuentasenBanco>0){ $infoCuentasBanco=ceros($cuentasenBanco,2)." CUENTAS"; }else{ $infoCuentasBanco="NINGUNO"; }
							if($infoChequeras>0){ $infoChequerasCTA=ceros($infoChequeras,2)." GENERADAS"; }else{ $infoChequerasCTA="NINGUNO"; }
							if($infoCheques>0){ $infoChequesCTA=ceros($infoCheques,2)." EMITIDOS"; }else{ $infoChequesCTA="NINGUNO"; }

							if($cuentasenBanco>0){
								$boton='<button type="button" class="btn btn-icon btn-xs bg-warning disabled"><i class="icon-trash"></i></button>';
							} else{
								$boton='<button type="button" id="'.$codigoBanco.'" class="btn btn-icon btn-xs bg-warning bt_eliminar_banco"><i class="icon-trash"></i></button>';
							}
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-center"><?= $codigoBanco ?></td>
						<td class="text-left"><?= texto($entidad) ?></td>
						<td class="text-center"><?= $infoCuentasBanco ?></td>
						<td class="text-center"><?= $infoChequerasCTA ?></td>
						<td class="text-center"><?= $infoChequesCTA ?></td>
						<td class="text-center"><?= $infoUsuario ?></td>
						<td class="text-center"><?= $boton ?></td>
					</tr>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>
	</div>
<?php }else{ echo alerta('SIN BANCOS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUN BANCO...','text-center','bg-warning-300'); } ?>

<?php if($bancosRegistrados>0){ ?>
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
							<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
							<th class="textoNegrita text-center">FECHA</th>
							<th class="textoNegrita text-center">HORA</th>
							<th class="textoNegrita text-center">USUARIO</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT proceso, fecha, hora, usuario FROM sm_bancos_operaciones ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$proceso =$n[proceso];
								$fecha   =$n[fecha];
								$hora    =$n[hora];
								$usuario =$n[usuario];
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-left"><?= utf8_encode($proceso) ?></td>
							<td class="text-center textoMayuscula"><?= infoFecha($fecha,'normal') ?></td>
							<td class="text-center"><?= horaCorta($hora) ?></td>
							<td class="text-center"><?= utf8_encode(datoUsuario($usuario,'nombre')) ?></td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php }else{ echo alerta('SIN HISTORIAL EN BANCOS','text-center textoNegrita','NO EXISTE REGISTRADO EN EL SISTEMA, NINGUNA OPERACION EN BANCOS...','text-center','bg-warning-300'); } ?>