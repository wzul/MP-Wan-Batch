<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);
set_time_limit(0);

$filename = $argv[1]; //'terbaru.csv'
$api_key = $argv[2]; //'4e49de80-1670-4606-84f8-2f1d33a38670'
$mode = $argv[3]; //'production'

if (empty($filename) || empty($api_key) || empty($mode)) {
    exit('Insufficient argument');
}

if ($mode == 'production' || $mode == 'sandbox') {
    // ok
} else {
    exit('Invalid mode ' . $mode);
}

$filecsv = file_get_contents($filename);

if (!$filecsv) {
    exit('Failed to read file contents');
}

$array = array_map("str_getcsv", explode("\n", $filecsv));
