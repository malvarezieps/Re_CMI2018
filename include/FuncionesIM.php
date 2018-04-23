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

	if($codIndicadorConsulta == 29 && $departamentoConsulta == 'IM')
	{
		$tableBody .= GetHeaderDetalle($departamentoConsulta, $codIndicadorConsulta);
		$detalleIndicador = MontoMercadoInternacional($codIndicadorConsulta, $mesIndicadorConsulta, $zonaIndicadorConsulta, $anioIndicadorConsulta, "detalle");	

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
		$a_resultadoIndicador = 0;
	}

	if($codIndicador == 27)
	{
		$a_resultadoIndicador = 0;
	}

	if($codIndicador == 29)
	{
		$a_resultadoIndicador = MontoMercadoInternacional($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador, 'monto');
		$a_resultadoIndicador = CambiarPuntoComa($a_resultadoIndicador);

		for($imes = 1; $imes <= $mesIndicador; $imes++)
		{
			$a_resultadoAcumuladoIndicador += MontoMercadoInternacional($codIndicador, $imes, $zonaIndicador, $anioIndicador, 'monto');
		}
		$a_resultadoAcumuladoIndicador = CambiarPuntoComa($a_resultadoAcumuladoIndicador);
		
		
	}

	if($codIndicador == 30)
	{
		$a_resultadoIndicador = 0;
	}

	if($codIndicador == 31)
	{
		$a_resultadoIndicador = 0;
	}

	if($codIndicador == 32)
	{
		$a_resultadoIndicador = 0;
	}

	if($codIndicador == 33)
	{
		$a_resultadoIndicador = 0;
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

function MontoMercadoInternacional($codIndicador, $mesIndicador, $zonaIndicador, $anioIndicador, $tipoReturn)
{
	
	// ***********************************************************************************************
	// INDICADOR : MONTO EN VENTA DE ORGANIZACIONES Y UEP AL MERCADO INTERNACIONAL APOYADAS POR IEPS
	// ***********************************************************************************************
	global $nombreMeses;
	if($tipoReturn == "monto")
	{
		$resultado = 0;
		$sqlMontoInternacional = "select sum(monto_contratacion) as montos from im_contratacion where tipo_contrato = 'internacional' and year(fecha_reporte) = " . $anioIndicador . " and month(fecha_reporte) = " . $mesIndicador . " and cod_zona = " . $zonaIndicador;
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
		

		$sqlMontoInternacional = "select monto_contratacion, cod_u_organizaciones, cod_provincia, cod_canton, cod_tipo_entidad_contratante, cod_entidad_contratante, fecha_adjudicacion, codigo_proceso, codigo_cpc, monto_contratacion, categoria_actividad_mp, identificacion_actividad_mp, circuito_economico from im_contratacion where tipo_contrato = 'internacional' and year(fecha_reporte) = " . $anioIndicador . " and month(fecha_reporte) = " . $mesIndicador . " and cod_zona = " . $zonaIndicador;
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



?>