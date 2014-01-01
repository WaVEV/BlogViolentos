<?

// Buscamos sinonimos (le pasamos la palabra)
function BuscaSinonimo($txt){
	$urlss="http://www.sinonimos.org/".$txt;
	$html2=file_get_html($urlss);
	foreach ($html2->find('div[align=left]') as $e) {
		foreach ($e->find('b') as $ee) {
			$txt=$ee->innertext;
			$sinonimoss[] = $txt;
		}
	}

	//Limpio la memoria
	$html2->clear(); 
	unset($html2);
    return $sinonimoss;
}


?>



