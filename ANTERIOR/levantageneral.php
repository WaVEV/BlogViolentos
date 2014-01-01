<?php
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');
$DirectorioListos="/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/listos/";

//Leo la base de datos obtengo datos de que fuente leer
mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']);

$muestrodatos=1;

//por parametro recibo el numero de la instancia uqe debe correr en este momento
$instancia = $argv[1]; //numero de la instancia para leer la fuente
 

//Armo el select, seleccion el Link que le toca chorear, elijo una y con esta paso a la lectura
$query_abajo = "SELECT * FROM crawl_articulos where Validado=0  ORDER BY FechaObtenido ASC LIMIT 1";
mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die("No se puede abrir base datos" . mysql_error());
$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('SelectCrawl 8La consulta fallo: ' . mysql_error());


//Empieza la estadistica
$horitas = explode(' ', microtime());
$iniciando = $horitas[1] + $horitas[0];

//Actualizo los datos de estadistica de la pagina seleccionada para tener un control
$mensaje="Inicia: ".$NombreFuente;
$query_paginas = "INSERT Estadisticas SET Pagina='$URLFuente', FechaEmpieza=NOW(), Mensaje='$mensaje', Resultado='Trabajando'";
$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('InicEstLa consulta fallo: ' . mysql_error());
$IdEstadistica=mysql_insert_id();//Devuelve el ultimo id cuando se hace un insert (jojo)


if ($abajonota) {
	$URLss = mysql_result($abajonota, 0, "Link"); //Piso la URLss que viene de la web_paginas
	$URLss=str_replace("&amp;","&",$URLss); //Reemplazando el & que a veces da problemas
	$IdCrawl = mysql_result($abajonota, 0, "IdCrawl");
	$Pagina = mysql_result($abajonota, 0, "Pagina");

	//include que llama al script del fuente y lo corre de una, esto devuelve $TituloOriginal;$TextoOriginal;$FechaOriginal;

	include($DirectorioListos."fuente_".$Pagina.".php");

	$PalabrasImportantes=PalabrasImportantes($TituloOriginal);
	$URLNotaOriginal=$URLss;


	//Actualizo la pagina que voy a crawlear asi no lo repite
	$query_paginas = "UPDATE crawl_articulos SET FechaLevantado=NOW(),Validado=1 WHERE IdCrawl=$IdCrawl";
	$resultado = mysql_query($query_paginas, $GLOBALS['publica']) or die('UpdateCrawl 9,1La consulta fallo: ' . mysql_error());

	//Controlo que se haya obtenido la info conrrectamente, actualiza registros
	$ResultadoGeneracion="PipiCucu";

	$mensaje="::::Resultado Nota: ".$ResultadoGeneracion." ::::URL: ".$URLNotaOriginal;
	$nada=ActualizaEstadistica($mensaje, $IdEstadistica);	


	//Guardo los datos obtenidos en la base Notas (todo en la parte originales e imagenes)
	//Hago el insert en la tabla de datos bonitos
	$query_notas = "INSERT Notas SET TituloOriginal='$TituloOriginal', TextoOriginal='$TextoOriginal',ImgURL1='$ImgURL[1]',ImgURL2='$ImgURL[2]',ImgURL3='$ImgURL[3]',ImgURL4='$ImgURL[4]',ImgURL5='$ImgURL[5]',FechaOriginal='$FechaOriginal',FechaProcesado=NOW(),URLNotaOriginal='$URLNotaOriginal',NombreFuente='$Pagina',PalabrasImportantes='$PalabrasImportantes'";
	if ($muestrodatos==1){echo "Insert Notas:".$query_notas."<br>";}
	$resultado=mysql_query($query_notas,$GLOBALS['publica']) or die('GuardaNotas La consulta fallo: ' . mysql_error());

}
mysql_free_result($abajonota);




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
