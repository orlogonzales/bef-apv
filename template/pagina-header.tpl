<div class="page-header">
	<div class="page-header-content">
		<div class="page-title">
			<div class="row">
				<div class="col-md-6">
					<h4><i class="icon-arrow-left52 position-left"></i> <?php echo $tituloPagina ?></h4>
				</div>
				<div class="col-md-6 text-right">
					<span class="badge badge-warning pl-15 pr-15">
						<?php if($rolUsuario=='ADM'){ ?>
							<h4><?php echo $descripcionRolAPV ?></h4>
						<?php }else{ ?>
							<h4 class="text-uppercase"><?php echo $descripcionRolAPV.'&nbsp;&nbsp;&raquo;&nbsp;&nbsp;'.$infoJDAPV ?></h4>
						<?php } ?>
					</span>
				</div>
			</div>
		</div>
	</div>
	<?php if($menuTop){ include($ruta.'menus/'.$menuTop); } ?>
</div>