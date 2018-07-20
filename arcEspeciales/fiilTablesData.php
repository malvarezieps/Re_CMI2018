<?php 
include ('../lib/dbconfig.php');


FillAsistencias();

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