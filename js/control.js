// Se inicializa las variables

var btnGenerarReporte = document.getElementById('btnGenerarIndicador');
var btnGuardarReporte = document.getElementById('btnGuardarIndicadores');
var btnExportaExcel = document.getElementById('bExportar');
var btnExportarReporte = document.getElementById('btnExportarReporte');
var meses = ['ENERO',
			'FEBRERO',
			'MARZO',
			'ABRIL',
			'MAYO',
			'JUNIO',
			'JULIO',
			'AGOSTO',
			'SEPTIEMBRE',
			'OCTUBRE',
			'NOVIEMBRE',
			'DICIEMBRE'
			];

// Se añaden los addListener
btnGenerarIndicador.addEventListener('click', GenerarIndicador);
btnGuardarReporte.addEventListener('click', GuardarReporte);
btnExportaExcel.addEventListener('click', ExportarExcel);
btnExportarReporte.addEventListener('click', ExportarExcel);



function crearAjax()
{
	var res;

	if(window.XMLHttpRequest)
		res = new XMLHttpRequest();
	else
		res = new ActiveXObject("Microsoft.XMLHTTP");

	return res;
}

function GenerarIndicador()
{
	//console.log("generar indicador");
	// Objeto ajax
	var request = crearAjax();
	request.previous_text = ' ';

	// Objeto fdata que toma los valores del formulario
	var fdata = new FormData();

	// Se toma los datos de consulta
	var codIndicadorConsulta = document.getElementById('indicador');
	codIndicadorConsulta = codIndicadorConsulta.options[codIndicadorConsulta.selectedIndex].value;
	var codZonaConsulta = document.getElementById('zonas');
	codZonaConsulta = codZonaConsulta.options[codZonaConsulta.selectedIndex].value;
	var codMesConsulta = document.getElementById('meses');
	codMesConsulta = codMesConsulta.options[codMesConsulta.selectedIndex].value;
	var codAnioConsulta = document.getElementById('anio');
	codAnioConsulta = codAnioConsulta.options[codAnioConsulta.selectedIndex].value;
	var siglasDepartamento = document.getElementById('siglasDepartamento').innerHTML;

	fdata.append('codIndicador', codIndicadorConsulta);
	fdata.append('codZona', codZonaConsulta);
	fdata.append('codMes', codMesConsulta);
	fdata.append('anio', codAnioConsulta);
	fdata.append('departamento', siglasDepartamento);
	fdata.append('accion', 'generarIndicador');

	if(siglasDepartamento == 'IM')
	{
		request.open('POST', '../../include/FuncionesIM.php', true);
		
	}

	if(siglasDepartamento == 'FA')
	{
		request.open('POST', '../../include/FuncionesFA.php', true);
	}

	if(siglasDepartamento == 'FP')
	{
		request.open('POST', '../../include/FuncionesFP.php', true);
	}



	// console.log(request);
	document.getElementById('reporteGenerado').innerHTML = "<div class='imgCargar'><img src='../../img/loading.gif'/></div>";
	// document.getElementById('barraProgreso').innerHTML = "25%";
	// document.getElementById('barraProgreso').style.width = "25%";

	// request.upload.addEventListener('progress', BarraProgreso, false);
	
	request.onreadystatechange = function()
	{

		if( request.readyState == 4)
		{
			// document.getElementById('barraProgreso').innerHTML = "100%";
			// document.getElementById('barraProgreso').style.width = "100%";
			document.getElementById('reporteGenerado').innerHTML = request.responseText;
		}

		// if(request.readyState > 2 && request.readyState <= 3)
		// {
		// 	var newResponse = request.responseText.substring(request.previous_text.length);
		// 	// console.log("controla");
		// 	// // var result = JSON.parse(newResponse);
			
		// 	// document.getElementById('barraProgreso').innerHTML = result.progress + "%";
		// 	// document.getElementById('barraProgreso').style.width = result.progress + "%";
				
		// 	// request.previous_text = request.responseText; 
			
		// 	document.getElementById('barraProgreso').innerHTML = "75%";
		// 	document.getElementById('barraProgreso').style.width = "75%";
		// 	request.addEventListener('progress', function(e){
		// 		console.log(request.readyState);
		// 	}, true);
		// 	// setTimeout(function(){ console.log(3); }, 3000);
		// }		
		
	}

	


	request.send(fdata);
	
	
}





function GuardarReporte()
{
	console.log('guardar Indicador');
}

function VerGrafica(id)
{
	console.log(id);
}

function DesplegarInfo(id, departamento)
{
	// Objeto Ajax
	var request = crearAjax();

	// Objeto fdata que toma los valores del formulario
	var fdata = new FormData();

	// Se necesita saber en que fila se encuentra el boton desplegar
	var textoId = id.split("-");	
	var filaTabla = textoId[0];

	//Se toman los datos de la fila a consultar
	var codIndicador = document.getElementById(filaTabla + "-16").innerHTML;
	var codZona = document.getElementById(filaTabla + "-12").innerHTML;
	var codMes = document.getElementById(filaTabla + "-13").innerHTML;
	var anio = document.getElementById('anio');

	// *****************************************************************************************
	// El mes y el año son casos especiales
	// 
	// OBTENER MES COMO NÚMERO
	// Necesito el codigo del mes, con el array meses definido al principio del script
	// busco el nombre del mes desplegado en dicho array y tomo su posicion, por ejemplo
	// Si es Enero la respuesta sera 0,
	// Si es Febrero la respuesta sera 1 y así con todos los meses
	// Por ultimo sumo 1 a la posicion encontrada lo que resulta
	// Enero = 1
	// Febreo = 2, etc...
	// FIN OBTENER MES COMO NÚMERO
	// 
	// OBTENER AÑO
	// Para el año, solo se toma el valor seleccionado en el combo 
	// FIN OBTENER AÑO

	// *******************************************************************************************
	anio = anio.options[anio.selectedIndex].value;
	codMes = meses.indexOf(codMes);
	codMes++;


	// Se añade los datos encontrados a la variable FormData
	fdata.append('codIndicador', codIndicador);
	fdata.append('codZona', codZona);
	fdata.append('codMes', codMes);
	fdata.append('anio', anio);
	fdata.append('departamento', departamento);
	fdata.append('accion', 'desplegarInfo');

	MostrarDialogo();
	request.open('POST', '../../include/FuncionesIM.php', true);
	document.getElementById('DatosGenerados').innerHTML = "<div class='imgCargar'><img src='../../img/loading.gif'/></div>";

	request.onload = function(e)
	{
		if(request.status == 200)
		{
			document.getElementById('DatosGenerados').innerHTML = request.responseText;
		}
		else
		{
			document.getElementById('DatosGenerados').innerHTML = "error";	
		}
	};

	request.send(fdata);
	
	console.log(fdata);
	
}

function MostrarDialogo()
{
	var modal = document.getElementById('myModal');
	var bClose = document.getElementById('bClose');
	var span = document.getElementsByClassName("myClose")[0];

	modal.style.display = "block";

	span.onclick = function()
	{
		modal.style.display = "none";
	}

	bClose.onclick = function()
	{
		modal.style.display = "none";
	}

	window.onclick = function(event)
	{
		if(event.target == modal)
		{
			modal.style.display = "none";		
		}
	}
}

function ExportarExcel(evento)
{

	console.log(this.id);

	var idName = this.id;
	var tablaExportar = "-";
	if(idName == 'btnExportarReporte')
	{	
		console.log("id es btnExportarReporte");
		tablaExportar = document.getElementById('reporteGenerado').innerHTML;
	}
	else
	{
		
		tablaExportar = document.getElementById('DatosGenerados').innerHTML;
	}
	
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent('<table>' + tablaExportar + '</table>') );
	
	evento.preventDefault();
}

