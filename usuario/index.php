<?php
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="SISTEMA DE CONTROL DE SOCIOS";
	$menuActual="dashboard";

	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	$ruta="../";
	include($ruta.'template/header.tpl');
	$hoy       =infoTiempo('fecha');

	if($_SESSION['rol_apv']=='ADM' || $_SESSION['rol_apv']=='ADM'){
		$idJuntaDirectiva='ALL';
	}else{
		if(strlen($_SESSION['idJDActual'])>0){
			$idJuntaDirectiva=$_SESSION['idJDActual'];
		}else{
			$idJuntaDirectiva='ALL';
		}
	}

	$totalGeneralASA=infoTotalesGeneral($idJuntaDirectiva,'ASA');
	$totalGeneralFAE=infoTotalesGeneral($idJuntaDirectiva,'FAE');
	$totalGeneralCUO=infoTotalesGeneral($idJuntaDirectiva,'CUO');
	$totalGeneralConceptos=$totalGeneralASA+$totalGeneralFAE+$totalGeneralCUO;

	if($totalGeneralASA>0){
		$infoTotalGeneralASA='S/. '.moneda($totalGeneralASA);
	}else{
		$infoTotalGeneralASA='S/. --';
	}

	if($totalGeneralFAE>0){
		$infoTotalGeneralFAE='S/. '.moneda($totalGeneralFAE);
	}else{
		$infoTotalGeneralFAE='S/. --';
	}

	if($totalGeneralCUO>0){
		$infoTotalGeneralCUO='S/. '.moneda($totalGeneralCUO);
	}else{
		$infoTotalGeneralCUO='S/. --';
	}

	if($totalGeneralConceptos>0){
		$infoTotalGeneralConceptos='S/. '.moneda($totalGeneralConceptos);
	}else{
		$infoTotalGeneralConceptos='S/. --';
	}

	$deudasASA = 'S/. '.moneda(infoActividad($idJuntaDirectiva,'','','totalDeudasASA'));
	$deudasFAE = 'S/. '.moneda(infoActividad($idJuntaDirectiva,'','','totalDeudasFAE'));
	$deudasCUO = 'S/. '.moneda(infoCuota($idJuntaDirectiva,'','totalDeudasCUO'));
	$nroASA    = infoActividad($idJuntaDirectiva,'ASA','','cantidadActividades');
	$nroFAE    = infoActividad($idJuntaDirectiva,'FAE','','cantidadActividades');
	$nroCUO    = infoCuota($idJuntaDirectiva,'','cantidadCuotas');

	$consultaFechaIni ='2014-01-01';
	$consultaFechaFin =$hoy;
	$montoDisponible  =montoDisponible('','');
	
	$totalASA =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','ASA','','ALL','totalActividades');
	$totalFAE =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','FAE','','ALL','totalActividades');
	$totalCUO =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','CUO','','ALL','totalActividades');
	$totalING =infoCaja($consultaFechaIni,$consultaFechaFin,'','','','ALL','totalING');
	$totalPAR =infoPartida($codigoPartida,'totalPartidas');
	$totalSAL =infoCaja($consultaFechaIni,$consultaFechaFin,'','','','ALL','totalSAL');;
	$totalEGR =$totalPAR+$totalSAL;
	$totalCJA =$totalING-$totalEGR;
	$totalCLS =infoPartida('','totalPartidasCierre');
	$toPARSAL =infoPartida('','totalPartidasDispuesto');
	$toPARPEN =$totalPAR-($toPARSAL+$totalCLS);

	$totalDeudalASA=infoDeudas('ASA');
	$totalDeudalFAE=infoDeudas('FAE');
	$totalDeudalCUO=infoDeudas('CUO');

	if($totalING>0){ $infoTotalING="S/. ".moneda($totalING); }else{ $infoTotalING="S/. ----"; }
	if($totalASA>0){ $infoTotalASA="S/. ".moneda($totalASA); }else{ $infoTotalASA="S/. ----"; }
	if($totalFAE>0){ $infoTotalFAE="S/. ".moneda($totalFAE); }else{ $infoTotalFAE="S/. ----"; }
	if($totalCUO>0){ $infoTotalCUO="S/. ".moneda($totalCUO); }else{ $infoTotalCUO="S/. ----"; }
	if($totalEGR>0){ $infoTotalEGR="S/. ".moneda($totalEGR); }else{ $infoTotalEGR="S/. ----"; }
	if($totalCJA>0){ $infoTotalCJA="S/. ".moneda($totalCJA); }else{ $infoTotalCJA="S/. ----"; }
	if($totalSAL>0){ $infoTotalSAL="S/. ".moneda($totalSAL); }else{ $infoTotalSAL="S/. ----"; }
	if($totalPAR>0){ $infoTotalPAR="S/. ".moneda($totalPAR); }else{ $infoTotalPAR="S/. ----"; }
	if($totalCLS>0){ $infoTotalCLS="S/. ".moneda($totalCLS); }else{ $infoTotalCLS="S/. ----"; }
	if($toPARPEN>0){ $infoToPARPEN="S/. ".moneda($toPARPEN); }else{ $infoToPARPEN="S/. ----"; }
	if($toPARSAL>0){ $infoToPARSAL="S/. ".moneda($toPARSAL); }else{ $infoToPARSAL="S/. ----"; }

	if($totalDeudalASA>0){ $infoTotalDeudalASA="S/. ".moneda($totalDeudalASA); }else{ $infoTotalDeudalASA="S/. ----"; }
	if($totalDeudalFAE>0){ $infoTotalDeudalFAE="S/. ".moneda($totalDeudalFAE); }else{ $infoTotalDeudalFAE="S/. ----"; }
	if($totalDeudalCUO>0){ $infoTotalDeudalCUO="S/. ".moneda($totalDeudalCUO); }else{ $infoTotalDeudalCUO="S/. ----"; }
?>
<script type="text/javascript">
	$(document).ready(function(){
		///////////////////////////////////////////////////
		/// BUSCAR SOCIO POR CODIGO
		///////////////////////////////////////////////////
		$("button#bt_buscar_socio_cod").click(function(){
			var ruta        ='../';
			var codigoSocio =$('input#codigoSocio').val();
			var operacion   ='BUSCA_SOCIO_COD';
			var datos       ='codigoSocio='+codigoSocio+'&operacion='+operacion;
			$.ajax({
				type: "POST",
				url: ruta+'php/busqueda-socios.php',
				data: datos,
				dataType:'json',
				beforeSend: function(){
					$('#resultadoNOM').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center text-danger"><i class="icon-spinner3 spinner"></i> BUSCANDO SOCIO</div></div></div>');
				},
				success: function(respuesta){
					$('#resultadoCOD').fadeIn('slow').html(respuesta.mensaje);
				}
			});
		});

		///////////////////////////////////////////////////
		/// BUSCAR SOCIO POR NOMBRE
		///////////////////////////////////////////////////
		$("button#bt_buscar_socio_nom").click(function(){
			var ruta        ='../';
			var nombreSocio =$('input#nombreSocio').val();
			var apPaternoSocio =$('input#apPaternoSocio').val();
			var apMaternoSocio =$('input#apMaternoSocio').val();
			var operacion   ='BUSCA_SOCIO_NOM';
			var datos       ='nombreSocio='+nombreSocio+'&apPaternoSocio='+apPaternoSocio+'&apMaternoSocio='+apMaternoSocio+'&operacion='+operacion;
			$.ajax({
				type: "POST",
				url: ruta+'php/busqueda-socios.php',
				data: datos,
				dataType:'json',
				beforeSend: function(){
					$('#resultadoNOM').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center text-danger"><i class="icon-spinner3 spinner"></i> BUSCANDO SOCIOS</div></div></div>');
				},
				success: function(respuesta){
					$('#resultadoNOM').fadeIn('slow').html(respuesta.mensaje);
				}
			});
		});

		///////////////////////////////////////////////////
		/// BUSCAR SOCIO POR DNI
		///////////////////////////////////////////////////
		$("a#btn_buscar_socio_dni").click(function(){
			var loader_circular  = 'CARGANDO';
			var ventana_modal    = '#modal_procesos';
			var error_modal      = '#box_modal_procesos_error';
			var loader_modal     = '#box_modal_procesos_loader';
			var contenedor_modal = '#box_modal_procesos_content';
			var operacion        = 'buscar_socio_dni';
			var modulo           = '../formularios/modulo-socios.php';
			var datos            = 'ventana_modal='+ventana_modal+'&error_modal='+error_modal+'&loader_modal='+loader_modal+'&contenedor_modal='+contenedor_modal+'&operacion='+operacion;
			muestraModal(ventana_modal);
			$.ajax({
				url  : modulo,
				type : 'POST',
				data : datos,
				beforeSend: function(){
					$(loader_modal).fadeIn().html(loader_circular);
				},
				complete: function(){
					$(loader_modal).fadeOut().html(loader_circular);
				},
				success: function(response){ 
					$(contenedor_modal).fadeIn().html(response);
				}
			});
		});
	});
</script>

<div class="row">
	<div class="col-md-6">
		<div class="panel panel-flat border-top-xlg border-top-warning">
			<div class="panel-body">
				<div class="col-md-4">
					<a href="javascript:void(0)" id="btn_buscar_socio_dni" class="btn btn-dashboard btn-block bg-warning-300 btn-float btn-float-lg"><i class="icon-search4 icon-dashboard"></i> <span><small>BUSQUEDA SOCIOS</small> POR DNI</span></a>
				</div>
				<div class="visible-xs">&nbsp;</div>
				<div class="col-md-4">
					<a href="#buscarSocioCOD" data-toggle="modal" class="btn btn-dashboard btn-block bg-warning-300 btn-float btn-float-lg"><i class="icon-search4 icon-dashboard"></i> <span><small>BUSQUEDA SOCIOS</small> POR CODIGO</span></a>
				</div>
				<div class="visible-xs">&nbsp;</div>
				<div class="col-md-4">
					<a href="#buscarSocioNOM" data-toggle="modal" class="btn btn-dashboard btn-block bg-warning-300 btn-float btn-float-lg"><i class="icon-search4 icon-dashboard"></i> <span><small>BUSQUEDA SOCIOS</small> POR NOMBRE</span></a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-flat border-top-xlg border-top-brown">
			<div class="panel-body text-center">
				<div class="col-md-4">
					<a href="generar-actividad.php?tipoActividad=ASA" class="btn btn-dashboard btn-block bg-brown-300 btn-float btn-float-lg"><i class="icon-megaphone icon-dashboard"></i> <span><small>AGREGAR</small> ASAMBLEA</span></a>
				</div>
				<div class="visible-xs">&nbsp;</div>
				<div class="col-md-4">
					<a href="generar-actividad.php?tipoActividad=FAE" class="btn btn-dashboard btn-block bg-brown-300 btn-float btn-float-lg"><i class="icon-paint-format icon-dashboard"></i> <span><small>AGREGAR</small> FAENA</span></a>
				</div>
				<div class="visible-xs">&nbsp;</div>
				<div class="col-md-4">
					<a href="generar-cuota.php" class="btn btn-dashboard btn-block bg-brown-300 btn-float btn-float-lg"><i class="icon-cash4 icon-dashboard"></i> <span><small>AGREGAR</small> CUOTA</span></a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-3">
		<div class="panel">
			<div class="panel-heading bg-slate-300">
				<h6 class="panel-title text-bold">TOTAL CONCEPTOS <span class="pull-right"><?= $infoTotalGeneralConceptos ?></span></h6>
			</div>
			<ul class="list-group">
				<li class="list-group-item"><span class="textoNegrita">TOTAL POR ASAMBLEAS</span> <span class="pull-right text-brown"><?= $infoTotalGeneralASA ?></span></li>
				<li class="list-group-item"><span class="textoNegrita">TOTAL POR FAENAS</span> <span class="pull-right text-brown"><?= $infoTotalGeneralFAE ?></span></li>
				<li class="list-group-item"><span class="textoNegrita">TOTALS POR CUOTAS</span> <span class="pull-right text-brown"><?= $infoTotalGeneralCUO ?></span></li>
			</ul>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel">
			<div class="panel-heading bg-slate-300">
				<h6 class="panel-title text-bold">TOTAL PAGADOS</h6>
			</div>
			<ul class="list-group">
				<li class="list-group-item"><span class="textoNegrita">COBROS POR ASAMBLEA</span> <span class="pull-right text-brown"><?= $infoTotalASA ?></span></li>
				<li class="list-group-item"><span class="textoNegrita">COBROS POR FAENAS</span> <span class="pull-right text-brown"><?= $infoTotalFAE ?></span></li>
				<li class="list-group-item"><span class="textoNegrita">COBROS POR CUOTAS</span> <span class="pull-right text-brown"><?= $infoTotalCUO ?></span></li>
			</ul>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel">
			<div class="panel-heading bg-slate-300">
				<h6 class="panel-title text-bold">TOTAL POR COBRAR</h6>
			</div>
			<ul class="list-group">
				<li class="list-group-item"><span class="textoNegrita">DEUDAS POR ASAMBLEA</span> <span class="pull-right text-brown"><?= $deudasASA ?></span></li>
				<li class="list-group-item"><span class="textoNegrita">DEUDA POR FAENAS</span> <span class="pull-right text-brown"><?= $deudasFAE ?></span></li>
				<li class="list-group-item"><span class="textoNegrita">DEUDAS POR CUOTAS</span> <span class="pull-right text-brown"><?= $deudasCUO ?></span></li>
			</ul>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel">
			<div class="panel-heading bg-slate-300">
				<h6 class="panel-title text-bold">INFORMACION DE CAJA</h6>
			</div>
			<ul class="list-group">
				<li class="list-group-item"><span class="textoNegrita">TOTAL INGRESOS</span> <span class="pull-right text-brown"><?= $infoTotalING ?></span></li>
				<li class="list-group-item"><span class="textoNegrita">TOTAL EGRESOS</span> <span class="pull-right text-brown"><?= $infoToPARSAL ?></span></li>
				<li class="list-group-item"><span class="textoNegrita">TOTAL DISPONIBLE</span> <span class="pull-right text-brown"><?= $infoTotalCJA ?></span></li>
			</ul>
		</div>
	</div>
</div>

<?php if($nroCUO>0){ ?>
	<div class="panel panel-flat border-top-xlg border-top-slate">
		<div class="table-responsive">
			<table class="table tabla table-bordered table-hover">
				<thead>
					<tr class="success">
						<th class="text-center">#</th>
						<th class="text-left">CONCEPTO DE CUOTA</th>
						<th class="text-center">FECHA LIMITE</th>
						<th class="text-center">MONTO</th>
						<th class="text-center">PAGARON</th>
						<th class="text-center">DEBEN</th>
						<th class="text-center"><i class="fa fa-align-justify"></i></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql="SELECT codigoCuota, conceptoCuota, montoCuota, fechaPago FROM sm_mod_cuotas ORDER BY id DESC";
						$rs=mysqli_query($conexion,$sql);
						$i=1;
						while($n=mysqli_fetch_array($rs)){
							$codigoCuota   =$n['codigoCuota'];
							$conceptoCuota =utf8_encode($n['conceptoCuota']);
							$montoCuota    =$n['montoCuota'];
							$fechaPago     =$n['fechaPago'];
							$diasEntre     =diasEntre($hoy,$fechaPago);
							$pagaron       =infoCuota($idJuntaDirectiva,$codigoCuota,'pagaron');
							$deben         =infoCuota($idJuntaDirectiva,$codigoCuota,'deben');

							if($diasEntre>0){
								$infoDiasEntre='FALTAN '.$diasEntre.' DIAS';
							}else{
								$infoDiasEntre='VENCIDO';
							}
					?>
					<tr>
						<td class="text-center"><?= ceros($i,2) ?></td>
						<td class="text-left"><?= $conceptoCuota ?></td>
						<td class="text-center text-danger textoNegrita"><span class="label bg-brown"><?= $infoDiasEntre ?></span></td>
						<td class="text-right text-danger textoNegrita">S/. <?= moneda($montoCuota) ?></td>
						<td class="text-center text-danger textoNegrita"><span class="label bg-success"><?= ceros($pagaron,4) ?> SOCIOS</span></td>
						<td class="text-center text-danger textoNegrita"><span class="label bg-warning"><?= ceros($deben,4) ?> SOCIOS</span></td>
						<td class="text-center"><a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=detalles" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Ver cuota"><i class="icon-plus-circle2"></i></a></td>
					</tr>
					<?php $i++; } ?>
				</tbody>
			</table>
		</div>
	</div>
<?php } ?>

<!-- MODAL - BUSQUEDA DE SOCIO POR CODIGO -->
<div id="buscarSocioCOD" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-brown">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">BUSQUEDA DE SOCIOS</h5>
			</div>
			<form action="detalles-socio.php" method="GET">
				<div class="modal-body">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-6">
								<input type="text" name="codigoSocio" class="form-control input-lg text-danger textoNegrita textoMayuscula" onblur="mayusculas(event, this)" placeholder="Ingrese codigo de socio" autofocus>
								<input type="hidden" name="opcion" value="detalles">
							</div>
							<div class="col-sm-3">
								<button type="submit" class="btn btn-block btn-lg bg-grey">BUSCAR SOCIO</button>
							</div>
							<div class="col-sm-3">
								<button type="button" class="btn btn-block btn-lg btn-warning" data-dismiss="modal">CERRAR VENTANA</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- MODAL - BUSQUEDA DE SOCIO POR NOMBRE -->
<div id="buscarSocioNOM" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-brown">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">BUSQUEDA DE SOCIOS</h5>
			</div>
			<form>
				<div class="modal-body">
					<div class="form-group">
						<div class="row">
							<div class="col-sm-3">
								<input type="text" id="nombreSocio" class="form-control input-lg text-danger textoNegrita textoMayuscula" onblur="mayusculas(event, this)" placeholder="Nombre" autofocus tabindex="1">
							</div>
							<div class="col-sm-3">
								<input type="text" id="apPaternoSocio" class="form-control input-lg text-danger textoNegrita textoMayuscula" onblur="mayusculas(event, this)" placeholder="Apellido Paterno" tabindex="2">
							</div>
							<div class="col-sm-3">
								<input type="text" id="apMaternoSocio" class="form-control input-lg text-danger textoNegrita textoMayuscula" onblur="mayusculas(event, this)" placeholder="Apellido Materno" tabindex="3">
							</div>
							<div class="col-sm-3">
								<button type="button" id="bt_buscar_socio_nom" class="btn btn-block btn-lg bg-grey">BUSCAR SOCIO</button>
							</div>
						</div>
					</div>
					<div id="resultadoNOM"></div>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- MODAL - BUSQUEDA DE SOCIO POR DNI -->
<div id="modal_procesos" class="modal fade">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-brown">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">BUSQUEDA DE SOCIOS POR DNI</h5>
			</div>
			<div class="modal-body">
				<div id="box_modal_procesos_error"></div>
				<div id="box_modal_procesos_loader"></div>
				<div id="box_modal_procesos_content"></div>
			</div>
		</div>
	</div>
</div>

<?php
	include($ruta.'template/footer.tpl');
?>