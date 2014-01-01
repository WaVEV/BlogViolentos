<?php
include ("../funcionesobtiene.php");
include ("../listos/definiciones_wordreference.php");
include ("../listos/sinonimos_wordreference.php");
include ("../listos/sintaxis_nltk.php");

define("PUNTO_ESPECIAL",    "###");         //string para escapar el caracter punto.
define("SEPARADORES_HTML", '<p><br><div>'); //Separadores de parrafos usuales

//Definicion de clases y funciones auxiliares
class Palabra
{
	public $id;//(Int)El id de la palabra en la base de datos
	public $palabra = "";//(String)La palabra propiamente dicha
	public $tipo = "";//(String) "ADJ"=adj, "COM"=sust. comun, "IND"=indeterminado
	public $reemp1 = "";//Reemplazo principal
	public $reemp2 = "";//Reemplazo secundario
	public $reemp3 = "";//Reemplazo terciario
	public $reemplazos = array();//Array de posibles reemplazos
	
	function __construct($string)  
	{	/*Constructor de clase*/

		$this->palabra = $string;
	}
	
	public function ClasificaContexto ($contexto)
	{   /*Clasifica palabra ({ADJ|COM|IND}) teniendo en cuenta el contexto
		 *Si no hay contexto, la clasifica igual*/
		
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
	
	public function SeteaTipo ($contexto)
	{   /*Setea el atributo tipo*/
		$this->tipo = $this->ClasificaContexto($contexto);
	}
	
	public function ParseoDefinicion ()
	{   /*Setea el array reemplazos si la palabra es un sustantivo*/
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
	
	public function ParseoSinonimos ()
	{   /*Setea el array reemplazos si al palabra es un sinónimo*/
		if ( $this->tipo != "ADJ" ) {
			echo "No se asignan sinonimos a sustantivos";
		}
		
		$this->reemplazos = BuscaSinonimo($this->palabra);
	}
	
	public function AutoReemp ()
	{   /*Buscamos los reemplazos para una palabra automáticamente*/
		//Reemplazamos sust. com por su definicion
		if ( $this->tipo == "COM" )
			$this->ParseoDefinicion();
		foreach ($this->reemplazos as $k) {
			echo "----------".$k.".....aaaaasdddddsdasdasdasdasdasd........<br>";
		}
		//Reemplazamos adj. por sinónimos
		else if ( $this->tipo == "ADJ" )
			$this->ParseoSinonimos();
		//Si no, no hacemos nada			
		else return;
		if ( count($this->reemplazos) >= 3 ) $this->reemp3 = $this->reemplazos[2];
		if ( count($this->reemplazos) >= 2 ) $this->reemp2 = $this->reemplazos[1];
		if ( count($this->reemplazos) >= 1 ) $this->reemp1 = $this->reemplazos[0];	
	}
	
	public function VerInfoPalabra()
	{
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
