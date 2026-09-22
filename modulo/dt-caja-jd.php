<?php
include('../php/conexion.php');
include('../php/funciones.php');

$j = 1;
$datos = [];

$idJuntaDirectiva = $_POST['idJuntaDirectiva'] ?? '';
$movimiento       = $_POST['movimiento'] ?? '';
$tipoActividad    = $_POST['tipoActividad'] ?? '';
$codigoConcepto   = $_POST['codigoConcepto'] ?? '';
$codigoCuenta     = $_POST['codigoCuenta'] ?? '';
$fechaInicio      = $_POST['fechaInicio'] ?? '';
$fechaFin         = $_POST['fechaFin'] ?? '';

if($codigoConcepto!='ALL'){
    $cCodigoConcepto="AND codigoConcepto='$codigoConcepto'";
}else{
    $cCodigoConcepto="";
}

if(strlen($codigoCuenta)>0){
    $cCodigoCuenta="AND codigoCuenta='$codigoCuenta'";
}else{
    $cCodigoCuenta="";
}

$sql="SELECT idJuntaDirectiva, movimiento, fechaOperacion, tipoActividad, codigoSocio, codigoConcepto, codigoCuenta, monto, detalleConcepto, fecha, hora, usuario FROM sm_mod_caja WHERE idJuntaDirectiva = '$idJuntaDirectiva' AND movimiento='$movimiento' AND tipoActividad='$tipoActividad' $cCodigoConcepto $cCodigoConcepto AND fechaOperacion BETWEEN '$fechaInicio' AND '$fechaFin' ORDER BY fechaOperacion ASC";
$consulta = $conexion->query($sql);
$totalConsulta = 0;

while ($dato = $consulta->fetch_assoc()) {
    $idJuntaDirectiva   = $dato['idJuntaDirectiva'];
    $movimiento         = $dato['movimiento'];
    $fechaOperacion     = $dato['fechaOperacion'];
    $tipoActividad      = $dato['tipoActividad'];
    $codigoSocio        = $dato['codigoSocio'];
    $codigoConcepto     = $dato['codigoConcepto'];
    $codigoCuenta       = $dato['codigoCuenta'];
    $monto              = $dato['monto'];
    $detalleConcepto    = $dato['detalleConcepto'];
    $fechaRegistro      = $dato['fecha'];
    $horaRegistro       = $dato['hora'];
    $usuario            = $dato['usuario'];
    $infofechaOperacion = strtoupper(infofecha($fechaOperacion,'normal'));
    $infoCodigoSocio    ='<span class="badge bg-gray-3">'.$codigoSocio.'</span>';
    if($monto>0){
        $infoMonto   = 'S/. '.moneda($monto);
    }else{
        $infoMonto   = '';
    }

    if($movimiento=='ING'){
        $infoTipoMovimiento='<span class="badge badge-success">INGRESO</span>';
    }else{
        $infoTipoMovimiento='<span class="badge badge-danger">EGRESO</span>';
    }

    if($tipoActividad=='ASA'){
        $infoTipoOperacion='<span class="badge bg-gray-5">ASAMBLEA</span>';
    }else if($tipoActividad=='FAE'){
        $infoTipoOperacion='<span class="badge bg-gray-4">FAENA</span>';
    }else{
        $infoTipoOperacion='<span class="badge bg-gray-3">CUOTA</span>';
    }

    if($tipoActividad=='ASA' || $tipoActividad=='FAE'){
        $query="SELECT temaActividad FROM sm_mod_actividades WHERE codigoActividad = '$codigoConcepto'";
        $info = $conexion->query($query);
        $res = $info->fetch_assoc();
        $conceptoMovimiento = $res['temaActividad'];
    }else{
        $query="SELECT conceptoCuota FROM sm_mod_cuotas WHERE codigoCuota = '$codigoConcepto'";
        $info = $conexion->query($query);
        $res = $info->fetch_assoc();
        $conceptoMovimiento = $res['conceptoCuota'];
    }

    $query="SELECT CONCAT(nombre,' ',apPaterno,' ',apMaterno) AS nombreSocio FROM sm_socios WHERE codigoSocio = '$codigoSocio'";
    $info = $conexion->query($query);
    $res = $info->fetch_assoc();
    $nombreSocio = $res['nombreSocio'];

    $query = "SELECT COUNT(id) AS lotesSocio FROM sm_lotes_socio WHERE codigoSocio='$codigoSocio'";
    $info = $conexion->query($query);
    $resultado = $info->fetch_assoc();
    $lotesSocio = $resultado['lotesSocio'];

    $query = "SELECT nombre, paterno, materno FROM sm_usuarios WHERE dni='$usuario'";
    $info = $conexion->query($query);
    $res = $info->fetch_assoc();
    $nombre          = $res['nombre'];
    $paterno         = $res['paterno'];
    $materno         = $res['materno'];
    $usuarioRegistro = substr($nombre,0,1).substr($paterno,0,1).substr($materno,0,1);
    $infoRegistro    = '<span class="badge bg-gray-5">'.strtoupper($usuarioRegistro.' | '.infoFecha($fechaRegistro,'normal')).'</span>';

    $datos[] = [
        'Nro'                => ceros($j, 2),
        'tipoOperacion'      => $infoTipoOperacion,
        'conceptoMovimiento' => $conceptoMovimiento,
        'tipoMovimiento'     => $infoTipoMovimiento,
        'fechaOperacion'     => $infofechaOperacion,
        'codigoSocio'        => $infoCodigoSocio,
        'nombreSocio'        => $nombreSocio,
        'lotesSocio'         => $lotesSocio,
        'montoCaja'          => $infoMonto,
        'infoRegistro'       => $infoRegistro,
    ];

    $totalConsulta += $monto;

    $j++;
}
$conexion->close();

$data = [
    'data'              => $datos,
    'totalConsulta'     => $totalConsulta,
    'infoTotalConsulta' => 'S/. ' . moneda($totalConsulta)
];

echo json_encode($data);
exit;