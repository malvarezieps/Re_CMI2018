<?php
include ('../lib/dbconfig.php');




function GenerarIndicador()
{
	$indicadores = array('Porcentaje de personas capacitadas que alcanzan resultados satisfactorios en su evaluación de conocimientos adquiridos en temática EPS',
							'Número de redes de actores de la EPS creadas y fortalecidas en procesos de formación y capacitación',
							'	Número de actores (OEPS, UEP y grupos de interés capacitados en los ejes organizativos, administrativos y/o  técnicos',
							'Número de circuitos económicos fortalecidos en capacitación organizativa, administrativa y/o técnica',
							'Porcentaje de compromisos adquiridos con los actores de IEPS en los diálogos sociales, que se han cumplido de acuerdo a la competencia del IEPS',
							'Número de instituciones públicas y privadas involucradas en el proceso de capacitación',
							'Porcentaje de personas capacitadas con entidades especializadas que alcanzan resultados satisfactorios en su evaluación de conocimientos adquiridos en temática de innovación y valor agregado');

	$lengthIndicador = count($indicadores);
	// echo $lengthIndicador;

	for($i = 0; $i < $lengthIndicador; $i++)
	{
		$indicadorName = strtoupper($indicadores[$i]);
		$sqlIndicadorExiste = "select * from indicador where indicador = '" . $indicadorName . "'";
		$resIndicadorExiste = query($sqlIndicadorExiste);
		$numFilaIndicadores = mysql_num_rows($resIndicadorExiste);

		if($numFilaIndicadores == 0)
		{
			// Se realiza la insertion de toda la informacion
			
			$sqlInsertIndicador = "insert into indicador (indicador, estado, departamento, anio_inicio, anio_fin) values ('" . $indicadorName ."', 1, 'FA', 2018, 2022)";
			$resInsertIndicador = query($sqlInsertIndicador);
			echo "deal " . $indicadores[$i] . "<br>";
		}
		else
		{
			$codIndicador = 0;
			while($filaIndicodores = mysql_fetch_array($resIndicadorExiste))
			{
				$codIndicador = $filaIndicodores['cod_indicador'];
				// echo $codIndicador . "<br>";

				for($zona = 1; $zona < 10; $zona++)
				{
					for($mes = 1; $mes < 13; $mes++)
					{
						$sqlZonaIndicador = "select * from indicador_zona_mes where cod_indicador = " . $codIndicador . " and zona = " . $zona . " mes = " . $mes;
						echo $sqlZonaIndicador . "<br>";	
					}
					
					
					
				}				

			}
		}
		
	}
}


GenerarIndicador();
?>