<?
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/clases.php');


//Ejemplo de como vendria el texto
$TextoOriginal='<p>Dando comienzo a la temporada de verano, <strong><em>Villa General Belgrano</em></strong> recibió ayer el <strong><em>Año Nuevo</em></strong>, con el tradicional espectáculo de <strong><em>Fuegos Artificiales</em></strong> al pie de las Sierras Chicas, sobre la Ruta 5.</p>
<p><span id="more-2774"></span></p>
<p>El evento fue organizado por la <strong><em>Comisión Directiva y Cuerpo de Bomberos Voluntarios de Villa General Belgrano</em></strong>, con el apoyo de la <strong><em>Municipalidad</em></strong>.</p>
<p>Gran convocatoria tuvo la procesión de luminarias que dio comienzo a la celebración. Según estimaron desde el Cuartel de Bomberos, más de <strong><em>300 personas</em></strong> participaron del <strong><em>descenso con antorchas</em></strong> desde el Cerro de la Virgen.</p>
<p>Más tarde, a las <strong><em>22:49hs</em></strong> comenzó el lanzamiento de <strong><em>Fuegos Artificiales</em></strong>, que se extendió durante 13 minutos consecutivos, ante aproximadamente <strong><em>3.000 personas </em></strong>que los observaron desde la ruta.</p>
<p>Finalmente, sobre las sierras se observó el cartel luminoso con la leyenda “<strong><em>Feliz 2013</em></strong>”, para de esta forma dar formalmente la bienvenida al nuevo año.</p>';

echo "<br>ORIGINAL:".$TextoOriginal."<br><br><br>";


echo "INICIO<br><br><br><br>";

$texto=new Palabras();

$TextoOriginal=$texto->Limpiar($TextoOriginal);

// Busco las palabras claves
$encontradas=$texto->ParalabrasClavesBD($TextoOriginal);

echo "PALABRAS CLAVES ENCONTRADAS: ".$encontradas."<br><br><br><br>";




// Una vez que tengo las palabras claves, elimino todas las claves
$TextoOriginal=strip_tags($TextoOriginal,'<br>');

$parrafo=$texto->Parrafos($TextoOriginal);

echo "TEXTO ARREGLADO: ".$TextoOriginal."<br><br><br><br>";

foreach($parrafo as $i => $elem){
	echo '<br><b>Parrafo '.$i.' (longitud: '.strlen($elem).'):</b> '.$elem;
}

//cargo la funcion que le toca para la sintaxis
include($DirectorioListos."sintaxis_mystilus.php");


foreach($parrafo as $i => $elem){
	echo '<br><h3>Parrafo '.$i.'</h3>';
	Analizador($elem); //jojo una maravilla
}

// Esto le pone 0 a la columna "publicar" a las palabras claves
$texto->MarcarPalabrasClaves($encontradas);





echo "RESULTADO<br><br><br><br>";


//print_r($matriz);


//Lo cargo de nuevo si no no toma los cambios
$matriz=$texto->Listo();
//$texto->Mostrar();


//cargo la funcion que le toca para la sinonimos
include($DirectorioListos."sinonimos_wordreference.php");

$total=count($matriz);
for($i=0; $i<$total; $i++){
	if ($matriz[$i]['tipo']=="Sustantivo" && $matriz[$i]['publicar']) {
		$sinonimos=BuscaSinonimo($matriz[$i]['palabra']);
		$paraazar=explode(',', $sinonimos);
		$texto->Cambiar($i,$paraazar[array_rand($paraazar, 1)]);
	}
}


//Lo cargo de nuevo si no no toma los cambios
$matriz=$texto->Listo();
//$texto->Mostrar();



//cargo la funcion que le toca para la verbos
include($DirectorioListos."verbos_mystilus.php");

$total=count($matriz);
for($i=0; $i<$total; $i++){
	if ($matriz[$i]['tipo']=="Verbo" && $matriz[$i]['publicar']) {


		//busco el verbo en infinitivo para buscar el sinonimo
		$infinitivo=explode('Lema:',$matriz[$i]['tiempo_verbal']);

		$sinonimos=BuscaSinonimo(trim(strip_tags($infinitivo[1])));
		$paraazar=explode(',', $sinonimos);
		$verboconjugado=$paraazar[array_rand($paraazar, 1)];
		//echo "<br><br>SIN CONJUGAR: ".$verboconjugado;

		//Ya tengo el sinonimo seleccionado ahroa lo debo conjugar al mismo tiempo verbal y genero
		$parametroverbo=CreaParametroVerbo($matriz[$i]['tiempo_verbal']);
		//echo "<br><br>Tipo:".$parametroverbo[0]." - Tiempo:".$parametroverbo[1]." - Persona:".$parametroverbo[2]."<br><br>";
		$verboconjugado=Conjugador(trim($verboconjugado),$parametroverbo[0],$parametroverbo[1],$parametroverbo[2]);
		//echo "<br><br>CONJUGADO: ".$parametroverbo[2]." ".$verboconjugado;
		$texto->Cambiar($i, $verboconjugado);
	}
}

//Lo cargo de nuevo si no no toma los cambios
$matriz=$texto->Listo();



$texto->Mostrar();


echo "<br><br><br><br>TEXTO TERMINADO<br><br>";

$listo=$texto->TextoListo();

echo $listo;


?>