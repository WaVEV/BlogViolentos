<?

function nGramas($string,$n)
{   /*Dado un string y un entero n, devuelve un array con todos los 
      n-gramas*/
	
	//$path = "/var/www/vhosts/marcelo/apdb2.no-ip.info/httpdocs/blogsviolentos/listos/";
	$command = "python ngrams_nltk.py '$string' $n";
	$result = exec($command);

	$a = explode("##",$result);
	$ngramas = array();
	$i = 0;
	foreach ($a as $e) {
		if ( $i )
			$ngramas[$i-1] = $e;
		$i++;
	}
	
	return $ngramas;			
}

?>



