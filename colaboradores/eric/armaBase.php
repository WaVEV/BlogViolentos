<?php
include "classWord.php";

$cant_pal = 0;
for ($id = 1; $id <= 9; $id++) {
	$filename = "fuentes/".$id.".txt";
	$fp = fopen($filename,"r");
	$texto = fread($fp, filesize($filename));
	fclose($fp);
	$palabras = explode(" ",$texto);
	$cont = 0;
	foreach ($palabras as $w) {
		if ( strlen($w) > 3 ) {
			$a[$w]++;
			$cont++;
		}
	}
	$cant_pal += $cont;
}

asort($a);
$b = array_reverse($a);
echo "<br>Cantidad de palabras: ".$cant_pal."<br>";
echo "Cantidad de palabras sin repetir: ".count($b)."<br>";

//Conecto a base de datos    
$link = mysqli_connect('localhost', 'alamaula', 'tontita', 'blogsviolentos');
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

if (!mysqli_set_charset($link, "utf8")) {
    printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($link));
} else {
    printf("Conjunto de caracteres actual: %s\n", mysqli_character_set_name($link));
}

$cont = 1;
foreach ($b as $key => $value) {
	if ( $cont > 1001 ) break;
	
	$p = new Word();
	$p->palabra = $key;
	$v1 = mysqli_real_escape_string($link, $p->palabra);
	$p->determinar_tipo();
	$v2 = mysqli_real_escape_string($link, $p->tipo);

	if ( $p->tipo == "COM" || $p->tipo == "ADJ" ) {
		echo "$cont \n";
		mysqli_query($link, "INSERT into words (id, palabra, tipo, modificada) VALUES ( '', '$v1', '$v2', '0')");
		$cont++;
	}
}

mysqli_close($link);
?>
