<div class="row">
	<div class="col-sm-4">
		<div class="panel">
			<div class="panel-heading bg-warning-300">
				<h6 class="panel-title">MOVIENTOS EN CAJA</h6>
			</div>
			<div class="totalMonto">
				<ul class="list-group">
					<li class="list-group-item"><span>TOTAL INGRESOS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalING ?></span></li>
					<li class="list-group-item"><span>TOTAL EGRESOS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalEGR ?></span></li>
					<li class="list-group-item"><span>TOTAL DISPONIBLE</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalCJA ?></span></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel">
			<div class="panel-heading bg-warning-300">
				<h6 class="panel-title">INGRESOS PROPIOS EN CAJA</h6>
			</div>
			<div class="totalMonto">
				<ul class="list-group">
					<li class="list-group-item"><span>TOTAL ASAMBLEAS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalASA ?></span></li>
					<li class="list-group-item"><span>TOTAL FAENAS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalFAE ?></span></li>
					<li class="list-group-item"><span>TOTAL CUOTAS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalCUO ?></span></li>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel">
			<div class="panel-heading bg-warning-300">
				<h6 class="panel-title">EGRESOS DE CAJA</h6>
			</div>
			<div class="totalMonto">
				<ul class="list-group">
					<li class="list-group-item"><span>TOTAL EGRESOS PARTIDA</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalPAR ?></span></li>
					<li class="list-group-item"><span>TOTAL SALDO PARTIDA</span> <span class="pull-right text-danger textoNegrita"><?= $infoToPARPEN ?></span></li>
					<li class="list-group-item"><span>TOTAL EGRESOS VARIOS</span> <span class="pull-right text-danger textoNegrita"><?= $infoTotalSAL ?></span></li>
				</ul>
			</div>
		</div>
	</div>
</div>