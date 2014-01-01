<?php
include ("../funcionesobtiene.php");
include ("../listos/definiciones_wordreference.php");
include ("../listos/sinonimos_wordreference.php");
include ("../listos/sintaxis_nltk.php");

class Word
{
	public $id;//(Int)El id de la palabra en la base de datos
	public $palabra = "";//(String)La palabra propiamente dicha
	public $tipo = "";//(String) "ADJ"=adj, "COM"=sust. comun, "IND"=indeterminado
	public $reemp_1 = "";//Reemplazo principal
	public $reemp_2 = "";//Reemplazo secundario
	public $reemp_3 = "";//Reemplazo terciario
	public $reemplazos = array();//Array de posibles reemplazos
	
	//Setea el array reemplazos si la palabra es un sustantivo
	public function parse_definicion ()
	{
		if ( $this->tipo != "COM" ) {
			echo "No se asignan definiciones a sust. propios o adjetivos";
			return;
		}
		
		$ar = array();
		$def = BuscaDefinicion($this->palabra);
		$cont = 0;
		foreach ($def as $e) {
			if ( $cont >= 10 ) break;
			preg_match_all("/.*([A-Z].*[\.])/",$e,$m);
			$aux = preg_split("/( |,|:)/",trim($m[1][0]));

			$r = ""; $c = 0; //$r guarda exact. 2 palabras de más de 3 chars
			foreach ($aux as $pal) {
				if ( strlen($pal) > 3 ) $c++;
				$r = $r." ".$pal;
				if ( $c == 2 ) break;
			}
			$ar[$cont] = $r;
			$cont++;
		}
		$this->reemplazos = $ar;
	}
	
	//Setea el array reemplazos si al palabra es un sinónimo
	public function parse_sinonimos ()
	{
		if ( $this->tipo != "ADJ" ) {
			echo "No se asignan sinonimos a sustantivos";
		}
		
		$this->reemplazos = BuscaSinonimo($this->palabra);
	}		
	
	//Clasifica una palabra en ADJ, PRO o COM
	public function determinar_tipo ()
	{
		$a = AnalizadorPalabra($this->palabra);
		$t = "IND";
		if ( preg_match("/\/JJ/",$a) ) $t = "ADJ";
		else if ( preg_match("/\/NN[^P]/",$a) ) $t = "COM";
		$this->tipo = $t;
	}
	
	public function determinar_tipo_contexto ($contexto) { //$s es el string oracion de contexto
		$s = $this->palabra;
		$a = AnalizadorPalabra($contexto);
		$t = "IND";
		if ( preg_match("/$s\/JJ/",$a) ) $t = "ADJ";
		else if ( preg_match("/$s\/NN[^P]/",$a) ) $t = "COM";
		return $t;
	}
	
}
