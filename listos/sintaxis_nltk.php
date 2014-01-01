<?

//llena los datos de las palabras, para despues poder procesarlos
function AnalizadorPalabra($palabra){

	//busco una palabra, y analizo el resultado
	$command="python /var/www/vhosts/marcelo/apdb2.no-ip.info/httpdocs/blogsviolentos/listos/sintaxis_nltk.py '$palabra'";
	$analisis=exec($command);
	
	return $analisis;		
}


?>



