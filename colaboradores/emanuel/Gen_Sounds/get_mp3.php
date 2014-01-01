<?php 

include 'googleTTSphp.class.php';
define('mp3_dir', "/var/www/vhosts/marcelo/apdb2.no-ip.info/httpdocs/blogsviolentos/videos/");
//define('mp3_dir', "videos/");

function GetMp3($text, $lang="es"){
    $ds = new GoogleTTSHTML;
    $ds->setStorageFolder(mp3_dir);
    $ds->setLang($lang);
    $ds->setAutoPlay(true);
    $ds->setInput($text);
    $ds->downloadMP3();
    $a = $ds->getMP3s(); // holy shit!
    unset($ds);
    return $a; // holy shit!
}


?>