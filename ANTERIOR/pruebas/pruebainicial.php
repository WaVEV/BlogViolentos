<?
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');

//busco un texto, y analizo el resultado
$URLDondeBusca="http://www.mystilus.com/MorphosyntacticAnalyzer";

$DatosPublicar = "";
$DatosPublicar['text'] = 'El turismo alternativo se ha convertido en una forma de vacacionar muy atractiva.';
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

echo "<br><br><br>////////////////////LO QUE LEVANTA ///////////////////////<br><br>";

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

	if(stripos($significado[$i], "nombre, común") !== false){
		echo " ----- SUSTANTIVO ----";
		$sinonimos=buscasinonimo ($palabra[$i]);
		echo $sinonimos;
		$paraazar=explode(',', $sinonimos);
		$palabra[$i]=$paraazar[array_rand($paraazar, 1)];
		echo " --- seleccionado:".$palabra[$i];
	}

	if(stripos($significado[$i], "adjetivo, en grado positivo") !== false){
		echo " ----- ADJETIVO ----";
		$sinonimos=buscasinonimo ($palabra[$i]);
		echo $sinonimos;
		$paraazar=explode(',', $sinonimos);
		$palabra[$i]=$paraazar[array_rand($paraazar, 1)];
		echo " --- seleccionado:".$palabra[$i];
	}

	if(stripos($significado[$i], "verbo") !== false){
		echo " ----- VERBO ----";
		//busco el verbo en infinitivo para buscar el sinonimo
		$infinitivo=explode('Lema:', $significado[$i]);

		$sinonimos=buscasinonimo (trim(strip_tags($infinitivo[1])));

		echo $sinonimos;
		$paraazar=explode(',', $sinonimos);
		$palabra[$i]=$paraazar[array_rand($paraazar, 1)];
		echo " --- seleccionado:".$palabra[$i];

		//Ya tengo el sinonimo seleccionado ahroa lo debo conjugar al mismo tiempo verbal y genero
		$palabra[$i]=conjugador($palabra[$i],$significado[$i]);
		echo " --- conjugado:".$palabra[$i];
	}


    echo "<br>";
}

//Limpio la memoria
$html->clear(); 
unset($html);
$analisis->clear(); 
unset($analisis);

echo "<br><br><br><br>//////////ORIGINAL///////////////<br><br>";
echo "Texto para analizar: ".$DatosPublicar['text'] ;

echo "<br><br><br><br>//////////RESULTADO FINAL///////////////<br><br>";

for ($i = 1; $i<=$cantpalabras; $i++) {
	echo $palabra[$i]." ";
}


function buscasinonimo($txt){
	$urlss="http://www.wordreference.com/sinonimos/".$txt;
	$html2=file_get_html($urlss);
	foreach ($html2->find('div[class=trans clickable]') as $e) {
		$sinonimos=explode('<li>',$e->innertext);
		$txt=strip_tags($sinonimos[1]);
		break; //tomo solo la primer lista que sale y salgo
	}

	//Limpio la memoria
	$html2->clear(); 
	unset($html2);
    return $txt;
}

function conjugador($txt,$significado){
	//conjuga un verbo, y lo devuelve
	$URLDondeBusca="http://www.mystilus.com/VerbConjugator";

	$DatosPublicar = "";
	$DatosPublicar['text'] = $txt;
	$DatosPublicar['clang'] = 'es';
	$DatosPublicar['check'] = 'Conjugar';

	//Hace el post con todos los datos y obtiene el resultado
	//$Request = new HttpRequest($URLDondeBusca, $DatosPublicar, "", $UsaProxy, $UsaPort);
	$Request = new HttpRequest($URLDondeBusca, $DatosPublicar);
	$Retorno = $Request->Retorno;
	$Devuelto = $Request->Devuelto;

	//html en base a lo que levanto arriba
	$html2=str_get_html($Retorno);//creo el objeto dom en base a lo que trae curl

	unset($Request);  //Destruyo el objeto curl


	foreach ($html2->find('div[class=trans clickable]') as $e) {
		$sinonimos=explode('<li>',$e->innertext);
		$txt=strip_tags($sinonimos[1]);
		break; //tomo solo la primer lista que sale y salgo
	}

	//Limpio la memoria
	$html2->clear(); 
	unset($html2);
	$txt="ha convertido";
    return $txt;
}
?>



