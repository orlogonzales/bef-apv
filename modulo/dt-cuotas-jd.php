<?php
include('../php/conexion.php');
include('../php/funciones.php');

$j = 1;
$datos = [];

$idJuntaDirectiva = $_POST['idJuntaDirectiva'] ?? '';
$codigoCuota      = $_POST['codigoCuota'] ?? 'ALL';
$estadoPago       = $_POST['estadoPago'] ?? 'ALL';
$fechaInicio      = $_POST['fechaInicio'] ?? '';
$fechaFin         = $_POST['fechaFin'] ?? '';

$idJuntaDirectiva = $conexion->real_escape_string($idJuntaDirectiva);
$codigoCuota = $conexion->real_escape_string($codigoCuota);
$estadoPago = $conexion->real_escape_string($estadoPago);

$cEstadoPago = '';
switch ($estadoPago) {
    case 'SI':
        $cEstadoPago = "AND estadoPago='SP'";
        break;
    case 'NO':
        $cEstadoPago = "AND estadoPago='NP'";
        break;
}

$sql = "SELECT cs.idJuntaDirectiva, cs.codigoCuota, cs.codigoSocio, 
               CONCAT(s.nombre, ' ', s.apPaterno, ' ', s.apMaterno) AS nombreSocio, 
               cs.lotes, cs.montoCuota, cs.montoPago, cs.estadoPago 
        FROM sm_mod_cuotas_socios cs 
        INNER JOIN sm_socios s ON cs.codigoSocio = s.codigoSocio 
        WHERE cs.idJuntaDirectiva = '$idJuntaDirectiva' $cEstadoPago";

if ($codigoCuota !== 'ALL') {
    $sql .= " AND cs.codigoCuota = '$codigoCuota'";
}

$sql .= " ORDER BY cs.codigoCuota ASC, s.nombre ASC, s.apPaterno ASC, s.apMaterno ASC";

$consulta = $conexion->query($sql);
$totalCuotas = 0;
$montosPagados = 0;
$montosPorCobrar = 0;

while ($dato = $consulta->fetch_assoc()) {
    $codigoSocio = $dato['codigoSocio'];
    $codigoCuota = $dato['codigoCuota'];
    $nombreSocio = $dato['nombreSocio'];
    $lotesSocio = $dato['lotes'];
    $montoCuota = $dato['montoCuota'];
    $montoPago = $dato['montoPago'];
    $estadoPago = $dato['estadoPago'];
    $montoCuota=$montoCuota*$lotesSocio;

    $infoCodigoCuota = '<span class="badge bg-gray-5">' . htmlspecialchars($codigoCuota) . '</span>';
    $infoCodigoSocio = '<span class="badge bg-gray-5">' . htmlspecialchars($codigoSocio) . '</span>';
    $infoMontoCuota = 'S/. ' . moneda($montoCuota);

    // **Consulta única para obtener el concepto y la fecha de pago**
    $query = "SELECT conceptoCuota, fechaPago FROM sm_mod_cuotas WHERE codigoCuota = '$codigoCuota'";
    $resultado = $conexion->query($query)->fetch_assoc();
    $conceptoCuota = $resultado['conceptoCuota'] ?? '';
    $fechaPago = $resultado['fechaPago'] ?? '';
    $infoFechaPago = infoFecha($fechaPago, 'normal');

    // **Consulta única para obtener el monto pagado**
    $query = "SELECT SUM(monto) as montoTotal FROM sm_mod_caja WHERE movimiento='ING' 
              AND idJuntaDirectiva='$idJuntaDirectiva' AND codigoSocio='$codigoSocio' 
              AND codigoConcepto ='$codigoCuota'";
    $resultado = $conexion->query($query)->fetch_assoc();
    $montoPagado = $resultado['montoTotal'] ?? 0;

    // **Determinación del estado de pago y verificación en caja**
    if ($estadoPago == 'SP' && $montoPagado == $montoCuota) {
        $infoPago = '<span class="badge badge-success">S/P</span>';
        $infoCaja = '<span class="badge badge-info">VERIFICADO</span>';
    } elseif ($estadoPago == 'SP' && $montoPagado != $montoCuota) {
        $infoPago = '<span class="badge badge-success">P/O</span>';
        $infoCaja = '<span class="badge badge-danger">ERROR</span>';
    } else {
        $infoPago = '<span class="badge badge-warning">N/P</span>';
        $infoCaja = '';
    }

    $datos[] = [
        'Nro'           => ceros($j, 2),
        'codigoCuota'   => $infoCodigoCuota,
        'conceptoCuota' => htmlspecialchars($conceptoCuota),
        'codigoSocio'   => $infoCodigoSocio,
        'montoCuota'    => $infoMontoCuota,
        'fechaPago'     => $infoFechaPago,
        'nombreSocio'   => htmlspecialchars($nombreSocio),
        'lotesSocio'    => htmlspecialchars($lotesSocio),
        'infoPago'      => $infoPago,
        'infoCaja'      => $infoCaja,
    ];

    $totalCuotas += $montoCuota;
    $montosPagados += $montoPagado;
    $montosPorCobrar += ($montoPagado == 0) ? $montoCuota : 0;

    $j++;
}
$conexion->close();

$data = [
    'data'            => $datos,
    'montoCuotas'     => $totalCuotas,
    'totalCuotas'     => 'S/. ' . moneda($totalCuotas),
    'montosPagados'   => 'S/. ' . moneda($montosPagados),
    'montosPorCobrar' => 'S/. ' . moneda($montosPorCobrar),
];

echo json_encode($data);
exit;