<?php $modulo=$_GET[modulo]; ?>

<?php if($modulo=="formINGpB"){ ?>
	<div class="panel">
		<div class="panel-heading bg-teal-600"><h6 class="panel-title">FORMULARIO DE CONSULTA</h6></div>
		<div class="panel-body">
			<form method="GET">
				<div class="row">
					<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="hidden" name="opcion" value="<?= $opcion ?>">
							<input type="text" name="fechaInicio" value="<?= $fechaInicio ?>" class="form-control input-lg fechas textoMayuscula" placeholder="Fecha de Inicio" tabindex="1">
						</div>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<span class="input-group-addon"><i class="icon-calendar"></i></span>
							<input type="text" name="fechaFin" value="<?= $fechaFin ?>" class="form-control input-lg fechas textoMayuscula" placeholder="Fecha de Fin" tabindex="2">
						</div>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<span class="input-group-addon"><i class="icon-user"></i></span>
							<select name="usuarioConsulta" class="seleccion select-lg">
							<?php if($usuarioConsulta=="ALL"){ echo '<option value="ALL" selected>TODOS LOS USUARIO</option>'; } ?>
							<?php
								$sql="SELECT dni FROM sm_usuarios";
								$rs=mysqli_query($conexion,$sql);
								while($datos=mysqli_fetch_array($rs)){
									if($datos[0]==$usuarioConsulta){
										echo '<option value="'.$datos[0].'" selected>'.texto(datoUsuario($datos[0],'nombreFull')).'</option>';
									}else{
										echo '<option value="'.$datos[0].'">'.texto(datoUsuario($datos[0],'nombreFull')).'</option>';
									}
								}
							?>
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<input type="hidden" name="modulo" value="<?= $modulo ?>">
						<button type="submit" class="btn btn-lg btn-block bg-teal-600" tabindex="3"><i class=" icon-search4"></i>&nbsp;&nbsp;BUSCAR</button>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php } ?>

<?php if($modulo=="formCHB"){ ?>
	<?php
		if($usuarioConsulta=="ALL"){ $infoUsuarioConsulta="TODOS LOS USUARIOS"; }else{
			$nombre=infoSocios($usuarioConsulta,'nombre');
			if($nombre==""){ $nombre=datoUsuario($usuarioConsulta,'nombreFull'); }else{ $nombre=$nombre; }
			$infoUsuarioConsulta=$nombre.' <small class="text-brown">('.$usuarioConsulta.')</small>';
		};
		
		if($tipoCheque=="ALL"){ $rotuloTipoCheque="TODOS LOS TIPO DE EMISION"; }else{
			if($tipoCheque=="PAR"){ $rotuloTipoCheque="EMITIDOS POR PARTIDA"; }
			if($tipoCheque=="VAR"){ $rotuloTipoCheque="EMITIDOS POR CONCEPTOS VARIOS"; }
		};
		if($entidadBancaria=="ALL"){ $rotuloInfoEntidadBancaria="TODAS LA ENTIDADES"; }else{ $rotuloInfoEntidadBancaria=infoBancos($entidadBancaria,'detalleEntidad'); };
		if($codigoCuenta=="ALL"){ $rotuloInfoCodigoCuenta="TODAS LAS CUENTAS"; }else{ $rotuloInfoCodigoCuenta=infoCuentas($codigoCuenta,'','detalleCuenta'); };
		if($codigoChequera=="ALL"){ $rotuloInfoCodigoChequera="TODAS LAS CHEQUERAS"; }else{ $rotuloInfoCodigoChequera=infoChequeras($codigoChequera,'','','detalleChequera'); };
	?>

	<div class="panel">
		<div class="panel-heading bg-teal-600">
			<h6 class="panel-title">FORMULARIO DE CONSULTA DE CHEQUES</h6>
			<div class="heading-elements">
				<button type="button" class="btn bg-warning-300 heading-btn btn-labeled text-bold" data-toggle="modal" data-target="#busqueda"><b><i class="icon-search4"></i></b> MODIFICAR CONSULTA</button>
				<a href="../documentos/cheques-emitidos.php?fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>&tipoCheque=<?= $tipoCheque ?>&entidadBancaria=<?= $entidadBancaria ?>&codigoCuenta=<?= $codigoCuenta ?>&codigoChequera=<?= $codigoChequera ?>&usuarioConsulta=<?= $usuarioConsulta ?>" class="btn btn-success heading-btn btn-labeled text-bold"><b><i class="icon-printer2"></i></b> IMPRIMIR</a>
			</div>
		</div>
		<div class="panel-body">
			<div class="row">
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">FECHA CONSULTA</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaInicio,'reunion').'&nbsp&nbsp&larr;&nbspAL&nbsp&rarr;&nbsp&nbsp'.infoFecha($fechaFin,'reunion') ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">ENTIDAD</span> <span class="pull-right text-danger"><?= $rotuloInfoEntidadBancaria ?></span></li>
					</ul>
				</div>
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TIPO DE EMISION</span> <span class="pull-right text-danger textoMayuscula"><?= $rotuloTipoCheque ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">CUENTA</span> <span class="pull-right text-danger"><?= $rotuloInfoCodigoCuenta ?></span></li>
					</ul>
				</div>
				<div class="col-sm-4">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">BENEFICIARIO</span> <span class="pull-right text-danger"><?= $infoUsuarioConsulta ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">CHEQUERA</span> <span class="pull-right text-danger"><?= $rotuloInfoCodigoChequera ?></span></li>
					</ul>
				</div>
			</div>

		</div>
	</div>

	<div id="busqueda" class="modal fade">
		<div class="modal-dialog modal-lg">
			<div class="modal-content bg-modal">
				<form method="GET">
					<div class="modal-body">
						<div class="form-group">
							<div class="row">
								<div class="col-md-6 col-xs-6">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon-calendar"></i></span>
										<input type="hidden" name="opcion" value="<?= $opcion ?>">
										<input type="text" name="fechaInicio" value="<?= $fechaInicio ?>" class="form-control input-lg fechas textoMayuscula" placeholder="Fecha de Inicio" tabindex="1">
									</div>
									<span class="label label-block bg-grey-300 text-left mt-5">Fecha inicio</span>
								</div>
								<div class="col-md-6 col-xs-6">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon-calendar"></i></span>
										<input type="text" name="fechaFin" value="<?= $fechaFin ?>" class="form-control input-lg fechas textoMayuscula" placeholder="Fecha de Fin" tabindex="2">
									</div>
									<span class="label label-block bg-grey-300 text-left mt-5">Fecha fin</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-12 col-xs-12">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon-user"></i></span>
										<select name="usuarioConsulta" class="seleccion select-lg">
										<?php
											$sql="SELECT codigoSocio FROM sm_mod_caja WHERE codigoBanco!='' AND codigoCuenta!='' AND codigoChequera!='' GROUP BY codigoSocio";
											$rs=mysqli_query($conexion,$sql);
											$i=1;
											while($datos=mysqli_fetch_array($rs)){
												if($i==1){
													if($usuarioConsulta=="ALL"){ 
														echo '<option value="ALL" selected>TODOS LOS BENEFICIARIOS O RESPONSABLES</option>';
													}else{
														echo '<option value="ALL">TODOS LOS BENEFICIARIOS O RESPONSABLES</option>';
													}
												}
												$documento =$datos[codigoSocio];
												$nombre    =infoSocios($documento,'nombre');
												if($nombre==""){ $infoNombre=datoUsuario($documento,'nombreFull'); }else{ $infoNombre=$nombre; }
												if($datos[0]==$usuarioConsulta AND $usuarioConsulta!="ALL"){
													echo '<option value="'.$datos[0].'" selected>'.texto($infoNombre).'</option>';
												}else{
													echo '<option value="'.$documento.'">'.texto($infoNombre).'</option>';
												}
												$i++;
											}
										?>
										</select>
									</div>
									<span class="label label-block bg-grey-300 text-left mt-5">Beneficiario o responsable de cheque a consultar</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-6 col-xs-12">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon-piggy-bank"></i></span>
										<select name="tipoCheque" class="seleccion select-lg">
											<?php
												if($tipoCheque=="ALL"){
													echo '
														<option value="">SELECCIONE TIPO DE EMISION</option>
														<option value="ALL" selected>TODOS LOS TIPO DE EMISION</option>
														<option value="PAR">TIPO DE EMISION POR PARTIDA</option>
														<option value="VAR">TIPO DE EMISION POR VARIOS CONCEPTOS</option>
													';
												}
											?>
											<?php
												if($tipoCheque=="VAR"){
													echo '
														<option value="">SELECCIONE TIPO DE EMISION</option>
														<option value="ALL">TODOS LOS TIPO DE EMISION</option>
														<option value="PAR">TIPO DE EMISION POR PARTIDA</option>
														<option value="VAR" selected>TIPO DE EMISION POR VARIOS CONCEPTOS</option>
													';
												}
											?>
											<?php
												if($tipoCheque=="PAR"){
													echo '
														<option value="">SELECCIONE TIPO DE EMISION</option>
														<option value="ALL">TODOS LOS TIPO DE EMISION</option>
														<option value="PAR" selected>TIPO DE EMISION POR PARTIDA</option>
														<option value="VAR">TIPO DE EMISION POR VARIOS CONCEPTOS</option>
													';
												}
											?>
										</select>
									</div>
									<span class="label label-block bg-grey-300 text-left mt-5">Tipo de emision de cheque</span>
								</div>
								<div class="col-md-6 col-xs-12">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon-piggy-bank"></i></span>
										<select name="entidadBancaria" class="seleccion select-lg">
										<?php
											$sql="SELECT sm_bancos.codigoBanco, sm_bancos.entidad FROM sm_bancos, sm_chequera WHERE sm_bancos.codigoBanco=sm_chequera.codigoBanco GROUP BY sm_bancos.codigoBanco";
											$rs=mysqli_query($conexion,$sql);
											$i=1;
											while($datos=mysqli_fetch_array($rs)){
												if($i==1){
													if($entidadBancaria=="ALL"){
														echo '<option value="ALL" selected>TODOS LOS BANCOS</option>';
													}else{
														echo '<option value="ALL">TODOS LOS BANCOS</option>';
													}
												}
												if($datos[0]==$entidadBancaria){
													echo '<option value="'.$datos[0].'" selected>'.texto($datos[1]).'</option>';
												}else{
													echo '<option value="'.$datos[0].'">'.texto($datos[1]).'</option>';
												}
												$i++;
											}
										?>
										</select>
									</div>
									<span class="label label-block bg-grey-300 text-left mt-5">Entidad Bancaria</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-12 col-xs-12">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon-direction"></i></span>
										<select name="codigoCuenta" class="seleccion select-lg">
										<?php
											$sql="SELECT codigoCuenta, codigoBanco, numeroCuenta, detalle, estado FROM sm_banco_cuentas";
											$rs=mysqli_query($conexion,$sql);
											$i=1;
											while($datos=mysqli_fetch_array($rs)){
												$codigoBanco  =$datos[codigoBanco];
												$numeroCuenta =$datos[numeroCuenta];
												$detalle      =$datos[detalle];
												$estado       =$datos[estado];
												if($estado=="ACT"){ $infoEstado="ACTIVO"; }else{ $infoEstado="INACTIVO";}
												$infoCuenta   ='CUENTA '.infoBancos($codigoBanco,'detalleEntidad').' - '.texto($detalle).' ('.$numeroCuenta.') &rarr; ['.$infoEstado.']';
												
												if($i==1){
													if($codigoCuenta=="ALL"){
														echo '<option value="ALL" selected>TODOS LAS CUENTAS</option>';
													}else{
														echo '<option value="ALL">TODOS LAS CUENTAS</option>';
													}
												}

												if($datos[0]==$codigoCuenta){
													echo '<option value="'.$datos[0].'" selected>'.$infoCuenta.'</option>';
												}else{
													echo '<option value="'.$datos[0].'">'.$infoCuenta.'</option>';
												}
												$i++;
											}
										?>
										</select>
									</div>
									<span class="label label-block bg-grey-300 text-left mt-5">Cuenta Bancaria</span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="row">
								<div class="col-md-12 col-xs-12">
									<div class="input-group">
										<span class="input-group-addon"><i class="icon-map4"></i></span>
										<select name="codigoChequera" class="seleccion select-lg">
										<?php
											$sql="SELECT codigoChequera, codigoBanco, codigoCuenta, detalle, estado FROM sm_chequera";
											$rs=mysqli_query($conexion,$sql);
											$i=1;
											while($datos=mysqli_fetch_array($rs)){
												if($i==1){
													if($entidadBancaria=="ALL"){
														echo '<option value="ALL" selected>TODOS LAS CHEQUERAS</option>';
													}else{
														echo '<option value="ALL">TODOS LAS CHEQUERAS</option>';
													}
												}

												$estado=$datos[estado];
												$codigoBanco    =$datos[codigoBanco];
												$c_codigoCuenta   =$datos[codigoCuenta];
												$detalle        =$datos[detalle];
												$detalleCTA     =infoCuentas($c_codigoCuenta,'','detalleCuenta');
												$numeroCuenta   =infoCuentas($c_codigoCuenta,'','numeroCuenta');
												$infoChequera   =infoBancos($codigoBanco,'detalleEntidad').' - '.$detalleCTA.' ('.$numeroCuenta.') - '.texto($detalle);
												$estadoCuenta   =infoCuentas($c_codigoCuenta,$codigoBanco,'estadoCuenta');
												if($estado=="ACT"){ $infoEstado="ACTIVO"; }else{ $infoEstado="INACTIVO";}

												if($datos[0]==$codigoChequera){
													echo '<option value="'.$datos[0].'" selected> CHEQUERA '.$infoChequera.' &rarr; ['.$infoEstado.']</option>';
												}else{
													echo '<option value="'.$datos[0].'"> CHEQUERA '.$infoChequera.' &rarr; ['.$infoEstado.']</option>';
												}
												$i++;
											}
										?>
										</select>
									</div>
									<span class="label label-block bg-grey-300 text-left mt-5">Chequera</span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer text-center">
						<input type="hidden" name="modulo" value="<?= $modulo ?>">
						<button type="button" class="btn btn-lg btn-warning" data-dismiss="modal">CERRAR</button>
						<button type="submit" class="btn btn-lg btn-success" tabindex="3">PROCESAR CONSULTA</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php } ?>