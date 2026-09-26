<?php
	$ruta='../';
	include_once $ruta."php/funciones.php";
	$simbolo         ='S/. ';
	$codigoSocio     =$_GET['codigoSocio'];
	$operacion       =$_GET['operacion'];
	$actividad       =$_GET['actividad'];
	$codigoActividad =$_GET['codigoActividad'];
	$conexion        =conexionDB();
	$asambleas       =infoSocios($codigoSocio,'nroAsambleas');
	$faenas          =infoSocios($codigoSocio,'nroFaenas');
	$cuotas          =infoSocios($codigoSocio,'nroCuotas');
	$total_ASA_saldo =(infoSocios($codigoSocio,'porPagarAsambleas_NO_MP'))+(infoSocios($codigoSocio,'porPagarAsambleas_SI_MP'));
	$total_FAE_saldo =(infoSocios($codigoSocio,'porPagarFaenas_NO_MP'))+(infoSocios($codigoSocio,'porPagarFaenas_SI_MP'));
	$total_CUO_saldo =(infoSocios($codigoSocio,'porPagarCuotas_NO_MP'))+(infoSocios($codigoSocio,'porPagarCuotas_SI_MP'));
	$infoNombreFull  =infoSocios($codigoSocio,'nombre');
	$lotesSocio      =infoSocios($codigoSocio,'lotes');

	if($total_ASA_saldo>0){
		$info_total_ASA_saldo=$simbolo.moneda($total_ASA_saldo);
		$estado_bt_ASA='';
	}else{
		$info_total_ASA_saldo='--';
		$estado_bt_ASA='disabled';
	}

	if($total_FAE_saldo>0){
		$info_total_FAE_saldo=$simbolo.moneda($total_FAE_saldo);
		$estado_bt_FAE='';
	}else{
		$info_total_FAE_saldo='--';
		$estado_bt_FAE='disabled';
	}

	if($total_CUO_saldo>0){
		$info_total_CUO_saldo=$simbolo.moneda($total_CUO_saldo);
		$estado_bt_CUO='';
	}else{
		$info_total_CUO_saldo='--';
		$estado_bt_CUO='disabled';
	}

	if($actividad=='ASA'){ $infoActividad='ASAMBLEA'; }
	if($actividad=='FAE'){ $infoActividad='FAENA'; }
	if($actividad=='CUO'){ $infoActividad='CUOTA'; }
?>
<div class="modal-header bg-danger">
	<h6 class="modal-title">
		<?php if($operacion=='EXONERAR_DEUDAS_PASO_1' or $operacion=='EXONERAR_DEUDAS_PASO_2' or $operacion=='EXONERAR_DEUDAS_PASO_3'){ ?>
			<button type="button" class="btn btn-xs btn-default pull-right" data-dismiss="modal" style="margin-top: -5px;">CERRAR VENTANA</button>
		<?php } ?>
		<?php if($operacion=='EXONERAR_DEUDAS_PASO_4'){ ?>
			<button type="button" class="btn btn-xs btn-default pull-right" id="bt_exoneracion_cerrar" style="margin-top: -5px;">FINALIZAR PROCESO Y CERRAR VENTANA</button>
			<a href="../documentos/exoneraciones.php?codigoSocio=<?= $codigoSocio ?>&actividad=<?= $actividad ?>&codigoActividad=<?= $codigoActividad ?>&operacion=CONSTANCIA_EXONERACION_DEUDA" class="btn btn-success btn-xs pull-right" style="margin-top: -5px; margin-right: 10px;"><i class="icon icon-printer2"></i> CONSTANCIA</a>
		<?php } ?>

		<?php if($operacion!='INFORMACION_EXONERACION'){ ?>
			<strong>EXONERAR DEUDAS:</strong> <?= $infoNombreFull ?> | <strong>LOTES:</strong> <?= $lotesSocio ?>
		<?php }else{ ?>
			<button type="button" class="btn btn-xs btn-default pull-right" id="bt_exoneracion_cerrar" style="margin-top: -5px;">CERRAR VENTANA</button>
			<a href="../documentos/exoneraciones.php?codigoSocio=<?= $codigoSocio ?>&actividad=<?= $actividad ?>&codigoActividad=<?= $codigoActividad ?>&operacion=CONSTANCIA_EXONERACION_DEUDA" class="btn btn-success btn-xs pull-right" style="margin-top: -5px; margin-right: 10px;"><i class="icon icon-printer2"></i> CONSTANCIA</a>
			<strong>INFORMACION DE EXONERARCION DE DEUDA</strong>
		<?php } ?>
	</h6>
</div>
<div class="modal-body">
	<?php if($operacion=='EXONERAR_DEUDAS_PASO_1'){ ?>
		<div class="form-group">
			<div class="row">
				<div class="col-md-4 col-xs-4">
					<form id="form_actividad_ASA">
						<input type="hidden" name="codigoSocio" value="<?= $codigoSocio ?>" ></input>
						<input type="hidden" name="operacion" value="EXONERAR_DEUDAS_PASO_2" ></input>
						<input type="hidden" name="actividad" value="ASA" ></input>
						<button type="button" class="btn btn-lg bg-slate-600 btn-block <?= $estado_bt_ASA ?>" id="bt_exonera_ASA"><strong>EXONERAR DEUDA<br>ASAMBLEA</strong><br><?= $info_total_ASA_saldo ?></button>
					</form>
				</div>

				<div class="col-md-4 col-xs-4">
					<form id="form_actividad_FAE">
						<input type="hidden" name="codigoSocio" value="<?= $codigoSocio ?>" ></input>
						<input type="hidden" name="operacion" value="EXONERAR_DEUDAS_PASO_2" ></input>
						<input type="hidden" name="actividad" value="FAE" ></input>
						<button type="button" class="btn btn-lg bg-slate-600 btn-block <?= $estado_bt_FAE ?>" id="bt_exonera_FAE"><strong>EXONERAR DEUDA<br>FAENA</strong><br><?= $info_total_FAE_saldo ?></button>
					</form>
				</div>

				<div class="col-md-4 col-xs-4">
					<form id="form_actividad_CUO">
						<input type="hidden" name="codigoSocio" value="<?= $codigoSocio ?>" ></input>
						<input type="hidden" name="operacion" value="EXONERAR_DEUDAS_PASO_2" ></input>
						<input type="hidden" name="actividad" value="CUO" ></input>
						<button type="button" class="btn btn-lg bg-slate-600 btn-block <?= $estado_bt_CUO ?>" id="bt_exonera_CUO"><strong>EXONERAR DEUDA<br>CUOTA</strong><br><?= $info_total_CUO_saldo ?></button>
					</form>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// BOTON DE PROCESO
				///////////////////////////////////////////////////
				$("button#bt_exonera_ASA").click(function(){
					var ruta        ='../';
					var datos=$('#form_actividad_ASA').serialize();
					var urlProceso  = ruta+'/modulo/modal-exonerar-deudas-socio.php?'+datos;
					$('#box_modal_exonerar_deudas').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
					$("#box_modal_exonerar_deudas").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
				});

				///////////////////////////////////////////////////
				/// BOTON FAENA
				///////////////////////////////////////////////////
				$("button#bt_exonera_FAE").click(function(){
					var ruta        ='../';
					var datos=$('#form_actividad_FAE').serialize();
					var urlProceso  = ruta+'/modulo/modal-exonerar-deudas-socio.php?'+datos;
					$('#box_modal_exonerar_deudas').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
					$("#box_modal_exonerar_deudas").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
				});

				///////////////////////////////////////////////////
				/// BOTON CUOTA
				///////////////////////////////////////////////////
				$("button#bt_exonera_CUO").click(function(){
					var ruta        ='../';
					var datos=$('#form_actividad_CUO').serialize();
					var urlProceso  = ruta+'/modulo/modal-exonerar-deudas-socio.php?'+datos;
					$('#box_modal_exonerar_deudas').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
					$("#box_modal_exonerar_deudas").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
				});
			});
		</script>
	<?php } ?>

	<?php if($operacion=='EXONERAR_DEUDAS_PASO_2'){ ?>
		<?php if($actividad=='ASA'){ ?>
			<div class="table-responsive">
				<table class="table tabla table-bordered table-hover tabla-lista-ASA">
					<thead>
						<tr class="success">
							<th class="textoNegrita text-center">#</th>
							<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
							<th class="textoNegrita text-center">FECHA</th>
							<th class="textoNegrita text-center">RAZON</th>
							<th class="textoNegrita text-center">TOTAL</th>
							<th class="textoNegrita text-center"><i class="fa fa-calendar"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND estadoPago='NP' AND codigoSocio='$codigoSocio' AND multa>0 ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$tipoActividad   =$n['tipoActividad'];
								$codigoActividad =$n['codigoActividad'];
								$temaActividad   =texto(infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad'));
								$fechaActividad  =infoFecha(infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'fechaActividad'),'muycorta');
								$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
								$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
								$lotes           =$n['lotes'];
								$asistio         =$n['asistio'];
								$ingreso         =$n['ingreso'];
								$salida          =$n['salida'];
								$retraso         =$n['retraso'];
								$multa           =$n['multa'];
								$estadoPago      =$n['estadoPago'];
								$conceptoPago    =$tipoActividad;
								$programado      =conceptoProgramado($codigoSocio,$codigoActividad);
								$porcentajePago  =porcentajePago($codigoSocio,$codigoActividad,$multa);
								$infoTotalMulta  ='S/. '.moneda($multa);
								
								if($programado>=2){
									$infoProgramado='<span class="label bg-brown">'.ceros($programado,2). ' FECHAS</span>';
								}else{
									$infoProgramado='';
								}

								if($asistio=="IN"){
									$asistencia="";
									$razon="";
									$infoMulta="";
									$infoTotalMulta="";
									$estado='<span class="label bg-slate-800">INVITADO</span>';
								}

								if($asistio=="NO"){
									$asistencia="FALTO";
									$razon="FALTA";
									$infoMulta='S/. '.moneda($multaFalta);
								}

								if($asistio=="SI" and $retraso<=0.15){
									$asistencia="ASISTIO";
									$razon="PUNTUAL";
									$infoMulta='';
									$estado='<span class="label bg-teal-800">ASISTIO</span>';
									$infoTotalMulta='';
								}

								if($asistio=="SI" and $retraso>0.15){
									$asistencia="TARDANZA";
									$razon="TARDE";
									$infoMulta='S/. '.moneda($multaTarde);
								}
								
								if($asistio=="JU"){
									$asistencia="JUSTIFICADO";
									$razon="FALTA";
									$infoMulta='S/. '.moneda($multaFalta);
									$infoTotalMulta='-'.$infoTotalMulta;
									$estadoPago="JUS";
								}

								if($estadoPago=="NP" and $programado==0){
									$estado       ='<span class="label label-danger">PENDIENTE</span>';
								}

								if($estadoPago=="SP" and $programado==0){
									$estado       ='<span class="label label-success">PAGADO</span>';
								}

								if($estadoPago=="SP" and $programado>=2){
									$estado       ='<span class="label label-success">PAGADO</span>';
								}

								if($estadoPago=="MP" and $programado>=2){
									$estado       ='<span class="label bg-violet-800">AL '.$porcentajePago.'%</span>';
								}

								if($estadoPago=="JUS"){
									$estado       ='<span class="label label-info">JUSTIFICADO</span>';
								}
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-left"><?= $temaActividad ?></td>
							<td class="text-center textoMayuscula"><?= $fechaActividad ?></td>
							<td class="text-center"><?= $asistencia ?></td>
							<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
							<td class="text-center">
								<button class="btn btn-xs btn-danger bt_exonerar" id="<?= $codigoActividad ?>">EXONERAR</button>
							</td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>

			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [  ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-lista-ASA').DataTable({ 'pageLength': 20 });
					$('.tabla-lista-ASA tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

					///////////////////////////////////////////////////
					/// BOTON DE PROCESO
					///////////////////////////////////////////////////
					$("button.bt_exonerar").click(function(){
						var ruta        ='../';
						var codigoSocio='<?= $codigoSocio ?>';
						var actividad ='<?= $actividad ?>';
						var codigoActividad=$(this).attr('id');
						var operacion='EXONERAR_DEUDAS_PASO_3';
						var datos='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
						var urlProceso  = ruta+'/modulo/modal-exonerar-deudas-socio.php?'+datos;
						$('#box_modal_exonerar_deudas').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
						$("#box_modal_exonerar_deudas").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
					});
				});
			</script>
		<?php } ?>

		<?php if($actividad=='FAE'){ ?>
			<div class="table-responsive">
				<table class="table tabla table-bordered table-hover tabla-lista-CUO">
					<thead>
						<tr class="success">
							<th class="textoNegrita text-center">#</th>
							<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
							<th class="textoNegrita text-center">FECHA</th>
							<th class="textoNegrita text-center">RAZON</th>
							<th class="textoNegrita text-center">TOTAL</th>
							<th class="textoNegrita text-center"><i class="fa fa-calendar"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND estadoPago='NP' AND codigoSocio='$codigoSocio' AND multa>0 ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$tipoActividad   =$n['tipoActividad'];
								$codigoActividad =$n['codigoActividad'];
								$temaActividad   =texto(infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad'));
								$fechaActividad  =infoFecha(infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'fechaActividad'),'muycorta');
								$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
								$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
								$lotes           =$n['lotes'];
								$asistio         =$n['asistio'];
								$ingreso         =$n['ingreso'];
								$salida          =$n['salida'];
								$retraso         =$n['retraso'];
								$multa           =$n['multa'];
								$estadoPago      =$n['estadoPago'];
								$conceptoPago    =$tipoActividad;
								$programado      =conceptoProgramado($codigoSocio,$codigoActividad);
								$porcentajePago  =porcentajePago($codigoSocio,$codigoActividad,$multa);
								$infoTotalMulta  ='S/. '.moneda($multa);
								
								if($programado>=2){
									$infoProgramado='<span class="label bg-brown">'.ceros($programado,2). ' FECHAS</span>';
								}else{
									$infoProgramado='';
								}

								if($asistio=="IN"){
									$asistencia="";
									$razon="";
									$infoMulta="";
									$infoTotalMulta="";
									$estado='<span class="label bg-slate-800">INVITADO</span>';
								}

								if($asistio=="NO"){
									$asistencia="FALTO";
									$razon="FALTA";
									$infoMulta='S/. '.moneda($multaFalta);
								}

								if($asistio=="SI" and $retraso<=0.15){
									$asistencia="ASISTIO";
									$razon="PUNTUAL";
									$infoMulta='';
									$estado='<span class="label bg-teal-800">ASISTIO</span>';
									$infoTotalMulta='';
								}

								if($asistio=="SI" and $retraso>0.15){
									$asistencia="TARDANZA";
									$razon="TARDE";
									$infoMulta='S/. '.moneda($multaTarde);
								}
								
								if($asistio=="JU"){
									$asistencia="JUSTIFICADO";
									$razon="FALTA";
									$infoMulta='S/. '.moneda($multaFalta);
									$infoTotalMulta='-'.$infoTotalMulta;
									$estadoPago="JUS";
								}

								if($estadoPago=="NP" and $programado==0){
									$estado       ='<span class="label label-danger">PENDIENTE</span>';
								}

								if($estadoPago=="SP" and $programado==0){
									$estado       ='<span class="label label-success">PAGADO</span>';
								}

								if($estadoPago=="SP" and $programado>=2){
									$estado       ='<span class="label label-success">PAGADO</span>';
								}

								if($estadoPago=="MP" and $programado>=2){
									$estado       ='<span class="label bg-violet-800">AL '.$porcentajePago.'%</span>';
								}

								if($estadoPago=="JUS"){
									$estado       ='<span class="label label-info">JUSTIFICADO</span>';
								}
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-left"><?= $temaActividad ?></td>
							<td class="text-center textoMayuscula"><?= $fechaActividad ?></td>
							<td class="text-center"><?= $asistencia ?></td>
							<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
							<td class="text-center">
								<button class="btn btn-xs btn-danger bt_exonerar" id="<?= $codigoActividad ?>">EXONERAR</button>
							</td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>

			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [  ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-lista-CUO').DataTable({ 'pageLength': 20 });
					$('.tabla-lista-CUO tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

					///////////////////////////////////////////////////
					/// BOTON DE PROCESO
					///////////////////////////////////////////////////
					$("button.bt_exonerar").click(function(){
						var ruta        ='../';
						var codigoSocio='<?= $codigoSocio ?>';
						var actividad ='<?= $actividad ?>';
						var codigoActividad=$(this).attr('id');
						var operacion='EXONERAR_DEUDAS_PASO_3';
						var datos='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
						var urlProceso  = ruta+'/modulo/modal-exonerar-deudas-socio.php?'+datos;
						$('#box_modal_exonerar_deudas').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
						$("#box_modal_exonerar_deudas").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
					});
				});
			</script>
		<?php } ?>

		<?php if($actividad=='CUO'){ ?>
			<div class="table-responsive">
				<table class="table tabla table-bordered table-hover tabla-lista-CUO">
					<thead>
						<tr class="success">
							<th class="text-center">#</th>
							<th class="text-left">CONCEPTO DE CUOTA</th>
							<th class="text-center">TOTAL</th>
							<th class="text-center"><i class="fa fa-align-justify"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago FROM sm_mod_cuotas_socios WHERE montoPago>0 AND estadoPago='NP' AND codigoSocio='$codigoSocio' ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$tipoActividad   ='CUO';
								$codigoCuota     =$n['codigoCuota'];
								$codigoActividad =$codigoCuota;
								$lotes           =$n['lotes'];
								$montoCuota      =$n['montoCuota'];
								$montoPago       =$n['montoPago'];
								$estadoPago      =$n['estadoPago'];
								$dni             =infoSocios($codigoSocio,'dni');
								$conceptoCuota   =texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota'));
								$verificaPago    =verificaPago($codigoCuota,$codigoSocio,$multa);
								$conceptoPago    =$tipoActividad;
								$programado      =conceptoProgramado($codigoSocio,$codigoCuota);
								$porcentajePago  =porcentajePago($codigoSocio,$codigoCuota,$montoPago);
								
								if($estadoPago=="NP" and $programado==0){
									$estado       ='<span class="label label-danger">PENDIENTE</span>';
									$verificaPago ="--";
								}

								if($estadoPago=="MP" and $programado>=2){
									$estado       ='<span class="label label-danger">PENDIENTE | '.ceros($programado,2).' F</span>';
									$verificaPago ="PEN";
									$pagoProgramado ="SI";
								}

								if($verificaPago=="--"){ $caja=''; }
								if($verificaPago=="PEN"){ $caja='<span class="label bg-grey">'.$porcentajePago.'%</span>'; }
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-left"><?= $conceptoCuota ?></td>
							<td class="text-right text-danger textoNegrita"><?= 'S/. '.moneda($montoPago) ?></td>
							<td class="text-center">
								<button class="btn btn-xs btn-danger bt_exonerar" id="<?= $codigoActividad ?>">EXONERAR</button>
							</td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>

			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [  ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-lista-CUO').DataTable({ 'pageLength': 20 });
					$('.tabla-lista-CUO tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });

					///////////////////////////////////////////////////
					/// BOTON DE PROCESO
					///////////////////////////////////////////////////
					$("button.bt_exonerar").click(function(){
						var ruta        ='../';
						var codigoSocio='<?= $codigoSocio ?>';
						var actividad ='<?= $actividad ?>';
						var codigoActividad=$(this).attr('id');
						var operacion='EXONERAR_DEUDAS_PASO_3';
						var datos='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
						var urlProceso  = ruta+'/modulo/modal-exonerar-deudas-socio.php?'+datos;
						$('#box_modal_exonerar_deudas').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
						$("#box_modal_exonerar_deudas").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
					});
				});
			</script>
		<?php } ?>
	<?php } ?>

	<?php if($operacion=='EXONERAR_DEUDAS_PASO_3'){ ?>
		<?php
			if($actividad=='ASA' or $actividad=='FAE'){
				$nombreActividad=infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
				$fechaActividad=infoActividad($idJuntaDirectiva,$codigoActividad,'','fechaActividad');
				$deudaActividad=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalMultaACTSocio');
			}
		?>

		<?php
			if($actividad=='CUO'){
				$nombreActividad=infoCuota($idJuntaDirectiva,$codigoActividad,'conceptoCuota');
				$fechaActividad=infoCuota($idJuntaDirectiva,$codigoActividad,'fechaPago');
				$deudaActividad=infoDeudasCuota($codigoActividad, $codigoSocio, 'montoPago');
			} 
		?>

		<div class="m-b-20">
			<div class="row">
				<div class="col-sm-12">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita"><?= $infoActividad ?></span> <span class="pull-right text-slate textoMayuscula"><?= $nombreActividad ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">FECHA</span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaActividad,'muycorta') ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL DEUDA A EXONERAR</span> <span class="pull-right text-slate text-uppercase"><?= $simbolo.moneda($deudaActividad) ?></span></li>
					</ul>
				</div>
			</div>
		</div>

		<div id="errorValidacion">
			 <span>ERROR EN FORMULARIO DE EXONERACION</span>
			 <p>Por favor revise los errores abajo mencionados y corrijalos...</p>
			<ul />
		</div>

		<div class="panel">
			<div class="panel-heading bg-brown">
				<h6 class="panel-title textoNegrita">DATOS DE SOCIO</h6>
			</div>
			<div class="panel-body">
				<form id="form_exonerar_procesar">
					<div class="form-group">
						<div class="row">
							<div class="col-md-3">
								<label class="display-block">descuento <small class="text-danger pull-right">(%)</small></label>
								<div class="input-group">
									<input type="text" name="porcentaje" id="porcentaje" class="form-control" placeholder="100%">
									<span class="input-group-addon">%</span>
								</div>
							</div>
							<div class="col-md-3">
								<label class="display-block">Monto a exonerar <small class="text-danger pull-right">(S/. 0.00)</small></label>
								<div class="input-group">
									<span class="input-group-addon">S/.</span>
									<input type="text" name="montoAExonerar" id="montoAExonerar" class="form-control" placeholder="S/. 0.00">
								</div>
							</div>
							<div class="col-md-3">
								<label class="display-block">Total a pagar <small class="text-danger pull-right">(S/. 0.00)</small></label>
								<input type="text" id="infoTotalPagoExonerado" class="form-control" placeholder="S/ 0.00">
								<input type="hidden" id="datoTotalPagoExonerado" name="totalPago">
							</div>
							<div class="col-md-3">
								<label class="display-block">Nro. de Voucher</label>
								<input type="text" name="nroDocumento" id="nroDocumento" class="form-control" placeholder="Nro. Voucher">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="row">
							<div class="col-md-12">
								<textarea name="observacion" id="observacion" class="form-control textoMayuscula" rows="3" onblur="mayusculas(event, this)"></textarea>
							</div>
						</div>
					</div>
					<div class="form-group no-margin no-padding">
						<div class="row">
							<div class="col-md-12">
								<input type="hidden" name="operacion" value="EXONERAR_DEUDAS_SOCIO">
								<input type="hidden" name="codigoSocio" value="<?= $codigoSocio ?>">
								<input type="hidden" name="actividad" value="<?= $actividad ?>">
								<input type="hidden" name="codigoActividad" value="<?= $codigoActividad ?>">
								<input type="hidden" name="deuda" value="<?= $deudaActividad ?>">
								<button type="button" class="btn btn-md btn-danger btn-block" id="bt_procesar_exoneracion">PROCESAR EXONERACION DE DEUDA</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// DESHABILITA FORMULARIO
				///////////////////////////////////////////////////
				$('input#porcentaje').focus();
				$('input#infoTotalPagoExonerado').attr("disabled", true);

				///////////////////////////////////////////////////
				/// CALCULA PAGO DE EXONERACION
				///////////////////////////////////////////////////
				$('input#montoAExonerar').keyup(function() {
					var montoAExonerar=$('input#montoAExonerar').val();
					var deuda='<?= $deudaActividad ?>';
					var montoExonerado=montoAExonerar;
					var totalPagoExonerado=(deuda-montoExonerado);
					var porcentaje = Number(((montoAExonerar*100)/deuda).toFixed(2));;

					if(montoAExonerar>0 && porcentaje<=100){
						$('input#porcentaje').val(porcentaje);
						$('input#infoTotalPagoExonerado').val('S/. '+totalPagoExonerado);
						$('input#datoTotalPagoExonerado').val(totalPagoExonerado);
					}else{
						$('input#porcentaje').val('');
						$('input#infoTotalPagoExonerado').val('S/. 0.00');
						$('input#datoTotalPagoExonerado').val('');
					}

					if(montoAExonerar==deuda){
						$('input#nroDocumento').attr("disabled", true);
					}
				});

				///////////////////////////////////////////////////
				/// CALCULA PAGO DE EXONERACION
				///////////////////////////////////////////////////
				$('input#porcentaje').keyup(function() {
					var porcentaje=$('input#porcentaje').val();
					var deuda='<?= $deudaActividad ?>';
					var montoExonerado=((porcentaje*deuda)/100);
					var totalPagoExonerado=deuda-montoExonerado;

					if(porcentaje>0 && porcentaje<=100){
						$('input#montoAExonerar').val(montoExonerado);
						$('input#infoTotalPagoExonerado').val('S/. '+totalPagoExonerado);
						$('input#datoTotalPagoExonerado').val(totalPagoExonerado);
					}else{
						$('input#montoAExonerar').val('');
						$('input#infoTotalPagoExonerado').val('S/. 0.00');
						$('input#datoTotalPagoExonerado').val('');
					}

					if(porcentaje==100){
						$('input#nroDocumento').attr("disabled", true);
					}
				});

				///////////////////////////////////////////////////
				/// VERIFICA SI VOUCHER SE REGISTRO
				///////////////////////////////////////////////////
				$("input#nroDocumento").focusout(function(){
					var ruta         ='../';
					var nroDocumento =$('input#nroDocumento').val();
					var operacion    ='VERIFICAR_VOUCHER_EXONERACION';
					var urlProceso   =ruta+'php/mantenimiento-socios.php';
					var datos        ='nroDocumento='+nroDocumento+'&operacion='+operacion;
					if(nroDocumento.length > 0){
						$.ajax({
							type: 'POST',
							url: urlProceso,
							data: datos,
							dataType:'json',
							success:function(respuesta){
								if(respuesta.mensaje=='EXISTE'){
									new PNotify({title: 'ADVERTENCIA', text: 'El nro de voucher ingresado ya esta registrado en el sistema, por favor reingrese un nro. de voucher valido.', addclass: 'bg-warning'});
									$('input#nroDocumento').val('');
									$('input#nroDocumento').focus();
								}
							}
						});
					}
				});

				///////////////////////////////////////////////////
				/// BOTON DE PROCESO
				///////////////////////////////////////////////////
				$("button#bt_procesar_exoneracion").click(function(){
					var datos      =$('form#form_exonerar_procesar').serialize();
					var valida     =$('#form_exonerar_procesar').valid();
					var ruta       ='../';
					var urlProceso =ruta+'php/mantenimiento-socios.php';
					if(valida){
						$.ajax({
							type: 'POST',
							url: urlProceso,
							data: datos,
							dataType:'json',
							success:function(respuesta){
								if(respuesta.mensaje=='EXONERACION_PROCESADA'){
									var ruta        ='../';
									var codigoSocio='<?= $codigoSocio ?>';
									var actividad ='<?= $actividad ?>';
									var codigoActividad='<?= $codigoActividad ?>';
									var operacion='EXONERAR_DEUDAS_PASO_4';
									var datos='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
									var urlProceso  = ruta+'/modulo/modal-exonerar-deudas-socio.php?'+datos;
									$('#box_modal_exonerar_deudas').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
									$("#box_modal_exonerar_deudas").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
								}else{
									return false;
								}
							}
						});
					}
				});
			});

			///////////////////////////////////////////////////
			/// VALIDAR FORMULARIO
			///////////////////////////////////////////////////
			$("#form_exonerar_procesar").validate({
				errorContainer: $("#errorValidacion"), 
				errorLabelContainer: $("#errorValidacion ul"), 
				wrapper: "li",
				highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
				unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
				rules: {
					porcentaje: { required: true, range: [0, 100] },
					montoAExonerar: { required: true,  range: [0, '<?= $deudaActividad ?>'] },
					nroDocumento: { required: true },
					observacion: { required: true },
				},
				messages: {
					porcentaje: { required: "Ingrese porcentaje de exoneración", range: "El rango de porcentaje debe ser entre 0 y 100%", },
					montoAExonerar: { required: "Ingrese monto a exonerar que sea menor a la deuda total", range: "El monto de exoneracion debe ser  desde S/. 0.00 hasta S/. <?= $deudaActividad ?>", },
					nroDocumento: { required: "Ingrese Nro. voucher en caso la exoneración sea parcial.", },
					observacion: { required: "Sustente en detalle la exoneracion en proceso.", },
				}
			});

			///////////////////////////////////////////////////
			/// MAYUSCULAS
			///////////////////////////////////////////////////
			function mayusculas(e, elemento) {
				tecla=(document.all) ? e.keyCode : e.which; 
				elemento.value = elemento.value.toUpperCase();
			}
		</script>

		<div id="informacion"></div>
	<?php } ?>

	<?php if($operacion=='EXONERAR_DEUDAS_PASO_4'){ ?>
		<?php
				$nombreActividad      =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
				$fechaActividad       =infoActividad($idJuntaDirectiva,$codigoActividad,'','fechaActividad');
				$deuda                =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'deudaExoneraciones');
				$porcentaje           =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
				$exonerado            =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
				$totalPago            =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
				$nroDocumento         =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'nroDocumentoExoneraciones');
				$observacion          =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'observacionExoneraciones');
				$codigoExoneracion    =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'codigoExoneracionExoneraciones');
				$fechaExoneraciones   =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'fechaExoneraciones');
				$horaExoneraciones    =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'horaExoneraciones');
				$usuarioExoneraciones =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'usuarioExoneraciones');
				$codOperacionCaja     =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'codOperacionCaja');
				$usuario              =datoUsuario($usuarioExoneraciones,'nombreCorto');

				if($codOperacionCaja!=''){ $infoCodOperacionCaja=$codOperacionCaja; }else{ $infoCodOperacionCaja='Sin registro de operación en CAJA'; }
		?>
		<div class="m-b-20">
			<div class="row">
				<div class="col-sm-12">
					<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig no-margin">
						<strong>EXONERACION PROCESADA Y REGISTRADA</strong> EN LA BASE DE DATOS<br><strong>Codigo Operacion:</strong> <?= $infoCodOperacionCaja ?>
					</div>
					<ul class="list-group m-t-10">
						<li class="list-group-item"><span class="textoNegrita"><?= $infoActividad ?></span> <span class="pull-right text-slate textoMayuscula"><?= $nombreActividad ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">FECHA</span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaActividad,'muycorta') ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL DEUDA A EXONERAR</span> <span class="pull-right text-slate text-uppercase"><?= $simbolo.moneda($deuda) ?></span></li>
					</ul>
				</div>

				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">PORCENTAJE EXONERADO</span> <span class="pull-right text-slate textoMayuscula"><?= $porcentaje.'%' ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">MONTO EXONERADO</span> <span class="pull-right text-slate text-uppercase text-danger"><?= '-'.$simbolo.moneda($exonerado) ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGADO</span> <span class="pull-right text-slate text-uppercase"><?= $simbolo.moneda($totalPago) ?></span></li>
					</ul>
				</div>

				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">NRO. DOCUMENTO</span> <span class="pull-right text-slate textoMayuscula"><?= $nroDocumento ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">FECHA Y HORA OPERACION</span> <span class="pull-right text-slate text-uppercase"><?= infoFecha($fechaExoneraciones,'muycorta').' | '.infoHora($horaExoneraciones) ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">USUARIO</span> <span class="pull-right text-slate text-uppercase"><?= $usuario ?></span></li>
					</ul>
				</div>
				<div class="col-sm-12 m-t-20">
					<div class="form-control"><?= $observacion ?></div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// BOTON DE PROCESO
				///////////////////////////////////////////////////
				$("button#bt_exoneracion_cerrar").click(function(){
					location.reload();
				});
			});
		</script>
	<?php } ?>

	<?php if($operacion=='INFORMACION_EXONERACION'){ ?>
		<?php
			if($actividad=='ASA' or $actividad=='FAE'){
				$nombreActividad =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
				$fechaActividad  =infoActividad($idJuntaDirectiva,$codigoActividad,'','fechaActividad');
				$deuda           =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'deudaExoneraciones');
			}

			if($actividad=='CUO'){
				$nombreActividad =infoCuota($idJuntaDirectiva,$codigoActividad,'conceptoCuota');
				$fechaActividad  =infoCuota($idJuntaDirectiva,$codigoActividad,'fechaPago');
				$deuda           =infoDeudasCuota($codigoActividad, $codigoSocio, 'montoPago');
			}
			
			$porcentaje           =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
			$exonerado            =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
			$totalPago            =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
			$nroDocumento         =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'nroDocumentoExoneraciones');
			$observacion          =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'observacionExoneraciones');
			$codigoExoneracion    =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'codigoExoneracionExoneraciones');
			$fechaExoneraciones   =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'fechaExoneraciones');
			$horaExoneraciones    =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'horaExoneraciones');
			$usuarioExoneraciones =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'usuarioExoneraciones');
			$codOperacionCaja     =infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'codOperacionCaja');
			$usuario              =datoUsuario($usuarioExoneraciones,'nombreCorto');
		?>
		<div class="m-b-20">
			<div class="row">
				<div class="col-sm-12">
					<ul class="list-group m-t-10">
						<li class="list-group-item"><span class="textoNegrita"><?= $infoActividad ?></span> <span class="pull-right text-slate textoMayuscula"><?= $nombreActividad ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">FECHA</span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaActividad,'muycorta') ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL DEUDA A EXONERAR</span> <span class="pull-right text-slate text-uppercase"><?= $simbolo.moneda($deuda) ?></span></li>
					</ul>
				</div>

				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">PORCENTAJE EXONERADO</span> <span class="pull-right text-slate textoMayuscula"><?= $porcentaje.'%' ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">MONTO EXONERADO</span> <span class="pull-right text-slate text-uppercase text-danger"><?= '-'.$simbolo.moneda($exonerado) ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGADO</span> <span class="pull-right text-slate text-uppercase"><?= $simbolo.moneda($totalPago) ?></span></li>
					</ul>
				</div>

				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">NRO. DOCUMENTO</span> <span class="pull-right text-slate textoMayuscula"><?= $nroDocumento ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">FECHA Y HORA OPERACION</span> <span class="pull-right text-slate text-uppercase"><?= infoFecha($fechaExoneraciones,'muycorta').' | '.infoHora($horaExoneraciones) ?></span></li>
					</ul>
				</div>
				<div class="col-sm-6">
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">USUARIO</span> <span class="pull-right text-slate text-uppercase"><?= $usuario ?></span></li>
					</ul>
				</div>
				<div class="col-sm-12 m-t-20">
					<div class="form-control"><?= $observacion ?></div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// BOTON DE PROCESO
				///////////////////////////////////////////////////
				$("button#bt_exoneracion_cerrar").click(function(){
					location.reload();
				});
			});
		</script>
	<?php } ?>
</div>