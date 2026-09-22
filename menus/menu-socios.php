<?php
	if($opcion=="detalles"){ $detalles='class="active"'; }
	if($opcion=="asistencias_cuotas"){ $asistencias_cuotas='class="active"'; }
	if($opcion=="pagos_asambleas"){ $pagos_asambleas='class="active"'; }
	if($opcion=="historial_pagos_asambleas"){ $historial_pagos_asambleas='class="active"'; }

	if($opcion=="pagos_faenas"){ $pagos_faenas='class="active"'; }
	if($opcion=="historial_pagos_faenas"){ $historial_pagos_faenas='class="active"'; }

	if($opcion=="pagos_cuotas"){ $pagos_cuotas='class="active"'; }
	if($opcion=="historial_pagos_cuotas"){ $historial_pagos_cuotas='class="active"'; }

	if($opcion=="historial_exoneraciones_asambleas"){ $historial_exoneraciones_asambleas='class="active"'; }
	if($opcion=="historial_exoneraciones_faenas"){ $historial_exoneraciones_faenas='class="active"'; }
	if($opcion=="historial_exoneraciones_cuotas"){ $historial_exoneraciones_cuotas='class="active"'; }
	
	$nroDeudasAsambleas =infoSocios($codigoSocio,'nroDeudasAsambleas');
	$nroPagosAsambleas  =infoSocios($codigoSocio,'nroPagosAsambleas');

	$nroDeudasFaenas    =infoSocios($codigoSocio,'nroDeudasFaenas');
	$nroPagosFaenas     =infoSocios($codigoSocio,'nroPagosFaenas');

	$nroDeudasCuotas    =infoSocios($codigoSocio,'nroDeudasCuotas');
	$nroPagosCuotas     =infoSocios($codigoSocio,'nroPagosCuotas');

	if($nroDeudasAsambleas>0){ $ndASA='<span class="badge badge-success badge-inline position-right">'.ceros($nroDeudasAsambleas,2).'</span>'; }else{ $ndASA=''; }
	if($nroPagosAsambleas>0){ $npASA='<span class="badge badge-success badge-inline position-right">'.ceros($nroPagosAsambleas,2).'</span>'; }else{ $npASA=''; }

	if($nroDeudasFaenas>0){ $ndFAE='<span class="badge badge-success badge-inline position-right">'.ceros($nroDeudasFaenas,2).'</span>'; }else{ $ndFAE=''; }
	if($nroPagosFaenas>0){ $npFAE='<span class="badge badge-success badge-inline position-right">'.ceros($nroPagosFaenas,2).'</span>'; }else{ $npFAE=''; }

	if($nroDeudasCuotas>0){ $ndCUO='<span class="badge badge-success badge-inline position-right">'.ceros($nroDeudasCuotas,2).'</span>'; }else{ $ndCUO=''; }
	if($nroPagosCuotas>0){ $npCUO='<span class="badge badge-success badge-inline position-right">'.ceros($nroPagosCuotas,2).'</span>'; }else{ $npCUO=''; }

	/////////////////////////////////////////////////////////////////////
	/// ESTADISTICAS DE EXONERACIONES
	/////////////////////////////////////////////////////////////////////
	$totalExoneradoSocio=infoExoneraciones($codigoSocio,'','totalExoneradoSocio');
	$totalExoneradoSocioASA=infoExoneraciones($codigoSocio,'','totalExoneradoSocioASA');
	$totalExoneradoSocioFAE=infoExoneraciones($codigoSocio,'','totalExoneradoSocioFAE');
	$totalExoneradoSocioCUO=infoExoneraciones($codigoSocio,'','totalExoneradoSocioCUO');

	if($totalExoneradoSocio>0){ $infoTotalExoneradoSocio='- S/. '.moneda($totalExoneradoSocio); }else{ $infoTotalExoneradoSocio='<i class="fa fa-ellipsis-h"></i>'; }
	if($totalExoneradoSocioASA>0){ $infoTotalExoneradoSocioASA='- S/. '.moneda($totalExoneradoSocioASA); }else{ $infoTotalExoneradoSocioASA='<i class="fa fa-ellipsis-h"></i>'; }
	if($totalExoneradoSocioFAE>0){ $infoTotalExoneradoSocioFAE='- S/. '.moneda($totalExoneradoSocioFAE); }else{ $infoTotalExoneradoSocioFAE='<i class="fa fa-ellipsis-h"></i>'; }
	if($totalExoneradoSocioCUO>0){ $infoTotalExoneradoSocioCUO='- S/. '.moneda($totalExoneradoSocioCUO); }else{ $infoTotalExoneradoSocioCUO='<i class="fa fa-ellipsis-h"></i>'; }

	$totalPagosExoneradoSocio=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocio');
	$totalPagosExoneradoSocioASA=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioASA');
	$totalPagosExoneradoSocioFAE=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioFAE');
	$totalPagosExoneradoSocioCUO=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioCUO');

	if($totalPagosExoneradoSocio>0){ $infoTotalPagosExoneradoSocio='S/. '.moneda($totalPagosExoneradoSocio); }else{ $infoTotalPagosExoneradoSocio='<i class="fa fa-ellipsis-h"></i>'; }
	if($totalPagosExoneradoSocioASA>0){ $infoTotalPagosExoneradoSocioASA='S/. '.moneda($totalPagosExoneradoSocioASA); }else{ $infoTotalPagosExoneradoSocioASA='<i class="fa fa-ellipsis-h"></i>'; }
	if($totalPagosExoneradoSocioFAE>0){ $infoTotalPagosExoneradoSocioFAE='S/. '.moneda($totalPagosExoneradoSocioFAE); }else{ $infoTotalPagosExoneradoSocioFAE='<i class="fa fa-ellipsis-h"></i>'; }
	if($totalPagosExoneradoSocioCUO>0){ $infoTotalPagosExoneradoSocioCUO='S/. '.moneda($totalPagosExoneradoSocioCUO); }else{ $infoTotalPagosExoneradoSocioCUO='<i class="fa fa-ellipsis-h"></i>'; }
?>
<div class="navbar navbar-default navbar-xs">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="icon-menu7"></i></a></li>
	</ul>

	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav element-active-slate-400 textoMayuscula">
			<li <?= $detalles ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=detalles">Detalles</a></li>
			<li <?= $asistencias_cuotas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=asistencias_cuotas">Asistencias / Cuotas</a></li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Pagos Pendientes <span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-right">
					<li <?= $pagos_asambleas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=pagos_asambleas">Pagos Pendientes - Asambleas <?= $ndASA ?></a></li>
					<li <?= $pagos_faenas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=pagos_faenas">Pagos Pendientes - Faenas <?= $ndFAE ?></a></li>
					<li <?= $pagos_cuotas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=pagos_cuotas">Pagos Pendientes - Cuotas <?= $ndCUO ?></a></li>
				</ul>
			</li>
			<li class="dropdown <?= $menu_asambleas.$menu_faenas.$menu_cuotas ?> ?>">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown">Pagos Realizados <span class="caret"></span></a>
				<ul class="dropdown-menu dropdown-menu-right">
					<li <?= $historial_pagos_asambleas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_pagos_asambleas"><i class="icon-credit-card2"></i> Pagos Realizados - Asambleas <?= $npASA ?></a></li>
					<li <?= $historial_pagos_faenas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_pagos_faenas"><i class="icon-credit-card2"></i> Pagos Realizados -  Faenas <?= $npFAE ?></a></li>
					<li <?= $historial_pagos_cuotas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_pagos_cuotas"><i class="icon-credit-card2"></i> Pagos Realizados - Cuotas <?= $npCUO ?></a></li>
				</ul>
			</li>
			<?php if($totalExoneradoSocio>0){ ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Exoneraciones <span class="caret"></span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<?php if($totalExoneradoSocioASA>0){ ?><li <?= $historial_exoneraciones_asambleas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_asambleas"><i class="icon-credit-card2"></i> Exoneraciones - Asambleas</a></li><?php } ?>
						<?php if($totalExoneradoSocioFAE>0){ ?><li <?= $historial_exoneraciones_faenas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_faenas"><i class="icon-credit-card2"></i> Exoneraciones -  Faenas</a></li><?php } ?>
						<?php if($totalExoneradoSocioCUO>0){ ?><li <?= $historial_exoneraciones_cuotas ?>><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_cuotas"><i class="icon-credit-card2"></i> Exoneraciones - Cuotas</a></li><?php } ?>
					</ul>
				</li>
			<?php } ?>
		</ul>
	</div>
</div>