<?php
$string = '{{The|A}} {{quick|speedy|fast}} {{brown|black|red}} {{fox|wolf}} {{jumped|bounded|hopped|skipped}} over the {{lazy|tired}} {{dog|hound}}';

echo '<p>';

for($i = 1; $i <= 5; $i++)
{
    echo Spinner::detect($string, false).'<br />';
    // or Spinner::flat($string, false).'<br />';
}

echo '</p>';
?>