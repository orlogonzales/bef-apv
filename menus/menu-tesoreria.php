<?php
	if($opcion=="cajaING"){ $cajaING='class="active"'; }
	if($opcion=="cajaINGCUO"){ $cajaINGCUO='class="active"'; }
	if($opcion=="cajaINGASA"){ $cajaINGASA='class="active"'; }
	if($opcion=="cajaINGFAE"){ $cajaINGFAE='class="active"'; }
	if($opcion=="cajaINGPAR"){ $cajaINGPAR='class="active"'; }
	if($opcion=="partidas"){ $partidas='class="active"'; }
	if($opcion=="verPartida"){ $partidas='class="active"'; }
	if($opcion=="cajaSAL"){ $cajaSAL='class="active"'; }
	if($opcion=="operacionesCaja"){ $operaciones='class="active"'; }
	if($opcion=="manPartidas"){ $manPartidas='class="active"'; }
	if($opcion=="operacionespartidas"){ $operaciones='class="active"'; }
	if($opcion=="bancos"){ $bancos='class="active"'; }
	if($opcion=="cuentasBancarias"){ $cuentasBancarias='class="active"'; }
	if($opcion=="chequeras"){ $chequeras='class="active"'; }
	if($opcion=="cheques"){ $cheques='class="active"'; }
	if($opcion=="emisionCheques"){ $emisionCheques='class="active"'; }

	if($opcion=="conceptoASA"){ $conceptoASA='class="active"'; }
	if($opcion=="conceptoFAE"){ $conceptoFAE='class="active"'; }
	if($opcion=="conceptoCUO"){ $conceptoCUO='class="active"'; }


	if($_GET[fechaInicio]){ $fechaInicio=$_GET[fechaInicio]; }else{ $fechaInicio=infoFecha(infoTiempo('primerDia'),'resultados'); }
	if($_GET[fechaFin]){ $fechaFin=$_GET[fechaFin]; }else{ $fechaFin=infoFecha(infoTiempo('ultimoDia'),'resultados'); }
	if($_GET[usuarioConsulta]){ $usuarioConsulta=$_GET[usuarioConsulta]; }else{ $usuarioConsulta="ALL"; }
	if($_GET[tipoCheque]){ $tipoCheque=$_GET[tipoCheque]; }else{ $tipoCheque="ALL"; }
	if($_GET[entidadBancaria]){ $entidadBancaria=$_GET[entidadBancaria]; }else{ $entidadBancaria="ALL"; }
	if($_GET[codigoCuenta]){ $codigoCuenta=$_GET[codigoCuenta]; }else{ $codigoCuenta="ALL"; }
	if($_GET[codigoChequera]){ $codigoChequera=$_GET[codigoChequera]; }else{ $codigoChequera="ALL"; }

	$consultaFechaIni=fechaSQL($fechaInicio);
	$consultaFechaFin=fechaSQL($fechaFin);

	$contarPartidas=infoPartida($codigoPartida,'contarPartidas');	
	$reporte_caja_SAL=$ruta.'documentos/salidas-caja.php?fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin.'&usuarioConsulta='.$usuarioConsulta;
	$reporte_caja_ALL=$ruta.'documentos/ingresos-caja.php?fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin.'&usuarioConsulta='.$usuarioConsulta.'&tipoActividad=ALL';
	$reporte_caja_CUO=$ruta.'documentos/ingresos-caja.php?fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin.'&usuarioConsulta='.$usuarioConsulta.'&tipoActividad=CUO';
	$reporte_caja_ASA=$ruta.'documentos/ingresos-caja.php?fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin.'&usuarioConsulta='.$usuarioConsulta.'&tipoActividad=ASA';
	$reporte_caja_FAE=$ruta.'documentos/ingresos-caja.php?fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin.'&usuarioConsulta='.$usuarioConsulta.'&tipoActividad=FAE';
	$reporte_caja_PAR=$ruta.'documentos/ingresos-caja.php?fechaInicio='.$fechaInicio.'&fechaFin='.$fechaFin.'&usuarioConsulta='.$usuarioConsulta.'&tipoActividad=PAR';
?>
<div class="navbar navbar-default navbar-xs">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="icon-menu7"></i></a></li>
	</ul>

	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav element-active-slate-400 textoMayuscula">
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Ingresos caja <span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-left">
					<li <?= $cajaING ?>><a href="tesoreria.php?opcion=cajaING&fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>&usuarioConsulta=<?= $usuarioConsulta ?>&modulo=formINGpB">Ingresos a caja &rarr; conceptos propios</a></li>
					<li <?= $cajaINGCUO ?>><a href="tesoreria.php?opcion=cajaINGCUO&fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>&usuarioConsulta=<?= $usuarioConsulta ?>&modulo=formINGpB">Ingresos a caja &rarr; cuotas</a></li>
					<li <?= $cajaINGASA ?>><a href="tesoreria.php?opcion=cajaINGASA&fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>&usuarioConsulta=<?= $usuarioConsulta ?>&modulo=formINGpB">Ingresos a caja &rarr; asambleas</a></li>
					<li <?= $cajaINGFAE ?>><a href="tesoreria.php?opcion=cajaINGFAE&fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>&usuarioConsulta=<?= $usuarioConsulta ?>&modulo=formINGpB">Ingresos a caja &rarr; faenas</a></li>
					<li <?= $cajaINGPAR ?>><a href="tesoreria.php?opcion=cajaINGPAR&fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>&usuarioConsulta=<?= $usuarioConsulta ?>&modulo=formINGpB">Ingresos a caja &rarr; partidas</a></li>
				</ul>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Partidas <span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-left">
					<li <?= $partidas ?>><a href="tesoreria.php?opcion=partidas">Lista de partidas</a></li>
					<?php if($rolUsuario=='ADM' || $rolUsuario=='JDP'){ ?>
						<?php if($opcion=="partidas"){ ?><li><a href="#crearPartida" data-toggle="modal">Generar nueva partida de gastos</a></li><?php } ?>
					<?php } ?>
					<?php if($contarPartidas>0){ ?><li <?= $opPartidas ?>><a href="tesoreria.php?opcion=operacionespartidas">Bitacora de partidas</a></li><?php } ?>
				</ul>
			</li>
			<?php if($rolUsuario=='ADM' || $rolUsuario=='JDT'){ ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Bancos <span class="caret"></span></a>
					<ul class="dropdown-menu dropdown-menu-left">
						<li <?= $bancos ?>><a href="tesoreria.php?opcion=bancos">Registro de entidades bancarias</a></li>
						<li <?= $cuentasBancarias ?>><a href="tesoreria.php?opcion=cuentasBancarias">Cuentas bancarias</a></li>
						<li <?= $chequeras ?>><a href="tesoreria.php?opcion=chequeras">Registro de chequeras</a></li>
					</ul>
				</li>
			<?php } ?>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Cheques <span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-left">
					<?php if($rolUsuario=='ADM' || $rolUsuario=='JDT'){ ?>
						<li <?= $emisionCheques ?>><a href="tesoreria.php?opcion=emisionCheques">Registro de emision de cheques</a></li>
					<?php } ?>
					<li <?= $cheques ?>><a href="tesoreria.php?opcion=cheques&fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>&tipoCheque=<?= $tipoCheque ?>&entidadBancaria=<?= $entidadBancaria ?>&codigoCuenta=<?= $codigoCuenta ?>&codigoChequera=<?= $codigoChequera ?>&usuarioConsulta=<?= $usuarioConsulta ?>&modulo=formCHB">Reporte de cheques emitidos</a></li>
				</ul>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Egresos caja <span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-left">
					<li <?= $cajaSAL ?>><a href="tesoreria.php?opcion=cajaSAL&fechaInicio=<?= $fechaInicio ?>&fechaFin=<?= $fechaFin ?>&usuarioConsulta=<?= $usuarioConsulta ?>&modulo=formINGpB">Consolidado de egresos de caja</a></li>
					<?php if($rolUsuario=='ADM' || $rolUsuario=='JDT'){ ?>
						<li><a href="#registroSalidas" data-toggle="modal">Registro de egresos de caja</a></li>
					<?php } ?>
				</ul>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Reportes <span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-left">
					<li><a href="<?= $reporte_caja_SAL ?>">Reporte de salidas de caja</a></li>
					<li><a href="<?= $reporte_caja_ALL ?>">Reporte de ingresos a caja</a></li>
					<li><a href="<?= $reporte_caja_CUO ?>">Reporte de ingresos por cuotas</a></li>
					<li><a href="<?= $reporte_caja_ASA ?>">Reporte de ingresos por asambleas</a></li>
					<li><a href="<?= $reporte_caja_FAE ?>">Reporte de ingresos por faenas</a></li>
					<li><a href="<?= $reporte_caja_FAE ?>">Reporte de ingresos por partida</a></li>
				</ul>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">CONCEPTOS PAGO <span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-left">
					<li <?= $conceptoASA ?>><a href="tesoreria.php?opcion=conceptoASA">Concepto - Asambleas</a></li>
					<li <?= $conceptoFAE ?>><a href="tesoreria.php?opcion=conceptoFAE">Concepto - Faenas</a></li>
					<li <?= $conceptoCUO ?>><a href="tesoreria.php?opcion=conceptoCUO">Concepto - Cuotas</a></li>
				</ul>
			</li>
			<li <?= $operaciones ?>><a href="tesoreria.php?opcion=operacionesCaja">Bitacora</a></li>
		</ul>
	</div>
</div>