<?php

require_once "funcionesobtiene.php";

function TakeDeff($word){
    $a = new HttpRequest("http://www.wordreference.com/definicion/" . urlencode($word));
    $html = $a->Retorno;
    unset($a);
    $rest = array();
    $l = explode("<ol class='entry'>", $html);
    foreach(array_slice($l, 1) as $e){
        $li = explode("</ol>", $e);
        $rest = array_merge($rest, array_map('strip_tags', array_slice(explode("<li>", $li[0]), 1)));
    }
    $r = array();
    foreach($rest as $e){
        if((stripos($e, "m. ") !== false) || (stripos($e, "f. ") !== false))
            $r[] = $e;
    }
    return $r;
}
?>