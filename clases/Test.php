<?php

include('classTexto.php');

$texto = '<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>1</title>
</head>
<body>
<h1>Facebook lanza el botón “no me gusta”, pero sólo para el messenger</h1>
<div>El esperado botón “no me gusta” podría estar abriéndose paso en Facebook.</div>
<div>A pesar de que aún no se ofrece como opción para opinar en los status, la red social incluyó un botón de “dislike” en el nuevo paquete de stickers para el messenger.</div>
<div>Si bien los stickers para el servicio de mensajería fueron presentados en abril, este nuevo paquete incluye no solamente el dislike, sino también la clásica mano de Facebook mostrando diversas señas como un puño cerrado, un like brillante, un “toque”, un ramo de flores o un corazón.</div>
<div>Este paquete de “manos” puede ser descargado de la Sticker Store, a la cual se accede mediante elsmiley dentro del chat, ya en la versión móvil como en la de navegador.</div>
</body>
</html>';

$pru = new Texto($texto);

$pru->SepararParrafos();

//$pru->ShowMetadata();

foreach ($pru->listaParrafos as $e) {
	//$e->VerMetadata();
}

echo "Chequear oraciones<br>";
echo "-------------------------------------------<br>";

foreach ($pru->listaParrafos as $e) {
	$e->SepararOraciones();
	foreach ($e->listaOraciones as $ee) {
		$ee->VerInfoOracion();
	}
}

?>