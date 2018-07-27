<?php 
include ('../lib/dbconfig.php');

FillSectorProductivoData();

function FillVisitas()
{
	$sql = "select * from fp_asesoria_asistencia_cofinanciamiento where year(fecha_reporte) = 2018";
	$res = query($sql);
	$anio = 2018;

	while($filaSql = mysql_fetch_array($res))
	{
		$codCof = $filaSql['cod_asesoria_asistencia_cofinanciamiento'];
		$ranNumber = mt_rand(1, 5);
		$mes = mt_rand(1, 12);
		$dia = 1;
		if($mes == 2)
		{
			$dia = mt_rand(1, 28);
		}
		else
		{
			$dia = mt_rand(1, 31);	
		}
		

		for($i = 0; $i <= $ranNumber; $i++)
		{
			$fecha = $anio . '-' . $mes . '-' . $dia;
			$sqlInsert = "insert into fp_visitas_cofinanciamiento (cod_asesoria_asistencia_cofinanciamiento, fecha_visita, fecha_registro) 	values (" . $codCof . ", '" . $fecha . "', '" . $fecha . "')";
			query($sqlInsert);
			echo $sqlInsert . " - Execute<br>";
		}	

		echo 'Deal<br>';

	}
}

function FillAsistencias()
{
	$sql = "select * from fp_asesoria_asistencia_cofinanciamiento where year(fecha_reporte) = 2018";
	$res = query($sql);

	while($fila = mysql_fetch_array($res))
	{
		$codLinea = $fila['cod_asesoria_asistencia_cofinanciamiento'];
		$opcRand = mt_rand(1, 3);
		$sqlUpdate = '';
		if($opcRand == 1)
		{
			$sqlUpdate = "update fp_asesoria_asistencia_cofinanciamiento set tipo_asistencia = 'tecnica' where cod_asesoria_asistencia_cofinanciamiento = " . $codLinea;
		}
		if($opcRand == 2)
		{
			$sqlUpdate = "update fp_asesoria_asistencia_cofinanciamiento set tipo_asistencia = 'administrativa' where cod_asesoria_asistencia_cofinanciamiento = " . $codLinea;
		}
		if($opcRand == 3)
		{
			$sqlUpdate = "update fp_asesoria_asistencia_cofinanciamiento set tipo_asistencia = 'operativa' where cod_asesoria_asistencia_cofinanciamiento = " . $codLinea;
		}

		query($sqlUpdate);
		echo $sqlUpdate . "<br>";
	}

	echo "Deal all works!!<br>";
}

function FillAccesoCredito()
{
	$sql = "select * from fp_asesoria_asistencia_cofinanciamiento where year(fecha_reporte) = 2018";
	$res = query($sql);
	$arrayDestino = array('Capital de Trabajo', 'Activos Productivos', 'Asistencia Tecnica', 'Otros');
	$montoCredito = array('$1.000 A $5.000',
							'$5.001 A $10.000',
							'$10.001 A $15.000',
							'$15.001 A $20.000',
							'$20.001 A $30.000',
							'$30.001 A $40.000',
							'$40.001 A $50.000',
							'$50.001 A $60.000',
							'MAS DE $60.000');
	$plazoCredito = array('DE 30 A 365 DIAS',
							'DE 366 A 730 DIAS',
							'DE 731 A 1.095 DIAS',
							'MÁS DE TRES AÑOS');

	$interesCredito = array('5% al 10%', '11% al 15%', 'mas 15%');
	$acompanamientoCredito = array('1 a 3 visitas', '4 a 7 visitas', 'mas de 8 visitas');
	$programaCredito = array('BanEcuador', 'Impulso Joven', 'Banco del Pueblo', 'Otros');

	while($fila = mysql_fetch_array($res))
	{
		$codLinea = $fila['cod_asesoria_asistencia_cofinanciamiento'];
		$opcRand = mt_rand(1, 2);
		$sqlUpdate = '';

		$valorMontoCredito = '';
		$valorMontoPlazo = '';
		$valorInteresCredito = '';
		$valorAcompanamiento = '';
		$valorProgramaProducto = '';
		$esArticulado = '';
		$accedeCredito = '';
		$destinoCredito = '';
		if($opcRand == 1)
		{

			// Tiene credito			

			$randomico = mt_rand(0, 3);
			$destinoCredito = $arrayDestino[$randomico];
			$esArticulado = 'si';
			$accedeCredito = 'si';			
			$randomico = mt_rand(0, 9);
			$valorMontoCredito = $montoCredito[$randomico];
			$randomico = mt_rand(0, 3);
			$valorMontoPlazo = $plazoCredito[$randomico];
			$randomico = mt_rand(0, 2);
			$valorInteresCredito = $interesCredito[$randomico];
			$randomico = mt_rand(0, 2);
			$valorAcompanamiento = $acompanamientoCredito[$randomico];
			$randomico = mt_rand(0, 3);
			$valorProgramaProducto = $programaCredito[$randomico];		
			
		}
		else
		{

			$destinoCredito = 'no';
			$esArticulado = 'no';
			$accedeCredito = 'no';						
			$valorMontoCredito = 0;			
			$valorMontoPlazo = 0;
			$valorInteresCredito = 0;			
			$valorAcompanamiento = 'no';			
			$valorProgramaProducto = 'no';
		}

		$sqlUpdate = "update fp_asesoria_asistencia_cofinanciamiento set destino_credito = '" . $destinoCredito . "', credito_articulado = '" . $esArticulado . "', accede_credito = '" . $accedeCredito . "', monto_credito = '" . $valorMontoCredito . "', plazo_credito = '" . $valorMontoPlazo . "', interes_credito = '" . $valorInteresCredito . "', acompanamiento = '" . $valorAcompanamiento . "', programa_producto = '" . $valorProgramaProducto . "'  where cod_asesoria_asistencia_cofinanciamiento = " . $codLinea;

		query($sqlUpdate);
		echo $sqlUpdate . "<br>";
	}

	echo "Deal all works!!<br>";
}

function FillTecnologiaData()
{
	$sql = "select * from fp_asesoria_asistencia_cofinanciamiento where year(fecha_reporte) = 2018";
	$res = query($sql);
	$arraySectorTec = array('sector 01', 'sector 02', 'sector 03', 'sector 04');
	$aAccesoTec = array('FACTURAS POR LA COMPRA DE RECURSOS',
							'APLICACION DE CONOCIMIENTOS ADQUIRIDOS',
							'MEJORAMIENTO DE FLUJO DE PROCESOS'
							);	

	while($fila = mysql_fetch_array($res))
	{
		$codLinea = $fila['cod_asesoria_asistencia_cofinanciamiento'];
		$opcRand = mt_rand(1, 2);
		$sqlUpdate = '';

		$accedioTec = '';
		$valorSectorTec = '';
		$valorAccesoTecMediante = '';
		
		if($opcRand == 1)
		{

			$accedioTec = 'si';			

			$randomico = mt_rand(0, 3);
			$valorSectorTec = $arraySectorTec[$randomico];
			
			$randomico = mt_rand(0, 4);
			if($randomico >= 0 && $randomico <= 2)
			{
				$valorAccesoTecMediante = $aAccesoTec[$randomico];
			}
			if($randomico == 3)
			{
				$inicio = mt_rand(0, 1);
				$fin = mt_rand($inicio, 2);
				if($inicio <= $fin)
				{
					for($i = $inicio; $i <= $fin; $i++)
					{
						if($i == $fin)
						{
							$valorAccesoTecMediante .= $aAccesoTec[$i];
						}
						else
						{
							$valorAccesoTecMediante .= $aAccesoTec[$i] . ";";	
						}
					}	
				}				
			}

			if($randomico == 4)
			{
				$valorAccesoTecMediante = $aAccesoTec[0] . ";" . $aAccesoTec[1] . ";" . $aAccesoTec[2] ;
			}			
			
		}
		else
		{
			$accedioTec = 'no';				
		}

		$sqlUpdate = "update fp_asesoria_asistencia_cofinanciamiento set accede_tecnologia = '" . $accedioTec . "', sector_incorpora_tecnologia = '" . $valorSectorTec . "', acceso_tecnologia_mediante = '" . $valorAccesoTecMediante . "'  where cod_asesoria_asistencia_cofinanciamiento = " . $codLinea;

		query($sqlUpdate);
		echo $sqlUpdate . "<br>";
	}

	echo "Deal all works!!<br>";
}

function FillSectorProductivoData()
{
	$sql = "select * from fp_asesoria_asistencia_cofinanciamiento where year(fecha_reporte) = 2018";
	$res = query($sql);
	$arraySectorPriorizado = array('sector 01', 'sector 02', 'sector 03', 'sector 04');
		

	while($fila = mysql_fetch_array($res))
	{
		$codLinea = $fila['cod_asesoria_asistencia_cofinanciamiento'];
		$opcRand = mt_rand(1, 2);
		$sqlUpdate = '';

		$implementado = '';
		$sectorProductivo = '';
		$obtuvoRespuestaAfirmativa = '';
		$fechaRespuestaAfirmativa = '';

		
		if($opcRand == 1)
		{

			$randomico = mt_rand(0, 3);
			$sectorProductivo = $arraySectorPriorizado[$randomico];
			$implementado = 'si';
			$obtuvoRespuestaAfirmativa = 'si';
			$anio = '2018';
			$mesR = mt_rand(1, 12);
			$diaR = 0;
			if($mesR == 2)
			{
				$diaR = mt_rand(1, 28);	
			}
			else
			{
				$diaR = mt_rand(1, 30);				
			}
			$fechaRespuestaAfirmativa = $anio . "-" . $mesR . "-" . $diaR;
					
			
		}
		else
		{
			$sectorProductivo = 'no';
			$implementado = 'no';
			$obtuvoRespuestaAfirmativa = 'no';
			$fechaRespuestaAfirmativa = 0;
		}

		if($fechaRespuestaAfirmativa == 0)
		{
			$sqlUpdate = "update fp_asesoria_asistencia_cofinanciamiento set sector_productivo_priorizado = '" . $sectorProductivo . "', plan_negocio_implementado = '" . $implementado . "', plan_negocio_respuesta_afirmativa = '" . $obtuvoRespuestaAfirmativa . "', plan_negocio_fecha_respuesta = null  where cod_asesoria_asistencia_cofinanciamiento = " . $codLinea;			
		}
		else
		{
			$sqlUpdate = "update fp_asesoria_asistencia_cofinanciamiento set sector_productivo_priorizado = '" . $sectorProductivo . "', plan_negocio_implementado = '" . $implementado . "', plan_negocio_respuesta_afirmativa = '" . $obtuvoRespuestaAfirmativa . "', plan_negocio_fecha_respuesta = '" . $fechaRespuestaAfirmativa . "'  where cod_asesoria_asistencia_cofinanciamiento = " . $codLinea;			
		}


		query($sqlUpdate);
		echo $sqlUpdate . "<br>";
	}

	echo "Deal all works!!<br>";
}