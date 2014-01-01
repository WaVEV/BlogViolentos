<?php
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');
$DirectorioListos="/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/listos/";

//Leo la base de datos obtengo datos de que fuente leer
mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']);

$muestrodatos=1;

//por parametro recibo el numero de la instancia uqe debe correr en este momento
$instancia = $argv[1]; //numero de la instancia para leer la Blogs
 

//Busco en la base el blog al que se debe publicar que debe leerr en una X cantidad de minutos
$query_Blogs = "SELECT * FROM Blogs WHERE Instancia=".$instancia." AND (DATE_ADD(UltimaActualizacion,INTERVAL CadaCuantoTiempo MINUTE)< NOW()) ORDER BY DATE_ADD(UltimaActualizacion,INTERVAL CadaCuantoTiempo MINUTE) LIMIT 1";
if ($muestrodatos==1){echo "Select Instancias:".$query_Blogs."<br>";} 
$Blogs = mysql_query($query_Blogs, $GLOBALS['publica']) or die('Blogs1La consulta fallo: ' . mysql_error());
while($row_Blogs = mysql_fetch_array($Blogs)){
	//Voy guardando los datos
	$IdBlog=$row_Blogs['IdBlog'];
	$Dominio=$row_Blogs['Dominio'];
	$Usuario=$row_Blogs['Usuario'];
	$Pass=$row_Blogs['Pass'];
	$TipoBlog=$row_Blogs['TipoBlog'];
	$Sinonimos=$row_Blogs['Sinonimos'];
	$Verbos=$row_Blogs['Verbos'];
	$PalabrasRedaccion=$row_Blogs['PalabrasRedaccion'];
	$FuentesRedaccion=$row_Blogs['FuentesRedaccion'];
}
mysql_free_result($Blogs);

//Empieza la estadistica
$horitas = explode(' ', microtime());
$iniciando = $horitas[1] + $horitas[0];

//Actualizo los datos de estadistica de la pagina seleccionada para tener un control
$mensaje="Inicia: ".$Dominio;
$query_paginas = "INSERT Estadisticas SET Pagina='$DatoTipoURL', FechaEmpieza=NOW(), Mensaje='$mensaje', Resultado='Trabajando'";
$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('InicEstLa consulta fallo: ' . mysql_error());
$IdEstadistica=mysql_insert_id();//Devuelve el ultimo id cuando se hace un insert (jojo)


//Actualizo
if ($IdBlog<>"" && !is_null($IdBlog)){
	$query_blogs = "UPDATE Blogs SET Cantidad=Cantidad+1,UltimaActualizacion=NOW() WHERE IdBlog=$IdBlog";
	if ($muestrodatos==1){echo "Actualizo Blogs:".$query_blogs."<br>";} 
	$resultado=mysql_query($query_blogs,$GLOBALS['publica']) or die('ActBlogs La consulta fallo: ' . mysql_error());
} else {
	//Actualizo estadistica
	$mensaje="::::Moco IdBlog: ".$query_blogs;
	$nada=ActualizaEstadistica($mensaje, $IdEstadistica);
	exit();
}



//levanto una nota que corresponda con la fuente que necesita el blog seleccionado, y la proceso, para despues publicarla
$query_Notas = "SELECT * FROM Notas WHERE NombreFuente='$FuentesRedaccion' AND BlogDondeSePublico='' ORDER BY FechaProcesado LIMIT 1";
if ($muestrodatos==1){echo "Select Notas:".$query_Notas."<br>";} 
$Notas = mysql_query($query_Notas, $GLOBALS['publica']) or die('Notas1La consulta fallo: ' . mysql_error());
while($row_Notas = mysql_fetch_array($Notas)){
	//Voy guardando los datos
	$IdNota=$row_Notas['IdNota'];
	$NombreFuente=$row_Notas['NombreFuente'];
	$TituloOriginal=$row_Notas['TituloOriginal'];
	$TextoOriginal=$row_Notas['TextoOriginal'];
	$PalabrasImportantes=$row_Notas['PalabrasImportantes'];
	$ImgURL1=$row_Notas['ImgURL1'];
	$ImgURL2=$row_Notas['ImgURL2'];
	$ImgURL3=$row_Notas['ImgURL3'];
	$ImgURL4=$row_Notas['ImgURL4'];
	$ImgURL5=$row_Notas['ImgURL5'];
}
mysql_free_result($Notas);

$palabrasimportantes=trim(strtolower(strip_tags($TituloOriginal)));
$palabrasimportantes=str_ireplace($TextoIrrelevante," ",$palabrasimportantes);  //saco palabras irrelevantes

//include que llama al script de las imagenes y lo corre de una, devuelve $imagenesnota
include($DirectorioListos."imagenes_".$BuscadorImagenes.".php");

//esto devuelve $imagenesnota un array de 1 a 5 de url imagenes
//Las modifico y piso para no tener quilombo con el reconocedor de google..
$iii = 0; ImgURL[1]=""; ImgURL[2]=""; ImgURL[3]=""; ImgURL[4]=""; ImgURL[5]="";
foreach ($imagenesnota as $imagenurl) {
	if ($imagenurl<>""){
		$iii = $iii +1;
		$ImgURL[$iii]=modificoimagenes($imagenurl);
	}
}

//Proceso los sinonimos, verbos, y una vez que esta todo eso listo se publica en el blog correspondiente


//Pido un proxy
list($UsaProxy,$UsaPort,$IdProxy) = PideProxy(); //Trae un proxy usable, si no nada
//Sin proxi Habilitar linea de abajo
//$UsaProxy=""; $UsaPort="";


//include que llama al script de sinonimos y lo corre de una
include($DirectorioListos."sinonimos_".$Sinonimos.".php");

//include que llama al script de los Verbos y lo corre de una
include($DirectorioListos."verbos_".$Verbos.".php");

//include que llama al script del publicador del tipo de blog determinado y lo corre de una
include($DirectorioListos."blog_".$TipoBlog.".php");


//Actualizo estadistica con proxi, lo hago aca, para que si lo cambio en el listo quede reflejado en la BD
$mensaje="::::Proxi: ".$UsaProxy;
$nada=ActualizaEstadistica($mensaje, $IdEstadistica);

//Para probar a mano el proxy (anda de 10)
//$Request = new HttpRequest("http://www.cualesmiip.com/", "", "", $UsaProxy, $UsaPort);
//$Retorno = $Request->Retorno;



//Controlo que se haya obtenido la info conrrectamente, actualiza registros
$ResultadoProceso="PipiCucu";

//Guardo los datos procesados en la base Notas (todo en la parte procesado)
if ($TituloProcesado<>"" && !is_null($TituloProcesado)){
	$query_Notas = "UPDATE Notas SET TituloProcesado='$TituloProcesado',TextoProcesado='$TextoProcesado',BlogDondeSePublico='$Dominio',URLNotaPublicada='$URLNotaPublicada', FechaPublicado=NOW() WHERE IdNota=$IdNota";
	if ($muestrodatos==1){echo "Update Notas:".$query_Notas."<br>";}
	$resultado=mysql_query($query_Notas,$GLOBALS['publica']) or die('UpdateNotas La consulta fallo: ' . mysql_error());
} else {
	//Actualizo estadistica
	$mensaje="::::Moco Proceso Notas IdNota: ".$IdNota;
	$nada=ActualizaEstadistica($mensaje, $IdEstadistica);
	exit();
}


//Termina estadistica de este proceso
$horitas = explode(' ', microtime());
$terminando = $horitas[1] + $horitas[0];
$tiempototal = ($terminando - $iniciando);
$mensaje=" >>>>>>Termina<<<<<<";
$query_paginas = "UPDATE Estadisticas SET Mensaje=CONCAT(Mensaje,'".$mensaje."'), FechaTermina=NOW(), Tiempo=$tiempototal, Resultado='$ResultadoProceso' WHERE IdEstadistica=$IdEstadistica";
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
	echo "<br>BlogSeleccionado: ".$Dominio;
	echo "<br>Usuario: ".$Usuario;
	echo "<br>Pass: ".$Pass;
	echo "<br>TipoBlog: ".$TipoBlog;
	echo "<br>Sinonimos: ".$Sinonimos;
	echo "<br>Verbos: ".$Verbos;
	echo "<br>PalabrasRedaccion: ".$PalabrasRedaccion;
	echo "<br>FuentesRedaccion: ".$FuentesRedaccion;
	echo "<br>IdNota: ".$IdNota;
	echo "<br>TituloOriginal: ".$TituloOriginal;
	echo "<br>TextoOriginal: ".$TextoOriginal;
	echo "<br>PalabrasImportantes: ".$PalabrasImportantes;
	echo "<br>ImgURL1: ".$ImgURL1;
	echo "<br>ImgURL2: ".$ImgURL2;
	echo "<br>ImgURL3: ".$ImgURL3;
	echo "<br>ImgURL4: ".$ImgURL4;
	echo "<br>ImgURL5: ".$ImgURL5;


	echo "<hr><br><br>DATOS RESULTADOS<br>";
	echo "<br>ResultadoProceso: ".$ResultadoProceso;	
	echo "<br>Titulo Procesado: ".$TituloProcesado;
	echo "<br>Texto Procesado: ".$TextoProcesado;
	echo "<br>BlogDondeSePublico: ".$Dominio;
	echo "<br>URLNotaPublicada: ".$URLNotaPublicada;

	echo "<br>FIN :)";
}
?>