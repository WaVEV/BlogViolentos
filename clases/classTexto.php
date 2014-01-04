<?php
class Texto{

	/*Clase que representa el texto completo*/

	public $texto;                     //String texto original
	public $titulo;					   //Titulo del texto
	public $listaParrafos = array();   //Lista de los parrafos del texto. Esta lista contiene los objeto Parrafo.
	public $cantidadParrafos = 0;     //Cantidad total de parrafos
	
	function __construct($file) {

		/*Constructor de clase*/
		$dom = file_get_html($file);
		
		foreach ($dom->find('h1') as $tit) {
			$this->titulo = $tit->innertext;			
		}

	    $this->texto = $dom;
    }

	public function SepararParrafos(){
		/*Funcion que divide en parrafos el texto*/
		$html = $this->texto;
		$cont = 0;
		foreach ($html->find("div") as $e) {
			if ( trim($e) ) {
				$cont++;
				//Instanciamos un objeto Parrafo para cada parrafo
				$parrafo = new Parrafo($e->innertext, $this->titulo, $cont);
				$this->listaParrafos[] = $parrafo;
			}			
		}
			
		//Seteamos cantidad de parrafos encontrados
		$this->cantidadParrafos = $cont;
	}

	public function VerInfoTexto(){

		/*Funcion de debug:
		Muestra la info almacenada en el objeto*/

		echo "<strong>Texto</strong><br>".$this->texto."<br>";
		echo "<strong>Titulo</strong><br>".$this->titulo."<br>";
		echo "<strong>fuente</strong><br>".$this->fuente."<br>";
		echo "<strong>parrafos</strong><br>".$this->cantidadParrafos."<br>";
	}
	

}
?>
