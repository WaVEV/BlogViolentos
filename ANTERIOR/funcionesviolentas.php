<?


// Le paso el texto a dividir
function Dividir($string){
	$Dividido=array();
	if(strlen($string)>24){
		$x=explode(' ',$string);
		$aux='';
		foreach($x as $palabra){
			$aux.=$palabra.' ';
			if(strlen($aux)>40){
				$Dividido[0][]=$aux;
				$aux='';
			}else{ // Para la ultima oracion
				$Dividido[0][]=$aux;
				$aux='';
			}
		}
	}else{
		$Dividido[0][]=$string;
	}
	return $Dividido;
}


// Le pongo estilo a las palabras claves
function PalabrasClaves($string){
	
	$consulta=mysql_query("SELECT * FROM PalabrasClaves");
	while($mostrar=mysql_fetch_array($consulta)){
	
		if($mostrar['tipo']==1){
			$tag1='<b>';
			$tag2='</b>';
		}elseif($mostrar['tipo']==2){
			$tag1='<u>';
			$tag2='</u>';
		}elseif($mostrar['tipo']==3){
			$tag1='<i>';
			$tag2='</i>';
		}else{
			$tag1=$tag2='';
		}
		
		$string=str_replace($mostrar["palabra"], $tag1.$mostrar['palabra'].$tag2, $string);
		
		return $string;
	}
	
}


?>