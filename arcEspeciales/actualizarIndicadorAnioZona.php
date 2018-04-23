<?php 
include ('../lib/dbconfig.php');

$sql = "select * from indicador group by cod_indicador";
$res = query($sql);
while($filaSql = mysql_fetch_array($res))
{
	$codindicador = $filaSql['cod_indicador'];

	$sql2 ="select * from indicador_zona where cod_indicador = " . $codindicador;
	$res2 = query($sql2);
	while($filaSql2 = mysql_fetch_array($res2))
	{
		$codIndicadorZona = $filaSql2['cod_indicador_zona'];
		$zona = $filaSql2['cod_zona'];
		$upd = "update indicador_zona_mes set cod_indicador = " . $codindicador . ", zona = " . $zona . " where cod_indicador_zona = " . $codIndicadorZona;
		$resUpd = query($upd);
		echo $upd . "<br>";

	}

}


?>