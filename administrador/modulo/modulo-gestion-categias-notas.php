<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$conexion=conexionDB();
?>
	<form id="form_registro_categoria">
		<input type="hidden" name="operacion" value="REGISTRA_CHEQUERA">
		<div class="row">
			<div class="col-md-9 col-xs-9">
				<input type="text" id="categoria" name="categoria" required="required" class="form-control input-lg textoMayuscula" placeholder="Nombre de categoria" onblur="mayusculas(event, this)">
			</div>
			<div class="col-md-3 col-xs-3">
				<input type="hidden" name="operacion" value="AGREGA_CATEGORIA_NOTA">
				<button type="button" id="bt_almacenar_categoria" class="btn btn-lg bg-slate btn-block">Agregar</button>
			</div>
		</div>
	</form>

	<hr>
	<?php
		$sql="SELECT id, categoria, fecha, hora, usuario FROM sm_notas_categorias ORDER BY id DESC";
		$rs=mysqli_query($conexion,$sql);
		$contar=mysqli_num_rows($rs);
		if($contar>0){
	?>
	<div class="table-responsive">
		<table class="table tabla-info table-bordered table-hover tabla-lista-categorias">
			<thead>
				<tr class="success">
					<th class="textoNegrita text-center">#</th>
					<th class="textoNegrita text-left">DETALLE DE CATEGORIA</th>
					<th class="textoNegrita text-center">REGISTRADO</th>
					<th class="textoNegrita text-center"><i class="icon-menu7"><i/></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$i=1;
					while($n=mysqli_fetch_array($rs)){
						$id           =$n[id];
						$categoria    =$n[categoria];
						$fecha        =$n[fecha];
						$hora         =$n[hora];
						$usuario      =$n[usuario];
						$infoRegistro =registradoPor($usuario,$fecha,$hora,'NO','');
				?>
				<tr>
					<td class="text-center"><?= ceros($i,2) ?></td>
					<td class="text-left"><?= texto($categoria) ?></td>
					<td class="text-center"><span class="label label-default"><?= $infoRegistro.' | '.horaCorta($hora) ?></span></td>
					<td class="text-center">
						<button type="button" id="<?= $id ?>" class="btn btn-xs btn-warning btn-icon btn-rounded bt_elimina_categoria_nota"><i class="icon-trash"></i></button>
					</td>
				</tr>
				<?php $i++; } ?>
			</tbody>
		</table>
	</div>
	<?php
		}else{
			echo cajaAlerta('SIN DATOS','text-center textoNegrita','No se ha encontrado, ningúna categoria registrada en el sistema.','text-center','bg-danger');
		}
	?>

	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// AGREGAR CHEQUERA
			///////////////////////////////////////////////////
			$("button#bt_almacenar_categoria").click(function(){
				var ruta   ='../';
				var datos  =$('#form_registro_categoria').serialize();
				var valida =$('#form_registro_categoria').valid();
				if(valida){
					$.ajax({
						type: 'POST',
						url: ruta+'php/mantenimiento-usuarios.php',
						data: datos,
						dataType:'json',
						beforeSend: function(){ $('button#bt_almacenar_categoria').prop('disabled', true); },
						success:function(respuesta){
							if(respuesta.mensaje=="CATEGORIA_AGREGADA_NOTA"){
								new PNotify({title: 'ALMACENADO', text: 'categoria de nota agregada a la BD.', addclass: 'bg-success'});
								$('#modulo_gestion_categiorias').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>').fadeIn(1000).delay(1000);
								$("#modulo_gestion_categiorias").fadeIn("slow").load(ruta+'administrador/modulo/modulo-gestion-categias-notas.php').fadeIn(1000).delay(1000);
							}
							if(respuesta.mensaje=="ERROR_CATEGORIA_AGREGADA_NOTA"){
								new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// ELIMINAR ITEM
			///////////////////////////////////////////////////
			$('.bt_elimina_categoria_nota').on('click', function() {
				var ruta   ='../';
				var operacion ='ELIMINAR_CATEGORIA_NOTA';
				var datos     ='id='+$(this).attr('id')+'&operacion='+operacion;
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
							url: ruta+'php/mantenimiento-usuarios.php',
							data: datos,
							dataType:'json',
							beforeSend: function(){ $('button.bt_eliminar_chequera').prop('disabled', true); },
							success: function(respuesta){
								if(respuesta.mensaje=="CATEGORIA_ELIMINADA_NOTA"){
									new PNotify({title: 'CONFIRMACION', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
									$('#modulo_gestion_categiorias').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>').fadeIn(1000).delay(1000);
									$("#modulo_gestion_categiorias").fadeIn("slow").load(ruta+'administrador/modulo/modulo-gestion-categias-notas.php').fadeIn(1000).delay(1000);
								}
							}
						});
					}
				});
			});
			
			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO - CHEQUERA
			///////////////////////////////////////////////////
			var validator = $("#form_registro_categoria").validate({
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
				validClass: "validation-valid-label",
				rules: {
					categoria: { required: true },
				},
				messages: {
					categoria: { required: "Ingrese nombre de categoria", },
				}
			});
		});

		///////////////////////////////////////////////////
		/// CONFIGURACION DE TABLAS
		///////////////////////////////////////////////////
		$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [  ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
		var lastIdx = null;
		var table = $('.tabla-lista-categorias').DataTable({ 'pageLength': 20 });
		$('.tabla-lista-notas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
		$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
		$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
	</script>
