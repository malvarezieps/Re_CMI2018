<?php
include ('../lib/dbconfig.php');
include ('FuncionesGlobales.php');

$codIndicador = GetCodigoIndicador();
$codZona = GetZonaIndicador();
$codMes = GetMes();
$codAnio = GetAnio();
$codDepartamento = GetDepartamento();
$codAccion = GetAccion();
// echo $codIndicador . ' - ' . $codZona . ' - ' . $codMes . ' - ' . $codAnio . ' - ' . $codDepartamento . ' - ' . $codAccion .  "<br>";

$accionRequerida = GetAccion();

if($accionRequerida == 'desplegarInfo')
{
    ImprimirDetalleIndicador();
}

if($accionRequerida == 'generarIndicador')
{
    ImprimirResultadoPantalla($accionRequerida);    
}

if($accionRequerida == 'consolidado')
{
    ImprimirConsolidado($accionRequerida);  
}
// **********************************************

function ImprimirResultadoPantalla($accion)
{
    // echo $accion . "<br>";
    // global $departamento, $anio, $mes, $codIndicador;
    global $arrayIndicadores;
    $departamentoConsulta = GetDepartamento();
    $anioConsulta = GetAnio();
    $mesConsulta = GetMes();
    $codIndicadorConsulta = GetCodigoIndicador();
    $zonaIndicadorConsulta = GetZonaIndicador();
    $arrayCodIndicadores = array();
    $arrayNombreIndicadores = array();
    $arrayMeses = array();
    $arrayZonas = array();
    $arrayResultado = array();
    $nombreMeses = array('Enero', 'Febrero', 'Marzo',   'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    $arrayIndicadores = GetIndicadores($departamentoConsulta, $anioConsulta);
    // print_r2($arrayIndicadores);
    $tableBody = "<table class='table table-striped table-bordered table-hover'>";


    if($accion == 'generarIndicador')
    {

        // ******************************************************************************
        // SE DEBE CONSULTAR CUANTOS INDICADORES HAY QUE IMPRIMIR EN PANTALLA
        $tableBody .= GetHeaderTabla($departamentoConsulta, $mesConsulta);
        $tableBody .="<tbody>";
        if($codIndicadorConsulta == 0)
        {
            // Se requiere que todos los indicadores aparezcan la pantalla
            $sqlNumIndicador = "select * from indicador where anio_inicio <= " . $anioConsulta . " and anio_fin >= " . $anioConsulta . " and estado = 1 and departamento = '" . $departamentoConsulta . "'";
            $resNumIndicador = query($sqlNumIndicador);
            $numIndicadoresTotales = mysql_num_rows($resNumIndicador);
            // $codIndicadorConsulta = $numIndicadoresTotales;
            while($filaIndicadorConsulta = mysql_fetch_array($resNumIndicador))
            {
                array_push($arrayCodIndicadores, $filaIndicadorConsulta['cod_indicador']);
                array_push($arrayNombreIndicadores, $filaIndicadorConsulta['indicador']);
            }
        }
        else
        {
            // Se requiere que el indicador seleccionado aparezca la pantalla
            $sqlNumIndicador = "select * from indicador where cod_indicador = " . $codIndicadorConsulta;
            $resNumIndicador = query($sqlNumIndicador);
            while($filaIndicadorConsulta = mysql_fetch_array($resNumIndicador))
            {
                array_push($arrayCodIndicadores, $filaIndicadorConsulta['cod_indicador']);
                array_push($arrayNombreIndicadores, $filaIndicadorConsulta['indicador']);
            }
        }
        // ************************************************************************************

        // ****************************************************************************************
        // SE DEBE SABER QUE MES DEBE IMPRIMIRSE EN PANTALLA
        if($mesConsulta == 0)
        {
            // Se requiere que todas los zonas se impriman
            for($ind = 1; $ind <= 12; $ind++)
            {
                array_push($arrayMeses, $ind);
            }
        }
        else
        {
            array_push($arrayMeses, $mesConsulta);
        }
        // *****************************************************************************************

        // **************************************************************************************
        // SE DEBE SABER QUE ZONAS DEBEN SER CONSULTADAS SUS INDICADORES
        if($zonaIndicadorConsulta == 0)
        {
            // se quiere que todas las zonas sean consultadas
            for($ind = 1; $ind <= 9; $ind++)
            {
                array_push($arrayZonas, $ind);
            }
        }
        else
        {
            array_push($arrayZonas, $zonaIndicadorConsulta);
        }

        $contLinea = 0;
        // print_r2($arrayZona);
        foreach ($arrayCodIndicadores as $valueCodIndicadores) 
        {
            // POR CADA INDICADOR SELECCIONADO
            foreach ($arrayZonas as $valueZona)
            {
                // POR CADA ZONA SELECCIONADA
                foreach ($arrayMeses as $valueMes) 
                {
                    // POR CADA MES SELECCIONADO
                    // IMPRIMIR EL INDICADOR
                    $contLinea++;
                    $arrayResultado = CalcularIndicador($valueCodIndicadores, $valueMes, $valueZona, $anioConsulta);                
                    $tableBody .= ImprimirResultado($contLinea, $valueCodIndicadores, $arrayResultado, $departamentoConsulta, $valueZona, $valueMes);
                }
            }
        }

        
        // **************************************************************************************
        
    }   
    $tableBody .= "</tbody>";
    $tableBody .= "</table>";
    echo $tableBody;
}

function CalcularIndicador($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador)
{
    $resultadoOrg = array();
    // echo $codIndicador . " - " . $mesIndicador . " - " . $zonaIndicador . " - " . $anioIndicador . "<br>";
    $nombreIndicadorPantalla = GetNameIndicador($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador);
    // echo $nombreIndicadorPantalla . "<br>";
    // añado el nombre del indicador al array resultante
    array_push($resultadoOrg, $nombreIndicadorPantalla);
    

    // Variables para cada servicio
    $resIndicadorAsesoria = 0;
    $resIndicadorCofinanciamiento = 0;
    $resIndicadorAsistencia = 0;
    $resIndicadorAlianza = 0;

    $a_resultadoAcumuladoIndicador = 0;

    if($codIndicador == 42)
    {
        // NÚMERO DE ORGANIZACIONES QUE RECIBIERON COFINANCIAMIENTO
        $resIndicadorAsesoria = 0;
        $resIndicadorCofinanciamiento = Indicador01($zonaIndicador, $mesIndicador, $anioIndicador, $codIndicador, 'org', 'numerico');
        $resIndicadorAsistencia = 0;
        $resIndicadorAlianza = 0;

    }

    // se añade el resultado de los indicadores
    array_push($resultadoOrg, $resIndicadorAsesoria);
    array_push($resultadoOrg, $resIndicadorCofinanciamiento);
    array_push($resultadoOrg, $resIndicadorAsistencia);
    array_push($resultadoOrg, $resIndicadorAlianza);

    // Se suma el total
    $a_resultadoAcumuladoIndicador = $resIndicadorAsesoria + $resIndicadorCofinanciamiento + $resIndicadorAsistencia + $resIndicadorAlianza;
    array_push($resultadoOrg, $a_resultadoAcumuladoIndicador);

    // añado la meta programada
    $metaProgramada = GetMetaProgramada($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador);
    $metaProgramada = CambiarPuntoComa($metaProgramada);
    array_push($resultadoOrg, $metaProgramada);      

    // Porcentaje de avance
    $porcentajeAvance = CalculoPorcentaje($metaProgramada, $a_resultadoAcumuladoIndicador);
    $porcentajeAvance = CambiarPuntoComa($porcentajeAvance);
    array_push($resultadoOrg, $porcentajeAvance . "%");    

    $metaTotal = 0;
    for($imes = 1; $imes <= 12; $imes++)
    {   
        $metaTotal += GetMetaProgramada($codIndicador, $imes, $zonaIndicador, $anioIndicador);
    }
    array_push($resultadoOrg, $metaTotal);

    // Porcentaje de avance anual
    $porcentajeAnual = CalculoPorcentaje($metaTotal, $a_resultadoAcumuladoIndicador);
    $porcentajeAnual = CambiarPuntoComa($porcentajeAnual);
    array_push($resultadoOrg, $porcentajeAnual . "%");
    
    // print_r2($resultadoOrg);

    return $resultadoOrg;
}

// REVISAR ORG YA HAN SIDO REPORTADAS
function RevisarOrgReportadas($zonaConsulta, $mesConsulta, $anioConsulta, $codIndicadorConsulta)
{
    $aOrgReportadas = array();
    if($mesConsulta > 1)
    {
        if($codIndicadorConsulta == 42)
        {
            // Organizaciones reportadas en meses anteriore
            $sqlReportadas = "select * from fp_asesoria_asistencia_cofinanciamiento f where f.zona = " . $zonaConsulta . " and year(f.fecha_reporte) = " . $anioConsulta . " and month(f.fecha_reporte) < " . $mesConsulta . " group by f.cod_u_organizaciones";
            // echo $sqlReportadas . "<br>";
        }

        // Se obtienen los datos generados
        $resReportadas = query($sqlReportadas);    

        while($filaReportadas = mysql_fetch_array($resReportadas))
        {
            array_push($aOrgReportadas, $filaReportadas['cod_u_organizaciones']);
        }
    }

    return $aOrgReportadas;
    
}

// *********************************************************************************************************
// IMPRESION DEL DETALLE DEL INDICADOR
// *********************************************************************************************************

function ImprimirDetalleIndicador()
{
    $codIndicadorConsulta = $_POST['codIndicador'];
    $zonaIndicadorConsulta = $_POST['codZona'];
    $mesIndicadorConsulta = $_POST['codMes'];
    $anioIndicadorConsulta = $_POST['anio'];
    $departamentoConsulta = $_POST['departamento'];
    $detalleIndicador = array();

    // echo $codIndicadorConsulta . "<br>";
    $tableBody = "<table id='tablaDetalle' class='table table-striped table-bordered table-hover'>";
    $tableBody .= GetHeaderDetalle($departamentoConsulta, $codIndicadorConsulta);

    if($codIndicadorConsulta == 42 && $departamentoConsulta == 'FP')
    {
        // $detalleIndicador = MontoMercado($codIndicadorConsulta, $mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, "detalle", 'publica');   

        
    }
    
    $tableBody .= "</tbody>";
    $tableBody .= "</table>";
    echo $tableBody;
}

// NÚMERO DE ORGANIZACIONES QUE RECIBIERON COFINANCIAMIENTO
function Indicador01($zonaConsulta, $mesConsulta, $anioConsulta, $codIndicadorConsulta, $tipoOrg, $tipoReporte)
{
    //VARIABLES
    $aOrganizaciones = array();
    // Consulta de todas las organizaciones que tuvieron el servicio cofinanciamiento
    $sqlIndicador = "select * from fp_asesoria_asistencia_cofinanciamiento f inner join u_organizaciones o on (o.cod_u_organizaciones = f.cod_u_organizaciones and o.tipo = '" . $tipoOrg . "') where f.zona = " . $zonaConsulta . " and year(f.fecha_reporte) = " . $anioConsulta . " and month(f.fecha_reporte) = " . $mesConsulta . " group by f.cod_u_organizaciones";
    echo $sqlIndicador . "<br>";

    //se ejecuta la sentencia sql
    $resIndicador = query($sqlIndicador);

    // se obtiene los datos consultados
    while($filaIndicador = mysql_fetch_array($resIndicador))
    {
        array_push($aOrganizaciones, $filaIndicador['cod_u_organizaciones']);
    }
    

    $aOrganizacionesReportadas = RevisarOrgReportadas($zonaConsulta, $mesConsulta, $anioConsulta, $codIndicadorConsulta);
    // print_r2($aOrganizacionesReportadas);
    $arrayFinal = QuitarDuplicadosArray($aOrganizaciones, $aOrganizacionesReportadas);
    // print_r2($arrayFinal);

    // DEPENDIENDO DEL TIPO DE REPORTE, SE NECESITA ENVIAR DATOS NUMERICOS O DESAGREGADOS
    if($tipoReporte == 'numerico')
    {
        return count($arrayFinal);
    }

    if($tipoReporte == 'detalle')
    {
        $resIndicador = query($sqlIndicador);
        $aCofinanciamiento = array();

        // *****************************************************************
        // en $aCofinanciamiento se tiene tanto las org con sus respectivos codigos de cofinanciamiento
        // almacena las eventos de cofinanciamiento que se reporto
        // *****************************************************************
        while($filaCof = mysql_fetch_array($resIndicador))
        {
            array_push($aCofinanciamiento, $filaCof['cod_asesoria_asistencia_cofinanciamiento']);
            array_push($aCofinanciamiento, $filaCof['cod_u_organizaciones']);
        }

        // variable que tendra el detalle de la informacion del indicador
        $aDetalleFinal = array();

        foreach($arrayFinal as $valorOrg)
        {
            $aInfoOrg = GetInformacionOrg($valorOrg);
            $numSociosOrg = GetNumSocios($valorOrg);
            array_push($aDetalleFinal, $zonaConsulta);                          // zona
            array_push($aDetalleFinal, $aInfoOrg[3]);                           // nombre org
            array_push($aDetalleFinal, $aInfoOrg[2] . ' - ' . $aInfoOrg[1]);    // ruc definitivo y provisional
            array_push($aDetalleFinal, $numSociosOrg);                          // num socios


        }
        

    }

}



?>