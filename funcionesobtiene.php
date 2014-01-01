<?php
include('simple_html_dom.php');
include('basededatos.php');


mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']);


ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');  //---->>>   VER ESTOOOOOOOO

$DirFotos="/var/www/vhosts/marcelo/apdb2.no-ip.info/httpdocs/blogsviolentos/uploads/";
$URLFotos="http://apdb2.no-ip.info/blogsviolentos/uploads/";
$DirectorioListos="/var/www/vhosts/marcelo/apdb2.no-ip.info/httpdocs/blogsviolentos/listos/";
$DirCache="/var/www/vhosts/marcelo/apdb2.no-ip.info/httpdocs/blogsviolentos/uploads/";

$CaracteresChotines=array("¨", "º","~","·", "'", "^", "`","´",'©','®','³','²','½','»','€','™');
$search2   = array("á","é","í","ó","ú"); 	
$replace2 = array("a","e","i","o","u"); 

//Creo array con palabras para borrar
//Armo el select, de los textos para crear el array
//$query_abajo= "SELECT * FROM TextoLimpiar";
//mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
//$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('1TextoLimpiarLa consulta fallo: ' . mysql_error());
//$gg=0;
//while ($site = mysql_fetch_assoc($abajonota)) {
//	$TextoIrrelevante[$gg]=" ".$site['TextoIrrelevante']." ";
//	$gg=$gg+1;
//}//Termina las palabras
//mysql_free_result($abajonota);


class HttpRequest{
    public $Url;
    public $DatosPublicar;
    public $Retorno;
    public $Devuelto;
 
    public function __construct( $Url, $DatosPublicar = array(), $Headerr , $UsaProxy, $UsaPort ){
        $this->Url = $Url;
        $this->DatosPublicar = $DatosPublicar;
        $this->Headerr = $Headerr;
        $this->UsaProxy = $UsaProxy;
        $this->UsaPort = $UsaPort;
        $this->CargaPagina();
    }   
 
 
    public function CargaPagina( ){
    	$azarcache=randomString(rand(3,5),"");
        //$CookiePath = realpath($GLOBALS['DirCache']."/cookie".$azarcache.".txt");
        $CookiePath = realpath($GLOBALS['DirCache']."/cookie1.txt");

        $ch = curl_init(); //Inicia objeto curl
        curl_setopt($ch, CURLOPT_COOKIEFILE, $CookiePath);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $CookiePath);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:8.0) Gecko/20100101 Firefox/8.0');
        curl_setopt($ch, CURLOPT_URL, $this->Url);
 
        if( count($this->DatosPublicar) > 0 ){ //Si es para hacer POST
            curl_setopt($ch, CURLOPT_POST, count($this->DatosPublicar));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->DatosPublicar);
        }
 
        if( $this->UsaProxy<>"" ){ //Si usa proxy
        	curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);
        	curl_setopt($ch, CURLOPT_PROXY, $this->UsaProxy.":".$this->UsaPort);
        	curl_setopt($ch, CURLOPT_PROXYPORT, $this->UsaPort);
		}

        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_FILETIME, 1);
//        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  

        if ($this->Headerr<>""){ //si trae cabecera especial la carga
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->Headerr);
        }
 
        $buf = curl_exec($ch);
        $info = curl_getinfo($ch);
 
        curl_close($ch);  //Libera el objeto
 
        $this->Retorno = $buf;  //HTML devuelto
        $this->Devuelto  = $info;  //Datos de la funcion crul
    }
 
}


//Para pisar la imagen con algo que diga el nombre de la pagina (asi queda distinta y no la reconoce google)
function FotoLink($FotoInicial, $TipoTexto){
	Global $RandTipoLetra, $RandColorLetra, $TipoLetra, $ColorLetra, $FotoFinal;
	
	//Randomizo la bandera para crear distintas probabilidades
	$RandTipoLetra = rand(1, 10);
	$RandColorLetra = rand(1, 10);

	//Uso la respectiva bandera para elegir las distintas opciones de tipo de letra
    switch ($RandTipoLetra){
		case 1:	$TipoLetra = "arial"; break;
		case 2:	$TipoLetra = "helvetica"; break;
		case 3:	$TipoLetra = "modern"; break;
		case 4:	$TipoLetra = "venetian"; break;
		case 5:	$TipoLetra = "consolas"; break;
		case 6:	$TipoLetra = "courier";	break;
		case 7:	$TipoLetra = "bering"; break;
		case 8:	$TipoLetra = "lucida"; break;
		case 9:	$TipoLetra = "century";	break;          
		default: $TipoLetra = "comic"; break;
	}
	
	//Uso la respectiva bandera para elegir las distintas opciones de color de la letra
    switch ($RandColorLetra){
		case 1: $ColorLetra = "#FE0000"; break;
		case 2:	$ColorLetra = "#9EB399"; break;
		case 3:	$ColorLetra = "#33CC33"; break;
		case 4:	$ColorLetra = "#7394D3"; break;
		case 5: $ColorLetra = "#990099"; break;
		case 6:	$ColorLetra = "#CC0066"; break;
		case 7:	$ColorLetra = "#00FFFF"; break;
		case 8:	$ColorLetra = "#FE0000"; break;
		case 9: $ColorLetra = "#FFFF00"; break;          
		default: $ColorLetra = "#E7B12E"; break;
	}

	//Le indico donde sacar la foto a modificar y donde dejar la modificada	
	$codigoimagen=$TipoTexto.rand(5000000,10000000);
	$FotoFinal=$GLOBALS['DirFotos'].$codigoimagen.".jpg";

	$tamanotexto=rand(20,30);
	// Ejecuto el comando para incertar el texto tuneado en la foto de entrada y obtener la foto de salida
	$command = "convert -font '$TipoLetra' -pointsize $tamanotexto -fill '$ColorLetra' -draw 'text 10,250 '$TipoTexto'' '$FotoInicial' '$FotoFinal'";
	
	//echo "$command" . "<br>";	
	exec($command);
	return $FotoFinal;
}




function checkProxy($ip, $port){
	$max = 1000000;
	$options = array(CURLOPT_RETURNTRANSFER  => 1,
		             CURLOPT_HTTPPROXYTUNNEL => 0,
		             CURLOPT_FOLLOWLOCATION  => 1,
		             CURLOPT_PROXY           => $ip.":".$port,
		             CURLOPT_PROXYPORT       => $port,
		             CURLOPT_CONNECTTIMEOUT  => 10);
	$ch = curl_init("http://www.cualesmiip.com/");
	curl_setopt_array($ch, $options);

	$ini = gettimeofday();
	$returndata = curl_exec($ch);
	$end = gettimeofday();
	if(curl_errno($ch)){
		return 999999999; //Devuelvo 999999999 asi no hay problemas con el orden ni nada
	} else {
		if($returndata && stripos($returndata, "No navegas a trav") !== false){
			return $end["usec"] - $ini["usec"] < 0 ? ($end["usec"] - $ini["usec"] + $max) : ($end["usec"] - $ini["usec"]);
		} else {
			return 999999999; //Devuelvo 999999999 asi no hay problemas con el orden ni nada
		}
	}
	curl_close($ch);

}


//pide un proxy, y lo comprueba en el caso que haga mucho que no
//Leo cual es el proxy que debo usar... lo compruebo en el caso que sea que no se comprobo hace 1 hora
//Se lee list($UsaProxy,$UsaPort) = PideProxy();
function PideProxy(){
	$CuantosBaja=$GLOBALS['instancia'];
	if ($CuantosBaja=="" || $CuantosBaja==0){$CuantosBaja=1;}

	$query_abajo= "SELECT IdProxy,Proxy,FechaUltimo,CantidadPruebas, TIMESTAMPDIFF(MINUTE,FechaUltimo,NOW()) AS intervalo FROM Proxys WHERE Habilitado AND CantidadPruebas<1 ORDER BY TiempoRespuesta ASC LIMIT ".$CuantosBaja;  // levanto la cantidad segun al instancia y lo controlo
	if ($muestrodatos==1){echo "Select Proxy:".$query_abajo."<br>";} 
	mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
	$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('ControlaProxy1La consulta fallo: ' . mysql_error());

	$CuantosBaja=$CuantosBaja-1;

	$IdProxy = mysql_result($abajonota,$CuantosBaja,"IdProxy");
	$UsaProxy = mysql_result($abajonota,$CuantosBaja,"Proxy");
	$ProxyFechaUltimo = mysql_result($abajonota,$CuantosBaja,"FechaUltimo");
	$CantidadPruebas = mysql_result($abajonota,$CuantosBaja,"CantidadPruebas");
	$intervalo = mysql_result($abajonota,$CuantosBaja,"intervalo");

	$list = explode(":", $UsaProxy);
	$UsaProxy = $list[0];
	$UsaPort = (int)$list[1];


	if ($intervalo>15){ //si se controlo hace 15 min o mas
		$seg=checkProxy($UsaProxy, $UsaPort);
		if ($seg > 999999) {
			$CantidadPruebas=$CantidadPruebas+1;
			$Habilitado=1;
			if ($CantidadPruebas>0){ //si tiene 1 o mas pruebas negativas lo deshabilito al proxy
				$Habilitado=0;
			}
			if ($IdProxy<>"" && !is_null($IdProxy)){
				$query_paginas = "UPDATE Proxys SET FechaUltimo=NOW(),CantidadPruebas=CantidadPruebas+1,TiempoRespuesta=$seg, Habilitado=$Habilitado WHERE IdProxy=$IdProxy";
				if ($muestrodatos==1){echo "Update Proxy:".$query_paginas."<br>";} 
				$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('ActProxy7La consulta fallo: ' . mysql_error());
			} else {
				//Actualizo estadistica
				$mensaje="::::Moco IdProxy: ".$query_abajo;
				$nada=ActualizaEstadistica($mensaje, $IdEstadistica);
				exit();
			}
			$UsaProxy = ""; $UsaPort = "";
		} else {
			//Lo actualiza por bien digamos asi no lo prueba de vuelta hasta dentro de 15 min
			if ($IdProxy<>"" && !is_null($IdProxy)){
				$query_paginas = "UPDATE Proxys SET FechaUltimo=NOW(),TiempoRespuesta=$seg WHERE IdProxy=$IdProxy";
				if ($muestrodatos==1){echo "Update Proxy2:".$query_paginas."<br>";} 
				$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('ActProxy27La consulta fallo: ' . mysql_error());
			}			
		}

	}

	return array($UsaProxy,$UsaPort,$IdProxy);
}




// FUNCIONES PARA TODOS

//Se aplica asi: $texto = limpiar_tags($texto,'<div><td>');
//Lo que dice '<div><td>' alli se escribe los tags a limpiar
function limpiar_tags($txt,$tags){
    preg_match_all("/<([^>]+)>/i",$tags,$allTags,PREG_PATTERN_ORDER);
    foreach ($allTags[1] as $tag){
        $txt = preg_replace("/<".$tag."[^>]*>/i","<".$tag.">",$txt);
    }
    return $txt;
}

//Pasar como parametro la url de la imagen
function Escala($url){ 
	$datos = GetImageSize($url) OR die("Imagen no valida"); 
	if($datos[0]>650){
		$ancho = 650;
		$xp = $datos[0]/650; 
		$alto = $datos[1]/$xp;
	}else{
		$ancho=$datos[0];
		$alto=$datos[1];
	}
	$imagen='<img src="'.$url.'" width="'.$ancho.'" height="'.$alto.'" border="0">';
	return $imagen;
}

//Funcion de Emanuel para imagenes
function limpiar_imagenes($texto, $path=""){
	preg_match_all('/<(img[^>]*src="([^"]+)"[^>]*\/>).*?|U/',$texto,$srcs,PREG_PATTERN_ORDER);
	$p = array();
	foreach ($srcs[2] as $src){
		$src = str_ireplace($path, "", $src);
		$p[] = '<img src="'.$path.$src.'" />';
	}
	$rst="";
	$i=0;
	$cnt=stripos($texto, "<img");
	$cnt = stripos(substr($texto, $cnt), ">") + $cnt;

	while($cnt !== false){
//		echo substr($texto, 0, $cnt+1)."\n";
    	$rst .= preg_replace("/<img[^>]*>/i", $p[$i], substr($texto, 0, $cnt + 1));
    	$texto = substr($texto, $cnt + 1);
    	$cnt = stripos($texto, "<img");
    	if($cnt === false){
    		$rst .= $texto;
    		break;
    	}
		$cnt = stripos(substr($texto, $cnt), ">") + $cnt; //por los 4 chars de <img
    	$i++;
	}
	return $rst;
}

// Comprobar variables
function comprobar_int(){
	global $precio, $rubro, $latitud, $longitud, $ano, $kilometraje;
	global $ok;
	if(!is_numeric($precio)){		echo '<h1>ERROR: La variable $precio no es INT</h1>';			$ok=0;	}
	if(!is_numeric($rubro)){		echo '<h1>ERROR: La variable $rubro no es INT</h1>';			$ok=0;	}
	if(!is_numeric($latitud)){		echo '<h1>ERROR: La variable $latitud no es INT</h1>';			$ok=0;	}
	if(!is_numeric($longitud)){		echo '<h1>ERROR: La variable $longitud no es INT</h1>';			$ok=0;	}
	if(!is_numeric($ano)){			echo '<h1>ERROR: La variable $ano no es INT</h1>';				$ok=0;	}
	if(!is_numeric($kilometraje)){	echo '<h1>ERROR: La variable $kilometraje no es INT</h1>';		$ok=0;	}
}







function ActualizaEstadistica($mensaje, $IdEstadistica){
$query_paginas = "UPDATE Estadisticas SET Mensaje=CONCAT(Mensaje,'".$mensaje."') WHERE IdEstadistica=$IdEstadistica";
//echo "Consulta:".$query_paginas;
$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('ActEstad1La consulta fallo: ' . mysql_error());
}


function recibe_imagen ($url_origen,$archivo_destino){  
	//$mi_curl = curl_init ($url_origen);  
	$fs_archivo = fopen ($archivo_destino, "w");
	$mi_curl = curl_init();
	curl_setopt($mi_curl, CURLOPT_URL, $url_origen);   
	curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo);  
	curl_setopt ($mi_curl, CURLOPT_HEADER, 0);  
	curl_setopt($mi_curl, CURLOPT_USERAGENT, '[Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2) Gecko/20070219 Firefox/2.0.0.2")]');
	curl_exec ($mi_curl);  
	curl_close ($mi_curl);
	fclose ($fs_archivo);
	//Para controlar que realmente se bajo el archivo el tamaño deberia ser >0
	$tamanoarchivo = filesize($archivo_destino);
	return $tamanoarchivo;
}

function recibe_imagen_wget($source, $destination){
	$command =  "wget \"$source\" -O $destination";
	exec($command);
}


function customError($errno, $errstr){
	global $jiji;
	//Moquito Importante
	//Actualizando("Moco por algo:".$errno."--Error:".$errstr,"web_paginas");
	//Trato de sumar uno al for
	$jiji=$jiji+1;
	//die();//Termina	
}

function trae_pedazo($str, $phrase, $tag_open = '<strong>', $tag_close = '</strong>') 
{ 
    if ($str == '') 
    { 
        return ''; 
    } 
     
    if ($phrase != '') 
    { 
        return preg_replace('/('.preg_quote($phrase).')/i', $tag_open."\\1".$tag_close, $str); 
    } 

    return $str; 
} 	


function  ControloTexto($cadena,$permitidos){
	$cadena = utf8_decode($cadena);
	for ($i=0; $i<strlen($cadena); $i++){ 
		if (strpos($permitidos, substr($cadena,$i,1))===false){
			//echo substr($cadena,$i,1);
			return false; 
		} 
	}
	return true; 
}



//Lee una imagen (captcha o lo que sea), desde una URL con una imagen
//Se usa OCRImagen("htttp://wwww....",".png",170,"140,70,100","","N","S") //este es para el captcha
//Se usa OCRImagen("http://www....",".gif",400,"100,100,100",'-background white  -flatten',"S","S") //este es con fondo transparente
function OCRImagen($imagenurl,$extencion,$tamano,$brillo,$variacion,$fondotrans,$borra,$esurl="U"){ 
	$codigoimagen="captcha".rand(5000000,10000000);
	//Copio la imagen al servidor desde el origen
	$imagenori=$GLOBALS['DirFotos'].$codigoimagen.$extencion;
	$imagenleer=$GLOBALS['DirFotos'].$codigoimagen.".tif";
	$imagentrans=$GLOBALS['DirFotos'].$codigoimagen."2.gif";
	$textoleer=$GLOBALS['DirFotos'].$codigoimagen;

	if ($esurl=="N"){
		//La imagen no es una url, entonces ya la tengo en mi servidor
		$imagenori=$imagenurl;
	} else {recibe_imagen($imagenurl,$imagenori);}
	

	//Saca el fondo si es transparente
	if ($fondotrans=="S"){ 
		//si es transparente hay que ponerle blanco atras
		$command="convert $imagenori $variacion $imagentrans";
		exec($command);

		$command="identify -verbose $imagentrans | perl -0777 -ne 's/^  //gm; print $& while /^(Colors|Alpha|Colormap):.*?(?=^\S)/gms'";
		exec($command);

		//Modifico la imagen para que se pueda leer (esto depende de que se lee, el captcha es 170)
		$command="convert -density 300 -depth 8 -resize $tamano% -type Grayscale -modulate $brillo $imagentrans $imagenleer";
		exec($command);
	} else {
		//Imagen comun sin transparencia
		//Modifico la imagen para que se pueda leer (esto depende de que se lee, el captcha es 170)
		$command="convert -density 300 -depth 8 -resize $tamano% -type Grayscale -modulate $brillo $imagenori $variacion $imagenleer";
		exec($command);
	}

	//Leo la imagen modificada
	$command="tesseract $imagenleer $textoleer";
	exec($command);

	//Levanto texto leido
	$textoleer=$textoleer.".txt";

	$archivo = file($textoleer);
	$lineas = count($archivo);

	$textoleido=$archivo[0];

	unset($archivo);

	//Borro todo
	if ($borra=="S"){
		unlink($imagenori);
		unlink($imagenleer);
		unlink($textoleer);
		if ($fondotrans=="S"){ 
			unlink($imagentrans);
		}
	}

	//Reemplazo caracteres que se como son
	$textoleido=trim(str_replace("><","X",$textoleido));//Cambio algunos caracteres 

	return $textoleido;
}



function  modificoimagenes($nombre,$imagenentrada){
	//levanto la imagen, la copio al servidor
	$cadenaazar=randomString(rand(3,5),"");
	$codigo_foto = $nombre."_".$cadenaazar.".jpg";
	$codigo_foto2 = $nombre."_".$cadenaazar."2.jpg";
	//Copio la imagen al servidor desde el origen
	$fotomia=$GLOBALS['DirFotos'].$codigo_foto;
	$fotomia2=$GLOBALS['DirFotos'].$codigo_foto2;
	$tamarch=0;
	$tamarch=recibe_imagen($imagenentrada,$fotomia);

	//modifico el tamaño
	$miniatura_ancho_maximo = 500;
	$miniatura_alto_maximo = 500;
	 
	$info_imagen = getimagesize($fotomia);
	$imagen_ancho = $info_imagen[0];
	$imagen_alto = $info_imagen[1];
	$imagen_tipo = $info_imagen['mime'];
	 
	$proporcion_imagen = $imagen_ancho / $imagen_alto;
	$proporcion_miniatura = $miniatura_ancho_maximo / $miniatura_alto_maximo;
	 
	if ( $proporcion_imagen > $proporcion_miniatura ){
	    $miniatura_ancho = $miniatura_ancho_maximo;
	    $miniatura_alto = $miniatura_ancho_maximo / $proporcion_imagen;
	} else if ( $proporcion_imagen < $proporcion_miniatura ){
	    $miniatura_ancho = $miniatura_ancho_maximo * $proporcion_imagen;
	    $miniatura_alto = $miniatura_alto_maximo;
	} else {
	    $miniatura_ancho = $miniatura_ancho_maximo;
	    $miniatura_alto = $miniatura_alto_maximo;
	}
	 
	switch ( $imagen_tipo ){
	    case "image/jpg":
	    case "image/jpeg":
	        $imagen = imagecreatefromjpeg( $fotomia );
	        break;
	    case "image/png":
	        $imagen = imagecreatefrompng( $fotomia );
	        break;
	    case "image/gif":
	        $imagen = imagecreatefromgif( $fotomia );
	        break;
	}
	 
	$lienzo = imagecreatetruecolor( $miniatura_ancho, $miniatura_alto );
	imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $miniatura_ancho, $miniatura_alto, $imagen_ancho, $imagen_alto);
	imagejpeg($lienzo, $fotomia2, 80);



	//la piso con texto
	$fotomia=FotoLink($fotomia2, $nombre);

	//devuelve url del servidor con la imagen nueva

	return $fotomia; 
}

function sacar_tags($string){
	$string=str_replace('</li>','<br>',$string);
	$string=str_replace('</ul>','<br>',$string);
	$string=str_replace('</p>','<br>',$string);
	$string=str_replace('</h1>','<br>',$string);
	$string=str_replace('</h2>','<br>',$string);
	$string=str_replace('</h3>','<br>',$string);
	$string=str_replace('</h4>','<br>',$string);
	$string=str_replace('</h5>','<br>',$string);
	
	return strip_tags($string,'<br>');
}


//Codigo que crea una cadena de texto aleatoria
function randomString($length, $letters){
	//Si no nos especifican lo contrario usaremos un conjunto de letras por defecto
	if(!isset($letters) || strlen($letters) == 0){
		$letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890._"; //Por defecto usaremos todas estas letras
	}
	$str = ''; //Cadena resultante
	$max = strlen($letters)-1;
	for($i=0; $i<$length; $i++){
		$str .= $letters[rand(0,$max)]; //Hasta que tengamos $length caracteres agregamos una letra al hazar del conjunto $letters
	}
	return $str;
}



?>

