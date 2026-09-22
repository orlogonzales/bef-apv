<?php
	/////////////////////////////////////////////////////////////////////
	/// VARIABLES
	/////////////////////////////////////////////////////////////////////
	$codigoSocio =$_GET['codigoSocio'];
	$opcion      =$_GET['opcion'];
	$ruta        ='../';
	$rutaFoto    =$ruta.'assets/images/socios/';
	$sinFoto     =$ruta.'assets/images/socios/no-socio.png';
	$rutaCB      ='../assets/images/codigo-barra/';
	$fileCB      =$codigoSocio.'.png';
	$codBar      =$rutaCB.$fileCB;
	if(file_exists($codBar)){ $existe_cb='SI'; }else{ $existe_cb='NO'; }

	/////////////////////////////////////////////////////////////////////
	/// MENU HEADER PAGINA
	/////////////////////////////////////////////////////////////////////
	$menuTop="menu-socios.php";
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - TITULO DE PAGINA Y OPCIONES DE SIDEBAR
	/////////////////////////////////////////////////////////////////////
	$tituloPagina="DETALLES DE SOCIO";
	$menuActual="listaSocios";
	
	/////////////////////////////////////////////////////////////////////
	/// SECCION - RUTA DEL SISTEMA
	/////////////////////////////////////////////////////////////////////
	include($ruta.'template/header.tpl');
	$existeSocio=infoSocios($codigoSocio,'verifica');

	if($existeSocio){
		$lotesSocio  =infoSocios($codigoSocio,'lotes');
		$asambleas   =infoSocios($codigoSocio,'nroAsambleas');
		$faenas      =infoSocios($codigoSocio,'nroFaenas');
		$cuotas      =infoSocios($codigoSocio,'nroCuotas');
		$fechaHoy=infoTiempo('fechaHoy');
		$sql="SELECT dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, fotoSocio, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular, fechaAdjudica, recibo, observaciones, sincronizado, eCardSocio, fecha, hora FROM sm_socios WHERE codigoSocio='$codigoSocio'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$dni                =$dato['dni'];
		$tratamiento        =$dato['tratamiento'];
		$nombre             =$dato['nombre'];
		$apPaterno          =$dato['apPaterno'];
		$apMaterno          =$dato['apMaterno'];
		$genero             =$dato['genero'];
		$fechaNacimiento    =$dato['fechaNacimiento'];
		$edad               =calculaEdad($fechaNacimiento);
		$fotoSocio          =$dato['fotoSocio'];
		$fotoSocioDB        =$dato['fotoSocio'];
		$nacionalidad       =$dato['nacionalidad'];
		$estadoCivil        =$dato['estadoCivil'];
		$direccion          =$dato['direccion'];
		$departamento       =$dato['departamento'];
		$provincia          =$dato['provincia'];
		$distrito           =$dato['distrito'];
		$telefono           =$dato['telefono'];
		$celular            =$dato['celular'];
		$observaciones      =$dato['observaciones'];
		$sincronizado       =$dato['sincronizado'];
		$fecha              =$dato['fecha'];
		$hora               =$dato['hora'];
		$fechaAdjudica      =$dato['fechaAdjudica'];
		$recibo             =$dato['recibo'];
		$eCardSocio         =$dato['eCardSocio'];
		$cuentas            =infoSocios($dni,'cuentaDNI');
		$segundaCuenta      =infoCuentaError($codigoSocio,$dni,'segundaCuenta');

		/////////////////////////////////////////////////////////////////////
		/// CRUSA INFORMACION DE CANTIDAD DE LOTES
		/////////////////////////////////////////////////////////////////////
		$cLotes=infoSocios($codigoSocio,'cantidadLotes');
		$lotesComprados=infoSocio($dni,'lotes');
		$lotesCodigo=substr($codigoSocio,-1);

		if($eCardSocio=="SI"){ $infoEntregacard='<span class="label label-success heading-text">TARJETA ENTREGADA</span>'; }else{ $infoEntregacard='<span class="label label-warning heading-text">SIN TARJETA</span>'; }

		$relacion           =infoSocios($codigoSocio,'cosocio');
		$infoNombre         =texto($nombre.' '.$apPaterno);
		$infoNombreFull     =texto($nombre.' '.$apPaterno.' '.$apMaterno);
		$nObservaciones     =infoSocios($codigoSocio,'observaciones');

		if($relacion){ $infoCosocio="SI"; }else{ $infoCosocio="NO"; }
		
		$deudasAsambleas    =infoSocios($codigoSocio,'deudasAsambleas');
		$pagosAsambleas     =infoPago($codigoSocio,'','','pagosAsambleas');

		$deudasFaenas       =infoSocios($codigoSocio,'deudasFaenas');
		$pagosFaenas        =infoPago($codigoSocio,'','','pagosFaenas');

		$deudasCuotas       =infoSocios($codigoSocio,'deudasCuotas');
		$pagosCuotas        =infoPago($codigoSocio,'','','pagosCuotas');
		
		if($relacion){
			$sql="SELECT dni, tratamiento, nombre, apPaterno, apMaterno, genero, fechaNacimiento, nacionalidad, estadoCivil, direccion, departamento, provincia, distrito, telefono, celular FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'";
			$row=mysqli_query($conexion,$sql);
			$dato=mysqli_fetch_array($row);
			$dniCS             =$dato['dni'];
			$tratamientoCS     =$dato['tratamiento'];
			$nombreCS          =$dato['nombre'];
			$apPaternoCS       =$dato['apPaterno'];
			$apMaternoCS       =$dato['apMaterno'];
			$generoCS          =$dato['genero'];
			$fechaNacimientoCS =$dato['fechaNacimiento'];
			$fotoSocioCS       =$dato['fotoSocio'];
			$nacionalidadCS    =$dato['nacionalidad'];
			$estadoCivilCS     =$dato['estadoCivil'];
			$direccionCS       =$dato['direccion'];
			$departamentoCS    =$dato['departamento'];
			$provinciaCS       =$dato['provincia'];
			$distritoCS        =$dato['distrito'];
			$telefonoCS        =$dato['telefono'];
			$celularCS         =$dato['celular'];
		}

		/////////////////////////////////////////////////////////////////////
		/// ESTADISTICAS DE DEUDAS
		/////////////////////////////////////////////////////////////////////

		// ESTADISTICAS ASAMBLEAS
		$total_ASA_DEU_PAG=infoSocios($codigoSocio,'total_ASA_DEU_PAG');
		$pagos_ASA_efectivos=infoSocios($codigoSocio,'pagosAsambleas');
		$pagos_ASA_programados=infoSocios($codigoSocio,'pagosAsambleas_SI_MP');
		$total_ASA_justificados=infoSocios($codigoSocio,'total_ASA_justificados');
		$total_ASA_saldo=(infoSocios($codigoSocio,'porPagarAsambleas_NO_MP'))+(infoSocios($codigoSocio,'porPagarAsambleas_SI_MP'));

		if($total_ASA_DEU_PAG>0){ $info_total_ASA_DEU_PAG='S/. '.moneda($total_ASA_DEU_PAG); }else{ $info_total_ASA_DEU_PAG='<i class="fa fa-ellipsis-h"></i>'; }
		if($pagos_ASA_efectivos>0){ $info_pagos_ASA_efectivos='- S/. '.moneda($pagos_ASA_efectivos); }else{ $info_pagos_ASA_efectivos='<i class="fa fa-ellipsis-h"></i>'; }
		if($pagos_ASA_programados>0){ $info_pagos_ASA_programados='- S/. '.moneda($pagos_ASA_programados); }else{ $info_pagos_ASA_programados='<i class="fa fa-ellipsis-h"></i>'; }
		if($total_ASA_justificados>0){ $info_total_ASA_justificados='- S/. '.moneda($total_ASA_justificados); }else{ $info_total_ASA_justificados='<i class="fa fa-ellipsis-h"></i>'; }
		if($total_ASA_saldo>0){ $info_total_ASA_saldo='S/. '.moneda($total_ASA_saldo); }else{ $info_total_ASA_saldo='<i class="fa fa-ellipsis-h"></i>'; }

		// ESTADISTICAS FAENAS
		$total_FAE_DEU_PAG=infoSocios($codigoSocio,'total_FAE_DEU_PAG');
		$pagos_FAE_efectivos=infoSocios($codigoSocio,'pagosFaenas');
		$pagos_FAE_programados=infoSocios($codigoSocio,'pagosFaenas_SI_MP');
		$total_FAE_justificados=infoSocios($codigoSocio,'total_FAE_justificados');
		$total_FAE_saldo=(infoSocios($codigoSocio,'porPagarFaenas_NO_MP'))+(infoSocios($codigoSocio,'porPagarFaenas_SI_MP'));

		if($total_FAE_DEU_PAG>0){ $info_total_FAE_DEU_PAG='S/. '.moneda($total_FAE_DEU_PAG); }else{ $info_total_FAE_DEU_PAG='<i class="fa fa-ellipsis-h"></i>'; }
		if($pagos_FAE_efectivos>0){ $info_pagos_FAE_efectivos='- S/. '.moneda($pagos_FAE_efectivos); }else{ $info_pagos_FAE_efectivos='<i class="fa fa-ellipsis-h"></i>'; }
		if($pagos_FAE_programados>0){ $info_pagos_FAE_programados='- S/. '.moneda($pagos_FAE_programados); }else{ $info_pagos_FAE_programados='<i class="fa fa-ellipsis-h"></i>'; }
		if($total_FAE_justificados>0){ $info_total_FAE_justificados='- S/. '.moneda($total_FAE_justificados); }else{ $info_total_FAE_justificados='<i class="fa fa-ellipsis-h"></i>'; }
		if($total_FAE_saldo>0){ $info_total_FAE_saldo='S/. '.moneda($total_FAE_saldo); }else{ $info_total_FAE_saldo='<i class="fa fa-ellipsis-h"></i>'; }

		// ESTADISTICAS CUOTAS
		$total_CUO_DEU_PAG=infoSocios($codigoSocio,'total_CUO_DEU_PAG');
		$pagos_CUO_efectivos=infoSocios($codigoSocio,'pagosCuotas');
		$pagos_CUO_programados=infoSocios($codigoSocio,'pagosCuotas_SI_MP');
		$total_CUO_justificados=0;
		$total_CUO_saldo=(infoSocios($codigoSocio,'porPagarCuotas_NO_MP'))+(infoSocios($codigoSocio,'porPagarCuotas_SI_MP'));

		if($total_CUO_DEU_PAG>0){ $info_total_CUO_DEU_PAG='S/. '.moneda($total_CUO_DEU_PAG); }else{ $info_total_CUO_DEU_PAG='<i class="fa fa-ellipsis-h"></i>'; }
		if($pagos_CUO_efectivos>0){ $info_pagos_CUO_efectivos='- S/. '.moneda($pagos_CUO_efectivos); }else{ $info_pagos_CUO_efectivos='<i class="fa fa-ellipsis-h"></i>'; }
		if($pagos_CUO_programados>0){ $info_pagos_CUO_programados='- S/. '.moneda($pagos_CUO_programados); }else{ $info_pagos_CUO_programados='<i class="fa fa-ellipsis-h"></i>'; }
		if($total_CUO_justificados>0){ $info_total_CUO_justificados='- S/. '.moneda($total_CUO_justificados); }else{ $info_total_CUO_justificados='<i class="fa fa-ellipsis-h"></i>'; }
		if($total_CUO_saldo>0){ $info_total_CUO_saldo='S/. '.moneda($total_CUO_saldo); }else{ $info_total_CUO_saldo='<i class="fa fa-ellipsis-h"></i>'; }

		$totalGeneralDeudas=$total_ASA_DEU_PAG+$total_FAE_DEU_PAG+$total_CUO_DEU_PAG;
		$totalDeudas = $total_ASA_saldo+$total_FAE_saldo+$total_CUO_saldo;

		if($totalDeudas>0){ $infoTotalDeudas='S/. '.moneda($totalDeudas); }else{ $infoTotalDeudas='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalGeneralDeudas>0){ $infoTotalGeneralDeudas='S/. '.moneda($totalGeneralDeudas); }else{ $infoTotalGeneralDeudas='<i class="fa fa-ellipsis-h"></i>'; }


		/////////////////////////////////////////////////////////////////////
		/// ESTADISTICAS DE EXONERACIONES
		/////////////////////////////////////////////////////////////////////
		$totalExoneradoSocio=infoExoneraciones($codigoSocio,'','totalExoneradoSocio');
		$totalExoneradoSocioASA=infoExoneraciones($codigoSocio,'','totalExoneradoSocioASA');
		$totalExoneradoSocioFAE=infoExoneraciones($codigoSocio,'','totalExoneradoSocioFAE');
		$totalExoneradoSocioCUO=infoExoneraciones($codigoSocio,'','totalExoneradoSocioCUO');

		if($totalExoneradoSocio>0){ $infoTotalExoneradoSocio='- S/. '.moneda($totalExoneradoSocio); }else{ $infoTotalExoneradoSocio='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalExoneradoSocioASA>0){ $infoTotalExoneradoSocioASA='- S/. '.moneda($totalExoneradoSocioASA); }else{ $infoTotalExoneradoSocioASA='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalExoneradoSocioFAE>0){ $infoTotalExoneradoSocioFAE='- S/. '.moneda($totalExoneradoSocioFAE); }else{ $infoTotalExoneradoSocioFAE='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalExoneradoSocioCUO>0){ $infoTotalExoneradoSocioCUO='- S/. '.moneda($totalExoneradoSocioCUO); }else{ $infoTotalExoneradoSocioCUO='<i class="fa fa-ellipsis-h"></i>'; }

		$totalPagosExoneradoSocio=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocio');
		$totalPagosExoneradoSocioASA=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioASA');
		$totalPagosExoneradoSocioFAE=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioFAE');
		$totalPagosExoneradoSocioCUO=infoExoneraciones($codigoSocio,'','totalPagosExoneradoSocioCUO');

		if($totalPagosExoneradoSocio>0){ $infoTotalPagosExoneradoSocio='S/. '.moneda($totalPagosExoneradoSocio); }else{ $infoTotalPagosExoneradoSocio='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalPagosExoneradoSocioASA>0){ $infoTotalPagosExoneradoSocioASA='S/. '.moneda($totalPagosExoneradoSocioASA); }else{ $infoTotalPagosExoneradoSocioASA='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalPagosExoneradoSocioFAE>0){ $infoTotalPagosExoneradoSocioFAE='S/. '.moneda($totalPagosExoneradoSocioFAE); }else{ $infoTotalPagosExoneradoSocioFAE='<i class="fa fa-ellipsis-h"></i>'; }
		if($totalPagosExoneradoSocioCUO>0){ $infoTotalPagosExoneradoSocioCUO='S/. '.moneda($totalPagosExoneradoSocioCUO); }else{ $infoTotalPagosExoneradoSocioCUO='<i class="fa fa-ellipsis-h"></i>'; }
		

		/////////////////////////////////////////////////////////////////////
		/// CONECCION CON LA INTERFACE JSON DEL SISTEMA DE VENTAS
		/////////////////////////////////////////////////////////////////////
		$servidor=$_SERVER['HTTP_HOST'];
		if($servidor=='app.bef'){
			$urlSistema="http://{$_SERVER['HTTP_HOST']}/ventas";
			$urlSocios="http://{$_SERVER['HTTP_HOST']}/apv";
		}else{
			$urlSistema="https://{$_SERVER['HTTP_HOST']}/ventas";
			$urlSocios="https://{$_SERVER['HTTP_HOST']}/apv";
		}

		$urlJSON    = $urlSistema.'/interface/consulta.php?operacion=consulta_cuentas_socio&dni='.$dni;
		$cDatos     = curl_init($urlJSON);
		curl_setopt($cDatos,CURLOPT_RETURNTRANSFER, TRUE);
		$dCuenta    = curl_exec($cDatos);
		$infoCuentas= json_decode($dCuenta,true);
		$nroCuentas = is_countable($infoCuentas) ? count($infoCuentas) : 0;

		if($nroCuentas>0){
			$codigoCta_1  = $infoCuentas[0]['cuenta_1']['codigoCta'];
			$codigoCta_2  = $infoCuentas[1]['cuenta_2']['codigoCta'];
			$codigoCta_3  = $infoCuentas[2]['cuenta_3']['codigoCta'];
			$codigoCta_4  = $infoCuentas[3]['cuenta_4']['codigoCta'];
			$codigoCta_5  = $infoCuentas[4]['cuenta_5']['codigoCta'];
			$codigoCta_6  = $infoCuentas[5]['cuenta_6']['codigoCta'];
			$codigoCta_7  = $infoCuentas[6]['cuenta_7']['codigoCta'];
			$codigoCta_8  = $infoCuentas[7]['cuenta_8']['codigoCta'];
			$codigoCta_9  = $infoCuentas[8]['cuenta_9']['codigoCta'];
			$codigoCta_10 = $infoCuentas[9]['cuenta_10']['codigoCta'];
			$codigoCta_11 = $infoCuentas[10]['cuenta_11']['codigoCta'];
			$codigoCta_12 = $infoCuentas[11]['cuenta_12']['codigoCta'];
			$codigoCta_13 = $infoCuentas[12]['cuenta_13']['codigoCta'];
			$codigoCta_14 = $infoCuentas[13]['cuenta_14']['codigoCta'];
			$codigoCta_15 = $infoCuentas[14]['cuenta_15']['codigoCta'];
			$codigoCta_16 = $infoCuentas[15]['cuenta_16']['codigoCta'];
			$codigoCta_17 = $infoCuentas[16]['cuenta_17']['codigoCta'];
			$codigoCta_18 = $infoCuentas[17]['cuenta_18']['codigoCta'];
			$codigoCta_19 = $infoCuentas[18]['cuenta_19']['codigoCta'];
			$codigoCta_20 = $infoCuentas[19]['cuenta_20']['codigoCta'];
			$codigoCta_21 = $infoCuentas[20]['cuenta_21']['codigoCta'];
			$codigoCta_22 = $infoCuentas[21]['cuenta_22']['codigoCta'];
			$codigoCta_23 = $infoCuentas[22]['cuenta_23']['codigoCta'];
			$codigoCta_24 = $infoCuentas[23]['cuenta_24']['codigoCta'];
			$codigoCta_25 = $infoCuentas[24]['cuenta_25']['codigoCta'];
			$codigoCta_26 = $infoCuentas[25]['cuenta_26']['codigoCta'];
			$codigoCta_27 = $infoCuentas[26]['cuenta_27']['codigoCta'];
			$codigoCta_28 = $infoCuentas[27]['cuenta_28']['codigoCta'];
			$codigoCta_29 = $infoCuentas[28]['cuenta_29']['codigoCta'];
			$codigoCta_30 = $infoCuentas[29]['cuenta_30']['codigoCta'];
			$codigoCta_31 = $infoCuentas[30]['cuenta_31']['codigoCta'];
			$codigoCta_32 = $infoCuentas[31]['cuenta_32']['codigoCta'];
			$codigoCta_33 = $infoCuentas[32]['cuenta_33']['codigoCta'];
			$codigoCta_34 = $infoCuentas[33]['cuenta_34']['codigoCta'];
			$codigoCta_35 = $infoCuentas[34]['cuenta_35']['codigoCta'];
			$codigoCta_36 = $infoCuentas[35]['cuenta_36']['codigoCta'];
			$codigoCta_37 = $infoCuentas[36]['cuenta_37']['codigoCta'];
			$codigoCta_38 = $infoCuentas[37]['cuenta_38']['codigoCta'];
			$codigoCta_39 = $infoCuentas[38]['cuenta_39']['codigoCta'];
			$codigoCta_40 = $infoCuentas[39]['cuenta_40']['codigoCta'];
			$codigoCta_41 = $infoCuentas[40]['cuenta_41']['codigoCta'];
			$codigoCta_42 = $infoCuentas[41]['cuenta_42']['codigoCta'];
			$codigoCta_43 = $infoCuentas[42]['cuenta_43']['codigoCta'];
			$codigoCta_44 = $infoCuentas[43]['cuenta_44']['codigoCta'];
			$codigoCta_45 = $infoCuentas[44]['cuenta_45']['codigoCta'];
			$codigoCta_46 = $infoCuentas[45]['cuenta_46']['codigoCta'];
			$codigoCta_47 = $infoCuentas[46]['cuenta_47']['codigoCta'];
			$codigoCta_48 = $infoCuentas[47]['cuenta_48']['codigoCta'];
			$codigoCta_49 = $infoCuentas[48]['cuenta_49']['codigoCta'];
			$codigoCta_50 = $infoCuentas[49]['cuenta_50']['codigoCta'];

			$cuentasSocio[]= $codigoCta_1;
			$cuentasSocio[]= $codigoCta_2;
			$cuentasSocio[]= $codigoCta_3;
			$cuentasSocio[]= $codigoCta_4;
			$cuentasSocio[]= $codigoCta_5;
			$cuentasSocio[]= $codigoCta_6;
			$cuentasSocio[]= $codigoCta_7;
			$cuentasSocio[]= $codigoCta_8;
			$cuentasSocio[]= $codigoCta_9;
			$cuentasSocio[]= $codigoCta_10;
			$cuentasSocio[]= $codigoCta_11;
			$cuentasSocio[]= $codigoCta_12;
			$cuentasSocio[]= $codigoCta_13;
			$cuentasSocio[]= $codigoCta_14;
			$cuentasSocio[]= $codigoCta_15;
			$cuentasSocio[]= $codigoCta_16;
			$cuentasSocio[]= $codigoCta_17;
			$cuentasSocio[]= $codigoCta_18;
			$cuentasSocio[]= $codigoCta_19;
			$cuentasSocio[]= $codigoCta_20;
			$cuentasSocio[]= $codigoCta_21;
			$cuentasSocio[]= $codigoCta_22;
			$cuentasSocio[]= $codigoCta_23;
			$cuentasSocio[]= $codigoCta_24;
			$cuentasSocio[]= $codigoCta_25;
			$cuentasSocio[]= $codigoCta_26;
			$cuentasSocio[]= $codigoCta_27;
			$cuentasSocio[]= $codigoCta_28;
			$cuentasSocio[]= $codigoCta_29;
			$cuentasSocio[]= $codigoCta_30;
			$cuentasSocio[]= $codigoCta_31;
			$cuentasSocio[]= $codigoCta_32;
			$cuentasSocio[]= $codigoCta_33;
			$cuentasSocio[]= $codigoCta_34;
			$cuentasSocio[]= $codigoCta_35;
			$cuentasSocio[]= $codigoCta_36;
			$cuentasSocio[]= $codigoCta_37;
			$cuentasSocio[]= $codigoCta_38;
			$cuentasSocio[]= $codigoCta_39;
			$cuentasSocio[]= $codigoCta_40;
			$cuentasSocio[]= $codigoCta_41;
			$cuentasSocio[]= $codigoCta_42;
			$cuentasSocio[]= $codigoCta_43;
			$cuentasSocio[]= $codigoCta_44;
			$cuentasSocio[]= $codigoCta_45;
			$cuentasSocio[]= $codigoCta_46;
			$cuentasSocio[]= $codigoCta_47;
			$cuentasSocio[]= $codigoCta_48;
			$cuentasSocio[]= $codigoCta_49;
			$cuentasSocio[]= $codigoCta_50;
		}

		/////////////////////////////////////////////////////////////////////
		/// VARIABLES DE SESION
		/////////////////////////////////////////////////////////////////////
		session_start();
		$dniUsuario     = $_SESSION['dni_apv'];
		$usuarioSistema = datoUsuario($dniUsuario,'nombreCorto');
		$rolUsuario     = $_SESSION['rol_apv'];

		/////////////////////////////////////////////////////////////////////
		/// VERIFICA Y REGISTRA FOTO DE SOCIO EN BASE DE DATOS
		/////////////////////////////////////////////////////////////////////
		$fotoSocio='../assets/images/socios/'.$codigoSocio.'.jpg';
		if (file_exists($fotoSocio) and $fotoSocioDB!='') {
			$verificaRegistro=verificaFotoDB($codigoSocio,'verifica');
			if($verificaRegistro=='FOTO_NO_REGISTRADA'){ $verificaRegistro=verificaFotoDB($codigoSocio,'registraFoto'); }
			$fotoSocio=$urlSocios.'/assets/images/socios/'.$codigoSocio.'.jpg';
		} else {
			$fotoSocio=$urlSocios.'/assets/images/socios/no-socio.png';
		}
?>
	<?php if($opcion=="detalles"){ ?>
		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CONFIGURACION DE DATEPICKER
				///////////////////////////////////////////////////
				$(".fechas").datepicker({
					format: 'dd/mm/yyyy'
				});

				///////////////////////////////////////////////////
				/// CAMBIO DE FOTOGRAFIA DE SOCIO
				///////////////////////////////////////////////////
				$("button#subirFoto").click(function(){
					var ruta        ="<?= $ruta ?>";
					var opcion      ="<?= $opcion ?>";
					var codigoSocio ="<?= $codigoSocio ?>";
					var operacion   ="ACTUALIZA_FOTO";
					var datos       = new FormData($("#formCambioFoto")[0]);
					var foto        = $('#fotoSocio').val();
					var socio       ="<?= ucwords(strtolower($nombre)) ?>";
					$.ajax({
						url: '../php/mantenimiento-socios.php?codigoSocio='+codigoSocio+'&subir='+operacion,
						type: "POST",
						dataType:'json',
						data: datos,
						contentType: false,
						processData: false,
						beforeSend: function(){
							$('#campoFoto').hide();
							$('button#subirFoto').prop('disabled', true);
							$('#procensadoSubida').fadeIn("slow").html('<div class="row mt-10"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"></div></div>').fadeIn(1000).delay(1000);
						},
						success: function(respuesta){
							if(respuesta.mensaje=="PROCESADO"){
								new PNotify({title: 'ACTUALIZADO', text: 'La foto de '+socio+' fue actualizada.', addclass: 'bg-success'});
								location.reload();
							}
							if(respuesta.mensaje=="NOPROCESADO"){
								$('#procensadoSubida').hide();
								$('#campoFoto').show();
								$('button#subirFoto').prop('disabled', false);
								new PNotify({title: 'ERROR', text: 'No se pudo cambiar la foto.', addclass: 'bg-warning'});
							}
							if(respuesta.mensaje=="ERRORFILE"){
								$('#procensadoSubida').hide();
								$('#campoFoto').show();
								$('button#subirFoto').prop('disabled', false);
								new PNotify({title: 'ERROR', text: 'La foto tiene que ser en formato JPG o ser menor que 200Kb.', addclass: 'bg-warning'});
							}
							if(respuesta.mensaje=="ERRORSERVER"){
								$('#procensadoSubida').hide();
								$('#campoFoto').show();
								$('button#subirFoto').prop('disabled', false);
								new PNotify({title: 'ERROR', text: 'Ha ocurrido un error en el servidor.', addclass: 'bg-warning'});
							}
						}
					});
				});
				
				///////////////////////////////////////////////////
				/// MODIFICA DATOS DE SOCIO
				///////////////////////////////////////////////////
				$("button#bt_edita_socio").click(function(){
					var ruta        ="<?= $ruta ?>";
					var datos           =$('#form_datos_socios').serialize();
   					var valida          =$('.form_valida_datos_socio').valid();
					if(valida){
						$.ajax({
							type: 'POST',
							url: ruta+'php/mantenimiento-socios.php',
							data: datos,
							dataType:'json',
							success:function(respuesta){
								if(respuesta.mensaje=="DATOS_SOCIOS_MODIFICADOS"){
									new PNotify({title: 'ALMACENADO', text: 'Los datos de socio fueron modificados.', addclass: 'bg-success'});
									location.reload();
								}
								if(respuesta.mensaje=="ERROR_DATOS_SOCIOS_MODIFICADOS"){
									new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
								}
							}
						});
					}
				});

				///////////////////////////////////////////////////
				/// VALIDA DATOS DE SOCIO
				///////////////////////////////////////////////////
				var validator = $(".form_valida_datos_socio").validate({
					errorClass: 'validation-error-label',
					highlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D65C4F'); }); },
					unhighlight: function(element, errorClass) { $(element).html(function(){ $(element).fadeIn(5000).css('border-color','#D5D5D5'); }); },
					validClass: "validation-valid-label",
					rules: {
						nombre: { required: true, },
						apPaterno: { required: true, },
						apMaterno: { required: true, },
						genero: { required: true, },
						nacionalidad: { required: true, },
						fechaNacimiento: { required: true, },
						estadoCivil: { required: true, },
						direccion: { required: true, },
						departamento: { required: true, },
						provincia: { required: true, },
						distrito: { required: true, },
						celular: { required: true, },
					},
					messages: {
						nombre: { required: 'Nombre(s)', },
						apPaterno: { required: 'Apellido paterno', },
						apMaterno: { required: 'Apellido materno', },
						genero: { required: 'Genero', },
						nacionalidad: { required: 'Nacionalidad', },
						fechaNacimiento: { required: 'F. Nacimiento', },
						estadoCivil: { required: '¿Casado?', },
						direccion: { required: 'Dirección', },
						departamento: { required: 'Departamento', },
						provincia: { required: 'Provincia', },
						distrito: { required: 'Distrito', },
						celular: { required: 'Celular', },
					}
				});



				///////////////////////////////////////////////////
				/// ENTREGAR TARJETA A SOCIO
				///////////////////////////////////////////////////
				$('#bt_exonerar_deudas').on('click', function() {
					var ruta        ='../';
					var codigoSocio ='<?= $codigoSocio ?>';
					var operacion   ='EXONERAR_DEUDAS_PASO_1';
					var datos       ='codigoSocio='+codigoSocio+'&operacion='+operacion;
					var urlProceso  = ruta+'/modulo/modal-exonerar-deudas-socio.php?'+datos;
					$('#modal_exonerar_deudas').modal({ backdrop: 'static', keyboard: false, show: true });
					$('#modal_exonerar_deudas').on('shown.bs.modal',function(){
						$('#box_modal_exonerar_deudas').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
						$("#box_modal_exonerar_deudas").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
					});
				});

				///////////////////////////////////////////////////
				/// AGREGAR OBSERVACIONES
				///////////////////////////////////////////////////
				$("button#bt_salvar_observacion").click(function(){
					var ruta        ="<?= $ruta ?>";
					var codigoSocio ="<?= $codigoSocio ?>";
					var observacion =$("textarea#observacion").val();
					var operacion   ="AGREGA_OBSERVACION";
					var datos       ="codigoSocio="+codigoSocio+"&observacion="+observacion+"&operacion="+operacion;
					if($.trim(observacion).length>0){
						$.ajax({
							type: 'POST',
							url: ruta+'php/mantenimiento-socios.php',
							data: datos,
							dataType:'json',
							success:function(respuesta){
								if(respuesta.mensaje=="OBSERVACION_AGREGADA"){
									new PNotify({title: 'ALMACENADO', text: 'Observación agregada a socio.', addclass: 'bg-success'});
									location.reload();
								}
								if(respuesta.mensaje=="ERROR_OBSERVACION_AGREGADA"){
									new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
								}
							}
						});
					}else{
						$("textarea#observacion").focus();
						new PNotify({title: 'ERROR', text: 'Por favor ingrese observación.', addclass: 'bg-warning'});
					}
				});

				///////////////////////////////////////////////////
				/// EDITA OBSERVACIONES
				///////////////////////////////////////////////////
				$("button.bt_editar_observacion").click(function(){
					var ruta          ="<?= $ruta ?>";
					var idObservacion =$(this).attr('id');
					var codigoSocio   ="<?= $codigoSocio ?>";
					var observacion   =$("textarea#detalleObservacion-"+idObservacion).val();
					var operacion     ="EDITA_OBSERVACION";
					var datos         ="codigoSocio="+codigoSocio+"&observacion="+observacion+'&id='+idObservacion+"&operacion="+operacion;
					if($.trim(observacion).length>0){
						$.ajax({
							type: 'POST',
							url: ruta+'php/mantenimiento-socios.php',
							data: datos,
							dataType:'json',
							success:function(respuesta){
								if(respuesta.mensaje=="OBSERVACION_MODIFICADA"){
									new PNotify({title: 'ALMACENADO', text: 'Observación modificada.', addclass: 'bg-success'});
									location.reload();
								}
								if(respuesta.mensaje=="ERROR_OBSERVACION_MODIFICADA"){
									new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
								}
							}
						});
					}else{
						$("textarea#observacion").focus();
						new PNotify({title: 'ERROR', text: 'Por favor ingrese observación.', addclass: 'bg-warning'});
					}
				});


				///////////////////////////////////////////////////
				/// ELIMINA OBSERVACION
				///////////////////////////////////////////////////
				$('.bt_elimina_observacion').on('click', function() {
					var ruta        ='../';
					var observacion = $(this).attr('id');
					var codigoSocio ='<?= $codigoSocio ?>';
					var operacion   ='ELIMINAR_OBSERVACION';
					var datos       ='observacion='+observacion+'&codigoSocio='+codigoSocio+'&operacion='+operacion;
					swal({
						title: "Eliminar",
						text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Eliminar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="OBSERVACION_ELIMINADA"){
										new PNotify({title: 'ELIMINADO', text: 'El item seleccionado fue eliminadO del sistema.', addclass: 'bg-success'});
										location.reload();
									}
									if(respuesta.mensaje=="ERROR_OBSERVACION_ELIMINADA"){
										new PNotify({title: 'ERROR', text: 'Por favor intentelo nuevamente.', addclass: 'bg-warning'});
									}
								}
							});
						}
					});
				});
				
				///////////////////////////////////////////////////
				/// ELIMINAR SOCIO
				///////////////////////////////////////////////////
				$('#bt_eliminar_socio').on('click', function() {
					var ruta        ='../';
					var codigoSocio ='<?= $codigoSocio ?>';
					var operacion   ='ELIMINA_SOCIO';
					var datos       ='codigoSocio='+codigoSocio+'&operacion='+operacion;
					swal({
						title: "Eliminar Socio de Sistema",
						text: "Se va ha eliminar el socio elegido del sistema, ¿desea continuar?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Eliminar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="SOCIO_ELIMINADO"){
										location.reload();
									}
								}
							});
						}
					});
				});

				///////////////////////////////////////////////////
				/// ELIMINAR FOTO DE SOCIO
				///////////////////////////////////////////////////
				$('#bt_foto_socio').on('click', function() {
					var ruta        ='../';
					var codigoSocio ='<?= $codigoSocio ?>';
					var operacion   ='ELIMINAR_FOTO_SOCIO';
					var datos       ='codigoSocio='+codigoSocio+'&operacion='+operacion;
					swal({
						title: "Quitar foto de Socio",
						text: "Se va ha quitar la foto de socio, ¿desea continuar?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Quitar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="FOTO_SOCIO_ELIMINADO"){
										location.reload();
									}
								}
							});
						}
					});
				});

				///////////////////////////////////////////////////
				/// ENTREGAR TARJETA A SOCIO
				///////////////////////////////////////////////////
				$('#entregar_tarjeta').on('click', function() {
					var ruta        ='../';
					var codigoSocio ='<?= $codigoSocio ?>';
					var operacion   ='ENTREGAR_TARJETA';
					var datos       ='codigoSocio='+codigoSocio+'&operacion='+operacion;
					swal({
						title: "Entregar Tarjeta de Socio",
						text: "Se va ha entregar la Tarjeta de socio",
						type: "success",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Entregar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							swal({ title: "Entregado!", text: "La tarjeta fue entregada a socio.", confirmButtonColor: "#66BB6A", type: "success" });
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="TARJETA_ENTREGADA"){
										location.reload();
									}
								}
							});
						}
					});
				});

				///////////////////////////////////////////////////
				/// ENTREGAR DUPLICADO TARJETA A SOCIO
				///////////////////////////////////////////////////
				$('#entregar_duplicado_tarjeta').on('click', function() {
					var ruta        ='../';
					var codigoSocio ='<?= $codigoSocio ?>';
					var operacion   ='ENTREGAR_DUPLICADO_TARJETA';
					var datos       ='codigoSocio='+codigoSocio+'&operacion='+operacion;
					swal({
						title: "Entregar Duplicado de Tarjeta de Socio",
						text: "Se va ha entregar la Tarjeta de socio",
						type: "success",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Entregar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							swal({ title: "Entregado!", text: "La tarjeta fue entregada a socio.", confirmButtonColor: "#66BB6A", type: "success" });
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="TARJETA_ENTREGADA"){
										location.reload();
									}
								}
							});
						}
					});
				});

				///////////////////////////////////////////////////
				/// CAMBIO DE ESTILO DE INPUT FILE
				///////////////////////////////////////////////////
				$(".inputSubir").uniform({
					wrapperClass: 'bg-warning',
					fileButtonHtml: '<i class="icon-plus2"></i>'
				});

				///////////////////////////////////////////////////
				/// CAMBIO DE ESTILO DE INPUT SELECT
				///////////////////////////////////////////////////
				$(".switch").bootstrapSwitch();
			});
		</script> 
		<div class="row">
			<div class="col-lg-9">
				<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
					<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
					<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
				</div>

				<!-- INFORMACION DE SOCIO -->
				<div class="panel">
					<div class="panel-heading bg-brown">
						<h6 class="panel-title textoNegrita">DATOS DE SOCIO</h6>
						<div class="heading-elements">
							<?= $infoEntregacard ?>
						</div>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-6">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">FECHA DE ADJUDICACION</span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaAdjudica,'corta') ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">DNI</span> <span class="pull-right text-slate"><?= $dni ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">NOMBRE</span> <span class="pull-right text-slate"><?= texto($nombre) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">APELLIDOS</span> <span class="pull-right text-slate"><?= texto($apPaterno.' '.$apMaterno) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">GENERO</span> <span class="pull-right text-slate"><?= infoGenero($genero) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">FECHA DE NACIMIENTO</span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaNacimiento,'corta') ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">EDAD</span> <span class="pull-right text-slate"><?= $edad ?> AÑOS</span></li>
									<li class="list-group-item"><span class="textoNegrita">ESTADO CIVIL</span> <span class="pull-right text-slate"><?= estadoCivil($estadoCivil) ?></span></li>
								</ul>
							</div>
							<div class="col-sm-6">
								<ul class="list-group">
									<li class="list-group-item"><span class="textoNegrita">EXPEDIENTE NRO.</span> <span class="pull-right text-slate"><?= $recibo ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">NACIONALIDAD</span> <span class="pull-right text-slate"><?= $nacionalidad ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">DIR</span> <span class="pull-right text-slate"><?= texto($direccion) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">DEPARTAMENTO</span> <span class="pull-right text-slate"><?= infoDepartamento($departamento) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">PROVINCIA</span> <span class="pull-right text-slate"><?= $provincia ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">DISTRITO</span> <span class="pull-right text-slate"><?= texto($distrito) ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">CELULAR / TELEFONO</span> <span class="pull-right text-slate"><?php if($celular){ echo $celular; } if($telefono){ echo ' / '.$telefono; } ?></span></li>
									<li class="list-group-item"><span class="textoNegrita">CO-SOCIO</span> <span class="pull-right text-slate"><?= $infoCosocio ?></span></li>
								</ul>
							</div>
						</div>
					</div>
				</div>

				<!-- INFORMACION DE CO-SOCIO -->
				<?php if($relacion){ ?>
					<div class="panel panel-default">
						<div class="panel-heading"><h6 class="panel-title textoNegrita text-slate">DATOS DEL CO-SOCIO</h6></div>
						<div class="panel-body">
							<div class="row">
								<div class="col-sm-6">
									<ul class="list-group">
										<li class="list-group-item"><span class="textoNegrita">DNI</span> <span class="pull-right text-slate"><?= $dniCS ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">NOMBRE</span> <span class="pull-right text-slate"><?= texto($nombreCS) ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">APELLIDOS</span> <span class="pull-right text-slate"><?= texto($apPaternoCS.' '.$apMaternoCS) ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">GENERO</span> <span class="pull-right text-slate"><?= infoGenero($generoCS) ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">FECHA DE NACIMIENTO</span> <span class="pull-right text-slate textoMayuscula"><?= infoFecha($fechaNacimientoCS,'corta') ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">EDAD</span> <span class="pull-right text-slate"><?= calculaEdad($fechaNacimientoCS) ?> AÑOS</span></li>
										<li class="list-group-item"><span class="textoNegrita">ESTADO CIVIL</span> <span class="pull-right text-slate"><?= estadoCivil($estadoCivilCS) ?></span></li>
									</ul>
								</div>
								<div class="col-sm-6">
									<ul class="list-group">
										<li class="list-group-item"><span class="textoNegrita">NACIONALIDAD</span> <span class="pull-right text-slate"><?= $nacionalidadCS ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">DIR</span> <span class="pull-right text-slate"><?= texto($direccionCS) ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">DEPARTAMENTO</span> <span class="pull-right text-slate"><?= infoDepartamento($departamentoCS) ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">PROVINCIA</span> <span class="pull-right text-slate"><?= $provinciaCS ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">DISTRITO</span> <span class="pull-right text-slate"><?= texto($distritoCS) ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">CELULAR</span> <span class="pull-right text-slate"><?php if($celularCS){ echo $celularCS; } if($telefonoCS){ echo ' / '.$telefonoCS; } ?></span></li>
										<li class="list-group-item"><span class="textoNegrita">CO-SOCIO</span> <span class="pull-right text-slate"><?= infoRelacion($relacion) ?></span></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- INFORMACION DE CUENTAS DE ADQUISICION DE LOTES -->
				<?php if($nroCuentas>0){ ?>
					<div class="panel panel-danger">
						<div class="panel-heading"><h6 class="panel-title textoNegrita">CUENTAS REGISTRADAS EN SISTEMA DE VENTAS</h6></div>
						<table class="table table-sm table-bordered table-hover table-responsive">
							<thead>
								<tr class="danger">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-left">CODIGO DE CUENTA</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-center">PAGADO</th>
									<th class="textoNegrita text-center">DEBE</th>
									<th class="textoNegrita text-center">PAGOS</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="textoNegrita text-center"><i class="fa fa-th-list"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$operacion='consulta_informacion_cuenta';
									$i=1;
									foreach($cuentasSocio as $codigoCta){
										if(!empty($codigoCta)){
											$urlJSON       = $urlSistema.'/interface/consulta.php?operacion='.$operacion.'&codigoCta='.$codigoCta;
											$cDatos        = curl_init($urlJSON);
											curl_setopt($cDatos,CURLOPT_RETURNTRANSFER, TRUE);
											$dCuenta       = curl_exec($cDatos);
											$infoCuenta    = json_decode($dCuenta,true);
											$monedaCuenta  =$infoCuenta[0]['monedaCuenta'];
											$lotesEnCuenta =$infoCuenta[0]['lotesEnCuenta'];
											$empadronado   =$infoCuenta[0]['empadronado'];
											$totalPagado   =$infoCuenta[0]['totalPagado'];
											$porcentaje    =$infoCuenta[0]['porcentaje'];
											$porPagar      =$infoCuenta[0]['porPagar'];
											$observaciones =$infoCuenta[0]['observaciones'];

											if($monedaCuenta=='SOL'){ $simbolo='S/. '; }else{ $simbolo='$ '; }
											if($totalPagado>0){ $infoTotalPagado=$simbolo.moneda($totalPagado); }else{ $infoTotalPagado=''; }
											if($porPagar>0){ $infoPorPagar=$simbolo.moneda($porPagar); }else{ $infoPorPagar=''; }
											if($porcentaje<100){ $infoPorcentaje= '<span class="label label-danger heading-text">'.numero($porcentaje).'%</span>'; }else{ $infoPorcentaje= '<span class="label label-success heading-text">'.numero($porcentaje).'%</span>'; }
											if($empadronado=='EXISTE'){ $estadoEmpadronado='<span class="label label-success heading-text">EMPADRONADO</span>'; }
											if($empadronado==''){ $estadoEmpadronado='<span class="label label-warning heading-text">NO EMPADRONADO</span>'; }
								?>
									<tr>
										<td class="text-center"><?= ceros($i,2) ?></td>
										<td><?= $codigoCta ?></td>
										<td class="text-center"><?= ceros($lotesEnCuenta,2) ?></td>
										<td class="text-right"><?= $infoTotalPagado ?></td>
										<td class="text-right"><?= $infoPorPagar ?></td>
										<td class="text-center"><?= $infoPorcentaje ?></td>
										<td class="text-center"><?= $estadoEmpadronado ?></td>
										<td class="text-center">
											<?php if($empadronado=='EXISTE'){ ?>
												<ul class="icons-list">
													<li class="dropup">
														<a href="javascript: void(0)" class="dropdown-toggle btn btn-icon btn-xs btn-danger" data-toggle="dropdown"><i class="icon-menu7"></i> OPCIONES</a>
														<ul class="dropdown-menu dropdown-menu-right">
															<li><a href="javascript: void(0)" class="bt_observaciones_empadronado" id="<?= $codigoCta ?>"><i class="icon-file-text2"></i> VER OBSERVACIONES</a></li>
															<li><a href="javascript: void(0)" class="bt_agrega_observaciones_empadronado" id="<?= $codigoCta ?>"><i class="icon-pencil7"></i> AGREGAR OBSERVACIONES</a></li>
															<li class="divider"></li>
															<li><a href="<?= $urlSistema ?>/documentos/reporte-empadronados.php?codigoCta=<?= $codigoCta ?>&operacion=imprimir_constacia_empadronamiento&origen=sistemaSocios&dniUsuario=<?= $dniUsuario ?>&usuarioSistema=<?= $usuarioSistema ?>"><i class="icon-printer2"></i> CONSTANCIA DE EMPADRONAMIENTO</a></li>
															<li><a href="<?= $urlSistema ?>/documentos/reporte-empadronados.php?codigoCta=<?= $codigoCta ?>&operacion=imprimir_acta_construccion&origen=sistemaSocios&dniUsuario=<?= $dniUsuario ?>&usuarioSistema=<?= $usuarioSistema ?>"><i class="icon-printer2"></i> ACTA DE COMPROMISO CONSTRUCCION</a></li>
															<li class="divider"></li>
															<li><a href="javascript: void(0)" class="bt_eliminar_empadronamiento" id="<?= $codigoCta ?>"><i class="icon-trash text-danger"></i> <strong class="text-danger">ELIMINAR EMPADRONAMIENTO</strong></a></li>
														</ul>
													</li>
												</ul>
											<?php } ?>
											<?php if($empadronado=='NO_EXISTE'){ ?>
												<button type="button" class="btn btn-xs btn-danger bt_empadronar" id="<?= $codigoCta ?>">EMPADRONAR</button>
											<?php } ?>
										</td>
									</tr>
								<?php
										}
										$i++;
									}
								?>
							</tbody>
						</table>
					</div>
					
					<!--////////////////////////////////////////////////////////////////////////////////
					MODAL - LISTAR OBSERVACIONES DE CUENTA
					////////////////////////////////////////////////////////////////////////////////-->
					<div id="modal_visualiza_observaciones_empadronado" class="modal fade" tabindex="-1" role="dialog">
						<div class="modal-dialog modal-full">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<h6 class="modal-title">
										<button type="button" class="btn btn-xs btn-default pull-right" data-dismiss="modal" style="margin-top: -5px;">CERRAR VENTANA</button>
										<strong>OBSERVACIONES DE EMPADRONAMIENTO</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<strong>CODIGO DE CUENTA:</strong>&nbsp;&nbsp;<span id="codigoCuenta"></span>
									</h6>
								</div>
								<div id="box_visualiza_observaciones_empadronado"></div>
							</div>
						</div>
					</div>

					<!--////////////////////////////////////////////////////////////////////////////////
					MODAL - AGREGAR OBSERVACION A CUENTA
					////////////////////////////////////////////////////////////////////////////////-->
					<div id="modal_agrega_observaciones_empadronado" class="modal fade" tabindex="-1" role="dialog">
						<div class="modal-dialog modal-md">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<h6 class="modal-title">
										<button type="button" class="btn btn-xs btn-default pull-right" data-dismiss="modal" style="margin-top: -5px;">CERRAR VENTANA</button>
										<strong>AGREGA OBSERVACIONES</strong> A CUENTA</span>
									</h6>
								</div>
								<div id="box_agrega_observaciones_empadronado"></div>
							</div>
						</div>
					</div>

					<!-- PROCESOS DE EMPADRONAMIENTO -->
					<script type="text/javascript">
						$(document).ready(function(){
							///////////////////////////////////////////////////
							/// VER OBSERVACIONES
							///////////////////////////////////////////////////
							$("a.bt_observaciones_empadronado").click(function(){
								var codigoCta      = $(this).attr('id');
								var operacion      = 'lista_observaciones';
								var dniUsuario     = '<?= $dniUsuario ?>';
								var rolUsuario     = '<?= $rolUsuario ?>';
								var origen         = 'sistemaSocios';
								var datos          = 'codigoCta='+codigoCta+'&dniUsuario='+dniUsuario+'&rolUsuario='+rolUsuario+'&origen='+origen+'&operacion='+operacion;
								var urlSistema     = '<?= $urlSistema ?>';
								var urlProceso     = urlSistema+'/modulo/modal_observaciones_empadronado_lote.php?'+datos;
							 	$('#modal_visualiza_observaciones_empadronado').modal({ backdrop: 'static', keyboard: false, show: true });
								$('#modal_visualiza_observaciones_empadronado').on('shown.bs.modal',function(){
									$("#codigoCuenta").html(codigoCta);
									$('#box_visualiza_observaciones_empadronado').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+urlSistema+'/images/loader.gif"><br>CARGANDO MODULO</div></div></div>');
									$("#box_visualiza_observaciones_empadronado").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
								});
							});
							
							///////////////////////////////////////////////////
							/// VER OBSERVACIONES
							///////////////////////////////////////////////////
							$("a.bt_agrega_observaciones_empadronado").click(function(){
								var codigoCta      = $(this).attr('id');
								var operacion      = 'agrega_observacion_desde_sistema_socios';
								var dniUsuario     = '<?= $dniUsuario ?>';
								var rolUsuario     = '<?= $rolUsuario ?>';
								var origen         = 'sistemaSocios';
								var datos          = 'codigoCta='+codigoCta+'&dniUsuario='+dniUsuario+'&rolUsuario='+rolUsuario+'&origen='+origen+'&operacion='+operacion;
								var urlSistema     = '<?= $urlSistema ?>';
								var urlProceso     = urlSistema+'/modulo/modal_observaciones_empadronado_lote.php?'+datos;
							 	$('#modal_agrega_observaciones_empadronado').modal({ backdrop: 'static', keyboard: false, show: true });
								$('#modal_agrega_observaciones_empadronado').on('shown.bs.modal',function(){
									$('#box_agrega_observaciones_empadronado').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+urlSistema+'/images/loader.gif"><br>CARGANDO MODULO</div></div></div>');
									$("#box_agrega_observaciones_empadronado").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
								});
							});

							///////////////////////////////////////////////////
							/// ELIMINAR EMPADRONAMIENTO
							///////////////////////////////////////////////////
							$("a.bt_eliminar_empadronamiento").click(function(){
								var codigoCta      = $(this).attr('id');
								var operacion      = 'eliminar_empadronamiento';
								var dniUsuario     = '<?= $dniUsuario ?>';
								var usuarioSistema = '<?= $usuarioSistema ?>';
								var origen         = 'sistemaSocios';
								var datos          = 'codigoCta='+codigoCta+'&origen='+origen+'&dniUsuario='+dniUsuario+'&usuarioSistema='+usuarioSistema+'&operacion='+operacion;
								var urlSistema     = '<?= $urlSistema ?>';
								var urlProceso     = urlSistema+'/php/mantenimiento_empadronar_socio.php';
								swal({
									title: "Eliminar empadronamiento",
									text: "Se va ha eliminar el empadronamiento de los lotes de esta cuenta, ¿esta seguro de hacerlo?",
									type: "warning",
									showCancelButton: true,
									confirmButtonColor: "#EF5350",
									confirmButtonText: "Si, Eliminar",
									cancelButtonText: "No, Cancelar",
									closeOnConfirm: true,
									closeOnCancel: true
								},
								function(isConfirm){
									if (isConfirm) { 
										swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
										$.ajax({
											type: "POST",
											url: urlProceso,
											data: datos,
											dataType:'json',
											success: function(respuesta){
												if(respuesta.mensaje=="EMPADRONAMIENTO_ELIMINADO"){
													new PNotify({title: 'ELIMINADO', text: 'El empadronamiento para esta cuenta fue eliminado.', addclass: 'bg-success'});
													location.reload();
												}
												if(respuesta.mensaje=="EMPADRONAMIENTO_NO_ELIMINADO"){
													new PNotify({title: 'ADVERTENCIA', text: 'Ocurrio un error en el registro a la base de datos, por favor intentelo nuevamente.', addclass: 'bg-warning'});
												}
											}
										});
									}
								});
							});

							///////////////////////////////////////////////////
							/// EMPADRONAR SOCIO
							///////////////////////////////////////////////////
							$("button.bt_empadronar").click(function(){
								var operacion  = 'empadronar_socio_desde_sistema_socios';
								var codigoCta  = $(this).attr('id');
								var dniUsuario = '<?= $dniUsuario ?>';
								var rolUsuario = '<?= $rolUsuario ?>';
								var origen     = 'sistemaSocios';
								var datos      = 'codigoCta='+codigoCta+'&dniUsuario='+dniUsuario+'&rolUsuario='+rolUsuario+'&operacion='+operacion;
								var urlSistema = '<?= $urlSistema ?>';
								var urlProceso = urlSistema+'/php/mantenimiento_empadronar_socio.php';
								$.ajax({
									type: "POST",
									url: urlProceso,
									data: datos,
									dataType:'json',
									beforeSend: function(){ $('#procesando').fadeIn("slow").html('<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="images/loader.gif"><br>PROCESANDO</div></div></div><br>'); },
									success: function(respuesta){
										if(respuesta.mensaje=='SOCIO_EMPADRONADO'){
											location.reload(true);
										}else{
											new PNotify({title: 'ADVERTENCIA', text: 'Ocurrio un error en la conexion con la base de datos.', addclass: 'bg-danger'});
										}
									}
								});
							});
						});
					</script>
				<?php } ?>

				<!-- INFORMACION DE LOTES EN PROPIEDAD -->
				<div class="panel panel-info">
					<div class="panel-heading"><h6 class="panel-title textoNegrita">LOTES EN PROPIEDAD</h6></div>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-sm table-bordered table-hover">
								<thead>
									<tr class="success">
										<th class="textoNegrita text-center">#</th>
										<th class="textoNegrita text-left">CODIGO</th>
										<th class="textoNegrita text-center">SECTOR</th>
										<th class="textoNegrita text-center">MANZANA</th>
										<th class="textoNegrita text-center">LOTE</th>
										<th class="textoNegrita text-left" colspan="2">DIRECCION</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$l=1;
										$sql="SELECT lotes, sector, manzana, lote, codigoLote, direccion FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio' ORDER BY manzana ASC";
										$rs=mysqli_query($conexion,$sql);
										$contar=mysqli_num_rows($rs);
										mysqli_set_charset($conexion, "utf8");
										while($n=mysqli_fetch_array($rs)){
											$lotes      =$n['lotes'];
											$sector     =$n['sector'];
											$manzana    =$n['manzana'];
											$lote       =$n['lote'];
											$codigoLote =$n['codigoLote'];
											$direccion  =$n['direccion'];
									 ?>
										<tr>
											<td><?= $l ?></td>
											<td><?= $codigoLote ?></td>
											<td><?= $sector ?></td>
											<td><?= $manzana ?></td>
											<td><?= $lote ?></td>
											<td><?= texto($direccion) ?></td>
											<td><a href="javascript: void(0)" class="textoNegrita bt_actualiza_direccion" id="<?= $codigoLote ?>">Editar</a></td>
										</tr>
									<?php $l++; } ?>
								</tbody>
							</table>
						</div>
					</div>

					<!--////////////////////////////////////////////////////////////////////////////////
					MODAL - EDITAR DIRECCION DE LOTE
					////////////////////////////////////////////////////////////////////////////////-->
					<div id="modal_actualiza_direccion_lote" class="modal fade" tabindex="-1" role="dialog">
						<div class="modal-dialog modal-md">
							<div class="modal-content">
								<div class="modal-header bg-danger">
									<h6 class="modal-title"><strong>ACTUALIZAR DIRECCION</strong>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<strong>CODIGO DE LOTE:</strong>&nbsp;&nbsp;<span id="codigoLote"></span></h6>
								</div>
								<div id="box_actualiza_direccion_lote"></div>
							</div>
						</div>
					</div>

					<!-- PROCESOS DE LOTES -->
					<script type="text/javascript">
						$(document).ready(function(){
							///////////////////////////////////////////////////
							/// AGREGA DIRECCION A LOTE
							///////////////////////////////////////////////////
							$("a.bt_actualiza_direccion").click(function(){
								var codigoLote      = $(this).attr('id');
								var codigoSocio     = '<?= $codigoSocio ?>';
								var urlSistema     = '<?= $urlSocios ?>';
								var datos          = 'codigoLote='+codigoLote+'&codigoSocio='+codigoSocio;
								var urlProceso     = urlSistema+'/formularios/actualizacion_direccion_lote.php?'+datos;
							 	$('#modal_actualiza_direccion_lote').modal({ backdrop: 'static', keyboard: false, show: true });
								$('#modal_actualiza_direccion_lote').on('shown.bs.modal',function(){
									$('#codigoLote').html(codigoLote);
									$('#box_actualiza_direccion_lote').fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><br><img src="'+urlSistema+'/images/loader.gif"><br>CARGANDO MODULO</div></div></div>');
									$("#box_actualiza_direccion_lote").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
								});
							});
						});
					</script>
				</div>

				<!-- LISTA DE OBSERVACIONES -->
				<?php if($nObservaciones>0){ ?>
					<div class="panel">
						<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">OBSERVACIONES DE SOCIO</h6></div>
						<div class="table-responsive">
							<table class="table tabla-info table-bordered table-hover">
								<thead>
									<tr class="success">
										<th class="textoNegrita text-center">#</th>
										<th class="textoNegrita text-left">OBSERVACION</th>
										<th class="textoNegrita text-center">REGISTRADO</th>
										<th class="textoNegrita text-center"><i class="icon-menu7"><i/></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$sql="SELECT id, observacion, fecha, hora, usuario FROM sm_socios_observacion WHERE codigoSocio='$codigoSocio' ORDER BY id DESC";
										$rs=mysqli_query($conexion,$sql);
										$i=1;
										while($n=mysqli_fetch_array($rs)){
											$id          =$n['id'];
											$observacion =$n['observacion'];
											$fecha       =$n['fecha'];
											$hora        =$n['hora'];
											$usuario     =$n['usuario'];
											$infoRegistro=registradoPor($usuario,$fecha,$hora,'NO','');
									?>
									<tr>
										<td class="text-center"><?= ceros($i,2) ?></td>
										<td class="text-left"><?= texto($observacion) ?></td>
										<td class="text-center"><span class="label label-default"><?= $infoRegistro.' | '.horaCorta($hora) ?></span></td>
										<td class="text-center">
											<button type="button" id="<?= $id ?>" data-toggle="modal" data-target="#editaObservacion-<?= $id ?>" class="btn btn-xs btn-success btn-icon btn-rounded bt_edita_observacion"><i class="icon-pencil"></i></button>
											<button type="button" id="<?= $id ?>" class="btn btn-xs btn-warning btn-icon btn-rounded bt_elimina_observacion"><i class="icon-trash"></i></button>
										</td>
									</tr>
									<!-- MODAL EDITA OBSERVACIONES -->
									<div id="editaObservacion-<?= $id ?>" class="modal fade">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header bg-slate-600">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">EDITA OBSERVACION</h6>
												</div>
												<div class="modal-body">
													<form id="frm_observaciones" class="validar">
														<div class="form-group">
															<div class="row">
																<div class="col-sm-9">
																	<div id="campoFoto">
																		<textarea rows="10" cols="5" name="detalleObservacion-<?= $id ?>" id="detalleObservacion-<?= $id ?>" class="form-control textoMayuscula" placeholder="Ingrese texto de observacion..." onblur="mayusculas(event, this)" autofocus><?= $observacion ?></textarea>
																	</div>
																</div>
																<div class="col-sm-3">
																	<button type="button" id="<?= $id ?>" class="btn btn-success btn-block btn-icon bt_editar_observacion">Actualizar</button>
																	<button type="button" class="btn btn-warning btn-block" data-dismiss="modal">Cerrar</button>
																</div>
															</div>
														</div>
													</form>
												</div>
											</div>
										</div>
									</div>
									<?php $i++; } ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php } ?>

				<!-- INFORMACION DE ENTREGA DE TARJETA DE SOCIO -->
				<?php if($eCardSocio=="SI"){ ?>
					<div class="panel">
						<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">REGISTRO DE ENTREGA DE TARJETA DE SOCIO</h6></div>
						<div class="table-responsive">
							<table class="table tabla-info table-bordered table-hover">
								<thead>
									<tr class="success">
										<th class="textoNegrita text-center">#</th>
										<th class="textoNegrita text-left">DETALLE</th>
										<th class="textoNegrita text-center">FECHA</th>
										<th class="textoNegrita text-center">HORA</th>
										<th class="textoNegrita text-center">USUARIO</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$sql="SELECT proceso, fecha, hora, usuario FROM sm_tarjeta_socio WHERE codigoSocio='$codigoSocio' ORDER BY id DESC";
										$rs=mysqli_query($conexion,$sql);
										$i=1;
										while($n=mysqli_fetch_array($rs)){
											$proceso =$n['proceso'];
											$fecha   =$n['fecha'];
											$hora    =$n['hora'];
											$usuario =$n['usuario'];
									?>
									<tr>
										<td class="text-center"><?= ceros($i,2) ?></td>
										<td class="text-left"><?= texto($proceso) ?></td>
										<td class="text-center textoMayuscula"><?= infoFecha($fecha,'normal') ?></td>
										<td class="text-center"><?= horaCorta($hora) ?></td>
										<td class="text-center"><?= texto(datoUsuario($usuario,'nombre')) ?></td>
									</tr>
									<?php $i++; } ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php } ?>
			</div>
			<div class="col-lg-3">
				<?php if($cuentas>1){ ?>
					<script type="text/javascript">
						$(document).ready(function(){
							///////////////////////////////////////////////////
							/// CORRIGE CUENTA CON ERROR
							///////////////////////////////////////////////////
							$("button#cuentaMover").click(function(){
								var ruta   ="<?= $ruta ?>";
								var cuenta_01="<?= $codigoSocio ?>";
								var cuenta_02="<?= $segundaCuenta ?>";
								var operacion ='MOVER_DATOS_CUENTA_SOCIO';
								var datos='cuenta_01='+cuenta_01+'&cuenta_02='+cuenta_02+'&operacion='+operacion;
								$.ajax({
									type: 'POST',
									url: ruta+'php/mantenimiento-socios.php',
									data: datos,
									dataType:'json',
									beforeSend: function(){
										$('#procesarCuenta').html('<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>ACTUALIZANDO DATOS DE CUENTA</div></div></div>').fadeIn(1000).delay(1000);
									},
									success:function(respuesta){
										if(respuesta.mensaje=="DATOS_SOCIOS_MOVIDOS"){
											new PNotify({title: 'ACTUALIZADO', text: 'Los datos de socio fueron actualizados.', addclass: 'bg-success'});
											window.location.replace('detalles-socio.php?codigoSocio='+cuenta_02+'&opcion=detalles');
										}
										if(respuesta.mensaje=="ERROR_DATOS_SOCIOS_MOVIDOS"){
											new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
										}
									}
								});
							});

							///////////////////////////////////////////////////
							/// ELIMINA CUENTA CON ERROR
							///////////////////////////////////////////////////
							$("button#cuentaElimina").click(function(){
								var ruta      ="<?= $ruta ?>";
								var cuenta_01 ="<?= $codigoSocio ?>";
								var cuenta_02 ="<?= $segundaCuenta ?>";
								var operacion ='ELIMINA_CUENTA_SOCIO';
								var datos='codigoSocio='+cuenta_01+'&operacion='+operacion;
								$.ajax({
									type: 'POST',
									url: ruta+'php/mantenimiento-socios.php',
									data: datos,
									dataType:'json',
									beforeSend: function(){
										$('#procesarCuenta').html('<br><div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>ELIMINANDO CUENTA</div></div></div>').fadeIn(1000).delay(1000);
									},
									success:function(respuesta){
										if(respuesta.mensaje=="CUENTA_ELIMINADA"){
											new PNotify({title: 'ELIMIMADO', text: 'Cuenta de socio eliminada.', addclass: 'bg-success'});
											window.location.replace('detalles-socio.php?codigoSocio='+cuenta_02+'&opcion=detalles');
										}
										if(respuesta.mensaje=="ERROR_CUENTA_ELIMINADA"){
											new PNotify({title: 'ERROR', text: 'Ocurrio un error intentelo nuevamente.', addclass: 'bg-warning'});
										}
									}
								});
							});
						});
					</script>

					<button type="button" data-toggle="modal" data-target="#corregirCuenta" class="btn btn-lg btn-danger btn-block mb-10 text-bold">ERROR EN CUENTA</button>
					
					<!-- MODAL CAMBIO DE FOTO -->
					<div id="corregirCuenta" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-slate-600">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">CORREGIR CUENTA &rarr; <?= $nombre.' '.$apPaterno ?></h6>
								</div>
								<div class="modal-body">
									<div id="procesarCuenta">
										<form id="formCorregirCuenta">
											<p class="text-center">MOVER LOS DATOS ECONOMICOS DE <span class="text-danger text-strong"><?= $codigoSocio ?></span> A LA CUENTA <span class="text-danger text-strong"><?= $segundaCuenta ?></span>.</p>
											<hr>
											<div class="form-group text-center">
												<button type="button" id="cuentaMover" class="btn btn-success">MOVER DATOS</button>
												<button type="button" id="cuentaElimina" class="btn btn-danger">ELIMINAR SOCIO</button>
												<button type="button" class="btn btn-warning" data-dismiss="modal">CERRAR</button>
											</div>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>

				<!-- FOTO DE SOCIO -->
				<div class="thumbnail">
					<div class="thumb thumb-slide">
						<img src="<?= $fotoSocio ?>" alt="">
						<div class="caption">
							<span>
								<a href="#cambiarFoto" data-toggle="modal" class="btn bg-success-400 btn-icon btn-xs"><i class="fa fa-pencil-square-o"></i></a>
								<a href="javascript:;" id="bt_foto_socio" class="btn bg-danger-400 btn-icon btn-xs"><i class="fa fa-trash"></i></a>
							</span>
						</div>
					</div>
					<div class="codigo-barra text-center mt-5 mb-5">
						<?php if($existe_cb=="SI"){ ?>
							<img src="<?= $rutaCB.$fileCB ?>">
						<?php }else{ ?>
							<!-- <img src="../php/codigo-barra.php?text=<?= $codigoSocio ?>&filepath=<?= $urlSocios ?>/assets/images/codigo-barra/<?= $codigoSocio ?>.png"> -->
							<img src="../php/codigo-barra.php?text=<?= $codigoSocio ?>">
							<script type="text/javascript">
								//$(document).ready(function(){ location.reload(); });
							</script>
						<?php } ?>
					</div>
					<!-- MODAL CAMBIO DE FOTO -->
					<div id="cambiarFoto" class="modal fade">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header bg-slate-600">
									<button type="button" class="close" data-dismiss="modal">&times;</button>
									<h6 class="modal-title">CAMBIO DE FOTO &rarr; <?= $nombre.' '.$apPaterno ?></h6>
								</div>
								<div class="modal-body">
									<form id="formCambioFoto" class="validar" method="post" enctype="multipart/form-data">
										<div class="form-group">
											<div class="row">
												<div class="col-sm-9">
													<div id="campoFoto">
														<input type="file" name="fotoSocio" id="fotoSocio" class="inputSubir" required="required">
													</div>
													<div id="procensadoSubida">
													</div>
												</div>
												<div class="col-sm-3">
													<button type="button" id="subirFoto" class="btn btn-success btn-icon"><i class=" icon-floppy-disk"></i></button>
													<button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>

				<?php if($totalDeudas>0){ ?>
					<div class="panel panel-info">
						<div class="panel-heading"><h6 class="panel-title textoNegrita">DEUDAS DE SOCIO</h6></div>
						<div class="list-group list-group-borderless no-padding-top textoMayuscula">
							<?php if($total_ASA_saldo>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=pagos_asambleas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - asambleas <span class="pull-right text-danger textoNegrita"><?= $info_total_ASA_saldo ?></span></a><?php } ?>
							<?php if($total_FAE_saldo>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=pagos_faenas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - faenas <span class="pull-right text-danger textoNegrita"><?= $info_total_FAE_saldo ?></span></a><?php } ?>
							<?php if($total_CUO_saldo>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=pagos_cuotas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - cuotas <span class="pull-right text-danger textoNegrita"><?= $info_total_CUO_saldo ?></span></a><?php } ?>
							<div class="list-group-item alpha-info" style="border-top: 2px solid #AAA;"><i class="icon-chevron-right"></i> <strong>Total deudas <span class="pull-right text-danger"><?= $infoTotalDeudas ?></span></strong></div>
						</div>
					</div>
				<?php } ?>

				<?php if($totalExoneradoSocio>0){ ?>
					<div class="panel panel-info">
						<div class="panel-heading"><h6 class="panel-title textoNegrita">DEUDAS EXONERADAS</h6></div>
						<div class="list-group list-group-borderless no-padding-top textoMayuscula">
							<?php if($totalExoneradoSocioASA>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_asambleas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - asambleas <span class="pull-right text-danger textoNegrita"><?= $infoTotalExoneradoSocioASA ?></span></a><?php } ?>
							<?php if($totalExoneradoSocioFAE>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_faenas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - faenas <span class="pull-right text-danger textoNegrita"><?= $infoTotalExoneradoSocioFAE ?></span></a><?php } ?>
							<?php if($totalExoneradoSocioCUO>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_cuotas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - cuotas <span class="pull-right text-danger textoNegrita"><?= $infoTotalExoneradoSocioCUO ?></span></a><?php } ?>
							<div class="list-group-item alpha-info" style="border-top: 2px solid #AAA;"><i class="icon-chevron-right"></i> <strong>Total deudas exoneradas <span class="pull-right text-danger"><?= $infoTotalExoneradoSocio ?></span></strong></div>
						</div>
					</div>
				<?php } ?>

				<?php if($totalPagosExoneradoSocio>0){ ?>
					<div class="panel panel-info">
						<div class="panel-heading"><h6 class="panel-title textoNegrita">PAGOS DEUDAS EXONERADAS</h6></div>
						<div class="list-group list-group-borderless no-padding-top textoMayuscula">
							<?php if($totalPagosExoneradoSocioASA>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_asambleas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - asambleas <span class="pull-right text-danger textoNegrita"><?= $infoTotalPagosExoneradoSocioASA ?></span></a><?php } ?>
							<?php if($totalPagosExoneradoSocioFAE>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_faenas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - faenas <span class="pull-right text-danger textoNegrita"><?= $infoTotalPagosExoneradoSocioFAE ?></span></a><?php } ?>
							<?php if($totalPagosExoneradoSocioCUO>0){ ?><a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=historial_exoneraciones_cuotas" class="list-group-item"><i class="icon-chevron-right"></i> Deudas - cuotas <span class="pull-right text-danger textoNegrita"><?= $infoTotalPagosExoneradoSocioCUO ?></span></a><?php } ?>
							<div class="list-group-item alpha-info" style="border-top: 2px solid #AAA;"><i class="icon-chevron-right"></i> <strong>Total pagos Exonerados <span class="pull-right text-danger"><?= $infoTotalPagosExoneradoSocio ?></span></strong></div>
						</div>
					</div>
				<?php } ?>

				<div class="panel panel-flat">
					<div class="list-group list-group-borderless no-padding-top textoMayuscula">
						<a href="#observaciones" data-toggle="modal" class="list-group-item"><i class="icon-comments"></i> Agregar observacion</a>
						<a href="#editardatos" data-toggle="modal" class="list-group-item"><i class="icon-pencil5"></i> Modificar datos del socio</a>
						<a href="../documentos/detalle-socio.php?codigoSocio=<?= $codigoSocio ?>" class="list-group-item"><i class="icon-printer2"></i> Reporte total deudas</a>
						<?php if($eCardSocio=="NO"){ ?>
							<a href="javascript:;" id="entregar_tarjeta" class="list-group-item"><i class="icon-credit-card2"></i> Entregar tarjeta de socio</a>
						<?php }else{ ?>
							<a href="javascript:;" id="entregar_duplicado_tarjeta" class="list-group-item"><i class="icon-credit-card2"></i> Entregar duplicado tarjeta</a>
						<?php } ?>
						<a href="detalles-socio.php?codigoSocio=<?= $codigoSocio ?>&opcion=operaciones" class="list-group-item"><i class="icon-list3"></i> Bitacora de operaciones</a>
						<?php if($totalDeudas>0){ ?><a href="javascript: void(0)" id="bt_exonerar_deudas" class="list-group-item"><strong class="text-danger"><i class="icon-coin-dollar"></i>&nbsp;&nbsp; Exonerar deudas</strong></a><?php } ?>
					</div>
				</div>

				<div class="panel panel-flat">
					<div class="list-group list-group-borderless no-padding-top textoMayuscula">
						<a href="javascript:;" class="list-group-item" id="bt_eliminar_socio"><i class="icon-trash"></i> Eliminar Socio</a>
					</div>
				</div>

				<!--////////////////////////////////////////////////////////////////////////////////
				MODAL - EXONERAR DEUDAS
				////////////////////////////////////////////////////////////////////////////////-->
				<div id="modal_exonerar_deudas" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div id="box_modal_exonerar_deudas"></div>
						</div>
					</div>
				</div>

				<!-- MODAL AGREGA OBSERVACIONES -->
				<div id="observaciones" class="modal fade">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header bg-slate-600">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h6 class="modal-title">AGREGAR OBSERVACION</h6>
							</div>
							<div class="modal-body">
								<form id="frm_observaciones" class="validar">
									<div class="form-group">
										<div class="row">
											<div class="col-sm-9">
												<div id="campoFoto">
													<textarea rows="10" cols="5" name="observacion" id="observacion" class="form-control textoMayuscula" placeholder="Ingrese texto de observacion..." onblur="mayusculas(event, this)" autofocus></textarea>
												</div>
											</div>
											<div class="col-sm-3">
												<button type="button" id="bt_salvar_observacion" class="btn btn-success btn-block btn-icon">Almacenar</button>
												<button type="button" class="btn btn-warning btn-block" data-dismiss="modal">Cerrar</button>
											</div>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- MODAL MODIFICAR DATOS DEL SOCIO -->
				<div id="editardatos" class="modal fade">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div class="modal-header bg-slate-600">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h6 class="modal-title">MODIFICAR DATOS DEL SOCIO</h6>
							</div>
							<div class="modal-body">
								<form id="form_datos_socios" class="form_valida_datos_socio">
									<input type="hidden" name="codigoSocio" value="<?= $codigoSocio ?>">
									<input type="hidden" name="dni" value="<?= $dni ?>">
									<input type="hidden" name="operacion" value="EDITA_DATOS_SOCIO">
									
									<div class="form-group">
										<div class="row">
											<div class="col-sm-4">
												<input type="text" disabled name="nombre" id="nombre" required="required" value="<?= texto($nombre) ?>" class="form-control textoMayuscula" onblur="mayusculas(event, this)">
												<span class="label label-block bg-grey-300 text-left">Nombre(s)</span>
											</div>
											<div class="col-sm-3">
												<input type="text" disabled name="apPaterno" id="apPaterno" required="required" value="<?= texto($apPaterno) ?>" class="form-control left textoMayuscula" onblur="mayusculas(event, this)">
												<span class="label label-block bg-grey-300 text-left">Apellido Paterno</span>
											</div>
											<div class="col-sm-3">
												<input type="text" disabled name="apMaterno" id="apMaterno" required="required" value="<?= texto($apMaterno) ?>" class="form-control left textoMayuscula" onblur="mayusculas(event, this)">
												<span class="label label-block bg-grey-300 text-left">Apellido Materno</span>
											</div>
											<div class="col-sm-2">
												<select name="genero" id="genero" required="required" class="form-control">
													<option value="" selected>SELECCIONE</option>
													<?php
														$sql="SELECT * FROM sm_a_genero";
														$rs=mysqli_query($conexion,$sql);
														while($datos=mysqli_fetch_array($rs)){
															if($genero==$datos[0]){
																echo '<option value="'.$datos[0].'" selected>'.$datos[1].'</option>';
															}else{
																echo '<option value="'.$datos[0].'">'.$datos[1].'</option>';
															}
														}
													?>
												</select>
												<span class="label label-block bg-grey-300 text-left">Genero</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-2">
												<input type="text" disabled name="nacionalidad" id="nacionalidad" required="required" value="<?= texto($nacionalidad) ?>" class="form-control textoMayuscula" onblur="mayusculas(event, this)">
												<span class="label label-block bg-grey-300 text-left">Nacionalidad</span>
											</div>
											<div class="col-sm-2">
												<input type="text" disabled name="fechaNacimiento" id="fechaNacimiento" required="required" value="<?php if($edad>120){ echo ""; }else{ echo infofecha($fechaNacimiento,'resultados'); } ?>" class="form-control fechas">

												<span class="label label-block bg-grey-300 text-left">F. Nacimiento (<?= $edad ?>A)</span>
											</div>
											<div class="col-sm-2">
												<select name="estadoCivil" id="estadoCivil" required="required" class="form-control">
													<option value="" selected>SELECCIONE</option>
													<?php
														$sql="SELECT * FROM sm_a_civil";
														$rs=mysqli_query($conexion,$sql);
														while($datos=mysqli_fetch_array($rs)){
															if($estadoCivil==$datos[0]){
																echo '<option value="'.$datos[0].'" selected>'.$datos[1].'</option>';
															}else{
																echo '<option value="'.$datos[0].'">'.$datos[1].'</option>';
															}
														}
													?>
												</select>
												<span class="label label-block bg-grey-300 text-left">Estado Civil:</span>
											</div>
											<div class="col-sm-6">
												<input type="text" name="direccion" id="direccion" required="required" value="<?= texto($direccion) ?>" class="form-control left textoMayuscula" onblur="mayusculas(event, this)">
												<span class="label label-block bg-grey-300 text-left">Dirección</span>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<div class="col-sm-2">
												<select disabled name="departamento" id="departamento" required="required" class="form-control">
													<option value="" selected>SELECCIONE</option>
													<?php
														$sql="SELECT * FROM sm_a_departamento";
														$rs=mysqli_query($conexion,$sql);
														while($datos=mysqli_fetch_array($rs)){
															if($departamento==$datos[0]){
																echo '<option value="'.$datos[0].'" selected>'.$datos[1].'</option>';
															}else{
																echo '<option value="'.$datos[0].'">'.$datos[1].'</option>';
															}
														}
													?>
												</select>
												<span class="label label-block bg-grey-300 text-left">Pepartamento</span>
											</div>
											<div class="col-sm-3">
												<input type="text" disabled name="provincia" id="provincia" required="required" value="<?= texto($provincia) ?>" class="form-control left textoMayuscula" onblur="mayusculas(event, this)">
												<span class="label label-block bg-grey-300 text-left">Provincia</span>
											</div>
											<div class="col-sm-3">
												<input type="text" disabled name="distrito" id="distrito" required="required" value="<?= texto($distrito) ?>" class="form-control left textoMayuscula" onblur="mayusculas(event, this)">
												<span class="label label-block bg-grey-300 text-left">Distrito</span>
											</div>
											<div class="col-sm-2">
												<input type="text" name="telefono" id="telefono" value="<?= $telefono ?>" class="form-control left">
												<span class="label label-block bg-grey-300 text-left">Telefono</span>
											</div>
											<div class="col-sm-2">
												<input type="text" name="celular" id="celular" required="required" value="<?= $celular ?>" class="form-control left">
												<span class="label label-block bg-grey-300 text-left">Celular</span>
											</div>
										</div>
									</div>
								</form>
							</div>

							<div class="modal-footer bg-modal text-center pt-20">
								<button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
								<button type="button" id="bt_edita_socio" class="btn btn-success">Guardar cambios</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php if($opcion=="asistencias_cuotas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<a href="../documentos/info-asistencias-pagos.php?codigoSocio=<?= $codigoSocio ?>" class="btn btn-xs btn-danger btn-labeled mt-15"><b><i class="icon-printer2"></i></b> IMPRIMIR INFORME</a>
		</div>

		<div class="row">
			<div class="col-md-3">
				<div class="panel">
					<div class="panel-heading bg-brown-600">
						<h6 class="panel-title text-bold">ASAMBLEAS</h6>
					</div>
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL EN ASAMBLEA</span> <span class="pull-right textoNegrita text-danger"><?= $info_total_ASA_DEU_PAG ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS EFECTIVOS</span> <span class="pull-right textoNegrita text-danger"><?= $info_pagos_ASA_efectivos ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS PROGRAMADOS</span> <span class="pull-right textoNegrita text-danger"><?= $info_pagos_ASA_programados ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL CONDONADAS <small>(JUSTIFICACIONES)</small></span> <span class="pull-right text-danger"><?= $info_total_ASA_justificados ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS PENDIENTES</span> <span class="pull-right textoNegrita text-danger"><?= $info_total_ASA_saldo ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL MONTOS EXONERADOS</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalExoneradoSocioASA ?></span></li>
					</ul>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel">
					<div class="panel-heading bg-brown-600">
						<h6 class="panel-title text-bold">FAENAS</h6>
					</div>
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL EN CUOTAS</span> <span class="pull-right text-danger"><?= $info_total_FAE_DEU_PAG ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS EFECTIVOS</span> <span class="pull-right text-danger"><?= $info_pagos_FAE_efectivos ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS PROGRAMADOS</span> <span class="pull-right text-danger"><?= $info_pagos_FAE_programados ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL CONDONADAS <small>(JUSTIFICACIONES)</small></span> <span class="pull-right text-danger"><?= $info_total_FAE_justificados ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS PENDIENTES</span> <span class="pull-right textoNegrita text-danger"><?= $info_total_FAE_saldo ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL MONTOS EXONERADOS</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalExoneradoSocioFAE ?></span></li>
					</ul>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel">
					<div class="panel-heading bg-brown-600">
						<h6 class="panel-title text-bold">CUOTAS</h6>
					</div>
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL EN CUOTAS</span> <span class="pull-right textoNegrita text-danger"><?= $info_total_CUO_DEU_PAG ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS EFECTIVOS</span> <span class="pull-right textoNegrita text-danger"><?= $info_pagos_CUO_efectivos ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS PROGRAMADOS</span> <span class="pull-right textoNegrita text-danger"><?= $info_pagos_CUO_programados ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL CONDONADAS <small>(JUSTIFICACIONES)</small></span> <span class="pull-right textoNegrita text-danger"><?= $info_total_CUO_justificados ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL PAGOS PENDIENTES</span> <span class="pull-right textoNegrita text-danger"><?= $info_total_CUO_saldo ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL MONTOS EXONERADOS</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalExoneradoSocioCUO ?></span></li>
					</ul>
				</div>
			</div>
			<div class="col-md-3">
				<div class="panel">
					<div class="panel-heading bg-brown-600">
						<h6 class="panel-title text-bold">EXONERACIONES PAGADAS</h6>
					</div>
					<ul class="list-group">
						<li class="list-group-item"><span class="textoNegrita">TOTAL EXONERADOS PAGADOS EN ASAMBLEAS</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalPagosExoneradoSocioASA ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL EXONERADOS PAGADOS EN FAENAS</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalPagosExoneradoSocioFAE ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL EXONERADOS PAGADOS EN CUOTAS</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalPagosExoneradoSocioCUO ?></span></li>
						<li class="list-group-item"><span class="textoNegrita">TOTAL DEUDAS EXONERADAS PAGADAS</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalPagosExoneradoSocio ?></span></li>
						<li class="list-group-item bg-lista-info-beige"><span class="textoNegrita">TOTAL DEUDA GENERAL</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalGeneralDeudas ?></span></li>
						<li class="list-group-item bg-lista-info-beige"><span class="textoNegrita">TOTAL MONTOS EXONERADOS</span> <span class="pull-right textoNegrita text-danger"><?= $infoTotalExoneradoSocio ?></span></li>
					</ul>
				</div>
			</div>
		</div>

		<div class="panel-group panel-group-control panel-group-control-right content-group-lg" id="accordion-control-right">
			<div class="panel panel-success">
				<div class="panel-heading"><h6 class="panel-title textoNegrita"><a class="collapsed" data-toggle="collapse" data-parent="#accordion-control-right" href="#accordion-control-right-group1">LISTA DE ASAMBLEAS &rarr; <?= ceros($asambleas,2) ?></a></h6></div>
				<div id="accordion-control-right-group1" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if($asambleas>0){ $infoActividad='ASAMBLEA'; ?>
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
								});
							</script>
							<div class="table-responsive">
								<table class="table tabla table-bordered table-hover tabla-lista-ASA">
									<thead>
										<tr class="success">
											<th class="textoNegrita text-center">#</th>
											<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
											<th class="textoNegrita text-center">FECHA</th>
											<th class="textoNegrita text-center">RAZON</th>
											<th class="textoNegrita text-center">MULTA</th>
											<th class="textoNegrita text-center">LOTES</th>
											<th class="textoNegrita text-center">TOTAL</th>
											<th class="textoNegrita text-center">ESTADO</th>
											<th class="text-center">%</th>
											<th class="text-center">EXONERADO</th>
											<th class="text-center">PAGADO</th>
											<th class="textoNegrita text-center"><i class="fa fa-calendar"></i></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND codigoSocio='$codigoSocio' ORDER BY id DESC";
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

												$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
												$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
												$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');

												if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
												if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
												if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }
												
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

												if($estadoPago=="EX"){
													$estado       ='<span class="label label-info">EXONERADO</span>';
												}
										?>
										<tr>
											<td class="text-center"><?= ceros($i,2) ?></td>
											<td class="text-left"><?= '<a href="detalle-actividad.php?codigoActividad='.$codigoActividad.'&tipoActividad='.$tipoActividad.'&opcion=detalles&temaActividad='.$temaActividad.'" target="_blank">'.$temaActividad.'</a>' ?></td>
											<td class="text-center textoMayuscula"><?= $fechaActividad ?></td>
											<td class="text-center"><?= $asistencia ?></td>
											<td class="text-center"><?= $infoMulta ?></td>
											<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
											<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
											<td class="text-center"><?= $estado ?></td>
											<td class="text-center"><?= $infoPorcentajeEXO ?></td>
											<td class="text-right text-danger textoNegrita"><?= $infoExonerado ?></td>
											<td class="text-right textoNegrita"><?= $infoTotalPago ?></td>
											<td class="text-center"><?= $infoProgramado ?></td>
										</tr>
										<?php $i++; } ?>
									</tbody>
								</table>
							</div>
						<?php }else{ echo cajaAlerta('SIN DEUDAS PENDIENTES EN ASAMBLEAS','text-center','No se ha encontrado, ningún registro en el sistema con deudas pendientes en asambleas para el socio seleccionado...','text-center','bg-warning'); } ?>
					</div>
				</div>
			</div>

			<div class="panel panel-success">
				<div class="panel-heading"><h6 class="panel-title textoNegrita"><a class="collapsed" data-toggle="collapse" data-parent="#accordion-control-right" href="#accordion-control-right-group2">LISTA FAENAS &rarr; <?= ceros($faenas,2) ?></a></h6></div>
				<div id="accordion-control-right-group2" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if($faenas>0){ $infoActividad='FAENA'; ?>
							<script type="text/javascript">
								$(document).ready(function(){
									///////////////////////////////////////////////////
									/// CONFIGURACION DE TABLAS
									///////////////////////////////////////////////////
									$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [  ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
									var lastIdx = null;
									var table = $('.tabla-lista-FAE').DataTable({ 'pageLength': 20 });
									$('.tabla-lista-FAE tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
									$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
									$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
								});
							</script>
							<div class="table-responsive">
								<table class="table tabla table-bordered table-hover tabla-lista-FAE">
									<thead>
										<tr class="success">
											<th class="textoNegrita text-center">#</th>
											<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
											<th class="textoNegrita text-center">FECHA</th>
											<th class="textoNegrita text-center">RAZON</th>
											<th class="textoNegrita text-center">MULTA</th>
											<th class="textoNegrita text-center">LOTES</th>
											<th class="textoNegrita text-center">TOTAL</th>
											<th class="textoNegrita text-center">ESTADO</th>
											<th class="text-center">%</th>
											<th class="text-center">EXONERADO</th>
											<th class="text-center">PAGADO</th>
											<th class="textoNegrita text-center"><i class="fa fa-calendar"></i></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND codigoSocio='$codigoSocio' ORDER BY id DESC";
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

												$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
												$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
												$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');

												if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
												if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
												if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }
												
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

												if($estadoPago=="EX"){
													$estado       ='<span class="label label-info">EXONERADO</span>';
												}
										?>
										<tr>
											<td class="text-center"><?= ceros($i,2) ?></td>
											<td class="text-left"><?= '<a href="detalle-actividad.php?codigoActividad='.$codigoActividad.'&tipoActividad='.$tipoActividad.'&opcion=detalles&temaActividad='.$temaActividad.'" target="_blank">'.$temaActividad.'</a>' ?></td>
											<td class="text-center textoMayuscula"><?= $fechaActividad ?></td>
											<td class="text-center"><?= $asistencia ?></td>
											<td class="text-center"><?= $infoMulta ?></td>
											<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
											<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
											<td class="text-center"><?= $estado ?></td>
											<td class="text-center"><?= $infoPorcentajeEXO ?></td>
											<td class="text-right text-danger textoNegrita"><?= $infoExonerado ?></td>
											<td class="text-right textoNegrita"><?= $infoTotalPago ?></td>
											<td class="text-center"><?= $infoProgramado ?></td>
										</tr>
										<?php $i++; } ?>
									</tbody>
								</table>
							</div>
						<?php }else{ echo cajaAlerta('SIN DEUDAS PENDIENTES EN ASAMBLEAS','text-center','No se ha encontrado, ningún registro en el sistema con deudas pendientes en faenas para el socio seleccionado...','text-center','bg-warning'); } ?>
					</div>
				</div>
			</div>

			<div class="panel panel-success">
				<div class="panel-heading"><h6 class="panel-title textoNegrita"><a class="collapsed" data-toggle="collapse" data-parent="#accordion-control-right" href="#accordion-control-right-group3">LISTA CUOTAS &rarr; <?= ceros($cuotas,2) ?></a></h6></div>
				<div id="accordion-control-right-group3" class="panel-collapse collapse">
					<div class="panel-body">
						<?php if($cuotas>0){ $infoActividad='CUOTA'; ?>
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
								});
							</script>

							<div class="table-responsive">
								<table class="table tabla table-bordered table-hover tabla-lista-CUO">
									<thead>
										<tr class="success">
											<th class="text-center">#</th>
											<th class="text-left">CONCEPTO DE CUOTA</th>
											<th class="text-center">LOTES</th>
											<th class="text-center">CUOTA</th>
											<th class="text-center">TOTAL</th>
											<th class="text-center">ESTADO</th>
											<th class="text-center">%</th>
											<th class="text-center">EXONERADO</th>
											<th class="text-center">PAGADO</th>
											<th class="textoNegrita text-center"><i class="fa fa-calendar"></i></th>
										</tr>
									</thead>
									<tbody>
										<?php
											$sql="SELECT codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' ORDER BY id DESC";
											$rs=mysqli_query($conexion,$sql);
											$i=1;
											while($n=mysqli_fetch_array($rs)){
												$tipoActividad  ='CUO';
												$codigoCuota    =$n['codigoCuota'];
												$lotes          =$n['lotes'];
												$montoCuota     =$n['montoCuota'];
												$montoPago      =$n['montoPago'];
												$estadoPago     =$n['estadoPago'];
												$dni            =infoSocios($codigoSocio,'dni');
												$conceptoCuota  =texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota'));
												$conceptoPago   =$tipoActividad;
												$programado     =conceptoProgramado($codigoSocio,$codigoCuota);
												$porcentajePago =porcentajePago($codigoSocio,$codigoCuota,$montoPago);
												$codigoActividad = $codigoCuota;

												$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
												$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
												$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');

												if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
												if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
												if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }

												if($programado>=2){
													$infoProgramado='<span class="label bg-brown">'.ceros($programado,2). ' FECHAS</span>';
												}else{
													$infoProgramado='';
												}

												if($estadoPago=="NP" and $programado==0){
													$estado       ='<span class="label label-danger">PENDIENTE</span>';
												}

												if($estadoPago=="MP" and $programado>=2){
													$estado       ='<span class="label bg-violet-800">AL '.$porcentajePago.'%</span>';
												}

												if($estadoPago=="SP" and $programado==0){
													$estado       ='<span class="label label-success">PAGADO</span>';
												}

												if($estadoPago=="SP" and $programado>=2){
													$estado       ='<span class="label label-success">PAGADO</span>';
												}

												if($estadoPago=="EX"){
													$estado       ='<span class="label label-info">EXONERADO</span>';
												}

												$totalPagado    =infoPagoFechas($codigoSocio,$codigoCuota,'totalFechasPagadas');
										?>
										<tr>
											<td class="text-center"><?= ceros($i,2) ?></td>
											<td class="text-left"><a href="detalle-cuota.php?codigoCuota=<?= $codigoCuota ?>&opcion=detalles&conceptoCuota=<?= $conceptoCuota ?>" target="_blank"><?= $conceptoCuota ?></a></td>
											<td class="text-center"><span class="label label-default textoNegrita"><?= ceros($lotes,2) ?> LOTES</span></td>
											<td class="text-right"><?= 'S/. '.moneda($montoCuota) ?></td>
											<td class="text-right text-danger textoNegrita"><?= 'S/. '.moneda($montoPago) ?></td>
											<td class="text-center"><?= $estado ?></td>
											<td class="text-center"><?= $infoPorcentajeEXO ?></td>
											<td class="text-right text-danger textoNegrita"><?= $infoExonerado ?></td>
											<td class="text-right textoNegrita"><?= $infoTotalPago ?></td>
											<td class="text-center"><?= $infoProgramado ?></td>
										</tr>
										<?php $i++; } ?>
									</tbody>
								</table>
							</div>
						<?php }else{ echo cajaAlerta('SIN DEUDAS PENDIENTES EN CUOTAS','text-center','No se ha encontrado, ningún registro en el sistema con deudas pendientes en cuotas para el socio seleccionado...','text-center','bg-warning'); } ?>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
	
	<?php if($opcion=="pagos_asambleas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<?php if($deudasAsambleas>0){ $infoActividad   ="ASAMBLEA"; ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-deuda-cuotas').DataTable({ 'pageLength': 20 });
					$('.tabla-deuda-cuotas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>

			<div class="panel">
				<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">DEUDAS ASAMBLEAS &rarr; <?= $info_total_ASA_saldo ?></h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO <?= $infoActividad ?></th>
									<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
									<th class="textoNegrita text-center">RAZON</th>
									<th class="textoNegrita text-center">MULTA</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-center">TOTAL</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="textoNegrita text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND (estadoPago='NP' OR estadoPago='MP') AND codigoSocio='$codigoSocio' ORDER BY id DESC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$tipoActividad   =$n['tipoActividad'];
										$codigoActividad =$n['codigoActividad'];
										$temaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
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

										if($asistio=="NO"){
											$rotActMay="POR INASISTENCIA A ".$infoActividad;
											$asistencia="FALTO";
											$razon="FALTA";
											$infoMulta='S/. '.moneda($multaFalta);

										}
										if($asistio=="SI" and $retraso>0.15){
											$rotActMay="POR TARDANZA A ".$infoActividad;
											$asistencia="TARDANZA";
											$razon="TARDE";
											$infoMulta='S/. '.moneda($multaTarde);
										}
										
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
									<td class="text-center"><?= $codigoActividad ?></td>
									<td class="text-left"><?= texto($temaActividad) ?></td>
									<td class="text-center"><?= $asistencia ?></td>
									<td class="text-center"><?= $infoMulta ?></td>
									<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center"><a href="#pagarMonto-<?= $codigoActividad ?>" data-toggle="modal" class="btn btn-xs btn-icon bg-brown"><i class=" icon-coin-dollar"></i></a></td>
									<!-- MODAL PAGO DE MULTA -->
									<script type="text/javascript">
										$(document).ready(function(){
											///////////////////////////////////////////////////
											/// MODULO DE PAGO
											///////////////////////////////////////////////////
											var codigoActividad='<?= $codigoActividad ?>';
											$('#pagarMonto-'+codigoActividad).on('shown.bs.modal', function(){
												var ruta           ='../';
												var opcion         ='modulo_pago_monto';
												var conceptoPago   ='<?= $tipoActividad ?>';
												var codigoConcepto ='<?= $codigoActividad ?>';
												var codigoSocio    ='<?= $codigoSocio ?>';
												var razon          ='<?= $razon ?>';
												var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&razon='+razon;
												$('#modulo_pago-'+codigoActividad).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
												$("#modulo_pago-"+codigoActividad).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
											});
										});
									</script>
									<div id="pagarMonto-<?= $codigoActividad ?>" class="modal fade">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header bg-slate-600">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">PAGO DE MULTA <?= $rotActMay ?> &rarr; <?= $infoNombre ?></h6>
												</div>
												<div class="modal-body">
													<div id="modulo_pago-<?= $codigoActividad ?>"></div>
													<div id="modulo_fechas_pago-<?= $codigoActividad ?>"></div>
												</div>
											</div>
										</div>
									</div>
								</tr>
								<?php $i++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php }else{ echo cajaAlerta('SIN DEUDAS PENDIENTES EN ASAMBLEAS','text-center','No se ha encontrado, ningún registro en el sistema con deudas pendientes en asambleas para el socio seleccionado...','text-center','bg-warning'); } ?>
	<?php } ?>

	<?php if($opcion=="historial_pagos_asambleas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<?php if($pagosAsambleas>0){ $infoActividad   ="ASAMBLEA"; ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-deuda-cuotas').DataTable({ 'pageLength': 20 });
					$('.tabla-deuda-cuotas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>

			<div class="panel">
				<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">LISTA DE PAGOS &rarr; ASAMBLEAS</h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO <?= $infoActividad ?></th>
									<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
									<th class="textoNegrita text-center">RAZON</th>
									<th class="textoNegrita text-center">MULTA</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-center">TOTAL</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="textoNegrita text-center">%</th>
									<th class="textoNegrita text-center">EXONERADO</th>
									<th class="textoNegrita text-center">PAGADO</th>
									<th class="textoNegrita text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND (estadoPago='SP' OR estadoPago='EX') AND codigoSocio='$codigoSocio' ORDER BY id DESC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$tipoActividad   =$n['tipoActividad'];
										$codigoActividad =$n['codigoActividad'];
										$temaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
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

										$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
										$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
										$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
										
										if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
										if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
										if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }

										if($programado>=2){
											$verificaPago   =verificaPagosProgramados($codigoActividad,$codigoSocio,$multa);
											$pagoProgramado ="SI";
										}else{
											$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$multa);
											$pagoProgramado ="NO";
										}

										if($asistio=="NO"){
											$rotActMay  ="POR INASISTENCIA A ".$infoActividad;
											$asistencia ="FALTO";
											$razon      ="FALTA";
											$infoMulta  ='S/. '.moneda($multaFalta);
											$inforazon  ="INASISTENCIA";

										}
										
										if($asistio=="SI" and $retraso>0.15){
											$rotActMay  ="POR TARDANZA A ".$infoActividad;
											$asistencia ="TARDANZA";
											$razon      ="TARDE";
											$infoMulta  ='S/. '.moneda($multaTarde);
											$inforazon  ="TARDANZA";
										}

										if($estadoPago=="SP" and $pagoProgramado=="SI"){ $infoEstadoPago='<span class="label label-success">PAGADO - '.ceros($programado,2).' F</span>'; }
										if($estadoPago=="SP" and $pagoProgramado=="NO"){ $infoEstadoPago='<span class="label label-success">PAGADO</span>'; }
										if($estadoPago=="EX"){
											$infoEstadoPago='<span class="label label-info">EXONERADO</span>';
											$monto=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
											$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$monto);
										}

										if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-info">CAJA</span>'; }
										if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-warning">ERROR</span>'; }
										if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='<span class="label label-success">NO REGISTRADO</span>'; }
								?>
									<tr>
										<td class="text-center"><?= ceros($i,2) ?></td>
										<td class="text-center"><?= $codigoActividad ?></td>
										<td class="text-left"><?= texto($temaActividad) ?></td>
										<td class="text-center"><?= $asistencia ?></td>
										<td class="text-center"><?= $infoMulta ?></td>
										<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
										<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
										<td class="text-center"><?= $infoEstadoPago ?></td>
										<td class="text-center"><?= $infoPorcentajeEXO ?></td>
										<td class="text-right text-danger textoNegrita"><?= $infoExonerado ?></td>
										<td class="text-right textoNegrita"><?= $infoTotalPago ?></td>
										<td class="text-center"><?= $caja ?></td>
										<td class="text-center"><a href="#infoPago-<?= $codigoActividad ?>" data-toggle="modal" class="btn btn-xs btn-icon bg-brown"><i class=" icon-info22"></i></a></td>
										<!-- MODAL INFORMACION PAGO DE MULTA -->
										<?php if($pagoProgramado=="SI"){ ?>
											<script type="text/javascript">
												$(document).ready(function(){
													///////////////////////////////////////////////////
													/// MODULO DE PAGO
													///////////////////////////////////////////////////
													var ruta            ='../';
													var opcion          ='pagar_fechas_programadas';
													var codigoSocio     ='<?= $codigoSocio ?>';
													var conceptoPago    ='<?= $conceptoPago ?>';
													var codigoConcepto  ='<?= $codigoActividad ?>';
													var codigoActividad ='<?= $codigoActividad ?>'
													var datos           ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto;
													$('#infoPago-'+codigoActividad).on('shown.bs.modal', function(){
														$('#modulo_fechas_pago_'+codigoActividad).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
														$("#modulo_fechas_pago_"+codigoActividad).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
													});
												});
											</script>

											<div id="infoPago-<?= $codigoActividad ?>" class="modal fade">
												<div class="modal-dialog modal-lg">
													<div class="modal-content">
														<div class="modal-header bg-brown">
															<button type="button" class="close" data-dismiss="modal">&times;</button>
															<h6 class="modal-title">INFORMACION DE PAGO</h6>
														</div>

														<div class="modal-body">
															<div class="row mb-20">
																<div class="col-sm-12">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita"><?= $infoActividad ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-5">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $inforazon ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-4">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-3">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">FECHAS DE PAGO</span> <span class="pull-right text-danger"><?= ceros($programado,2) ?></span></li>
																	</ul>
																</div>
															</div>
															<div id="modulo_fechas_pago_<?= $codigoActividad ?>"></div>
														</div>
													</div>
												</div>
											</div>
										<?php } ?>


										<div id="infoPago-<?= $codigoSocio ?>" class="modal fade">
											<div class="modal-dialog modal-lg">
												<div class="modal-content">
													<div class="modal-header bg-brown">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h6 class="modal-title textoNegrita">INFORMACION DE PAGO <?= $pagoProgramado ?></h6>
													</div>
													<div class="modal-body">
														<?php
															$sql="SELECT fechaOperacion, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND  codigoConcepto='$codigoActividad'";
															$infopago=mysqli_query($conexion,$sql);
															$dato=mysqli_fetch_array($infopago);
															$fechaOperacion  =$dato['fechaOperacion'];
															$concepto        =$dato['concepto'];
															$tipoDocumento   =$dato['tipoDocumento'];
															$nroDocumento    =$dato['nroDocumento'];
															$monto           =$dato['monto'];
															$detalleConcepto =$dato['detalleConcepto'];
															$codigoOperacion =$dato['codigoOperacion'];
															$fecha           =$dato['fecha'];
															$hora            =$dato['hora'];
															$usuario         =$dato['usuario'];
														?>
														<div class="row mb-20">
															<div class="col-sm-12">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $rotuloCM ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">DOCUMENTO</span> <span class="pull-right text-danger"><?= infoTipoDOC($tipoDocumento) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">INGRESADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">NRO DOCUMENTO</span> <span class="pull-right text-danger"><?= $nroDocumento ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">CODIGO OPERACION</span> <span class="pull-right text-danger"><?= $codigoOperacion ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">INGRESO</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fecha,'larga').' - '.horaCorta($hora) ?></span></li>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php if($pagoProgramado=="NO"){ ?>
											<div id="infoPago-<?= $codigoActividad ?>" class="modal fade">
												<div class="modal-dialog modal-lg">
													<div class="modal-content">
														<div class="modal-header bg-brown">
															<button type="button" class="close" data-dismiss="modal">&times;</button>
															<h6 class="modal-title textoNegrita">INFORMACION DE PAGO <?= $pagoProgramado ?></h6>
														</div>
														<div class="modal-body">
															<?php
																$sql="SELECT fechaOperacion, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND  codigoConcepto='$codigoActividad'";
																$infopago=mysqli_query($conexion,$sql);
																$dato=mysqli_fetch_array($infopago);
																$fechaOperacion  =$dato['fechaOperacion'];
																$concepto        =$dato['concepto'];
																$tipoDocumento   =$dato['tipoDocumento'];
																$nroDocumento    =$dato['nroDocumento'];
																$monto           =$dato['monto'];
																$detalleConcepto =$dato['detalleConcepto'];
																$codigoOperacion =$dato['codigoOperacion'];
																$fecha           =$dato['fecha'];
																$hora            =$dato['hora'];
																$usuario         =$dato['usuario'];
															?>
															<div class="row mb-20">
																<div class="col-sm-12">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-6">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $inforazon ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">DOCUMENTO</span> <span class="pull-right text-danger"><?= infoTipoDOC($tipoDocumento) ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">INGRESADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-6">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">NRO DOCUMENTO</span> <span class="pull-right text-danger"><?= $nroDocumento ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">CODIGO OPERACION</span> <span class="pull-right text-danger"><?= $codigoOperacion ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">INGRESO</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fecha,'larga').' - '.horaCorta($hora) ?></span></li>
																	</ul>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										<?php } ?>
									</tr>
								<?php $i++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php }else{ echo cajaAlerta('SIN HISTORIAL DE PAGOS EN ASAMBLEAS','text-center','No se ha encontrado, ningún registro en el sistema para esta opción...','text-center','bg-warning'); } ?>
	<?php } ?>

	<?php if($opcion=="historial_exoneraciones_asambleas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CONFIGURACION DE TABLAS
				///////////////////////////////////////////////////
				$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
				var lastIdx = null;
				var table = $('.tabla-deuda-cuotas').DataTable({ 'pageLength': 20 });
				$('.tabla-deuda-cuotas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
				$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
				$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
			});
		</script>

		<div class="panel">
			<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">LISTA DE PAGOS &rarr; ASAMBLEAS</h6></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
						<thead>
							<tr class="success">
								<th class="textoNegrita text-center">#</th>
								<th class="textoNegrita text-center">CODIGO <?= $infoActividad ?></th>
								<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
								<th class="textoNegrita text-center">DEUDA</th>
								<th class="textoNegrita text-center">%</th>
								<th class="textoNegrita text-center">EXONERADO</th>
								<th class="textoNegrita text-center">PAGADO</th>
								<th class="textoNegrita text-center">ESTADO</th>
								<th class="textoNegrita text-center">CAJA</th>
								<th class="text-center"><i class="fa fa-align-justify"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND estadoPago='EX' AND codigoSocio='$codigoSocio' ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$tipoActividad   =$n['tipoActividad'];
									$codigoActividad =$n['codigoActividad'];
									$temaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
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

									$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
									$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
									$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
									$codigoExoneracion=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'codigoExoneracionExoneraciones');

									if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
									if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
									if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }

									if($estadoPago=="SP" and $pagoProgramado=="SI"){ $infoEstadoPago='<span class="label label-success">PAGADO - '.ceros($programado,2).' F</span>'; }
									if($estadoPago=="SP" and $pagoProgramado=="NO"){ $infoEstadoPago='<span class="label label-success">PAGADO</span>'; }
									if($estadoPago=="EX"){
										$infoEstadoPago='<span class="label label-info">EXONERADO</span>';
										$monto=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
										$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$monto);
									}

									if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-info">CAJA</span>'; }
									if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-warning">ERROR</span>'; }
									if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='<span class="label label-success">NO REGISTRADO</span>'; }
							?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoActividad ?></td>
									<td class="text-left"><?= texto($temaActividad) ?></td>
									<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
									<td class="text-center"><?= $infoPorcentajeEXO ?></td>
									<td class="text-center text-danger textoNegrita"><?= $infoExonerado ?></td>
									<td class="text-center textoNegrita"><?= $infoTotalPago ?></td>
									<td class="text-center"><?= $infoEstadoPago ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center">
										<button class="btn btn-xs btn-icon bg-brown btn_info_exoneracion" id="<?= $codigoActividad ?>"><i class=" icon-info22"></i></button>
										<button class="btn btn-xs btn-icon bg-danger btn_elimina_exoneracion" id="<?= $codigoExoneracion ?>"><i class=" icon-trash"></i></button>
									</td>
								</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!--////////////////////////////////////////////////////////////////////////////////
		MODAL - EXONERAR DEUDAS
		////////////////////////////////////////////////////////////////////////////////-->
		<div id="modal_info_exoneraciones" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div id="box_modal_info_exoneraciones"></div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// BOTON DETALLES DE EXONERACION
				///////////////////////////////////////////////////
				$('button.btn_info_exoneracion').on('click', function() {
					var ruta            ='../';
					var codigoSocio     ='<?= $codigoSocio ?>';
					var actividad       ='ASA';
					var codigoActividad =$(this).attr('id');
					var operacion       ='INFORMACION_EXONERACION';
					var datos           ='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
					var urlProceso      =ruta+'modulo/modal-exonerar-deudas-socio.php?'+datos;
					$('#modal_info_exoneraciones').modal({ backdrop: 'static', keyboard: false, show: true });
					$('#modal_info_exoneraciones').on('shown.bs.modal',function(){
						$('#box_modal_info_exoneraciones').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
						$("#box_modal_info_exoneraciones").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
					});
				});

				///////////////////////////////////////////////////
				/// BOTON ELIMINAR EXONERACION
				///////////////////////////////////////////////////
				$('button.btn_elimina_exoneracion').on('click', function() {
					var ruta            ='../';
					var codigoSocio     ='<?= $codigoSocio ?>';
					var actividad       ='ASA';
					var codigoExoneracion =$(this).attr('id');
					var operacion       ='ELIMINA_EXONERACION_DEUDAS_SOCIO';
					var datos           ='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoExoneracion='+codigoExoneracion+'&operacion='+operacion;

					swal({
						title: "Eliminar",
						text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Eliminar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="EXONERACION_ELIMINADA"){
										new PNotify({title: 'ELIMINADO', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
										location.reload();
									}
									if(respuesta.mensaje=="ERROR_EXONERACION_ELIMINADA"){
										new PNotify({title: 'ERROR', text: 'Por favor intentelo nuevamente.', addclass: 'bg-warning'});
									}
								}
							});
						}
					});
				});
			});
		</script>
	<?php } ?>

	<?php if($opcion=="pagos_faenas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<?php if($deudasFaenas>0){ $infoActividad   ="FAENA"; ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-deuda-cuotas').DataTable({ 'pageLength': 20 });
					$('.tabla-deuda-cuotas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>

			<div class="panel">
				<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">DEUDAS FAENAS &rarr; <?= $info_total_FAE_saldo ?></h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO <?= $infoActividad ?></th>
									<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
									<th class="textoNegrita text-center">RAZON</th>
									<th class="textoNegrita text-center">MULTA</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-center">TOTAL</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="textoNegrita text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND (estadoPago='NP' OR estadoPago='MP') AND codigoSocio='$codigoSocio' ORDER BY id DESC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$tipoActividad   =$n[tipoActividad];
										$codigoActividad =$n[codigoActividad];
										$temaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
										$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
										$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
										$lotes           =$n[lotes];
										$asistio         =$n[asistio];
										$ingreso         =$n[ingreso];
										$salida          =$n[salida];
										$retraso         =$n[retraso];
										$multa           =$n[multa];
										$estadoPago      =$n[estadoPago];
										$conceptoPago    =$tipoActividad;
										$programado      =conceptoProgramado($codigoSocio,$codigoActividad);
										$porcentajePago  =porcentajePago($codigoSocio,$codigoActividad,$multa);
										$infoTotalMulta  ='S/. '.moneda($multa);

										if($asistio=="NO"){
											$rotActMay="POR INASISTENCIA A ".$infoActividad;
											$asistencia="FALTO";
											$razon="FALTA";
											$infoMulta='S/. '.moneda($multaFalta);

										}
										if($asistio=="SI" and $retraso>0.15){
											$rotActMay="POR TARDANZA A ".$infoActividad;
											$asistencia="TARDANZA";
											$razon="TARDE";
											$infoMulta='S/. '.moneda($multaTarde);
										}
										
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
									<td class="text-center"><?= $codigoActividad ?></td>
									<td class="text-left"><?= texto($temaActividad) ?></td>
									<td class="text-center"><?= $asistencia ?></td>
									<td class="text-center"><?= $infoMulta ?></td>
									<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center"><a href="#pagarMonto-<?= $codigoActividad ?>" data-toggle="modal" class="btn btn-xs btn-icon bg-brown"><i class=" icon-coin-dollar"></i></a></td>
									<!-- MODAL PAGO DE MULTA -->
									<script type="text/javascript">
										$(document).ready(function(){
											///////////////////////////////////////////////////
											/// MODULO DE PAGO
											///////////////////////////////////////////////////
											var codigoActividad='<?= $codigoActividad ?>';
											$('#pagarMonto-'+codigoActividad).on('shown.bs.modal', function(){
												var ruta           ='../';
												var opcion         ='modulo_pago_monto';
												var conceptoPago   ='<?= $tipoActividad ?>';
												var codigoConcepto ='<?= $codigoActividad ?>';
												var codigoSocio    ='<?= $codigoSocio ?>';
												var razon          ='<?= $razon ?>';
												var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&razon='+razon;
												$('#modulo_pago-'+codigoActividad).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
												$("#modulo_pago-"+codigoActividad).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
											});
										});
									</script>
									<div id="pagarMonto-<?= $codigoActividad ?>" class="modal fade">
										<div class="modal-dialog modal-lg">
											<div class="modal-content">
												<div class="modal-header bg-slate-600">
													<button type="button" class="close" data-dismiss="modal">&times;</button>
													<h6 class="modal-title">PAGO DE MULTA <?= $rotActMay ?> &rarr; <?= $infoNombre ?></h6>
												</div>
												<div class="modal-body">
													<div id="modulo_pago-<?= $codigoActividad ?>"></div>
													<div id="modulo_fechas_pago-<?= $codigoActividad ?>"></div>
												</div>
											</div>
										</div>
									</div>
								</tr>
								<?php $i++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php }else{ echo cajaAlerta('SIN DEUDAS PENDIENTES EN FAENAS','text-center','No se ha encontrado, ningún registro en el sistema con deudas pendientes en faenas para el socio seleccionado...','text-center','bg-warning'); } ?>
	<?php } ?>

	<?php if($opcion=="historial_pagos_faenas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<?php if($pagosFaenas>0){ $infoActividad   ="FAENA"; ?>
			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-deuda-cuotas').DataTable({ 'pageLength': 20 });
					$('.tabla-deuda-cuotas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>

			<div class="panel">
				<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">LISTA DE PAGOS &rarr; FAENAS</h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
							<thead>
								<tr class="success">
									<th class="textoNegrita text-center">#</th>
									<th class="textoNegrita text-center">CODIGO <?= $infoActividad ?></th>
									<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
									<th class="textoNegrita text-center">RAZON</th>
									<th class="textoNegrita text-center">MULTA</th>
									<th class="textoNegrita text-center">LOTES</th>
									<th class="textoNegrita text-center">TOTAL</th>
									<th class="textoNegrita text-center">ESTADO</th>
									<th class="textoNegrita text-center">%</th>
									<th class="textoNegrita text-center">EXONERADO</th>
									<th class="textoNegrita text-center">PAGADO</th>
									<th class="textoNegrita text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND (estadoPago='SP' OR estadoPago='EX') AND codigoSocio='$codigoSocio' ORDER BY id DESC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$tipoActividad   =$n[tipoActividad];
										$codigoActividad =$n[codigoActividad];
										$temaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
										$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
										$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
										$lotes           =$n[lotes];
										$asistio         =$n[asistio];
										$ingreso         =$n[ingreso];
										$salida          =$n[salida];
										$retraso         =$n[retraso];
										$multa           =$n[multa];
										$estadoPago      =$n[estadoPago];
										$conceptoPago    =$tipoActividad;
										$programado      =conceptoProgramado($codigoSocio,$codigoActividad);
										$porcentajePago  =porcentajePago($codigoSocio,$codigoActividad,$multa);
										$infoTotalMulta  ='S/. '.moneda($multa);

										$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
										$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
										$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');

										if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
										if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }


										if($programado>=2){
											$verificaPago   =verificaPagosProgramados($codigoActividad,$codigoSocio,$multa);
											$pagoProgramado ="SI";
										}else{
											$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$multa);
											$pagoProgramado ="NO";
										}

										if($asistio=="NO"){
											$rotActMay  ="POR INASISTENCIA A ".$infoActividad;
											$asistencia ="FALTO";
											$razon      ="FALTA";
											$infoMulta  ='S/. '.moneda($multaFalta);
											$inforazon  ="INASISTENCIA";

										}
										
										if($asistio=="SI" and $retraso>0.15){
											$rotActMay  ="POR TARDANZA A ".$infoActividad;
											$asistencia ="TARDANZA";
											$razon      ="TARDE";
											$infoMulta  ='S/. '.moneda($multaTarde);
											$inforazon  ="TARDANZA";
										}

										if($estadoPago=="SP" and $pagoProgramado=="SI"){ $infoEstadoPago='<span class="label label-success">PAGADO - '.ceros($programado,2).' F</span>'; }
										if($estadoPago=="SP" and $pagoProgramado=="NO"){ $infoEstadoPago='<span class="label label-success">PAGADO</span>'; }
										if($estadoPago=="EX"){
											$infoEstadoPago='<span class="label label-info">EXONERADO</span>';
											$monto=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
											$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$monto);
										}

										if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-info">CAJA</span>'; }
										if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-warning">ERROR</span>'; }
										if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='<span class="label label-success">NO REGISTRADO</span>'; }
								?>
									<tr>
										<td class="text-center"><?= ceros($i,2) ?></td>
										<td class="text-center"><?= $codigoActividad ?></td>
										<td class="text-left"><?= texto($temaActividad) ?></td>
										<td class="text-center"><?= $asistencia ?></td>
										<td class="text-center"><?= $infoMulta ?></td>
										<td class="text-center"><span class="label label-default"><?= ceros($lotes,2) ?> LOTES</span></td>
										<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
										<td class="text-center"><?= $infoEstadoPago ?></td>
										<td class="text-center"><?= $porcentaje.'%' ?></td>
										<td class="text-right text-danger textoNegrita"><?= $infoExonerado ?></td>
										<td class="text-right textoNegrita"><?= $infoTotalPago ?></td>
										<td class="text-center"><?= $caja ?></td>
										<td class="text-center"><a href="#infoPago-<?= $codigoActividad ?>" data-toggle="modal" class="btn btn-xs btn-icon bg-brown"><i class=" icon-info22"></i></a></td>
										<!-- MODAL INFORMACION PAGO DE MULTA -->
										<?php if($pagoProgramado=="SI"){ ?>
											<script type="text/javascript">
												$(document).ready(function(){
													///////////////////////////////////////////////////
													/// MODULO DE PAGO
													///////////////////////////////////////////////////
													var ruta            ='../';
													var opcion          ='pagar_fechas_programadas';
													var codigoSocio     ='<?= $codigoSocio ?>';
													var conceptoPago    ='<?= $conceptoPago ?>';
													var codigoConcepto  ='<?= $codigoActividad ?>';
													var codigoActividad ='<?= $codigoActividad ?>'
													var datos           ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto;
													$('#infoPago-'+codigoActividad).on('shown.bs.modal', function(){
														$('#modulo_fechas_pago_'+codigoActividad).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
														$("#modulo_fechas_pago_"+codigoActividad).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
													});
												});
											</script>

											<div id="infoPago-<?= $codigoActividad ?>" class="modal fade">
												<div class="modal-dialog modal-lg">
													<div class="modal-content">
														<div class="modal-header bg-brown">
															<button type="button" class="close" data-dismiss="modal">&times;</button>
															<h6 class="modal-title">INFORMACION DE PAGO</h6>
														</div>

														<div class="modal-body">
															<div class="row mb-20">
																<div class="col-sm-12">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita"><?= $infoActividad ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-5">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $inforazon ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-4">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-3">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">FECHAS DE PAGO</span> <span class="pull-right text-danger"><?= ceros($programado,2) ?></span></li>
																	</ul>
																</div>
															</div>
															<div id="modulo_fechas_pago_<?= $codigoActividad ?>"></div>
														</div>
													</div>
												</div>
											</div>
										<?php } ?>


										<div id="infoPago-<?= $codigoSocio ?>" class="modal fade">
											<div class="modal-dialog modal-lg">
												<div class="modal-content">
													<div class="modal-header bg-brown">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h6 class="modal-title textoNegrita">INFORMACION DE PAGO <?= $pagoProgramado ?></h6>
													</div>
													<div class="modal-body">
														<?php
															$sql="SELECT fechaOperacion, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND  codigoConcepto='$codigoActividad'";
															$infopago=mysqli_query($conexion,$sql);
															$dato=mysqli_fetch_array($infopago);
															$fechaOperacion  =$dato[fechaOperacion];
															$concepto        =$dato[concepto];
															$tipoDocumento   =$dato[tipoDocumento];
															$nroDocumento    =$dato[nroDocumento];
															$monto           =$dato[monto];
															$detalleConcepto =$dato[detalleConcepto];
															$codigoOperacion =$dato[codigoOperacion];
															$fecha           =$dato[fecha];
															$hora            =$dato[hora];
															$usuario         =$dato[usuario];
														?>
														<div class="row mb-20">
															<div class="col-sm-12">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $rotuloCM ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">DOCUMENTO</span> <span class="pull-right text-danger"><?= infoTipoDOC($tipoDocumento) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">INGRESADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">NRO DOCUMENTO</span> <span class="pull-right text-danger"><?= $nroDocumento ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">CODIGO OPERACION</span> <span class="pull-right text-danger"><?= $codigoOperacion ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">INGRESO</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fecha,'larga').' - '.horaCorta($hora) ?></span></li>
																</ul>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php if($pagoProgramado=="NO"){ ?>
											<div id="infoPago-<?= $codigoActividad ?>" class="modal fade">
												<div class="modal-dialog modal-lg">
													<div class="modal-content">
														<div class="modal-header bg-brown">
															<button type="button" class="close" data-dismiss="modal">&times;</button>
															<h6 class="modal-title textoNegrita">INFORMACION DE PAGO <?= $pagoProgramado ?></h6>
														</div>
														<div class="modal-body">
															<?php
																$sql="SELECT fechaOperacion, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND  codigoConcepto='$codigoActividad'";
																$infopago=mysqli_query($conexion,$sql);
																$dato=mysqli_fetch_array($infopago);
																$fechaOperacion  =$dato[fechaOperacion];
																$concepto        =$dato[concepto];
																$tipoDocumento   =$dato[tipoDocumento];
																$nroDocumento    =$dato[nroDocumento];
																$monto           =$dato[monto];
																$detalleConcepto =$dato[detalleConcepto];
																$codigoOperacion =$dato[codigoOperacion];
																$fecha           =$dato[fecha];
																$hora            =$dato[hora];
																$usuario         =$dato[usuario];
															?>
															<div class="row mb-20">
																<div class="col-sm-12">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita"><?= $rotActMay ?></span> <span class="pull-right text-danger"><?= texto($temaActividad) ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-6">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= $inforazon ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">DOCUMENTO</span> <span class="pull-right text-danger"><?= infoTipoDOC($tipoDocumento) ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">INGRESADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
																	</ul>
																</div>
																<div class="col-sm-6">
																	<ul class="list-group">
																		<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($multa) ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">NRO DOCUMENTO</span> <span class="pull-right text-danger"><?= $nroDocumento ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">CODIGO OPERACION</span> <span class="pull-right text-danger"><?= $codigoOperacion ?></span></li>
																		<li class="list-group-item"><span class="textoNegrita">INGRESO</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fecha,'larga').' - '.horaCorta($hora) ?></span></li>
																	</ul>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										<?php } ?>
									</tr>
								<?php $i++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php }else{ echo cajaAlerta('SIN HISTORIAL DE PAGOS EN FAENAS','text-center','No se ha encontrado, ningún registro en el sistema para esta opción...','text-center','bg-warning'); } ?>
	<?php } ?>

	<?php if($opcion=="historial_exoneraciones_faenas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// CONFIGURACION DE TABLAS
				///////////////////////////////////////////////////
				$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
				var lastIdx = null;
				var table = $('.tabla-deuda-cuotas').DataTable({ 'pageLength': 20 });
				$('.tabla-deuda-cuotas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
				$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
				$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
			});
		</script>

		<div class="panel">
			<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">LISTA DE PAGOS &rarr; FAENAS</h6></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
						<thead>
							<tr class="success">
								<th class="textoNegrita text-center">#</th>
								<th class="textoNegrita text-center">CODIGO <?= $infoActividad ?></th>
								<th class="textoNegrita text-left">TEMA <?= $infoActividad ?></th>
								<th class="textoNegrita text-center">DEUDA</th>
								<th class="textoNegrita text-center">%</th>
								<th class="textoNegrita text-center">EXONERADO</th>
								<th class="textoNegrita text-center">PAGADO</th>
								<th class="textoNegrita text-center">ESTADO</th>
								<th class="textoNegrita text-center">CAJA</th>
								<th class="text-center"><i class="fa fa-align-justify"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="SELECT tipoActividad, codigoActividad, lotes, asistio, ingreso, salida, retraso, multa, estadoPago FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND estadoPago='EX' AND codigoSocio='$codigoSocio' ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$tipoActividad   =$n[tipoActividad];
									$codigoActividad =$n[codigoActividad];
									$temaActividad   =infoActividad($idJuntaDirectiva,$codigoActividad,'','temaActividad');
									$multaTarde      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaporTardanza');
									$multaFalta      =infoActividad($idJuntaDirectiva,$codigoActividad,'','infoMultaPorFalta');
									$lotes           =$n[lotes];
									$asistio         =$n[asistio];
									$ingreso         =$n[ingreso];
									$salida          =$n[salida];
									$retraso         =$n[retraso];
									$multa           =$n[multa];
									$estadoPago      =$n[estadoPago];
									$conceptoPago    =$tipoActividad;
									$programado      =conceptoProgramado($codigoSocio,$codigoActividad);
									$porcentajePago  =porcentajePago($codigoSocio,$codigoActividad,$multa);
									$infoTotalMulta  ='S/. '.moneda($multa);

									$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
									$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
									$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
									$codigoExoneracion=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'codigoExoneracionExoneraciones');

									if($porcentaje>0){ $infoPorcentajeEXO=$porcentaje.'%'; }else{ $infoPorcentajeEXO=''; }
									if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
									if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }

									if($estadoPago=="SP" and $pagoProgramado=="SI"){ $infoEstadoPago='<span class="label label-success">PAGADO - '.ceros($programado,2).' F</span>'; }
									if($estadoPago=="SP" and $pagoProgramado=="NO"){ $infoEstadoPago='<span class="label label-success">PAGADO</span>'; }
									if($estadoPago=="EX"){
										$infoEstadoPago='<span class="label label-info">EXONERADO</span>';
										$monto=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
										$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$monto);
									}

									if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-info">CAJA</span>'; }
									if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-warning">ERROR</span>'; }
									if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='<span class="label label-success">NO REGISTRADO</span>'; }
							?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoActividad ?></td>
									<td class="text-left"><?= texto($temaActividad) ?></td>
									<td class="text-center text-danger textoNegrita"><?= $infoTotalMulta ?></td>
									<td class="text-center"><?= $infoPorcentajeEXO ?></td>
									<td class="text-center text-danger textoNegrita"><?= $infoExonerado ?></td>
									<td class="text-center textoNegrita"><?= $infoTotalPago ?></td>
									<td class="text-center"><?= $infoEstadoPago ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center">
										<button class="btn btn-xs btn-icon bg-brown btn_info_exoneracion" id="<?= $codigoActividad ?>"><i class=" icon-info22"></i></button>
										<button class="btn btn-xs btn-icon bg-danger btn_elimina_exoneracion" id="<?= $codigoExoneracion ?>"><i class=" icon-trash"></i></button>
									</td>
								</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!--////////////////////////////////////////////////////////////////////////////////
		MODAL - EXONERAR DEUDAS
		////////////////////////////////////////////////////////////////////////////////-->
		<div id="modal_info_exoneraciones" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div id="box_modal_info_exoneraciones"></div>
				</div>
			</div>
		</div>

		<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// BOTON DETALLES DE EXONERACION
				///////////////////////////////////////////////////
				$('button.btn_info_exoneracion').on('click', function() {
					var ruta            ='../';
					var codigoSocio     ='<?= $codigoSocio ?>';
					var actividad       ='FAE';
					var codigoActividad =$(this).attr('id');
					var operacion       ='INFORMACION_EXONERACION';
					var datos           ='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
					var urlProceso      =ruta+'modulo/modal-exonerar-deudas-socio.php?'+datos;
					$('#modal_info_exoneraciones').modal({ backdrop: 'static', keyboard: false, show: true });
					$('#modal_info_exoneraciones').on('shown.bs.modal',function(){
						$('#box_modal_info_exoneraciones').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
						$("#box_modal_info_exoneraciones").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
					});
				});

				///////////////////////////////////////////////////
				/// BOTON ELIMINAR EXONERACION
				///////////////////////////////////////////////////
				$('button.btn_elimina_exoneracion').on('click', function() {
					var ruta            ='../';
					var codigoSocio     ='<?= $codigoSocio ?>';
					var actividad       ='FAE';
					var codigoExoneracion =$(this).attr('id');
					var operacion       ='ELIMINA_EXONERACION_DEUDAS_SOCIO';
					var datos           ='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoExoneracion='+codigoExoneracion+'&operacion='+operacion;

					swal({
						title: "Eliminar",
						text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Eliminar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="EXONERACION_ELIMINADA"){
										new PNotify({title: 'ELIMINADO', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
										location.reload();
									}
									if(respuesta.mensaje=="ERROR_EXONERACION_ELIMINADA"){
										new PNotify({title: 'ERROR', text: 'Por favor intentelo nuevamente.', addclass: 'bg-warning'});
									}
								}
							});
						}
					});
				});
			});
		</script>
	<?php } ?>

	<?php if($opcion=="pagos_cuotas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<?php if($nroDeudasCuotas>0){ ?>
			<div class="panel">
				<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">DEUDAS CUOTAS &rarr; <?= $info_total_CUO_saldo ?></h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
							<thead>
								<tr class="success">
									<th class="text-center">#</th>
									<th class="text-center">CODIGO DE CUOTA</th>
									<th class="text-left">CONCEPTO DE CUOTA</th>
									<th class="text-center">LOTES</th>
									<th class="text-center">CUOTA</th>
									<th class="text-center">TOTAL</th>
									<th class="text-center">ESTADO</th>
									<th class="text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql="SELECT codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago FROM sm_mod_cuotas_socios WHERE (estadoPago='NP' OR estadoPago='MP') AND codigoSocio='$codigoSocio' ORDER BY id DESC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$tipoActividad   ='CUO';
										$codigoCuota     =$n[codigoCuota];
										$codigoActividad =$codigoCuota;
										$lotes           =$n[lotes];
										$montoCuota      =$n[montoCuota];
										$montoPago       =$n[montoPago];
										$estadoPago      =$n[estadoPago];
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
									<td class="text-center"><?= $codigoCuota ?></td>
									<td class="text-left"><?= $conceptoCuota ?></td>
									<td class="text-center"><span class="label label-default textoNegrita"><?= ceros($lotes,2) ?> LOTES</span></td>
									<td class="text-right"><?= 'S/. '.moneda($montoCuota) ?></td>
									<td class="text-right text-danger textoNegrita"><?= 'S/. '.moneda($montoPago) ?></td>
									<td class="text-center"><?= $estado ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center"><a href="#pagarMonto-<?= $codigoActividad ?>" data-toggle="modal" class="btn btn-xs btn-icon bg-brown"><i class=" icon-coin-dollar"></i></a></td>
								</tr>

								<!-- MODAL PAGO DE MULTA -->
								<script type="text/javascript">
									$(document).ready(function(){
										///////////////////////////////////////////////////
										/// MODULO DE PAGO
										///////////////////////////////////////////////////
										var codigoActividad='<?= $codigoActividad ?>';
										$('#pagarMonto-'+codigoActividad).on('shown.bs.modal', function(){
											var ruta           ='../';
											var opcion         ='modulo_pago_monto';
											var conceptoPago   ='<?= $tipoActividad ?>';
											var codigoConcepto ='<?= $codigoActividad ?>';
											var codigoSocio    ='<?= $codigoSocio ?>';
											var razon          ='<?= $razon ?>';
											var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto+'&razon='+razon;
											$('#modulo_pago-'+codigoActividad).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
											$("#modulo_pago-"+codigoActividad).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
										});
									});
								</script>
								<div id="pagarMonto-<?= $codigoActividad ?>" class="modal fade">
									<div class="modal-dialog modal-lg">
										<div class="modal-content">
											<div class="modal-header bg-slate-600">
												<button type="button" class="close" data-dismiss="modal">&times;</button>
												<h6 class="modal-title">PAGO DE MULTA <?= $rotActMay ?> &rarr; <?= $infoNombre ?></h6>
											</div>
											<div class="modal-body">
												<div id="modulo_pago-<?= $codigoActividad ?>"></div>
												<div id="modulo_fechas_pago-<?= $codigoActividad ?>"></div>
											</div>
										</div>
									</div>
								</div>
								<?php $i++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<script type="text/javascript">
				$(document).ready(function(){
					///////////////////////////////////////////////////
					/// CONFIGURACION DE TABLAS
					///////////////////////////////////////////////////
					$.extend( $.fn.dataTable.defaults, { autoWidth: false, columnDefs: [{ orderable: false, width: '100px', targets: [ 7 ] }], dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>', language: { search: '<span>Busqueda:</span> _INPUT_', lengthMenu: '<span>Mostar:</span> _MENU_', paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }}, drawCallback: function () { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup'); }, preDrawCallback: function() { $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup'); }});
					var lastIdx = null;
					var table = $('.tabla-deuda-cuotas').DataTable({ 'pageLength': 20 });
					$('.tabla-deuda-cuotas tbody').on('mouseover', 'td', function() { var colIdx = table.cell(this).index().column; if (colIdx !== lastIdx) { $(table.cells().nodes()).removeClass('active'); $(table.column(colIdx).nodes()).addClass('active'); } }).on('mouseleave', function() { $(table.cells().nodes()).removeClass('active'); });
					$('.dataTables_filter input[type=search]').attr('placeholder','Buscar...');
					$('.dataTables_length select').select2({ minimumResultsForSearch: "-1" });
				});
			</script>
		<?php }else{ echo cajaAlerta('SIN DEUDAS PENDIENTES EN CUOTAS','text-center','No se ha encontrado, ningún registro en el sistema con deudas pendientes en cuotas para el socio seleccionado...','text-center','bg-warning'); } ?>
	<?php } ?>

	<?php if($opcion=="historial_pagos_cuotas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<?php if($pagosCuotas>0){ ?>
			<div class="panel">
				<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">LISTA DE PAGOS &rarr; CUOTAS</h6></div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
							<thead>
								<tr class="success">
									<th class="text-center">#</th>
									<th class="text-center">CODIGO DE CUOTA</th>
									<th class="text-left">CONCEPTO DE CUOTA</th>
									<th class="text-center">LOTES</th>
									<th class="text-center">CUOTA</th>
									<th class="text-center">TOTAL</th>
									<th class="text-center">ESTADO</th>
									<th class="text-center">%</th>
									<th class="text-center">EXONERADO</th>
									<th class="text-center">PAGADO</th>
									<th class="text-center">CAJA</th>
									<th class="text-center"><i class="fa fa-align-justify"></i></th>
								</tr>
							</thead>
							<tbody>
								<?php
									$sql="SELECT codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago FROM sm_mod_cuotas_socios WHERE (estadoPago='SP' OR estadoPago='EX')  AND codigoSocio='$codigoSocio' ORDER BY id DESC";
									$rs=mysqli_query($conexion,$sql);
									$i=1;
									while($n=mysqli_fetch_array($rs)){
										$codigoCuota     =$n[codigoCuota];
										$codigoSocio     =$n[codigoSocio];
										$lotes           =$n[lotes];
										$montoCuota      =$n[montoCuota];
										$montoPago       =$n[montoPago];
										$estadoPago      =$n[estadoPago];
										$conceptoPago    ="CUO";
										$dni             =infoSocios($codigoSocio,'dni');
										$conceptoCuota   =texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota'));
										$programado      =conceptoProgramado($codigoSocio,$codigoCuota);
										$codigoActividad =$codigoCuota;

										$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
										$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
										$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');

										if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
										if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }

										if($programado>=2){
											$verificaPago   =verificaPagosProgramados($codigoCuota,$codigoSocio,$montoPago);
											$pagoProgramado ="SI";
										}else{
											$verificaPago   =verificaPago($codigoCuota,$codigoSocio,$montoPago);
											$pagoProgramado ="NO";
										}

										if($estadoPago=="SP" and $pagoProgramado=="SI"){ $infoEstadoPago='<span class="label label-success">PAGADO - '.ceros($programado,2).' F</span>'; }
										if($estadoPago=="SP" and $pagoProgramado=="NO"){ $infoEstadoPago='<span class="label label-success">PAGADO</span>'; }
										if($estadoPago=="EX"){
											$infoEstadoPago='<span class="label label-info">EXONERADO</span>';
											$monto=infoActividad($idJuntaDirectiva,$codigoCuota,$codigoSocio,'totalPagoExoneraciones');
											$verificaPago   =verificaPago($codigoCuota,$codigoSocio,$monto);
										}

										if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-info">CAJA</span>'; }
										if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-warning">ERROR</span>'; }
										if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='<span class="label label-success">NO REGISTRADO</span>'; }
								?>
									<tr>
										<td class="text-center"><?= ceros($i,2) ?></td>
										<td class="text-center"><?= $codigoCuota ?></td>
										<td class="text-left"><?= $conceptoCuota ?></td>
										<td class="text-center"><span class="label label-default textoNegrita"><?= ceros($lotes,2) ?> LOTES</span></td>
										<td class="text-right"><?= 'S/. '.moneda($montoCuota) ?></td>
										<td class="text-right text-danger textoNegrita"><?= 'S/. '.moneda($montoPago) ?></td>
										<td class="text-center"><?= $infoEstadoPago ?></td>
										<td class="text-center"><?= $porcentaje.'%' ?></td>
										<td class="text-right text-danger textoNegrita"><?= $infoExonerado ?></td>
										<td class="text-right textoNegrita"><?= $infoTotalPago ?></td>
										<td class="text-center"><?= $caja ?></td>
										<td class="text-center"><a href="#infoPago-<?= $codigoCuota ?>" data-toggle="modal" class="btn btn-xs btn-icon bg-grey" data-popup="tooltip" title="Informacion de pago"><i class=" icon-info22"></i></a></td>
									</tr>
									<!-- MODAL INFORMACIO DE PAGO DE CUOTA -->
									<?php if($pagoProgramado=="SI"){ ?>
										<script type="text/javascript">
											$(document).ready(function(){
												///////////////////////////////////////////////////
												/// MODULO DE PAGO
												///////////////////////////////////////////////////
												var ruta           ='../';
												var opcion         ='pagar_fechas_programadas';
												var codigoSocio    ='<?= $codigoSocio ?>';
												var conceptoPago   ='<?= $conceptoPago ?>';
												var codigoConcepto ='<?= $codigoCuota ?>';
												var codigoCuota    ='<?= $codigoCuota ?>'
												var datos          ='opcion='+opcion+'&codigoSocio='+codigoSocio+'&conceptoPago='+conceptoPago+'&codigoConcepto='+codigoConcepto;
												$('#infoPago-'+codigoCuota).on('shown.bs.modal', function(){
													$('#modulo_fechas_pago_'+codigoCuota).fadeIn("slow").html('<div class="form-group"><div class="row"><div class="col-sm-12 text-center"><img src="'+ruta+'assets/images/loader.gif"><br>CARGANDO MODULO DE PAGO</div></div></div>').fadeIn(1000).delay(1000);
													$("#modulo_fechas_pago_"+codigoCuota).fadeIn("slow").load('modulo/modulo-pagos-socio.php?'+datos).fadeIn(1000).delay(1000);
												});
											});
										</script>

										<div id="infoPago-<?= $codigoCuota ?>" class="modal fade">
											<div class="modal-dialog modal-lg">
												<div class="modal-content">
													<div class="modal-header bg-brown">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h6 class="modal-title">INFORMACION DE PAGO</h6>
													</div>
													<div class="modal-body">
														<div class="row mb-20">
															<div class="col-sm-12">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">DETALLE</span> <span class="pull-right text-danger"><?= texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota')) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-4">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= texto(infoPago('','',$concepto,'conceptoPago')) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-4">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($montoCuota) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-4">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">FECHAS DE PAGO</span> <span class="pull-right text-danger"><?= ceros($programado,2) ?></span></li>
																</ul>
															</div>
														</div>
														<div id="modulo_fechas_pago_<?= $codigoCuota ?>"></div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
									<?php if($pagoProgramado=="NO"){ ?>
										<div id="infoPago-<?= $codigoCuota ?>" class="modal fade">
											<div class="modal-dialog modal-lg">
												<div class="modal-content">
													<div class="modal-header bg-brown">
														<button type="button" class="close" data-dismiss="modal">&times;</button>
														<h6 class="modal-title">INFORMACION DE PAGO</h6>
													</div>
													<div class="modal-body">
														<?php
															$sql="SELECT fechaOperacion, concepto, tipoDocumento, nroDocumento, monto, detalleConcepto, codigoOperacion, fecha, hora, usuario FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND  codigoConcepto='$codigoCuota'";
															$infopago=mysqli_query($conexion,$sql);
															$dato=mysqli_fetch_array($infopago);
															$fechaOperacion  =$dato[fechaOperacion];
															$concepto        =$dato[concepto];
															$tipoDocumento   =$dato[tipoDocumento];
															$nroDocumento    =$dato[nroDocumento];
															$monto           =$dato[monto];
															$detalleConcepto =$dato[detalleConcepto];
															$codigoOperacion =$dato[codigoOperacion];
															$fecha           =$dato[fecha];
															$hora            =$dato[hora];
															$usuario         =$dato[usuario];
														?>
														<div class="row mb-20">
															<div class="col-sm-12">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">DETALLE</span> <span class="pull-right text-danger"><?= texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota')) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">CONCEPTO</span> <span class="pull-right text-danger"><?= texto(infoPago('','',$concepto,'conceptoPago')) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">DOCUMENTO</span> <span class="pull-right text-danger"><?= infoTipoDOC($tipoDocumento) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">FECHA OPERACION</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fechaOperacion,'larga') ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">INGRESADO POR</span> <span class="pull-right text-danger"><?= texto(datoUsuario($usuario,'nombreFull')) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-6">
																<ul class="list-group">
																	<li class="list-group-item"><span class="textoNegrita">MONTO PAGADO</span> <span class="pull-right text-danger"><?= 'S/. '.moneda($monto) ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">NRO DOCUMENTO</span> <span class="pull-right text-danger"><?= $nroDocumento ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">CODIGO OPERACION</span> <span class="pull-right text-danger"><?= $codigoOperacion ?></span></li>
																	<li class="list-group-item"><span class="textoNegrita">INGRESO</span> <span class="pull-right text-danger textoMayuscula"><?= infoFecha($fecha,'larga').' - '.horaCorta($hora) ?></span></li>
																</ul>
															</div>
															<div class="col-sm-12 mt-20 text-center">
																<a href="../documentos/info-pago.php?codigoSocio=<?= $codigoSocio ?>&codigoOperacion=<?= $codigoOperacion ?>&opcion=infoPago" class="btn bg-teal-400 btn-labeled btn-rounded"><b><i class="icon-printer"></i></b> Imprimir</a>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
								<?php $i++; } ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		<?php }else{ echo cajaAlerta('SIN HISTORIAL DE PAGOS EN CUOTAS','text-center','No se ha encontrado, ningún registro en el sistema para esta opción...','text-center','bg-warning'); } ?>
	<?php } ?>

	<?php if($opcion=="historial_exoneraciones_cuotas"){ ?>
		<div class="alert alert-styled-left alert-arrow-left alpha-teal text-center textoBig">
			<div class="visible-lg"><strong>SOCIO:</strong> <?= $infoNombreFull ?>&nbsp;&nbsp;|&nbsp;&nbsp;<strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
			<div class="visible-xs"><?= $infoNombreFull ?><br><strong>CODIGO SOCIO:</strong> <?= $codigoSocio ?></div>
		</div>

		<div class="panel">
			<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">LISTA DE PAGOS &rarr; CUOTAS</h6></div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table tabla table-bordered table-hover tabla-deuda-cuotas">
						<thead>
							<tr class="success">
								<th class="text-center">#</th>
								<th class="text-center">CODIGO DE CUOTA</th>
								<th class="text-left">CONCEPTO DE CUOTA</th>
								<th class="text-center">DEUDA</th>
								<th class="text-center">%</th>
								<th class="text-center">EXONERADO</th>
								<th class="text-center">PAGADO</th>
								<th class="text-center">ESTADO</th>
								<th class="text-center">CAJA</th>
								<th class="text-center"><i class="fa fa-align-justify"></i></th>
							</tr>
						</thead>
						<tbody>
							<?php
								$sql="SELECT codigoCuota, codigoSocio, lotes, montoCuota, montoPago, estadoPago FROM sm_mod_cuotas_socios WHERE (estadoPago='SP' OR estadoPago='EX')  AND codigoSocio='$codigoSocio' ORDER BY id DESC";
								$rs=mysqli_query($conexion,$sql);
								$i=1;
								while($n=mysqli_fetch_array($rs)){
									$codigoCuota     =$n[codigoCuota];
									$codigoSocio     =$n[codigoSocio];
									$lotes           =$n[lotes];
									$montoCuota      =$n[montoCuota];
									$montoPago       =$n[montoPago];
									$estadoPago      =$n[estadoPago];
									$conceptoPago    ="CUO";
									$dni             =infoSocios($codigoSocio,'dni');
									$conceptoCuota   =texto(infoCuota($idJuntaDirectiva,$codigoCuota,'conceptoCuota'));
									$programado      =conceptoProgramado($codigoSocio,$codigoCuota);
									$codigoActividad = $codigoCuota;

									$porcentaje=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'porcentajeExoneraciones');
									$exonerado=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'exoneradoExoneraciones');
									$totalPago=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
									$codigoExoneracion=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'codigoExoneracionExoneraciones');

									if($exonerado>0){ $infoExonerado='- S/.'.moneda($exonerado); }else{ $infoExonerado=''; }
									if($totalPago>0){ $infoTotalPago='S/.'.moneda($totalPago); }else{ $infoTotalPago=''; }

									if($estadoPago=="SP" and $pagoProgramado=="SI"){ $infoEstadoPago='<span class="label label-success">PAGADO - '.ceros($programado,2).' F</span>'; }
									if($estadoPago=="SP" and $pagoProgramado=="NO"){ $infoEstadoPago='<span class="label label-success">PAGADO</span>'; }
									if($estadoPago=="EX"){
										$infoEstadoPago='<span class="label label-info">EXONERADO</span>';
										$monto=infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,'totalPagoExoneraciones');
										$verificaPago   =verificaPago($codigoActividad,$codigoSocio,$monto);
									}

									if($verificaPago=="OK" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-info">CAJA</span>'; }
									if($verificaPago=="ERROR" and ($estadoPago=="SP" or $estadoPago=="EX")){ $caja='<span class="label label-warning">ERROR</span>'; }
									if($verificaPago=="ERROR" and ($estadoPago=="EX" and $monto=="0")){ $caja='<span class="label label-success">NO REGISTRADO</span>'; }
							?>
								<tr>
									<td class="text-center"><?= ceros($i,2) ?></td>
									<td class="text-center"><?= $codigoCuota ?></td>
									<td class="text-left"><?= $conceptoCuota ?></td>
									<td class="text-right text-danger textoNegrita"><?= 'S/. '.moneda($montoPago) ?></td>
									<td class="text-center"><?= $porcentaje.'%' ?></td>
									<td class="text-right text-danger textoNegrita"><?= $infoExonerado ?></td>
									<td class="text-right textoNegrita"><?= $infoTotalPago ?></td>
									<td class="text-center"><?= $infoEstadoPago ?></td>
									<td class="text-center"><?= $caja ?></td>
									<td class="text-center">
										<button class="btn btn-xs btn-icon bg-brown btn_info_exoneracion" id="<?= $codigoActividad ?>"><i class=" icon-info22"></i></button>
										<button class="btn btn-xs btn-icon bg-danger btn_elimina_exoneracion" id="<?= $codigoExoneracion ?>"><i class=" icon-trash"></i></button>
									</td>
								</tr>
							<?php $i++; } ?>
						</tbody>
					</table>
				</div>
				<!--////////////////////////////////////////////////////////////////////////////////
				MODAL - EXONERAR DEUDAS
				////////////////////////////////////////////////////////////////////////////////-->
				<div id="modal_info_exoneraciones" class="modal fade" tabindex="-1" role="dialog">
					<div class="modal-dialog modal-lg">
						<div class="modal-content">
							<div id="box_modal_info_exoneraciones"></div>
						</div>
					</div>
				</div>

				<script type="text/javascript">
			$(document).ready(function(){
				///////////////////////////////////////////////////
				/// BOTON DETALLES DE EXONERACION
				///////////////////////////////////////////////////
				$('button.btn_info_exoneracion').on('click', function() {
					var ruta            ='../';
					var codigoSocio     ='<?= $codigoSocio ?>';
					var actividad       ='CUO';
					var codigoActividad =$(this).attr('id');
					var operacion       ='INFORMACION_EXONERACION';
					var datos           ='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoActividad='+codigoActividad+'&operacion='+operacion;
					var urlProceso      =ruta+'modulo/modal-exonerar-deudas-socio.php?'+datos;
					$('#modal_info_exoneraciones').modal({ backdrop: 'static', keyboard: false, show: true });
					$('#modal_info_exoneraciones').on('shown.bs.modal',function(){
						$('#box_modal_info_exoneraciones').fadeIn("slow").html('<div class="row"><div class="col-sm-12 text-center"><br><img src="'+ruta+'/assets/images/loader.gif"><br>CARGANDO MODULO<br><br></div></div>');
						$("#box_modal_info_exoneraciones").fadeIn("slow").load(urlProceso).hide().fadeIn(300).delay(300);
					});
				});

				///////////////////////////////////////////////////
				/// BOTON ELIMINAR EXONERACION
				///////////////////////////////////////////////////
				$('button.btn_elimina_exoneracion').on('click', function() {
					var ruta            ='../';
					var codigoSocio     ='<?= $codigoSocio ?>';
					var actividad       ='CUO';
					var codigoExoneracion =$(this).attr('id');
					var operacion       ='ELIMINA_EXONERACION_DEUDAS_SOCIO';
					var datos           ='codigoSocio='+codigoSocio+'&actividad='+actividad+'&codigoExoneracion='+codigoExoneracion+'&operacion='+operacion;

					swal({
						title: "Eliminar",
						text: "Se va ha eliminar el item seleccionado, ¿esta seguro de hacerlo?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: "#EF5350",
						confirmButtonText: "Si, Eliminar",
						cancelButtonText: "No, Cancelar",
						closeOnConfirm: true,
						closeOnCancel: true
					},
					function(isConfirm){
						if (isConfirm) { 
							swal({ title: "Eliminado!", text: "El item seleccionado fue eliminado del sistema.", confirmButtonColor: "#66BB6A", type: "success" });
							$.ajax({
								type: "POST",
								url: ruta+'php/mantenimiento-socios.php',
								data: datos,
								dataType:'json',
								success: function(respuesta){
									if(respuesta.mensaje=="EXONERACION_ELIMINADA"){
										new PNotify({title: 'ELIMINADO', text: 'El item seleccionado fue eliminado del sistema.', addclass: 'bg-success'});
										location.reload();
									}
									if(respuesta.mensaje=="ERROR_EXONERACION_ELIMINADA"){
										new PNotify({title: 'ERROR', text: 'Por favor intentelo nuevamente.', addclass: 'bg-warning'});
									}
								}
							});
						}
					});
				});
			});
		</script>
			</div>
		</div>
	<?php } ?>

	<?php if($opcion=="operaciones"){ ?>
		<div class="panel panel-default">
			<div class="panel-heading bg-teal"><h6 class="panel-title textoNegrita">REGISTRO DE OPERACIONES PROCESADAS</h6></div>
			<div class="table-responsive">
				<table class="table tabla-info table-bordered table-hover">
					<thead>
						<tr class="success">
							<th class="textoNegrita text-center">#</th>
							<th class="textoNegrita text-left">DETALLE DE PROCESO</th>
							<th class="textoNegrita text-center">FECHA</th>
							<th class="textoNegrita text-center">HORA</th>
							<th class="textoNegrita text-center">USUARIO</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$sql="SELECT proceso, fecha, hora, usuario FROM sm_procesos_socios WHERE codigoSocio='$codigoSocio' ORDER BY id DESC";
							$rs=mysqli_query($conexion,$sql);
							$i=1;
							while($n=mysqli_fetch_array($rs)){
								$proceso =$n[proceso];
								$fecha   =$n[fecha];
								$hora    =$n[hora];
								$usuario =$n[usuario];
						?>
						<tr>
							<td class="text-center"><?= ceros($i,2) ?></td>
							<td class="text-left"><?= texto($proceso) ?></td>
							<td class="text-center textoMayuscula"><?= infoFecha($fecha,'normal') ?></td>
							<td class="text-center"><?= horaCorta($hora) ?></td>
							<td class="text-center"><?= texto(datoUsuario($usuario,'nombre')) ?></td>
						</tr>
						<?php $i++; } ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php } ?>

<?php
	}else{ include($ruta.'template/404-socio.tpl'); }
	include($ruta.'template/footer.tpl');
?>