<?php
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/recursivo.php');
$DirectorioListos="/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/listos/";

//Leo la base de datos obtengo datos de que fuente leer
mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']);

$muestrodatos=1;

//por parametro recibo el numero de la instancia uqe debe correr en este momento
$instancia = $argv[1]; //numero de la instancia para leer la fuente
 

//Busco en la base la fuente que debe leerr en una X cantidad de minutos
$query_Fuente = "SELECT * FROM Fuentes WHERE Instancia=".$instancia." AND (DATE_ADD(UltimaActualizacion,INTERVAL CadaCuantoTiempo MINUTE)< NOW()) ORDER BY DATE_ADD(UltimaActualizacion,INTERVAL CadaCuantoTiempo MINUTE) LIMIT 1";
if ($muestrodatos==1){echo "Select Instancias:".$query_Fuente."<br>";} 
$Fuentes = mysql_query($query_Fuente, $GLOBALS['publica']) or die('Fuentes1La consulta fallo: ' . mysql_error());
while($row_Fuentes = mysql_fetch_array($Fuentes)){
	//Voy guardando los datos
	$IdFuente=$row_Fuentes['IdFuente'];
	$NombreFuente=$row_Fuentes['NombreFuente'];
	$URLFuente=$row_Fuentes['URLFuente'];
	$Recursivo=$row_Fuentes['Recursivo'];
	$ParametrosRecursivo=explode(',', $Recursivo);  //Separo los parametros por , asi los paso a la funcion
	$BuscadorImagenes=$row_Fuentes['BuscadorImagenes'];
}
mysql_free_result($Fuentes);

//Empieza la estadistica
$horitas = explode(' ', microtime());
$iniciando = $horitas[1] + $horitas[0];

//Actualizo los datos de estadistica de la pagina seleccionada para tener un control
$mensaje="Inicia: ".$NombreFuente;
$query_paginas = "INSERT Estadisticas SET Pagina='$URLFuente', FechaEmpieza=NOW(), Mensaje='$mensaje', Resultado='Trabajando'";
$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('InicEstLa consulta fallo: ' . mysql_error());
$IdEstadistica=mysql_insert_id();//Devuelve el ultimo id cuando se hace un insert (jojo)



//Proceso el listo de la fuente, que devuelve los datos obtenidos (o nada en el caso que no encuentre nada actualizado)


//Pido un proxy
list($UsaProxy,$UsaPort,$IdProxy) = PideProxy(); //Trae un proxy usable, si no nada
//Sin proxi Habilitar linea de abajo
//$UsaProxy=""; $UsaPort="";

//Uso el recursivo para llenar los datos en la tabla crawl, asi usa el fuente y levanta las notas
//Recursivo($Pagina, $Dominio, $Recursivo, $Corta, $Primera, $IdentificaArticulo, $IdentificaLink, $NOIdentificaLink);
$nada=Recursivo($ParametrosRecursivo[0], $ParametrosRecursivo[1], $ParametrosRecursivo[2], $ParametrosRecursivo[3], $ParametrosRecursivo[4], $ParametrosRecursivo[5], $ParametrosRecursivo[6], $ParametrosRecursivo[7]);


//Actualizo estadistica con proxi, lo hago aca, para que si lo cambio en el listo quede reflejado en la BD
$mensaje="::::Proxi: ".$UsaProxy;
$nada=ActualizaEstadistica($mensaje, $IdEstadistica);

//Para probar a mano el proxy (anda de 10)
//$Request = new HttpRequest("http://www.cualesmiip.com/", "", "", $UsaProxy, $UsaPort);
//$Retorno = $Request->Retorno;







//Termina estadistica de este proceso
$horitas = explode(' ', microtime());
$terminando = $horitas[1] + $horitas[0];
$tiempototal = ($terminando - $iniciando);
$mensaje=" >>>>>>Termina<<<<<<";
$query_paginas = "UPDATE Estadisticas SET Mensaje=CONCAT(Mensaje,'".$mensaje."'), FechaTermina=NOW(), Tiempo=$tiempototal, Resultado='$ResultadoGeneracion' WHERE IdEstadistica=$IdEstadistica";
//echo "Consulta:".$query_paginas;
$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('ActEstad1La consulta fallo: ' . mysql_error());

//Si el tiempo total es de mas de 2 min revisa el proxi
if ($IdProxy<>"" && !is_null($IdProxy) && $seg>120){
	$seg=$tiempototal*1000; //Son milisegundos
	$query_paginas = "UPDATE Proxys SET FechaUltimo=NOW(),TiempoRespuesta=$seg WHERE IdProxy=$IdProxy";
	if ($muestrodatos==1){echo "Update Proxy:".$query_paginas."<br>";} 
	$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('ActProxyTiempoLa consulta fallo: ' . mysql_error());
}


//Muestro para controlar
if ($muestrodatos==1){
	echo "<br><hr><br>DATOS EXTRAIDOS<br>";
	echo "<br>NombreFuente: ".$NombreFuente;
	echo "<br>URLFuente: ".$URLFuente;
	echo "<br>Recursivo: ".$Recursivo;
	echo "<br>BuscadorImagenes: ".$BuscadorImagenes;



	echo "<hr><br><br>DATOS RESULTADOS<br>";
	echo "<br>ResultadoGeneracion: ".$ResultadoGeneracion;	
	echo "<br>Titulo Original: ".$TituloOriginal;
	echo "<br>Texto Original: ".$TextoOriginal;
	echo "<br>ImgURL1: ".$ImgURL1;
	echo "<br>ImgURL2: ".$ImgURL2;
	echo "<br>ImgURL3: ".$ImgURL3;
	echo "<br>ImgURL4: ".$ImgURL4;
	echo "<br>ImgURL5: ".$ImgURL5;
	echo "<br>FechaOriginal: ".$FechaOriginal;
	echo "<br>URLNotaOriginal: ".$URLNotaOriginal;
	echo "<br>NombreFuente: ".$NombreFuente;
	echo "<br>PalabrasImportantes: ".$PalabrasImportantes;

	echo "<br>FIN :)";
}
?>
