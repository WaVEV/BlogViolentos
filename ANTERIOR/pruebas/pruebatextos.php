<?
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/clases.php');


//Ejemplo de como vendria el texto
$TextoOriginal='Dando comienzo a la temporada de verano, Villa General Belgrano recibió ayer el Año Nuevo, con el tradicional espectáculo de Fuegos Artificiales al pie de las Sierras Chicas, sobre la Ruta 5. El evento fue organizado por la Comisión Directiva y Cuerpo de Bomberos Voluntarios de Villa General Belgrano, con el apoyo de la Municipalidad.';

echo "<br>ORIGINAL:".$TextoOriginal."<br><br><br>";


$encontradas='';
$bd=new MySQL();
$query = "SELECT * FROM PalabrasClaves";
$consulta = $bd->Consulta($query);
$Hay = $bd->num_rows($consulta);
if($Hay){ // si hay sigo
	while($row = $bd->fetch_array($consulta)){
		$TextoOriginal=str_replace($row['palabra'],'<clave>'.$row['palabra'].'</clave>',$TextoOriginal);
	}
}

$dom = new simple_html_dom();
$dom->load($TextoOriginal);

foreach($dom->find('clave') as $e){
	$encontradas.=$e->innertext.' ';
}

echo "INICIO<br><br><br><br>";

$texto=new Palabras();

$parrafo=$texto->Parrafos($TextoOriginal);
//$parrafo[0]='Dando comienzo a la temporada de verano';
//$parrafo[1]='Villa General Belgrano recibió ayer el Año Nuevo';

foreach($parrafo as $i => $elem){
	echo '<br><h3>Parrafo '.$i.'</h3>';
	analizador($elem); //jojo una maravilla
}

$texto->PalabrasClaves($encontradas);

//include que llama al script de las imagenes y lo corre de una, devuelve $imagenesnota
//include($DirectorioListos."imagenes_google.php");


echo "RESULTADO<br><br><br><br>";

$matriz=$texto->Listo();

//print_r($matriz);

$total=count($matriz);
for($i=0; $i<$total; $i++){
	if ($matriz[$i]['tipo']=="Sustantivo") {
		$sinonimos=buscasinonimo ($matriz[$i]['palabra']);
		$paraazar=explode(',', $sinonimos);
		$texto->Cambiar($i,$paraazar[array_rand($paraazar, 1)]);
	}
}


//Lo cargo de nuevo si no no toma los cambios
$matriz=$texto->Listo();


$texto->Mostrar();


//llena los datos de las palabras, para despues poder procesarlos
function analizador($oracion){

	global $texto;

	//busco un texto, y analizo el resultado
	$URLDondeBusca="http://www.mystilus.com/MorphosyntacticAnalyzer";
	
	$DatosPublicar = "";
	$DatosPublicar['text'] = $oracion;
	$DatosPublicar['clang'] = 'es';
	$DatosPublicar['analyze'] = 'Analizar';
	
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
		$analisis=$e->innertext;
		//ya tengo el analisis completo en la variable, ahora lo desmenuzo
		if ($analisis<>"") break;
	}
	
	if ($analisis<>"") {
		$analisis=str_get_html($analisis); //creo un objeto dom del analisis asi es mas facil
		$cantpalabras=0;
		//esto separa cada palabra
		foreach ($analisis->find('tr') as $e) {
			$cantpalabras=$cantpalabras+1;
			$contenido=strip_tags($e->innertext,"<strong><td>");
			$contenido=str_replace('</td>','',$contenido);
			$contenido=str_replace('</strong>','',$contenido);
			$explotado=explode('<td>',$contenido);
			//aca ya tengo cada palabra y su significado separados (el primero es la palabra el segundo el significado)
			$palabra[$cantpalabras]=trim(strip_tags($explotado[0]));
			$primersignificado=explode('<strong>',$explotado[1]);
			$significado[$cantpalabras]=trim(strip_tags($primersignificado[1]));
		}
	}
	
	for ($i = 1; $i<=$cantpalabras; $i++) {
		echo "Palabra.".$i."= ".$palabra[$i]." --- ";
		echo "Significado.".$i."= ".$significado[$i];
	
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
	
	//Limpio la memoria
	$html->clear(); 
	unset($html);
	$analisis->clear(); 
	unset($analisis);
}


function buscasinonimo($txt){/*
	$urlss="http://www.wordreference.com/sinonimos/".$txt;
	$html2=file_get_html($urlss);
	foreach ($html2->find('div[class=trans clickable]') as $e) {
		$sinonimos=explode('<li>',$e->innertext);
		$txt=strip_tags($sinonimos[1]);
		break; //tomo solo la primer lista que sale y salgo
	}

	//Limpio la memoria
	$html2->clear(); 
	unset($html2);*/
	return $txt;
}

?>