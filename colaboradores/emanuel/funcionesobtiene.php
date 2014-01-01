<?php
include('simple_html_dom.php');
function ww($html){$f = fopen("salida.html", "w");fwrite($f, $html);fclose($f);}

//Datos BD
/*$hostname_publica = "localhost";
$database_publica = "visiting";
$username_publica = "visiting";
$password_publica = "tontita";
$publica = mysql_pconnect($hostname_publica, $username_publica, $password_publica) or die('No pudo conectarse : ' . mysql_error());

mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']);


ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');  //---->>>   VER ESTOOOOOOOO*/

//Servidor externo
if (gethostname()=="apdb1") {
	$externoo="apdb2.no-ip.info";
	$locall="apdb1.no-ip.info";
} else {
	$externoo="apdb1.no-ip.info";
	$locall="apdb2.no-ip.info";
}
//en el caso de ser el servidor 3 solo trabaja local por ahora asi que no tiene mucho sentido
if (gethostname()=="apdb3") {
	$externoo="apdb1.no-ip.info";
	$locall="apdb3.no-ip.info";
}



$DirFotos="/var/www/vhosts/marcelo/".$GLOBALS['locall']."/httpdocs/visiting/imagenes/";
$URLFotos="http://".$GLOBALS['locall']."/visiting/imagenes/";
$DirXMLs="/var/www/vhosts/marcelo/".$GLOBALS['locall']."/httpdocs/visiting/csvs/";


//Creo array con palabras para borrar
//Armo el select, de los textos para crear el array
//$query_abajo= "SELECT * FROM web_categtextolimpiar";
//mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
//$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('1web_categtextolimpiarLa consulta fallo: ' . mysql_error());
$gg=0;
/*while ($site = mysql_fetch_assoc($abajonota)) {
	$TextoIrrelevante[$gg]=" ".$site['TextoIrrelevante']." ";
	$gg=$gg+1;
}//Termina las palabras
mysql_free_result($abajonota);*/


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
	preg_match_all("/<img[^>]*src=\"([^\"]+)\"[^>]*>/i", $texto, $listImag, PREG_PATTERN_ORDER); //extraigo las imagenes del texto
	$list = explode("<img", $texto); // separo entre imagen e imagen
	$cnt=0;
	$texto=$list[0]; // asigno lo primero hasta este instante no hay imagenes
	foreach($listImag[1] as $e){ //recorro todas las imagenes
		$cnt++; // franccion del texto dividito en el explode a ver
		$datos = GetImageSize(str_ireplace($path.$path, $path, $path.$e)) OR die("Imagen no valida");
		if($datos[0]>650){
			$ancho = 650;
			$xp = $datos[0]/650; 
			$alto = $datos[1]/$xp;
		}else{
			$ancho=$datos[0];
			$alto=$datos[1];
		}
		$texto .= preg_replace("/<img[^>]*>/i", '<img src="'.$path.$e.'" width="'.$ancho.'" height="'.$alto.'" border="0">', "<img".$list[$cnt]); // el replace
	}
	$texto = str_ireplace($path.$path, $path, $texto); //un chequeo por si mandaron el path cuando el coso tenia el path aveces algunas imagenes tienen path de root otras no
	return $texto; // resultado
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






function LevantoInfoBase(){

	// pongo las variables globales
	include('globalvariablesbase.php');

	$CantidadDatos=0;
	$CantidadListos=0;
	//echo "Titulo en Base:".$titulo;
	//Levanto la info a la base
	//if ($titulo<>"nada" && $titulo<>"" && strlen($titulo)>10 && strlen($texto)>10 ){ //Lo saco por ahora
	if ($titulo<>"nada" && $titulo<>""){

		//Modifico la categoria si trae el XX
		if ((strpos($rubro, "XX")!== false)){
			$rubro=BuscaCategoria($titulo, $rubro);
		}

		$CantidadReal=$CantidadReal+1;

		//Cargo los datos del texto del anuncio
		$query_Email = "INSERT web_datos SET IdPaginas=$IdChoreadito, Pagina='$Pagina', Version=$Version, FechaLevantado=NOW(), Titulo='$titulo', Texto='$texto', idCategoria=$rubro, idPais='$pais', idProvincia='$provincia', idCiudad='$ciudad', idLatitud='$latitud', idLongitud='$longitud', idMoneda='$moneda', Precio='$precio', Telefono='$telefonos', Email='$email', URLimg1='$imagenurl1', TXTimg1='$imagentxt1', URLimg2='$imagenurl2', TXTimg2='$imagentxt2', URLimg3='$imagenurl3', TXTimg3='$imagentxt3', URLimg4='$imagenurl4', TXTimg4='$imagentxt4', URLimg5='$imagenurl5', TXTimg5='$imagentxt5', URLimg6='$imagenurl6', TXTimg6='$imagentxt6', URLimg7='$imagenurl7', TXTimg7='$imagentxt7', ImagenOriginal='$imagenoriginal1', Video='$video', Tags='$tagsss', URL='$urlss', Completo='$completo', FechaLimpiado=NOW(), AutoMarca='$marca', AutoModelo='$modelo', AutoModeloVersion='$modeloversion', AutoAno='$ano', AutoCondicion='$condicion', AutoKM='$kilometraje', AutoColor='$color', AutoPuertas='$puertas', AutoNafta='$nafta', AutoDireccion='$direccion', AutoCaja='$caja',InmoDia=$dia,InmoSemana=$semana,InmoQuincena=$quincena,InmoMes=$mes,InmoMinmonoches=$minmonoches,InmoBalcon=$balcon,InmoBanadera=$banadera,InmoCalefaccionelectrica=$calefaccionelectrica,InmoDucha=$ducha,InmoPiscina=$piscina,InmoCajafuerte=$cajafuerte,InmoSeguridadalarma=$seguridadalarma,InmoAdmitemmascotas=$admitemmascotas,InmoPisosmadera=$pisosmadera,InmoBidette=$bidette,InmoCamassimples=$camassimples,InmoCamasqueensize=$camasqueensize,InmoAireacondicionado=$aireacondicionado,InmoCafetera=$cafetera,InmoDvd=$dvd,InmoHeladera=$heladera,InmoInternet=$internet,InmoLcd=$lcd,InmoMicroondas=$microondas,InmoMucama=$mucama,InmoSabanas=$sabanas,InmoSecadorcabello=$secadorcabello,InmoTelefono=$telefono,InmoToallas=$toallas,InmoTostadora=$tostadora,InmoTvporcable=$tvporcable,InmoWifi=$wifi,InmoLavadoralavarropa=$lavadoralavarropa,InmoGaraje=$garaje,InmoTerraza=$terraza,InmoJard=$jard,InmoEstacionamientotechado=$estacionamientotechado,InmoParrilla=$parrilla,InmoPatio=$patio,InmoAccesodiscapacitados=$accesodiscapacitados,InmoAscensor=$ascensor,InmoPortero=$portero,InmoServiciolimpieza=$serviciolimpieza,InmoVajilla=$vajilla,InmoPermitidofumar=$permitidofumar,InmoProhibidofumar=$prohibidofumar,InmoNoadmitenmascotas=$noadmitenmascotas,InmoCamadosplazas=$camadosplazas,InmoGimnasio=$gimnasio,InmoCapacidad='$capacidad',InmoDimension='$dimension',InmoAmbientes='$ambientes',InmoBarrio='$barrio',InmoDireccion='$direccion',InmoTipopropiedad='$tipopropiedad',idFicha='$id',alqenta='$alqenta'";
		//echo "<br />query_Email:".$query_Email;
		$resultado=mysql_query($query_Email,$GLOBALS['publica']) or die('3La consulta fallo: ' . mysql_error());
		$CantidadDatos=1;

		
		//LimpiaBorraAcomoda asi no queda tanta basura solo mete en web_limpios lo que va posta, sin repetidos
		
			//Saco lo del logo agregado, me pidieron que no saliera
			//if ($imagenurl1=="nada" || $imagenurl1=="") {$imagenurl1=="";} else if ($imagenurl2=="nada" || $imagenurl2=="") {$imagenurl2=$logoimg; $imagentxt2=$titulo;} else if ($imagenurl3=="nada" || $imagenurl3=="") {$imagenurl3=$logoimg; $imagentxt3=$titulo;} else if ($imagenurl4=="nada" || $imagenurl4=="") {$imagenurl4=$logoimg; $imagentxt4=$titulo;} else if ($imagenurl5=="nada" || $imagenurl5=="") {$imagenurl5=$logoimg; $imagentxt5=$titulo;}//cargo el logo de la pagina, si la primera esta vacia, lo dejo sin nada... asi le pone el foto vacia de alaMaula
			if ($imagenurl1=="nada") $imagenurl1="";
			if ($imagenurl2=="nada") $imagenurl2="";
			if ($imagenurl3=="nada") $imagenurl3="";
			if ($imagenurl4=="nada") $imagenurl4="";
			if ($imagenurl5=="nada") $imagenurl5="";
			if ($imagenurl6=="nada") $imagenurl6="";
			if ($imagenurl7=="nada") $imagenurl7="";

			//Controlo que el precio sea un entero, si no le clavo un 0
			if (!ControloTexto($precio,"0123456789")) {
				$precio="0";
				Actualizando("El precio no es un entero:".$urlss, "web_paginas");
			}
			if (strlen($precio)==0) {$precio="0";}			
		
			//Armo el select, para ver si ya lo tenia
			$Titulazo = "kkk"; //Si tiene un kkk significa que no lo tengo
			//$query_abajo= "SELECT Titulo FROM web_limpios WHERE Titulo='$Titulo' AND Pagina='$Pagina' AND Version=$Version LIMIT 1";  // Comparo el titulo con el resto de la misma pagina
			$query_abajo= "SELECT URL FROM web_limpios WHERE URL='$urlss' AND Pagina='$Pagina' AND Version=$Version LIMIT 1";  // Comparo el urlss con el resto de la misma pagina
			
			mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
			$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('Limpia1La consulta fallo: ' . mysql_error());
			if ($abajonota) {
				$Titulazo = mysql_result($abajonota,0,"Titulo");
			}
			if ($Titulazo==""){$Titulazo="kkk";}	
			mysql_free_result($abajonota);

			//echo "<br>Titulazo:".$Titulazo."///Titulo:".$titulo;

			//SACAR esto para controlar duplicados
			//$Titulazo="kkk";  //Meto todo por ahora

			//Pongo los controles de titulo, precio e imagenes
			$controlotodo=0;
			if ($Titulazo=="kkk"){$controlotodo=0;}
			else {
				//$controlotodo=1;  //NO CONTORLO EL TITULOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOOO
				Actualizando("urlss repetido:".$urlss, "web_paginas");
			} //No lo tengo, asi que vamos para adelante

			//if ($precio==0 && $rubro<>65 && $rubro<>66 && $rubro<>67){
				//$controlotodo=1; //LO SACO POR AHORA
			//	Actualizando("No tiene precio:".$urlss, "web_paginas");
			//} //Precio no puede ser =0


			//if ($imagenurl1==""){
			//	$controlotodo=1;
			//	Actualizando("No tiene Imagen:".$urlss, "web_paginas");
			//} //Si no tiene nada la primer imagen se rechaza

			if ($controlotodo==0){
				//meto los datos en la tabla que va de una si estan completos los datos
				if ($titulo<>"nada" && $titulo<>""){
				
					//Controlo que el texto del aviso tenga texto realmente, y no solamente tags...
					$CaracteresDesc=strlen($texto);
					$CaracteresDescSinTags=strlen(strip_tags($texto));
					//si tengo una diferencia demasido grande algo paso, si es mayor a 1000 no lo guardo en los limpios
					//if (($CaracteresDesc-$CaracteresDescSinTags)<1000){ //Cambio esto por ahora

						//Hago el insert en la tabla de datos bonitos
						$query_paginas = "INSERT web_limpios SET IdPaginas=$IdChoreadito, Pagina='$Pagina', Version=$Version, Titulo='$titulo', Descripcion='$texto', idPais='$pais', idProvincia='$provincia', idCiudad='$ciudad', idLongitud='$longitud', idLatitud='$latitud', idCategoria=$rubro, idTelefono='$telefonos', idMail='$email', idMoneda='$moneda', Precio='$precio', URLimg1='$imagenurl1', TXTimg1='$imagentxt1', URLimg2='$imagenurl2', TXTimg2='$imagentxt2', URLimg3='$imagenurl3', TXTimg3='$imagentxt3',URLimg4='$imagenurl4', TXTimg4='$imagentxt4',URLimg5='$imagenurl5', TXTimg5='$imagentxt5',URLimg6='$imagenurl6', TXTimg6='$imagentxt6',URLimg7='$imagenurl7', TXTimg7='$imagentxt7', ImagenOriginal='$imagenoriginal1', Video='$video', URL='$urlss', FechaLimpiado=NOW(), AutoMarca='$marca', AutoModelo='$modelo', AutoModeloVersion='$modeloversion', AutoAno='$ano', AutoCondicion='$condicion', AutoKM='$kilometraje', AutoColor='$color', AutoPuertas='$puertas', AutoNafta='$nafta', AutoDireccion='$direccion', AutoCaja='$caja',InmoDia=$dia,InmoSemana=$semana,InmoQuincena=$quincena,InmoMes=$mes,InmoMinmonoches=$minmonoches,InmoBalcon=$balcon,InmoBanadera=$banadera,InmoCalefaccionelectrica=$calefaccionelectrica,InmoDucha=$ducha,InmoPiscina=$piscina,InmoCajafuerte=$cajafuerte,InmoSeguridadalarma=$seguridadalarma,InmoAdmitemmascotas=$admitemmascotas,InmoPisosmadera=$pisosmadera,InmoBidette=$bidette,InmoCamassimples=$camassimples,InmoCamasqueensize=$camasqueensize,InmoAireacondicionado=$aireacondicionado,InmoCafetera=$cafetera,InmoDvd=$dvd,InmoHeladera=$heladera,InmoInternet=$internet,InmoLcd=$lcd,InmoMicroondas=$microondas,InmoMucama=$mucama,InmoSabanas=$sabanas,InmoSecadorcabello=$secadorcabello,InmoTelefono=$telefono,InmoToallas=$toallas,InmoTostadora=$tostadora,InmoTvporcable=$tvporcable,InmoWifi=$wifi,InmoLavadoralavarropa=$lavadoralavarropa,InmoGaraje=$garaje,InmoTerraza=$terraza,InmoJard=$jard,InmoEstacionamientotechado=$estacionamientotechado,InmoParrilla=$parrilla,InmoPatio=$patio,InmoAccesodiscapacitados=$accesodiscapacitados,InmoAscensor=$ascensor,InmoPortero=$portero,InmoServiciolimpieza=$serviciolimpieza,InmoVajilla=$vajilla,InmoPermitidofumar=$permitidofumar,InmoProhibidofumar=$prohibidofumar,InmoNoadmitenmascotas=$noadmitenmascotas,InmoCamadosplazas=$camadosplazas,InmoGimnasio=$gimnasio,InmoCapacidad='$capacidad',InmoDimension='$dimension',InmoAmbientes='$ambientes',InmoBarrio='$barrio',InmoDireccion='$direccion',InmoTipopropiedad='$tipopropiedad',idFicha='$id',alqenta='$alqenta'";
						
						//echo $query_paginas;
						$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('Limpia3La consulta fallo: ' . mysql_error());
						$CantidadListos=1;
					//}
				}
			} //ya lo tengo o tiene algo mal voy a buscar otro
	}

	//Actualizo la cantidad de la pagina actual, lo hago aca tambien para evitar cuando no anda que repita la misma pagina muchas veces
	$query_paginas = "UPDATE web_paginas SET CantidadBajadaLimpios=CantidadBajadaLimpios+$CantidadListos,CantidadBajadaDatos=CantidadBajadaDatos+$CantidadDatos WHERE IdChoreadito=$IdChoreadito";
	$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('UpdateCant7,1La consulta fallo: ' . mysql_error());

}



function LimpioTodoParaLevantar(){
	// pongo las variables globales
	include('globalvariablesbase.php');


	//Hago trim y strip_tag de todos.... despues lo de siempre
	$titulo=trim(strip_tags($titulo));
	$texto=trim(strip_tags($texto));
	$precio=trim(strip_tags($precio));
	$moneda=trim(strip_tags($moneda));
	$rubro=trim(strip_tags($rubro));
	$pais=trim(strip_tags($pais));
	$provincia=trim(strip_tags($provincia));
	$ciudad=trim(strip_tags($ciudad));
	$latitud=trim(strip_tags($latitud));
	$longitud=trim(strip_tags($longitud));
	$telefonos=trim(strip_tags($telefonos));
	$email=trim(strip_tags($email));
	$imagentxt1=trim(strip_tags($imagentxt1));
	$imagentxt2=trim(strip_tags($imagentxt2));
	$imagentxt3=trim(strip_tags($imagentxt3));
	$imagentxt4=trim(strip_tags($imagentxt4));
	$imagentxt5=trim(strip_tags($imagentxt5));
	$imagentxt6=trim(strip_tags($imagentxt6));
	$imagentxt7=trim(strip_tags($imagentxt7));
	$marca=trim(strip_tags($marca));
	$modelo=trim(strip_tags($modelo));
	$modeloversion=trim(strip_tags($modeloversion));
	$condicion=trim(strip_tags($condicion));
	$ano=trim(strip_tags($ano));
	$color=trim(strip_tags($color));
	$kilometraje=trim(strip_tags($kilometraje));
	$puertas=trim(strip_tags($puertas));
	$nafta=trim(strip_tags($nafta));
	$direccion=trim(strip_tags($direccion));
	$caja=trim(strip_tags($caja));

	//Limpia y arregla
	$texto=trim(str_replace("'"," ",$texto));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$texto=trim(str_replace(";",",",$texto));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$titulo=strip_tags($titulo); //Saco los tag del titulo
	$titulo=trim(str_replace("'","",$titulo));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$titulo=trim(str_replace('"',"",$titulo));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$titulo=trim(str_replace(';',",",$titulo));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt1=strip_tags($imagentxt1);//Saco los tag
	$imagentxt1=trim(str_replace("'","",$imagentxt1));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt1=trim(str_replace('"',"",$imagentxt1));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt1=trim(str_replace(';',",",$imagentxt1));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt2=strip_tags($imagentxt2);//Saco los tag
	$imagentxt2=trim(str_replace("'","",$imagentxt2));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt2=trim(str_replace('"',"",$imagentxt2));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt2=trim(str_replace(';',",",$imagentxt2));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt3=strip_tags($imagentxt3);//Saco los tag
	$imagentxt3=trim(str_replace("'","",$imagentxt3));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt3=trim(str_replace('"',"",$imagentxt3));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt3=trim(str_replace(';',",",$imagentxt3));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt4=strip_tags($imagentxt4);//Saco los tag
	$imagentxt4=trim(str_replace("'","",$imagentxt4));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt4=trim(str_replace('"',"",$imagentxt4));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt4=trim(str_replace(';',",",$imagentxt4));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt5=strip_tags($imagentxt5);//Saco los tag
	$imagentxt5=trim(str_replace("'","",$imagentxt5));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt5=trim(str_replace('"',"",$imagentxt5));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt5=trim(str_replace(';',",",$imagentxt5));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt6=strip_tags($imagentxt6);//Saco los tag
	$imagentxt6=trim(str_replace("'","",$imagentxt6));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt6=trim(str_replace('"',"",$imagentxt6));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt6=trim(str_replace(';',",",$imagentxt6));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt7=strip_tags($imagentxt7);//Saco los tag
	$imagentxt7=trim(str_replace("'","",$imagentxt7));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt7=trim(str_replace('"',"",$imagentxt7));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))
	$imagentxt7=trim(str_replace(';',",",$imagentxt7));//Cambio algunos caracteres que estan jodiendo(ESTE AL ULTIMO))))))))))))))



	//Limpio caracteres chotos, y reemplazo algunas cosas
	$CaracteresChotines=array("¨", "º","~","·", "'", "^", "`","´",'©','®','³','²','½','»','€','™');
	$search2   = array("Ã¡", "Ã³", "Ã*", "Ãº", "Ã©", "Ã±", "Ã", "Ã‰", "Ã“", "Ã", "Ãš", "Ã‘", "Ã ", "Ã?", "Ã¬", "Ã²", "Ã¹",  "Ã€", "Ãˆ", "ÃŒ", "Ã’", "Ã™", "Â¿","Ã?","Ã®","Â¡","Ãœ","ÃŽ","Ã§","Ã‡", "&amp;"); 	
	$replace2 = array("á", "ó", "í", "ú", "é", "ñ", "Á", "É", "Ó", "Í", "Ú", "Ñ", "à", "è", "ì", "ò", "ù", "ï", "À", "È", "Ì", "Ò", "Ù", "¿","ü","î","¡","Ü","Î","ç","Ç", "&"); 	
		
	
	
	//Reemplazo algunas cosas que despues hechan moco...
	$telefonos=strip_tags($telefonos);//Saco los tag
	$telefonos=trim(str_replace('/',"-",$telefonos));//Cambio algunos caracteres
	$telefonos=str_replace(" ","-",$telefonos); //Le saco los espacios
	
	if((strpos($titulo, "Ã")!== false)){ //Esta mal los caracteres
		$titulo=str_replace($search2,$replace2,$titulo); //Reemplazando
		$imagentxt1=str_replace($search2,$replace2,$imagentxt1); //Reemplazando
		$imagentxt2=str_replace($search2,$replace2,$imagentxt2); //Reemplazando
		$imagentxt3=str_replace($search2,$replace2,$imagentxt3); //Reemplazando
		$imagentxt4=str_replace($search2,$replace2,$imagentxt4); //Reemplazando
		$imagentxt5=str_replace($search2,$replace2,$imagentxt5); //Reemplazando
		$imagentxt6=str_replace($search2,$replace2,$imagentxt6); //Reemplazando
		$imagentxt7=str_replace($search2,$replace2,$imagentxt7); //Reemplazando
	}
	$titulo=trim(str_replace($CaracteresChotines," ",$titulo));//Cambio algunos caracteres
	$imagentxt1=trim(str_replace($CaracteresChotines," ",$imagentxt1));//Cambio algunos caracteres
	$imagentxt2=trim(str_replace($CaracteresChotines," ",$imagentxt2));//Cambio algunos caracteres
	$imagentxt3=trim(str_replace($CaracteresChotines," ",$imagentxt3));//Cambio algunos caracteres
	$imagentxt4=trim(str_replace($CaracteresChotines," ",$imagentxt4));//Cambio algunos caracteres
	$imagentxt5=trim(str_replace($CaracteresChotines," ",$imagentxt5));//Cambio algunos caracteres
	$imagentxt6=trim(str_replace($CaracteresChotines," ",$imagentxt6));//Cambio algunos caracteres
	$imagentxt7=trim(str_replace($CaracteresChotines," ",$imagentxt7));//Cambio algunos caracteres
	

	if((strpos($texto, "Ã")!== false)){ //Esta mal los caracteres en el texto
		$texto=str_replace($search2,$replace2,$texto); //Reemplazando
	}

	
	$texto=$llenoprincipio.$texto.$llenofinal;

	//Saco esto por ahora
	//if ($imagenurl2==""){$texto=$llenoprincipio.$texto.$llenofinal;} //Solo cargo la imagen en el texto si tiene al menos 2 fotos
	//else {  //Si existe va la primer foto como parte del texto tambien
	//	$texto=$llenoprincipio.$texto.'<br><br><img alt="'.$imagentxt1.'" width="650" src="'.$imagenurl1.'"><br><br>'.$llenofinal;
	//	$imagenurl1=$imagenurl2;
	//	$imagenurl2=$imagenurl3;
	//	$imagenurl3=$imagenurl4;
	//	$imagenurl4="";
	//	$imagentxt1=$imagentxt2;
	//	$imagentxt2=$imagentxt3;
	//	$imagentxt3=$imagentxt4;
	//	$imagentxt4="";
	//} 
	
	
	$imagenurl1=str_replace(" ","%20",$imagenurl1); //Reemplazando el espacio que a veces da problemas
	$imagenurl2=str_replace(" ","%20",$imagenurl2); //Reemplazando el espacio que a veces da problemas
	$imagenurl3=str_replace(" ","%20",$imagenurl3); //Reemplazando el espacio que a veces da problemas
	$imagenurl4=str_replace(" ","%20",$imagenurl4); //Reemplazando el espacio que a veces da problemas
	$imagenurl5=str_replace(" ","%20",$imagenurl5); //Reemplazando el espacio que a veces da problemas
	$imagenurl6=str_replace(" ","%20",$imagenurl6); //Reemplazando el espacio que a veces da problemas
	$imagenurl7=str_replace(" ","%20",$imagenurl7); //Reemplazando el espacio que a veces da problemas
	$imagenurl1=str_replace("&amp;","&",$imagenurl1); //Reemplazando el & que a veces da problemas
	$imagenurl2=str_replace("&amp;","&",$imagenurl2); //Reemplazando el & que a veces da problemas
	$imagenurl3=str_replace("&amp;","&",$imagenurl3); //Reemplazando el & que a veces da problemas
	$imagenurl4=str_replace("&amp;","&",$imagenurl4); //Reemplazando el & que a veces da problemas
	$imagenurl5=str_replace("&amp;","&",$imagenurl5); //Reemplazando el & que a veces da problemas
	$imagenurl6=str_replace("&amp;","&",$imagenurl6); //Reemplazando el & que a veces da problemas
	$imagenurl7=str_replace("&amp;","&",$imagenurl7); //Reemplazando el & que a veces da problemas
	
	$cadenaazar=randomString(rand(3,5));
	$imagenoriginal1=$imagenurl1; //guardo la primera imagen de donde la saco
	//Todo va por la api
	if (strlen($imagenurl1)!=0) {
		//Lo bajo al servidor de una
		$codigo_foto = $Pagina."_".$jiji.$cadenaazar."1.jpg";
		//Copio la imagen al servidor desde el origen
		$fotomia1=$GLOBALS['DirFotos'].$codigo_foto;
		$tamarch=0;
		$tamarch=recibe_imagen($imagenurl1,$fotomia1);
		if ($tamarch>1000){$imagenurl1=$GLOBALS['URLFotos'].$codigo_foto;}
		else {$imagenurl1="";}
		if(!$imagenurl1){ $imagenurl1=""; }
	}
	if (strlen($imagenurl2)!=0) {
		//Lo bajo al servidor de una
		$codigo_foto = $Pagina."_".$jiji.$cadenaazar."2.jpg";
		//Copio la imagen al servidor desde el origen
		$fotomia2=$GLOBALS['DirFotos'].$codigo_foto;
		$tamarch=0;
		$tamarch=recibe_imagen($imagenurl2,$fotomia2);
		if ($tamarch>1000){$imagenurl2=$GLOBALS['URLFotos'].$codigo_foto;}
		else {$imagenurl2="";}
		if(!$imagenurl2){ $imagenurl2=""; }
	}
	if (strlen($imagenurl3)!=0) {
		//Lo bajo al servidor de una
		$codigo_foto = $Pagina."_".$jiji.$cadenaazar."3.jpg";
		//Copio la imagen al servidor desde el origen
		$fotomia3=$GLOBALS['DirFotos'].$codigo_foto;
		$tamarch=0;
		$tamarch=recibe_imagen($imagenurl3,$fotomia3);
		if ($tamarch>1000){$imagenurl3=$GLOBALS['URLFotos'].$codigo_foto;}
		else {$imagenurl3="";}
		if(!$imagenurl3){ $imagenurl3=""; }
	}
	if (strlen($imagenurl4)!=0) {
		//Lo bajo al servidor de una
		$codigo_foto = $Pagina."_".$jiji.$cadenaazar."4.jpg";
		//Copio la imagen al servidor desde el origen
		$fotomia4=$GLOBALS['DirFotos'].$codigo_foto;
		$tamarch=0;
		$tamarch=recibe_imagen($imagenurl4,$fotomia4);
		if ($tamarch>1000){$imagenurl4=$GLOBALS['URLFotos'].$codigo_foto;}
		else {$imagenurl4="";}
		if(!$imagenurl4){ $imagenurl4=""; }
	}
	if (strlen($imagenurl5)!=0) {
		//Lo bajo al servidor de una
		$codigo_foto = $Pagina."_".$jiji.$cadenaazar."5.jpg";
		//Copio la imagen al servidor desde el origen
		$fotomia5=$GLOBALS['DirFotos'].$codigo_foto;
		$tamarch=0;
		$tamarch=recibe_imagen($imagenurl5,$fotomia5);
		if ($tamarch>1000){$imagenurl5=$GLOBALS['URLFotos'].$codigo_foto;}
		else {$imagenurl5="";}
		if(!$imagenurl5){ $imagenurl5=""; }
	}
	if (strlen($imagenurl6)!=0) {
		//Lo bajo al servidor de una
		$codigo_foto = $Pagina."_".$jiji.$cadenaazar."6.jpg";
		//Copio la imagen al servidor desde el origen
		$fotomia6=$GLOBALS['DirFotos'].$codigo_foto;
		$tamarch=0;
		$tamarch=recibe_imagen($imagenurl6,$fotomia6);
		if ($tamarch>1000){$imagenurl6=$GLOBALS['URLFotos'].$codigo_foto;}
		else {$imagenurl6="";}
		if(!$imagenurl6){ $imagenurl6=""; }
	}
	if (strlen($imagenurl7)!=0) {
		//Lo bajo al servidor de una
		$codigo_foto = $Pagina."_".$jiji.$cadenaazar."7.jpg";
		//Copio la imagen al servidor desde el origen
		$fotomia7=$GLOBALS['DirFotos'].$codigo_foto;
		$tamarch=0;
		$tamarch=recibe_imagen($imagenurl7,$fotomia7);
		if ($tamarch>1000){$imagenurl7=$GLOBALS['URLFotos'].$codigo_foto;}
		else {$imagenurl7="";}
		if(!$imagenurl7){ $imagenurl7=""; }
	}

}



function Actualizando($mensaje, $tabla){
	global $horitas;
	global $iniciando;
	global $UltimoDondeQuedo;
	global $CantidadLeido;
	global $PermiteEscribir;
	global $IdChoreadito;
	global $CantidadReal;
	global $CantidadImagenes;
	global $codigo_estadistica;
	
	//Actualizo la cantidad de la pagina actual
	$horitas = explode(' ', microtime());
	$terminando = $horitas[1] + $horitas[0];
	$tiempototal = ($terminando - $iniciando);
	$UltimoActual=$UltimoDondeQuedo+$CantidadLeido;

	if ($IdChoreadito=="") {$IdChoreadito=0;}
	if ($CantidadReal=="") {$CantidadReal=0;}
	if ($CantidadImagenes=="") {$CantidadImagenes=0;}
	if ($codigo_estadistica=="") {$codigo_estadistica=1;}

	$query_paginas = "UPDATE ".$tabla." SET FechaUltimo=NOW(),TiempoUltimaVez=$tiempototal WHERE IdChoreadito=$IdChoreadito";
	//echo $query_paginas;
	$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('Act7La consulta fallo: ' . mysql_error());

	//Actualizo estadistica
	$mensaje=":::::".$mensaje;
	$mensaje=trim(str_replace("'","",$mensaje));

	$query_paginas = "UPDATE web_estadistica SET FechaTermina=NOW(),CantidadObtenido=$CantidadReal,CantidadImagenes=$CantidadImagenes,Mensaje=CONCAT(Mensaje,'".$mensaje."') WHERE IdEstadistica=$codigo_estadistica";
	//echo "Consulta:".$query_paginas;
	$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('Act8La consulta fallo: ' . mysql_error());

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

function api_imagen($imgPath,$imgName){
	//Ebay session api handler
	require_once($GLOBALS['DirFotos'].'ebaySession.php');
	//Alamaula eps class
	require_once($GLOBALS['DirFotos'].'epsupload.php');
	
	//set api configuration
	$is_test = true;
	
	$devID_test = '54eb4b78-20d0-45b4-b454-a74830afdb58'; //jorge's
	$appID_test = 'alamaula-c321-4163-8c02-9bd3cd65182d'; //jorge's
	$certID_test = '4548c3e3-ed62-4e79-9559-becfab77adaa'; //jorge's
	$userToken_test = 'AgAAAA**AQAAAA**aAAAAA**nMHHTw**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wFk4GhCJmCpwidj6x9nY+seQ**RdMBAA**AAMAAA**EMPklxl6tunfz2P/E3Zg8jlT/xNL9TzyTKltCViLrc3uK/9Kgyl+KMGru30CvGzxITimdjqA1AGy7Q4Nm87jATHYtaglGVNNcLXqqLaJi4LTIXJJyW0HsMQT1LB3llx2dMTiLT7nlImuQwIVN+11jF+f+EqWE+qyc7msA4fc+LT8nDP+Fhn+8jXeaQofVy3kr+vIGR6XUPtj/oqcXNeGfmRPFTsSngQLsMXjxPy12LxwworARTIniWnEkSHu3525feFeP5qtpqg7dToXsAYcU8Y0NA6kgMKCISGxAahQK7Yg71DahnbcjyC9dBPLUcX56HnP2xIsM61ZryHMFNvIVQQfk9lC43hEIN4KXzb4wCdtcjPppAGbzqyNIkPNazRnTbOeQ5o2VVUduiUL/yUDA58E18WAZEwpFVqsK5k4UcHLZdIAukPJS41V0JdLUiLmS+p2FaYCJaqThZbZnMYljUnD9oFUgiPzJa6BE7GXEgpKOCHyFjt10aQ0o2oU1Ts8+xkVaeEXK4+Z1PINcvhLtdCibQxYP+DuVWUMYqdUTZbAmuUNVGCTAaI08ARNSz9R0eqwpX+3xUPhuZ52UTaS7ptkW9wn+IxxC45FHda0LDZo4Nu0jjhUHM7rhIjJjRpYty5Qcx8pwouZqVpHEh53NkAn59x0/7N0TA3a+jNUIl+1h5YWNoYziLC6kd84uywHwMoey4F38UfAN38gMwbyRKwbRWKT2DXG2vhSZ4Fpf25KKJXjJAWiIlW2F0cvk+qH'; //jorge's sandbox
	
	$devID_prod = ''; //prod
	$appID_prod = ''; //prod
	$certID_prod = ''; //prod
	$userToken_prod = ''; //prod
	
	//site id 0 is USA (just for testing purpose)
	$siteID  = 0;
	//verb is the ebay api request to be called
	$verb = 'UploadSiteHostedPictures';

	//check if it use sandbox or production services
	$appID = $appID_test;
	$devID = $devID_test;
	$certID = $certID_test;
	$userToken = $userToken_test;

	//instance the class that builds the xml to use in the call
	$eps_upload = new epsupload($userToken, $siteId=0);
	//build xml to be sent by post to the api
	$fullPost = $eps_upload->buildPost($imgName, $imgPath);
	
	//connect to the API
	$session = new eBaySession($userToken, $devID, $appID, $certID, $is_test, 517, $siteID, $verb);
	//Send request and return the result
	$result = $session->sendHttpRequest($fullPost);


	if(!empty($result)):
		
		if(isset($result->SiteHostedPictureDetails)):	
		
			$IMGurl=str_replace('_12','_10',$result->SiteHostedPictureDetails->FullURL);
			$IMGname=$result->SiteHostedPictureDetails->PictureName;
			
			echo $result->SiteHostedPictureDetails->PictureSetMember->PictureWidth;
			echo '<br>';
		    
		endif;
		    
	endif;

	return $IMGurl;
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
	//$cadena = utf8_decode($cadena);
	for ($i=0; $i<strlen($cadena); $i++){ 
		if (strpos($permitidos, substr($cadena,$i,1))===false){
			//echo substr($cadena,$i,1);
			return false; 
		} 
	}
	return true; 
}


function  ControloMalasPalabras($cadena){
	//Armo el select, para ver si tiene malas palbras
	$Palabraza = "kkk"; //Si tiene un kkk significa que no lo tengo
	//$query_control= "SELECT * , MATCH (palabra) AGAINST ('".$cadena."') AS relevancia FROM web_stopwords WHERE MATCH (palabra) AGAINST ('".$cadena."') ORDER BY relevancia LIMIT 1";
	$query_control= "SELECT * FROM web_stopwords WHERE '".$cadena."' LIKE CONCAT('% ',palabra,' %') LIMIT 1";
	
	mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
	$controltexto = mysql_query($query_control, $GLOBALS['publica']) or die('ControlMalasPalabras1La consulta fallo: ' . mysql_error());
	if ($controltexto) {
		$Palabraza = mysql_result($controltexto,0,"palabra");
	}
	if ($Palabraza==""){$Palabraza="kkk";} //si no lo encuentra lo pasa
	mysql_free_result($controltexto);

	if ($Palabraza=="kkk"){
		return true; 
	} else {
		return false; 
	}
}


function  ControloMalasPalabras_nuevo($arr,$cadena){

	foreach($arr as $elem){
		$cadena=str_replace($elem,'<span style="background-color:red">'.$elem.'</span>',$cadena);
	}
	
	return $cadena;
}

//Se usa
  //global $localidadPosta;
  //global $provinciaPosta;
  //BuscaLocalidad($lolidadprobable, $provinciaprobable, "AR");
  //$ciudad = $localidadPosta;
  //$provincia = $provinciaPosta;
function BuscaLocalidad($localidaddd, $provinciaaa, $paisss){
	global $localidadPosta;
	global $provinciaPosta;
	
	if ($localidaddd<>"" && $provinciaaa<>"" && $paisss<>""){
		//Viene con todos los datos
		//Armo el select, para buscar la localidad
		$encontro = 0; //Si tiene un 0 significa que no lo tengo
		$query_abajo= "SELECT Provincia, Localidad, MATCH Localidad AGAINST ('".$localidaddd."' IN BOOLEAN MODE) AS Relevance FROM Localidades WHERE Pais = '".$paisss."' AND MATCH Localidad AGAINST ( '".$localidaddd."' IN BOOLEAN MODE) HAVING Relevance > 0.2  AND MATCH Provincia AGAINST ( '".$provinciaaa."' IN BOOLEAN MODE) ORDER BY Relevance DESC LIMIT 1";


		//echo "<br />query_abajo:".$query_abajo;		
		mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
		$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('Limpia1La consulta fallo: ' . mysql_error());
		$localidadPosta = trim(mysql_result($abajonota,0,"Localidad"));
		$provinciaPosta = trim(mysql_result($abajonota,0,"Provincia"));
		if ($localidadPosta <>"") {

			$encontro = 1;
			
			//$localidad=trim($localidad); //Reemplazo 3 primeros caracteres que quedaron mal
			//$localidad=strtr($localidad,'???????????????????????????????????????????????????','aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
			//echo "<br />localidad:".$localidad;
			
		}
		mysql_free_result($abajonota);
		
		
		if ($encontro==0){ //Si no lo encuentra busco la principal de la provincia y le pongo eso
			//Armo el select, para buscar el encontro
			$encontro = 0; //Si tiene un 0 significa que no lo tengo
			$query_abajo= "SELECT Provincia, Localidad FROM Localidades WHERE Pais = '".$paisss."' AND PrincipalProv AND MATCH Provincia AGAINST ( '".$provinciaaa."' IN BOOLEAN MODE) LIMIT 1";


			//echo "<br />query_abajo2:".$query_abajo;		
			mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
			$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('Limpia1La consulta fallo: ' . mysql_error());
			if ($abajonota) {
				$localidadPosta = trim(mysql_result($abajonota,0,"Localidad"));
				$provinciaPosta = trim(mysql_result($abajonota,0,"Provincia"));
			} else { //Si no se encontro nada lo dejo en blanco despues dara error en el xml y corregirlo a mano
				$localidadPosta = "";
				$provinciaPosta = "";
			}
			mysql_free_result($abajonota);
		}
	} else {
		//Viene solo con la provincia y el pais
		$query_abajo= "SELECT Provincia, Localidad FROM Localidades WHERE Pais = '".$paisss."' AND PrincipalProv AND MATCH Provincia AGAINST ( '".$provinciaaa."' IN BOOLEAN MODE) LIMIT 1";


		echo "<br />query_abajo2:".$query_abajo;		
		mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
		$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('Limpia1La consulta fallo: ' . mysql_error());
		if ($abajonota) {
			$localidadPosta = trim(mysql_result($abajonota,0,"Localidad"));
			$provinciaPosta =trim( mysql_result($abajonota,0,"Provincia"));
		} else { //Si no se encontro nada lo dejo en blanco despues dara error en el xml y corregirlo a mano
			$localidadPosta = "";
			$provinciaPosta = "";
		}
		mysql_free_result($abajonota);		
	}
	
}

function BuscaLocalidadExiste($localidaddd, $provinciaaa, $paisss){
	//Armo el select, para buscar la localidad
	$query_abajo= "SELECT * FROM Localidades WHERE Pais = '".$paisss."' AND Localidad = '".$localidaddd."' AND Provincia = '".$provinciaaa."'";

	echo "<br />query_abajo:".$query_abajo;		
	mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
	$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('Localidad 1La consulta fallo: ' . mysql_error());

	if ($abajonota) {
	  $localidadPosta = mysql_result($abajonota,0,"Localidad");
	  $provinciaPosta = mysql_result($abajonota,0,"Provincia");
	}
while($row = mysql_fetch_array($abajonota)){ 
echo $row['Provincia'].'fff'.$row['Localidad']; 
} 
	mysql_free_result($abajonota);
	if ($localidadPosta == $localidaddd && $provinciaPosta == $provinciaaa) {return true;} else {return false;}
}



//Busca la categoria posta segun el titulo...
//Pasar como parametro el titulo, la categoria probable
function BuscaCategoria($tituloss, $categoriadefecto){
	// La categoria llega completa, por ejemplo, 'XX,19,15,11'
	//XX dice que hay que buscar categorias, 19 por defecto, 15 y 11 aparte de las 19 en las que hay que buscar
	$categoriasprobables=explode(',',$categoriadefecto);
	$categoriaposta=$categoriasprobables[1]; //la categoria por defecto es la 1 (el 0 es XX)


	//limpio el titulo que trae para poder compararlo correctamente
	$contsep=explode(" ", $tituloss);
	$tituloss="";
	foreach($contsep as &$value){	
		if(strlen($value) > 3){
		    $tituloss .=$value . ' ';
		}
	}

	$CaracteresChotines=array("¨", "º","~","·", "'", "^", "`","´",'©','®','³','²','½','»','€','™',".",",",":",";","_","!","?","¿","¡");
	$search2   = array("Ã­","Ã¡", "Ã³", "Ã*", "Ãº", "Ã©", "Ã±", "Ã", "Ã‰", "Ã“", "Ã", "Ãš", "Ã‘", "Ã ", "Ã?", "Ã¬", "Ã²", "Ã¹",  "Ã€", "Ãˆ", "ÃŒ", "Ã’", "Ã™", "Â¿","Ã?","Ã®","Â¡","Ãœ","ÃŽ","Ã§","Ã‡", "&amp;"); 	
	$replace2 = array("i","a", "o", "i", "u", "e", "ñ", "Á", "É", "Ó", "Í", "Ú", "Ñ", "a", "e", "i", "o", "u", "ï", "À", "È", "Ì", "Ò", "Ù", "¿","ü","î","¡","Ü","Î","ç","Ç", "&"); 	
	$caractereschotazos = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ"; 
	$caracteresnormales = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"; 	
	
	echo "<br>".$tituloss."--"; //probando

	if(preg_match("/[áéíóúÁÉÍÓÚñÑ]/", utf8_encode($tituloss))){$tituloss=utf8_encode($tituloss);}
	$tituloss=str_replace($search2,$replace2,$tituloss); //Reemplazando
	$tituloss=str_replace($caractereschotazos,$caracteresnormales,$tituloss); //Reemplazo los ultimos caracteres que pueden haber quedado
	$tituloss=trim(str_replace($CaracteresChotines," ",$tituloss));//Cambio algunos caracteres
	$tituloss=strtolower($tituloss); //todo minusculas

	echo $tituloss; //probando

	$query_abajo= "SELECT * FROM web_categorias WHERE TextoBusqueda<>''";
	mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
	$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('Limpia1La consulta fallo: ' . mysql_error());
	$iii=0;
	while ($site = mysql_fetch_assoc($abajonota)) {
		//Veo si la categoria esta dentro de las probables, asi no trabaja al pedo y es mas precisa la busqueda
		if (in_array($site['idCategoria'], $categoriasprobables)) {
			$idCategoria=$site['idCategoria'];
			$TextoCategoria=$site['TextoBusqueda'];

			//Meto todo en un array
			$contsep=explode(",", $TextoCategoria);
			foreach($contsep as &$value){	
				$categoriabusca[$iii][1]=strtolower($value); //texto
				$categoriabusca[$iii][2]=$idCategoria; //categoria
				$iii=$iii+1;
			}
		}
	}
	mysql_free_result($abajonota);

	//Busco cada palabra que queda, y las agrego en un contador para categorias, asi la categoria que tenga mas palabras, sale con fritas
	//$categoriacasi = 0; //Si tiene un 0 significa que no lo tengo
	$contsep=explode(" ", $tituloss);
	foreach($contsep as &$value){
		for ($jjj = 0; $jjj<=$iii; $jjj++) {
			similar_text(trim($categoriabusca[$jjj][1]), trim($value), $porcparecido);
		    if ($porcparecido>85){
		    	$categoriacasi[$categoriabusca[$jjj][2]]=$categoriacasi[$categoriabusca[$jjj][2]]+1;
		    }
		}
	}

	$categoriasprobables[0]=0; //saco la XX porque si no no me deja  sacar el maximo
	$cantidadcategorias=max($categoriasprobables);
	$mayor1=0;
	$indice1=0;
	$mayor2=0;
	$indice2=0;

	for($cci=1;$cci<=$cantidadcategorias;$cci++){ //define las 2 categorias con mas aciertos
		if($categoriacasi[$cci]>$mayor2){
			$mayor2=$categoriacasi[$cci];
			$indice2=$cci;
		}
		if($categoriacasi[$cci]>$mayor1){
			$mayor2=$mayor1;
			$indice2=$indice1;			
			$mayor1=$categoriacasi[$cci];
			$indice1=$cci;
		}
	}

	//Define que categoria se devuelve efectivamente
	if ($mayor1>$mayor2){$categoriaposta=$indice1;} //tengo la categoria mayor
	if ($mayor1==$mayor2){ //tengo un empate
		if ($indice1==$categoriaposta || $indice2==$categoriaposta){ //si es igual al por defecto lo dejo al por defecto
			$categoriaposta=$categoriaposta;
		}else {
			if ($mayor1==0){
				$categoriaposta=$categoriaposta; //No se encontro nada, va el por defecto de una
			} else {
				$categoriaposta=0; //"BUSCAR CATEGORIA A MANO:".$indice1."..".$indice2;	//si es empate se ve a mano y listo
			}
		}
	}

	echo "<br>categoriaposta:".$categoriaposta;
	return $categoriaposta;
}


//Busca la categoria posta segun el titulo...
//Pasar como parametro el titulo, la categoria probable
//devuelve un texto con categoria y separado con coma el atributo en el caso de existir
function BuscaCategoriaFull($tituloss, $categoriadefecto){
	// La categoria llega completa, por ejemplo, 'XX,19,15,11'
	//XX dice que hay que buscar categorias, 19 por defecto, 15 y 11 aparte de las 19 en las que hay que buscar
	$categoriasprobables=explode(',',$categoriadefecto);
	$categoriaposta=$categoriasprobables[1]; //la categoria por defecto es la 1 (el 0 es XX)


	//limpio el titulo que trae para poder compararlo correctamente
	$contsep=explode(" ", $tituloss);
	$tituloss="";
	foreach($contsep as &$value){	
		if(strlen($value) > 3){
		    $tituloss .=$value . ' ';
		}
	}

	$CaracteresChotines=array(">","<","-","|","}","{","[","]","/","+", "=", "(", ")", "%", "#", "&", "$", "*", "¨", "º","~","·", "'", "^", "`","´",'©','®','³','²','½','»','€','™',".",",",":",";","_","!","?","¿","¡");
	$search2   = array("Ã­","Ã¡", "Ã³", "Ã*", "Ãº", "Ã©", "Ã±", "Ã", "Ã‰", "Ã“", "Ã", "Ãš", "Ã‘", "Ã ", "Ã?", "Ã¬", "Ã²", "Ã¹",  "Ã€", "Ãˆ", "ÃŒ", "Ã’", "Ã™", "Â¿","Ã?","Ã®","Â¡","Ãœ","ÃŽ","Ã§","Ã‡", "&amp;"); 	
	$replace2 = array("i","a", "o", "i", "u", "e", "ñ", "Á", "É", "Ó", "Í", "Ú", "Ñ", "a", "e", "i", "o", "u", "ï", "À", "È", "Ì", "Ò", "Ù", "¿","ü","î","¡","Ü","Î","ç","Ç", "&"); 	
	$caractereschotazos = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ"; 
	$caracteresnormales = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"; 	
	
	if(preg_match("/[áéíóúÁÉÍÓÚñÑ]/", utf8_encode($tituloss))){$tituloss=utf8_encode($tituloss);}
	$tituloss=str_replace($search2,$replace2,$tituloss); //Reemplazando
	$tituloss=str_replace($caractereschotazos,$caracteresnormales,$tituloss); //Reemplazo los ultimos caracteres que pueden haber quedado
	$tituloss=trim(str_replace($CaracteresChotines," ",$tituloss));//Cambio algunos caracteres
	$tituloss=trim(strtolower(strip_tags($tituloss)));
	$tituloss=str_ireplace($GLOBALS['TextoIrrelevante']," ",$tituloss);  //saco palabras irrelevantes

	echo "<br>Palabras:".$tituloss; //probando

	//Busco cada palabra que queda, y las agrego en un contador para categorias, asi la categoria que tenga mas palabras, sale con fritas
	//$categoriacasi = 0; //Si tiene un 0 significa que no lo tengo
	$contsep=explode(" ", $tituloss);
	//print_r($contsep);

	$categoriasprobables[0]=0; //saco la XX porque si no no me deja  sacar el maximo y buscar

	foreach($contsep as &$value){

		//$query_abajo= "SELECT Palabra,idCategoria,Peso,MATCH(Palabra) AGAINST('".utf8_decode(trim($value))."') AS porcparecido FROM web_categdic WHERE idCategoriaPadre IN(".implode(',',$categoriasprobables).") ORDER BY porcparecido DESC LIMIT 2";
		$query_abajo= "SELECT Palabra,idCategoria,idCategoriaSubSub,Peso,levenshtein_ratio(Palabra,'".utf8_decode(trim($value))."') AS porcparecido FROM web_categdic WHERE idCategoriaPadre IN(".implode(',',$categoriasprobables).") ORDER BY porcparecido DESC , peso DESC LIMIT 2";
		//echo "<br>Query:".$query_abajo;
		
		mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
		$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('Diccionariocompara1La consulta fallo: ' . mysql_error());
		$iii=0;
		while ($site = mysql_fetch_assoc($abajonota)) {
			echo "<br>".$site['porcparecido']."---".$site['Palabra']."---".$site['idCategoria'];
		    if ($site['porcparecido']>89){
		    	$iii=$iii+1;
		    	$categoriasexistentes[$iii]=$site['idCategoria'];
		    	$categoriacasi[$site['idCategoria']]=$categoriacasi[$site['idCategoria']]+1;  //cantidad acumulada por categoria
		    	$porcentajecasi[$site['idCategoria']]=$porcentajecasi[$site['idCategoria']]+$site['porcparecido']; //porc acumulado por categoria
		    	$pesocasi[$site['idCategoria']]=$pesocasi[$site['idCategoria']]+$site['Peso']; //peso acumulado por categoria
		    	$categoriasubsubcasi[$site['idCategoria']]=$site['idCategoriaSubSub']; //categoriasubsub de la palabra
		    }
		}
	}

	$mayor1=0;
	$indice1=0;
	$mayor2=0;
	$indice2=0;

	foreach($categoriasexistentes as $cci){ //define las 2 categorias con mas aciertos
		if($pesocasi[$cci]>$mayor2){
			$mayor2=$pesocasi[$cci];
			$indice2=$cci;
		}
		if($pesocasi[$cci]>$mayor1){
			$mayor2=$mayor1;
			$indice2=$indice1;			
			$mayor1=$pesocasi[$cci];
			$indice1=$cci;
		}
	}


	echo "<br>indice1:".$indice1;
	echo "<br>indice2:".$indice2;

	//Define que categoria se devuelve efectivamente
	if ($mayor1>$mayor2){$categoriaposta=$indice1.",".$categoriasubsubcasi[$indice1];} //tengo la categoria mayor
	if ($mayor1==$mayor2){ //tengo un empate
		if ($mayor1==0){
			$categoriaposta=$categoriaposta.",0"; //No se encontro nada, va el por defecto de una
		} else {
			if ($indice1==$indice2 && $categoriasubsubcasi[$indice1]==$categoriasubsubcasi[$indice2]){
				$categoriaposta=$indice1.",".$categoriasubsubcasi[$indice1];
			} else {
				//Tenemos un empate de los 2 indices distintos y que se encontro algo (no es 0)
				$categoriaposta="AMANO-".$indice1.",".$categoriasubsubcasi[$indice1].",".$indice2.",".$categoriasubsubcasi[$indice2]; //"BUSCAR CATEGORIA A MANO:".$indice1."..".$indice2;	//es empate de categorias distintas se ve a mano y listo
			}
		}
	}

	echo "<br>categoriaposta:".$categoriaposta;
	return $categoriaposta;
}




//Busca la marca y modelo segun un texto
//Devuelve un array con marca y modelo return array($AutoMarca,$AutoModelo);
//Se lee list($AutoMarca,$AutoModelo) = BuscaMarcaModelo($textobuscar); 
function BuscaMarcaModelo($textopelado){
	//limpio el titulo que trae para poder compararlo correctamente
	$contsep=explode(" ", $textopelado);
	$tituloss="";
	foreach($contsep as &$value){	
		if(strlen($value) > 1){
		    $tituloss .=$value . ' ';
		}
	}

	$CaracteresChotines=array("¨", "º","~","·", "'", "^", "`","´",'©','®','³','²','½','»','€','™',".",",",":",";","_","!","?","¿","¡");
	$search2   = array("Ã¡", "Ã³", "Ã*", "Ãº", "Ã©", "Ã±", "Ã", "Ã‰", "Ã“", "Ã", "Ãš", "Ã‘", "Ã ", "Ã?", "Ã¬", "Ã²", "Ã¹",  "Ã€", "Ãˆ", "ÃŒ", "Ã’", "Ã™", "Â¿","Ã?","Ã®","Â¡","Ãœ","ÃŽ","Ã§","Ã‡", "&amp;"); 	
	$replace2 = array("á", "ó", "í", "ú", "é", "ñ", "Á", "É", "Ó", "Í", "Ú", "Ñ", "à", "è", "ì", "ò", "ù", "ï", "À", "È", "Ì", "Ò", "Ù", "¿","ü","î","¡","Ü","Î","ç","Ç", "&"); 	
	$caractereschotazos = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ"; 
	$caracteresnormales = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn"; 	
	
	$tituloss=str_replace($search2,$replace2,$tituloss); //Reemplazando
	$tituloss=str_replace($caractereschotazos,$caracteresnormales,$tituloss); //Reempla zo los ultimos caracteres que pueden haber quedado
	$tituloss=trim(str_replace($CaracteresChotines," ",$tituloss));//Cambio algunos caracteres
	$tituloss=strtolower($tituloss); //todo minusculas

	$query_abajo= "SELECT * FROM web_modelos WHERE Modelo<>''";
	mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
	$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('Limpia1La consulta fallo: ' . mysql_error());
	$iii=0;
	while ($site = mysql_fetch_assoc($abajonota)) {
		$modelobusca[$iii][1]=str_replace($search2,$replace2,$site['Marca']); //Reemplazando
		$modelobusca[$iii][1]=str_replace($caractereschotazos,$caracteresnormales,$modelobusca[$iii][1]); //Reempla zo los ultimos caracteres que pueden haber quedado
		$modelobusca[$iii][1]=trim(str_replace($CaracteresChotines," ",$modelobusca[$iii][1]));//Cambio algunos caracteres

		$modelobusca[$iii][2]=str_replace($search2,$replace2,$site['Modelo']); //Reemplazando
		$modelobusca[$iii][2]=str_replace($caractereschotazos,$caracteresnormales,$modelobusca[$iii][2]); //Reempla zo los ultimos caracteres que pueden haber quedado
		$modelobusca[$iii][2]=trim(str_replace($CaracteresChotines," ",$modelobusca[$iii][2]));//Cambio algunos caracteres		


		$modelobusca[$iii][1]=strtolower(trim($modelobusca[$iii][1]));
		$modelobusca[$iii][2]=strtolower(trim($modelobusca[$iii][2]));
		$iii=$iii+1;
	}
	mysql_free_result($abajonota);



	//Busco cada palabra que queda
	$marcacasi = 0; //Si tiene un 0 significa que no lo tengo
	$contsep=explode(" ", $tituloss);
	foreach($contsep as &$value){
		for ($jjj = 0; $jjj<$iii; $jjj++) {
			similar_text(trim($modelobusca[$jjj][1]), trim($value), $porcparecido); //1° Busca Marca 
		    if ($porcparecido>90){$marcacasi=$jjj; break;}
		}
		if ($marcacasi<>0){break;} //salgo del loop
	}

	if ($marcacasi==0){ //Busco marcas con mas de una palabra por si acaso
		for ($jjj = 0; $jjj<$iii; $jjj++) {
			if ((strpos($tituloss, $modelobusca[$jjj][1]))!== false){$marcacasi=$jjj; break;}
		}
	}

	if ($marcacasi<>0){ //Busca el modelo con la marca
		$modelocasi = 0; //Si tiene un 0 significa que no lo tengo
		$contsep=explode(" ", $tituloss);
		foreach($contsep as &$value){
			for ($jjj = 0; $jjj<$iii; $jjj++) {
				if ($modelobusca[$marcacasi][1]==$modelobusca[$jjj][1]){
					similar_text(trim($modelobusca[$jjj][2]), trim($value), $porcparecido); //Busca Modelo
				    if ($porcparecido>90){$modelocasi=$jjj; break;}
				 }
			}
			if ($modelocasi<>0){break;} //salgo del loop
		}
	} else { //Busca el modelo pelado sin la marca
		$modelocasi = 0; //Si tiene un 0 significa que no lo tengo
		$contsep=explode(" ", $tituloss);
		foreach($contsep as &$value){
			for ($jjj = 0; $jjj<$iii; $jjj++) {
				similar_text(trim($modelobusca[$jjj][2]), trim($value), $porcparecido); //Busca Modelo
			    if ($porcparecido>90){$modelocasi=$jjj; break;}
			}
			if ($modelocasi<>0){break;} //salgo del loop
		}
	}

	if ($marcacasi<>0 && $modelocasi<>0){return array($modelobusca[$modelocasi][1],$modelobusca[$modelocasi][2]);}
	if ($marcacasi==0 && $modelocasi<>0){return array("BUSCAR A MANO ... ".$modelobusca[$modelocasi][1],$modelobusca[$modelocasi][2]);}
	if ($marcacasi<>0 && $modelocasi==0){return array($modelobusca[$marcacasi][1],"BUSCAR A MANO");}
	if ($marcacasi==0 && $modelocasi==0){return array("BUSCAR A MANO","BUSCAR A MANO");}
	

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

	return $textoleido;
}


class HttpRequest{
    public $Url;
    public $DatosPublicar;
    public $Retorno;
    public $Devuelto;
 
    public function __construct( $Url, $DatosPublicar = array(), $Headerr="" , $UsaProxy="", $UsaPort="" ){
        $this->Url = $Url;
        $this->DatosPublicar = $DatosPublicar;
        $this->Headerr = $Headerr;
        $this->UsaProxy = $UsaProxy;
        $this->UsaPort = $UsaPort;
        $this->CargaPagina();
    }   
 
 
    public function CargaPagina( ){
        $CookiePath = "/var/www/vhosts/marcelo/apdb2.no-ip.info/httpdocs/blogviolentoscookie.txt";
 
        $ch = curl_init(); //Inicia objeto curl
        curl_setopt($ch, CURLOPT_COOKIEFILE, $CookiePath);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $CookiePath);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/28.0.1500.71 Chrome/28.0.1500.71 Safari/537.36');
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

        curl_setopt($ch, CURLOPT_VERBOSE, 0);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_FILETIME, 1);
	    //curl_setopt($ch, CURLOPT_TIMEOUT, );
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


//Funcion de Emanuel que levanta el telefono de la pagina Doplim (con jquery)
function QueryPhone($url, $proxy="", $port="")
{
	$html = file_get_contents($url);
	preg_match_all("/\(\"#datafono\"\).load\(\"([^\"]+)\",[^\{]*\{id:[^0-9]*([0-9]+)/i", $html, $mat);

	$url_to_post = $mat[1][0];
	$id = $mat[2][0];
	$query = "&id=".urlencode($id);
	$con = new HttpRequest($url_to_post, $query, "", $proxy, $port);
	preg_match_all("/<b>([0-9]+)<\/b>/i", $con->Retorno, $mat);
	$phoneNumber = $mat[1][0];
	unset($con);
	return $phoneNumber;
}


function ObtieneDireccion( $lat, $lng ){   
    // Construct the Google Geocode API call
    $URL = "http://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&sensor=false";

    // Extract the location lat and lng values
    $data = file( $URL );
    foreach ($data as $line_num => $line) 
    {
        if ( false != strstr( $line, "\"formatted_address\"" ) )
        {
            $addr = substr( trim( $line ), 22, -2 );
            break;
        }
    }
	$addr=str_replace('"','',$addr);
    return $addr;
}
?>

