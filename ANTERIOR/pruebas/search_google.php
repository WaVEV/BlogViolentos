<?php

function search_google(/*string*/ $tema,/*int*/ $nurls){
	$query = $tema;
	$npages = $nurls/10; //pues son 10 por pagina
	$start = 0;
	if($npages >= 100) $npages=100;//no mas de 100 paginas sino explota (?) a si google no te deja
	$gg_url = 'http://www.google.com/search?hl=en&q=' . urlencode($query) . '&start=';
	$i = 0;
	$size = 0;
	$rst = array();
	$options = array(
	CURLOPT_RETURNTRANSFER => true, // return web page
	CURLOPT_HEADER => false, // don't return headers
	CURLOPT_FOLLOWLOCATION => true, // follow redirects
	CURLOPT_ENCODING => "", // handle all encodings
	CURLOPT_AUTOREFERER => true, // set referer on redirect
	CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
	CURLOPT_TIMEOUT => 120, // timeout on response
	CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
	CURLOPT_COOKIEFILE    => "cookie.txt",
	CURLOPT_COOKIEJAR    => "cookie.txt",
	CURLOPT_USERAGENT    => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3",
	CURLOPT_REFERER        => "http://www.google.com/",
	);
	for ($page = $start; $page < $npages; $page++){
	  $ch = curl_init($gg_url.$page.'0');
	  curl_setopt_array($ch,$options);
	  $scraped="";
	  $scraped.=curl_exec($ch);
	  curl_close( $ch );
	  $results = array();
	  preg_match_all('/a href="([^"]+)" class=l.+?>.+?<\/a>/',$scraped,$results);
	  foreach ($results[1] as $url){
	    $i++;
	    $rst[$i] = $url;
	    //echo "<a href='$url'>$url</a> ";
	  }
	  $size+=strlen($scraped);
	}
	fclose($ch);
	//echo "Number of results: $i-1 Total KB read: ".($size/1024.0);
	
	return $rst;
}
?>
