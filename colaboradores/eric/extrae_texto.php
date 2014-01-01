<?php
include('simple_html_dom.php');

$arch = 0;
for ($id = 0; $id <= 8; $id++) {
	echo "\n<br>$id<br>\n";
	$urlgeneral = "http://www.lavoz.com.ar/tecnologia?page=".$id."&rc=1#ancla-paginador-notas";
	$html = file_get_html($urlgeneral);
	foreach ($html->find('article[class=teaser  clearfix]') as $art) {
		foreach ($art->find('div[class=contenido]') as $content) {
			foreach ($content->find('a') as $link) {
				$arch++;
				$fp = fopen("$arch.html","a");
				fwrite($fp,"<html>\n<head>\n");
				fwrite($fp,'<meta http-equiv="Content-Type" content="text/html; charset=utf-8">');
				fwrite($fp,"\n<title>$arch</title>\n</head>\n<body>\n");
				$url = "http://www.lavoz.com.ar".$link->href;
				echo "\n<br>$arch.- ".$url;
				$pag = file_get_html($url);
				foreach ($pag->find('header[class=Main]') as $e) {
					foreach ($e->find('h1') as $ee) {
						$titulo = strip_tags($ee->innertext);
						break(2);
					}
				}
				
				fwrite($fp,"<h1>$titulo</h1>\n");
				
				foreach ($pag->find('div[class=field-item even]') as $e) {
					foreach ($e->find('p') as $ee) {
						$txt = trim(strip_tags($ee->innertext));
						fwrite($fp,"<div>$txt</div>\n");
					}
					foreach ($e->find('div') as $ee) {
						$txt = strip_tags($ee->innertext);
						fwrite($fp,"<div>$txt</div>\n");
					}
					break;
				}
				fwrite($fp,"</body>\n</html>");
				fclose($fp);
				
				$pag->clear(); 
				unset($pag);
				break(2);
			}
		}
	}
	$html->clear(); 
	unset($html);
}

?>
