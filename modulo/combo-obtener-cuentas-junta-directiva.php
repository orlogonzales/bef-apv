<?php
    include '../php/conexion.php';
    $idJuntaDirectiva = $_POST['idJuntaDirectiva'];

    if ($idJuntaDirectiva) {
        $sql="SELECT sm_junta_directiva_cuenta_banco.codigoCuenta, sm_bancos.entidad, sm_banco_cuentas.numeroCuenta, sm_banco_cuentas.detalle FROM sm_junta_directiva_cuenta_banco INNER JOIN sm_banco_cuentas ON sm_junta_directiva_cuenta_banco.codigoCuenta = sm_banco_cuentas.codigoCuenta INNER JOIN sm_bancos ON sm_banco_cuentas.codigoBanco = sm_bancos.codigoBanco WHERE idJuntaDirectiva = '$idJuntaDirectiva'";
        $consultaCuentas = $conexion->query($sql);

        $cuentas = array();
        while ($cuenta = $consultaCuentas->fetch_assoc()) {
            $codigoCuenta=$cuenta['codigoCuenta'];
            $entidad=$cuenta['entidad'];
            $numeroCuenta=$cuenta['numeroCuenta'];
            $detalle=$cuenta['detalle'];
            $infoCuenta=$entidad.'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;'.$numeroCuenta.'&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;'.$detalle;
            
            $cuentas[] = array(
                'codigoCuenta' => $codigoCuenta, 
                'infoCuenta' => $infoCuenta
            );
        }

        echo json_encode($cuentas);
    }

    $conexion->close();
?>