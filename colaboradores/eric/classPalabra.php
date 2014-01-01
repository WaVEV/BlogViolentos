<?php
include ("../funcionesobtiene.php");
include ("../listos/definiciones_wordreference.php");
include ("../listos/sinonimos_wordreference.php");
include ("../listos/sintaxis_nltk.php");

class Palabra
{
	public $id;//(Int)El id de la palabra en la base de datos
	public $palabra = "";//(String)La palabra propiamente dicha
	public $tipo = "";//(String) "ADJ"=adj, "COM"=sust. comun, "IND"=indeterminado
	public $reemp1 = "";//Reemplazo principal
	public $reemp2 = "";//Reemplazo secundario
	public $reemp3 = "";//Reemplazo terciario
	public $reemplazos = array();//Array de posibles reemplazos
	
	function __construct($string) {
		/*Constructor de clase*/

		$this->palabra = $string;
	}
	
	//Clasifica palabra ({ADJ|COM|IND}) teniendo en cuenta el contexto
	//Si no hay contexto, la clasifica igual
	public function clasificaContexto ($contexto) {
		$s = $this->palabra;
		if ( $contexto == "") {
			$a = AnalizadorPalabra($this->palabra);
			$t = "IND";
			if ( preg_match("/\/JJ/",$a) ) $t = "ADJ";
			else if ( preg_match("/\/NN[^P]/",$a) ) $t = "COM";
			return $t;
		}
		else {
			$a = AnalizadorPalabra($contexto);
			$t = "IND";
			if ( preg_match("/$s\/JJ/",$a) ) $t = "ADJ";
			else if ( preg_match("/$s\/NN[^P]/",$a) ) $t = "COM";
			return $t;
		}
	}
	
	public function seteaTipo ($contexto) {
		$this->tipo = $this->clasificaContexto($contexto);
	}
	
	//Setea el array reemplazos si la palabra es un sustantivo
	public function parseoDefinicion ()
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
	public function parseoSinonimos ()
	{
		if ( $this->tipo != "ADJ" ) {
			echo "No se asignan sinonimos a sustantivos";
		}
		
		$this->reemplazos = BuscaSinonimo($this->palabra);
	}
	
	public function autoReemp () {
		//Reemplazamos sust. com por su definicion
		if ( $this->tipo == "COM" )
			$this->parseDefinicion();
		//Reemplazamos adj. por sinónimos
		else if ( $this->tipo == "ADJ" )
			$this->parseSinonimos();
		//Si no, no hacemos nada			
		else return;
		if ( count($this->reemplazos) >= 3 ) $this->reemp3 = $this->reemplazos[2];
		if ( count($this->reemplazos) >= 2 ) $this->reemp2 = $this->reemplazos[1];
		if ( count($this->reemplazos) >= 1 ) $this->reemp1 = $this->reemplazos[0];	
	}
	
	public function VerInfoOracion(){
		/*Funcion de debug:
		Muestra la info almacenada en el objeto*/

		echo $this->id."<br>";

		echo $this->palabra."<br>";

		echo $this->tipo."<br>";

		echo $this->reemp1."<br>";
		
		echo $this->reemp2."<br>";
		
		echo $this->reemp3."<br>";

		echo "----------------------------------<br><br>";

	}
}
