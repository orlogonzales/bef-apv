<?php if($_POST['proceso']=='MUESTRA_LISTA_DEUDAS_SOCIO'){ ?>
	<?php
		include('../php/funciones.php');
		include('../php/conexion.php');
		$conexion         = conexionDB();
		$idJuntaDirectiva = $_POST['idJuntaDirectiva'];
		$codigoSocio      = $_POST['codigoSocio'];
		$nombreSocio      = $_POST['nombreSocio'];
		$concepto         = $_POST['concepto'];

	    if($concepto=='ASA'){
	        $infoConcepto='ASAMBLEA';
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosASA=$dato['cantidadRegistros'];
            $totalDeudaASA=$dato['totalDeuda'];
            $totalDeuda=$totalDeudaASA;
        }

        if($concepto=='FAE'){
        	$infoConcepto='FAENA';
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosFAE=$dato['cantidadRegistros'];
            $totalDeudaFAE=$dato['totalDeuda'];
            $totalDeuda=$totalDeudaFAE;
        }

	    if($concepto=='CUO'){
	        $infoConcepto='CUOTA';
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='idJuntaDirectiva='.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_cuotas_socios.id) AS cantidadRegistros, SUM(sm_mod_cuotas_socios.montoCuota) AS totalDeuda FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE $consultaJD sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosCUO=$dato['cantidadRegistros'];
            $totalDeudaCUO=$dato['totalDeuda'];
            
            $totalDeuda=$totalDeudaCUO;
        }

	    if($concepto=='ALL'){
	    	$infoConcepto='TODOS';
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosASA=$dato['cantidadRegistros'];
            $totalDeudaASA=$dato['totalDeuda'];

            $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosFAE=$dato['cantidadRegistros'];
            $totalDeudaFAE=$dato['totalDeuda'];

            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='idJuntaDirectiva='.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_cuotas_socios.id) AS cantidadRegistros, SUM(sm_mod_cuotas_socios.montoCuota) AS totalDeuda FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE $consultaJD sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosCUO=$dato['cantidadRegistros'];
            $totalDeudaCUO=$dato['totalDeuda'];
            
            $totalDeuda=$totalDeudaASA+$totalDeudaFAE+$totalDeudaCUO;
        }
	?>
	<div class="row" style="margin-bottom: 20px;">
		<div class="col-md-2">
			<ul class="list-group">
				<li class="list-group-item font-15 text-danger">CONCEPTO: <strong class="pull-right"><?= $infoConcepto ?></strong></li>
			</ul>
		</div>
		<div class="col-md-4">
			<ul class="list-group">
				<li class="list-group-item font-15 text-danger">SOCIO: <strong class="pull-right"><?= $nombreSocio ?></strong></li>
			</ul>
		</div>
		<div class="col-md-2">
			<ul class="list-group">
				<li class="list-group-item font-15 text-danger">CODIGO: <strong class="pull-right"><?= $codigoSocio ?></strong></li>
			</ul>
		</div>
		<div class="col-md-4">
			<ul class="list-group">
				<?php
					$sql="SELECT codigoLote FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
					$rs = $conexion->query($sql);
					$cantidadResultados = $rs->num_rows;
					if($cantidadResultados==1){
						echo '<li class="list-group-item font-15 text-danger">LOTE: <strong class="pull-right">';
					}else{
						echo '<li class="list-group-item font-15 text-danger">LOTES: <strong class="pull-right">';
					}
					$l=1;
					while ($datos = $rs->fetch_assoc()) {
						$codigoLote = $datos['codigoLote'];
						if($cantidadResultados>1){
							if($l<$cantidadResultados){
								$separador = '&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;';
							}else{
								$separador = '';
							}
							echo $codigoLote.$separador;
						}else{
							echo $codigoLote;
						}
						$l++;
					}
					echo '</strong></li>'
				?>
			</ul>
		</div>
	</div>

	<table id="dataTableDeudasSocios" class="table table-bordered table-hover table-striped table-sm mb-15 w-100">
		<thead class="bg-dark">
			<tr>
				<th class="text-center">#</th>
				<th class="text-center">CONCEPTO</th>
				<th class="text-left">DETALLE CONCEPTO</th>
				<th class="text-center">FECHA</th>
				<th class="text-center">LOTES</th>
				<th class="text-right">DEUDA</th>
			</tr>
		</thead>
		<tbody>
			<?php if($concepto=='ALL'){ ?>
				<?php if($cantidadRegistrosASA>0){ ?>
					<?php
						$a=1;
						$deudaASA=0;
						$infoConcepto='ASAMBLEA';
						$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.fechaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta, sm_mod_asistencia.lotes, sm_mod_asistencia.multa FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0 ORDER BY sm_mod_actividades.fechaActividad ASC";
						$rs = $conexion->query($sql);
						while ($datos = $rs->fetch_assoc()) {
							$temaActividad = $datos['temaActividad'];
							$fechaActividad = $datos['fechaActividad'];
							$mTardanza = $datos['mTardanza'];
							$mFalta = $datos['mFalta'];
							$lotes = $datos['lotes'];
							$multa = $datos['multa'];
					?>
						<tr>
							<td class="text-center"><?= ceros($a,2) ?></td>
							<td class="text-center"><?= $infoConcepto ?></td>
							<td class="text-left"><?= $temaActividad ?></td>
							<td class="text-center text-uppercase"><?= infoFecha($fechaActividad,'normal') ?></td>
							<td class="text-center"><?= ceros($lotes,2) ?></td>
							<td class="text-right">S/. <strong><?= moneda($multa,2) ?></strong></td>
						</tr>
					<?php
							$deudaASA=$deudaASA+$multa;
							$a++;
						}
					?>
				<?php }else{ $deudaASA=0; } ?>

				<?php if($cantidadRegistrosFAE>0){ ?>
					<?php
						if($b>0){
							$b=$a;
						}else{
							$b=1;
						}
						$deudaFAE=0;
						$infoConcepto='FAENA';
						$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.fechaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta, sm_mod_asistencia.lotes, sm_mod_asistencia.multa FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0 ORDER BY sm_mod_actividades.fechaActividad ASC";
						$rs = $conexion->query($sql);
						while ($datos = $rs->fetch_assoc()) {
							$temaActividad = $datos['temaActividad'];
							$fechaActividad = $datos['fechaActividad'];
							$mTardanza = $datos['mTardanza'];
							$mFalta = $datos['mFalta'];
							$lotes = $datos['lotes'];
							$multa = $datos['multa'];
					?>
						<tr>
							<td class="text-center"><?= ceros($b,2) ?></td>
							<td class="text-center"><?= $infoConcepto ?></td>
							<td class="text-left"><?= $temaActividad ?></td>
							<td class="text-center text-uppercase"><?= infoFecha($fechaActividad,'normal') ?></td>
							<td class="text-center"><?= ceros($lotes,2) ?></td>
							<td class="text-right">S/. <strong><?= moneda($multa,2) ?></strong></td>
						</tr>
					<?php
							$deudaFAE=$deudaFAE+$multa;
							$b++;
						}
					?>
				<?php }else{ $deudaFAE=0; } ?>

				<?php if($totalDeudaCUO>0){ ?>
					<?php
						if($b>0){
							$c=$b;
						}else{
							$c=$a;
						}
						$deudaCUO=0;
						$infoConcepto='CUOTA';
						$sql="SELECT sm_mod_cuotas.conceptoCuota, sm_mod_cuotas.montoCuota, sm_mod_cuotas.fechaPago, sm_mod_cuotas_socios.lotes, sm_mod_cuotas_socios.montoPago FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0 ORDER BY sm_mod_cuotas.fechaPago ASC";
						$rs = $conexion->query($sql);
						while ($datos = $rs->fetch_assoc()) {
							$conceptoCuota = $datos['conceptoCuota'];
							$fechaPago = $datos['fechaPago'];
							$montoCuota = $datos['montoCuota'];
							$lotes = $datos['lotes'];
							$montoPago = $datos['montoPago'];
					?>
						<tr>
							<td class="text-center"><?= ceros($c,2) ?></td>
							<td class="text-center"><?= $infoConcepto ?></td>
							<td class="text-left"><?= $conceptoCuota ?></td>
							<td class="text-center text-uppercase"><?= infoFecha($fechaPago,'normal') ?></td>
							<td class="text-center"><?= ceros($lotes,2) ?></td>
							<td class="text-right">S/. <strong><?= moneda($montoPago,2) ?></strong></td>
						</tr>
					<?php
							$deudaCUO=$deudaCUO+$montoPago;
							$c++;
						}
					?>
				<?php } ?>
			<?php } ?>

			<?php if($concepto=='ASA'){ ?>
				<?php if($cantidadRegistrosASA>0){ ?>
					<?php
						$a=1;
						$deudaASA=0;
						$infoConcepto='ASAMBLEA';
						$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.fechaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta, sm_mod_asistencia.lotes, sm_mod_asistencia.multa FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0 ORDER BY sm_mod_actividades.fechaActividad ASC";
						$rs = $conexion->query($sql);
						while ($datos = $rs->fetch_assoc()) {
							$temaActividad = $datos['temaActividad'];
							$fechaActividad = $datos['fechaActividad'];
							$mTardanza = $datos['mTardanza'];
							$mFalta = $datos['mFalta'];
							$lotes = $datos['lotes'];
							$multa = $datos['multa'];
					?>
						<tr>
							<td class="text-center"><?= ceros($a,2) ?></td>
							<td class="text-center"><?= $infoConcepto ?></td>
							<td class="text-left"><?= $temaActividad ?></td>
							<td class="text-center text-uppercase"><?= infoFecha($fechaActividad,'normal') ?></td>
							<td class="text-center"><?= ceros($lotes,2) ?></td>
							<td class="text-right">S/. <strong><?= moneda($multa,2) ?></strong></td>
						</tr>
					<?php
							$deudaASA=$deudaASA+$multa;
							$a++;
						}
					?>
				<?php }else{ $deudaASA=0; } ?>
			<?php } ?>

			<?php if($concepto=='FAE'){ ?>
				<?php if($cantidadRegistrosFAE>0){ ?>
					<?php
						if($b>0){
							$b=$a;
						}else{
							$b=1;
						}
						$deudaFAE=0;
						$infoConcepto='FAENA';
						$sql="SELECT sm_mod_actividades.temaActividad, sm_mod_actividades.fechaActividad, sm_mod_actividades.mTardanza, sm_mod_actividades.mFalta, sm_mod_asistencia.lotes, sm_mod_asistencia.multa FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0 ORDER BY sm_mod_actividades.fechaActividad ASC";
						$rs = $conexion->query($sql);
						while ($datos = $rs->fetch_assoc()) {
							$temaActividad = $datos['temaActividad'];
							$fechaActividad = $datos['fechaActividad'];
							$mTardanza = $datos['mTardanza'];
							$mFalta = $datos['mFalta'];
							$lotes = $datos['lotes'];
							$multa = $datos['multa'];
					?>
						<tr>
							<td class="text-center"><?= ceros($b,2) ?></td>
							<td class="text-center"><?= $infoConcepto ?></td>
							<td class="text-left"><?= $temaActividad ?></td>
							<td class="text-center text-uppercase"><?= infoFecha($fechaActividad,'normal') ?></td>
							<td class="text-center"><?= ceros($lotes,2) ?></td>
							<td class="text-right">S/. <strong><?= moneda($multa,2) ?></strong></td>
						</tr>
					<?php
							$deudaFAE=$deudaFAE+$multa;
							$b++;
						}
					?>
				<?php }else{ $deudaFAE=0; } ?>
			<?php } ?>

			<?php if($concepto=='CUO'){ ?>
				<?php if($totalDeudaCUO>0){ ?>
					<?php
						if($b>0){
							$c=$b;
						}else{
							$c=$a;
						}
						$deudaCUO=0;
						$infoConcepto='CUOTA';
						$sql="SELECT sm_mod_cuotas.conceptoCuota, sm_mod_cuotas.montoCuota, sm_mod_cuotas.fechaPago, sm_mod_cuotas_socios.lotes, sm_mod_cuotas_socios.montoPago FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0 ORDER BY sm_mod_cuotas.fechaPago ASC";
						$rs = $conexion->query($sql);
						while ($datos = $rs->fetch_assoc()) {
							$conceptoCuota = $datos['conceptoCuota'];
							$fechaPago = $datos['fechaPago'];
							$montoCuota = $datos['montoCuota'];
							$lotes = $datos['lotes'];
							$montoPago = $datos['montoPago'];
					?>
						<tr>
							<td class="text-center"><?= ceros($c,2) ?></td>
							<td class="text-center"><?= $infoConcepto ?></td>
							<td class="text-left"><?= $conceptoCuota ?></td>
							<td class="text-center text-uppercase"><?= infoFecha($fechaPago,'normal') ?></td>
							<td class="text-center"><?= ceros($lotes,2) ?></td>
							<td class="text-right">S/. <strong><?= moneda($montoPago,2) ?></strong></td>
						</tr>
					<?php
							$deudaCUO=$deudaCUO+$montoPago;
							$c++;
						}
					?>
				<?php } ?>
			<?php } ?>
		</tbody>
		<tfoot>
			<?php
				$totalDeuda=$deudaASA+$deudaFAE+$deudaCUO;
			?>
			<tr class="font-15 bg-light">
				<td colspan="5" class="text-right">TOTAL DEUDA</td>
				<td class="text-right"><strong>S/.<?= moneda($totalDeuda) ?></strong></td>
			</tr>
		</tfoot>
	</table>

	<script type="text/javascript">
		$(document).ready(function(){
			const dataTableDeudasSocios = $('#dataTableDeudasSocios').DataTable({
				"autoWidth"    : true,
				"paging"       : true,
				"pageLength"   : 8,
				"lengthChange" : true,
				"info"         : true,
				"bPaginate"    : true,
				"bSort"        : false,
				"columns": [
					{ width: "5%", targets: 0 },
					{ width: "5%", targets: 1 },
					{ width: "75%", targets: 2 },
					{ width: "5%", targets: 3 },
					{ width: "5%", targets: 4 },
					{ width: "5%", targets: 5 },
				],
			});
		});
	</script>
<?php } ?>