<?php
include ("obtiene_deff_wordmagicsoft.php");

$palabras = array("perro", "auto", "abogado", "mesa", "silla");

foreach($palabras as $e){
    print_r("palabra:" . $e . "\n");
    print_r(TakeDeff($e));
    echo "sinonimo \n";
    print_r(TakeSyn($e));
}



?>