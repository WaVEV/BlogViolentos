<?php
class Oracion{
	public $oracion;    //string que contiene la oracion
	public $titulo;		//texto de donde proviene
	public $numOracion; //numero de oracion del texto
	public $parrafo;    //parrafo del que proviene
	public $listaPalabras = array();

	function __construct($string,$id,$numO,$numP) {
		/*Constructor de clase*/

		$this->oracion = $string;
		$this->titulo = $id;
		$this->numOracion = $numO;
		$this->parrafo = $numP;
	}
	
	public function SepararPalabras ()
	{   /*Filtra los adjetivos y sustantivos comunes de la oracion*/
		 //$analisis = AnalizadorPalabra($this->oracion);
		$analisis = AnalizadorPalabra($this->oracion);
		preg_match_all("/([^\sO\/]+)\/(NN[^P]|JJ*)/",$analisis,$m);
		for ($i = 0; $i < count($m[1]); $i++) {
			$p = new Palabra($m[1][$i]);
			$aux = str_ireplace("/","",$m[2][$i]);
			if ( preg_match("/NN/",$aux) ) $p->tipo = "COM";
			else $p->tipo = "ADJ";
			$this->listaPalabras[] = $p;
		}
	}

	public function VerInfoOracion(){
		/*Funcion de debug:
		Muestra la info almacenada en el objeto*/

		echo "<strong>Oracion</strong><br>".$this->oracion."<br>";

		echo "<strong>Titulo</strong><br>".$this->titulo."<br>";

		echo "<strong>Numero de oracion</strong><br>".$this->numOracion."<br>";

		echo "<strong>Numero de Parrafo</strong><br>".$this->parrafo."<br>";

		echo "----------------------------------<br><br>";

	}

}
?>
