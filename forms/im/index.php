<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Indicadores Intercambio</title>
	<link rel="stylesheet" href="../../css/bootstrap.css">
	<link rel="stylesheet" href="../../css/estilos.css">
</head>
<body>
	<?php 
	include('../../lib/dbconfig.php');					// conexion a base de datos	
	 ?>
	<div class="container-fluid">
		<nav class="navbar navbar-inverse">
			<div class="container">				
				<!-- <span class="icon-menu"></span>
				<span class="tFiltro">Filtros</span> -->
				<div class="row">
					<div class="col-sm-6">
						<!-- Indicador -->
						<label for="selIndicador">Indicador</label>
						<select class="indicador" id="indicador">
							<option value="0" selected> TODOS </option>
							<?php
							$anio = date('Y');
							$departamento = 'IM';							
							$sqlIndicadores = "select cod_indicador, indicador from indicador where estado = 1 and departamento = '" . $departamento . "' and anio_inicio <= " . $anio . " and anio_fin >= " . $anio;
							$resIndicadores = query($sqlIndicadores);
							$arrayIndicadores = array();
							while($filaIndicadores = mysql_fetch_array($resIndicadores))
							{
								array_push($arrayIndicadores, $filaIndicadores['cod_indicador']);
								array_push($arrayIndicadores, $filaIndicadores['indicador']);
							}							
							$lengthInd = count($arrayIndicadores);
							echo $lengthInd;

							for($i = 0; $i < $lengthInd; $i = $i + 2)
							{
								echo "<option value='" . $arrayIndicadores[$i] . "'>" . $arrayIndicadores[$i + 1] . "</option>";
							}
							?>								
						</select>
					</div>
					<div class="col-sm-2">
						<!-- Zonas -->
						<label for="selZonas">Zonas</label>
						<select class="zonas" id="zonas">
							<option value="0" selected> TODOS </option>
							<?php
							$sqlZonas = "select * from u_zona";
							$resZonas = query($sqlZonas);
							while($filaZonas = mysql_fetch_array($resZonas))
							{
								echo "<option value='" . $filaZonas['cod_zona'] . "'>" . $filaZonas['zona'] . "</option>";
							}
							?>
						</select>
					</div>
					<div class="col-sm-2">
						<!-- Meses -->
						<label for="selMeses">Meses</label>
						<select class="meses" id="meses">
							<option value="0" selected> TODOS </option>
							<?php
							$sqlMeses = "select codigo, valor from catalogo where tipo = 'meses'";
							$resMeses = query($sqlMeses);
							while($filaMeses = mysql_fetch_array($resMeses))
							{
								echo "<option value='" . $filaMeses['codigo'] . "'>" . $filaMeses['valor'] . "</option>";
							}
							?>
						</select>
					</div>
					<div class="col-sm-2">
						<!-- Anio -->
						<label for="selAnio">Año</label>
						<select class="anio" id="anio">								
							<?php
							$sqlAnio = "select codigo, valor from catalogo where tipo = 'anio'";
							$resAnio = query($sqlAnio);
							while($filaAnio = mysql_fetch_array($resAnio))
							{
								echo "<option value='" . $filaAnio['codigo'] . "' selected>" . $filaAnio['valor'] . "</option>";
							}
							?>
						</select>
					</div>
				</div>
				
			</div>
		</nav>		
	</div>
	<div class="container-fluid">
		<!-- <div id="progress" class="progress">
			<div id="barraProgreso" class="progress-bar myBar" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100" style="width: 5%;">5%</div>
		</div> -->
		<div class="row">
			<div class="col-sm-12 text-center controles">
				<button class="buscarIndicador btn btn-info" id="btnGenerarIndicador">Generar Indicador</button>	
				<button class="buscarIndicador btn btn-info" id="btnGuardarIndicadores">Guardar Indicador</button>
				<button class="buscarIndicador btn btn-info" id="btnExportarReporte">Exportar a Excel</button>
				<label for="archivo" class="btn btn-info">Subir Archivo</label>
				<input type="file" name="archivo" id="archivo" />

			</div>
		</div>
		<div class="row reporte">
			<div class="col-md-12 contenedorTabla" id='reporteGenerado'>				
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th scope="col"># GPR</th>
							<th scope="col">Indicador</th>
							<th scope="col">Meta Mensual Programada (mes)</th>
							<th scope="col">Meta Mensual Ejecutada (mes)</th>
							<th scope="col">% de Avance Mensual (mes)</th>
							<th scope="col">Meta Programada (Ene-Dic)</th>
							<th scope="col">Meta Acumulada Programada (Ene-messeleccionado)</th>
							<th scope="col">Meta Acumulada Ejecutada (Ene-messeleccionado)</th>
							<th scope="col">% de Avance(Ene-messeleccionado)</th>
							<th scope="col">% Avance Anual</th>
							<th scope="col">Justificación Sobre Cumplimiento o No Cumplimiento</th>
							<th scope="col">Zona</th>
							<th scope="col">Mes</th>
							<th scope="col">Gráfico</th>
							<th scope="col">Detalle</th>
							<th scope="col" class="oculto">CodigoIndicador</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th scope='row'>1</th>
							<td id="1-2">Monto en ventas de exportaciones de las OEPS y UEP apoyadas por el IEPS</td>
							<td id="1-3">100000,12</td>
							<td id="1-4">100000,12</td>
							<td id="1-5">100%</td>
							<td id="1-6">100000,12</td>
							<td id="1-7">100000,12</td>
							<td id="1-8">100000,12</td>
							<td id="1-9">80%</td>
							<td id="1-10">12%</td>
							<td id="1-11"></td>
							<td id="1-12">1</td>
							<td id="1-13">Enero</td>
							<td><button id="1-14" onclick="VerGrafica(this.id)" type="button" class="btn btn-warning">Ver</button></td>
							<td><button id="1-15" onclick="DesplegarInfo(this.id, 'IM')" type="button" class="btn btn-warning">Desplegar</button></td>
							<td id="1-16" class="oculto">17</td>
						</tr>
						<tr>
							<th scope='row'>2</th>
							<td id="2-2">Monto en ventas de exportaciones de las OEPS y UEP apoyadas por el IEPS</td>
							<td id="2-3">100000,12</td>
							<td id="2-4">100000,12</td>
							<td id="2-5">100%</td>
							<td id="2-6">100000,12</td>
							<td id="2-7">100000,12</td>
							<td id="2-8">100000,12</td>
							<td id="2-9">80%</td>
							<td id="2-10">12%</td>
							<td id="2-11"></td>
							<td id="2-12">1</td>
							<td id="2-13">Febrero</td>
							<td><button id="2-14" onclick="VerGrafica(this.id)" type="button" class="btn btn-warning">Ver</button></td>
							<td><button id="2-15" onclick="DesplegarInfo(this.id, 'IM')" type="button" class="btn btn-warning">Desplegar</button></td>
							<td id="2-16" class="oculto">18</td>
						</tr>
						<tr>
							<th scope='row'>3</th>
							<td id="3-2">Monto en ventas de exportaciones de las OEPS y UEP apoyadas por el IEPS</td>
							<td id="3-3">100000,12</td>
							<td id="3-4">100000,12</td>
							<td id="3-5">100%</td>
							<td id="3-6">100000,12</td>
							<td id="3-7">100000,12</td>
							<td id="3-8">100000,12</td>
							<td id="3-9">80%</td>
							<td id="3-10">12%</td>
							<td id="3-11"></td>
							<td id="3-12">1</td>
							<td id="3-13">Marzo</td>
							<td><button id="3-14" onclick="VerGrafica(this.id)" type="button" class="btn btn-warning">Ver</button></td>
							<td><button id="3-15" onclick="DesplegarInfo(this.id, 'IM')" type="button" class="btn btn-warning">Desplegar</button></td>
							<td id="3-16" class="oculto">19</td>
						</tr>
						<tr>
							<th scope='row'>4</th>
							<td id="4-2">Monto en ventas de exportaciones de las OEPS y UEP apoyadas por el IEPS</td>
							<td id="4-3">100000,12</td>
							<td id="4-4">100000,12</td>
							<td id="4-5">100%</td>
							<td id="4-6">100000,12</td>
							<td id="4-7">100000,12</td>
							<td id="4-8">100000,12</td>
							<td id="4-9">80%</td>
							<td id="4-10">12%</td>
							<td id="4-11"></td>
							<td id="4-12">1</td>
							<td id="4-13">Abril</td>
							<td><button id="4-14" onclick="VerGrafica(this.id)" type="button" class="btn btn-warning">Ver</button></td>
							<td><button id="4-15" onclick="DesplegarInfo(this.id, 'IM')" type="button" class="btn btn-warning">Desplegar</button></td>
							<td id="4-16" class="oculto">22</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<div class="myModal" id="myModal">			
			<div class="myModal-content">
				<div class="myModal-header">
					<h5>Datos Indicador</h5>					
					<span class="myClose">&times;</span>					
				</div>
				<div class="myModal-body" id="DatosGenerados"></div>
				<div class="myModal-footer">
					<button type="button" id="bClose" class="btn btn-warning" onclick="">Cerrar</button>
					<button type="button" id="bExportar" class="btn btn-warning">Exportar Excel</button>
				</div>
			</div>			
		</div>
		<span class="oculto" id="siglasDepartamento"><?php echo 'IM'; ?></span>			
	</div>		
	<script src="../../js/jquery.js"></script>
	<script src="../../js/bootstrap.js"></script>
	<script src="../../js/control.js"></script>
</body>
</html>