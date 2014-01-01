<?
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');

//busco un texto, y analizo el resultado
$TituloOriginal="Obra de teatro de adultos mayores Los Sin Verguenza";
$NombreFuente="vgb.gov.ar";


$palabrasimportantes=trim(strtolower(strip_tags($TituloOriginal)));
$palabrasimportantes=str_ireplace($TextoIrrelevante," ",$palabrasimportantes);  //saco palabras irrelevantes


//include que llama al script de las imagenes y lo corre de una, devuelve $imagenesnota
include($DirectorioListos."imagenes_google.php");


//esto devuelve $imagenesnota un array de 1 a 5 de url imagenes
//Las modifico y piso para no tener quilombo con el reconocedor de google..
$iii = 0;
$nombreimagen=explode(" ", $palabrasimportantes);
//foreach ($imagenesnota as $imagenurl) {
//	if ($imagenurl<>""){
//		$iii = $iii +1;
		echo '<br>'.$imagenesnota[0];
		$ImgURL[0]=modificoimagenes($nombreimagen[0],$imagenesnota[0]);
		echo '<br>'.$ImgURL[0];
//	}
//}



?>



