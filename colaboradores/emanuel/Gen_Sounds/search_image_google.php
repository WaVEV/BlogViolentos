<?php
include ("../funcionesobtiene.php");

function load($url){
    sleep(2);
    $r = new HttpRequest($url);
    $ret = $r->Retorno;
    unset($r);
    return $ret;
}

function search_in_google_image($titulo, $imags = 5){

    $titulo = urlencode($titulo);
    $next = "https://www.google.com.ar/search?q=$titulo&source=lnms&tbm=isch";
    $cnt = 0;
    $result = array();

    while($cnt < $imags){

        $html = load($next);
        $n = preg_match_all('/<a[^>]* href="([^"]+)"[^>]* class=rg_l/i', $html, $mat);
        $next = "https://www.google.com.ar/search?q=teclado+knucker&tbm=isch&ijn=1&start=$n&csl=1";

        foreach($mat[1] as $e){
            $e = str_ireplace("&amp;", "&", $e);
            $l = load("$e");
            preg_match_all('/<meta[^>]* itemprop="image"[^>]* content="([^"]+)"/i', $l, $m);
            $result[] = $m[1][0];
            $cnt ++;
            if($cnt >= $imags) break;
        }
        
    }

    return $result;
}



?>