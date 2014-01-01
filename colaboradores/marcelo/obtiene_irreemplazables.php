<?php

require_once "../funcionesobtiene.php";

foreach (range('a', 'z') as $letra) {

    $html = file_get_html("http://www.internetglosario.com/letra-" . urlencode($letra).".html");

    foreach ($html->find('h4') as $e) {
        $txt = $e->innertext;
        $nomprod = strtolower(strip_tags($txt));
        //Cargo los datos de la palabra
        $query_Email = "INSERT Irreemplazables SET Palabra='$nomprod', Nicho='Informatica'";
        //echo "<br />query_Email:".$query_Email;
        $resultado=mysql_query($query_Email,$GLOBALS['publica']) or die('3La consulta fallo: ' . mysql_error());
    }
    $html->clear(); 
    unset($html);

} 
?>