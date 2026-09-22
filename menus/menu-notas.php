<div class="navbar navbar-default navbar-xs">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="icon-menu7"></i></a></li>
	</ul>

	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav element-active-slate-400 textoMayuscula">
			<li><a href="#modal_agregar_nota" data-toggle="modal">Agregar Nota</a></li>
			<li><a href="#modal_gestion_categorias" data-toggle="modal">Gestion de categorias</a></li>
		</ul>
	</div>
</div>

<!-- MODAL AGREGA NOTA -->
<div id="modal_agregar_nota" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-slate-600">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h6 class="modal-title">AGREGAR OBSERVACION</h6>
			</div>
			<div class="modal-body">
				<form id="frm_observaciones">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-12">
								<textarea rows="10" cols="5" name="observacion" id="observacion" required="required" class="form-control textoMayuscula" placeholder="Ingrese texto de observacion..." onblur="mayusculas(event, this)" autofocus></textarea>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-sm-12">
								<select id="idCategoria" name="idCategoria" required="required" class="form-control input-lg">
									<option value="" selected>SELECCIONE CATEGORIA</option>
									<?php
										$sql="SELECT id, categoria FROM sm_notas_categorias ORDER BY id DESC";
										$rs=mysqli_query($conexion,$sql);
										while($datos=mysqli_fetch_array($rs)){
											$id        =$datos['id'];
											$categoria =$datos['categoria'];
											echo '<option value="'.$id.'">'.texto($categoria).'</option>';
										}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-sm-6 col-xs-6">
								<input type="hidden" name="operacion" value="AGREGA_NOTA">
								<button type="button" id="bt_salvar_observacion" class="btn btn-lg btn-success btn-block btn-icon">Almacenar</button>
							</div>
							<div class="col-sm-6 col-xs-6">
								<button type="button" class="btn btn-lg btn-warning btn-block" data-dismiss="modal">Cerrar</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<!-- MODAL GESTION DE CATEGORIAS -->
<div id="modal_gestion_categorias" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-slate-600">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h6 class="modal-title">GESTION DE CATEGORIAS PARA NOTAS</h6>
			</div>
			<div class="modal-body">
				<div id="modulo_gestion_categiorias"></div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// AGREGAR OBSERVACIONES
		///////////////////////////////////////////////////
		$("button#bt_salvar_observacion").click(function(){
			var ruta        ="<?= $ruta ?>";
			var datos  =$('#frm_observaciones').serialize();
			var valida =$('#frm_observaciones').valid();
			if(valida){
				$.ajax({
					type: 'POST',
					url: ruta+'php/mantenimiento-usuarios.php',
					data: datos,
					dataType:'json',
					success:function(respuesta){
						if(respuesta.mensaje=="OBSERVACION_AGREGADA"){
							new PNotify({title: 'ALMACENADO', text: 'Nota agregada a la BD.', addclass: 'bg-success'});
							location.reload();
						}
						if(respuesta.mensaje=="ERROR_OBSERVACION_AGREGADA"){
							new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
						}
					}
				});
			}else{
				$("textarea#observacion").focus();
				new PNotify({title: 'ERROR', text: 'Por favor ingrese observación.', addclass: 'bg-warning'});
			}
		});


		///////////////////////////////////////////////////
		/// GESTION DE CATEGORIAS
		///////////////////////////////////////////////////
		$('#modal_gestion_categorias').on('shown.bs.modal', function(){
			var ruta        ="<?= $ruta ?>";
			$('#modulo_gestion_categiorias').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO</div></div></div>').fadeIn(1000).delay(1000);
			$("#modulo_gestion_categiorias").fadeIn("slow").load(ruta+'administrador/modulo/modulo-gestion-categias-notas.php').fadeIn(1000).delay(1000);
		});

		///////////////////////////////////////////////////
		/// VALIDAR FORMULARIO - CHEQUERA
		///////////////////////////////////////////////////
		var validator = $("#frm_observaciones").validate({
			errorClass: 'validation-error-label',
			highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
			unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
			validClass: "validation-valid-label",
			rules: {
				observacion: { required: true },
				idCategoria: { required: true },
			},
			messages: {
				observacion: { required: "Ingrese nota u observacion", },
				idCategoria: { required: "Seleccione categoria de nota", },
			}
		});
	});
</script>