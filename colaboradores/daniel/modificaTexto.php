<?php
include ("../clases/classTexto.php");

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
		//echo "<br>".$s."<br>";
		foreach ($oracion->listaPalabras as $palabra) {
			echo "<br>".$palabra->palabra." - ".$palabra->tipo." <---------------------------mira aca";
			$palabra->AutoReemp();
			$p = $palabra->palabra;
			$s = preg_replace("/$p/",$palabra->reemp1,$oracion->oracion);
			//echo "<br>".$palabra->reemp1;
		}
		echo "<br>".$s."<br>";
		fwrite($fp,$s);
	}
	fwrite($fp,"\n</div>\n");
}

fwrite($fp,"</body>\n</html>");
echo "ok";

?>
