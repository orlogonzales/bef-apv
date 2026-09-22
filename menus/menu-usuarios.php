<?php
	if($opcion=="lista"){ $lista='class="active"'; }
	if($opcion=="detalles"){ $detalles='class="active"'; }
	if($opcion=="editar"){ $editar='class="active"'; }
	if($opcion=="agregar"){ $agregar='class="active"'; }
	if($opcion=="operacionesUser"){ $operacionesUser='class="active"'; }
	if($opcion=="operacionesUsers"){ $operacionesUsers='class="active"'; }
?>
<div class="navbar navbar-default navbar-xs">
	<ul class="nav navbar-nav visible-xs-block">
		<li class="full-width text-center"><a data-toggle="collapse" data-target="#navbar-filter"><i class="icon-menu7"></i></a></li>
	</ul>

	<div class="navbar-collapse collapse" id="navbar-filter">
		<ul class="nav navbar-nav element-active-slate-400 textoMayuscula">
			<li <?= $lista ?>><a href="detalle-usuarios.php?opcion=lista">Usuarios</a></li>
			<li <?= $agregar ?>><a href="detalle-usuarios.php?opcion=agregar">Agregar Usuario</a></li>
			<li <?= $detalles ?>><a href="detalle-usuarios.php?dni=<?= $dni ?>&opcion=detalles">Detalles Usuario</a></li>
			<li <?= $editar ?>><a href="detalle-usuarios.php?dni=<?= $dni ?>&opcion=editar">Editar Usuario</a></li>
			<li <?= $operacionesUser ?>><a href="detalle-usuarios.php?opcion=operaciones">Bitacora</a></li>
		</ul>
	</div>
</div>