<?php

echo "123";
var_dump(ob_get_level());
echo "<hr>";
ob_start();
echo "Hello World";

$out = ob_get_clean();
$out = strtolower($out);

var_dump(ob_get_level());
var_dump($out);