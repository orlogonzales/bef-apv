<?php
	if($opcion=="detalles"){ $detalles='class="active"'; }
	if($opcion=="pagados"){ $pagados='class="active"'; }
	if($opcion=="deben"){ $deben='class="active"'; }
	if($opcion=="operaciones"){ $operaciones='class="active"'; }
?>
<div class="navbar navbar-default navbar-xs">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="icon-menu7"></i></a></li>
	</ul>

	<div class="navbar-collapse collapse textoMayuscula" id="navbar-filter">
		<ul class="nav navbar-nav element-active-slate-400">
			<li <?= $detalles ?>><a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=detalles&conceptoCuota=<?= $conceptoCuota ?>">Detalles</a></li>
			<li <?= $pagados ?>><a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=pagados&conceptoCuota=<?= $conceptoCuota ?>">Cuotas Pagadas</a></li>
			<li <?= $deben ?>><a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=deben&conceptoCuota=<?= $conceptoCuota ?>">Cuotas sin Pago</a></li>
			<?php if($permitido=="SI"){ ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">Reportes <span class="caret"></span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="../documentos/reporte-cuotas.php?conceptoPago=<?= $codigoCuota ?>&opcion=reporte_socios">Reporte de Socios - cuota</a></li>
						<li><a href="../documentos/reporte-cuotas.php?conceptoPago=<?= $codigoCuota ?>&opcion=reporte_socios_pagaron">Reporte de Socios - pagaron cuota</a></li>
						<li><a href="../documentos/reporte-cuotas.php?conceptoPago=<?= $codigoCuota ?>&opcion=reporte_socios_deben">Reporte de Socios - deben cuota</a></li>
					</ul>
				</li>
			<?php } ?>
			<?php if($permitido=="SI"){ ?><li <?= $operaciones ?>><a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=operaciones&conceptoCuota=<?= $conceptoCuota ?>">Bitacora</a></li><?php } ?>
		</ul>
	</div>
</div>