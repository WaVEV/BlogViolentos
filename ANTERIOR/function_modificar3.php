<?php

include('clases.php');

$texto=new Palabras();
$texto->Almacenar('poner',1,'sustantivo','singular','masculino',null,null);

$matriz=$texto->Listo();

print_r($matriz);



echo '<br><br>';



$texto->Cambiar(0,'apoyar');

$matriz2=$texto->Listo();

print_r($matriz2);

?>
