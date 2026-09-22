<?php
    include '../php/conexion.php';
    $codigoBanco = $_POST['codigoBanco'];

    if ($codigoBanco) {
        $sql="SELECT codigoCuenta, detalle FROM sm_banco_cuentas WHERE codigoBanco='$codigoBanco'";
        $consultaCuentas = $conexion->query($sql);

        $cuentas = array();
        while ($cuenta = $consultaCuentas->fetch_assoc()) {
            $cuentas[] = $cuenta;
        }

        echo json_encode($cuentas);
    }

    $conexion->close();
?>