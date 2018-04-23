<?php session_start(); 
function query($sql)
{
	$server = "10.2.74.50";//Direcci�n del servidor
	$user = "root";
	$pwd = "n1md0gm11i3p5";
	// $pwd = "namd0gma1";
	$basedatos = "cmi";//Base de datos a ser utilizada
	$connect = mysql_connect($server,$user,$pwd);
       mysql_set_charset('utf8', $connect); 
	mysql_select_db($basedatos, $connect);
	$result=mysql_query ($sql,$connect);//Realizo el query
	return ($result);//Retorno el resultado del query*/
	
}

function logs($query, $ci, $type)
	{
		if (buscarEvento($query) || $type == true)
		{
		   //echo "logs = $query , $ci , $type <br>";
		   
$xtransaccion = "select u.idusuario as cod_usuario, tu.idperfil as cod_tipo_usuario, tu.nombre as perfil,concat(p.nombre,' ',p.apellido) as nombre_usuario, 
u.usuario as user_name, u.contrasena as password FROM usuario u 
						inner join persona p on(p.cedula = u.usuario)
						inner join perfil_usuario pu on(u.idusuario = pu.idusuario)
						inner join perfil tu on(tu.idperfil = pu.idperfil)
						where u.activo=1 and u.usuario ='$ci'";		  
		   //echo "xtransaccion= $xtransaccion<br>";
		   //HACE UN SUBSTRING DE LOS PRIMEROS 40 CARACTERES
		   //$xtransaccion_old = substr($query, 0, 40).'....';
		   $xtransaccion_old = $query;
		   $remote_addr = $_SERVER['REMOTE_ADDR'];
		   $http_host = $_SERVER['HTTP_HOST'];
		   $http_referer = $_SERVER['HTTP_REFERER'];
		   /***************************inicio programa*********************************/
		   $request_uri = $_SERVER['REQUEST_URI'];
		   $posicion_coincidencia = 0;
		   $posicion_coincidencia = strrpos($request_uri, '?');
		   if($posicion_coincidencia > 0)
			   $request_uri = substr($request_uri, 0, $posicion_coincidencia+20).'....';		  
		   /****************************fin programa***********************************/
			//path de logs
		   $xcarpetaLogs = 'logs_app_ieps.txt';
		   $posicion_ini = 0;
		   $posicion_fin = 0;
		   $posicion_ini = strpos($_SERVER['PHP_SELF'], '/');
		   $posicion_fin = strpos($_SERVER['PHP_SELF'], '/',$posicion_ini+1);
		   $app_name = substr($_SERVER['PHP_SELF'], $posicion_ini+1, $posicion_fin-1);	
		   //para el servidor windows
		   //$xcarpetaLogs = $_SERVER['DOCUMENT_ROOT']."logs/logs_".$app_name."_ieps.txt";	   
		   //para el servidor linux
		   $xcarpetaLogs = $_SERVER['DOCUMENT_ROOT']."/logs/logs_".$app_name."_ieps.txt";	   
		   
		   //echo "xcarpetaLogs = $xcarpetaLogs<br>"; 	   

		   /*if ($_SERVER['HTTP_HOST'] != '127.0.0.1')
			{
				$xcarpetaLogs = '/var/www/code/libs/logs/';
				$xcarpetaErrs = '/var/www/code/libs/errors/';
			}*/
		   
		   //Ejecutar transaccion
		   $xresult_sql = query($xtransaccion);
		   if(isset($xresult_sql))
			{
			//Registra las variables de la sesión y direcciona al menú principal
				while($usuario=mysql_fetch_object($xresult_sql))
				{	
					$cod_usuario=$usuario->cod_usuario;
					$cod_tipo_usuario=$usuario->cod_tipo_usuario;
					$perfil=$usuario->perfil;
					$nombres_usuario=$usuario->nombre_usuario;
					$username=$usuario->user_name;
					//$_SESSION["password"]=$usuario->password;
				}				
			   //$xlast_error = pg_last_error($xconn);
			   $xfecha = date("Y-m-d");
			   $xhora = date("H:i:s");
			   
			   // Generacion del log
			   $xcadenota =$remote_addr." -- [".date("d/m/Y").":".date("H:i:s")."]";
			   $xcadenota.= ",[usuario:".$cod_usuario."-".$nombres_usuario."]";
			   $xcadenota.= ",[host:".$http_host."]";
			   //$xcadenota.= "\t,CLIENTE:".$_SERVER['REMOTE_ADDR'];
			   $xcadenota.= ",[perfil:".$cod_tipo_usuario."-".$perfil."]";
			   // Coloca el nombre del programa que hizo la llamada al programa que se ejecut�
			   $xcadenota.= ",[llamada:".$http_referer."]"; 
			   // Coloca el nombre del programa que se ejecut� m�s sus variables trasferidas por la URL
			   $xcadenota.= ",[programa:".$request_uri."]";
			   /***********************/
			  //$xcadenota.= ",[xcarpetaLogs:".$xcarpetaLogs."]";
			   /***********************/
			   //if ($xlast_error)
				  //$xcadenota.= "\r\n\t".$xlast_error; // En caso de haber error, coloca el mensaje de error del manejador de la BD
			  // Coloca la transacci?n o la consulta tal cual sucedi? en la BD
			   $xcadenota.= ",[evento:".$xtransaccion_old."]\r\n\r\n"; 
			   $arch = fopen($xcarpetaLogs, "a+");
			   
			   fwrite($arch, $xcadenota);
			   fclose($arch);
			}
			//echo "xresult_sql = $xresult_sql <br>";
			return $xresult_sql;
		}
	} // END FUNCTION
	   
	function buscarEvento($cadena_de_texto)
	{
		//echo "buscarEvento:".$cadena_de_texto."<br>";
		$evento_clave = false;
		//EVENTO INSERT
		$cadena_insert = strtolower('insert into');
		if (strstr(strtolower($cadena_de_texto),$cadena_insert) == true) 
			{    
				$evento_clave = true;
				//echo "Éxito!!! El evento es ".$cadena_insert."<br>";
				return $evento_clave;
			} 
		
		//EVENTO UPDATE
		$cadena_update = strtolower('update');
		if (strstr(strtolower($cadena_de_texto),$cadena_update) == true) 
			{
				$evento_clave = true;    
				//echo "Éxito!!! El evento es ".$cadena_update."<br>";            
				return $evento_clave;
			} 
			
		//EVENTO DELETE
		$cadena_delete = strtolower('delete');
			if (strstr(strtolower($cadena_de_texto),$cadena_delete) == true) 
			{  
				$evento_clave = true;  
				//echo "Éxito!!! El evento es".$cadena_delete."<br>";            
				return $evento_clave;
			} 
		//echo "buscarEvento:".$evento_clave."<br>";
		return $evento_clave;
	}

?>