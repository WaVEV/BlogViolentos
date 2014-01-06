<?php
function TakeDeff($word)
{
	$url = str_replace('+' , '%20' ,  urlencode($word));
    $html = file_get_contents("http://www.wordmagicsoft.com/diccionario/es-en/" . $url . ".php");
    preg_match_all('/explanation\'[^>]*>[^<>]*Definir significado de[^<>]*<\/span>([^<>]*)/i', $html, $mat);
    return array_map('trim', $mat[1]);
}

function TakeSyn($word){
	$url = str_replace('+' , '%20' ,  urlencode($word));
    $html = file_get_contents("http://www.wordmagicsoft.com/diccionario/es-en/" . $url . ".php");
    preg_match_all('/explanation\'[^>]*>[^<>]*Sin[^n]{1,4}nimos[^<>:]*: *(<\/span>)?([^<>]*)/i', preg_replace("/<\/?a[^>]*>/i", "", $html), $mat) ||
    preg_match_all('/Sin[^n]{1,4}nimos[^<>:]*: *(<\/span>)?([^<>;]*)/i', preg_replace("/<\/?a[^>]*>/i", "", $html), $mat);
    return array_map(function($a){return explode(",", $a);}, $mat[2]);
}
?>