<?php
include('funcionesobtiene.php');


$base="http://blog.visiting.net/";
$articulo='obras-de-teatro-en-carlos-paz-anticipo-temporada-2013/';
$URLcompleta=$base.$articulo;


// SCRIPT ---------------------------------------------------------------------------------------------------


$fecha='';
$titulo='';
$texto='';

$html=file_get_html($URLcompleta);


// BASE
foreach ($html->find('div[id=singlePost]') as $e) {
	
	// TITULO
	foreach ($e->find('h1') as $ee) {
		$titulo=$ee->innertext;
	}
	
	// TEXTO
	foreach ($e->find('p') as $ee) {
		if(strpos($ee->innertext,'<script')!==false){
			break;
		}else{
			if(strpos($ee,'text-align:')===false){
				$texto.=$ee->innertext.'<br>';
			}
		}
	}
	
	// FECHA
	foreach ($e->find('div[class=meta]') as $ee) {
		$fecha=$ee->innertext;
	}
	$fecha=substr($fecha,0,strpos($fecha,'por'));
	$aux=explode(' ',$fecha);
	$mes=$aux[2];
	$dia=$aux[3];
	$ano=$aux[4];
	
	if($mes=='oct'){	$mes=10;	}
	
	$dia=str_replace(',','',$dia);
	
	$dia=trim($dia);
	$mes=trim($mes);
	$ano=trim($ano);
	
	$fecha=$ano.'-'.$mes.'-'.$dia;
	
}



 
// FIN SCRIPT ---------------------------------------------------------------------------------------------------


echo '<b style="color:red">Fecha:</b>'.$fecha.'<br>';
echo '<b style="color:red">Titulo:</b>'.$titulo.'<br>';
echo '<b style="color:red">Texto:</b>'.$texto.'<br>';



//Limpio la memoria
$html->clear(); 
unset($html);

// FIN DE TODO ---------------------------------------------------------------------------------------------------	
?>