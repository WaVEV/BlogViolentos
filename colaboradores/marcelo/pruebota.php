<?php
include ("../funcionesobtiene.php");
include ("../listos/definiciones_wordreference.php");

$palabras = array("botón");

foreach($palabras as $e){
	print_r("palabra:" . $e . "\n");
    print_r(BuscaDefinicion($e));
}

//NADADA fdbdfbd lolololo lalla popop

?>
