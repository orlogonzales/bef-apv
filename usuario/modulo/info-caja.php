<?php
	$consultaFechaIni =fechaSQL($fechaInicio);
	$consultaFechaFin =fechaSQL($fechaFin);
	$montoDisponible  =montoDisponible('','');
	
	$totalASA =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','ASA','',$usuarioConsulta,'totalActividades');
	$totalFAE =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','FAE','',$usuarioConsulta,'totalActividades');
	$totalCUO =infoCaja($consultaFechaIni,$consultaFechaFin,'ING','CUO','',$usuarioConsulta,'totalActividades');
	$totalING =infoCaja($consultaFechaIni,$consultaFechaFin,'','','',$usuarioConsulta,'totalING');
	$totalPAR =infoPartida($codigoPartida,'totalPartidas');
	$totalSAL =infoCaja($consultaFechaIni,$consultaFechaFin,'','','',$usuarioConsulta,'totalSAL');;
	$totalEGR =$totalPAR+$totalSAL;
	$totalCJA =$totalING-$totalEGR;
	$totalCLS =infoPartida('','totalPartidasCierre');
	$toPARSAL =infoPartida('','totalPartidasDispuesto');
	$toPARPEN =$totalPAR-($toPARSAL+$totalCLS);

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

	if($usuarioConsulta=="ALL"){ $consultaUsuario=""; }
	if($usuarioConsulta!="ALL"){ $consultaUsuario=" AND usuario='$usuarioConsulta'"; }
?>