<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES
	/////////////////////////////////////////////////////////////////////
	session_start();
	$dniUsuario =$_SESSION['dni_apv'];
	$opcion     =$_GET['opcion'];
	$dni        =$_GET['dni'];
	$ruta       ='../';
	$rutaFoto   =$ruta.'assets/images/user/';
	$sinFoto    =$ruta.'assets/images/user/no-user.png';

	/////////////////////////////////////////////////////////////////////
	/// MENU HEADER PAGINA
	/////////////////////////////////////////////////////////////////////
	$menuTop="menu-usuarios.php";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="USUARIOS DEL SISTEMA";
	$menuActual="usuarios";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
?>

<?php if($opcion=="lista"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CAMBIO DE CLAVE
			///////////////////////////////////////////////////
			$("button.bt_guardar_clave").click(function(){
				var ruta      ='<?= $ruta ?>';
				var opcion    ='lista';
				var dni       =$(this).attr('id');
				var clave     =$('#clave'+dni).val();
				var rClave    =$('#rClave'+dni).val();
				var operacion ='CAMBIA_CLAVE';
				var datos     = 'clave='+clave+'&dni='+dni+'&operacion='+operacion;
				if($.trim(clave).length>0 & $.trim(rClave).length>0){
					if(clave==rClave){
						$.ajax({
							type: "POST",
							url: ruta+'php/mantenimiento-usuarios.php',
							data: datos,
							dataType:'json',
							success: function(respuesta){
								if(respuesta.mensaje=="CLAVE_CAMBIADA"){
									new PNotify({title: 'CONFIRMACION', text: 'La clave fue cambiada.', addclass: 'bg-success'});
									location.reload();
								}
							}
						});
					}else{
						new PNotify({ text: 'Las claves ingresadas deben coincidir.', addclass: 'bg-warning alert-styled-right', type: 'warning' });
						$('#clave'+dni).focus();
					}
				}else{
					new PNotify({ text: 'Debe ingresar la nueva clave.', addclass: 'bg-warning alert-styled-right', type: 'warning' });
					$('#clave'+dni).focus();
				}
			});

			///////////////////////////////////////////////////
			/// CAMBIO DE ESTADO DE USUARIO
			///////////////////////////////////////////////////
			$(".bt_cambia_estado").click(function(){
				var ruta      ='<?= $ruta ?>';
				var opcion    ='lista';
				var dni       =$(this).attr('id');
				var operacion ='CAMBIA_ESTADO';
				var datos     = 'dni='+dni+'&operacion='+operacion;
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-usuarios.php',
					data: datos,
					dataType:'json',
					success: function(respuesta){
						if(respuesta.mensaje=="USUARIO_REVOCADO"){
							new PNotify({title: 'CONFIRMACION', text: 'El usuario fue revocado del sistema.', addclass: 'bg-success'});
						}

						if(respuesta.mensaje=="USUARIO_ACTIVADO"){
							new PNotify({title: 'CONFIRMACION', text: 'El usuario fue activado en el sistema.', addclass: 'bg-success'});
						}
						location.reload();
					}
				});
			});
		});
	</script>
	<div class="panel">
		<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">LISTA DE USARIOS DEL SISTEMA</h6></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table id="tablaUsuarios" class="table tabla table-bordered table-hover tabla-listaSocios">
						<thead>
							<tr class="success">
								<th class="text-center">#</th>
								<th class="text-center">USUARIO</th>
								<th class="text-left">NOMBRE DE USUARIO</th>
								<th class="text-center">GENERO</th>
								<th class="text-center">TELEFONO</th>
								<th class="text-center">ROL</th>
								<th class="text-center">ACTIVIDAD</th>
								<th class="text-center">ESTADO</th>
								<th class="text-center"><i class="fa fa-align-justify"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="SELECT dni, nombre, paterno, materno, foto, genero, email, telefono, rol, usuario, clave, estado, fechaRegistro FROM sm_usuarios ORDER BY paterno ASC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$dni           =$n['dni'];
									$nombre        =$n['nombre'];
									$paterno       =$n['paterno'];
									$materno       =$n['materno'];
									$foto          =$n['foto'];
									$genero        =$n['genero'];
									$email         =$n['email'];
									$telefono      =$n['telefono'];
									$rol           =$n['rol'];
									$usuario       =$n['usuario'];
									$clave         =$n['clave'];
									$estado        =$n['estado'];
									$fechaRegistro =$n['fechaRegistro'];
									$horaRegistro  ="00:00:00";
									$haceTiempo    =haceTiempo($fechaRegistro.$horaRegistro);

									if($rol=='JDP' || $rol=='JDT'){
										$infoRol = '<span class="label label-warning textoNegrita">'.infoRol($rol).'</span>';
									}else{
										$infoRol = '<span class="label label-default textoNegrita">'.infoRol($rol).'</span>';
									}
									if($estado=="ACT"){ $estadoUser='<span class="label label-success textoNegrita">Activo</span>'; }
									if($estado=="INC"){ $estadoUser='<span class="label label-danger textoNegrita">Inactivo</span>'; }
							?>
							<tr>
								<td class="text-center"><?= ceros($i,2) ?></td>
								<td class="text-center"><span class="label label-default textoMinuscula font12 textoNegrita"><?= $usuario ?><span></td>
								<td class="text-left"><?= utf8_encode($nombre.' '.$paterno.' '.$materno) ?></td>
								<td class="text-center"><?= infoGenero($genero) ?></td>
								<td class="text-center"><?= $telefono ?></td>
								<td class="text-center"><?= $infoRol ?></td>
								<td class="text-center"><span class="label bg-teal textoNegrita"><?= $haceTiempo ?></span></td>
								<td class="text-center">
									<?php if($estado=="ACT"){ ?><a href="javascript:;" id="<?= $dni ?>&estado=DESACTIVA" class="bt_cambia_estado"><?= $estadoUser ?></a><?php } ?>
									<?php if($estado=="INC"){ ?><a href="javascript:;" id="<?= $dni ?>&estado=ACTIVA" class="bt_cambia_estado"><?= $estadoUser ?></a><?php } ?>
								</td>
								<td class="text-center">
									<a href="detalle-usuarios.php?dni=<?= $dni ?>&opcion=detalles" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Perfil de usuario"><i class="icon-user-check"></i></a>
									<a href="detalle-usuarios.php?dni=<?= $dni ?>&opcion=editar" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Editar usuario"><i class="fa fa-pencil"></i></a>
									<a href="#cambioClave-<?= $dni ?>" data-toggle="modal" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Cambiar clave"><i class="fa fa-key"></i></a>
									<?php if($_SESSION['rol_apv']=='ADM'){ ?>
										<a href="#" class="btn btn-xs btn-icon btn-danger btnEliminar" data-usuario="<?= $dni ?>"><i class="icon-trash"></i></a>
									<?php } ?>
								</td>
							</tr>
							<!-- MODAL DIRECCION 01 -->
							<div id="cambioClave-<?= $dni ?>" class="modal fade">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header bg-slate-600">
											<button type="button" class="close" data-dismiss="modal">&times;</button>
											<h6 class="modal-title textoNegrita">CAMBIO DE CLAVE &rarr; <?= utf8_encode($nombre.' '.$paterno) ?></h6>
										</div>
										<div class="modal-body">
											<form id="form_usuario_clave" class="form_valida">
												<div class="form-group">
													<div class="row">
														<div class="col-sm-4">
															<input type="password" id="clave<?= $dni ?>" placeholder="Ingrese clave" class="form-control" autofocus>
															<span class="label label-block bg-brown text-left tope2">Ingrese clave</span>
														</div>
														<div class="col-sm-4">
															<input type="password" id="rClave<?= $dni ?>" placeholder="Repita clave" class="form-control">
															<span class="label label-block bg-brown text-left tope2">Repita clave</span>
														</div>
														<div class="col-sm-4">
															<button type="button"  id="<?= $dni ?>" class="btn btn-success btn-icon bt_guardar_clave">Guardar Clave</button>
															<button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
														</div>
													</div>
												</div>
											</form>
										</div>
									</div>
								</div>
							</div>
							<script type="text/javascript">
								$(document).ready(function(){
									$('#tablaUsuarios').on('click', '.btnEliminar', function() {
										var urlProceso = '../php/mantenimiento-usuarios.php';
										var dni        = $(this).data('usuario');
										var operacion  = 'ELIMINA_USUARIO';
										var datos      = {
											dni:dni,
											operacion:operacion
										};
										swal({
											title: "Eliminar Usuario",
											text: "¿Esta seguro de eliminar a Usuario?",
											type: "warning",
											showCancelButton: true,
											cancelButtonText: "No, Cancelar",
											confirmButtonColor: "#DD6B55",
											confirmButtonText: "Si, Eliminar",
											closeOnConfirm: false
										},
										function(){
											$.ajax({
												method   : 'POST',
												dataType : 'json',
												url      : urlProceso,
												data     : datos,
												success: function(respuesta){
													console.log(respuesta);
													if(respuesta.resultado=='ELIMINADO'){
														location.reload();
													}
													if(respuesta.resultado=='ERROR'){
														swal({
														title: "Error en Servidor",
														text: "Ha ocurrido un error al conectarse con la Base de Datos",
														type: "info",
														showCancelButton: false,
														closeOnConfirm: false
														});
													}
												}
											});	
										});
									});
								});
							</script>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
<?php } ?>

<?php if($opcion=="agregar"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// AGREGAR NUEVO USUARIO
			///////////////////////////////////////////////////
			$("button#bt_agregar_usuario").click(function(){
				var ruta='<?= $ruta ?>';
				var operacion ='REGISTRA_USUARIO';
				var opcion    ='lista';
				var datos     = $('#form_registra_usuario').serialize()+'&opcion='+opcion+'&operacion='+operacion;
				var valida    = $('.form_valida').valid();
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-usuarios.php',
						data: datos,
						dataType:'json',
						success: function(respuesta){
							if(respuesta.mensaje=="USUARIO_REGISTRADO"){
								new PNotify({title: 'CONFIRMACION', text: 'Usuario fue registrado.', addclass: 'bg-success'});
								window.location.replace("detalle-usuarios.php?opcion="+opcion);
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// VALIDA FORMULARIO
			///////////////////////////////////////////////////
			var validator = $(".form_valida").validate({
				ignore: 'input[type=hidden], .select2-input',
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).removeClass(errorClass); },
				unhighlight: function(element, errorClass) { $(element).removeClass(errorClass); },

				errorPlacement: function(error, element) {
					if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container') ) {
						if(element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
							error.appendTo( element.parent().parent().parent().parent() );
						}
						 else {
							error.appendTo( element.parent().parent().parent().parent().parent() );
						}
					}

					else if (element.parents().hasClass('input-group')) {
						error.appendTo( element.parent().parent() );
					}
					else {
						error.insertAfter(element);
					}
				},
				validClass: "validation-valid-label",
				rules: {
					vali: "required",
					UsuarioDNI: { minlength: 8 },
					usuarioNombre: { minlength: 3 },
					usuarioPaterno: { minlength: 3 },
					usuarioMaterno: { minlength: 3 },
					usuarioTelefono: { minlength: 4 },
					usuarioLogin: { minlength: 4 },
					usuarioClave: { minlength: 8 },
					usuarioClaveR: { equalTo: '#usuarioClave' },
				},
				messages: {
					UsuarioDNI: { required: "Ingrese DNI", minlength: "DNI valido", },
					usuarioNombre: { required: "Ingrese Nombre", minlength: "Ingrese nombre valido", },
					usuarioPaterno: { required: "Ingrese apellido materno", minlength: "Ingrese apellido valido", },
					usuarioMaterno: { required: "Ingrese apellido paterno", minlength: "Ingrese apellido valido", },
					usuarioGenero: { required: "Genero", },
					usuarioTelefono: { required: "Nro de telefono", minlength: "Nro valido", },
					usuarioRol: { required: "Rol de usuario", },
					usuarioLogin: { required: "Ingrese usuario de acceso", minlength: "Debe ser minimo 4 caracteres", },
					usuarioClave: { required: "Ingrese clave de acceso", minlength: "La clave debe ser mayor a 8 caracteres", },
					usuarioClaveR: { required: "Repita clave", equalTo: "Debe se igual a clave ingresada", },
				}
			});

			///////////////////////////////////////////////////
			/// CAMBIO DE ESTILO DE INPUT FILE
			///////////////////////////////////////////////////
			$(".inputSubir").uniform({
				wrapperClass: 'bg-brown',
				fileButtonHtml: '<i class="icon-plus2"></i>'
			});
		});

		///////////////////////////////////////////////////
		/// VALIDA NUMEROS
		///////////////////////////////////////////////////
		$(function(){ $('#UsuarioDNI, #usuarioTelefono').validar('0123456789'); });

		///////////////////////////////////////////////////
		/// MUESTRA NOMBRE Y DNI
		///////////////////////////////////////////////////
		function nuevoUsuario(){
			var dni = $("input#UsuarioDNI").val();
			var nombre = $("input#usuarioNombre").val();
			var paterno = $("input#usuarioPaterno").val();
			var materno = $("input#usuarioMaterno").val();
			var nombre=nombre+' '+paterno+' '+materno;
			if($.trim(dni).length>0 && $.trim(nombre).length>0 && $.trim(paterno).length>0 && $.trim(materno).length>0){
				$('#nuevoUsuario').fadeIn().html('<div class="form-group"><div class="row"><div class="col-md-12"><div class="form-group"><span class="form-control text-danger text-center textoMayuscula"><strong>USARIO:</strong> '+nombre+'&nbsp;&nbsp;|&nbsp;&nbsp;<strong>DNI:</strong> '+dni+'</span></div></div></div></div>');
			}
		}

		///////////////////////////////////////////////////
		/// VERIFICA DNI
		///////////////////////////////////////////////////
		function verificaDNI(){
			var ruta      ='<?= $ruta ?>';
			var dni       =$("input#UsuarioDNI").val();
			var operacion ="VERIFICA_DNI";
			var datos='dni='+dni+'&operacion='+operacion;
			if($.trim(dni).length>0){
				$.ajax({
					type: "POST",
					url: ruta+'php/mantenimiento-usuarios.php',
					data: datos,
					dataType:'json',
					success: function(respuesta){
						if(respuesta.mensaje=="DNI_EXISTE"){
							new PNotify({ text: 'El DNI ingresado ya existe en la DB, intentelo muevamente...', addclass: 'bg-warning alert-styled-right', type: 'warning' });
							$('button#bt_agregar_usuario').prop('disabled', true);
							$("input#UsuarioDNI").focus()
						}
						if(respuesta.mensaje=="DNI_NO_EXISTE"){
							new PNotify({ text: 'El DNI es valido, puede continuar...', addclass: 'bg-success alert-styled-right', type: 'success' });
							$('button#bt_agregar_usuario').prop('disabled', false);
						}
					}
				});
			}else{
				$('button#bt_agregar_usuario').prop('disabled', true);
			}
		}

		///////////////////////////////////////////////////
		/// VERIFICA USUARIO
		///////////////////////////////////////////////////
		function verificaUSER(){
			var ruta      ='<?= $ruta ?>';
			var usuario   =$("input#usuarioLogin").val();
			var operacion ="VERIFICA_USER";
			var datos     ='usuario='+usuario+'&operacion='+operacion;
			$.ajax({
				type: "POST",
				url: ruta+'php/mantenimiento-usuarios.php',
				data: datos,
				dataType:'json',
				success: function(respuesta){
					if(respuesta.mensaje=="EXISTE_USER"){
						new PNotify({ text: 'El Nombre de Usuario ingresado ya existe en la DB, intentelo muevamente...', addclass: 'bg-warning alert-styled-right', type: 'warning' });
						$('button#bt_agregar_usuario').prop('disabled', true);
						$("input#usuarioLogin").focus()
					}
					if(respuesta.mensaje=="NO_EXISTE_USER"){
						new PNotify({ text: 'El Nombre de Usuario es valido, puede continuar...', addclass: 'bg-success alert-styled-right', type: 'success' });
						$('button#bt_agregar_usuario').prop('disabled', false);
					}
				}
			});
		}
	</script>
	
	<div class="panel">
		<div class="panel-heading bg-brown">
			<h6 class="panel-title">AGREAR NUEVO USUARIO</h6>
		</div>
		<div class="panel-body">
			<form id="form_registra_usuario" class="form_valida">
				<div class="row">
					<div class="col-md-9">
						<div class="form-group">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<input type="text" id="UsuarioDNI" name="UsuarioDNI" maxlength="8" required="required" class="form-control textoMayuscula" onChange="nuevoUsuario();" onblur="verificaDNI();" tabindex="1" autofocus>
										<span class="label label-block label-default text-left tope2">Ingrese DNI</span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<input type="text" id="usuarioNombre" name="usuarioNombre" required="required" class="form-control textoMayuscula" onblur="mayusculas(event, this)" onChange="nuevoUsuario();" tabindex="2">
										<span class="label label-block label-default text-left tope2">Ingrese Nombre</span>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id="usuarioPaterno" name="usuarioPaterno" required="required" class="form-control textoMayuscula" onblur="mayusculas(event, this)" onChange="nuevoUsuario();" tabindex="3">
										<span class="label label-block label-default text-left tope2">Ingrese Apellido Paterno</span>
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<input type="text" id="usuarioMaterno" name="usuarioMaterno" required="required" class="form-control textoMayuscula" onblur="mayusculas(event, this)" onChange="nuevoUsuario();" tabindex="4">
										<span class="label label-block label-default text-left tope2">Ingrese Apellido Materno</span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<select id="usuarioGenero" name="usuarioGenero" required="required" class="form-control" tabindex="5">
											<option value="" selected>SELECCIONE</option>
											<?php
												$sql="SELECT * FROM sm_a_genero";
												$rs=mysqli_query($conexion,$sql);
												while($datos=mysqli_fetch_array($rs)){
													echo '<option value="'.$datos[0].'">'.$datos[1].'</option>';
												}
											?>
										</select>
										<span class="label label-block label-default text-left tope2">Seleccione Genero</span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<input type="text" id="usuarioEmail" name="usuarioEmail" class="form-control textoMinuscula" placeholder="Email" onblur="minusculas(event, this)" tabindex="6">
										<span class="label label-block label-default text-left tope2">Ingrese Email</span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<input type="text" id="usuarioTelefono" name="usuarioTelefono" maxlength="9" required="required" class="form-control textoMayuscula" placeholder="Telefono" onblur="mayusculas(event, this)" tabindex="7">
										<span class="label label-block label-default text-left tope2">Ingrese Teléfono</span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<select id="usuarioRol" name="usuarioRol" required="required" class="form-control" tabindex="8">
											<option value="" selected>SELECCIONE</option>
											<?php
												$sql="SELECT * FROM sm_usuarios_rol";
												$rs=mysqli_query($conexion,$sql);
												while($datos=mysqli_fetch_array($rs)){
													echo '<option value="'.$datos[1].'">'.$datos[2].'</option>';
												}
											?>
										</select>
										<span class="label label-block label-default text-left tope2">Seleccione Rol de Usuario</span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<input type="text" id="usuarioLogin" name="usuarioLogin" required="required" class="form-control textoMinuscula" onblur="minusculas(event, this); verificaUSER();" tabindex="9">
										<span class="label label-block label-default text-left tope2">Ingrese Usuario de Acceso</span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<input type="password" id="usuarioClave" name="usuarioClave" required="required" class="form-control" tabindex="10">
										<span class="label label-block label-default text-left tope2">Ingrese Clave</span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<input type="password" id="usuarioClaveR" name="usuarioClaveR" required="required" class="form-control" tabindex="11">
										<span class="label label-block label-default text-left tope2">Repita la clave</span>
									</div>
								</div>
							</div>
						</div>
						<div id="nuevoUsuario"></div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<button type="button" id="bt_agregar_usuario" class="btn btn-xl btn-success btn-block">REGISTRAR USUARIO</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="thumb thumb-slide"><img src="<?= $sinFoto ?>"></div>
					</div>
				</div>
			</form>		
		</div>
	</div>
<?php } ?>

<?php if($opcion=="editar"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// AGREGAR NUEVO USUARIO
			///////////////////////////////////////////////////
			$("button#bt_modifica_usuario").click(function(){
				var ruta='<?= $ruta ?>';
				var UsuarioDNI='<?= $dni ?>';
				var operacion ='MODIFICA_USUARIO';
				var opcion    ='lista';
				var datos     = $('#form_registra_usuario').serialize()+'&UsuarioDNI='+UsuarioDNI+'&opcion='+opcion+'&operacion='+operacion;
				var valida    = $('.form_valida').valid();
				if(valida){
					$.ajax({
						type: "POST",
						url: ruta+'php/mantenimiento-usuarios.php',
						data: datos,
						dataType:'json',
						success: function(respuesta){
							if(respuesta.mensaje=="USUARIO_MODIFICADO"){
								new PNotify({title: 'CONFIRMACION', text: 'Los datos del usuario fueron modificados correctamente.', addclass: 'bg-success'});
								window.location.replace("detalle-usuarios.php?opcion="+opcion);
							}
						}
					});
				}
			});

			///////////////////////////////////////////////////
			/// VALIDA FORMULARIO
			///////////////////////////////////////////////////
			var validator = $(".form_valida").validate({
				ignore: 'input[type=hidden], .select2-input',
				errorClass: 'validation-error-label',
				highlight: function(element, errorClass) { $(element).removeClass(errorClass); },
				unhighlight: function(element, errorClass) { $(element).removeClass(errorClass); },

				errorPlacement: function(error, element) {
					if (element.parents('div').hasClass("checker") || element.parents('div').hasClass("choice") || element.parent().hasClass('bootstrap-switch-container') ) {
						if(element.parents('label').hasClass('checkbox-inline') || element.parents('label').hasClass('radio-inline')) {
							error.appendTo( element.parent().parent().parent().parent() );
						}
						 else {
							error.appendTo( element.parent().parent().parent().parent().parent() );
						}
					}

					else if (element.parents().hasClass('input-group')) {
						error.appendTo( element.parent().parent() );
					}
					else {
						error.insertAfter(element);
					}
				},
				validClass: "validation-valid-label",
				rules: {
					vali: "required",
					usuarioNombre: { minlength: 3 },
					usuarioPaterno: { minlength: 3 },
					usuarioMaterno: { minlength: 3 },
					usuarioTelefono: { minlength: 4 },
				},
				messages: {
					usuarioNombre: { required: "Ingrese Nombre", minlength: "Ingrese nombre valido", },
					usuarioPaterno: { required: "Ingrese apellido materno", minlength: "Ingrese apellido valido", },
					usuarioMaterno: { required: "Ingrese apellido paterno", minlength: "Ingrese apellido valido", },
					usuarioGenero: { required: "Genero", },
					usuarioTelefono: { required: "Nro de telefono", minlength: "Nro valido", },
					usuarioRol: { required: "Rol de usuario", },
				}
			});
		});

		///////////////////////////////////////////////////
		/// VALIDA NUMEROS
		///////////////////////////////////////////////////
		$(function(){ $('#usuarioTelefono').validar('0123456789'); });
	</script>

	<?php 
		$sql="SELECT dni, nombre, paterno, materno, foto, genero, email, telefono, rol, usuario, clave FROM sm_usuarios WHERE dni='$dni'";
		$row=mysqli_query($conexion,$sql);
		$n=mysqli_fetch_array($row);
		$dni      =$n['dni'];
		$nombre   =$n['nombre'];
		$paterno  =$n['paterno'];
		$materno  =$n['materno'];
		$foto     =$n['foto'];
		$genero   =$n['genero'];
		$email    =$n['email'];
		$telefono =$n['telefono'];
		$rol      =$n['rol'];
		$usuario  =$n['usuario'];
		$clave    =$n['clave'];
	?>
	
	<div class="panel">
		<div class="panel-heading bg-brown">
			<h6 class="panel-title">EDITAR DATOS DE USUARIO</h6>
		</div>
		<div class="panel-body">
			<form id="form_registra_usuario" class="form_valida">
				<div class="form-group">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<input disabled="disabled" value="<?= $dni ?>" class="form-control">
								<span class="label label-block label-default text-left tope2">DNI</span>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<input type="text" id="usuarioNombre" name="usuarioNombre" required="required" value="<?= utf8_encode($nombre) ?>" class="form-control textoMayuscula" onblur="mayusculas(event, this)" autofocus tabindex="1">
								<span class="label label-block label-default text-left tope2">Nombre</span>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" id="usuarioPaterno" name="usuarioPaterno" required="required" value="<?= utf8_encode($paterno) ?>" class="form-control textoMayuscula" onblur="mayusculas(event, this)" tabindex="2">
								<span class="label label-block label-default text-left tope2">Apellido Paterno</span>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input type="text" id="usuarioMaterno" name="usuarioMaterno" required="required" value="<?= utf8_encode($materno) ?>" class="form-control textoMayuscula" onblur="mayusculas(event, this)" tabindex="3">
								<span class="label label-block label-default text-left tope2">Apellido Materno</span>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<select id="usuarioGenero" name="usuarioGenero" required="required" class="form-control" tabindex="4">
									<option value="" selected>SELECCIONE</option>
									<?php
										$sql="SELECT * FROM sm_a_genero";
										$rs=mysqli_query($conexion,$sql);
										while($datos=mysqli_fetch_array($rs)){
											if($genero==$datos[0]){
												echo '<option value="'.$datos[0].'" selected>'.$datos[1].'</option>';
											}else{
												echo '<option value="'.$datos[0].'">'.$datos[1].'</option>';
											}
										}
									?>
								</select>
								<span class="label label-block label-default text-left tope2">Genero</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" id="usuarioEmail" name="usuarioEmail" value="<?= $email ?>" class="form-control textoMinuscula" placeholder="Email" onblur="minusculas(event, this)" tabindex="5">
								<span class="label label-block label-default text-left tope2">Email</span>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<input type="text" id="usuarioTelefono" name="usuarioTelefono" value="<?= $telefono ?>" maxlength="9" required="required" class="form-control textoMayuscula" placeholder="Telefono" onblur="mayusculas(event, this)" tabindex="6">
								<span class="label label-block label-default text-left tope2">Teléfono</span>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<select id="usuarioRol" name="usuarioRol" required="required" class="form-control" tabindex="7">
									<option value="" selected>SELECCIONE</option>
									<?php
										$sql="SELECT * FROM sm_usuarios_rol";
										$rs=mysqli_query($conexion,$sql);
										while($datos=mysqli_fetch_array($rs)){
											if($rol==$datos[1]){
												echo '<option value="'.$datos[1].'" selected>'.$datos[2].'</option>';
											}else{
												echo '<option value="'.$datos[1].'">'.$datos[2].'</option>';
											}
										}
									?>
								</select>
								<span class="label label-block label-default text-left tope2">Rol de Usuario</span>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<input disabled="disabled" value="<?= $usuario ?>" class="form-control">
								<span class="label label-block label-default text-left tope2">Usuario de Acceso</span>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<button type="button" id="bt_modifica_usuario" class="btn btn-xl btn-success btn-block">MODIFICAR DATOS DE USUARIO</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php } ?>

<?php if($opcion=="detalles"){ ?>
	<script type="text/javascript">
		$(document).ready(function(){
			///////////////////////////////////////////////////
			/// CAMBIO DE FOTOGRAFIA DE USUARIO
			///////////////////////////////////////////////////
			$("button#subirFoto").click(function(){
				var ruta      ="<?= $ruta ?>";
				var opcion    ="<?= $opcion ?>";
				var dni       ="<?= $dni ?>";
				var operacion ="ACTUALIZA_FOTO";
				var foto      = $('#fotoUsuario').val();
				var datos     = new FormData($("#formCambioFoto")[0]);
				$.ajax({
					url: ruta+'php/mantenimiento-usuarios.php?dni='+dni+'&foto='+foto+'&operacion='+operacion,
					type: "POST",
					dataType:'json',
					data: datos,
					contentType: false,
					processData: false,
					success: function(respuesta){
						if(respuesta.mensaje=="PROCESADO"){
							new PNotify({title: 'CONFIRMACION', text: 'La foto de usuario fue actualizada.', addclass: 'bg-success'});
							$("#cambiarFoto").modal('hide');
							window.location.replace("detalle-usuarios.php?dni="+dni+'&opcion='+opcion);
						}
						if(respuesta.mensaje=="ERRORFILE"){
							new PNotify({title: 'ERROR', text: 'La foto tiene que ser en formato JPG o ser menor que 200Kb.', addclass: 'bg-warning'});
						}
						if(respuesta.mensaje=="ERRORSERVER"){
							new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
						}
					}
				});
			});
			
			///////////////////////////////////////////////////
			/// CAMBIO DE ESTILO DE INPUT FILE
			///////////////////////////////////////////////////
			$(".inputSubir").uniform({
				wrapperClass: 'bg-brown',
				fileButtonHtml: '<i class="icon-plus2"></i>'
			});
		});
	</script>
	<?php
		$sql="SELECT nombre, paterno, materno, foto, genero, email, telefono, rol, usuario, clave, estado, fechaRegistro FROM sm_usuarios WHERE dni='$dni'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$nombre=utf8_encode($dato['nombre']);
		$paterno=utf8_encode($dato['paterno']);
		$materno=utf8_encode($dato['materno']);
		$foto          =$dato['foto'];
		$genero        =$dato['genero'];
		$email         =$dato['email'];
		$telefono      =$dato['telefono'];
		$rol           =$dato['rol'];
		$usuario       =$dato['usuario'];
		$clave         =$dato['clave'];
		$estado        =$dato['estado'];
		$fechaRegistro =$dato['fechaRegistro'];
		if($foto){ $fotografia=$rutaFoto.$foto; }else{ $fotografia=$sinFoto; }
		if($estado=="ACT"){ $estadoUsuario='<span class="label label-success">ACTIVO</span>'; }
		if($estado=="INC"){ $estadoUsuario='<span class="label label-danger">INACTIVO</span>'; }
	?>

	<div class="row">
		<div class="col-lg-1"></div>
		<div class="col-lg-3">
			<div class="thumbnail">
				<div class="thumb thumb-slide">
					<img src="<?= $fotografia ?>" alt="">
					<div class="caption">
						<span>
							<a href="#cambiarFoto" data-toggle="modal" class="btn bg-success-400 btn-icon btn-xs"><i class="fa fa-pencil-square-o"></i></a>
						</span>
					</div>
				</div>
				<div class="caption text-center">
					<h6 class="text-semibold no-margin"><?= $nombre.' '.$paterno ?><small class="display-block"><?= infoRol($rol) ?></small></h6>
				</div>
				<!-- MODAL CAMBIO DE FOTO -->
				<div id="cambiarFoto" class="modal fade">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header bg-slate-600">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h6 class="modal-title textoNegrita">CAMBIO DE FOTO DE USUARIO &rarr; <?= $nombre.' '.$paterno ?></h6>
							</div>
							<div class="modal-body">
								<form id="formCambioFoto" class="validar" method="post" enctype="multipart/form-data">
									<div class="form-group">
										<div class="row">
											<div class="col-sm-9">
												<div id="procensadoSubida">
													<input type="file" name="fotoUsuario" id="fotoUsuario" class="inputSubir" required="required">
												</div>
											</div>
											<div class="col-sm-3">
												<button type="button" id="subirFoto" class="btn btn-success btn-icon"><i class=" icon-floppy-disk"></i></button>
												<button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-7">
			<div class="panel">
				<div class="panel-heading bg-brown"><h6 class="panel-title textoNegrita">DATOS DE USUARIO DEL SISTEMA</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<ul class="list-group">
								<li class="list-group-item"><span class="textoNegrita">DNI</span> <span class="pull-right text-slate"><?= $dni ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">NOMBRE</span> <span class="pull-right text-slate"><?= $nombre ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">APELLIDOS</span> <span class="pull-right text-slate"><?= $paterno.' '.$materno ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">GENERO</span> <span class="pull-right text-slate"><?= infoGenero($genero) ?></span></li>
								<?php if($email){ ?><li class="list-group-item"><span class="textoNegrita">EMAIL</span> <span class="pull-right text-slate"><?= $email ?></span></li><?php } ?>
								<li class="list-group-item"><span class="textoNegrita">TELEFONO</span> <span class="pull-right text-slate"><?= $telefono ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">USUARIO DE LOGIN</span> <span class="pull-right text-slate"><span class="label label-info"><?= $usuario ?></span></span></li>
								<li class="list-group-item"><span class="textoNegrita">ROL DE USUARIO</span> <span class="pull-right text-slate"><?= infoRol($rol) ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">FECHA DE REGISTRO</span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaRegistro,'corta') ?></span></li>
								<li class="list-group-item"><span class="textoNegrita">ESTADO DE USUARIO</span> <span class="pull-right text-slate"><?= $estadoUsuario ?></span></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-1"></div>
	</div>
<?php } ?>

<?php if($opcion=="operaciones"){ ?>
	<div class="panel panel-default">
		<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">REGISTRO DE OPERACIONES PROCESADAS</h6></div>
		<div class="table-responsive">
			<table class="table tabla-info table-bordered table-hover">
				<thead>
					<tr class="success">
						<th class="textoNegrita text-center">#</th>
						<th class="textoNegrita text-left">NOMBRE USUARIO</th>
						<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
						<th class="textoNegrita text-center">FECHA</th>
						<th class="textoNegrita text-center">HORA</th>
						<th class="textoNegrita text-center">USUARIO</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT dni, proceso, fecha, hora, usuario FROM sm_usuarios_operaciones ORDER BY userID DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$dni     =$n['dni'];
							$proceso =$n['proceso'];
							$fecha   =$n['fecha'];
							$hora    =$n['hora'];
							$usuario =$n['usuario'];
							$nombre  =datoUsuario($dni,'nombreFull');
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-left"><?= utf8_encode($nombre) ?></td>
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
<?php } ?>

<?php if($opcion=="operacionesUser"){ ?>
	<div class="panel panel-default">
		<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">REGISTRO DE OPERACIONES PROCESADAS</h6></div>
		<div class="table-responsive">
			<table class="table tabla-info table-bordered table-hover">
				<thead>
					<tr class="success">
						<th class="textoNegrita text-center">#</th>
						<th class="textoNegrita text-left">NOMBRE USUARIO</th>
						<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
						<th class="textoNegrita text-center">FECHA</th>
						<th class="textoNegrita text-center">HORA</th>
						<th class="textoNegrita text-center">USUARIO</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT dni, proceso, fecha, hora, usuario FROM sm_usuarios_operaciones WHERE dni='$dni' ORDER BY userID DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$dni     =$n['dni'];
							$proceso =$n['proceso'];
							$fecha   =$n['fecha'];
							$hora    =$n['hora'];
							$usuario =$n['usuario'];
							$nombre  =datoUsuario($dni,'nombreFull');
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-left"><?= utf8_encode($nombre) ?></td>
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
<?php } ?>

<?php include($ruta.'template/footer.tpl'); ?>