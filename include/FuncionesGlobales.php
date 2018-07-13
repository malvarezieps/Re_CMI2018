<?php
// VARIABLES GLOBALES
$codIndicadorPantalla = 0;
$arrayIndicadores = array();
$metaProgramada = 0;
$nombreMeses = array('TODOS', 'ENERO', 'FEBRERO', 'MARZO',  'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');

function print_r2($val)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';
}

function GetDepartamento()
{
    $departamento = $_POST['departamento'];
    return $departamento;
}

function GetMes()
{
    $mes = $_POST['codMes'];
    return $mes;
}

function GetAnio()
{
    $anio = $_POST['anio'];
    return $anio;
}

function GetCodigoIndicador()
{
    $codIndicador = $_POST['codIndicador'];
    return $codIndicador;
}

function GetZonaIndicador()
{
    $codZona = $_POST['codZona'];
    return $codZona;
}

function GetAccion()
{
    $accion = $_POST['accion'];
    return $accion;
}



function GetInformacionOrg($codigoOrg)
{
    $infoOrg = array();
    $sqlInfoOrg = "select * from u_organizaciones where cod_u_organizaciones = " . $codigoOrg;
    $resInfoOrg = query($sqlInfoOrg);
    while($filaOrg = mysql_fetch_array($resInfoOrg))
    {
        array_push($infoOrg, $filaOrg['cod_u_organizaciones']);                 // 0
        array_push($infoOrg, $filaOrg['ruc_provisional']);                      // 1
        array_push($infoOrg, $filaOrg['ruc_definitivo']);                       // 2
        array_push($infoOrg, $filaOrg['organizacion']);                         // 3
        array_push($infoOrg, $filaOrg['actividad']);                            // 4
        array_push($infoOrg, $filaOrg['categoria_actividad_mp']);               // 5
        array_push($infoOrg, $filaOrg['identificacion_actividad_mp']);          // 6
        array_push($infoOrg, $filaOrg['forma_organizacion']);                   // 7
        array_push($infoOrg, $filaOrg['estado_organizacion']);                  // 8
        array_push($infoOrg, $filaOrg['num_socios']);                           // 9
        array_push($infoOrg, $filaOrg['email']);                                // 10
        array_push($infoOrg, $filaOrg['telefono']);                             // 11
        array_push($infoOrg, $filaOrg['tipo']);                                 // 12
        array_push($infoOrg, $filaOrg['circuito_economico']);                   // 13
        array_push($infoOrg, $filaOrg['nombre_representante_legal']);           // 14
        array_push($infoOrg, $filaOrg['cod_zona']);                             // 15            
        array_push($infoOrg, $filaOrg['cod_provincia']);                        // 16
        array_push($infoOrg, $filaOrg['cod_canton']);                           // 17
        array_push($infoOrg, $filaOrg['cod_parroquia']);                        // 18
        array_push($infoOrg, $filaOrg['direccion']);                            // 19
        array_push($infoOrg, $filaOrg['celular']);                              // 20
        array_push($infoOrg, $filaOrg['num_resolucion']);                       // 21
        array_push($infoOrg, $filaOrg['estado_juridico']);                      // 22
        array_push($infoOrg, $filaOrg['producto_servicio']);                    // 23

    }

    return $infoOrg;
}

function GetNumSocios($codOrganizaciones)
{
    $sqlNumSocios = "select count(distinct cedula) as numSocios from socios where cod_u_organizaciones = " . $codOrganizaciones . " and estado = 1";
    $resNumSocios = query($sqlNumSocios);
    $numSocios = 0;
    while($filaNumSocios = mysql_fetch_array($resNumSocios))
    {
        $numSocios = $filaNumSocios['numSocios'];
    }
    return $numSocios;
}

function GetNumEmpleados($codOrganizaciones)
{
    $sqlNumEmpleados = "select count(distinct cedula) as numEmpleados from socios where cod_u_organizaciones = " . $codOrganizaciones . " and estado = 1 and socio_empleado in ('empleado', 'socio_trabajador')";
    $resNumEmpleados = query($sqlNumEmpleados);
    $numEmpleados = 0;
    while($filaNumEmpleados = mysql_fetch_array($resNumEmpleados))
    {
        $numEmpleados = $filaNumEmpleados['numEmpleados'];
    }
    return $numEmpleados;
}

function GetIndicadores($departamento, $anioIndicador)
{

    $sqlIndicadores = "select cod_indicador, indicador from indicador where estado = 1 and departamento = '" . $departamento . "' and anio_inicio <= " . $anioIndicador . " and anio_fin >= " . $anioIndicador;
    $resIndicadores = query($sqlIndicadores);
    $arrayIndicadores = array();
    while($filaIndicadores = mysql_fetch_array($resIndicadores))
    {
        array_push($arrayIndicadores, $filaIndicadores['cod_indicador']);
        array_push($arrayIndicadores, $filaIndicadores['indicador']);
    }

    // print_r2($arrayIndicadores);
    return $arrayIndicadores;

}

function EsNuevaOrganizacion($codOrganizacion, $departamento, $mesIndicador, $anioIndicador, $tipoRevision)
{
    $nuevaOrg = "no";
    if($departamento == 'IM')
    {
        // se debe revisar registros anteriores al mes consultado       
        $sqlNuevaOrg = "";
        if($tipoRevision == 'manual')
        {
            $sqlNuevaOrg = "select * from im_contratacion where cod_u_organizaciones = " . $codOrganizacion . " and month(fecha_reporte) < " . $mesIndicador . " and year(fecha_reporte) = " . $anioIndicador; 
        }
        if($tipoRevision == 'auto')
        {
            $sqlNuevaOrg = "select * from im_contratacion where cod_u_organizaciones = " . $codOrganizacion . " and month(fecha_reporte) < " . $mesIndicador . " and year(fecha_reporte) = " . $anioIndicador . " and se_reporta = si";   
        }

        $resNuevaOrg = query($sqlNuevaOrg);
        $numNuevaOrg = mysql_num_rows($resNuevaOrg);

        if($numNuevaOrg > 0)
        {
            $nuevaOrg = "si";
        }

    }

    return $nuevaOrg;
}

function GetNameIndicador($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador)
{
    global $codIndicadorPantalla, $arrayIndicadores;
    $departamentoConsulta = GetDepartamento();
    $indexArrayIndicadores = 0;
    if($codIndicador != $codIndicadorPantalla)
    {
        $indexArrayIndicadores = array_search($codIndicador, $arrayIndicadores);
        $codIndicadorPantalla = $arrayIndicadores[$indexArrayIndicadores];      
    }
    else
    {
        $indexArrayIndicadores = array_search($codIndicador, $arrayIndicadores);        
    }

    return $arrayIndicadores[$indexArrayIndicadores + 1];
}

function GetMetaProgramada($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador)
{
    $metaProgramada = 0;    
    
    $sqlMetaProgramada = "select meta_programada from indicador_zona_mes where cod_indicador = " . $codIndicador . " and anio_indicador = " . $anioIndicador . " and mes = " . $mesIndicador . " and zona = " . $zonaIndicador;
    // echo $sqlMetaProgramada . "<br>";
    $resMetaProgramada = query($sqlMetaProgramada);
    while($filaMetaProgramada = mysql_fetch_array($resMetaProgramada))
    {
        $metaProgramada = $filaMetaProgramada['meta_programada'];
    }   

    return $metaProgramada;
}

function GetMetaTotal($codIndicador, $zonaIndicador, $anioIndicador)
{
    $sqlMetaTotal = "select sum(meta_programada) as metaAnual from indicador_zona_mes where cod_indicador = " . $codIndicador . " and anio_indicador = " . $anioIndicador . " and zona = " . $zonaIndicador;

    $resMetaTotal = query($sqlMetaTotal);
    $metaTotal = 0;
    while($filaMetaTotal = mysql_fetch_array($resMetaTotal))
    {
        $metaTotal = $filaMetaTotal['metaAnual'];
    }

    return $metaTotal;
}

function GetHeaderTabla($departamento, $mesIndicador)
{
    $tHeader = "";
    global $nombreMeses;
    $tHeader = "<thead>";
    if($departamento == 'IM')
    {
        $tHeader .= "<tr>
                        <th scope='col'># GPR</th>
                        <th scope='col'>INDICADOR</th>
                        <th scope='col'>META MENSUAL PROGRAMADA (" . $nombreMeses[$mesIndicador]  . ")</th>
                        <th scope='col'>META MENSUAL EJECUTADA (" . $nombreMeses[$mesIndicador]  . ")</th>
                        <th scope='col'>% DE AVANCE MENSUAL (" . $nombreMeses[$mesIndicador]  . ")</th>";
                        if($mesIndicador == 0)
                        {                               
                            $tHeader .= "<th scope='col'>META PROGRAMADA (" . $nombreMeses[$mesIndicador]  . ")</th>                        
                                        <th scope='col'>META ACUMULADA PROGRAMADA (" . $nombreMeses[$mesIndicador]  . ")</th>
                                        <th scope='col'>META ACUMULADA EJECUTADA (" . $nombreMeses[$mesIndicador]  . ")</th>
                                        <th scope='col'>% AVANCE (" . $nombreMeses[$mesIndicador]  . ")</th>";
                        }
                        else
                        {
                            $tHeader .= "<th scope='col'>META PROGRAMADA (ENERO - " . $nombreMeses[$mesIndicador]  . ")</th>        
                                        <th scope='col'>META ACUMULADA PROGRAMADA (ENERO - " . $nombreMeses[$mesIndicador]  . ")</th>
                                        <th scope='col'>META ACUMULADA EJECUTADA (ENERO - " . $nombreMeses[$mesIndicador]  . ")</th>
                                        <th scope='col'>% AVANCE (ENERO - " . $nombreMeses[$mesIndicador]  . ")</th>";
                        }
                        
                        $tHeader .= "<th scope='col'>% AVANCE ANUAL (ANUAL)</th>
                                    <th scope='col'>POR EJECUTAR</th>                           
                                    <th scope='col'>ZONA</th>
                                    <th scope='col'>MES</th>
                                    <th scope='col' class='oculto'>GRÁFICO</th>
                                    <th scope='col'>DETALLE</th>
                                    <th scope='col' class='oculto'>CodigoIndicador</th>";
        $tHeader.= "</tr>";
    }

    if($departamento == 'FA')
    {
        $tHeader .= "<tr>
                        <th scope='col'># N°</th>
                        <th scope='col'>INDICADORES</th>
                        <th scope='col'>DIRECCIÓN ZONAL</th>
                        <th scope='col'>MES</th>
                        <th scope='col'>META PROGRAMADA</th>";                     
                        
                        $tHeader .= "<th scope='col'>META EJECUTADA</th>                                    
                                    <th scope='col'>GRÁFICO</th>
                                    <th scope='col'>DETALLE</th>
                                    <th scope='col' class='oculto'>CodigoIndicador</th>";
        $tHeader.= "</tr>";
    }

    if($departamento == 'FP')
    {
        $tHeader .= "<tr>
                        <th colspan=\"1\" class=\"cuadroBlanco\"></th>
                        <th colspan=\"6\" class=\"colorIndicador total\">SERVICIOS DE LA DIRECCION</th>
                        <th colspan=\"7\" class=\"colorIndicador1\">CUMPLIMIENTO</th>
                    </tr>
                    <tr>
                        <th scope='col'>INDICE</th>
                        <th scope='col'>INDICADORES</th>
                        <th scope='col'>Asesoría para la elaboración de planes de negocio solidarios</th>
                        <th scope='col'>Cofinanciamiento para proyectos de la EPS</th>
                        <th scope='col'>Asistencia técnica en procesos administrativos</th>
                        <th scope='col'>Alianza con instituciones para la AT en procesos operativos</th>
                        <th scope='col'>Total</th>
                        <th scope='col'>Meta Periodo</th>
                        <th scope='col'>% Ejecutado</th>
                        <th scope='col'>Meta Anual</th>
                        <th scope='col'>% Avance</th>
                        <th scope='col'>Zona</th>
                        <th scope='col'>Mes</th>";                     
                        
                        $tHeader .= "<th scope='col'>DETALLE</th>
                                    <th scope='col' class='oculto'>CodigoIndicador</th>";
        $tHeader.= "</tr>";
    }
    $tHeader.= "</thead>";
    return $tHeader;
}

function GetHeaderDetalle($departamento, $codIndicador)
{
    $tHeader = "<thead>";

    if($departamento == 'IM' && ($codIndicador == 26 || $codIndicador == 27 || $codIndicador == 29))
    {        
            $tHeader .= "<tr>
                            <th scope='col'>ZONA</th>
                            <th scope='col'>PROVINCIA</th>
                            <th scope='col'>CANTÓN</th>
                            <th scope='col'>MES REPORTE</th>
                            <th scope='col'>TIPO ENTIDAD CONTRATANTE</th>
                            <th scope='col'>NOMBRE ENTIDAD CONTRATANTE</th>
                            <th scope='col'>FECHA ADJUDICACIÓN DEL CONTRATO</th>
                            <th scope='col'>CÓDIGO DEL PROCESO</th>
                            <th scope='col'>CÓDIGO CPC</th>
                            <th scope='col'>MONTO DE CONTRATACIÓN</th>                            
                            <th scope='col'>SECTOR PRIORIZADO</th>
                            <th scope='col'>BIEN O SERVICIO CONTRATADO</th>
                            <th scope='col'>TIPO ORGANIZACIÓN EPS</th>
                            <th scope='col'>CIRCUITO ECONÓMICO</th>
                            <th scope='col'>NOMBRE DE LA ORGANIZACIÓN</th>
                            <th scope='col'>SIGLAS ORG</th>
                            <th scope='col'>RUC ORG</th>
                            <th scope='col'>NUM SOCIOS</th>
                            <th scope='col'>NUM EMPLEADOS</th>
                            <th scope='col'>NUEVA ORGANIZACIÓN</th>
                        </tr>";
    }

    if($departamento == 'IM' && ($codIndicador == 30 || $codIndicador == 31 || $codIndicador == 33))
    {        
            $tHeader .= "<tr>
                            <th scope='col'>ZONA</th>
                            <th scope='col'>PROVINCIA</th>
                            <th scope='col'>CANTÓN</th>
                            <th scope='col'>MES REPORTE</th>
                            <th scope='col'>TIPO ENTIDAD CONTRATANTE</th>
                            <th scope='col'>NOMBRE ENTIDAD CONTRATANTE</th>
                            <th scope='col'>FECHA ADJUDICACIÓN DEL CONTRATO</th>
                            <th scope='col'>CÓDIGO DEL PROCESO</th>
                            <th scope='col'>CÓDIGO CPC</th>
                            <th scope='col'>MONTO DE CONTRATACIÓN</th>                            
                            <th scope='col'>SECTOR PRIORIZADO</th>
                            <th scope='col'>BIEN O SERVICIO CONTRATADO</th>
                            <th scope='col'>TIPO ORGANIZACIÓN EPS</th>
                            <th scope='col'>CIRCUITO ECONÓMICO</th>
                            <th scope='col'>NOMBRE DE LA ORGANIZACIÓN</th>
                            <th scope='col'>SIGLAS ORG</th>
                            <th scope='col'>RUC ORG</th>
                            <th scope='col'>NUM SOCIOS</th>
                            <th scope='col'>NUM EMPLEADOS</th>
                            <th scope='col'>NUEVA ORGANIZACIÓN</th>
                            <th scope='col'>COD CONTRATACIÓN</th>
                        </tr>";
    }
    if($departamento == 'FP' && ($codIndicador == 42))
    {        
            $tHeader .= "<tr>
                            <th scope='col'>ZONA</th>
                            <th scope='col'>NOMBRE OEPS</th>
                            <th scope='col'>RUC ORG</th>
                            <th scope='col'>NUM SOCIOS</th>
                            <th scope='col'>DESTINO</th>
                            <th scope='col'>MONTO</th>
                            <th scope='col'>PLAZO</th>
                            <th scope='col'>INTERES</th>
                            <th scope='col'>VERIFICABLE</th>
                            <th scope='col'>CÓDIGO DEL COFINANCIAMIENTO</th>                            
                        </tr>";
    }
    $tHeader .= "</thead>";

    return $tHeader;
}

function ImprimirResultado($lineaImprimir, $codIndicador, $arrayImprimir, $departamento, $zonaIndicador, $mesIndicador)
{   
    global $nombreMeses;
    $contLinea = $lineaImprimir;
    $tBody = "";
    // echo $departamento . "<br>";
    if($departamento == 'IM')
    {
        $tamArray = count($arrayImprimir);
        // echo $tamArray . "<br>";
        for($indice = 0; $indice < $tamArray; $indice = $indice + 10)
        {
            // $contLinea++;
            $tBody .= "<tr>";
            $tBody .= "<th scope='row'>" . $contLinea . "</th>";
            $tBody .= "<td id='" . $contLinea . "-2'>" . $arrayImprimir[$indice] . "</td>
                        <td id='" . $contLinea . "-3'>" . $arrayImprimir[$indice + 1] . "</td>
                        <td id='" . $contLinea . "-4'>" . $arrayImprimir[$indice + 2] . "</td>
                        <td id='" . $contLinea . "-5'>" . $arrayImprimir[$indice + 3] . "</td>
                        <td id='" . $contLinea . "-6'>" . $arrayImprimir[$indice + 4] . "</td>
                        <td id='" . $contLinea . "-7'>" . $arrayImprimir[$indice + 5] . "</td>
                        <td id='" . $contLinea . "-8'>" . $arrayImprimir[$indice + 6] . "</td>
                        <td id='" . $contLinea . "-9'>" . $arrayImprimir[$indice + 7] . "</td>
                        <td id='" . $contLinea . "-10'>" . $arrayImprimir[$indice + 8] . "</td>
                        <td id='" . $contLinea . "-11'>" . $arrayImprimir[$indice + 9] . "</td>
                        <td id='" . $contLinea . "-12'>" . $zonaIndicador . "</td>
                        <td id='" . $contLinea . "-13'>" . $nombreMeses[$mesIndicador] . "</td>
                        <td class='oculto'><button id='" . $contLinea . "-14' onclick='VerGrafica(this.id)' type='button' class='btn btn-warning'>Ver</button></td>
                        <td><button id='" . $contLinea . "-15' onclick='DesplegarInfo(this.id, \"IM\")' type='button' class='btn btn-warning'>Desplegar</button></td>
                        <td id=" . $contLinea . "-16 class='oculto'>" . $codIndicador . "</td>";
            $tBody .= "</tr>";
        }
            
        
        
    }

    if($departamento == 'FP')
    {
        $tamArray = count($arrayImprimir);
        // echo $tamArray . "<br>";
        for($indice = 0; $indice < $tamArray; $indice = $indice + 10)
        {
            // $contLinea++;
            $tBody .= "<tr>";
            $tBody .= "<th scope='row'>" . $contLinea . "</th>";
            $tBody .= "<td id='" . $contLinea . "-2'>" . $arrayImprimir[$indice] . "</td>
                        <td id='" . $contLinea . "-3'>" . $arrayImprimir[$indice + 1] . "</td>
                        <td id='" . $contLinea . "-4'>" . $arrayImprimir[$indice + 2] . "</td>
                        <td id='" . $contLinea . "-5'>" . $arrayImprimir[$indice + 3] . "</td>
                        <td id='" . $contLinea . "-6'>" . $arrayImprimir[$indice + 4] . "</td>
                        <td id='" . $contLinea . "-7'>" . $arrayImprimir[$indice + 5] . "</td>
                        <td id='" . $contLinea . "-8'>" . $arrayImprimir[$indice + 6] . "</td>
                        <td id='" . $contLinea . "-9'>" . $arrayImprimir[$indice + 7] . "</td>
                        <td id='" . $contLinea . "-10'>" . $arrayImprimir[$indice + 8] . "</td>
                        <td id='" . $contLinea . "-11'>" . $arrayImprimir[$indice + 9] . "</td>
                        <td id='" . $contLinea . "-12'>" . $zonaIndicador . "</td>
                        <td id='" . $contLinea . "-13'>" . $nombreMeses[$mesIndicador] . "</td>
                        <td class='oculto'><button id='" . $contLinea . "-14' onclick='VerGrafica(this.id)' type='button' class='btn btn-warning'>Ver</button></td>
                        <td><button id='" . $contLinea . "-15' onclick='DesplegarInfo(this.id, \"FP\")' type='button' class='btn btn-warning'>Desplegar</button></td>
                        <td id=" . $contLinea . "-16 class='oculto'>" . $codIndicador . "</td>";
            $tBody .= "</tr>";
        }
            
        
        
    }
    
    return $tBody;
}

function CalculoPorcentaje($valorLimite, $valorObtenido)
{
    $porcentaje = 0;
    if($valorLimite == 0)
    {
        $porcentaje = ($valorObtenido * 100);   
    }
    else
    {
        $porcentaje = ($valorObtenido * 100) / $valorLimite;
        $porcentaje = round($porcentaje, 2);         
    }

    return $porcentaje;
}

function GetProvincia($codZona, $codProvincia)
{
    $sqlProvincia = "select provincia from u_provincia where zona = " . $codZona . " and cod_provincia = " . $codProvincia;
    $resProvincia = query($sqlProvincia);
    $nomProvincia = "0";
    while($filaProvincia = mysql_fetch_array($resProvincia))
    {
        $nomProvincia = $filaProvincia['provincia'];
    }
    return $nomProvincia;
}

function GetCanton($codProvincia, $codCanton)
{
    $sqlCanton = "select canton from u_canton where cod_provincia = " . $codProvincia . " and cod_canton = " . $codCanton;
    // echo $sqlCanton . "<br>";
    $resCanton = query($sqlCanton);
    $nomCanton = "0";
    while($filaCanton = mysql_fetch_array($resCanton))
    {
        $nomCanton = $filaCanton['canton'];
    }

    return $nomCanton;
}

function CambiarPuntoComa($valor)
{
    $cambio = str_replace(".", ",", $valor);
    return $cambio;
}

function GetEntidadContratante($codTipoEntidadContratante)
{
    $sqlTipoEntidad = "select tipo from im_tipo_entidad_contratante where cod_tipo_entidad_contratante = " . $codTipoEntidadContratante;
    $resTipoEntidad = query($sqlTipoEntidad);
    $tipoEntidad = "0";
    while($filaTipoEntidad = mysql_fetch_array($resTipoEntidad))
    {
        $tipoEntidad = $filaTipoEntidad['tipo'];
    }
    return $tipoEntidad;
}

function GetNombreEntidadContratante($codEntidadContratante)
{
    $sqlEntidad = "select entidad_contratante from im_entidad_contratante where cod_entidad_contratante = " . $codEntidadContratante;
    $resEntidad = query($sqlEntidad);
    $entidad = "0";
    while($filaEntidad = mysql_fetch_array($resEntidad))
    {
        $entidad = $filaEntidad['entidad_contratante'];
    }
    return $entidad;
}

function QuitarDuplicadosArray($arrayInicial, $arrayComparar)
{
    $arrayResultado = array();
    $cont = 0;
    foreach ($arrayInicial as $valor) 
    {
        if(in_array($valor, $arrayComparar))
        {
            unset($arrayInicial[$cont]);
        }
        $cont++;
    }

    $arrayResultado = array_unique($arrayInicial);
    return $arrayResultado;
}

?>