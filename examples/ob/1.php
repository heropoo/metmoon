<?php
error_reporting(E_ALL);
ini_set('Display_errors', 'On');

if (ob_get_level() == 0) ob_start();

for ($i = 0; $i<10; $i++){

    echo "<br> Line to show.";

    //ob_flush();
    //flush();
    sleep(2);
}

echo "Done.";

ob_end_flush();