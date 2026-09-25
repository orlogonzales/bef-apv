<?php 
	ini_set('display_errors', 'Off');
    ini_set('display_startup_errors', 'Off');
    ini_set('log_errors', 'Off');
    error_reporting(0);
	date_default_timezone_set("America/Lima");
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// RUTAS DEL SISTEMA
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$servidor=$_SERVER['HTTP_HOST'];
	if($servidor=='aap.bef-lacabanadelabuelo.com'){
		$urlSistema="http://{$_SERVER['HTTP_HOST']}/apv";
	}else{
		$urlSistema="https://{$_SERVER['HTTP_HOST']}/apv";
	}
	$urlSistema = htmlspecialchars( $urlSistema, ENT_QUOTES, 'UTF-8' );
	define("RUTA",$urlSistema);

	session_start();
	$rolUsuario=$_SESSION['rol_apv'];
	define('ROL', $rolUsuario);

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// CONEXION A LA BASE DE DATOS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function conexionDB(){
		$servidor = "localhost";
		$usuario  = "resikqao_admin_app_bef";
		$clave    = "3@hFwEQbZ#DDH5qGQAD29D";
		$bd       = "resikqao_db_apv";
		$conexion = mysqli_connect($servidor, $usuario, $clave, $bd) or die("Ha sucedido un error inexperado en la conexion de la base de datos");
		return $conexion;
	}

	function cerrarDB($conexion = null){
		if ($conexion instanceof mysqli) {
			return mysqli_close($conexion);
		}
		return true;
	}

	function conexionBEF(){
		$servidor = "localhost";
		$usuario  = "resikqao_admin_app_bef";
		$clave    = "3@hFwEQbZ#DDH5qGQAD29D";
		$bd       = "resikqao_db_bef_ventas";
		$conexion = mysqli_connect($servidor, $usuario, $clave, $bd) or die("Ha sucedido un error inexperado en la conexion de la base de datos");
		return $conexion;
	}

	function limpiar($tags){
		$tags = strip_tags($tags);
		return $tags;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE TIEMPO
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoTiempo($info){
		if($info=="fecha"){ $info=date('Y-m-d'); }
		if($info=="fechaHoy"){ $info=date('d/m/Y'); }
		if($info=="hora"){ $info=date("H:i:s"); }
		if($info=="primerDia"){ 
			$month = date('m');
			$year = date('Y');
			return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
		}
		if($info=="ultimoDia"){ 
			$month = date('m');
			$year = date('Y');
			$day = date("d", mktime(0,0,0, $month+1, 0, $year));
			return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
		}
		return $info;
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

	function infoFecha($fecha,$forma){
		$normaliza=fechaSQL($fecha);
		$fe=date('D-Y-m-d',strtotime($normaliza));
		$dias=substr($fe,0,3);

		$dia=substr($normaliza,8,2);
		$mes=substr($normaliza,5,2);
		$anio=substr($normaliza,0,4);

		if($forma=='valida'){
			try {
			    $d = new DateTime($fecha);
			    return 'VALIDO';
			} catch (Exception $e) {
			    return 'INVALIDO';
			}
		}

		if($forma=="year"){ //2016-12-04
			$anio=substr($fecha,0,4);
			$info=$anio;
		}

		if($forma=="registro"){ //2016
			$info=$dia.'/'.$mes.'/'.$anio;
		}

		if($forma=="resultados"){ //2016-12-04
			$dia=substr($fecha,8,2);
			$mes=substr($fecha,5,2);
			$anio=substr($fecha,0,4);
			$info=$dia.'/'.$mes.'/'.$anio;
		}

		if($forma=="fecha"){ //31/10/2016
			if ($dias=="Mon"){$diasemana="Lun";}
			if ($dias=="Tue"){$diasemana="Mar";}
			if ($dias=="Wed"){$diasemana="Mie";}
			if ($dias=="Thu"){$diasemana="Jue";}
			if ($dias=="Fri"){$diasemana="Vie";}
			if ($dias=="Sat"){$diasemana="Sab";}
			if ($dias=="Sun"){$diasemana="Dom";}

			if ($mes=="01"){$mesannio="Enero";}
			if ($mes=="02"){$mesannio="Febrero";}
			if ($mes=="03"){$mesannio="Marzo";}
			if ($mes=="04"){$mesannio="Abril";}
			if ($mes=="05"){$mesannio="Mayo";}
			if ($mes=="06"){$mesannio="Junio";}
			if ($mes=="07"){$mesannio="Julio";}
			if ($mes=="08"){$mesannio="Agosto";}
			if ($mes=="09"){$mesannio="Setiembre";}
			if ($mes=="10"){$mesannio="Octubre";}
			if ($mes=="11"){$mesannio="Noviembre";}
			if ($mes=="12"){$mesannio="Diciembre";}
			
			$info=$diasemana.' '.$dia.' de '.$mesannio.' de '.$anio;
		}

		if($forma=="larga"){ //2016-12-04
			$dia=substr($fecha,8,2);
			$mes=substr($fecha,5,2);
			$anio=substr($fecha,0,4);
			$fe=date('D-Y-m-d',strtotime($fecha));
			$dias=substr($fe,0,3);

			if ($dias=="Mon"){$diasemana="Lunes";}
			if ($dias=="Tue"){$diasemana="Martes";}
			if ($dias=="Wed"){$diasemana="Miércoles";}
			if ($dias=="Thu"){$diasemana="Jueves";}
			if ($dias=="Fri"){$diasemana="Viernes";}
			if ($dias=="Sat"){$diasemana="Sábado";}
			if ($dias=="Sun"){$diasemana="Domingo";}

			if ($mes=="01"){$mesannio="Enero";}
			if ($mes=="02"){$mesannio="Febrero";}
			if ($mes=="03"){$mesannio="Marzo";}
			if ($mes=="04"){$mesannio="Abril";}
			if ($mes=="05"){$mesannio="Mayo";}
			if ($mes=="06"){$mesannio="Junio";}
			if ($mes=="07"){$mesannio="Julio";}
			if ($mes=="08"){$mesannio="Agosto";}
			if ($mes=="09"){$mesannio="Setiembre";}
			if ($mes=="10"){$mesannio="Octubre";}
			if ($mes=="11"){$mesannio="Noviembre";}
			if ($mes=="12"){$mesannio="Diciembre";}

			$info=$diasemana.' '.$dia.' de '.$mesannio.' de '.$anio;
		}
		
		if($forma=="corta"){ //2016-12-04
			$dia=substr($fecha,8,2);
			$mes=substr($fecha,5,2);
			$anio=substr($fecha,0,4);
			$fe=date('D-Y-m-d',strtotime($fecha));
			$dias=substr($fe,0,3);

			if ($dias=="Mon"){$diasemana="Lun";}
			if ($dias=="Tue"){$diasemana="Mar";}
			if ($dias=="Wed"){$diasemana="Mie";}
			if ($dias=="Thu"){$diasemana="Jue";}
			if ($dias=="Fri"){$diasemana="Vie";}
			if ($dias=="Sat"){$diasemana="Sab";}
			if ($dias=="Sun"){$diasemana="Dom";}

			if ($mes=="01"){$mesannio="Ene";}
			if ($mes=="02"){$mesannio="Feb";}
			if ($mes=="03"){$mesannio="Mar";}
			if ($mes=="04"){$mesannio="Abr";}
			if ($mes=="05"){$mesannio="May";}
			if ($mes=="06"){$mesannio="Jun";}
			if ($mes=="07"){$mesannio="Jul";}
			if ($mes=="08"){$mesannio="Ago";}
			if ($mes=="09"){$mesannio="Set";}
			if ($mes=="10"){$mesannio="Oct";}
			if ($mes=="11"){$mesannio="Nov";}
			if ($mes=="12"){$mesannio="Dic";}

			$info=$diasemana.' '.$dia.' de '.$mesannio.' de '.$anio;
		}

		if($forma=="muycorta"){ //2016-12-04
			$dia=substr($fecha,8,2);
			$mes=substr($fecha,5,2);
			$anio=substr($fecha,0,4);
			$fe=date('D-Y-m-d',strtotime($fecha));
			$dias=substr($fe,0,3);

			if ($dias=="Mon"){$diasemana="Lun";}
			if ($dias=="Tue"){$diasemana="Mar";}
			if ($dias=="Wed"){$diasemana="Mie";}
			if ($dias=="Thu"){$diasemana="Jue";}
			if ($dias=="Fri"){$diasemana="Vie";}
			if ($dias=="Sat"){$diasemana="Sab";}
			if ($dias=="Sun"){$diasemana="Dom";}

			if ($mes=="01"){$mesannio="Ene";}
			if ($mes=="02"){$mesannio="Feb";}
			if ($mes=="03"){$mesannio="Mar";}
			if ($mes=="04"){$mesannio="Abr";}
			if ($mes=="05"){$mesannio="May";}
			if ($mes=="06"){$mesannio="Jun";}
			if ($mes=="07"){$mesannio="Jul";}
			if ($mes=="08"){$mesannio="Ago";}
			if ($mes=="09"){$mesannio="Set";}
			if ($mes=="10"){$mesannio="Oct";}
			if ($mes=="11"){$mesannio="Nov";}
			if ($mes=="12"){$mesannio="Dic";}

			$info=$diasemana.'/'.$dia.'/'.$mesannio.'/'.$anio;
		}

		if($forma=="normal"){ //2016-12-04
			$dia=substr($fecha,8,2);
			$mes=substr($fecha,5,2);
			$anio=substr($fecha,0,4);

			if ($mes=="01"){$mesannio="Ene";}
			if ($mes=="02"){$mesannio="Feb";}
			if ($mes=="03"){$mesannio="Mar";}
			if ($mes=="04"){$mesannio="Abr";}
			if ($mes=="05"){$mesannio="May";}
			if ($mes=="06"){$mesannio="Jun";}
			if ($mes=="07"){$mesannio="Jul";}
			if ($mes=="08"){$mesannio="Ago";}
			if ($mes=="09"){$mesannio="Set";}
			if ($mes=="10"){$mesannio="Oct";}
			if ($mes=="11"){$mesannio="Nov";}
			if ($mes=="12"){$mesannio="Dic";}

			$info=$dia.'/'.$mesannio.'/'.$anio;
		}

		if($forma=="reunion"){ //31/10/2016
			if ($dias=="Mon"){$diasemana="Lun";}
			if ($dias=="Tue"){$diasemana="Mar";}
			if ($dias=="Wed"){$diasemana="Mie";}
			if ($dias=="Thu"){$diasemana="Jue";}
			if ($dias=="Fri"){$diasemana="Vie";}
			if ($dias=="Sat"){$diasemana="Sab";}
			if ($dias=="Sun"){$diasemana="Dom";}

			$info=$diasemana.'/'.$dia.'/'.$mes.'/'.$anio;
		}

		if($forma=="info"){ //31/10/2016
			$dia=substr($fecha,0,2);
			$mes=substr($fecha,3,2);
			$anio=substr($fecha,6,4);
			$info=$dia.'/'.$mes.'/'.$anio;
		}
		
		return $info;
	}

	function fechas($fecha, $tipo) {
	    setlocale(LC_TIME, 'es_ES.UTF-8');
	    $timestamp = strtotime($fecha);

	    if ($tipo == 'corto') {
	        $resultado = strftime('%d de %B de %Y', $timestamp);
	    } elseif ($tipo == 'largo') {
	        $resultado = strftime('%A, %d de %B de %Y', $timestamp);
	    } else {
	        $resultado = 'Error Fecha';
	    }
	    $resultado = ucfirst($resultado);

	    return $resultado;
	}

	function fechaSQL($fecha){
		$info =substr($fecha, 6).'-'.substr($fecha, 3,2).'-'.substr($fecha, 0,2);
		return $info;
	}

	function calculaEdad($fecha){
		list($ano,$mes,$dia) = explode("-",$fecha);
		$ano_diferencia = date("Y") - $ano;
		$mes_diferencia = date("m") - $mes;
		$dia_diferencia = date("d") - $dia;
		if ($dia_diferencia < 0 && $mes_diferencia <= 0)
		$ano_diferencia--;
		return $ano_diferencia;
	}

	function haceTiempo($fecha) {
		if(empty($fecha)) { return "No hay fecha"; }
		$intervalos = array("segundo", "minuto", "hora", "día", "semana", "mes", "año");
		$duraciones = array("60","60","24","7","4.35","12");
		$ahora = time();
		$Fecha_Unix = strtotime($fecha);		
		if(empty($Fecha_Unix)) { return "Fecha incorracta"; }
		if($ahora > $Fecha_Unix) {   
			  $diferencia     =$ahora - $Fecha_Unix;
			  $tiempo         = "Hace";
		} else {
			  $diferencia     = $Fecha_Unix -$ahora;
			  $tiempo         = "Dentro de";
		}
		for($j = 0; $diferencia >= $duraciones[$j] && $j < count($duraciones)-1; $j++) { $diferencia /= $duraciones[$j]; }
		$diferencia = round($diferencia);		
		if($diferencia != 1) {
			$intervalos[5].="e"; //MESES
			$intervalos[$j].= "s";
		}
		return "$tiempo $diferencia $intervalos[$j]";
	}

	function verificafecha($input,$format=""){
		$separator_type= array(
			"/",
			"-",
			"."
			);
		foreach ($separator_type as $separator) {
			$find= stripos($input,$separator);
			if($find<>false){
				$separator_used= $separator;
			}
		}
		$input_array= explode($separator_used,$input);
		if ($format=="mdy") {
			return checkdate($input_array[0],$input_array[1],$input_array[2]);
		} elseif ($format=="ymd") {
			return checkdate($input_array[1],$input_array[2],$input_array[0]);
		} else {
			return checkdate($input_array[1],$input_array[0],$input_array[2]);
		}
		$input_array=array();
	}

	function diasEntre($fecha_a,$fecha_b){
		$fecha_a=strtotime($fecha_a);
		$fecha_b=strtotime($fecha_b);
		$diferencia=$fecha_b-$fecha_a;
		$info=((($diferencia/60)/60)/24);
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
	/// FUNCIONES DE TEXTO
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function cajaAlerta($titulo,$alinEncabezado,$texto,$alinTexto,$fondo){
		$caja='<div class="row"><div class="col-md-3"></div><div class="col-md-6"><div class="panel '.$fondo.' mt-20"><div class="panel-heading"><h6 class="panel-title '.$alinEncabezado.'">'.$titulo.'</h6></div><div class="panel-body '.$alinTexto.'">'.$texto.'</div></div></div><div class="col-md-3"></div></div>';
		return $caja;
	}

	function alerta($titulo,$alinEncabezado,$texto,$alinTexto,$fondo){
		$caja='<div class="panel '.$fondo.'"><div class="panel-heading"><h6 class="panel-title '.$alinEncabezado.'">'.$titulo.'</h6></div><div class="panel-body '.$alinTexto.'">'.$texto.'</div></div>';
		return $caja;
	}

	function texto($texto){
		//$info=utf8_encode($texto);
		$info=$texto;
		return $info;
	}

	function cTexto($texto){
		$info=utf8_decode($texto);
		return $info;
	}

	function registradoPor($usuario,$fecha,$hora,$estilo,$color){
		if($estilo=="SI"){ $info='<span class="label '.$color.'">'.datoUsuario($usuario,'iniciales').' | '.infoFecha($fecha,'normal').'</span>'; }
		if($estilo=="NO"){ $info=datoUsuario($usuario,'iniciales').' | '.infoFecha($fecha,'normal'); }
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// CONFIGURACION DEL SISTEMA
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function nombreSistema(){
		$conexion=conexionDB();
		$sql="SELECT sistema FROM sm_config WHERE id='1'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function versionSistema(){
		$conexion=conexionDB();
		$sql="SELECT versionSistema FROM sm_config WHERE id='1'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE LOGIN
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function login($usuario, $clave){
		$conexion=conexionDB();
		$sql="SELECT sm_usuarios.nombre, sm_usuarios.paterno, sm_usuarios.materno, sm_usuarios.dni, sm_usuarios.foto, sm_usuarios.rol, sm_usuarios_rol.detalle AS descripcionRol, sm_usuarios.estado FROM sm_usuarios INNER JOIN sm_usuarios_rol ON sm_usuarios.rol = sm_usuarios_rol.rol WHERE sm_usuarios.usuario = '$usuario' AND sm_usuarios.clave = '$clave'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$contarUsuario=mysqli_num_rows($row);
		$nombre=$dato['nombre'];
		$paterno=$dato['paterno'];
		$nombreUsuario = trim($nombre.' '.$paterno);
		$dni=$dato['dni'];
		$foto=$dato['foto'];
		$rol=$dato['rol'];
		$descripcionRol=$dato['descripcionRol'];
		$estado=$dato['estado'];
		
		$sql="SELECT idJuntaDirectiva FROM sm_junta_directiva ORDER BY id DESC LIMIT 1";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$contar=mysqli_num_rows($row);
		$idJuntaDirectiva=$dato['idJuntaDirectiva'];

		if($contar>0){
			$idJuntaDirectiva=$idJuntaDirectiva;
			$existeJD='SI';
		}else{
			$idJuntaDirectiva='';
			$existeJD='NO';
		}

		if($contarUsuario>0){
			if($estado=="ACT"){
				if($existeJD=='SI'){
					$sql="SELECT YEAR(sm_junta_directiva.fechaPeriodo) AS anioInicio, YEAR(sm_junta_directiva.fechaFinPeriodo) AS anioFin FROM sm_junta_directiva WHERE sm_junta_directiva.idJuntaDirectiva = '$idJuntaDirectiva'";
					$row=mysqli_query($conexion,$sql);
					$dato=mysqli_fetch_array($row);
					$contar=mysqli_num_rows($row);
					$anioInicio=$dato['anioInicio'];
					$anioFin=$dato['anioFin'];
					$infoGestion=$anioInicio.' - '.$anioFin;
				}else{
					$infoGestion='';
				}
				session_start();
				$_SESSION['login_apv']      = "LOGUEADO";
				$_SESSION['usuario_apv']    = $usuario;
				$_SESSION['dni_apv']        = $dni;
				$_SESSION['foto_apv']       = $foto;
				$_SESSION['rol_apv']        = $rol;
				$_SESSION['descripcionRol'] = $descripcionRol;
				$_SESSION['idJDActual']     = $idJuntaDirectiva;
				$_SESSION['infoJDAPV']      = $infoGestion;
				$login = array(
					'resultado'     => 'LOGIN_OK',
					'rol'           => $rol,
					'descripcionRol'=> $descripcionRol,
					'nombreUsuario' => $nombreUsuario
				);
			}else{
				$login = array(
					'resultado'     => 'USUARIO_INACTIVO',
					'rol'           => $rol,
					'descripcionRol'=> $descripcionRol,
					'nombreUsuario' => $nombreUsuario
				);
			}
		}else{
			$login = array(
				'resultado'     => 'USUARIO_NO_EXISTE',
				'rol'           => '',
				'descripcionRol'=> '',
				'nombreUsuario' => ''
			);
		}
		cerrarDB();
		return $login;
	}

	function verificaEstadoActividad($usuario){
			session_start();
			$login=$_SESSION['login_apv'];
			if($login=='LOGUEADO'){ $sesion='SESION_ACTIVA'; }else{ $sesion='SESION_CERRADA'; }
			return $sesion;
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE DATOS DE TABLAS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function datoTabla($info,$tabla){
		$conexion=conexionDB();
		if($tabla=="detalleRol"){ $sql="SELECT detalle FROM sm_usuarios_rol WHERE rol='$info'"; }
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE USUARIO
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function datoUsuario($dni,$info){
		$conexion=conexionDB();
		if($info=="nombre"){ $sql="SELECT nombre FROM sm_usuarios WHERE dni='$dni'"; }
		if($info=="iniciales"){ $sql="SELECT CONCAT(SUBSTRING(nombre, 1, 1),SUBSTRING(paterno, 1, 1),SUBSTRING(materno, 1, 1)) AS nombre FROM sm_usuarios WHERE dni='$dni'"; }
		if($info=="nombrePaterno"){ $sql="SELECT CONCAT(nombre,' ',paterno) as nombre  FROM sm_usuarios WHERE dni='$dni'"; }
		if($info=="nombreFull"){ $sql="SELECT CONCAT(nombre,' ',paterno,' ',materno) as nombre FROM sm_usuarios WHERE dni='$dni'"; }
		if($info=="nombreCorto"){ $sql="SELECT CONCAT(nombre,' ',paterno) FROM sm_usuarios WHERE dni='$dni'"; }
		if($info=="foto"){ $sql="SELECT foto FROM sm_usuarios WHERE dni='$dni'"; }
		if($info=="verificaDNI"){ $sql="SELECT dni FROM sm_usuarios WHERE dni='$dni'"; }
		if($info=="verificaUSER"){ $sql="SELECT usuario FROM sm_usuarios WHERE usuario='$dni'"; }
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=is_array($dato) ? $dato[0] : '';
		cerrarDB();
		return $info;
	}

	function infoRol($cod){
		$conexion=conexionDB();
		$sql="SELECT detalle FROM sm_usuarios_rol WHERE rol='$cod'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoNotas($categoria){
		$conexion=conexionDB();
		$sql="SELECT categoria FROM sm_notas_categorias WHERE id='$categoria'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}	

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE SOCIOS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoSocio($dni,$info){
		$conexion=conexionBEF();
		if($info=="lotes"){ $sql="SELECT COUNT(cliente) AS lotes FROM lotes WHERE cliente='$dni'"; }
		if($info=="tratamiento"){ $sql="SELECT tratamiento FROM clientes WHERE dni='$dni'"; }
		if($info=="nombre"){ $sql="SELECT nombre FROM clientes WHERE dni='$dni'"; }
		if($info=="paterno"){ $sql="SELECT paterno FROM clientes WHERE dni='$dni'"; }
		if($info=="materno"){ $sql="SELECT materno FROM clientes WHERE dni='$dni'"; }
		if($info=="genero"){ $sql="SELECT genero FROM clientes WHERE dni='$dni'"; }
		if($info=="nacimiento"){ $sql="SELECT nac FROM clientes WHERE dni='$dni'"; }
		if($info=="nacionalidad"){ $sql="SELECT nacionalidad FROM clientes WHERE dni='$dni'"; }
		if($info=="ciudad"){ $sql="SELECT ciudad FROM clientes WHERE dni='$dni'"; }
		if($info=="civil"){ $sql="SELECT civil FROM clientes WHERE dni='$dni'"; }
		if($info=="direccion"){ $sql="SELECT direccion FROM clientes WHERE dni='$dni'"; }
		if($info=="departamento"){ $sql="SELECT departamento FROM clientes WHERE dni='$dni'"; }
		if($info=="provincia"){ $sql="SELECT provincia FROM clientes WHERE dni='$dni'"; }
		if($info=="distrito"){ $sql="SELECT distrito FROM clientes WHERE dni='$dni'"; }
		if($info=="telefono"){ $sql="SELECT telefono FROM clientes WHERE dni='$dni'"; }
		if($info=="celular"){ $sql="SELECT celular FROM clientes WHERE dni='$dni'"; }
		if($info=="provincia"){ $sql="SELECT provincia FROM clientes WHERE dni='$dni'"; }
		if($info=="distrito"){ $sql="SELECT distrito FROM clientes WHERE dni='$dni'"; }
		if($info=="fechaAdjudica"){ $sql="SELECT fechaAdjudica FROM adjudicalote WHERE cliente='$dni' LIMIT 1"; }
		if($info=="recibo"){ $sql="SELECT recibo FROM adjudicalote WHERE cliente='$dni' LIMIT 1"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function verificaFotoDB($codigoSocio,$info){
		$conexion=conexionDB();
		if($info=="verifica"){
			$sql="SELECT fotoSocio FROM sm_socios WHERE codigoSocio = '$codigoSocio'"; 
			$row=mysqli_query($conexion,$sql);
			$dato=mysqli_fetch_array($row);
			$info=$dato[0];
			if($info!=''){ $info='FOTO_REGISTRADA'; }else{ $info='FOTO_NO_REGISTRADA'; }
		}
		if($info=="registraFoto"){
			$fichero=$codigoSocio.'.jpg';
			$sql="UPDATE sm_socios SET fotoSocio='$fichero' WHERE codigoSocio = '$codigoSocio'"; 
			$row=mysqli_query($conexion,$sql); 
			if($row){ $info='REGISTRO_ACTUALIZADO'; }else{ $info='ERROR_REGISTRO_ACTUALIZADO'; }
		}

		return $info;
		cerrarDB();
	}

	function infoLotes($dni,$info,$nro){
		$conexion=conexionBEF();
		if($info=='codigoLote'){
			$sql="SELECT codigoLote AS lotes FROM lotes WHERE cliente='$dni'";
			$rs=mysqli_query($conexion,$sql);
			$contar=mysqli_num_rows($rs);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				if($i==$nro){
					$dato=$n[0];
				}
				$i++;
			}
		}

		if($info=='sector'){
			$sql="SELECT sector AS lotes FROM lotes WHERE cliente='$dni'";
			$rs=mysqli_query($conexion,$sql);
			$contar=mysqli_num_rows($rs);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				if($i==$nro){
					$dato=$n[0];
				}
				$i++;
			}
		}

		if($info=='manzana'){
			$sql="SELECT manzana AS lotes FROM lotes WHERE cliente='$dni'";
			$rs=mysqli_query($conexion,$sql);
			$contar=mysqli_num_rows($rs);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				if($i==$nro){
					$dato=$n[0];
				}
				$i++;
			}
		}

		if($info=='lote'){
			$sql="SELECT lote AS lotes FROM lotes WHERE cliente='$dni'";
			$rs=mysqli_query($conexion,$sql);
			$contar=mysqli_num_rows($rs);
			$i=1;
			while($n=mysqli_fetch_array($rs)){
				if($i==$nro){
					$dato=$n[0];
				}
				$i++;
			}
		}
		cerrarDB();
		return $dato;
	}

	function verificaCodigoSocio($dni){
		$conexion=conexionDB();
		$sql="SELECT codigoSocio FROM sm_socios WHERE dni='$dni'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$codigoSocio=$dato[codigoSocio];
		
		if(strlen($codigoSocio)>0){
			$existe='SI';
			$codigoSocio=$codigoSocio;
		}else{
			$existe='NO';
			$codigoSocio='';
		}

		$informacion = array(
			'existe'      => $existe,
			'codigoSocio' => $codigoSocio
		);

		cerrarDB();
		return $informacion;
	}
	
	function socioRegistrado($codigoSocio){
		$conexion=conexionDB();
		$sql="SELECT sincronizado FROM sm_socios WHERE codigoSocio='$codigoSocio'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoSocios($codigoSocio,$informacion){
		$conexion=conexionDB();
		if($informacion=="codigoSocio"){ $sql="SELECT codigoSocio FROM sm_socios WHERE dni='$codigoSocio'"; }
		if($informacion=="verificaDNI"){ $sql="SELECT dni FROM sm_socios WHERE dni='$codigoSocio'"; }
		if($informacion=="cuentaDNI"){ $sql="SELECT COUNT(dni) FROM sm_socios WHERE dni='$codigoSocio'"; }
		if($informacion=="dni"){ $sql="SELECT dni FROM sm_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="nombreSolo"){ $sql="SELECT nombre FROM sm_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="apPaterno"){ $sql="SELECT apPaterno FROM sm_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="apMaterno"){ $sql="SELECT apMaterno FROM sm_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="nombre"){ $sql="SELECT CONCAT(nombre,' ',apPaterno,' ',apMaterno) FROM sm_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="nombreCorto"){ $sql="SELECT CONCAT(nombre,' ',apPaterno) FROM sm_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="nombreLargo"){ $sql="SELECT CONCAT(nombre,' ',apPaterno,' ',apMaterno) FROM sm_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="cantidadLotes"){ $sql="SELECT lotes FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="cosocio"){ $sql="SELECT relacion FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="CSdni"){ $sql="SELECT dni FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="CSnombre"){ $sql="SELECT CONCAT(nombre,' ',apPaterno,' ',apMaterno) AS CSnombre FROM sm_relacion_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="verifica"){ $sql="SELECT codigoSocio FROM sm_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="cantidadSocios"){ $sql="SELECT COUNT(codigoSocio) FROM sm_socios"; }
		if($informacion=="nroFaenas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="nroCuotas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio'"; }
		
		// INFORMACION DE ASAMBLEAS
		if($informacion=="nroDeudasAsambleas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND (estadoPago='NP' OR estadoPago='MP') AND codigoSocio='$codigoSocio'"; }
		if($informacion=="deudasAsambleas"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND asistio!='JU' AND (estadoPago='NP' OR estadoPago='MP') AND codigoSocio='$codigoSocio'"; }
		if($informacion=="nroAsambleas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="nroPagosAsambleas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND estadoPago='SP' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="total_ASA_DEU_PAG"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="pagosAsambleas"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio' AND tipoActividad='ASA' AND estadoPago='SP' AND caja='SI' AND porcentajePago='0'"; }
		if($informacion=="pagosAsambleas_SI_MP"){ $sql="SELECT SUM(montoPagado) AS total FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio' AND tipoActividad='ASA'"; }
		if($informacion=="total_ASA_justificados"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND asistio='JU' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="porPagarAsambleas_NO_MP"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio' AND tipoActividad='ASA' AND estadoPago='NP' AND asistio!='JU'"; }
		if($informacion=="porPagarAsambleas_SI_MP"){ $sql="SELECT SUM(multa-montoPagado) AS total FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio' AND tipoActividad='ASA' AND estadoPago='MP' AND asistio!='JU'"; }

		// INFORMACION DE FAENAS
		if($informacion=="nroDeudasFaenas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND estadoPago='NP' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="deudasFaenas"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND asistio!='JU' AND (estadoPago='NP' OR estadoPago='MP') AND codigoSocio='$codigoSocio'"; }
		if($informacion=="nroPagosFaenas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND estadoPago='SP' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="total_FAE_DEU_PAG"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="pagosFaenas"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio' AND tipoActividad='FAE' AND estadoPago='SP' AND caja='SI' AND porcentajePago='0'"; }
		if($informacion=="pagosFaenas_SI_MP"){ $sql="SELECT SUM(montoPagado) AS total FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio' AND tipoActividad='FAE'"; }
		if($informacion=="total_FAE_justificados"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND asistio='JU' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="porPagarFaenas_NO_MP"){ $sql="SELECT SUM(multa) AS total FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio' AND tipoActividad='FAE' AND estadoPago='NP' AND asistio!='JU'"; }
		if($informacion=="porPagarFaenas_SI_MP"){ $sql="SELECT SUM(multa-montoPagado) AS total FROM sm_mod_asistencia WHERE codigoSocio='$codigoSocio' AND tipoActividad='FAE' AND estadoPago='MP' AND asistio!='JU'"; }

		// INFORMACION DE CUOTAS
		if($informacion=="nroDeudasCuotas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_cuotas_socios WHERE (estadoPago='NP' OR estadoPago='MP') AND codigoSocio='$codigoSocio'"; }
		if($informacion=="deudasCuotas"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios WHERE (estadoPago='NP' OR estadoPago='MP') AND codigoSocio='$codigoSocio'"; }
		if($informacion=="nroPagosCuotas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_cuotas_socios WHERE estadoPago='SP' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="total_CUO_DEU_PAG"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="pagosCuotas"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' AND estadoPago='SP' AND montoPagado='0' AND porcentajePago='0'"; }
		if($informacion=="pagosCuotas_SI_MP"){ $sql="SELECT SUM(montoPagado) AS total FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' AND estadoPago='MP'"; }
		if($informacion=="porPagarCuotas_NO_MP"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' AND estadoPago='NP'"; }
		if($informacion=="porPagarCuotas_SI_MP"){ $sql="SELECT SUM(montoPago-montoPagado) AS total FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' AND estadoPago='MP'"; }

		// INFORMACION DE CANTIDAD DE LOTES
		if($informacion=="lotes"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'"; }

		if($informacion=="observaciones"){ $sql="SELECT COUNT(codigoSocio) FROM sm_socios_observacion WHERE codigoSocio='$codigoSocio'"; }
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=is_array($dato) ? $dato[0] : '';
		cerrarDB();
		return $info;
	}

	function infoCuentaError($codigoSocio,$dni,$informacion){
		$conexion=conexionDB();
		if($informacion=="segundaCuenta"){ $sql="SELECT codigoSocio FROM sm_socios WHERE dni='$dni' AND codigoSocio!='$codigoSocio'"; }
		
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function estadoCivil($cod){
		$conexion=conexionDB();
		$sql="SELECT detalle FROM sm_a_civil WHERE id='$cod'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoGenero($cod){
		$conexion=conexionDB();
		$sql="SELECT detalle FROM sm_a_genero WHERE id='$cod'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoDepartamento($cod){
		$conexion=conexionDB();
		$sql="SELECT detalle FROM sm_a_departamento WHERE id='$cod'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoRelacion($cod){
		$conexion=conexionDB();
		$sql="SELECT detalle FROM sm_a_cosocio WHERE id='$cod'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function informacionLote($codigoSocio, $codigoLote, $informacion){
		$conexion=conexionDB();
		if($informacion=="direccion"){ $sql="SELECT direccion FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio' AND codigoLote='$codigoLote'"; }
		
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}


	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE SINCRONIZACION
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoACT($tipo, $fecha){
		$conexion=conexionDB();
		if($tipo=="ASA"){
			$sql="SELECT COUNT(codigoActividad) AS total FROM sm_mod_actividades WHERE tipoActividad='$tipo' AND procesado!='SI' AND fechaActividad>='$fecha'";
		}
		if($tipo=="allASA"){
			$sql="SELECT COUNT(codigoActividad) AS total FROM sm_mod_actividades WHERE tipoActividad='$tipo'";
		}
		if($tipo=="FAE"){
			$sql="SELECT COUNT(codigoActividad) AS total FROM sm_mod_actividades WHERE tipoActividad='$tipo' AND procesado!='SI' AND fechaActividad>='$fecha'";
		}
		if($tipo=="allFAE"){
			$sql="SELECT COUNT(codigoActividad) AS total FROM sm_mod_actividades WHERE tipoActividad='$tipo'";
		}

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE ACTIVIDAD
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function codActividad($tipoActividad,$fecha,$hora,$usuario){
		$conexion=conexionDB();
		$sql="SELECT COUNT(codigoActividad) AS asambleas FROM sm_mod_actividades";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$nActividad=$dato[0]+1;
		$infoFecha =substr($fecha, 0,4).substr($fecha, 5,2).substr($fecha, 8,2);
		$infoHora =substr($hora, 0,2).substr($hora, 3,2).substr($hora, 6,2);
		if($tipoActividad=="ASAMBLEA"){ $tipoActividad="ASA".$nActividad; }
		if($tipoActividad=="FAENA"){ $tipoActividad="FAE".$nActividad; }
		$info=$tipoActividad.$infoFecha.$infoHora.$usuario;
		cerrarDB();
		return $info;
	}

	function infoTotalesGeneral($idJuntaDirectiva,$concepto){
		$conexion=conexionDB();
		if($idJuntaDirectiva=='ALL'){
			$consultaJD='';
		}else{
			$consultaJD="sm_mod_actividades.idJuntaDirectiva = '" . $idJuntaDirectiva . "' AND ";;
		}

		if($concepto=='ASA' || $concepto=='FAE'){
			$sql="SELECT SUM(sm_mod_asistencia.multa) AS total FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = '$concepto'";
		}

		if($idJuntaDirectiva=='ALL'){
			$consultaJD='';
		}else{
			$consultaJD="WHERE sm_mod_cuotas_socios.idJuntaDirectiva = '" . $idJuntaDirectiva . "'";;
		}

		if($concepto=='CUO'){
			$sql="SELECT SUM(sm_mod_cuotas_socios.montoCuota) AS total FROM sm_mod_cuotas_socios $consultaJD";
		}

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato['total'];
		cerrarDB();
		return $info;
	}

	function infoActividad($idJuntaDirectiva,$codigoActividad,$codigoSocio,$informacion){
		$conexion=conexionDB();
		if($informacion=="verifica"){ $sql="SELECT codigoActividad FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="aforo"){ $sql="SELECT COUNT(codigoSocio) FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="asistio"){ $sql="SELECT COUNT(codigoSocio) AS dato FROM sm_mod_asistencia WHERE asistio='SI' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="tarde"){ $sql="SELECT COUNT(codigoSocio) AS dato FROM sm_mod_asistencia WHERE asistio='SI' AND retraso>0.15 AND codigoActividad='$codigoActividad'"; }
		if($informacion=="justifico"){ $sql="SELECT COUNT(codigoSocio) AS dato FROM sm_mod_asistencia WHERE asistio='JU' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="falto"){ $sql="SELECT COUNT(codigoSocio) AS dato FROM sm_mod_asistencia WHERE asistio='NO' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="mtarde"){ $sql="SELECT SUM(multa) AS dato FROM sm_mod_asistencia WHERE asistio='SI' AND retraso>0.15 AND codigoActividad='$codigoActividad'"; }
		if($informacion=="mFalta"){ $sql="SELECT SUM(multa) AS dato FROM sm_mod_asistencia WHERE asistio='NO' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="mJUS"){ $sql="SELECT SUM(multa) AS dato FROM sm_mod_asistencia WHERE asistio='JU' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="mPagadas"){ $sql="SELECT SUM(MONTO) AS total FROM sm_mod_caja WHERE codigoConcepto='$codigoActividad'"; }
		if($informacion=="temaActividad"){ $sql="SELECT temaActividad FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="infoMultaporTardanza"){ $sql="SELECT mTardanza FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="infoMultaPorFalta"){ $sql="SELECT mFalta FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="cuentaBanco"){ $sql="SELECT codigoCuenta FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="fechaActividad"){ $sql="SELECT fechaActividad FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="horaActividad"){ $sql="SELECT horaActividad FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="totalDeudasASA"){ $sql="SELECT SUM(sm_mod_asistencia.multa) AS dato FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0"; }
		if($informacion=="totalDeudasFAE"){ $sql="SELECT SUM(sm_mod_asistencia.multa) AS dato FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0"; }
		if($informacion=="cantidadActividades"){ $sql="SELECT COUNT(codigoActividad) AS dato FROM sm_mod_actividades WHERE tipoActividad='$codigoActividad'"; }
		if($informacion=="asistenciaSocio"){ $sql="SELECT asistio FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="asistenciaTardeSocio"){ $sql="SELECT retraso FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND codigoSocio='$codigoSocio' AND retraso>0.15"; }
		if($informacion=="cantidadASA"){ $sql="SELECT COUNT(codigoActividad) AS total FROM sm_mod_actividades WHERE tipoActividad='ASA'"; }
		if($informacion=="cantidadFAE"){ $sql="SELECT COUNT(codigoActividad) AS total FROM sm_mod_actividades WHERE tipoActividad='FAE'"; }
		if($informacion=="archivoACT"){ $sql="SELECT archivoACT FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="procesado"){ $sql="SELECT procesado FROM sm_mod_actividades WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=="totalMultaACTSocio"){ $sql="SELECT multa FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="nroSociosPagaron"){ $sql="SELECT COUNT(codigoSocio) FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND estadoPago='SP'"; }
		if($informacion=="nroSociosFechasProgramas"){ $sql="SELECT COUNT(codigoSocio) FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND estadoPago='MP'"; }
		if($informacion=="nroSociosDeben"){ $sql="SELECT COUNT(codigoSocio) FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND estadoPago='NP' AND asistio!='JU'"; }

		if($informacion=="totalGlobalASA"){ $sql="SELECT SUM(multa) FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND asistio!='IN'"; }
		if($informacion=="totalGlobalASA_JUS"){ $sql="SELECT SUM(multa) FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND asistio='JU'"; }
		if($informacion=="totalGlobalASA_SP"){ $sql="SELECT SUM(multa) FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND estadoPago='SP'"; }
		if($informacion=="totalGlobalASA_FP"){ $sql="SELECT SUM(montoPagado) FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND estadoPago='MP'"; }

		if($informacion=="totalGlobalFAE"){ $sql="SELECT SUM(multa) FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND asistio!='IN'"; }
		if($informacion=="totalGlobalFAE_JUS"){ $sql="SELECT SUM(multa) FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND asistio='JU'"; }
		if($informacion=="totalGlobalFAE_SP"){ $sql="SELECT SUM(multa) FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND estadoPago='SP'"; }
		if($informacion=="totalGlobalFAE_FP"){ $sql="SELECT SUM(montoPagado) FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND estadoPago='MP'"; }

		if($informacion=="deudaExoneraciones"){ $sql="SELECT deuda FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="porcentajeExoneraciones"){ $sql="SELECT porcentaje FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="exoneradoExoneraciones"){ $sql="SELECT exonerado FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="totalPagoExoneraciones"){ $sql="SELECT totalPago FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="nroDocumentoExoneraciones"){ $sql="SELECT nroDocumento FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="observacionExoneraciones"){ $sql="SELECT observacion FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="codigoExoneracionExoneraciones"){ $sql="SELECT codigoExoneracion FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="fechaExoneraciones"){ $sql="SELECT fecha FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="horaExoneraciones"){ $sql="SELECT hora FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="usuarioExoneraciones"){ $sql="SELECT usuario FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoActividad='$codigoActividad'"; }
		if($informacion=="codOperacionCaja"){ $sql="SELECT codigoOperacion FROM sm_mod_caja WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoActividad'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoTerminal($codigoActividad,$terminal,$archivo,$informacion){
		$conexion=conexionDB();
		if($informacion=='verificaArchivo'){ $sql="SELECT archivo FROM sm_mod_asistencia_json WHERE codigoActividad='$codigoActividad' AND archivo='$archivo'"; }
		if($informacion=='subidosServer'){ $sql="SELECT COUNT(archivo) AS total FROM sm_mod_asistencia_json WHERE codigoActividad='$codigoActividad'"; }
		if($informacion=='totalAsistentes'){ $sql="SELECT SUM(socios) AS total FROM sm_mod_asistencia_json WHERE codigoActividad='$codigoActividad'"; }
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoAsistencia($codigoActividad,$codigoSocio){
		$conexion=conexionDB();
		$sql="SELECT asistio, retraso, multa FROM sm_mod_asistencia WHERE codigoActividad='$codigoActividad' AND codigoSocio='$codigoSocio'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$asistio=$dato[0];
		$retraso=$dato[1];
		$multa  =$dato[2];

		$regla=($retraso*1)/0.60;
		$minutos=($regla*60)/1;

		if($retraso>1){
			$retraso=ceros(round($minutos/60),2)." HRS";
		}else{
			$retraso=$minutos." MIN";
		}

		if($asistio=="IN"){ $info="INVITADO"; }
		if($asistio=="SI"){ if($retraso>0.15){ $info="TARDE ".$retraso; }else{ $info="ASISTIO"; } }
		if($asistio=="NO"){ $info="INASISTENCIA"; }
		if($asistio=="JU" and $multa>0){ $info="INASISTENCIA"; }
		if($asistio=="JU" and $retraso>0.15){ $info="TARDE ".$retraso; }
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE CUOTA
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function codCuota($fecha,$usuario){
		$conexion=conexionDB();
		$sql="SELECT COUNT(codigoCuota) AS cuotas FROM sm_mod_cuotas";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$cuotas=$dato[0]+1;
		$infoFecha =substr($fecha, 0,2).substr($fecha, 3,2).substr($fecha, 6,4);
		$info="CUO".$cuotas.$infoFecha.$usuario;
		cerrarDB();
		return $info;
	}

	function infoCuota($idJuntaDirectiva,$codigoCuota,$informacion){
		$conexion=conexionDB();
		if($informacion=="conceptoCuota"){ $sql="SELECT conceptoCuota AS total FROM sm_mod_cuotas WHERE codigoCuota='$codigoCuota'"; }
		if($informacion=="montoCuota"){ $sql="SELECT montoCuota FROM sm_mod_cuotas WHERE codigoCuota='$codigoCuota'"; }
		if($informacion=="fechaPago"){ $sql="SELECT fechaPago FROM sm_mod_cuotas WHERE codigoCuota='$codigoCuota'"; }
		if($informacion=="cuentaBanco"){ $sql="SELECT codigoCuenta FROM sm_mod_cuotas WHERE codigoCuota='$codigoCuota'"; }
		if($informacion=="aforo"){ $sql="SELECT COUNT(codigoCuota) AS total FROM sm_mod_cuotas_socios WHERE codigoCuota='$codigoCuota'"; }
		if($informacion=="verifica"){ $sql="SELECT codigoCuota FROM sm_mod_cuotas WHERE codigoCuota='$codigoCuota'"; }
		if($informacion=="pagaron"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_cuotas_socios WHERE estadoPago='SP' AND codigoCuota='$codigoCuota'"; }
		if($informacion=="fechasProgramadas"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_cuotas_socios WHERE estadoPago='MP' AND codigoCuota='$codigoCuota'"; }
		if($informacion=="deben"){ $sql="SELECT COUNT(codigoSocio) AS total FROM sm_mod_cuotas_socios WHERE estadoPago='NP' AND codigoCuota='$codigoCuota'"; }
		if($informacion=="totalCuotas"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios WHERE codigoCuota='$codigoCuota'"; }
		if($informacion=="totalPagados"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios WHERE estadoPago='SP' AND codigoCuota='$codigoCuota'"; }
		if($informacion=="totalDeben"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios WHERE estadoPago='NP' AND codigoCuota='$codigoCuota'"; }
		if($informacion=="totalFechasProgramadas"){ $sql="SELECT SUM(montoPagado) AS total FROM sm_mod_cuotas_socios WHERE estadoPago='MP' AND codigoCuota='$codigoCuota'"; }
		if($informacion=="totalDeudasCUO"){ $sql="SELECT SUM(sm_mod_cuotas_socios.montoCuota) AS total FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0"; }
		if($informacion=="cantidadCuotas"){ $sql="SELECT COUNT(codigoCuota) AS total FROM sm_mod_cuotas"; }
		if($informacion=="totalGlobalCUO"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios"; }
		if($informacion=="totalGlobalCUO_SP"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuotas_socios WHERE estadoPago='SP' AND caja='SI'"; }
		if($informacion=="totalGlobalCUO_FP"){ $sql="SELECT SUM(montoPagado) AS total FROM sm_mod_cuotas_socios"; }
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function verificaSocioACT($codigoConcepto,$codigoSocio,$informacion){
		$conexion=conexionDB();
		if($informacion=="verificaSocioCUO"){ $sql="SELECT codigoSocio FROM sm_mod_cuotas_socios WHERE codigoCuota='$codigoConcepto' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="verificaSocioASAFAE"){ $sql="SELECT codigoSocio FROM sm_mod_asistencia WHERE codigoActividad='$codigoConcepto' AND codigoSocio='$codigoSocio'"; }
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoDeudasCuota($codigoCuota, $codigoSocio, $informacion){
		$conexion=conexionDB();
		if($informacion=="montoPago"){ $sql="SELECT montoPago FROM sm_mod_cuotas_socios WHERE codigoSocio='$codigoSocio' AND codigoCuota='$codigoCuota'"; }
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE CAJA
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function codOperacion($tipoActividad,$fecha,$hora,$usuario){ /* FINAL */
		$conexion=conexionDB();
		$sql="SELECT COUNT(id) AS dato FROM sm_mod_caja";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$numOP=$dato[0]+1;
		$infoFecha =substr($fecha, 0,4).substr($fecha, 5,2).substr($fecha, 8,2);
		$infoHora =substr($hora, 0,2).substr($hora, 3,2).substr($hora, 6,2);
		$info=$tipoActividad.$numOP.$infoFecha.$infoHora.$usuario;
		cerrarDB();
		return $info;
	}

	function infoPago($codigoSocio,$codigoConcepto,$concepto,$informacion){
		$conexion=conexionDB();
		if($informacion=="conceptoPago"){ $sql="SELECT detalle FROM sm_a_concepto_pago WHERE id='$concepto'"; }
		if($informacion=="pagosAsambleas"){ $sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE tipoActividad='ASA' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="pagosFaenas"){ $sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE tipoActividad='FAE' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="pagosCuotas"){ $sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE tipoActividad='CUO' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="estadoPagoCuotaSocio"){ $sql="SELECT estadoPago FROM sm_mod_cuotas_socios WHERE codigoCuota='$codigoConcepto' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="montoCuotaSocio"){ $sql="SELECT montoPago FROM sm_mod_cuotas_socios WHERE codigoCuota='$codigoConcepto' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="totalPagoActividadSocio"){ $sql="SELECT multa FROM sm_mod_asistencia WHERE codigoActividad='$codigoConcepto' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="estadoPagoActividadSocio"){ $sql="SELECT estadoPago FROM sm_mod_asistencia WHERE codigoActividad='$codigoConcepto' AND codigoSocio='$codigoSocio'"; }
		if($informacion=="montoMultaSocio"){ $sql="SELECT multa FROM sm_mod_asistencia WHERE codigoActividad='$codigoConcepto' AND codigoSocio='$codigoSocio'"; }
		
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function verificaPago($codigoConcepto,$codigoSocio,$monto){
		$conexion=conexionDB();
		$sql="SELECT codigoOperacion FROM sm_mod_caja WHERE codigoConcepto='$codigoConcepto' AND codigoSocio='$codigoSocio' AND monto='$monto'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		if($info){ $info="OK"; }else{ $info="ERROR"; }
		cerrarDB();
		return $info;
	}

	function infoCaja($fechaIni,$fechaFin,$tipoMov,$actividad,$concepto,$usuario,$informacion){
		$conexion=conexionDB();
		if($usuario=="ALL"){ $consultaUsuario=""; }
		if($usuario!="ALL"){ $consultaUsuario=" AND usuario='$usuario'"; }
		if($informacion=="totalActividades"){
			if($actividad=="ASA"){ $tipoActividad=" AND tipoActividad='$actividad' "; }
			if($actividad=="FAE"){ $tipoActividad=" AND tipoActividad='$actividad' "; }
			if($actividad=="CUO"){ $tipoActividad=" AND tipoActividad='$actividad' "; }
			$sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE movimiento='$tipoMov' AND concepto!='PAR' $tipoActividad AND (fechaOperacion BETWEEN '$fechaIni' AND '$fechaFin') $consultaUsuario";
		}

		if($informacion=="totalING"){ $sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE movimiento='ING' AND concepto!='PAR' AND (fechaOperacion BETWEEN '$fechaIni' AND '$fechaFin') $consultaUsuario"; }
		if($informacion=="totalSAL"){ $sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE movimiento='SAL' AND concepto!='PAR' AND (fechaOperacion BETWEEN '$fechaIni' AND '$fechaFin') $consultaUsuario";}
		if($informacion=="totalPAR"){ $sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE movimiento='PAR'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function cobroCajero($nroDocumento){
		$conexion=conexionDB();
		$sql="SELECT usuario FROM sm_mod_caja WHERE nroDocumento='$nroDocumento'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}


	function infoTipoDOC($tipo){
		$conexion=conexionDB();
		$sql="SELECT detalle FROM sm_a_doc_pago WHERE id='$tipo'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=is_array($dato) ? $dato[0] : '';
		cerrarDB();
		return $info;
	}

	function infoTipoDOCShort($tipo){
		$conexion=conexionDB();
		$sql="SELECT corto FROM sm_a_doc_pago WHERE id='$tipo'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function verificaDOC($nroDocumento,$tipoDocumento){
		$conexion=conexionDB();
		$sql="SELECT nroDocumento FROM sm_mod_caja WHERE nroDocumento='$nroDocumento' AND tipoDocumento='$tipoDocumento'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		if($info==$nroDocumento){ $info="EXISTE"; }else{ $info="NOEXISTE"; }
		return $info;
	}

	function montoDisponible($fechaInicio,$fechaFin){
		if($fechaInicio==""){ $fechaInicio="1900-01-01"; }else{ $fechaInicio=$fechaInicio; }
		if($fechaFin==""){ $fechaFin=infoTiempo('fecha'); }else{ $fechaFin=$fechaFin; }

		$totalING =infoCaja($fechaInicio,$fechaFin,'','','','ALL','totalING');
		$totalSAL =infoCaja($fechaInicio,$fechaFin,'','','','ALL','totalSAL');;
		$totalPAR =infoPartida($codigoPartida,'totalPartidas');

		if($totalING>0){ $totalING=$totalING; }else{ $totalING=0; }
		if($totalSAL>0){ $totalSAL=$totalSAL; }else{ $totalSAL=0; }
		if($totalPAR>0){ $totalPAR=$totalPAR; }else{ $totalPAR=0; }

		$info=$totalING-($totalSAL+$totalPAR);
		return $info;
	}	


	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE PARTIDAS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoPartida($codigoPartida,$informacion){
		$conexion=conexionDB();
		if($informacion=="codigoPartida"){ $sql="SELECT codigoPartida FROM sm_partidas WHERE codigoOperacion='$codigoPartida'"; }
		if($informacion=="contarPartidas"){ $sql="SELECT COUNT(codigoPartida) AS total FROM sm_partidas"; }
		if($informacion=="conceptoPartida"){ $sql="SELECT concepto FROM sm_partidas WHERE codigoPartida='$codigoPartida'"; }
		if($informacion=="montoPartida"){ $sql="SELECT monto FROM sm_partidas WHERE codigoPartida='$codigoPartida'"; }
		if($informacion=="usuarioPartida"){ $sql="SELECT usuarioPartida FROM sm_partidas WHERE codigoPartida='$codigoPartida'"; }
		if($informacion=="verificaPartida"){ $sql="SELECT codigoPartida FROM sm_partidas WHERE codigoPartida='$codigoPartida'"; }
		if($informacion=="totalSAL"){ $sql="SELECT SUM(monto) AS total from sm_mod_caja WHERE movimiento='SAL' AND codigoConcepto='$codigoPartida'"; }
		if($informacion=="totalING"){ $sql="SELECT SUM(monto) AS total from sm_mod_caja WHERE movimiento='ING' AND codigoConcepto='$codigoPartida'"; }
		if($informacion=="totalPartidas"){ $sql="SELECT SUM(monto) AS total from sm_partidas"; }
		if($informacion=="totalPartidasDispuesto"){ $sql="SELECT SUM(monto) AS total from sm_mod_caja WHERE movimiento='SAL' AND concepto='PAR'"; }
		if($informacion=="totalPartidasCierre"){ $sql="SELECT SUM(monto) AS total from sm_mod_caja WHERE movimiento='ING' AND concepto='PAR'"; }
		if($informacion=="montoGastado"){ $sql="SELECT SUM(monto) FROM sm_mod_caja WHERE movimiento='SAL' AND codigoConcepto='$codigoPartida'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE APERTURA DE PAGOS PROGRAMADOS POR FECHA
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function codprogramacion($codigoSocio,$cuota,$fechaProgramada,$conceptoPago){
		$infoFecha =substr($fechaProgramada, 0,4).substr($fechaProgramada, 5,2).substr($fechaProgramada, 8,2);
		$info='FP'.$cuota.$codigoSocio.$conceptoPago.$infoFecha;
		return $info;
	}

	function infoPagoFechas($codigoSocio,$codigoConcepto,$informacion){
		$conexion=conexionDB();
		if($informacion=="programadoPor"){ $sql="SELECT usuario FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto' LIMIT 1"; }
		if($informacion=="FPSocioConcepto"){ $sql="SELECT COUNT(cuota) AS total FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto'"; }
		if($informacion=="contarFechasPagadas"){ $sql="SELECT COUNT(cuota) AS total FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto' AND estadoPago='PGD'"; }
		if($informacion=="totalFechasPagadas"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto' AND estadoPago='PGD'"; }
		if($informacion=="totalFechasSaldo"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto' AND estadoPago='PEN'"; }
		if($informacion=="totalPagadosConcepto"){ $sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuentas WHERE codigoConcepto='$codigoConcepto' AND estadoPago='PGD'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function porcentajePago($codigoSocio,$codigoConcepto,$totalPago){
		$conexion=conexionDB();
		$sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto' AND estadoPago='PGD'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$pagos=$dato[0];
		if($pagos>0){
			$info=floor(($pagos*100)/($totalPago));
		}else{
			$info=0;
		}
		cerrarDB();
		return $info;
	}

	function conceptoProgramado($codigoSocio,$codigoConcepto){
		$conexion=conexionDB();
		$sql="SELECT COUNT(cuota) AS total FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function verificaPagosProgramados($codigoConcepto,$codigoSocio,$monto){
		$conexion=conexionDB();
		$sql="SELECT SUM(montoPago) AS total FROM sm_mod_cuentas WHERE codigoSocio='$codigoSocio' AND codigoConcepto='$codigoConcepto' AND estadoPago='PGD'";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		if($monto>0){
			if($info==""){ $info=0; }else{ $info=$info; }
			if($info>=$monto){ $info="OK"; }else{ $info="ERROR"; }
		}else{
			$info="ERROR";
		}
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE BANCOS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function codigoBanco(){
		$conexion=conexionDB();
		$sql="SELECT SUBSTRING(codigoBanco FROM 3) FROM sm_bancos WHERE id=(SELECT MAX(id) FROM sm_bancos)";
		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$entidad=$dato[0]+1;
		$info='EB'.$entidad;
		cerrarDB();
		return $info;
	}

	function infoBancos($codigoBanco,$informacion){
		$conexion=conexionDB();
		if($informacion=="bancosRegistrados"){ $sql="SELECT COUNT(codigoBanco) AS total FROM sm_bancos"; }
		if($informacion=="detalleEntidad"){ $sql="SELECT entidad FROM sm_bancos WHERE codigoBanco='$codigoBanco'"; }
		if($informacion=="registrosHistorial"){ $sql="SELECT COUNT(ID) AS total FROM sm_bancos_operaciones"; }
		if($informacion=="chequerasEmitidosBanco"){ $sql="SELECT COUNT(codigoBanco) AS total FROM sm_chequera WHERE codigoBanco='$codigoBanco'"; }
		if($informacion=="chequesEmitidosBanco"){ $sql="SELECT COUNT(codigoBanco) AS total FROM sm_mod_caja WHERE codigoBanco='$codigoBanco' AND codigoChequera!='' AND nroDocumento!=''"; }
		if($informacion=="cuentasenBanco"){ $sql="SELECT COUNT(codigoCuenta) AS cuentas FROM sm_banco_cuentas WHERE codigoBanco='$codigoBanco'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoCuentas($codigoCuenta,$codigoBanco,$informacion){
		$conexion=conexionDB();
		if($informacion=="codigoBanco"){ $sql="SELECT codigoBanco FROM sm_banco_cuentas WHERE codigoCuenta='$codigoCuenta'"; }
		if($informacion=="estadoCuenta"){ $sql="SELECT estado FROM sm_banco_cuentas WHERE codigoCuenta='$codigoCuenta'"; }
		if($informacion=="detalleCuenta"){ $sql="SELECT detalle FROM sm_banco_cuentas WHERE codigoCuenta='$codigoCuenta'"; }
		if($informacion=="numeroCuenta"){ $sql="SELECT numeroCuenta FROM sm_banco_cuentas WHERE codigoCuenta='$codigoCuenta'"; }
		if($informacion=="cuentasRegistradas"){ $sql="SELECT SUBSTRING(codigoCuenta,4,3) FROM sm_banco_cuentas WHERE id=(SELECT MAX(id) FROM sm_banco_cuentas)"; }
		if($informacion=="verificaCTA"){ $sql="SELECT codigoCuenta FROM sm_banco_cuentas WHERE codigoCuenta='$codigoCuenta'"; }
		if($informacion=="chequesEmitidosCTA"){ $sql="SELECT COUNT(nroDocumento) AS cheques FROM sm_mod_caja WHERE codigoBanco='$codigoBanco' AND codigoCuenta='$codigoCuenta'"; }
		if($informacion=="chequerasEmitidasCTA"){ $sql="SELECT COUNT(codigoChequera) AS chequeras FROM sm_chequera WHERE codigoBanco='$codigoBanco' AND codigoCuenta='$codigoCuenta'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoCuentasPagos($codigoCuenta,$informacion){
		$conexion=conexionDB();
		if($informacion=="ASA"){ $sql="SELECT codigoCuenta FROM sm_banco_cuentas_pago WHERE conceptopago='ASA'"; }
		if($informacion=="FAE"){ $sql="SELECT codigoCuenta FROM sm_banco_cuentas_pago WHERE conceptopago='FAE'"; }
		if($informacion=="CUO"){ $sql="SELECT codigoCuenta FROM sm_banco_cuentas_pago WHERE conceptopago='CUO'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoConceptoCuentas($concepto){
		if($concepto=="ASA"){ $info="PAGOS ASAMBLEAS"; }
		if($concepto=="FAE"){ $info="PAGOS FAENAS"; }
		if($concepto=="CUO"){ $info="PAGOS DE CUOTAS"; }
		return $info;
	}

	function infoChequeras($codigoChequera,$codigoBanco,$codigoCuenta,$informacion){
		$conexion=conexionDB();
		if($informacion=="chequerasRegistradosCTA"){ $sql="SELECT SUBSTRING(codigoChequera,2,5) FROM sm_chequera WHERE codigoCuenta='$codigoCuenta' AND id=(SELECT MAX(id) FROM sm_chequera)"; }
		if($informacion=="chequerasRegistrados"){ $sql="SELECT COUNT(codigoChequera) AS total FROM sm_chequera"; }
		if($informacion=="verificaChequera"){ $sql="SELECT codigoChequera FROM sm_chequera WHERE codigoChequera='$codigoChequera'"; }
		if($informacion=="emitidosChequera"){ $sql="SELECT COUNT(id) AS total FROM sm_mod_caja WHERE codigoChequera='$codigoChequera'"; }
		if($informacion=="entidadBancaria"){ $sql="SELECT codigoBanco FROM sm_chequera WHERE codigoChequera='$codigoChequera'"; }
		if($informacion=="detalleChequera"){ $sql="SELECT detalle FROM sm_chequera WHERE codigoChequera='$codigoChequera'"; }
		if($informacion=="codigoCuenta"){ $sql="SELECT codigoCuenta FROM sm_chequera WHERE codigoChequera='$codigoChequera'"; }
		if($informacion=="totalChequera"){ $sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE codigoChequera='$codigoChequera'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoCheques($fechaIni,$fechaFin,$entidadBancaria,$codigoCuenta,$codigoChequera,$usuario,$informacion){
		$conexion=conexionDB();
		if($usuario=="ALL"){ $infoUsuario=""; }else{ $infoUsuario="AND beneficiario='$usuario'";}
		if($entidadBancaria=="ALL"){ $infoEntidadBancaria=""; }else{ $infoEntidadBancaria="AND codigoBanco='$entidadBancaria'"; }
		if($codigoCuenta=="ALL"){ $infoCodigoCuenta=""; }else{ $infoCodigoCuenta="AND codigoCuenta='$codigoCuenta'"; }
		if($codigoChequera=="ALL"){ $infoCodigoChequera=""; }else{ $infoCodigoChequera="AND codigoChequera='$codigoChequera'"; }
		
		if($informacion=="chequesEmitidosPAR"){ $sql="SELECT COUNT(id) AS total FROM sm_mod_caja WHERE movimiento='PAR' AND tipoDocumento='CHB' AND fechaOperacion BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		if($informacion=="totalChequesPAR"){ $sql="SELECT SUM(monto) AS total FROM sm_mod_caja WHERE movimiento='PAR' AND tipoDocumento='CHB' AND fechaOperacion BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		if($informacion=="chequesEmitidosHOY"){ $sql="SELECT COUNT(id) AS total FROM sm_cheques WHERE tipoCheque='VAR' AND fecha BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		if($informacion=="totalChequesHOY"){ $sql="SELECT SUM(monto) AS total FROM sm_cheques WHERE tipoCheque='VAR' AND fecha BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		if($informacion=="chequesEmitidos"){ $sql="SELECT COUNT(id) AS total FROM sm_cheques WHERE fechaEmision BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		if($informacion=="totalCheques"){ $sql="SELECT SUM(monto) AS total FROM sm_cheques WHERE fechaEmision BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }

		//INFORMACION GENERAL
		if($informacion=="nroEmitidosFechasVAR"){ $sql="SELECT COUNT(id) AS total FROM sm_cheques WHERE tipoCheque='VAR' AND fechaEmision BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		if($informacion=="nroEmitidosFechasPAR"){ $sql="SELECT COUNT(id) AS total FROM sm_cheques WHERE tipoCheque='PAR' AND fechaEmision BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		
		if($informacion=="totalEmitidosFechas"){ $sql="SELECT SUM(monto) AS total FROM sm_cheques WHERE fechaEmision BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		if($informacion=="totalEmitidosFechasPAR"){ $sql="SELECT SUM(monto) AS total FROM sm_cheques WHERE tipoCheque='PAR' AND fechaEmision BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }
		if($informacion=="totalEmitidosFechasVAR"){ $sql="SELECT SUM(monto) AS total FROM sm_cheques WHERE tipoCheque='VAR' AND fechaEmision BETWEEN '$fechaIni' AND '$fechaFin' $infoUsuario $infoEntidadBancaria $infoCodigoCuenta $infoCodigoChequera"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE CHEQUES EMITIDOS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infocheque($codigoBanco,$codigoCuenta,$codigoChequera,$nroCheque,$fechaIni,$fechaFin,$beneficiario,$informacion){
		$conexion=conexionDB();
		if($informacion=="verficaBeneficiario"){ $sql="SELECT documento FROM sm_cheques_beneficiarios WHERE documento='$beneficiario'"; }
		if($informacion=="nombreBeneficiario"){ $sql="SELECT nombre FROM sm_cheques_beneficiarios WHERE documento='$beneficiario'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE TOTAL DE DEUDAS DESDE EL INICIO DEL SISTEMA HASTA LA FECHA ACTUAL
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoDeudas($informacion){
		$conexion=conexionDB();
		if($informacion=="ASA"){ $sql="SELECT SUM(multa) as TOTAL FROM sm_mod_asistencia WHERE tipoActividad='ASA' AND (asistio!='IN' OR asistio!='JU') AND estadoPago='NP'"; }
		if($informacion=="FAE"){ $sql="SELECT SUM(multa) as TOTAL FROM sm_mod_asistencia WHERE tipoActividad='FAE' AND (asistio!='IN' OR asistio!='JU') AND estadoPago='NP'"; }
		if($informacion=="CUO"){ $sql="SELECT SUM(montoPago) as TOTAL FROM sm_mod_cuotas_socios WHERE estadoPago='NP'"; }

		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// GENERADOR DE CODIGOS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function generaCodigo($largo,$documento){ 
		srand((double)microtime()*rand(1000000,9999999)); 
		$letras=array(); 
		$uId=$documento.'-'; 
		for($i=65;$i<90;$i++){ 
			array_push($letras,chr($i)); 
		}
		for($i=48;$i<57;$i++){ 
			array_push($letras,chr($i)); 
		} 
		for($i=0;$i<$largo;$i++){ 
			$uId.=$letras[rand(0,count($letras))]; 
		}
		return $uId;
	}

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// SECCION DE EXONERACIONES
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function infoExoneraciones($codigoSocio,$codigoActividad,$informacion){
		$conexion=conexionDB();
		if($informacion=="totalExoneradoSocio"){ $sql="SELECT SUM(exonerado) FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="totalExoneradoSocioASA"){ $sql="SELECT SUM(exonerado) FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND  tipoActividad='ASA'"; }
		if($informacion=="totalExoneradoSocioFAE"){ $sql="SELECT SUM(exonerado) FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND  tipoActividad='FAE'"; }
		if($informacion=="totalExoneradoSocioCUO"){ $sql="SELECT SUM(exonerado) FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND  tipoActividad='CUO'"; }

		if($informacion=="totalPagosExoneradoSocio"){ $sql="SELECT SUM(totalPago) FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio'"; }
		if($informacion=="totalPagosExoneradoSocioASA"){ $sql="SELECT SUM(totalPago) FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND  tipoActividad='ASA'"; }
		if($informacion=="totalPagosExoneradoSocioFAE"){ $sql="SELECT SUM(totalPago) FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND  tipoActividad='FAE'"; }
		if($informacion=="totalPagosExoneradoSocioCUO"){ $sql="SELECT SUM(totalPago) FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND  tipoActividad='CUO'"; }


		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}

	function infoExoneracionesALT($codigoSocio,$codigoExoneracion,$informacion){
		$conexion=conexionDB();
		if($informacion=="codigoActividad"){ $sql="SELECT codigoActividad FROM sm_mod_exoneracion WHERE codigoSocio='$codigoSocio' AND codigoExoneracion='$codigoExoneracion'"; }


		$row=mysqli_query($conexion,$sql);
		$dato=mysqli_fetch_array($row);
		$info=$dato[0];
		cerrarDB();
		return $info;
	}
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// FUNCIONES DE RETRASO
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function retraso($horaActividad,$horaActual){
		$datoHora   =substr($horaActual,0,2);
		$datoMinuto =substr($horaActual,3,2);
		$horaACT    =substr($horaActividad,0,2);
		$minutoACT  =substr($horaActividad,3,2);
		$retrasoHora=$datoHora-$horaACT;
		$retrasoMinutos=ceros($datoMinuto-$minutoACT,2);
		$info=array(
			'retrasoHora' => $retrasoHora,
			'retrasoMinutos' => $retrasoMinutos
		);
		return $info;
	}

	function infoRetraso($retraso,$informacion){
		if($informacion=="hora"){ $info=substr($retraso,0,1); }
		if($informacion=="minuto"){ $info=substr($retraso,2,2); }
		return $info;
	}
?>