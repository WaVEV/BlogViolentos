<?php

include('classTexto.php');

$texto = '<p class="capital">A lo largo del siglo XVII la orden jesuita creó, a la sombra de un muy lento y paulatino crecimiento urbano de Córdoba, una serie de unidades productivas con las cuales sostener las actividades de los establecimientos urbanos de la Compañía de Jesús.</p> <p>La producción y el avituallamiento del Colegio Máximo, del antiguo noviciado y del Real Colegio Convictorio de la ciudad, fueron posibles gracias al ingente esfuerzo de un importante número de seres humanos esclavizados que, de manera coercitiva, fueron sacados de su entorno natural para ser comercializados bajo el amparo legal de la corona española. </p> <p>En las estancias jesuíticas, las prácticas rituales propias de toda orden religiosa se combinaban con una serie de actividades productivas y con la aplicación de ciertos mecanismos de control y sanción sobre los esclavos. </p> <p>Es sobre dichas actividades, sobre el rol que jugaron los negros esclavizados y sobre el régimen disciplinario impuesto, que se compartirá una jornada de esparcimiento y aprendizaje acompañados por expertos en la temática y en un intercambio de ideas, que reconoce cierta diversidad de criterios pero que tiene como eje común conocer la raíz africana de sus orígenes.</p> <p>Además, como el tema de la negritud es complejo de comunicar, durante el encuentro se propondrán diversas estrategias de sensibilización para que la visita sea vivenciada también desde lo sensorial, en un recorrido turístico cultural denominado “El sistema productivo y reproductivo en las estancias jesuíticas: los esclavizados como sustento económico”.</p> <p><img src="http://staticf5a.lavozdelinterior.com.ar/sites/default/files/styles/landscape_565_318/public/nota_periodistica/sierras-10.jpg" alt="" title="La ranchería, lugar donde vivían los esclavos." width="565" height="318" class="image-landscape_565_318" /></p> <p class="SubTitulo">Proyecto internacional</p> <p>Como proyecto internacional de la Unesco, la Ruta del Esclavo nació en 1994 a propuesta de Haití. Se trata de un programa intersectorial y multidisciplinario, que apunta a romper el silencio sobre la esclavitud, poner de manifiesto las transformaciones sociales que se produjeron y la interacción cultural que generó la trata de esclavos, con el fin de contribuir a la cultura de la paz y la coexistencia pacífica entre los pueblos.</p> <p>Con el objetivo de contribuir a esta iniciativa, desde la perspectiva local se formó en 2010 el grupo Córdoba Ruta del Esclavo, que está integrado por representantes de instituciones públicas, académicas, investigadores, sociedades civiles y organizaciones interesadas en la planificación y ejecución del proyecto. </p> <p>El grupo en su conjunto tiene la decisión de trabajar en el reconocimiento de la presencia y el aporte de los argentinos de origen afro a la diversidad cultural. </p> <p>En esa tarea de sensibilización trabajan especialmente en el aporte al patrimonio inmaterial de los que una vez fueron esclavizados y cuyos descendientes hoy son importante parte de nuestra compleja sociedad moderna.</p> <p class="SubTitulo">Itinerario</p> <p> 8.30, partida desde ex Plaza Vélez Sársfield (por calle Montevideo); 10, arribo a la estancia jesuítica Santa Catalina, recorrido acompañados por el licenciado en historia Carlos Crouzeilles; 12.30, llegada a la estancia jesuítica Colonia Caroya, degustación de productos regionales de Colonia Caroya (no es almuerzo), participación de la escritora e investigadora uruguaya Graciela Leguizamón, recorrido por la estancia; 15, arribo a la estancia jesuítica Jesús María, recorrido junto a la licenciada Belén Domínguez y actividad a cargo de Mariano Giosa, merienda a la canasta, y 18, regreso a Córdoba, al punto de partida.</p> <p><strong>Cuándo.</strong> Sábado 14 de diciembre. </p> <p><strong>Salida:</strong> ex plaza Vélez Sársfield (por calle Montevideo) a las 8.30.</p> <p><strong>Costo:</strong> $ 100 (debe ser abonado al momento de reservar).</p> <p><strong>Inscripciones y confirmación de reserva:</strong> Museo de Antropología, Hipólito Yrigoyen 174, Nueva Córdoba, desde el lunes 2 al viernes 6, de 9 a 17. Cupos limitados. </p> <p><strong>Contactos:</strong> teléfonos (0351) 155-503752 y 155-399595. </p> <p><strong>E-mail:</strong> <a href="mailto:rutadelesclavocba@gmail.com">rutadelesclavocba@gmail.com</a> </p> <p><strong>En Internet:</strong> <a href="http://rutadelesclavocba.wordpress.com ">http://rutadelesclavocba.wordpress.com </a></p> ';

$pru = new Texto($texto,1,"Prueba");

$pru->SepararParrafos();

//$pru->ShowMetadata();

foreach ($pru->parrafosList as $e) {
	//$e->VerMetadata();
}

echo "Chequear oraciones<br>";
echo "-------------------------------------------<br>";

foreach ($pru->parrafosList as $e) {
	$e->SepararOraciones();
	foreach ($e->listaOraciones as $ee) {
		$ee->VerInfoOracion();
	}
}

?>