<?php
/*
* Created by Kostyantyn Kyyashko
* k.konstantin.n@gmail.com
*/
$php = file_get_contents('../commands/functions.php');
preg_match_all('/function\s{0,5}[^\(]+\s{0,5}\(/i', $php, $z);
echo '<pre>';
var_dump($z[0]);
