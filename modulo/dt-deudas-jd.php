<?php
    include('../php/conexion.php');
    include ('../php/funciones.php');

    $idJuntaDirectiva=$_POST['idJuntaDirectiva'];
    $concepto=$_POST['concepto'];
    $sector=$_POST['sector'];
    $manzana=$_POST['manzana'];
    $ordenar=$_POST['ordenar'];

    if($concepto=='ASA'){
        $infoConcepto='ASAMBLEA';
    }

    if($concepto=='FAE'){
        $infoConcepto='FAENA';
    }

    if($concepto=='CUO'){
        $infoConcepto='CUOTA';
    }

    $j=1;
    $datos = array();

    if($ordenar=='LOTE'){
        $orden="CAST(SUBSTRING_INDEX(REPLACE(sm_lotes_socio.manzana, '-', '.'), '.', -1) AS UNSIGNED) ASC, CAST(SUBSTRING_INDEX(sm_lotes_socio.lote, '-', -1) AS UNSIGNED) ASC";
    }

    if($ordenar=='NOMBRE'){
        $orden="sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
    }

    if($ordenar=='APELLIDO'){
        $orden="sm_socios.nombre ASC, sm_socios.apPaterno ASC, sm_socios.apMaterno ASC";
    }

    if($sector!='ALL' && $manzana!='ALL'){
        $sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio WHERE sm_lotes_socio.sector = '$sector' AND sm_lotes_socio.manzana = '$manzana' ORDER BY $orden";
    }

    if($sector!='ALL' && $manzana=='ALL'){
        $sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio WHERE sm_lotes_socio.sector = '$sector' ORDER BY $orden";
    }

    if($sector=='ALL' && $manzana=='ALL'){
        $sql="SELECT sm_socios.codigoSocio, sm_socios.dni, sm_socios.tratamiento, sm_socios.nombre, sm_socios.apPaterno, sm_socios.apMaterno, sm_socios.genero, sm_socios.fechaNacimiento, sm_socios.fotoSocio, sm_socios.nacionalidad, sm_socios.estadoCivil, sm_socios.direccion, sm_socios.departamento, sm_socios.provincia, sm_socios.distrito, sm_socios.telefono, sm_socios.celular, sm_socios.observaciones, sm_lotes_socio.sector, sm_lotes_socio.manzana, sm_lotes_socio.lote, sm_lotes_socio.metraje FROM sm_socios INNER JOIN sm_lotes_socio ON sm_socios.codigoSocio = sm_lotes_socio.codigoSocio ORDER BY $orden";
    }

    $socios = $conexion->query($sql);
    $i=1;
    $totalDeudas = 0;

    while ($socio = $socios->fetch_assoc()) {
        $codigoSocio     = $socio['codigoSocio'];
        $dni             = $socio['dni'];
        $tratamiento     = $socio['tratamiento'];
        $nombre          = $socio['nombre'];
        $apPaterno       = $socio['apPaterno'];
        $apMaterno       = $socio['apMaterno'];
        $genero          = $socio['genero'];
        $fechaNacimiento = $socio['fechaNacimiento'];
        $fotoSocio       = $socio['fotoSocio'];
        $nacionalidad    = $socio['nacionalidad'];
        $estadoCivil     = $socio['estadoCivil'];
        $direccion       = $socio['direccion'];
        $departamento    = $socio['departamento'];
        $provincia       = $socio['provincia'];
        $distrito        = $socio['distrito'];
        $telefono        = $socio['telefono'];
        $celular         = $socio['celular'];
        $observaciones   = $socio['observaciones'];
        $sector          = $socio['sector'];
        $manzana         = $socio['manzana'];
        $lote            = $socio['lote'];
        $metraje         = $socio['metraje'];
        $infoMetraje     = $metraje.' m²';

        $cantidadLotes   = infoSocios($codigoSocio,'cantidadLotes');
        $nroObservaciones= infoSocios($codigoSocio,'observaciones');
        $nombreSocio     = trim($nombre.' '.$apPaterno.' '.$apMaterno);
        if($nroObservaciones>0){ $btn_lista=""; }else{ $btn_lista="disabled"; }

        if($concepto=='ASA' || $concepto=='FAE'){
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistros=$dato['cantidadRegistros'];
            $totalDeuda=$dato['totalDeuda'];
        }

        if($concepto=='CUO'){
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='idJuntaDirectiva='.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_cuotas_socios.id) AS cantidadRegistros, SUM(sm_mod_cuotas_socios.montoCuota) AS totalDeuda FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE $consultaJD sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistros=$dato['cantidadRegistros'];
            $totalDeuda=$dato['totalDeuda'];
        }

        if($concepto=='ALL'){
            $infoConcepto='TODOS';
            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='sm_mod_actividades.idJuntaDirectiva = '.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'ASA' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosASA=$dato['cantidadRegistros'];
            $totalDeudaASA=$dato['totalDeuda'];

            $query="SELECT COUNT(sm_mod_asistencia.id) AS cantidadRegistros, SUM(sm_mod_asistencia.multa) AS totalDeuda FROM sm_mod_asistencia INNER JOIN sm_mod_actividades ON sm_mod_asistencia.codigoActividad = sm_mod_actividades.codigoActividad WHERE $consultaJD sm_mod_asistencia.tipoActividad = 'FAE' AND sm_mod_asistencia.codigoSocio = '$codigoSocio' AND sm_mod_asistencia.multa > 0 AND sm_mod_asistencia.estadoPago = 'NP' AND sm_mod_asistencia.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosFAE=$dato['cantidadRegistros'];
            $totalDeudaFAE=$dato['totalDeuda'];

            if($idJuntaDirectiva='ALL'){
                $consultaJD='';
            }else{
                $consultaJD='idJuntaDirectiva='.$idJuntaDirectiva.' AND';
            }

            $query="SELECT COUNT(sm_mod_cuotas_socios.id) AS cantidadRegistros, SUM(sm_mod_cuotas_socios.montoCuota) AS totalDeuda FROM sm_mod_cuotas_socios INNER JOIN sm_mod_cuotas ON sm_mod_cuotas_socios.codigoCuota = sm_mod_cuotas.codigoCuota WHERE $consultaJD sm_mod_cuotas_socios.codigoSocio = '$codigoSocio' AND sm_mod_cuotas_socios.montoPago > 0 AND sm_mod_cuotas_socios.estadoPago = 'NP' AND sm_mod_cuotas_socios.caja <> 'SI' AND sm_mod_cuotas_socios.montoPagado = 0";
            $row=mysqli_query($conexion,$query);
            $dato=mysqli_fetch_array($row);
            $cantidadRegistrosCUO=$dato['cantidadRegistros'];
            $totalDeudaCUO=$dato['totalDeuda'];

            $cantidadRegistros=$cantidadRegistrosASA+$cantidadRegistrosFAE+$cantidadRegistrosCUO;
            $totalDeuda=$totalDeudaASA+$totalDeudaFAE+$totalDeudaCUO;
        }

        if($cantidadRegistros>0 && $totalDeuda>0){
            $infoMontoDeuda='S/.'.moneda($totalDeuda);
            $lotesSocio='<span class="badge badge-secondary">'.ceros($cantidadLotes,2).' LOTES</span>';
            $infoSector= '<span class="badge badge-primary">'.$sector.'</span>';
            $infoManzana= '<span class="badge badge-primary">'.$manzana.'</span>';
            $infoLote= '<span class="badge badge-primary">'.$lote.'</span>';
            if($cantidadRegistros>1){
                if($concepto=='CUO'){
                    $etiquetaConcepto='CUOTAS';
                }
                if($concepto=='ASA'){
                    $etiquetaConcepto='ASAMBLEAS';
                }
                if($concepto=='FAE'){
                    $etiquetaConcepto='FAENAS';
                }
                if($concepto=='ALL'){
                    $etiquetaConcepto='CONCEPTOS';
                }
            }else{
                if($concepto=='CUO'){
                    $etiquetaConcepto='CUOTA';
                }
                if($concepto=='ASA'){
                    $etiquetaConcepto='ASAMBLEA';
                }
                if($concepto=='FAE'){
                    $etiquetaConcepto='FAENA';
                }
                if($concepto=='ALL'){
                    $etiquetaConcepto='CONCEPTO';
                }
            }

            $btnConcepto = '<a href="javascript:void(0);" class="btn btn-xs btn-danger btn_infoDeuda" data-id="'.$codigoSocio.'" data-socio="'.$nombreSocio.'" data-concepto="'.$concepto.'" data-jd="'.$idJuntaDirectiva.'">'.ceros($cantidadRegistros,2).' '.$etiquetaConcepto.'</a>';

            $datos[] = array(
                'Nro'         => ceros($j,2),
                'sector'      => $infoSector,
                'manzana'     => $infoManzana,
                'lote'        => $infoLote,                
                'codigoSocio' => '<span class="badge badge-secondary">'.$codigoSocio.'</span>',
                'nombreSocio' => $nombreSocio,
                'lotesSocio'  => $lotesSocio,
                'concepto'    => $btnConcepto,
                'montoDeuda'  => $infoMontoDeuda,
            );

            $totalDeudas=$totalDeudas+$totalDeuda;
            $infoMontoDeudas='S/.'.moneda($totalDeudas);

            $j++;
        }
    }
    $conexion->close();

    $data=array(
        'data'            => $datos,
        'totalDeudas'     => $totalDeudas,
        'infoMontoDeudas' => $infoMontoDeudas,
    );

    echo json_encode($data);
    exit;
?>