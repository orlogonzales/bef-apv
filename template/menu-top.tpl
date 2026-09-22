	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-header">
			<a class="navbar-brand" href="index.php"><img src="<?= $ruta ?>assets/images/logo.png" alt=""></a>
			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>
		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
			<!-- DATOS DE USUARIO LOGUEADO Y MENU DE USUARIO LOGUEADO -->
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<img src="<?= $ruta ?>assets/images/user/<?php echo $fotoUsuario ?>" alt="">
						<span class="textoNormal"><?php echo $nombreUsuario ?></span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="detalle-usuarios.php?dni=<?= $_SESSION['dni_apv'] ?>&opcion=detalles"><i class="icon-user-plus"></i> Mi perfil</a></li>
						<li><a href="javascript:void(0)" id="btn-salir"><i class="icon-switch2"></i> Salir de sistema</a></li>
					</ul>
				</li>
			</ul>
		</div>
	</div>