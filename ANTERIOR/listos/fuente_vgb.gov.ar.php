<?php

$URLcompleta="http://".$URLss;


// SCRIPT ---------------------------------------------------------------------------------------------------
$FechaOriginal='';
$TituloOriginal='';
$TextoOriginal='';

$html=file_get_html($URLcompleta);


// FECHA
foreach ($html->find('time[class=post-date]') as $e) {
	$FechaOriginal=$e->datetime;
}

// TITULO
foreach ($html->find('h1[class=post-title]') as $e) {
	$TituloOriginal=$e->innertext;
	$TituloOriginal=strip_tags($TituloOriginal);
}

// TEXTO
foreach ($html->find('div[class=post-content]') as $e) {
	foreach ($e->find('p') as $ee) {
		$TextoOriginal.=$ee->innertext.'<br>';
	}
}



 
// FIN SCRIPT ---------------------------------------------------------------------------------------------------


echo '<b style="color:red">Fecha:</b>'.$FechaOriginal.'<br>';
echo '<b style="color:red">Titulo:</b>'.$TituloOriginal.'<br>';
echo '<b style="color:red">Texto:</b>'.$TextoOriginal.'<br>';



//Limpio la memoria
$html->clear(); 
unset($html);

// FIN DE TODO ---------------------------------------------------------------------------------------------------	
?>