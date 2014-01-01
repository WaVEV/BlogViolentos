<?php
//Trae listados de proxy, desde paginas de listados y los sube a la base
//Controla que no esten repetidos
include('/var/www/vhosts/sergio/sergio-1.no-ip.info/httpdocs/publicadorphp/funcionesobtiene.php');
$DirectorioProxys="/var/www/vhosts/sergio/sergio-1.no-ip.info/httpdocs/publicadorphp/proxys/";

//include de los distintos listados de proxys
include($DirectorioProxys."proxy_samair.php");
include($DirectorioProxys."proxy_ip_adress.php");
include($DirectorioProxys."proxy_xroxy.php");
include($DirectorioProxys."proxy_nntime.php");


$DesdeDonde="";
$Devuelto=proxy_samair();
if(!empty($Devuelto)){
	$DesdeDonde="samair";
	LevantoProxyBase();
}
$Devuelto=proxy_ip_adress();
if(!empty($Devuelto)){
	$DesdeDonde="ip-adress";
	LevantoProxyBase();
}
$Devuelto=proxy_xroxy();
if(!empty($Devuelto)){
	$DesdeDonde="xroxy";
	LevantoProxyBase();
}
$Devuelto=proxy_nntime();
if(!empty($Devuelto)){
	$DesdeDonde="nntime";
	LevantoProxyBase();
}

//print_r($Devuelto);  //Proxys de vuelta



function LevantoProxyBase(){
	global $Devuelto;
	global $DesdeDonde;

	foreach($Devuelto as $Ipss){
		//Armo el select, para ver si ya lo tenia
		$Proxyss = "kkk"; //Si tiene un kkk significa que no lo tengo
		$query_abajo= "SELECT Proxy FROM Proxys WHERE Proxy='".$Ipss."' LIMIT 1";  // Comparo el Ipss con el resto
		mysql_select_db($GLOBALS['database_publica'], $GLOBALS['publica']) or die ("No se puede abrir base datos".mysql_error());
		$abajonota = mysql_query($query_abajo, $GLOBALS['publica']) or die('ControlaProxy1La consulta fallo: ' . mysql_error());
		if ($abajonota) {
			$Proxyss = mysql_result($abajonota,0,"Proxy");
		}
		if ($Proxyss==""){$Proxyss="kkk";}	
		mysql_free_result($abajonota);

		//echo "<br>Proxyss:".$Proxyss;

		$list = explode(":", $Ipss);
		$Proxyy = $list[0];
		$Puertoo = (int)$list[1];		

		if ($Proxyss=="kkk" && ($Puertoo==80 || $Puertoo==8080)){
			$query_Proxy = "INSERT Proxys SET Proxy='$Ipss', DeDonde='$DesdeDonde', FechaUltimo=NOW(), TiempoRespuesta=999999998, CantidadPruebas=0, Habilitado=1";
			//echo "<br />query_Proxy:".$query_Proxy;
			$resultado=mysql_query($query_Proxy,$GLOBALS['publica']) or die('AltaProxy3La consulta fallo: ' . mysql_error());
		}
	}
}


?>

