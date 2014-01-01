<?php

require_once 'googleTTSphp.class.php';
require_once "mp3file_class.php";
require_once "search_image_google.php";
define('mp3_dir', "/var/www/vhosts/marcelo/apdb2.no-ip.info/httpdocs/blogsviolentos/videos/");
//define('mp3_dir', "mp3_tts/");


/* devuelve una lista de archivos ordenados de los distintos fragmentos del texto*/
function default_image($instancia){
    return mp3_dir . "Viamge_" . "$instancia" . ".jpg";
}

function GetMp3($text, $lang="es"){
    $ds = new GoogleTTS;
    $ds->setStorageFolder(mp3_dir);
    $ds->setLang($lang);
    $ds->setInput($text);
    $ds->downloadMP3();
    $a = $ds->getMP3s();
    unset($ds);
    return $a; // holy shit!
}

function GetVideo($title, $text, $imags = 4 ,$lang="es"){

    assert($imags  < 50 ) or die;
    $sounds = GetMp3($text, $lang);
    $cmd = "cat ";
    foreach($sounds as $e)
        $cmd .= "$e ";
    $audio = mp3_dir . "salida" . rand() . ".mp3";
    $cmd .= "> $audio";
    exec($cmd, $output);
    $m = new mp3file($audio);
    $a = $m->get_metadata();
    /* calcula la longitud de todas las pistas */
    unset($m);
    $sz = $a['Length'];

    /* busca en google las imagenes */
    $photos_url = search_in_google_image($title, $imags);
    $imags = sizeof($photos_url);
    $output_file = mp3_dir . "salida.log";
    $i = 0;
    $photos = array();
    $w = -1; $h = -1;
    foreach($photos_url as $e){
        $img = default_image($i<10 ? "0$i" : $i);
        $photos[$i++] = $img;
        exec("wget " . $e . " --output-document " . $img);
        if($i == 1){
            $info = getimagesize($img);
            $w = $info[0];
            $h = $info[1];
        }
        else{
            $thumb = imagecreatetruecolor($w, $h);
            list($ow, $oh) = getimagesize($img);
            $source = imagecreatefromjpeg($img);
            imagecopyresized($thumb, $source, 0, 0, 0, 0, $w, $h, $ow, $oh);
            imagejpeg($thumb, $img);
        }
    }
    /* repite las imagenes cuantas veces sea necesario */
    $i = 0;
    foreach ($photos as $k => $v) {
        $imgdata = file_get_contents($v);
        $ni = ($k<10 ? "0$k" : $k);
        $one_more = $i < $sz % $imags ? 1 : 0;
        for($j = 0; $j < ($sz / $imags) + $one_more - 2; $j++){
            $img = default_image("${ni}_$j");
            /* con la variable j no hace falta hacerla de dos cifras pues no importa que se cuele el 11 antes que el 2 porque siempre es la misma imagen */
            $f = fopen($img, "w");
            fwrite($f, $imgdata);
            fclose($f);
        }
        $i++;
    }
    /* junta todo */
    $video = mp3_dir . "video" . rand() . ".avi";
    echo "mencoder \"mf://" . mp3_dir . "*.jpg\" -mf fps=1 -ovc lavc -audiofile $audio -oac mp3lame -lameopts preset=medium -o $video\n";
    exec("mencoder \"mf://" . mp3_dir . "*.jpg\" -mf fps=1 -ovc lavc -audiofile $audio -oac mp3lame -lameopts preset=medium -o $video");
    /* devuelve el archivo avi del video y burra lo anterior para no generar basura */
    exec("rm " . mp3_dir . "*.jpg");
    exec("rm " . mp3_dir . "*.mp3");
    /* limpio la basura */
    return $video;
}


?>