<?php
function Recursivo($Pagina, $Dominio, $Recursivo, $Corta, $Primera, $IdentificaArticulo, $IdentificaLink, $NOIdentificaLink){
	//Parametros
	//$Pagina Codigo de la pagina seria el nombre de la fuente en este caso
	$Dominio=str_replace('***',"&",$Dominio);  //Pagina Origen de todo (lo guardo en la base)
	$Dominio=str_replace('===',";",$Dominio);  //Cambio otros caracteres
	$Dominio=str_replace('=*=',"?",$Dominio);  //Cambio otros caracteres
	//$Recursivo Si la busqueda de las paginas de busqueda es recursiva o lo hago por numeros o busca todo (R o N o T o E)
	//$Corta Cantidad de notas a buscar para que corte
	$Primera=str_replace('***',"&",$Primera); //Pagina desde donde se inicia el crawler, por lo general pagina de busqueda
	$Primera=str_replace('===',";",$Primera);  //Cambio otros caracteres
	$Primera=str_replace('=*=',"?",$Primera);  //Cambio otros caracteres
	$IdentificaArticulo=str_replace('***',"&",$IdentificaArticulo);  //Algo en el link del articulo que lo identifique como articulo 'veritem.html'
	$IdentificaArticulo=str_replace('===',";",$IdentificaArticulo);  //Cambio otros caracteres
	$IdentificaArticulo=str_replace('=*=',"?",$IdentificaArticulo);  //Cambio otros caracteres
	$IdentificaLink=str_replace('***',"&",$IdentificaLink); //Algo en la URL de la busqueda que lo identifique como busqueda y listado de articulos 'pagina='
	$IdentificaLink=str_replace('===',";",$IdentificaLink);  //Cambio otros caracteres
	$IdentificaLink=str_replace('=*=',"?",$IdentificaLink);  //Cambio otros caracteres
	$NOIdentificaLink=str_replace('***',"&",$NOIdentificaLink); //Algo en la URL de la busqueda para filtrar que lo identifique como que NO es de busqueda y NO es listado de articulos '&amp;'
	$NOIdentificaLink=str_replace('===',";",$NOIdentificaLink);  //Cambio otros caracteres
	$NOIdentificaLink=str_replace('=*=',"?",$NOIdentificaLink);  //Cambio otros caracteres


	// Para pasar como parametro el &, reemplazarlo con ***
	//Si es numerico este codigo indica el numero que reemplaza luego %%%


	echo "\nPagina:".$Pagina;
	echo "\nDominio:".$Dominio;
	echo "\nRecursivo:".$Recursivo;
	echo "\nCorta:".$Corta;
	echo "\nPrimera:".$Primera;
	echo "\nIdentificaArticulo:".$IdentificaArticulo;
	echo "\nIdentificaLink:".$IdentificaLink;
	echo "\nNOIdentificaLink:".$NOIdentificaLink;

	switch ($Recursivo) {
	case "BR":
		//Es recursivo comun pero con base en vez de ram
		if ($Dominio=="0"){$Dominio="";} //Si el dominio tiene un 0 significa que dentro de la pagina los links vienen con el dominio...

		AgregoLinkBD($Pagina,$Primera,"Primera");
		getarticulosBR("http://".$Dominio.$Primera, $IdentificaArticulo, $IdentificaLink, $NOIdentificaLink, $Pagina);  //Inicia el crawler con el primer link
	    break;
	}

	echo "\n\n\nTERMINADOOOOO";  //si termina bien todo el proceso
		
}	


function getarticulosBR($pagina, $IdentificaArticulo, $IdentificaLink, $NOIdentificaLink, $Pagina) {
    global $articulos,$linkList,$Dominio,$Corta;
	
	$pagina=str_replace('&amp;',"&",$pagina);  //Pagina para abrir ahora
	$pagina=str_replace(' ',"%20",$pagina);  //Reemplazando caracteres del toor
    
	echo "\n>>>> ABRIENDO: ".$pagina;

	if ($Corta<=0){
	
		$html = new simple_html_dom();
		$html=file_get_html($pagina);
		if($html && is_object($html) && isset($html->nodes)){
			
			
			foreach($html->find('a') as $items){
				
				if (strpos($items->href, $IdentificaArticulo)!== false){
					$LinkNuevo=str_replace('http://www.',"",$items->href);
					$LinkNuevo=str_replace('http://',"",$LinkNuevo);
					$LinkNuevo=str_replace('https://www.',"",$LinkNuevo);
					$LinkNuevo=str_replace('https://',"",$LinkNuevo);
					$LinkNuevo=str_replace($Dominio,"",$LinkNuevo);
					$LinkNuevo=str_replace("//","/",$LinkNuevo);  //cosa extraña que pasa, se multiplican los /
					
					//echo "/n articulo ".$items->href;

					//Me pasa en uno que tengo que quitar esta parte porque si no no termina mas...
					if (strpos($items->href, "&osCsid=")!== false){
						$posicion=strpos($LinkNuevo, "&osCsid=");
						$sacandolo=$posicion-strlen($LinkNuevo);
						$LinkNuevo=substr ($LinkNuevo,0, $sacandolo);; //Toma solo desde el principio a donde esta &osCsid=
					}
					
					if (EstaLinkBD($Pagina,$LinkNuevo)<>"NoEsta"){
						echo "\nNO SERVIS $LinkNuevo <<<";
					} else {
						AgregoLinkBD($Pagina,$LinkNuevo,$pagina);
						echo "\nArticulo a Crawlear: ". $LinkNuevo;
						
						ActualizaArticulos($Pagina,$LinkNuevo,$pagina);

						if ($Corta!=0){$Corta=$Corta-1;} //Corta apenas llega a la cantidad que se buscaba de notas
						
						//print_r ($articulos);
					}
				}
			}
			
			$loli=0;
			foreach($html->find('a') as $items){
				if (strpos($items->href, $IdentificaLink)!== false){
					$DominioSinWWW=str_replace('http://www.',"",$Dominio);
					$DominioSinWWW=str_replace('http://',"",$DominioSinWWW);
					$DominioSinWWW=str_replace('https://www.',"",$DominioSinWWW);
					$DominioSinWWW=str_replace('https://',"",$DominioSinWWW);
					$DominioSinWWW=str_replace('www.',"",$DominioSinWWW);
					$LinkNuevo=str_replace('http://www.',"",$items->href);
					$LinkNuevo=str_replace('http://',"",$LinkNuevo);
					$LinkNuevo=str_replace('https://www.',"",$LinkNuevo);
					$LinkNuevo=str_replace('https://',"",$LinkNuevo);
					$LinkNuevo=str_replace($DominioSinWWW,"",$LinkNuevo);
					$LinkNuevo=str_replace("//","/",$LinkNuevo);  //cosa extraña que pasa, se multiplican los /

					$LinkNuevo=str_replace('../',"",$LinkNuevo); //VERLOO
					
					//Esto es para casos rarisimos.. puta que lo pario
					//if (strpos($LinkNuevo, "tienda/")== false){ //si no esta la palabra la agrego
					//	$LinkNuevo="tienda/".$LinkNuevo;
					//}
					
					if (EstaLinkBD($Pagina,$LinkNuevo)<>"NoEsta" || (strpos($LinkNuevo, $NOIdentificaLink)!== false)) {
						echo "\nNO SIRVE $LinkNuevo <<<";
					} else {
						AgregoLinkBD($Pagina,$LinkNuevo,$pagina);
						echo "\nAbriendo $LinkNuevo <<<";
						
						# Limpio la puta memoria (puta madre)
						if (isset($html)) {
							$html->clear();
							unset($html);
						}
						
						//print_r ($linkList);

						$LinkNuevo=trim($LinkNuevo);
						
						sleep (6); //Espera 6 segundos antes de pasar a la proxima, si no se va todo al carajo....
						getarticulosBR("http://".$Dominio.$LinkNuevo, $IdentificaArticulo, $IdentificaLink, $NOIdentificaLink, $Pagina);
					}
				}
			}
		}
	}
}


function AgregoLinkBD($pagina,$linknuevo,$linkdesde){
	$linknuevo=trim($linknuevo);
	//Guardo en la tabla los links a ser crawleados despues
	$query_paginas = "INSERT crawl_links SET Pagina='$pagina',Link='$linknuevo',DeDonde='$linkdesde',FechaObtenido=NOW()";
	$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('La consulta fall&oacute;: ' . mysql_error());
	unset($resultado);
	$resultado = null;
}


function EstaLinkBD($pagina,$linknuevo){
	$linknuevo=trim($linknuevo);
	//Armo el select, para ver si ya lo tenia
	$Linkazo = "NoEsta"; //Si tiene un kkk significa que no lo tengo
	$query_abajo= "SELECT Link FROM crawl_links WHERE Link='".$linknuevo."' AND Pagina='".$pagina."' LIMIT 1";  // Comparo el titulo con el resto de la misma pagina
	$resultado = mysql_query($query_abajo, $GLOBALS['publica']) or die('ComparaLink1La consulta fallo: ' . mysql_error());
	if ($resultado) {
		$Linkazo = mysql_result($resultado,0,"Link");
	}
	if ($Linkazo==""){$Linkazo="NoEsta";}	
	mysql_free_result($resultado);
	unset($resultado);
	$resultado = null;	
	return $Linkazo;
}

function ActualizaArticulos($pagina,$linknuevo,$linkdesde){
	$linknuevo=trim($linknuevo);
	$linknuevo=str_replace('&amp;',"&",$linknuevo);  //Pagina para abrir ahora
	$linknuevo=str_replace(' ',"%20",$linknuevo);  //Reemplazando caracteres del toor
	$linknuevo=str_replace('"',"",$linknuevo);  //Reemplazando caracteres del toor
	$linknuevo=str_replace("'","",$linknuevo);  //Reemplazando caracteres del toor
	//Guardo en la tabla los links a ser crawleados despues
	$query_paginas = "INSERT crawl_articulos SET Pagina='$pagina',Link='$linknuevo',DeDonde='$linkdesde',FechaObtenido=NOW()";
	$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('La consulta fall&oacute;: ' . mysql_error());
	unset($resultado);
	$resultado = null;
}
?>
