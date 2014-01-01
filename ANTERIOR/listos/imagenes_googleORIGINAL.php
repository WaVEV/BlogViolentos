<?php

$query = $palabrasimportantes.' -"'.$NombreFuente.'"'; //la busqueda es por las palbras y que no busque en la pagina original, si no no tiene chiste
echo $query;
$npages = 2; //pues son 10 por pagina

$start = 0;
if($npages >= 10)
	$npages=10;//no mas de 100 paginas sino explota (?) a si google no te deja
//$gg_url = 'http://images.google.com/searchbyimage?hl=en&image_url=' . urlencode($query) . '&start=';
$gg_url = 'https://www.google.com.ar/search?num=10&hl=es&site=imghp&tbm=isch&source=hp&q=' . urlencode($query) . '&oq=' . urlencode($query) . '';
//echo "<br>gg_url=".$gg_url;
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
CURLOPT_COOKIEFILE    => "cache/cookiegoogleurlimg.txt",
CURLOPT_COOKIEJAR    => "cache/cookiegoogleurlimg.txt",
CURLOPT_USERAGENT    => "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3",
CURLOPT_REFERER        => "http://www.google.com/",
);
for ($page = $start; $page < $npages; $page++){
  //$ch = curl_init($gg_url.$page.'0');
  $ch = curl_init($gg_url);
  curl_setopt_array($ch,$options);
  $scraped="";
  $scraped.=curl_exec($ch);
  curl_close( $ch );
  echo "<br>scraped=".$scraped;
  $results = array();

  preg_match_all('/<a.*?href=["\'](.*?)["\']/s',$scraped,$results);

  //preg_match_all('/<img.*?src=["\'](.*?)["\']/s',$scraped,$results);
  foreach ($results[1] as $url){
    $i++;
    if(strpos($url,'imgurl=')!==false){
    	$url=str_replace('http://www.google.com.ar/imgres?imgurl=','',$url);
    	$url=substr($url,0,strpos($url,'imgrefurl'));
    	$url=str_replace('&amp;','',$url);
    	$url=str_replace('&;','',$url);
    	$rst[] = $url;
    }
    //echo "<br>URL=".$url;
  }
  $size+=strlen($scraped);
}
//fclose($fp);

$imagenesnota=$rst;

//print_r($imagenesnota)

?>
