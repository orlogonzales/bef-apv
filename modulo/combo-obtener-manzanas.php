<?php
    include '../php/conexion.php';
    $sector = $_POST['sector'];

    if ($sector) {
        //$sql="SELECT manzana FROM sm_lotes_socio WHERE sector = '$sector' GROUP BY manzana ORDER BY CAST(SUBSTRING_INDEX(manzana, '-', -1) AS UNSIGNED) ASC";
        $sql="SELECT manzana FROM sm_lotes_socio WHERE sector = '$sector' GROUP BY manzana ORDER BY CAST(SUBSTRING_INDEX(REPLACE(manzana, '-', '.'), '.', -1) AS UNSIGNED) ASC";
        $consultaManzanas = $conexion->query($sql);

        $manzanas = array();
        while ($manzana = $consultaManzanas->fetch_assoc()) {
            $manzanas[] = $manzana;
        }

        echo json_encode($manzanas);
    }

    $conexion->close();
?>