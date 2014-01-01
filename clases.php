<?php

//Clase para las palabras y textos
class Palabras{
	private $matriz=array();
	private $palabra;
	private $publicar;
	private $tipo;
	private $estado;
	private $genero;
	private $tiempo_verbal;
	private $nueva;
	
	private $cont=0;
	
	public function Limpiar($string){
		// Las que no sirven
		$string=str_replace('<br/>','<br>',$string);
		$string=str_replace('<br />','<br>',$string);
		$string=str_replace('</li>','<br>',$string);
		$string=str_replace('</p>','<br>',$string);
		$string=str_replace('<p>','<br>',$string);
		$string=str_replace('</ul>','<br>',$string);
		
		// Las que son claves
		$string=str_replace('<b>','<clave>',$string);
		$string=str_replace('</b>','</clave>',$string);
		$string=str_replace('<strong>','<clave>',$string);
		$string=str_replace('</strong>','</clave>',$string);
		$string=str_replace('<i>','<clave>',$string);
		$string=str_replace('</i>','</clave>',$string);
		$string=str_replace('<u>','<clave>',$string);
		$string=str_replace('</u>','</clave>',$string);
		$string=str_replace('<em>','<clave>',$string);
		$string=str_replace('</em>','</clave>',$string);
		
		$string=strip_tags($string,'<br><clave>');
		
		return $string;
	}
	
	public function Almacenar($palabra, $publicar, $tipo, $estado, $genero, $tiempo_verbal,$nueva){
		$this->palabra=$palabra;
		$this->publicar=$publicar;
		$this->tipo=$tipo;
		$this->estado=$estado;
		$this->genero=$genero;
		$this->tiempo_verbal=$tiempo_verbal;
		$this->nueva=$nueva;
		
		$i=$this->cont;
		
		$this->matriz[$i]['palabra']=$this->palabra;
		$this->matriz[$i]['publicar']=$this->publicar;
		$this->matriz[$i]['tipo']=$this->tipo;
		$this->matriz[$i]['estado']=$this->estado;
		$this->matriz[$i]['genero']=$this->genero;
		$this->matriz[$i]['tiempo_verbal']=$this->tiempo_verbal;
		$this->matriz[$i]['nueva']=$this->nueva;
		
		$this->cont++;
	}
	
	public function Cambiar($id,$nueva){
		$this->matriz[$id]['nueva']=$nueva;
	}
	
	public function Listo(){
		return $this->matriz;
	}
	
	public function TotalRegistros(){
		return $this->cont;
	}
	
	public function EsLetra($string){
		if(preg_match("/[A-Za-z]/",$string)){
			return true;
		}else{
			return false;
		}
	}
	
	public function Preposicion($string){
		if(
		$string=='a' || 
		$string=='ante' || 
		$string=='bajo' || 
		$string=='cabe' || 
		$string=='con' || 
		$string=='contra' || 
		$string=='de' || 
		$string=='desde' || 
		$string=='durante' || 
		$string=='en' || 
		$string=='entre' || 
		$string=='hacia' || 
		$string=='hasta' || 
		$string=='mediante' || 
		$string=='para' || 
		$string=='por' || 
		$string=='según' || 
		$string=='segun' || 
		$string=='sin' || 
		$string=='so' || 
		$string=='sobre' || 
		$string=='tras' || 
		$string=='versus' || 
		$string=='vía' || 
		$string=='via'
		){
			return true;
		}else{
			return false;
		}
	}
	
	public function ParalabrasClavesBD($TextoOriginal){
	
		function SoloUnaClave($string){
			$string=str_replace('<clave><clave>','<clave>',$string);
			if(strpos($string,'<clave><clave>')!==false){
				SoloUnaClave($string);
			}else{
				return $string;
			}
		}
	
		$encontradas='';
		$bd=new MySQL();
		$query = "SELECT * FROM PalabrasClaves";
		$consulta = $bd->Consulta($query);
		$Hay = $bd->num_rows($consulta);
		if($Hay){ // si hay sigo
			while($row = $bd->fetch_array($consulta)){
				$TextoOriginal=str_replace($row['palabra'],'<clave>'.$row['palabra'].'</clave>',$TextoOriginal);
			}
		}
		
		//$TextoOriginal=SoloUnaClave($TextoOriginal);
		
		$dom = new simple_html_dom();
		$dom->load($TextoOriginal);
		
		foreach($dom->find('clave') as $e){
			if(strpos($encontradas,$e->innertext)===false){
				$encontradas.=$e->innertext.' ';
			}
		}
		return $encontradas;
	}
	
	public function MarcarPalabrasClaves($encontradas){
		// Busco las palabras claves
		if( !empty($encontradas) ){
			$x=explode(' ',$encontradas);
			foreach($x as $elem){
				for($i=0; $i<count($this->matriz); $i++){
					if( strpos($this->matriz[$i]['palabra'],$elem)!==false ){ // Si el elemento se encuentra en la matriz, entonces meto 0 al publicar
						if( $this->matriz[$i]['tipo']!='Preposicion' and $this->matriz[$i]['tipo']!='Articulo' and $this->matriz[$i]['tipo']!='Adverbio' and $this->matriz[$i]['tipo']!='Contraccion' ){ // Pero antes de poner 0 al publicar me fijo que tipo de palabra es
							$this->matriz[$i]['publicar']=0;
						}
					}
				}
			}
		}
		
		// Busco las comillas y marco lo que esta adentro
		$primera=0;
		$ultima=0;
		$total=count($this->matriz);
		for($i=0; $i<$total; $i++){
			if($this->matriz[$i]['palabra']=='"'){
				if($inicio==0){
					$inicio=$i;
				}else{
					$ultima=$i;
				}
			}
			if($ultima!=0 and $inicio!=0){
				$diferencia=$ultima-$inicio;
				if($diferencia>0){
					for($z=$inicio; $z<=$ultima; $z++){
						$this->matriz[$z]['publicar']=0;
					}
				}
				$inicio=$ultima=0;
			}
		}
		
	}
	
	function Mostrar(){
		foreach($this->matriz as $columnas){
			echo '<table border="1" width="500">';
			echo '<tr>';
			foreach($columnas as $k => $elem){
				echo '<td>'.$k.'</td>';
			}
			echo '</tr>';
			break;
		}
		foreach($this->matriz as $columnas){
			echo '<tr>';
			foreach($columnas as $k => $elem){
				echo '<td>'.$elem.'</td>';
			}
			echo '</tr>';
		}
		echo '</table>';
	}
	
	function ChequearNumeros($string,$caracter){
		if(!empty($string) and !empty($caracter)){
			$veces = substr_count($string,$caracter);
			$posiciones = array();
			for ($num = 0; $num < $veces; $num++) {
				$posiciones[] = strpos($string,$caracter,end($posiciones) + 1);
			}
			
			foreach($posiciones as $p){
				if( is_numeric($string[$p-1]) and is_numeric($string[$p+1]) ){
					$string[$p]='';
					/*$lugar=$p;
					$insertar='***SACAR***';
					$string = substr_replace($string, $insertar, $lugar, 0);*/
				}
			}
		}
		return $string;
	}
	
	function Parrafos($string){
		
		function ContarPalabras($string){
			$x=explode(' ',$string);
			return count($x);
		}
		
		$parrafo=array();
		$aux=array();
		$i=0; // posicion del array
		$cont=1; // contador de palabras
		$coma=0;
		
		// Si el texto tiene un <br> entonces lo divido
		if(strpos($string,'<br>')!==false){
			$x=explode('<br>',$string);
			foreach($x as $elem){
				if($elem){
					$parrafo[]=$elem;
				}
			}
		}else{ // Si no hay <br> entonces el array tiene 1 solo indice con todo el texto
			$parrafo[]=$string;
		}
		
		// El segundo paso es analizar el array y dividirlo por "."
		foreach($parrafo as $elem){
			if($elem){ // si el parrafo tiene contenido..
				if(ContarPalabras($elem)>19){ // Si el parrafo tiene mas de 20 palabras lo tengo que dividir
					if(strpos($elem,'.')!==false){ // Si el parrafo tiene un "." lo divido
					
						$elem=$this->ChequearNumeros($elem,'.');
					
						$x=explode('.',$elem);
						foreach($x as $e){
							if(strpos($e,'.')!==false){
								$aux[]=$e;
							}else{
								$aux[]=$e.'. ';
							}
						}
					}else{ // Si el parrafo no tiene punto lo guardo tal cual
						$aux[]=$elem;
					}
				}else{ // Si es menor a 20 palabras lo guardo tal cual
					$aux[]=$elem;
				}
			}
		}
		
		// El tercer paso es analizar el array y dividirlo por ","
		// Ahora todos los parrafos estan en $aux, asi que $parrafo lo reinicio
		$parrafo=array();
		foreach($aux as $elem){
			if($elem){ // si el parrafo tiene contenido..
				if(ContarPalabras($elem)>19){ // Si el parrafo tiene mas de 20 palabras lo tengo que dividir
					// Puede pasar que una coma esté entre comillas y si divido el parrafo por comas hacemos moco, entonces primero me fijo si hay comillas
					if(strpos($elem,'"')!==false){
						// Si hay comillas entonces el punto de division es la comilla
						$x=explode('"',$elem);
						foreach($x as $k => $e){
							if( ($k%2)!=0 ){ // esto quiere decir que es impar
								$parrafo[]=' "'.$e.'" ';
							}else{
								$parrafo[]=$e;
							}
						}
					}elseif(strpos($elem,',')!==false){ // Si el parrafo no tiene comillas pero si coma, divido
						
						$elem=$this->ChequearNumeros($elem,',');
						
						$x=explode(',',$elem);
						foreach($x as $e){
							if(strpos($e,'.')!==false){ // si hay un punto entonces no es necesario colocar una coma
								$parrafo[]=$e;
							}else{
								$parrafo[]=$e.', ';
							}
						}
					}else{ // Si el parrafo no tiene punto lo guardo tal cual
						$parrafo[]=$elem;
					}
				}else{ // Si es menor a 20 palabras lo guardo tal cual
					$parrafo[]=$elem;
				}
			}
		}
		
		
		if(count($parrafo)>1){
			// Recorro el array y trato de concatenar parrafos cortos
			$primera=$segunda=0;
			for($i=0; $i<count($parrafo); $i++){
				if($parrafo[$i]){
					$primera=ContarPalabras($parrafo[$i]);
					$segunda=ContarPalabras($parrafo[$i+1]);
					if( ($primera+$segunda)<=20 ){
						$parrafo[$i].=$parrafo[$i+1];
						$parrafo[$i+1]='';
					}
				}
			}
		}
		
		$aux=array();
		foreach($parrafo as $elem){
			$elem=chop($elem);
			if(strlen($elem)>1){
				$elem=str_replace('. .','.',$elem);
				// Puede pasar que un parrafo tenga mas de 20 palabras y ninguna coma, punto, <br> o comillas.
				// Entonces, primero calculo la cantidad de palabras
				$total=ContarPalabras($elem);
				if($total<20){
					$aux[]=$elem;
				}else{ // Si supera las 20 palabras, tomo la cantidad que tiene y la divido por 20
					$x=explode(' ',$elem); // Divido el parrafo en palabras
					$cant=0;
					$nuevo=true;
					$pos=count($aux); // Tomo la posicion para el $aux (asi voy concatenando)
					foreach($x as $e){ // Recorro palabra por palabra
						// Por cada parrafo tomo 15 palabras nomas, asi tengo un margen de error chico
						if($cant<=15){
							$cant++;
						}else{
							if($cant>15 and !$this->Preposicion($e)){
								$cant++;
							}else{
								$cant=0;
								$pos++;
							}
						}
						$aux[$pos].=$e.' ';
					}
				}
			}
		}

		return $aux;
	}
	
	public function TextoListo(){
		$listo='';
		$noespacio=false;
		$total=count($this->matriz);
		
		for($i=0; $i<$total; $i++){
		
			$palabra=$this->matriz[$i]['palabra'];
			$nueva=$this->matriz[$i]['nueva'];
			$publicar=$this->matriz[$i]['publicar'];
		
			// Si publicar es 1 y el campo "nueva" no esta vacia, entonces muestro nueva
			if($publicar==1 and !empty($nueva)){
				$listo.=' '.$nueva;
			
			}else{ // Sino, muestro la comun
				// Si es un solo caracter y no es letra, va sin espacio
				if(strlen($palabra)==1 and !$this->EsLetra($palabra)){
					$listo.=$palabra;
				}
				
			}
		}
		
		$listo=str_replace(',',', ',$listo);
		$listo=str_replace('.','. ',$listo);
		
		return trim($listo);
		
	}
	
	
	
}

?>