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
$nombreMeses = array('Enero', 'Febrero', 'Marzo',   'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

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
        // POR CADA INDICADOR SELECCIONADO
        foreach ($arrayCodIndicadores as $valueCodIndicadores) 
        {            
            // POR CADA ZONA SELECCIONADA
            foreach ($arrayZonas as $valueZona)
            {
                // POR CADA MES SELECCIONADO
                foreach ($arrayMeses as $valueMes) 
                {
                    // IMPRIMIR EL INDICADOR                    
                    $arrayResultado = array();
                    if($valueCodIndicadores == 43 && ($valueMes == 6 || $valueMes == 12))
                    {
                        $contLinea++;
                        $arrayResultado = CalcularIndicador($valueCodIndicadores, $valueMes, $valueZona, $anioConsulta);
                    }
                    // else
                    // {
                    //     $arrayResultado = CalcularIndicador($valueCodIndicadores, $valueMes, $valueZona, $anioConsulta);
                    // }
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

    if($codIndicador == 43 && ($mesIndicador == 6 || $mesIndicador == 12))
    {
        // PORCENTAJES DE OEPS COFINANCIADAS QUE RECIBEN MONITOREO POSTERIOR AL CIERRE DEL CONVENIO Y SE ENCUENTRAN OPERATIVAS
        $resIndicadorAsesoria = 0;
        $resIndicadorCofinanciamiento = Indicador02($zonaIndicador, $mesIndicador, $anioIndicador, $codIndicador, 'org', 'numerico');
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
        $sqlReportadas = '';
        if($codIndicadorConsulta == 42)
        {
            // Organizaciones reportadas en meses anteriore
            $sqlReportadas = "select * from fp_asesoria_asistencia_cofinanciamiento f where f.zona = " . $zonaConsulta . " and year(f.fecha_reporte) = " . $anioConsulta . " and month(f.fecha_reporte) < " . $mesConsulta . " group by f.cod_u_organizaciones";
            // echo $sqlReportadas . "<br>";
        }
        if($codIndicadorConsulta == 43)
        {
            // Organizaciones reportadas en meses anteriore
            $sqlReportadas = "select * from fp_asesoria_asistencia_cofinanciamiento f inner join u_organizaciones o on (o.cod_u_organizaciones = f.cod_u_organizaciones ) where f.zona = " . $zonaConsulta . " and year(f.fecha_reporte) = " . $anioConsulta . " and month(f.fecha_reporte) < 7 and f.cod_servicio = 2 group by f.cod_u_organizaciones";
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
    $nombreMeses = array('Enero', 'Febrero', 'Marzo',   'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    $detalleIndicador = array();

    // echo $codIndicadorConsulta . "<br>";
    $tableBody = "<table id='tablaDetalle' class='table table-striped table-bordered table-hover'>";
    $tableBody .= GetHeaderDetalle($departamentoConsulta, $codIndicadorConsulta);

    if($codIndicadorConsulta == 42 && $departamentoConsulta == 'FP')
    {
        // $detalleIndicador = MontoMercado($codIndicadorConsulta, $mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, "detalle", 'publica');
        
        $detalleIndicador = Indicador01($zonaIndicadorConsulta, $mesIndicadorConsulta, $anioIndicadorConsulta, $codIndicadorConsulta, 'org', 'detalle');

        $tamDetalleIndicador = count($detalleIndicador);
        $tableBody .= "<tbody>";
        for($posicion = 0; $posicion < $tamDetalleIndicador; $posicion += 20)
        {
            $tableBody .= "<tr>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 1] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 2] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 3] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 4] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 5] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 6] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 7] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 8] . "</td>";
            $tableBody .= "<td>" . CambiarPuntoComa($detalleIndicador[$posicion + 9]) . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 10] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 11] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 12] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 13] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 14] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 15] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 16] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 17] . "</td>";         
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 18] . "</td>";         
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 19] . "</td>";     
            $tableBody .= "</tr>";
        }
        
    }

    if($codIndicadorConsulta == 43 && $departamentoConsulta == 'FP')
    {
         $detalleIndicador = Indicador02($zonaIndicadorConsulta, $mesIndicadorConsulta, $anioIndicadorConsulta, $codIndicadorConsulta, 'org', 'detalle');

        $tamDetalleIndicador = count($detalleIndicador);
        $tableBody .= "<tbody>";
        $contIndice = 0;
        for($posicion = 0; $posicion < $tamDetalleIndicador; $posicion += 8)
        {
            $contIndice++;
            $tableBody .= "<tr>";
            $tableBody .= "<td>" . $contIndice . "</td>";            
            $tableBody .= "<td>" . $detalleIndicador[$posicion] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 7] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 1] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 2] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 3] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 4] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 5] . "</td>";
            $tableBody .= "<td>" . $detalleIndicador[$posicion + 6] . "</td>";
                            
            $tableBody .= "</tr>";
        }
    }


    
    $tableBody .= "</tbody>";
    $tableBody .= "</table>";
    echo $tableBody;
}

function GetFechaVisita($codigoCofinanciamiento, $anioConsulta)
{
    // Devuelve la fecha de visita (si la tuviera) del cofinanciamiento realizado
    $sqlFechaVisita = "select * from fp_visitas_cofinanciamiento where cod_asesoria_asistencia_cofinanciamiento = " . $codigoCofinanciamiento . " and year(fecha_visita) = " . $anioConsulta;
    $resFechaVisita = query($sqlFechaVisita);
    $resFechas = array();

    while($filaFechaVisita = mysql_fetch_array($resFechaVisita))
    {
        array_push($resFechas, $filaFechaVisita['fecha_visita']);
    }
    return $resFechas;
}


// **************************************************************
// INDICADORES
// **************************************************************

// NÚMERO DE ORGANIZACIONES QUE RECIBIERON COFINANCIAMIENTO
function Indicador01($zonaConsulta, $mesConsulta, $anioConsulta, $codIndicadorConsulta, $tipoOrg, $tipoReporte)
{
    //VARIABLES
    $aOrganizaciones = array();
    $arrayFinal = array();
    // Consulta de todas las organizaciones que tuvieron el servicio cofinanciamiento
    $sqlIndicador = "select * from fp_asesoria_asistencia_cofinanciamiento f inner join u_organizaciones o on (o.cod_u_organizaciones = f.cod_u_organizaciones) where f.zona = " . $zonaConsulta . " and year(f.fecha_reporte) = " . $anioConsulta . " and month(f.fecha_reporte) = " . $mesConsulta . " and f.cod_servicio = 2 group by f.cod_u_organizaciones";

   
    // echo $sqlIndicador . "<br>";

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

    if($tipoReporte == 'numerico')
    {
        // DEPENDIENDO DEL TIPO DE REPORTE, SE NECESITA ENVIAR DATOS NUMERICOS O DESAGREGADOS
        return count($arrayFinal);
    }

    if($tipoReporte == 'detalle')
    {
        // La sentencia sql para detalles necesita generar todos los datos de una organizacion incluso si esta se repite, para temas
        // de reporte
        $sqlIndicadorDetalle = "select * from fp_asesoria_asistencia_cofinanciamiento f inner join u_organizaciones o on (o.cod_u_organizaciones = f.cod_u_organizaciones) where f.zona = " . $zonaConsulta . " and year(f.fecha_reporte) = " . $anioConsulta . " and month(f.fecha_reporte) = " . $mesConsulta;
        // Se ejecuta la sentencia sql para generar el detalle de la información
        $resIndicador = query($sqlIndicadorDetalle);
        // Variables
        $aCofinanciamiento = array();                   // guarda codigos del campo cod_asesoria_asistencia_cofinanciamiento
        $aOrgSinFiltrar = array();                      // guarda codigos de organizaciones, puede tener duplicados
        $aDestinoCof = array();                         // guarda los datos del campo destino_cofinanciamiento
        $aMontoCof = array();                           // guarda datos del monto de cofinanciamiento
        $aPlazoCof = array();                           // guarda datos del plazo de cofinanciamiento
        $aInteresCof = array();                         // guarda datos del interes de cofinanciamiento
        // variable que tendra el detalle de la informacion del indicador
        $aDetalleFinal = array();

        while($filaCof = mysql_fetch_array($resIndicador))
        {
            array_push($aOrgSinFiltrar, $filaCof['cod_u_organizaciones']);
            array_push($aCofinanciamiento, $filaCof['cod_asesoria_asistencia_cofinanciamiento']);
            array_push($aDestinoCof, $filaCof['destino_cofinanciamiento']);
            array_push($aMontoCof, $filaCof['monto_cof_rango']);
            array_push($aPlazoCof, $filaCof['plazo_cofinanciamiento']);

        }

        

        foreach($arrayFinal as $valorOrg)
        {
            $aInfoOrg = GetInformacionOrg($valorOrg);
            $numSociosOrg = GetNumSocios($valorOrg);
            array_push($aDetalleFinal, $zonaConsulta);                          // zona
            array_push($aDetalleFinal, $aInfoOrg[3]);                           // nombre org
            array_push($aDetalleFinal, $aInfoOrg[2] . ' - ' . $aInfoOrg[1]);    // ruc definitivo y provisional
            array_push($aDetalleFinal, $numSociosOrg);                          // num socios
            
            // Para obtener los datos faltantes se debe recorrer el array de organizaciones sin filtrar
            // Cuando se encuentre el codigo en dicho array, tomamos su posicion y con ella 
            // consultamos los valores guardados en la misma posicion en los otros arrays como aCofinanciamiento
            $posArray = 0;
            foreach($aOrgSinFiltrar as $valorOrgSinFiltrar)
            {
                if($valorOrgSinFiltrar == $valorOrg)
                {
                    // Destino cofinanciamiento
                    array_push($aDetalleFinal, "COD_CMI: " . $aCofinanciamiento[$posArray] . ' - ' . $aDestinoCof[$posArray]);
                    // Monto Cofinanciamiento
                    array_push($aDetalleFinal, "COD_CMI: " . $aCofinanciamiento[$posArray] . ' - ' . $aMontoCof[$posArray]);
                    // plazo cofinanciamiento
                    array_push($aDetalleFinal, "COD_CMI: " . $aCofinanciamiento[$posArray] . ' - ' . $aPlazoCof[$posArray]);
                    // plazo cofinanciamiento
                    array_push($aDetalleFinal, "COD_CMI: " . $aCofinanciamiento[$posArray] . ' - ' . $aInteresCof[$posArray]);
                    // verificable
                    array_push($aDetalleFinal, "COD_CMI: " . $aCofinanciamiento[$posArray] . ' - ');
                    // codigo cofinanciamiento
                    array_push($aDetalleFinal, $aCofinanciamiento[$posArray]);
                    // echo $posArray . ' - ' . $valorOrg . "<br>";
                }
                else
                {
                    $posArray++;
                }
            } 


        }
       
       return $aDetalleFinal;
    }

}

// *************************************************************
// PORCENTAJES DE OEPS COFINANCIADAS QUE RECIBEN MONITOREO POSTERIOR AL CIERRE DEL CONVENIO Y SE ENCUENTRAN OPERATIVAS
// *************************************************************
function Indicador02($zonaConsulta, $mesConsulta, $anioConsulta, $codIndicadorConsulta, $tipoOrg, $tipoReporte)
{
    //VARIABLES
    global $nombreMeses;
    $aOrganizaciones = array();
    $arrayFinal = array();
    $sqlIndicador = '';
    // El indicador es semestral, por lo cual solo puede mostrarse en junio o en diciembre
    if($mesConsulta <= 6)
    {
        // Si el mes de consulta es 
        $sqlIndicador = "select f.cod_u_organizaciones, f.cod_asesoria_asistencia_cofinanciamiento, f.seguimiento_cof, f.org_operativas, month(f.fecha_reporte) as mesCof from fp_asesoria_asistencia_cofinanciamiento f inner join u_organizaciones o on (o.cod_u_organizaciones = f.cod_u_organizaciones ) where f.zona = " . $zonaConsulta . " and year(f.fecha_reporte) = " . $anioConsulta . " and month(f.fecha_reporte) >= 1 and month(f.fecha_reporte) <= " . $mesConsulta . " and f.cod_servicio = 2 and seguimiento_cof = 'si' and org_operativas = 'si' and estado_convenio = 'cerrado' group by f.cod_u_organizaciones order by mesCof";
    }    
    else
    {
        $sqlIndicador = "select f.cod_u_organizaciones, f.cod_asesoria_asistencia_cofinanciamiento, f.seguimiento_cof, f.org_operativas, month(f.fecha_reporte) as mesCof from fp_asesoria_asistencia_cofinanciamiento f inner join u_organizaciones o on (o.cod_u_organizaciones = f.cod_u_organizaciones ) where f.zona = " . $zonaConsulta . " and year(f.fecha_reporte) = " . $anioConsulta . " and month(f.fecha_reporte) >= 7 and month(f.fecha_reporte) <= " . $mesConsulta . " and f.cod_servicio = 2 and seguimiento_cof = 'si' and org_operativas = 'si' and estado_convenio = 'cerrado' group by f.cod_u_organizaciones order by mesCof";
    }
    // echo $sqlIndicador . "<br>";

    //se ejecuta la sentencia sql
    $resIndicador = query($sqlIndicador);

    // se obtiene los datos consultados
    while($filaIndicador = mysql_fetch_array($resIndicador))
    {
        array_push($aOrganizaciones, $filaIndicador['cod_u_organizaciones']);
    }    

    if($mesConsulta <= 6)
    {
        $arrayFinal = $aOrganizaciones;
    }
    else
    {
        $aOrganizacionesReportadas = RevisarOrgReportadas($zonaConsulta, $mesConsulta, $anioConsulta, $codIndicadorConsulta);
        $arrayFinal = QuitarDuplicadosArray($aOrganizaciones, $aOrganizacionesReportadas);
    }

    // print_r2($aOrganizacionesReportadas);
    // print_r2($arrayFinal);

    // DEPENDIENDO DEL TIPO DE REPORTE, SE NECESITA ENVIAR DATOS NUMERICOS O DESAGREGADOS
    if($tipoReporte == 'numerico')
    {
        return count($arrayFinal);
    }

    if($tipoReporte == 'detalle')
    {
        // La sentencia sql para detalles necesita generar todos los datos de una organizacion incluso si esta se repite, para temas
        // de reporte              
        // Se ejecuta la sentencia sql para generar el detalle de la información
        $resIndicador = query($sqlIndicador);
        // Variables
        $aCofinanciamiento = array();                   // guarda codigos del campo cod_asesoria_asistencia_cofinanciamiento
        $aOrgSinFiltrar = array();                      // guarda codigos de organizaciones, puede tener duplicados
        $aSeguimiento = array();                        // guarda los datos del campo seguimiento_cof
        $aOperativas = array();                         // guarda datos del campo org_operativas
        $aMesConf = array();                            // gurada el mes en el que fue hecha el cofinanciamiento

        // variable que tendra el detalle de la informacion del indicador
        $aDetalleFinal = array();

        while($filaCof = mysql_fetch_array($resIndicador))
        {
            array_push($aOrgSinFiltrar, $filaCof['cod_u_organizaciones']);
            array_push($aCofinanciamiento, $filaCof['cod_asesoria_asistencia_cofinanciamiento']);
            array_push($aSeguimiento, $filaCof['seguimiento_cof']);
            array_push($aOperativas, $filaCof['org_operativas']);
            array_push($aMesConf, $filaCof['mesCof']);

        }        

        // Reviso las organizaciones a reportar que se encuentran en el array $arrayFinal
        foreach($arrayFinal as $valorOrg)
        {
            $aInfoOrg = GetInformacionOrg($valorOrg);
            $numSociosOrg = GetNumSocios($valorOrg);            
            array_push($aDetalleFinal, $zonaConsulta);                          // zona            
            array_push($aDetalleFinal, $aInfoOrg[3]);                           // nombre org
            array_push($aDetalleFinal, $aInfoOrg[2] . ' - ' . $aInfoOrg[1]);    // ruc definitivo y provisional
            array_push($aDetalleFinal, $numSociosOrg);                          // num socios
            
            // Para obtener los datos faltantes se debe recorrer el array de organizaciones sin filtrar
            // Cuando se encuentre el codigo en dicho array, tomamos su posicion y con ella 
            // consultamos los valores guardados en la misma posicion en los otros arrays como aCofinanciamiento
            $posArray = 0;
            $seguimientoCof = '';           // Guarda la info de seguimientos de acuerdo al codigo de la organizacion
            $orgOperativas = '';            // Guarda la info registrada en el campo org_operativas
            $fechasVisitas = '';            // Guarda las visitas (si las tuvieran) registradas por el codigo de cofinanciamiento
            $mesCofinanciamiento = '';      // Guarda el mes en el que se realizo el cofinanciamiento
            foreach($aOrgSinFiltrar as $valorOrgSinFiltrar)
            {
                if($valorOrgSinFiltrar == $valorOrg)
                {
                    $seguimientoCof .= "<p> COD CMI: " . $aCofinanciamiento[$posArray] . " - " . $aSeguimiento[$posArray] . "</p>";
                    $orgOperativas .= "<p> COD CMI: " . $aCofinanciamiento[$posArray] . " - " . $aOperativas[$posArray] . "</p>";
                    $mesCofinanciamiento .= "<p> COD CMI: " . $aCofinanciamiento[$posArray] . " - " . $nombreMeses[$aMesConf[$posArray]] . "</p>";
                    $auxVisitas = GetFechaVisita($aCofinanciamiento[$posArray], $anioConsulta);
                    foreach ($auxVisitas as $auxFecha) 
                    {                        
                        $fechasVisitas .= "<p> COD CMI: " . $aCofinanciamiento[$posArray] . " - " . $auxFecha . "</p>";
                    }
                }
                else
                {
                    $posArray++;
                }
            }

            // añado los datos encontrados al array de detalle
            array_push($aDetalleFinal, $seguimientoCof);
            array_push($aDetalleFinal, $orgOperativas);
            array_push($aDetalleFinal, $fechasVisitas);
            array_push($aDetalleFinal, $mesCofinanciamiento);                        // mes

        }
       
       return $aDetalleFinal;
    }
}

// ***********************************************************************************
// NUMERO DE OEPS PERTENECIENTES A SECTORES PRODUCTIVOS PRIORIZADOS EN LA ZONA, QUE RECIBIERON ASISTENCIA TECNICA ADMINISTRATIVA U OPERATIVA
// ***********************************************************************************
function Indicador03($zonaConsulta, $mesConsulta, $anioConsulta, $codIndicadorConsulta, $tipoOrg, $tipoReporte)
{
    // $sqlIndicador = "select * "
}





?>