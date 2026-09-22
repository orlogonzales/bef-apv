<?php
	$ruta='../../';
	include_once $ruta."php/funciones.php";
	$conexion=conexionDB();
?>
<div class="panel panel-default">
	<div class="panel-heading"><h6 class="panel-title textoNegrita text-slate">BITACORA DE SINCRONIZACION</h6></div>
	<div class="table-responsive">
		<table class="table table-xxs table-bordered table-hover">
			<thead>
				<tr class="success">
					<th class="textoNegrita text-center">#</th>
					<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
					<th class="textoNegrita text-left">SOCIOS</th>
					<th class="textoNegrita text-center">FECHA</th>
					<th class="textoNegrita text-left">HORA</th>
					<th class="textoNegrita text-left">USUARIO</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$sql="SELECT proceso, socios, fecha, hora, usuario FROM sm_socios_sync ORDER BY id DESC";
					$rs=mysqli_query($conexion,$sql);
					$i=1;
					while($n=mysqli_fetch_array($rs)){
						$proceso =$n[proceso];
						$socios =$n[socios];
						$fecha   =$n[fecha];
						$hora    =$n[hora];
						$usuario =$n[usuario];

						if($socios==0){ $socios="NINGUNO"; }else{ $socios=ceros($socios,2)." NUEVOS"; }
				?>
				<tr>
					<td class="text-center"><?= ceros($i,2) ?></td>
					<td class="text-left"><?= utf8_encode($proceso) ?></td>
					<td class="text-left"><?= $socios ?></td>
					<td class="text-center textoMayuscula"><?= infoFecha($fecha,'normal') ?></td>
					<td class="text-left"><?= horaCorta($hora) ?></td>
					<td class="text-left"><?= utf8_encode(datoUsuario($usuario,'nombre')) ?></td>
				</tr>
				<?php $i++; } ?>
			</tbody>
		</table>
	</div>
</div>