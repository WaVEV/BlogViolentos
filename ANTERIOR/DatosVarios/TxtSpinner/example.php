<?php
$content="WordPress has a mechanism for saving, updating, and retrieving individual, named pieces of data (options) in the WordPress database. Option values can be 
strings, arrays, or PHP objects (they will be serialized, or converted to a string, before storage, and unserialized when retrieved). Option names are strings, and 
they must be unique, so that they do not conflict with either WordPress or other Plugins.";


$spinContent="WordPress has a {mechanism|mechanistic|medal|medallion|meddle|meddler|meddling|media|medial|median|} for {saving|saving clause|savings|savings and 
loan association|savior|Savitar|savoir faire|savoir-faire|savor|savorless|} updating, and retrieving 
{individual|individualism|individualist|individualistic|individuality|individualize|individually|indivisible|indocile|indocility|} 
named pieces of data (options) in the WordPress database. Option values can be strings, arrays, or PHP objects (they will be serialized, or converted to a string, before 
storage, and unserialized when retrieved). Option names are strings, and they must be unique, so that they do not conflict with either WordPress or other Plugins.";


require("PhpTextSpinner.inc.php");
$phpTextSpinner=new PhpTextSpinner();

//$spinContent=$phpTextSpinner->spinContent($content);
$newContent=$phpTextSpinner->runTextSpinner($spinContent);

echo "<div><h3>Original Content</h3>$content</div>";
echo "<div><h3>Content After TextSpin</h3>$newContent</div>";
?>