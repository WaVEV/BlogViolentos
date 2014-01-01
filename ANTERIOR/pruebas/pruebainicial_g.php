<?
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesviolentas.php');


/******************************

	CONFIGURACION	
			  

******************************/

//busco un texto, y analizo el resultado
$URLDondeBusca="http://www.mystilus.com/MorphosyntacticAnalyzer";

// Array que contiene el texto dividido en partes y convertidos, $Dividido[0][x] se almacena la oracion original y $Dividido[1][x] la oracion convertida
$Dividido=array();

// El texto a convertir
$original='El taller cuenta con la participación de niños, jóvenes y adultos, que ejecutan instrumentos musicales o cantan. Se tratará de un encuentro  en el que participarán grupos representantes de academias musicales de Río Tercero y localidades vecinas.';

// Funcion llamada BuscaSinonimos();
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/sinonimos_sinonimos.org.php');




/*------------------------------------------------------------------------------------------*/






echo '<br>//////// DIVIDO EL TEXTO EN ORACIONES CORTAS /////////<br>';

// Divido el texto para poder procesarlo
$Dividido=Dividir($original);

// Una vez dividido exploro el array y lo voy convirtiendo
$cont=0;
$n=1;
foreach($Dividido[0] as $oracion){

	$listo='';

	$DatosPublicar = array();
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
	if($cont==0){
		echo "<br><br><br>////////////////////LO QUE LEVANTA ///////////////////////<br><br>";
	}
	
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
			$palabra[$cantpalabras]=strip_tags($explotado[0]);
			$primersignificado=explode('<strong>',$explotado[1]);
			$significado[$cantpalabras]=strip_tags($primersignificado[1]);
		}
	}
	
	for ($i = 1; $i<=$cantpalabras; $i++) {
		echo "Palabra.".$n."= ".$palabra[$i]." --- ";
		echo "Significado.".$n."= ".$significado[$i];
		$n++;
	
		if(stripos($significado[$i], "nombre, común") !== false){
			echo " ----- SUSTANTIVO ----";
			$sinonimos=BuscaSinonimo ($palabra[$i]);
			echo $sinonimos;
			$paraazar=explode(',', $sinonimos);
			$palabra[$i]=$paraazar[array_rand($paraazar, 1)];
			echo " --- seleccionado:".$palabra[$i];
		}
	
		if(stripos($significado[$i], "adjetivo, en grado positivo") !== false){
			echo " ----- ADJETIVO ----";
			$sinonimos=BuscaSinonimo ($palabra[$i]);
			echo $sinonimos;
			$paraazar=explode(',', $sinonimos);
			$palabra[$i]=$paraazar[array_rand($paraazar, 1)];
			echo " --- seleccionado:".$palabra[$i];
		}
	
		echo "<br>";
	}
	
	//Limpio la memoria
	$html->clear(); 
	unset($html);
	$analisis->clear(); 
	unset($analisis);
	
	for ($i = 1; $i<=$cantpalabras; $i++) {
		$listo.=$palabra[$i]." ";
	}
	
	$Dividido[1][$cont]=$listo;
	
	$cont++;
	
} // FIN foreach que divide el texto en oraciones


// Rearmo el resultado
foreach($Dividido[1] as $z){
	$fin.=$z;
}
foreach($Dividido[0] as $v){
	$ori.=$v;
}
	
echo "<br><br><br><br>//////////ORIGINAL///////////////<br><br>";
echo "Texto para analizar: ".$original;

echo "<br><br><br><br>//////////RESULTADO FINAL///////////////<br><br>";
echo $fin;




?>