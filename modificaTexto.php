<?php
include ("listos/funcionesobtiene.php");
include ("listos/definiciones_wordreference.php");
include ("listos/obtiene_deff_wordmagicsoft.php"); 
include ("listos/sinonimos_wordreference.php");
include ("listos/sintaxis_nltk.php");
include ("clases/classTexto.php");
include ("clases/classParrafo.php");
include ("clases/classOracion.php");
include ("clases/classFragmento.php");
include ("clases/classPalabra.php");


$fp = fopen("m1.html","w");
fwrite($fp,"<html>\n<head>\n");
fwrite($fp,'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">');
fwrite($fp,"\n<title>Modificado 1</title>\n</head>\n<body>\n");

//Armo la data para instanciar un objeto de la clase Texto
$texto = new Texto("1.html");
$texto->SepararParrafos();

foreach ($texto->listaParrafos as $parrafo) {
	$parrafo->SepararOraciones();
	fwrite($fp,"<div>\n");
	foreach ($parrafo->listaOraciones as $oracion) {
		$oracion->SepararPalabras();
		$s = $oracion->oracion;
		echo "<br>".$s."<br>";
		foreach ($oracion->listaPalabras as $palabra) {
			$palabra->AutoReemp();
			$p = $palabra->palabra;
			$s = str_ireplace($p,$palabra->reemp1,$s);
		}
		echo "<br>".$s."<br>";
		fwrite($fp,$s);
	}
	fwrite($fp,"\n</div>\n");
}

fwrite($fp,"</body>\n</html>");
echo "ok";

?>
