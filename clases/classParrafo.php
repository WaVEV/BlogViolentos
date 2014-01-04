<?php
define("PUNTO_ESPECIAL",    "###");         //string para escapar el caracter punto.

class Parrafo{

	/*Clase que representa un parrafo del texto*/	
	
	public $parrafo;         //String que contiene el parrafo
	public $idTitulo="";     //Id que identifica de que texto proviene
	public $numParrafo=0; //Puesto que ocupa el parrafo en el texto. Ej: 1 indica que es el primer parrafo
	public $listaOraciones = array();
	public $cantOraciones = 0;
	//Constructor
	function __construct($string,$id,$num) {
	      
	      /*Constructor de clase*/

	      $this->parrafo = $string;
	      $this->idTitulo = $id;
	      $this->numParrafo = $num;

	}

	public function VerInfoParrafo(){

		/*Funcion para debug:
		Muestra la info almacenada en el objeto*/

		echo "<strong>Id Titulo</strong><br>".$this->idTitulo."<br>";

		echo "<strong>Numero de parrafo</strong><br>".$this->numParrafo."<br>";

		echo "<strong>Parrafo</strong><br>".$this->parrafo."<br>";

		echo "----------------------------------<br><br>";

	}
	private function LimpiaPuntos() { 

		//Duplicamos los ";". Esto es para que la expresion regular que divide las oraciones
		//funcione correctamente


		$this->parrafo = str_replace(";", ";;", $this->parrafo);

		$this->parrafo = str_replace("&nbsp;", "", $this->parrafo);
		//Limpieza de puntos en numeros
	    preg_match_all('/\d+([\.,]\d+)*/', $this->parrafo, $match);
		
		foreach ($match[0] as $res) {
			$aux = str_replace('.', PUNTO_ESPECIAL, $res);

			$this->parrafo = str_replace($res, $aux, $this->parrafo);
		}

		//Limpieza de puntos en abreviaciones de nombres. Ej Homer J. Simpson
		preg_match_all('/\s[A-Z]\./', $this->parrafo, $match);
		
		foreach ($match[0] as $res) {
			$aux = str_replace('.', PUNTO_ESPECIAL, $res);

			$this->parrafo = str_replace($res, $aux, $this->parrafo);
		}

		//Limpieza de puntos en abreviaciones que no esten seguidos de un nombre propio
		preg_match_all('/([a-zA-Z]*)\.(\s*),?(\s*)[a-z]/', $this->parrafo, $match);
		
		foreach ($match[0] as $res) {
			$aux = str_replace('.', PUNTO_ESPECIAL, $res);

			$this->parrafo = str_replace($res, $aux, $this->parrafo);
		} 
	}

	public function SepararOraciones(){
		
		$this->limpiaPuntos();
		

		if(preg_match_all('/\[[^\?]+\]([\)\.]*)|\( ([^\.^\)]*)\)\.|¿[^\?]+\?([\)\.]*)|¡[^!]+!([\)\.]*)|[a-zA-Z0-9;][^\;^\.]+\;|;[^\.]*\.|[A-Z0-9\(][^\.]+\./', $this->parrafo, $match)){
		
			$cont = 0;

			foreach ($match[0] as $elem) {
				$cont++;

				$nuevaOracion= str_replace("###", ".", $elem);
				//$nuevaOracion= str_replace("&nbsp;", "", $nuevaOracion);
				
				if(preg_match_all('/;(.+)/',$nuevaOracion,$newMatch)){
					$nuevaOracion = substr($nuevaOracion, 1);
				}

				$oracion = new Oracion($nuevaOracion,$this->idTitulo,$cont,$this->numParrafo);

				$this->listaOraciones[] = $oracion;
			}

			$this->cantOraciones = $cont;

		}
		//Si un parrafo no tiene oracion puede ser un caracter raro, o un subtitulo.
		//Creamos una oración por las dudas
		else{
			$nuevaOracion= str_replace("###", ".", $this->parrafo);
				//$nuevaOracion= str_replace("&nbsp;", "", $nuevaOracion);
				
			if(preg_match_all('/;(.+)/',$nuevaOracion,$newMatch)){
				$nuevaOracion = substr($nuevaOracion, 1);
			}
			
			$oracion = new Oracion($nuevaOracion,$this->idTitulo,1,$this->numParrafo);
			$this->listaOraciones[] = $oracion;
		}
	}

}
?>
