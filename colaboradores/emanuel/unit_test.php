<?php
include ("obtiene_deff.php");

$palabras = array("perro", "auto", "abogado", "mesa", "silla");

foreach($palabras as $e){
	print_r("palabra:" . $e . "\n");
    print_r(TakeDeff($e));
}



?>