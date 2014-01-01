<?php
//Controla la velocidad del proxy, y actualiza en la base
include('/var/www/vhosts/sergio/sergio-1.no-ip.info/httpdocs/publicadorphp/funcionesobtiene.php');

$query_abajo= "SELECT * FROM Proxys WHERE Habilitado ORDER BY FechaUltimo ASC LIMIT 5";  // levanto 1 y lo controlo
mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('ControlaProxy1La consulta fallo: ' . mysql_error());

while ($site = mysql_fetch_assoc($abajonota)) {
	$IdProxy = $site['IdProxy'];
	$Proxyss = $site['Proxy'];
	$CantidadPruebas = $site['CantidadPruebas'];
	$TiempoRespuesta = $site['TiempoRespuesta'];

	$list = explode(":", $Proxyss);
	$ip = $list[0];
	$port = (int)$list[1];
	//echo $ip.":".$port."\n";
	$seg = checkProxy($ip, $port);
	//echo $seg."\n";

	$Habilitado=1;
	if ($seg==999999999 || $seg>999999){
		$CantidadPruebas=$CantidadPruebas+1;
	}

	if ($CantidadPruebas>0){ //si tiene 1 o mas pruebas negativas lo deshabilito al proxy
		$Habilitado=0;
	}
	$query_paginas = "UPDATE Proxys SET FechaUltimo=NOW(),CantidadPruebas=$CantidadPruebas,TiempoRespuesta=$seg, Habilitado=$Habilitado WHERE IdProxy=$IdProxy";
	//echo $query_paginas;
	$resultado=mysql_query($query_paginas,$GLOBALS['publica']) or die('ActProxy7La consulta fallo: ' . mysql_error());

}
mysql_free_result($abajonota);

?>