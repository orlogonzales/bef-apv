<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// ACCIONES AL ABRIR MODAL DE ASISNTES
		///////////////////////////////////////////////////
		$('#exportar').on('click', function() {
			$('#exportarSocios').on('shown.bs.modal', function() {
				$('#moduloExportar').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
				$("#moduloExportar").fadeIn("slow").load('../administrador/modulo/exportar-socios.php').fadeIn(1000).delay(1000);
			});
			$('#exportarSocios').on('hidden.bs.modal', function() {
				var datos='opcion=ELIMINA_JSON';
				$('#moduloExportar').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="../assets/images/loader.gif"><br>CARGANDO DATOS</div></div></div>');
				$("#moduloExportar").fadeIn("slow").load('../administrador/modulo/exportar-socios.php?'+datos).fadeIn(1000).delay(1000);
			});
		});
	});
</script>

<div id="exportarSocios" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content bg.modal">
			<div class="modal-header bg-brown">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h6 class="modal-title">EXPORTAR SOCIOS</h6>
			</div>
			<div id="moduloExportar"></div>
		</div>
	</div>
</div>

<div class="sidebar sidebar-main">
	<div class="sidebar-content">	
		<!-- DATOS DE USUARIO Y ROL EN SISTEMA -->
		<div class="sidebar-user">
			<div class="category-content">
				<div class="media">
					<a href="detalle-usuarios.php?dni=<?= $_SESSION['dni_apv'] ?>&opcion=detalles" class="media-left"><img src="<?= $ruta ?>assets/images/user/<?php echo $fotoUsuario ?>" class="img-circle img-sm" alt=""></a>
					<div class="media-body">
						<span class="media-heading text-semibold"><?php echo $nombrePaterno ?></span>
						<div class="text-size-mini text-muted"><?php echo $detalleRol ?></div>
					</div>
				</div>
			</div>
		</div>

		<!-- MENU SIDEBAR -->
		<?php
			if($menuActual=="dashboard"){ $op_dashboard='class="active"'; }else{ $op_dashboard=''; }
			if($menuActual=="usuarios"){ $op_usuarios='class="active"'; }else{ $op_usuarios=''; }
			if($menuActual=="listaSocios"){ $op_socios='class="active"'; }else{ $op_socios=''; }
			if($menuActual=="sincronizarSocios"){ $op_sync_socios='class="active"'; }else{ $op_sync_socios=''; }
			if($menuActual=="generarAsamblea"){ $op_gen_asamblea='class="active"'; }else{ $op_gen_asamblea=''; }
			if($menuActual=="listaAsambleas"){ $op_asambleas='class="active"'; }else{ $op_asambleas=''; }
			if($menuActual=="generarFaena"){ $op_gen_faena='class="active"'; }else{ $op_gen_faena=''; }
			if($menuActual=="listaFaenas"){ $op_faenas='class="active"'; }else{ $op_faenas=''; }
			if($menuActual=="generarCuota"){ $op_gen_cuotas='class="active"'; }else{ $op_gen_cuotas=''; }
			if($menuActual=="listaCuotas"){ $op_cuotas='class="active"'; }else{ $op_cuotas=''; }
			if($menuActual=="modTesoreria"){ $op_tesoreria='class="active"'; }else{ $op_tesoreria=''; }
			if($menuActual=="modNotas"){ $op_notas='class="active"'; }else{ $op_notas=''; }
			if($menuActual=="registroJuntaDirectiva"){ $op_registro_jd='class="active"'; }else{ $op_registro_jd=''; }
			if($menuActual=="periodosJuntaDirectiva"){ $op_periodos_jd='class="active"'; }else{ $op_periodos_jd=''; }
			if($menuActual=="reporteActividadesJD"){ $op_r_act_jd='class="active"'; }else{ $op_r_act_jd=''; }
			if($menuActual=="reporteCuotasJD"){ $op_r_cuo_jd='class="active"'; }else{ $op_r_cuo_jd=''; }
			if($menuActual=="reporteDeudasJD"){ $op_r_deu_jd='class="active"'; }else{ $op_r_deu_jd=''; }
			if($menuActual=="reporteCajaJD"){ $op_r_caj_jd='class="active"'; }else{ $op_r_caj_jd=''; }
		?>
		<div class="sidebar-category sidebar-category-visible">
			<div class="category-content no-padding">
				<ul class="navigation navigation-main navigation-accordion">
					<?php if($rolUsuario=='ADM'){ ?>
						<li <?= $op_dashboard ?>><a href="index.php"><i class="icon-home5"></i> <span>Dashboard</span></a></li>
						<li <?= $op_usuarios ?>><a href="detalle-usuarios.php?opcion=lista"><i class="icon-user"></i> <span>Usuarios</span></a></li>
						<!-- MODULO DE SOCIOS -->
						<li>
							<a href="#"><i class="icon-users4"></i> <span>Modulo de Socios</span></a>
							<ul>
								<li <?= $op_sync_socios ?>><a href="sincronizar-socios.php"><i class="fa fa-download"></i> Sincronizar Socios</a></li>
								<li <?= $op_socios ?>><a href="lista-socios.php"><i class="fa fa-th-list"></i> Lista de Socios</a></li>
								<li><a href="#exportarSocios" data-toggle="modal" id="exportar"><i class="fa fa-exchange"></i> Modulo de Exportación Socios</a></li>
							</ul>
						</li>
						<!-- MODULO DE ASAMBLEAS -->
						<li>
							<a href="#"><i class="icon-megaphone"></i> <span>Modulo de Asambleas</span></a>
							<ul>
								<li <?= $op_gen_asamblea ?>><a href="generar-actividad.php?tipoActividad=ASA"><i class="fa fa-cogs"></i> Generar Asamblea</a></li>
								<li <?= $op_asambleas ?>><a href="lista-asambleas.php"><i class="fa fa-th-list"></i> Lista de Asambleas</a></li>
							</ul>
						</li>
						<!-- MODULO DE FAENAS -->
						<li>
							<a href="#"><i class="icon-paint-format"></i> <span>Modulo de Faenas</span></a>
							<ul>
								<li <?= $op_gen_faena ?>><a href="generar-actividad.php?tipoActividad=FAE"><i class="fa fa-cogs"></i> Generar Faena</a></li>
								<li <?= $op_faenas ?>><a href="lista-faenas.php"><i class="fa fa-th-list"></i> Lista de Faenas</a></li>
							</ul>
						</li>
						<!-- MODULO DE CUOTAS -->
						<li>
							<a href="#"><i class="icon-cash4"></i> <span>Modulo de Cuotas</span></a>
							<ul>
								<li <?= $op_gen_cuotas ?>><a href="generar-cuota.php"><i class="fa fa-cogs"></i> Generar Cuotas</a></li>
								<li <?= $op_cuotas ?>><a href="lista-cuotas.php"><i class="fa fa-th-list"></i> Lista de Cuotas</a></li>
							</ul>
						</li>
						<!-- MODULO DE CAJA -->
						<li <?= $op_tesoreria ?>><a href="tesoreria.php?opcion=cajaING&modulo=formINGpB"><i class="icon-coin-dollar"></i> <span>Tesoreria</span></a></li>
						<!-- MODULO JUNTA DIRECTIVA -->					
						<li>
							<a href="#"><i class="fa fa-heart" aria-hidden="true"></i> <span>Modulo de Junta Directiva</span></a>
							<ul>
								<li <?= $op_registro_jd ?>><a href="junta-directiva-registro.php"><i class="fa fa-plus-square" aria-hidden="true"></i> Registro Junta Directiva</a></li>
								<li <?= $op_periodos_jd ?>><a href="junta-directiva-lista-periodos.php"><i class="fa fa-th-list"></i> Periodos Junta Directiva</a></li>							
								<li <?= $op_r_act_jd ?>><a href="junta-directiva-reporte-actividades.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Actividades</a></li>
								<li <?= $op_r_cuo_jd ?>><a href="junta-directiva-reporte-cuotas.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Cuotas</a></li>
								<li <?= $op_r_deu_jd ?>><a href="junta-directiva-reporte-deudas.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Deudas</a></li>
								<li <?= $op_r_caj_jd ?>><a href="junta-directiva-reporte-caja.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Caja</a></li>
							</ul>
						</li>
						<!-- MODULO OBSERVACIONES -->
						<li <?= $op_notas ?>><a href="notas.php"><i class="icon-pencil"></i> <span>Notas & Observaciones</span></a></li>
					<?php } ?>
					
					<?php if($rolUsuario=='TES'){ ?>
						<li <?= $op_dashboard ?>><a href="index.php"><i class="icon-home5"></i> <span>Dashboard</span></a></li>
						<!-- MODULO DE SOCIOS -->
						<li>
							<a href="#"><i class="icon-users4"></i> <span>Modulo de Socios</span></a>
							<ul>
								<li <?= $op_socios ?>><a href="lista-socios.php"><i class="fa fa-th-list"></i> Lista de Socios</a></li>
							</ul>
						</li>
						<!-- MODULO DE ASAMBLEAS -->
						<li>
							<a href="#"><i class="icon-megaphone"></i> <span>Modulo de Asambleas</span></a>
							<ul>
								<li <?= $op_asambleas ?>><a href="lista-asambleas.php"><i class="fa fa-th-list"></i> Lista de Asambleas</a></li>
							</ul>
						</li>
						<!-- MODULO DE FAENAS -->
						<li>
							<a href="#"><i class="icon-paint-format"></i> <span>Modulo de Faenas</span></a>
							<ul>
								<li <?= $op_faenas ?>><a href="lista-faenas.php"><i class="fa fa-th-list"></i> Lista de Faenas</a></li>
							</ul>
						</li>
						<!-- MODULO DE CUOTAS -->
						<li>
							<a href="#"><i class="icon-cash4"></i> <span>Modulo de Cuotas</span></a>
							<ul>
								<li <?= $op_cuotas ?>><a href="lista-cuotas.php"><i class="fa fa-th-list"></i> Lista de Cuotas</a></li>
							</ul>
						</li>
						<li>
							<a href="#"><i class="fa fa-heart" aria-hidden="true"></i> <span>Modulo de Junta Directiva</span></a>
							<ul>
								<li <?= $op_r_act_jd ?>><a href="junta-directiva-reporte-actividades.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Actividades</a></li>
								<li <?= $op_r_cuo_jd ?>><a href="junta-directiva-reporte-cuotas.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Cuotas</a></li>
								<li <?= $op_r_deu_jd ?>><a href="junta-directiva-reporte-deudas.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Deudas</a></li>
								<li <?= $op_r_caj_jd ?>><a href="junta-directiva-reporte-caja.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Caja</a></li>
							</ul>
						</li>
						<!-- MODULO OBSERVACIONES -->
						<li <?= $op_notas ?>><a href="notas.php"><i class="icon-pencil"></i> <span>Notas & Observaciones</span></a></li>
					<?php } ?>

					<?php if($rolUsuario=='JDP'){ ?>
						<li <?= $op_dashboard ?>><a href="index.php"><i class="icon-home5"></i> <span>Dashboard</span></a></li>
						<!-- MODULO DE SOCIOS -->
						<li>
							<a href="#"><i class="icon-users4"></i> <span>Modulo de Socios</span></a>
							<ul>
								<li <?= $op_sync_socios ?>><a href="sincronizar-socios.php"><i class="fa fa-download"></i> Sincronizar Socios</a></li>
								<li <?= $op_socios ?>><a href="lista-socios.php"><i class="fa fa-th-list"></i> Lista de Socios</a></li>
							</ul>
						</li>
						<!-- MODULO DE ASAMBLEAS -->
						<li>
							<a href="#"><i class="icon-megaphone"></i> <span>Modulo de Asambleas</span></a>
							<ul>
								<li <?= $op_gen_asamblea ?>><a href="generar-actividad.php?tipoActividad=ASA"><i class="fa fa-cogs"></i> Generar Asamblea</a></li>
								<li <?= $op_asambleas ?>><a href="lista-asambleas.php"><i class="fa fa-th-list"></i> Lista de Asambleas</a></li>
							</ul>
						</li>
						<!-- MODULO DE FAENAS -->
						<li>
							<a href="#"><i class="icon-paint-format"></i> <span>Modulo de Faenas</span></a>
							<ul>
								<li <?= $op_gen_faena ?>><a href="generar-actividad.php?tipoActividad=FAE"><i class="fa fa-cogs"></i> Generar Faena</a></li>
								<li <?= $op_faenas ?>><a href="lista-faenas.php"><i class="fa fa-th-list"></i> Lista de Faenas</a></li>
							</ul>
						</li>
						<!-- MODULO DE CUOTAS -->
						<li>
							<a href="#"><i class="icon-cash4"></i> <span>Modulo de Cuotas</span></a>
							<ul>
								<li <?= $op_gen_cuotas ?>><a href="generar-cuota.php"><i class="fa fa-cogs"></i> Generar Cuotas</a></li>
								<li <?= $op_cuotas ?>><a href="lista-cuotas.php"><i class="fa fa-th-list"></i> Lista de Cuotas</a></li>
							</ul>
						</li>
						<!-- MODULO DE CAJA -->
						<li <?= $op_tesoreria ?>><a href="tesoreria.php?opcion=cajaING&modulo=formINGpB"><i class="icon-coin-dollar"></i> <span>Tesoreria</span></a></li>
						<!-- MODULO JUNTA DIRECTIVA -->					
						<li>
							<a href="#"><i class="fa fa-heart" aria-hidden="true"></i> <span>Modulo de Junta Directiva</span></a>
							<ul>
								<li <?= $op_registro_jd ?>><a href="junta-directiva-registro.php"><i class="fa fa-plus-square" aria-hidden="true"></i> Registro Junta Directiva</a></li>
								<li <?= $op_periodos_jd ?>><a href="junta-directiva-lista-periodos.php"><i class="fa fa-th-list"></i> Periodos Junta Directiva</a></li>							
								<li <?= $op_r_act_jd ?>><a href="junta-directiva-reporte-actividades.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Actividades</a></li>
								<li <?= $op_r_cuo_jd ?>><a href="junta-directiva-reporte-cuotas.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Cuotas</a></li>
								<li <?= $op_r_deu_jd ?>><a href="junta-directiva-reporte-deudas.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Deudas</a></li>
								<li <?= $op_r_caj_jd ?>><a href="junta-directiva-reporte-caja.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Caja</a></li>
							</ul>
						</li>
						<!-- MODULO OBSERVACIONES -->
						<li <?= $op_notas ?>><a href="notas.php"><i class="icon-pencil"></i> <span>Notas & Observaciones</span></a></li>
					<?php } ?>

					<?php if($rolUsuario=='JDT'){ ?>
						<li <?= $op_dashboard ?>><a href="index.php"><i class="icon-home5"></i> <span>Dashboard</span></a></li>
						<!-- MODULO DE SOCIOS -->
						<li>
							<a href="#"><i class="icon-users4"></i> <span>Modulo de Socios</span></a>
							<ul>
								<li <?= $op_socios ?>><a href="lista-socios.php"><i class="fa fa-th-list"></i> Lista de Socios</a></li>
							</ul>
						</li>
						<!-- MODULO DE ASAMBLEAS -->
						<li>
							<a href="#"><i class="icon-megaphone"></i> <span>Modulo de Asambleas</span></a>
							<ul>
								<li <?= $op_asambleas ?>><a href="lista-asambleas.php"><i class="fa fa-th-list"></i> Lista de Asambleas</a></li>
							</ul>
						</li>
						<!-- MODULO DE FAENAS -->
						<li>
							<a href="#"><i class="icon-paint-format"></i> <span>Modulo de Faenas</span></a>
							<ul>
								<li <?= $op_faenas ?>><a href="lista-faenas.php"><i class="fa fa-th-list"></i> Lista de Faenas</a></li>
							</ul>
						</li>
						<!-- MODULO DE CUOTAS -->
						<li>
							<a href="#"><i class="icon-cash4"></i> <span>Modulo de Cuotas</span></a>
							<ul>
								<li <?= $op_cuotas ?>><a href="lista-cuotas.php"><i class="fa fa-th-list"></i> Lista de Cuotas</a></li>
							</ul>
						</li>
						<!-- MODULO DE CAJA -->
						<li <?= $op_tesoreria ?>><a href="tesoreria.php?opcion=cajaING&modulo=formINGpB"><i class="icon-coin-dollar"></i> <span>Tesoreria</span></a></li>
						<!-- MODULO JUNTA DIRECTIVA -->					
						<li>
							<a href="#"><i class="fa fa-heart" aria-hidden="true"></i> <span>Modulo de Junta Directiva</span></a>
							<ul>
								<li <?= $op_r_act_jd ?>><a href="junta-directiva-reporte-actividades.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Actividades</a></li>
								<li <?= $op_r_cuo_jd ?>><a href="junta-directiva-reporte-cuotas.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Cuotas</a></li>
								<li <?= $op_r_deu_jd ?>><a href="junta-directiva-reporte-deudas.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Deudas</a></li>
								<li <?= $op_r_caj_jd ?>><a href="junta-directiva-reporte-caja.php"><i class="fa fa-print" aria-hidden="true"></i> Reportes de Caja</a></li>
							</ul>
						</li>
						<!-- MODULO OBSERVACIONES -->
						<li <?= $op_notas ?>><a href="notas.php"><i class="icon-pencil"></i> <span>Notas & Observaciones</span></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>