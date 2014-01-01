<?

//llena los datos de las palabras, para despues poder procesarlos
function Analizador($oracion){

	global $texto;

	//busco un texto, y analizo el resultado
	$URLDondeBusca="http://www.mystilus.com/Analizador_morfosintactico";
	
	$DatosPublicar = "";
	$DatosPublicar['text'] = $oracion;
	$DatosPublicar['clang'] = 'es';
	$DatosPublicar['analyze'] = 'analyze';
	
	//Hace el post con todos los datos y obtiene el resultado
	//$Request = new HttpRequest($URLDondeBusca, $DatosPublicar, "", $UsaProxy, $UsaPort);
	$Request = new HttpRequest($URLDondeBusca, $DatosPublicar);
	$Retorno = $Request->Retorno;
	$Devuelto = $Request->Devuelto;
	
	//html en base a lo que levanto arriba
	$html=str_get_html($Retorno);//creo el objeto dom en base a lo que trae curl
	
	unset($Request);  //Destruyo el objeto curl
	
	//echo $html;
	
	//echo "<br><br><br>////////////////////LO QUE LEVANTA ///////////////////////<br><br>";
	
	$analisis="";
	foreach ($html->find('div[id=analysis]') as $e) {
		// Creo un div con class para dividir cada palabra y contenido, ya que la web no lo hace
		$analisis=str_replace("<span class='titulo'>","</div><div class='divido_cada_palabra'><span class='titulo'>",$e->innertext);
		$analisis='<div class="divido_cada_palabra">'.$analisis;
		$analisis=$analisis.'</div>';
		$analisis=str_replace('  				','',$analisis);
		
		//ya tengo el analisis completo en la variable, ahora lo desmenuzo
		if ($analisis!=="") break;
	}
	
	if ($analisis!=="") {
		$analisis=str_get_html($analisis); //creo un objeto dom del analisis asi es mas facil
		$cantpalabras=0;
		
		if(strpos($analisis,'Para realizar revisiones de más de 20 palabras')!==false){
			echo 'Para realizar revisiones de más de 20 palabras, regístrese gratis.';
		}else{
		
			//echo $analisis;
		
			//esto separa cada palabra
			foreach ($analisis->find('div[class=divido_cada_palabra]') as $e) {

				foreach ($e->find('span[class=titulo]') as $ee) {
					$palabra[$cantpalabras]=$ee->innertext;
				}
				
				foreach ($e->find('strong') as $ee) {
					$significado[$cantpalabras]=$ee->innertext;
					break;
				}

				foreach ($e->find('ul') as $ee) {
					if(stripos($significado[$cantpalabras], "verbo") !== false){
						$significado[$cantpalabras]=$significado[$cantpalabras]." ".trim(strip_tags($ee));
						break;
					}
				}			

				$cantpalabras++;
				
				/*$contenido=strip_tags($e->innertext,"<strong><td>");
				$contenido=str_replace('</td>','',$contenido);
				$contenido=str_replace('</strong>','',$contenido);
				$explotado=explode('<td>',$contenido);
				//aca ya tengo cada palabra y su significado separados (el primero es la palabra el segundo el significado)
				$palabra[$cantpalabras]=trim(strip_tags($explotado[0]));
				$primersignificado=explode('<strong>',$explotado[1]);
				$significado[$cantpalabras]=trim(strip_tags($primersignificado[1]));*/
			}
		}
	}
	
	for ($i = 1; $i<$cantpalabras; $i++) {
		echo "Palabra.".$i."= ".$palabra[$i]." --- ";
		echo "Significado.".$i."= ".$significado[$i];
		$tiempo_verbal[$i]=null;

		//Tipo de palabra
		if((stripos($significado[$i], "nombre, común") !== false) || (stripos($significado[$i], "nombre, propio") !== false)){
			$tipo[$i]="Sustantivo";
		}
		if((stripos($significado[$i], "adjetivo, en grado positivo") !== false) || (stripos($significado[$i], "adjetivo, en grado comparativo") !== false)){
			$tipo[$i]="Adjetivo";
		}
		if(stripos($significado[$i], "verbo") !== false){
			$tipo[$i]="Verbo";
			$tiempo_verbal[$i]=$significado[$i];
		}
		if(stripos($significado[$i], "adverbio") !== false){
			$tipo[$i]="Adverbio";
		}		
		if((stripos($significado[$i], "preposición") !== false)){
			$tipo[$i]="Preposicion";
		}
		if((stripos($significado[$i], "conjunción") !== false)){
			$tipo[$i]="Conjuncion";
		}
		if((stripos($significado[$i], "cuantificador") !== false)){
			$tipo[$i]="Cuantificador";
		}		
		if((stripos($significado[$i], "contracción") !== false)){
			$tipo[$i]="Contraccion";
		}			
		if(stripos($significado[$i], "artículo, determinante") !== false){
			$tipo[$i]="Articulo";
		}
		if(stripos($significado[$i], "posesivo, determinante") !== false){
			$tipo[$i]="Posesivo";
		}
		if(stripos($significado[$i], "numeral") !== false){
			$tipo[$i]="Numeral";
		}
		if(stripos($significado[$i], "personal, pronominal") !== false){
			$tipo[$i]="Personal";
		}		
		if(stripos($significado[$i], "puntuación") !== false){
			$tipo[$i]="Puntuacion";
		}		



		//Singular Plural
		if(stripos($significado[$i], "singular") !== false){
			$estado[$i]="Singular";
		}
		if(stripos($significado[$i], "plural") !== false){
			$estado[$i]="Plural";
		}

		//Genero
		if(stripos($significado[$i], "masculino") !== false){
			$genero[$i]="Masculino";
		}
		if(stripos($significado[$i], "femenino") !== false){
			$genero[$i]="Femenino";
		}
			

		$texto->Almacenar($palabra[$i],1,$tipo[$i],$estado[$i],$genero[$i],$tiempo_verbal[$i],null);
		
		echo "<br>";
	}
	if (count($palabra)>1){
		if($palabra[$cantpalabras]=='.'){
				$texto->Almacenar('<br>',0,'-','-','-','-',null);
		}
	}
	//Limpio la memoria
	$html->clear(); 
	unset($html);
	$analisis->clear(); 
	unset($analisis);
		
}


?>



