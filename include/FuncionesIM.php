<?php
include ('../lib/dbconfig.php');
include ('FuncionesGlobales.php');

// *********************************************
// VARIABLES LOCALES

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

// Imprime en pantalla los resultados requeridos
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
	$nombreMeses = array('Enero', 'Febrero', 'Marzo',	'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
	$arrayIndicadores = GetIndicadores($departamentoConsulta, $anioConsulta);
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
		// SE DEBE SABER QUE ZONA DEBE IMPRIMIRSE EN PANTALLA
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

	if($codIndicadorConsulta == 26 && $departamentoConsulta == 'IM')
	{
		$detalleIndicador = MontoMercado($codIndicadorConsulta, $mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, "detalle", 'publica');	

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

	if($codIndicadorConsulta == 27 && $departamentoConsulta == 'IM')
	{
		$detalleIndicador = MontoMercado($codIndicadorConsulta, $mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, "detalle", 'privada');	

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

	if($codIndicadorConsulta == 29 && $departamentoConsulta == 'IM')
	{
		$detalleIndicador = MontoMercado($codIndicadorConsulta, $mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, "detalle", 'internacional');	

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

	if($codIndicadorConsulta == 30 && $departamentoConsulta == 'IM')
	{
		$detalleIndicador = GetOrgComercializando('org', $mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, 'detalle', $codIndicadorConsulta);

		// print_r2($detalleIndicador);	

		$tamDetalleIndicador = count($detalleIndicador);
		$tableBody .= "<tbody>";
		for($posicion = 0; $posicion < $tamDetalleIndicador; $posicion += 21)
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
			$tableBody .= "<td>" . $detalleIndicador[$posicion + 20] . "</td>";		
			$tableBody .= "</tr>";
		}
	}

	if($codIndicadorConsulta == 31 && $departamentoConsulta == 'IM')
	{
		$detalleIndicador = GetOrgComercializando('uep', $mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, 'detalle', $codIndicadorConsulta);

		// print_r2($detalleIndicador);	

		$tamDetalleIndicador = count($detalleIndicador);
		$tableBody .= "<tbody>";
		for($posicion = 0; $posicion < $tamDetalleIndicador; $posicion += 21)
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
			$tableBody .= "<td>" . $detalleIndicador[$posicion + 20] . "</td>";		
			$tableBody .= "</tr>";
		}
	}

	if($codIndicadorConsulta == 33 && $departamentoConsulta == 'IM')
	{
		$detalleIndicador = GetProcesosFerias($mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, 'detalle', $codIndicadorConsulta);

		// print_r2($detalleIndicador);	

		$tamDetalleIndicador = count($detalleIndicador);
		$tableBody .= "<tbody>";
		for($posicion = 0; $posicion < $tamDetalleIndicador; $posicion += 21)
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
			$tableBody .= "<td>" . $detalleIndicador[$posicion + 20] . "</td>";		
			$tableBody .= "</tr>";
		}
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
	// añado la meta programada
	$metaProgramada = GetMetaProgramada($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador);
	$metaProgramada = CambiarPuntoComa($metaProgramada);
	array_push($resultadoOrg, $metaProgramada);
	$a_resultadoIndicador = 0;
	$a_resultadoAcumuladoIndicador = 0;

	if($codIndicador == 26)
	{
		$a_resultadoIndicador = MontoMercado($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador, 'monto', 'publica');
		$a_resultadoIndicador = CambiarPuntoComa($a_resultadoIndicador);

		for($imes = 1; $imes <= $mesIndicador; $imes++)
		{
			$a_resultadoAcumuladoIndicador += MontoMercado($codIndicador, $imes, $zonaIndicador, $anioIndicador, 'monto', 'publica');
		}
		$a_resultadoAcumuladoIndicador = CambiarPuntoComa($a_resultadoAcumuladoIndicador);
	}

	if($codIndicador == 27)
	{
		$a_resultadoIndicador = MontoMercado($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador, 'monto', 'privada');
		$a_resultadoIndicador = CambiarPuntoComa($a_resultadoIndicador);

		for($imes = 1; $imes <= $mesIndicador; $imes++)
		{
			$a_resultadoAcumuladoIndicador += MontoMercado($codIndicador, $imes, $zonaIndicador, $anioIndicador, 'monto', 'privada');
		}
		$a_resultadoAcumuladoIndicador = CambiarPuntoComa($a_resultadoAcumuladoIndicador);
	}

	if($codIndicador == 29)
	{
		$a_resultadoIndicador = MontoMercado($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador, 'monto', 'internacional');
		$a_resultadoIndicador = CambiarPuntoComa($a_resultadoIndicador);

		for($imes = 1; $imes <= $mesIndicador; $imes++)
		{
			$a_resultadoAcumuladoIndicador += MontoMercado($codIndicador, $imes, $zonaIndicador, $anioIndicador, 'monto', 'internacional');
		}
		$a_resultadoAcumuladoIndicador = CambiarPuntoComa($a_resultadoAcumuladoIndicador);
		
		
	}

	if($codIndicador == 30)
	{
		$a_resultadoIndicador = GetOrgComercializando('org', $mesIndicador, $zonaIndicador, $anioIndicador, 'numero', $codIndicador);
		// $a_resultadoIndicador = 0;

		for($imes = 1; $imes <= $mesIndicador; $imes++)
		{
			$a_resultadoAcumuladoIndicador += GetOrgComercializando('org', $imes, $zonaIndicador, $anioIndicador, 'numero', $codIndicador);
		}
		// $a_resultadoAcumuladoIndicador = CambiarPuntoComa($a_resultadoAcumuladoIndicador);
	}

	if($codIndicador == 31)
	{
		$a_resultadoIndicador = GetOrgComercializando('uep', $mesIndicador, $zonaIndicador, $anioIndicador, 'numero', $codIndicador);
		// $a_resultadoIndicador = 0;

		for($imes = 1; $imes <= $mesIndicador; $imes++)
		{
			$a_resultadoAcumuladoIndicador += GetOrgComercializando('uep', $imes, $zonaIndicador, $anioIndicador, 'numero', $codIndicador);
		}
		
	}

	if($codIndicador == 32)
	{
		$a_resultadoIndicador = 0;
	}

	if($codIndicador == 33)
	{
		$a_resultadoIndicador = GetProcesosFerias($mesIndicador, $zonaIndicador, $anioIndicador, 'numero', $codIndicador);

		for($imes = 1; $imes <= $mesIndicador; $imes++)
		{
			$a_resultadoAcumuladoIndicador += GetProcesosFerias($imes, $zonaIndicador, $anioIndicador, 'numero', $codIndicador);
		}
	}

	if($codIndicador == 34)
	{
		$a_resultadoIndicador = 0;
	}

	array_push($resultadoOrg, $a_resultadoIndicador); // META MENSUAL EJECUTADA

	$porcentaje = CalculoPorcentaje($metaProgramada, $a_resultadoIndicador);	
	$porcentaje = CambiarPuntoComa($porcentaje);
	array_push($resultadoOrg, $porcentaje . "%");
	
	$metaTotal = 0;
	for($imes = 1; $imes <= 12; $imes++)
	{	
		$metaTotal += GetMetaProgramada($codIndicador, $imes, $zonaIndicador, $anioIndicador);
	}
	$metaTotal = CambiarPuntoComa($metaTotal);
	array_push($resultadoOrg, $metaTotal);

	// meta acumulada programada
	$metaAcumuladaProgramada = 0;
	for($imes = 1; $imes <= $mesIndicador; $imes++)
	{
		$metaAcumuladaProgramada += GetMetaProgramada($codIndicador, $imes, $zonaIndicador, $anioIndicador);
	}
	$metaAcumuladaProgramada = CambiarPuntoComa($metaAcumuladaProgramada);
	array_push($resultadoOrg, $metaAcumuladaProgramada);

	// meta acumulada ejecutada
	array_push($resultadoOrg, $a_resultadoAcumuladoIndicador);

	// Porcentaje de avance
	$porcentajeAvance = CalculoPorcentaje($metaAcumuladaProgramada, $a_resultadoAcumuladoIndicador);	
	$porcentajeAvance = CambiarPuntoComa($porcentajeAvance);
	array_push($resultadoOrg, $porcentajeAvance . "%");

	// Porcentaje de avance anual
	$porcentajeAnual = CalculoPorcentaje($metaTotal, $a_resultadoAcumuladoIndicador);
	$porcentajeAnual = CambiarPuntoComa($porcentajeAnual);
	array_push($resultadoOrg, $porcentajeAnual . "%");

	// Por ejecutar
	$porEjecutar = $metaTotal - $a_resultadoAcumuladoIndicador;
	$porEjecutar = CambiarPuntoComa($porEjecutar);
	array_push($resultadoOrg, $porEjecutar);

	// print_r2($resultadoOrg);
	return $resultadoOrg;
}

function MontoMercado($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador, $tipoReturn, $tipoContrato)
{
	
	// ***********************************************************************************************
	// INDICADOR : MONTO EN VENTA DE ORGANIZACIONES Y UEP AL MERCADO INTERNACIONAL APOYADAS POR IEPS
	// ***********************************************************************************************
	global $nombreMeses;
	if($tipoReturn == "monto")
	{
		$resultado = 0;
		$sqlMontoInternacional = "select sum(monto_contratacion) as montos from im_contratacion where tipo_contrato = '" . $tipoContrato . "' and year(fecha_reporte) = " . $anioIndicador . " and month(fecha_reporte) = " . $mesIndicador . " and cod_zona = " . $zonaIndicador;
		$resMontoInternacional = query($sqlMontoInternacional);
		while($filaMontoInternacional = mysql_fetch_array($resMontoInternacional))
		{
			$resultado = $filaMontoInternacional['montos'];
			if($resultado == '')
				$resultado = 0;
		}

		return $resultado;
	}

	if($tipoReturn == "detalle")
	{
		$resultado = array();
		

		$sqlMontoInternacional = "select monto_contratacion, cod_u_organizaciones, cod_provincia, cod_canton, cod_tipo_entidad_contratante, cod_entidad_contratante, fecha_adjudicacion, codigo_proceso, codigo_cpc, monto_contratacion, categoria_actividad_mp, identificacion_actividad_mp, circuito_economico from im_contratacion where tipo_contrato = '" . $tipoContrato . "' and year(fecha_reporte) = " . $anioIndicador . " and month(fecha_reporte) = " . $mesIndicador . " and cod_zona = " . $zonaIndicador;
		// echo $sqlMontoInternacional . "<br>";
		$resMontoInternacional = query($sqlMontoInternacional);
		$numFilasDetalle = mysql_num_rows($resMontoInternacional);
		if($numFilasDetalle > 0)
		{			
			while($filaDetalle = mysql_fetch_array($resMontoInternacional))
			{
				array_push($resultado, $zonaIndicador);		// Zona del indicador en consulta

				// Provincia			
				$provincia = GetProvincia($zonaIndicador, $filaDetalle['cod_provincia']);
				if($provincia != '')
				{
					array_push($resultado, $provincia);
				}
				else
				{
					array_push($resultado, "NO DEFINIDO");
				}

				// Canton
				$canton = GetCanton($filaDetalle['cod_provincia'], $filaDetalle['cod_canton']);
				// echo $canton . "<br>";
				if($canton != "0")
				{
					array_push($resultado, $canton);					
				}
				else
				{
					array_push($resultado, "NO DEFINIDO");	
				}

				// mes
				array_push($resultado, $nombreMeses[$mesIndicador]);
				// tipo entidad contratante
				$tipoEntidad = GetEntidadContratante($filaDetalle['cod_tipo_entidad_contratante']);
				array_push($resultado, strtoupper($tipoEntidad));
				//nombre entidad contratante
				$entidadContratante = GetNombreEntidadContratante($filaDetalle['cod_entidad_contratante']);
				array_push($resultado, $entidadContratante);
				// fecha adjudicadion
				array_push($resultado, $filaDetalle['fecha_adjudicacion']);
				// codigo proceso
				array_push($resultado, $filaDetalle['codigo_proceso']);
				// codigo cpc
				array_push($resultado, $filaDetalle['codigo_cpc']);
				// monto de contratacion
				array_push($resultado, $filaDetalle['monto_contratacion']);
				// sector priorizado
				array_push($resultado, strtoupper($filaDetalle['categoria_actividad_mp']));
				// bien o servicio
				array_push($resultado, strtoupper($filaDetalle['identificacion_actividad_mp']));

				// Tipo Org eps
				$informacionOrg = GetInformacionOrg($filaDetalle['cod_u_organizaciones']);
				array_push($resultado, strtoupper($informacionOrg[7]));

				// circuito economico
				$circuitoOrg = $filaDetalle['circuito_economico'];
				array_push($resultado, strtoupper($circuitoOrg));

				// nombre de la org
				array_push($resultado, $informacionOrg[3]);

				// siglas org
				array_push($resultado, "NO DEFINIDO");

				// ruc org
				array_push($resultado, $informacionOrg[1] . " - " . $informacionOrg[2]);

				// num socios
				$numSocios = GetNumSocios($informacionOrg[0]);
				array_push($resultado, $numSocios);

				// num empleados
				$numEmpleados = GetNumEmpleados($informacionOrg[0]);
				array_push($resultado, $numEmpleados);

				// nueva organizacion
				$esNuevaOrg = EsNuevaOrganizacion($filaDetalle['cod_u_organizaciones'], 'IM', $mesIndicador, $anioIndicador, 'auto');
				array_push($resultado, strtoupper($esNuevaOrg));

			}
		}
		else
		{
			array_push($resultado, $zonaIndicador);	// zona
			array_push($resultado, "SIN REGISTRO");	// provincia
			array_push($resultado, "SIN REGISTRO");	// canton
			array_push($resultado, "SIN REGISTRO");	// mes
			array_push($resultado, "SIN REGISTRO");	// tipo Entidad
			array_push($resultado, "SIN REGISTRO");	// Entidad Contratante
			array_push($resultado, "SIN REGISTRO");	// Fecha adjudicacion
			array_push($resultado, "SIN REGISTRO");	// codigo proceso
			array_push($resultado, "SIN REGISTRO");	// codigo cpc
			array_push($resultado, "SIN REGISTRO");	// monto de contratacion
			array_push($resultado, "SIN REGISTRO");	// sector priorizado
			array_push($resultado, "SIN REGISTRO");	// bien o servicio
			array_push($resultado, "SIN REGISTRO");	// tipo org eps
			array_push($resultado, "SIN REGISTRO");	// circuito economico
			array_push($resultado, "SIN REGISTRO");	// nombre de la organizacion
			array_push($resultado, "SIN REGISTRO");	// siglas org
			array_push($resultado, "SIN REGISTRO");	// ruc org
			array_push($resultado, "SIN REGISTRO");	// num socios
			array_push($resultado, "SIN REGISTRO");	// num empleados
			array_push($resultado, "SIN REGISTRO");	// nueva organizacion

		}

		// print_r2($resultado);
		return $resultado;

	}
	
}

function ImprimirConsolidado($accionRequerida)
{
	$codConsolidado = $_POST['codReporteConsolidado'];
	$anioConsulta = GetAnio();
	$mesConsulta = GetMes();
	$departamentoConsulta = GetDepartamento();

	if($codConsolidado == 0)
	{
		echo 'elegir_consolidado';
	}
	else
	{

		$tableBody = "<table class='table table-striped table-bordered table-hover'>";
		$tableBody .= GetHeaderTablaConsolidada($codConsolidado, 0);
		
		$arrayIndicadoresConsulta = array();
		$arrayIndicadoresNombres = array();

		if($codConsolidado == 273)
		{
			$sqlCodIndicador = "select * from indicador where departamento = 'IM' and anio_inicio <= " . $anioConsulta . " and anio_fin >= " . $anioConsulta . " and estado = 1";
			$resCodIndicador = query($sqlCodIndicador);
			while($filaCodIndicador = mysql_fetch_array($resCodIndicador))
			{
				array_push($arrayIndicadoresConsulta, $filaCodIndicador['cod_indicador']);
				array_push($arrayIndicadoresNombres, $filaCodIndicador['indicador']);
			}
		}

		if($mesConsulta == 0)
		{
			$mesConsulta = 12;
		}
		// echo $codConsolidado . "<br>";
		// echo $mesConsulta . "<br>";
		// print_r2($arrayIndicadoresConsulta);

		//**********************************************
		// siglas vc = variable consolidada
		//********************************************
		$vc_programadoConsolidado = 0;
		$vc_ejecutado = 0;
		$vc_porcentaje = 0;
		$vc_metaProgramadaAnual = 0;
		$vc_metaProgramadaMes = 0;
		$vc_metaConsolidadaMes = 0;
		$vc_metaProgramadaMes = 0;
		$vc_metaEjecutadaRango = 0;
		$vc_porcentajeEjecutado = 0;
		$vc_porcentajeAnual = 0;
		$vc_porEjecutar = 0;
		$vc_table = '';
		$posArray = 0;

		foreach($arrayIndicadoresConsulta as $valor)
		{
			$vc_programadoConsolidado = 0;
			$vc_ejecutado = 0;
			$vc_porcentaje = 0;
			$vc_metaProgramadaAnual = 0;
			$vc_metaProgramadaMes = 0;
			$vc_metaConsolidadaMes = 0;
			$vc_metaProgramadaMes = 0;
			$vc_metaEjecutadaRango = 0;
			$vc_porcentajeEjecutado = 0;
			$vc_porcentajeAnual = 0;
			for($izona = 1; $izona <= 9; $izona++)
			{
				for($imes = 1; $imes <= $mesConsulta; $imes++)
				{
					if($codConsolidado == 273)
					{
						$vc_programadoConsolidado += GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
						if($valor == 26)
						{
							$vc_ejecutado += MontoMercado($valor, $imes, $izona, $anioConsulta, 'monto', 'publica');		
							// $vc_metaEjecutadaRango = GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
						}
						if($valor == 27)
						{
							// $vc_programadoConsolidado += GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
							$vc_ejecutado += MontoMercado($valor, $imes, $izona, $anioConsulta, 'monto', 'privada');		
							// $vc_metaEjecutadaRango = GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
						}
						if($valor == 29)
						{
							// $vc_programadoConsolidado += GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
							$vc_ejecutado += MontoMercado($valor, $imes, $izona, $anioConsulta, 'monto', 'internacional');		
							// $vc_metaEjecutadaRango = GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
						}

						if($valor == 30)
						{
							// $vc_programadoConsolidado += GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
							$vc_ejecutado += GetOrgComercializando('org', $imes, $izona, $anioConsulta, 'numero', $valor);		
							// $vc_metaEjecutadaRango = GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
						}

						if($valor == 31)
						{
							// $vc_programadoConsolidado += GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
							$vc_ejecutado += GetOrgComercializando('uep', $imes, $izona, $anioConsulta, 'numero', $valor);		
							// $vc_metaEjecutadaRango = GetMetaProgramada($valor, $imes, $izona, $anioConsulta);
						}
						
					}
				}
			}
			
			
			$vc_metaProgramadaAnual = GetMetaProgramadaAnual($valor, $anioConsulta);
			$vc_metaConsolidadaMes = GetMetaMensualEjecutadaConsolidada($valor, $mesConsulta, $anioConsulta);
			$vc_metaProgramadaMes = GetMetaProgramadaConsolidada($valor, $mesConsulta, $anioConsulta);
			$vc_porcentaje = CalculoPorcentaje($vc_metaProgramadaMes, $vc_metaConsolidadaMes);
			$vc_porcentajeEjecutado = CalculoPorcentaje($vc_programadoConsolidado, $vc_ejecutado);
			$vc_porcentajeAnual = CalculoPorcentaje($vc_metaProgramadaAnual, $vc_metaConsolidadaMes);
			$vc_porEjecutar = $vc_metaProgramadaAnual - $vc_metaConsolidadaMes;

			$vc_table .= '<tr>
							<th scope="row">' . $valor . '</th>
							<td>' . $arrayIndicadoresNombres[$posArray] . '</td>
							<td>' . CambiarPuntoComa($vc_metaProgramadaMes) . '</td>
							<td>' . CambiarPuntoComa($vc_metaConsolidadaMes) . '</td>
							<td>' . CambiarPuntoComa($vc_porcentaje) . '</td>
							<td>' . CambiarPuntoComa($vc_metaProgramadaAnual) . '</td>
							<td>' . CambiarPuntoComa($vc_programadoConsolidado) . '</td>
							<td>' . CambiarPuntoComa($vc_ejecutado) . '</td>
							<td>' . CambiarPuntoComa($vc_porcentajeEjecutado) . '</td>
							<td>' . CambiarPuntoComa($vc_porcentajeAnual) . '</td>
							<td>' . CambiarPuntoComa($vc_porEjecutar) . '</td>							
						</tr>';
			
			$posArray++;
			

			// echo $valor . ' - ' . $arrayIndicadoresNombres[$posArray] . ' - ' . $vc_metaProgramadaMes  . ' - ' . $vc_metaConsolidadaMes . ' - ' . $vc_porcentaje . ' - ' . $vc_metaProgramadaAnual . ' - ' . $vc_programadoConsolidado . ' - ' . $vc_ejecutado . ' - ' . $vc_porcentajeEjecutado . ' - ' . $vc_porcentajeAnual . ' - ' . $vc_porEjecutar . '<br>';
		}

		$tableBody .= $vc_table;
		$tableBody .= "</table>";
		echo $tableBody;
	}
}

function GetMetaProgramadaAnual($codIndicador, $anioIndicador)
{
	$auxProgramada = 0;

	for($auxZona = 1; $auxZona <= 9; $auxZona++)
	{
		for($auxMes = 1; $auxMes <= 12; $auxMes++)
		{
			$auxProgramada += GetMetaProgramada($codIndicador, $auxMes, $auxZona, $anioIndicador);
		}
	}  

    return $auxProgramada;
}

function GetMetaMensualEjecutadaConsolidada($codIndicador, $mesIndicador, $anioIndicador)
{
	$auxMetaConsolidada = 0;

	for($auxZona = 1; $auxZona <= 12; $auxZona++)
	{
		if($codIndicador == 26)
		{
			$auxMetaConsolidada += MontoMercado($codIndicador, $mesIndicador, $auxZona, $anioIndicador, 'monto', 'publica');
		}

		if($codIndicador == 27)
		{
			$auxMetaConsolidada += MontoMercado($codIndicador, $mesIndicador, $auxZona, $anioIndicador, 'monto', 'privada');
		}

		if($codIndicador == 29)
		{
			$auxMetaConsolidada += MontoMercado($codIndicador, $mesIndicador, $auxZona, $anioIndicador, 'monto', 'internacional');
		}

		if($codIndicador == 30)
		{

			$auxMetaConsolidada += GetOrgComercializando('org', $mesIndicador, $auxZona, $anioConsulta, 'numero', $codIndicador);
		}

		if($codIndicador == 31)
		{
			$auxMetaConsolidada += GetOrgComercializando('uep', $mesIndicador, $auxZona, $anioConsulta, 'numero', $codIndicador);
		}
		
	}

	return $auxMetaConsolidada;
}

function GetMetaProgramadaConsolidada($codIndicador, $mesIndicador, $anioIndicador)
{
	$auxMetaConsolidada = 0;

	for($auxZona = 1; $auxZona <= 9; $auxZona++)
	{
		
		$auxMetaConsolidada += GetMetaProgramada($codIndicador, $mesIndicador, $auxZona, $anioIndicador);
		
	}

	return $auxMetaConsolidada;
}

function GetHeaderTablaConsolidada($codIndicador, $tipoTabla)
{
    $tHeader = "";
    $nombreMeses = array('TODOS', 'ENERO', 'FEBRERO', 'MARZO',  'ABRIL', 'MAYO', 'JUNIO', 'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE');
    $mesAux = GetMes();
    $tHeader = "<thead>";
    if($codIndicador == 273)
    {
        $tHeader .= "<tr>
                        <th scope='col'>NUMERACIÓN GPR DEL INDICADOR</th>
                        <th scope='col'>NOMBRE DE LOS INDICADORES</th>
                        <th scope='col'>META MENSUAL PROGRAMADA (" . $nombreMeses[$mesAux]  . ")</th>
                        <th scope='col'>META MENSUAL EJECUTADA (" . $nombreMeses[$mesAux]  . ")</th>
                        <th scope='col'>% DE AVANCE MENSUAL (" . $nombreMeses[$mesAux]  . ")</th>
                        <th scope='col'>META PROGRAMADA (ENERO - DICIEMBRE)</th>";
                        
                        $tHeader .= "<th scope='col'>META ACUMULADA PROGRAMADA (ENERO - " . $nombreMeses[$mesAux]  . ")</th>
                                    <th scope='col'>META ACUMULADA EJECUTADA (ENERO - " . $nombreMeses[$mesAux]  . ")</th>
                                    <th scope='col'>% AVANCE (ENERO - " . $nombreMeses[$mesAux]  . ")</th>";
                    
                        
                        $tHeader .= "<th scope='col'>% AVANCE ANUAL (ANUAL)</th>
                                    <th scope='col'>POR EJECUTAR</th>";
        $tHeader.= "</tr>";
    }

    if($codIndicador == 274 && $tipoTabla == 'sector')
    {
        $tHeader .= "<tr>
                        <th scope='col'>SECTOR</th>
                        <th scope='col'>MONTO EN VENTAS</th>";
        $tHeader.= "</tr>";
    }

    if($codIndicador == 274 && $tipoTabla == 'provincia')
    {
        $tHeader .= "<tr>
                        <th scope='col'>NUMERACIÓN GPR DEL INDICADOR</th>
                        <th scope='col'>NOMBRE DE LOS INDICADORES</th>
                        <th scope='col'>META MENSUAL PROGRAMADA (" . $nombreMeses[$mesAux]  . ")</th>
                        <th scope='col'>META MENSUAL EJECUTADA (" . $nombreMeses[$mesAux]  . ")</th>
                        <th scope='col'>% DE AVANCE MENSUAL (" . $nombreMeses[$mesAux]  . ")</th>
                        <th scope='col'>META PROGRAMADA (ENERO - DICIEMBRE)</th>";
                        
                        $tHeader .= "<th scope='col'>META ACUMULADA PROGRAMADA (ENERO - " . $nombreMeses[$mesAux]  . ")</th>
                                    <th scope='col'>META ACUMULADA EJECUTADA (ENERO - " . $nombreMeses[$mesAux]  . ")</th>
                                    <th scope='col'>% AVANCE (ENERO - " . $nombreMeses[$mesAux]  . ")</th>";
                    
                        
                        $tHeader .= "<th scope='col'>% AVANCE ANUAL (ANUAL)</th>
                                    <th scope='col'>POR EJECUTAR</th>";
        $tHeader.= "</tr>";
    }

    $tHeader .= "</thead>";

    
    return $tHeader;
}

function GetOrgComercializando($tipoOrg, $mesIndicador, $zonaIndicador, $anioIndicador, $tipoReturn, $codIndicador)
{
	global $nombreMeses;
	// **********************************************************
	// Se obtiene las org o uep reportadas en el mes seleccionado
	// ***********************************************************
	$arrayOrg = array();
	$sqlOrgReportadas = "select * from u_organizaciones o inner join im_contratacion c on (o.cod_u_organizaciones = c.cod_u_organizaciones) where o.tipo = '" . $tipoOrg . "' and c.servicios in (2, 3, 4, 5, 6, 7, 8, 9, 10, 11) and year(c.fecha_reporte) = " . $anioIndicador . " and month(c.fecha_reporte) = " . $mesIndicador . " and c.cod_zona = " . $zonaIndicador . " order by o.cod_u_organizaciones";
	$resOrgReportadas = query($sqlOrgReportadas);
	while($filaOrg = mysql_fetch_array($resOrgReportadas))
	{
		array_push($arrayOrg, $filaOrg['cod_u_organizaciones']);
	}

	$arrayOrg = array_unique($arrayOrg);
	$arrayOrg = array_values($arrayOrg);

	// **********************************************************
	// Se revisa si las org consultadas no fueron reportadas en meses anteriores		
	// **********************************************************

	$arrayOrg = RevisarOrgReportadas($arrayOrg, $codIndicador, $mesIndicador, $anioIndicador, $zonaIndicador);

	if($tipoReturn == 'numero')
	{
		// *******************************************************************
		// Devuelve el numero de organizaciones o uep reportadas
		// *******************************************************************
		return count($arrayOrg); 
	}

	if($tipoReturn == 'detalle')
	{
		
		
		$detalles = array();
		$detalles = GetDetalle($mesIndicador, $zonaIndicador, $anioIndicador, $arrayOrg, $sqlOrgReportadas);
		return $detalles;


	}
}

function RevisarOrgReportadas($orgArray, $codIndicador, $mesIndicador, $anioIndicador, $zonaIndicador)
{
	$auxOrg = $orgArray;
	$sqlOrgReportadasAnt = "";
	if($codIndicador == 30)
	{
		$sqlOrgReportadasAnt = "select o.cod_u_organizaciones, c.servicios from u_organizaciones o inner join im_contratacion c on (o.cod_u_organizaciones = c.cod_u_organizaciones) where o.tipo = '" . $tipoOrg . "' and c.servicios in (2, 3, 4, 5, 6, 7, 8, 9, 10, 11) and year(c.fecha_reporte) = " . $anioIndicador . " and month(c.fecha_reporte) < " . $mesIndicador . " and c.cod_zona = " . $zonaIndicador . " group by o.cod_u_organizaciones order by o.cod_u_organizaciones";
	}

	$resOrgReportadasAnt = query($sqlOrgReportadasAnt);
	$auxOrgAnt = array();
	while($filaOrgAnt = mysql_fetch_array($resOrgReportadasAnt))
	{
		array_push($auxOrgAnt, $filaOrgAnt['cod_u_organizaciones']);
	}

	$posArray = 0;
	foreach ($auxOrg as $valor) 
	{
		if(in_array($valor, $auxOrgAnt))
		{
			unset($auxOrg[$posArray]);
		}
		$posArray++;
	}

	return $auxOrg;
}

function GetRegistroOrg($codOrg, $mesIndicador, $anioIndicador, $zonaIndicador)
{
	$sqlRegistros = "select * from im_contratacion where cod_u_organizaciones = " . $codOrg . " and month(fecha_reporte) = " . $mesIndicador . " and year(fecha_reporte) = " . $anioIndicador . " and cod_zona = " . $zonaIndicador;

	$resRegistros = query($sqlRegistros);
	$arrayRegistros = array();

	while($filaRegistros = mysql_fetch_array($resRegistros))
	{
		array_push($arrayRegistros, $filaRegistros['cod_contratacion']);
	}

	return $arrayRegistros;

}

function GetMarcaColectiva($mesIndicador, $zonaIndicador, $anioIndicador, $tipoReturn, $codIndicador)
{
	// por definir
}

function GetProcesosFerias($mesIndicador, $zonaIndicador, $anioIndicador, $tipoReturn, $codIndicador)
{
	$sqlRegistros = "select * from im_contratacion c where month(c.fecha_reporte) = " . $mesIndicador . " and c.servicios in (3, 6, 7) and c.cod_zona = " . $zonaIndicador . " and year(c.fecha_reporte) = " . $anioIndicador . " group by c.cod_u_organizaciones";
	// echo $sqlRegistros . "<br>";
	$resRegistros = query($sqlRegistros);
	$arrayRegistros = array();

	while($filaRegistro = mysql_fetch_array($resRegistros))
	{
		array_push($arrayRegistros, $filaRegistro['cod_u_organizaciones']);
	}

	// Por seguridad eliminemos duplicados
	// print_r2($arrayRegistros);
	$arrayRegistros = array_unique($arrayRegistros);
	$arrayRegistros = array_values($arrayRegistros);

	if($tipoReturn == 'numero')
	{
		

		return count($arrayRegistros);
	}

	if($tipoReturn == 'detalle')
	{
		$detalles = GetDetalle($mesIndicador, $zonaIndicador, $anioIndicador, $arrayRegistros, $sqlRegistros);
		return $detalles;
	}
}

function GetDetalle($mesIndicador, $zonaIndicador, $anioIndicador, $arrayOrg, $sqlIndicador)
{
	global $nombreMeses;
	$arrayDetalle = array();
	foreach ($arrayOrg as $valor) 
	{
		array_push($arrayDetalle, $zonaIndicador);

		$infoOrg = GetInformacionOrg($valor);
		// print_r2($infoOrg);
		$provincia = GetProvincia($infoOrg[15], $infoOrg[16]);
		if($provincia != '')
		{
			array_push($arrayDetalle, $provincia);
		}
		else
		{
			array_push($arrayDetalle, "NO DEFINIDO");				
		}
		

		$canton = GetCanton($infoOrg[16], $infoOrg[17]);
		if($canton != "0")
		{
			array_push($arrayDetalle, $canton);
		}
		else
		{
			array_push($arrayDetalle, "NO DEFINIDO");				
		}
		

		array_push($arrayDetalle, $nombreMeses[$mesIndicador]);

		// Tomar todos los tipos de entidades contratantes
		// echo $sqlIndicador . "<br>";
		$resOrgReportadas = query($sqlIndicador);
		$tipoEntidad = '\\ ';
		$entidadContratante = '\\ ';
		$fechaAdjudicacion = '\\ ';
		$codProceso = '\\ ';
		$codCpc ='\\ ';
		$montoCon = '\\ ';
		$sectorPrio = '\\ ';
		$bienServicio = '\\ ';
		$circuitoOrg = '';
		$codContratacion = '\\ ';
		while($filaOrg = mysql_fetch_array($resOrgReportadas))
		{
			if($filaOrg['cod_u_organizaciones'] == $valor)
			{
				$tipoEntidad .= GetEntidadContratante($filaOrg['cod_tipo_entidad_contratante']) . ' \\ ';
				//nombre entidad contratante
				$entidadContratante .= GetNombreEntidadContratante($filaOrg['cod_entidad_contratante']) . ' \\ ';
				//fecha adjudicacion contrato
				$fechaAdjudicacion .= $filaOrg['fecha_adjudicacion'] . ' \\ ';
				// codigo proceso
				$codProceso .= $filaOrg['codigo_proceso'] . ' \\ ';
				// codigo cpc
				$codCpc .= $filaOrg['codigo_cpc'] . ' \\ ';
				// monto contratacion
				$montoCon .= CambiarPuntoComa($filaOrg['monto_contratacion']) . ' \\ ';
				// sector priorizado
				$sectorPrio = strtoupper($filaOrg['categoria_actividad_mp']);
				// bien servicio
				$bienServicio = strtoupper($filaOrg['identificacion_actividad_mp']);
				// circuito economico
				$circuitoOrg = $filaOrg['circuito_economico'];
				// cod contratacion
				$codContratacion .= $filaOrg['cod_contratacion'] . ' \\ ';
			}
		}
		array_push($arrayDetalle, $tipoEntidad);

		// Entidad contratante
		array_push($arrayDetalle, $entidadContratante);
		
		// fecha adjudicadion
		array_push($arrayDetalle, $fechaAdjudicacion);

		// codigo proceso
		array_push($arrayDetalle, $codProceso);
		// codigo cpc
		array_push($arrayDetalle, $codCpc);
		// monto de contratacion
		array_push($arrayDetalle, $montoCon);
		// sector priorizado
		array_push($arrayDetalle, $sectorPrio);
		// bien o servicio
		array_push($arrayDetalle, $bienServicio);

		// Tipo Org eps
		if($infoOrg[7] == -1)
		{
			array_push($arrayDetalle, strtoupper('uep'));	
		}
		else
		{
			array_push($arrayDetalle, strtoupper($infoOrg[7]));
		}

		// circuito economico
		if($circuitoOrg == -1)
		{
			array_push($arrayDetalle, strtoupper('no'));	
		}
		else
		{
			array_push($arrayDetalle, strtoupper($circuitoOrg));
		}

		// nombre de la org
		array_push($arrayDetalle, $infoOrg[3]);

		// siglas org
		array_push($arrayDetalle, "NO DEFINIDO");

		// ruc org
		array_push($arrayDetalle, $infoOrg[1] . " - " . $infoOrg[2]);

		// num socios
		$numSocios = GetNumSocios($valor);
		array_push($arrayDetalle, $numSocios);

		// num empleados
		$numEmpleados = GetNumEmpleados($valor);
		array_push($arrayDetalle, $numEmpleados);

		// nueva organizacion
		$esNuevaOrg = EsNuevaOrganizacion($valor, 'IM', $mesIndicador, $anioIndicador, 'auto');
		array_push($arrayDetalle, strtoupper($esNuevaOrg));

		// cod contratacion
		array_push($arrayDetalle, $codContratacion);
	}

	return $arrayDetalle;
}


?>