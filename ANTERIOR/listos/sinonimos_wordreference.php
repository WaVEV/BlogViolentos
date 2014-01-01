<?

// Buscamos sinonimos (le pasamos la palabra)
function BuscaSinonimo($txt){
	$txt=str_replace(" ","%20",$txt);
	$urlss="http://www.wordreference.com/sinonimos/".$txt;
	$html2=file_get_html($urlss);
	foreach ($html2->find('div[class=trans clickable]') as $e) {
		$sinonimos=explode('<li>',$e->innertext);
		$txt=strip_tags($sinonimos[1]);
		$sinonimoss[] = $txt;
	}

	//Limpio la memoria
	$html2->clear(); 
	unset($html2);
    return $sinonimoss;
}


?>



