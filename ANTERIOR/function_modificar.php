<?php
include('/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/funcionesobtiene.php');
$DirectorioListos="/var/www/vhosts/marcelo/apdb1.no-ip.info/httpdocs/blogsviolentos/listos/";


function Arreglar($string){
	include('caractereschotines.php');
	$string=str_ireplace($search2,$replace2,$string);
	$string=str_replace('á','a',$string);
	$string=str_replace('é','e',$string);
	$string=str_replace('í','i',$string);
	$string=str_replace('ó','o',$string);
	$string=str_replace('ú','u',$string);
	$string=str_replace("'",'"',$string);
	//$string=str_replace('','',$string);
	return $string;
}



/*
Conecto a la base de datos para tomar las palabras importantes
*/


mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']);

// Busco una nota cualquiera
$query = "SELECT * FROM Notas";
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
		echo $row['palabra'];
		$todas_juntas=$row['palabra'];
	}else{
		echo ', '.$row['palabra'];
		$todas_juntas.=','.$row['palabra'];
	}
	$i++;
}




echo '<br><br>';



$TextoArreglado=Arreglar($TextoOriginal);


// exploro las palabras
$x=explode(',',$todas_juntas);
foreach($x as $elem){
	$TextoArreglado=str_replace($elem,'<b>'.$elem.'</b>',$TextoArreglado);
}

echo $TextoArreglado;







?>