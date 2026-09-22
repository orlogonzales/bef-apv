<?php
	date_default_timezone_set("America/Lima");

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// CONEXION A LA BASE DE DATOS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function conexionDB(){
		$servidor = "localhost";
		$usuario  = "admin";
		$clave    = "cuscoperu";
		$bd       = "sis_meeting";
		$conexion = mysqli_connect($servidor, $usuario, $clave,$bd) or die("Ha sucedido un error inexperado en la conexion de la base de datos");
		return $conexion;
	}

	function cerrarDB(){
		//$close = mysqli_close($conexion);
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE TIEMPO
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoTiempo($info){
		if($info=="fecha"){ $info=date('Y-m-d'); }
		if($info=="hora"){ $info=date("H:i:s"); }
		return $info;
	}

	function diasEntre($fecha_a,$fecha_b){
		$fecha_a=strtotime($fecha_a);
		$fecha_b=strtotime($fecha_b);
		$diferencia=$fecha_b-$fecha_a;
		$info=((($diferencia/60)/60)/24);
		return $info;
	}
	
	function infoFecha($fecha,$forma){ 
		$fe=date('D d M Y', strtotime($fecha));
		$dias=substr($fe,0,3);
		$dia=substr($fe,4,2);
		$mes=substr($fe,7,3);
		$anio=substr($fe,11,4);

		if($forma=="file"){
			if ($dias=="Mon"){$diasemana="LUN";}
			if ($dias=="Tue"){$diasemana="MAR";}
			if ($dias=="Wed"){$diasemana="MIE";}
			if ($dias=="Thu"){$diasemana="JUE";}
			if ($dias=="Fri"){$diasemana="VIE";}
			if ($dias=="Sat"){$diasemana="SAB";}
			if ($dias=="Sun"){$diasemana="DOM";}

			if ($mes=="Jan"){$mesannio="ENE";}
			if ($mes=="Feb"){$mesannio="FEB";}
			if ($mes=="Mar"){$mesannio="MAR";}
			if ($mes=="Apr"){$mesannio="ABR";}
			if ($mes=="May"){$mesannio="MAY";}
			if ($mes=="Jun"){$mesannio="JUN";}
			if ($mes=="Jul"){$mesannio="JUL";}
			if ($mes=="Aug"){$mesannio="AGO";}
			if ($mes=="Sep"){$mesannio="SET";}
			if ($mes=="Oct"){$mesannio="OCT";}
			if ($mes=="Nov"){$mesannio="NOV";}
			if ($mes=="Dec"){$mesannio="DIC";}
			$fecha=$diasemana.''.$dia.''.$mesannio.''.$anio;
		}

		if($forma=="larga"){
			if ($dias=="Mon"){$diasemana="Lunes";}
			if ($dias=="Tue"){$diasemana="Martes";}
			if ($dias=="Wed"){$diasemana="Miércoles";}
			if ($dias=="Thu"){$diasemana="Jueves";}
			if ($dias=="Fri"){$diasemana="Viernes";}
			if ($dias=="Sat"){$diasemana="Sábado";}
			if ($dias=="Sun"){$diasemana="Domingo";}

			if ($mes=="Jan"){$mesannio="Enero";}
			if ($mes=="Feb"){$mesannio="Febrero";}
			if ($mes=="Mar"){$mesannio="Marzo";}
			if ($mes=="Apr"){$mesannio="Abril";}
			if ($mes=="May"){$mesannio="Mayo";}
			if ($mes=="Jun"){$mesannio="Junio";}
			if ($mes=="Jul"){$mesannio="Julio";}
			if ($mes=="Aug"){$mesannio="Agosto";}
			if ($mes=="Sep"){$mesannio="Setiembre";}
			if ($mes=="Oct"){$mesannio="Octubre";}
			if ($mes=="Nov"){$mesannio="Noviembre";}
			if ($mes=="Dec"){$mesannio="Diciembre";}
			$fecha=$diasemana.' '.$dia.' de '.$mesannio.' de '.$anio;
		}
		
		if($forma=="corta"){
			if ($dias=="Mon"){$diasemana="Lun";}
			if ($dias=="Tue"){$diasemana="Mar";}
			if ($dias=="Wed"){$diasemana="Mie";}
			if ($dias=="Thu"){$diasemana="Jue";}
			if ($dias=="Fri"){$diasemana="Vie";}
			if ($dias=="Sat"){$diasemana="Sab";}
			if ($dias=="Sun"){$diasemana="Dom";}

			if ($mes=="Jan"){$mesannio="Ene";}
			if ($mes=="Feb"){$mesannio="Feb";}
			if ($mes=="Mar"){$mesannio="Mar";}
			if ($mes=="Apr"){$mesannio="Abr";}
			if ($mes=="May"){$mesannio="May";}
			if ($mes=="Jun"){$mesannio="Jun";}
			if ($mes=="Jul"){$mesannio="Jul";}
			if ($mes=="Aug"){$mesannio="Ago";}
			if ($mes=="Sep"){$mesannio="Set";}
			if ($mes=="Oct"){$mesannio="Oct";}
			if ($mes=="Nov"){$mesannio="Nov";}
			if ($mes=="Dec"){$mesannio="Dic";}
			$fecha=$diasemana.' '.$dia.' de '.$mesannio.' de '.$anio;
		}

		if($forma=="normal"){
			if ($mes=="Jan"){$mesannio="Ene";}
			if ($mes=="Feb"){$mesannio="Feb";}
			if ($mes=="Mar"){$mesannio="Mar";}
			if ($mes=="Apr"){$mesannio="Abr";}
			if ($mes=="May"){$mesannio="May";}
			if ($mes=="Jun"){$mesannio="Jun";}
			if ($mes=="Jul"){$mesannio="Jul";}
			if ($mes=="Aug"){$mesannio="Ago";}
			if ($mes=="Sep"){$mesannio="Set";}
			if ($mes=="Oct"){$mesannio="Oct";}
			if ($mes=="Nov"){$mesannio="Nov";}
			if ($mes=="Dec"){$mesannio="Dic";}
			$fecha=$dia.'/'.$mesannio.'/'.$anio;
		}

		if($forma=="reunion"){
			if ($dias=="Mon"){$diasemana="Lun";}
			if ($dias=="Tue"){$diasemana="Mar";}
			if ($dias=="Wed"){$diasemana="Mie";}
			if ($dias=="Thu"){$diasemana="Jue";}
			if ($dias=="Fri"){$diasemana="Vie";}
			if ($dias=="Sat"){$diasemana="Sab";}
			if ($dias=="Sun"){$diasemana="Dom";}

			if ($mes=="Jan"){$mesannio="01";}
			if ($mes=="Feb"){$mesannio="02";}
			if ($mes=="Mar"){$mesannio="03";}
			if ($mes=="Apr"){$mesannio="04";}
			if ($mes=="May"){$mesannio="05";}
			if ($mes=="Jun"){$mesannio="06";}
			if ($mes=="Jul"){$mesannio="07";}
			if ($mes=="Aug"){$mesannio="08";}
			if ($mes=="Sep"){$mesannio="09";}
			if ($mes=="Oct"){$mesannio="10";}
			if ($mes=="Nov"){$mesannio="11";}
			if ($mes=="Dec"){$mesannio="12";}
			$fecha=$diasemana.'/'.$dia.'/'.$mesannio.'/'.$anio;
		}

		if($forma=="info"){
			if ($mes=="Jan"){$mesannio="01";}
			if ($mes=="Feb"){$mesannio="02";}
			if ($mes=="Mar"){$mesannio="03";}
			if ($mes=="Apr"){$mesannio="04";}
			if ($mes=="May"){$mesannio="05";}
			if ($mes=="Jun"){$mesannio="06";}
			if ($mes=="Jul"){$mesannio="07";}
			if ($mes=="Aug"){$mesannio="08";}
			if ($mes=="Sep"){$mesannio="09";}
			if ($mes=="Oct"){$mesannio="10";}
			if ($mes=="Nov"){$mesannio="11";}
			if ($mes=="Dec"){$mesannio="12";}
			$fecha=$dia.'/'.$mesannio.'/'.$anio;
		}
		
		return $fecha;
	}

	function horaCorta($hora){ 
		$info=date('H:i', strtotime($hora));
		return $info;
	}

	function infoHora($hora){
		$horas=substr($hora,0,2);
		$minutos=substr($hora,3,2);
		if($horas>=01 and $horas<13){ $info=$horas.":".$minutos."AM"; }
		if($horas>=13 and $horas<=23){ $info=$horas.":".$minutos."PM"; }
		return $info;
	}

	function retraso($horaActividad,$horaActual){
		$datoHora   =substr($horaActual,0,2);
		$datoMinuto =substr($horaActual,3,2);
		$horaACT    =substr($horaActividad,0,2);
		$minutoACT  =substr($horaActividad,3,2);
		$retrasoHora=$datoHora-$horaACT;
		$retrasoMinutos=ceros($datoMinuto-$minutoACT,2);
		if($retrasoHora<0 or $retrasoHora==0){ $retrasoHora='00'; }
		if($retrasoMinutos<0){ $retrasoMinutos=0; }
		$retraso=$retrasoHora.'.'.$retrasoMinutos;
		return $retraso;
	}

	function infoRetraso($retraso,$informacion){
		if($informacion=="hora"){ $info=substr($retraso,0,1); }
		if($informacion=="minuto"){ $info=substr($retraso,2,2); }
		return $info;
	}

	function texto($texto){
		$info=utf8_encode($texto);
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE NUMERO
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function ceros($numero,$ceros){
		if($ceros=="2"){$resultado=sprintf("%02d",$numero);}
		if($ceros=="3"){$resultado=sprintf("%03d",$numero);}
		if($ceros=="4"){$resultado=sprintf("%04d",$numero);}
		if($ceros=="5"){$resultado=sprintf("%05d",$numero);}
		if($ceros=="6"){$resultado=sprintf("%06d",$numero);}
		return $resultado;
	}

	function moneda($monto){
		$resultado=number_format($monto,2,'.',',');
		return $resultado;
	}

	function numero($monto){
		$resultado=number_format($monto,0,'.',',');
		return $resultado;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// CONFIGURACION DEL SISTEMA
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function nombreSistema(){
		$conexion=conexionDB();
		$sql="SELECT sistema FROM sm_terminal_config WHERE id='1'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		return $info;
	}

	function versionSistema(){
		$conexion=conexionDB();
		$sql="SELECT versionSistema FROM sm_terminal_config WHERE id='1'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		return $info;
	}

	function terminal(){
		$conexion=conexionDB();
		$sql="SELECT terminal FROM sm_terminal_config WHERE id='1'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE ACTIVIDAD
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,$informacion){
		$conexion=conexionDB();
		if($informacion=="tipoActividad"){ $sql="SELECT tipoActividad FROM sm_terminal_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="temaActividad"){ $sql="SELECT temaActividad FROM sm_terminal_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="fechaActividad"){ $sql="SELECT fechaActividad FROM sm_terminal_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="horaActividad"){ $sql="SELECT horaActividad FROM sm_terminal_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="infoMultaporTardanza"){ $sql="SELECT mTardanza FROM sm_terminal_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="infoMultaPorFalta"){ $sql="SELECT mFalta FROM sm_terminal_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="archivoACT"){ $sql="SELECT archivoACT FROM sm_terminal_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="estado"){ $sql="SELECT estado FROM sm_terminal_actividades WHERE codigoActividad='$codigoActividad'"; }

		if($informacion=="ingreso"){ $sql="SELECT ingreso FROM sm_terminal_asistencia WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="salida"){ $sql="SELECT salida FROM sm_terminal_asistencia WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="retraso"){ $sql="SELECT retraso FROM sm_terminal_asistencia WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		
		if($informacion=="asistentes"){ $sql="SELECT COUNT(codigoActividad) FROM sm_terminal_asistencia WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="puntuales"){ $sql="SELECT COUNT(codigoActividad) FROM sm_terminal_asistencia WHERE (retraso=0 OR retraso<=0.15) AND codigoActividad='$codigoActividad'"; }
		if($informacion=="tardanza"){ $sql="SELECT COUNT(codigoActividad) FROM sm_terminal_asistencia WHERE retraso>0.15 AND codigoActividad='$codigoActividad'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE SOCIOS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoSocio($codigoSocio,$informacion){
		$conexion=conexionDB();
		if($informacion=="socios"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_terminal_socios"; }
		if($informacion=="verifica"){ $sql="SELECT codigoSocio FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="nombre"){ $sql="SELECT nombre FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="apPaterno"){ $sql="SELECT apPaterno FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="apMaterno"){ $sql="SELECT apMaterno FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="lotes"){ $sql="SELECT lotes FROM sm_terminal_socios WHERE codigoSocio='$codigoSocio'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		return $info;
	}
?>