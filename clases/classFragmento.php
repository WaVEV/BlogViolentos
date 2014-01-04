<?php

define("MARCA_INICIO", "%%##"); //Marca de inicio de string irremplazable
define("MARCA_FIN", "##%%");  //Marca de fin de string irremplazable

define("SEPARADOR_MARCAS", "$$$$"); //Marca que indica que en esa posicion va un string irremplazable

define("LISTO", 1);  // estado que representa que se termino el proceso de reemplazo.

define("FALTA_REEMPLAZO", 2); //estado que representa que hay strings para reemplazar aun.



class Fragmento {
	/*Clase que representa  parte de una oracion*/

	public $fragmento;
	public $estado;
	public $noMarcados = array();

	function __construct($string) {
		/*Constructor de clase*/

		$this->fragmento = $string;
		$this->estado = 0;

	}

	public function SetearEstado(){
		/*Deternina el estado del fragmento creado*/
		
		$aux = $this->fragmento;

		$aux = preg_replace('/##([^#]*)##/', "", $aux);
		
		if(!$aux){
			$this->estado = LISTO;
		}
		else {
			$this->estado = FALTA_REEMPLAZO;	
		}

	}

	public function Reemplazar($viejoString,$nuevoString){
		/*Funcion para reemplazar un substring en el atributo fragmento*/

		if($this->estado == FALTA_REEMPLAZO){	
		
			$match = "/".$viejoString."/";
			
			$reemplazo = MARCA_INICIO.$nuevoString.MARCA_FIN;
			
			$this->fragmento = preg_replace($match, $reemplazo, $this->fragmento);
		
		}
		else {
			echo "error, no se puede aplicar Reemplazar a un fragmento listo!!\n";
		}
	}
	

	public function ObtenerNoMarcados(){
		/*Obtiene los substrings que restan por reemplazar*/

		$aux = preg_replace('/%%##([^#]*)##%%/', "$$$$", $this->fragmento);


		$list = explode(SEPARADOR_MARCAS, $aux);

		foreach ($list as $key => $value) {
			if(!$value){
				
			}else {$this->noMarcados[] = $value;}
		}
	}

	public function LimpiarArrayMarcados(){
		/*Limpia el arreglo noMarcados*/
		
		unset($this->noMarcados);
		$this->noMarcados = array();

	}


	public function Marcar($string){
		/*Marca un substring*/

		$aMarcar = "/".$string."/";
		$marca = MARCA_INICIO.$string.MARCA_FIN;
		$this->fragmento = preg_replace($aMarcar, $marca, $this->fragmento);
	
	}

	public function LimpiarMarcas(){
		$this->fragmento = str_replace(MARCA_FIN, "", $this->fragmento);
		$this->fragmento = str_replace(MARCA_INICIO, "", $this->fragmento);
	}

	public function VerInfoFragmento(){
		/*Funcion de debug:
		Muestra la info almacenada en el objeto*/

		echo "<strong>fragmento</strong><br>".$this->fragmento."<br>";

		echo "<strong>estado</strong><br>".$this->estado."<br>";

		echo "<strong>noMarcados</strong><br>";

		foreach ($this->noMarcados as $key => $value) {
			echo "<strong>".$key."</strong><br>".$value."<br>";

		}

		echo "----------------------------------<br><br>";
	}
}
?>


