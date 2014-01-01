<?php
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');
$DirectorioListos="/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/listos/";


/*

ACA ESTAN LAS FUNCIONES

*/


function Arreglar($string){
	include('caractereschotines.php');
	$string=str_ireplace($search2,$replace2,$string);
	$string=str_replace('á','a',$string);
	$string=str_replace('é','e',$string);
	$string=str_replace('í','i',$string);
	$string=str_replace('ó','o',$string);
	$string=str_replace('ú','u',$string);
	$string=str_replace("'",'"',$string);
	$string=str_replace('“','"',$string);
	$string=str_replace('”','"',$string);
	//$string=str_replace('','',$string);
	return $string;
}
function PosicionBR($texto){
	$z=explode('<br>',$texto);
	$posicion=array();
	foreach($z as $elem){
		$cont=0;
		$palabra=array();
		$palabra=explode(' ',$elem);
		foreach($palabra as $e){ // exploro palabra por palabra, asi solo tomo la posicion de una palabra mayor a 0 caracteres
			if(strlen($e)>0){
				$cont++;
			}
		}
		$posicion[]=$cont;
	}
	return $posicion;
}

function ColocarBR($texto,$posiciones){
	$textofinal='';
	$x=explode(' ',$texto);
	$cont=0; // Cuanta las palabras
	$i=0; // es el indice de $posiciones
	foreach($x as $elem){
		if($cont==$posiciones[$i]){
			$elem=$elem.'<br>';
			$cont=0;
			$i++;
		}else{
			$cont++;
			$elem.=' ';
		}
		$textofinal.=$elem;
	}
	return $textofinal;
}
function TipoCaracter($string){
	$letras='abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	if(	strpos($letras,$string)!==false ){
		$ok=1;
	}else{
		$ok=0;
	}
	return $ok;
}


echo TipoCaracter('a');
echo '<br><br>';





/* ------------------------------------------------------------------------------

PASO 1:
		Tomo un articulo aleatorio de la base para transformarlo

------------------------------------------------------------------------------ */




// Conecto a la base de datos para tomar las palabras importantes
mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']);

// Busco una nota cualquiera
$query = "SELECT * FROM Notas limit 1,2";
$notas = mysql_query($query, $GLOBALS['publica']) or die('Palabras - La consulta fallo: ' . mysql_error());
$Hay = mysql_num_rows($notas);
if($Hay){
	echo "<b>".$query."</b><br>";
}else{
	echo '<b>No hay notas por procesar</b><br>';
	die();
}

$row = mysql_fetch_array($notas);
$TituloOriginal=$row['TituloOriginal'];
$TextoOriginal=$row['TextoOriginal'];

echo $TextoOriginal;




echo '<br><br>';




/* ------------------------------------------------------------------------------

PASO 2:
		Tomo las palabras claves y luego las que estan entre comillas. A los espacios de dichas palabras los reemplazo por *** para que tome solo una palabra, por ejemplo, "Villa General Belgrano" son 3 palabras, en cambio, "Villa***General***Belgrano" es una sola palabra.
		A cada palabra clave le pongo <este-no>...</este-no> para identificar que palabra NO HAY que modificar.

------------------------------------------------------------------------------ */


$query = "SELECT * FROM PalabrasClaves";
$palabras = mysql_query($query, $GLOBALS['publica']) or die('Palabras - La consulta fallo: ' . mysql_error());
$Hay = mysql_num_rows($palabras);
if($Hay){
	echo "<b>".$query."</b><br>";
}else{
	echo '<b>No hay palabras por procesar</b><br>';
	die();
}

$todas_juntas='';
$i=0;
while($row = mysql_fetch_array($palabras)){
	if($i==0){
		$todas_juntas=$row['palabra'];
	}else{
		$todas_juntas.=','.$row['palabra'];
	}
	$i++;
}
echo $todas_juntas;


echo '<br><br>';



$TextoArreglado=sacar_tags($TextoOriginal);
$TextoArreglado=Arreglar($TextoArreglado);

// exploro las palabras
$x=explode(',',$todas_juntas);
foreach($x as $elem){
	$TextoArreglado=str_replace($elem,'<este-no>'.str_replace(' ','***',$elem).'</este-no>',$TextoArreglado);  // El espacio lo reemplazo por *** ya que luego, como divido las palabras por espacio, por ejemplo, "Villa General Belgrano" me va a escanear y reemplazar "General"
}

// Divido el texto por las comillas
$z=explode('"',$TextoArreglado);

// Los pares desde el 0 es texto
// Los impares desde el 1 es lo que esta entre comilla

$TextoCasiListo='';
$cont=0;
foreach($z as $elem){
	if( ($cont%2)!=0 ){
		$elem='<este-no>"'.str_replace(' ','***',$elem).'"</este-no>';     // El espacio lo reemplazo por *** ya que luego, como divido las palabras por espacio, por ejemplo, "Villa General Belgrano" me va a escanear y reemplazar "General"
	}
	$cont++;
	$TextoCasiListo.=$elem.' ';
}

echo '<h1>Arreglo el texto</h1>';
echo $TextoCasiListo;




echo '<br><br>';




/* ------------------------------------------------------------------------------

PASO 3:
		Tomo la cantidad de palabras que hay antes de un <br> y los almaceno en un array

------------------------------------------------------------------------------ */




// $TextoCasiListo esta todo el texto limpio con <br> y <este-no>. Lo que hago ahora es buscar la posicion de los brs segun la cantidad de palabras, para luego, volver a colocarlos

$posiciones=PosicionBR($TextoCasiListo);

foreach($posiciones as $e){
	echo 'Pos: '.$e.'<br>';
}
echo '<br><br>';

//$TextoAnalizar=strip_tags($TextoCasiListo,'<este-no>');
$TextoAnalizar=str_replace('<br>',' ',$TextoCasiListo);

/*$TextoAnalizar=str_replace('.',' . ',$TextoAnalizar);
$TextoAnalizar=str_replace('  ',' ',$TextoAnalizar);
$TextoAnalizar=str_replace(' .','.',$TextoAnalizar);*/






/* ------------------------------------------------------------------------------

PASO 4:
		Saco los <br>, pongo todo junto, y divido en oraciones de menos de 20 palabras.

------------------------------------------------------------------------------ */





// En vez de analizar palabra por palabra, envio una oracion de menos de 20 palabras (asi es mas rapido y no saturamos la web)
$cont=1;
$pos=0;
$oraciones=array();
$t=explode(' ',$TextoAnalizar);
foreach($t as $elem){
	if($cont<20){
		$oraciones[$pos].=$elem.' ';
		$cont++;
	}else{
		$oraciones[$pos].=$elem.' ';
		$cont=1;
		$pos++;
	}
}

echo '<br><br>';

foreach($oraciones as $e){
	echo 'Oracion: '.$e.'<br>';
}





/* ------------------------------------------------------------------------------

PASO 5:
		Aca es la magia, le paso cada oracion para ser analizada y transformada (es mejor pasar por oraciones que por palabras, asi se hace mas rapido).

------------------------------------------------------------------------------ */



// METO TODO EL ANALIZADOR PORQUE POR FUNCION NO ANDA NO SE PORQUE MIERDA

$TextoFinal='';
foreach($oraciones as $elem){

	// "este-no" es una etiqueta falsa para identificar las palabras que no hay que tocar, que luego se marcan con color para identificarlas visualmente
	if( strpos($elem,'este-no>')===false ){


		//busco un texto, y analizo el resultado
		$URLDondeBusca="http://www.mystilus.com/MorphosyntacticAnalyzer";
		
		$DatosPublicar = "";
		$DatosPublicar['text'] = $elem;
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
		
		//echo "<br><br><br><br>//////////ORIGINAL///////////////<br><br>";
		//echo "Texto para analizar: ".$DatosPublicar['text'] ;
		
		//echo "<br><br><br><br>//////////RESULTADO FINAL///////////////<br><br>";
		
		for ($i = 1; $i<=$cantpalabras; $i++) {
			$ok=TipoCaracter($palabra[$i]);
			if($ok){
				$TextoFinal.=$palabra[$i];
			}else{
				$TextoFinal.=' '.$palabra[$i];
			}
		}

	}else{
		if($ok){
			$TextoFinal.=$elem;
		}else{
			$TextoFinal.=' '.$elem;
		}
	}
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





/* ------------------------------------------------------------------------------

PASO 6:
		Con una function le coloco los <br>, limpio un poco la basura que queda y LISTO.

------------------------------------------------------------------------------ */

$TextoFinal=ColocarBR($TextoFinal,$posiciones);

/*
$TextoFinal=str_replace('.',' . ',$TextoFinal);
$TextoFinal=str_replace('  ',' ',$TextoFinal);
$TextoFinal=str_replace(' .','.',$TextoFinal);

$TextoFinal=str_replace('<este-no>','<span style="color:red">',$TextoFinal);
$TextoFinal=str_replace('</este-no>','</span>',$TextoFinal);
$TextoFinal=str_replace('***',' ',$TextoFinal);
*/



echo '<h1>Texto Convertido</h1>';
echo $TextoFinal;









?>