<?php

function BuscaDefinicion($word){
    $a = new HttpRequest("http://www.wordreference.com/definicion/" . urlencode($word));
    //echo "========================================================<br>";
    //echo "PEDIDO:"."http://www.wordreference.com/definicion/" . urlencode($word);
    $html = $a->Retorno;
    unset($a);
    $rest = array();
    $l = explode("<ol class='entry'>", $html);
    foreach(array_slice($l, 1) as $e){
        $li = explode("</ol>", $e);
        $rest = array_merge($rest, array_map('strip_tags', array_slice(explode("<li>", $li[0]), 1)));
    }

    //echo "***********************************************************************************************************<br>";
    //print_r($rest);    
    return $rest;
}
?>
