<?php

$query = $palabrasimportantes.' -"'.$NombreFuente.'"'; //la busqueda es por las palbras y que no busque en la pagina original, si no no tiene chiste
echo $query;


$manual_referer = 'http://google.com/';

// See reference for how to modify search
// http://code.google.com/apis/ajaxsearch/documentation/reference.html
$args = array(
    'v' => '1.0',
    'q' => $query,
    'as_filetype' => 'jpg',
    'imgsz' => 'all', // image size
    'safe' => 'active', // image "safeness"
    'as_filetype' => 'jpg',
);
$url = "http://ajax.googleapis.com/ajax/services/search/images?";
foreach ($args as $key => $val) {
    $url .= $key . '=' . rawurlencode($val) . '&';
}
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_REFERER, $manual_referer);
$body = curl_exec($ch);
curl_close($ch);

$json = json_decode($body, true);
$results = $json['responseData']['results'];
$iii=0;
foreach ($results as $result) {
    $imagenesnota[$iii]=$result['url'];
    $iii=$iii+1;
}

//print_r($imagenesnota)

?>
