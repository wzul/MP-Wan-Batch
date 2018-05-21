<?php

require 'vendor/autoload.php';

use Billplz\API;
use Billplz\Connect;

ini_set('display_errors', 'On');
error_reporting(E_ALL);
set_time_limit(0);

echo 'Make sure you set to UTF-8 character encoding on the file! '.PHP_EOL;
echo 'The expected arrangement is: Bank Code, Bank Account Number, ID Number, Bank Account Holder Name, Total, Description, Email, ID, Status' . PHP_EOL;
echo PHP_EOL;

$filename = $argv[1];
$api_key = $argv[2];

$filecsv = file_get_contents($filename);
$array = array_map("str_getcsv", explode("\n", $filecsv));

$bank_account = array(array());

$index_1 = 0;
$index_2 = 0;

foreach ($array as $data) {
    if ($data[0] === 'Bank Code') {
        continue;
    }

    if (empty($data[0])) {
        break;
    }

    if ($index_2 === 9) {
        $index_2 = 0;
        $index_1++;
    }

    $bank_account[$index_1][$index_2] = $data[1];
    $index_2++;
}
$connnect = (new Connect($api_key))->detectMode();
$billplz = new API($connnect);

$response = array();

foreach ($bank_account as $bank) {
    $response[] = $billplz->getBankAccountIndex(array('account_numbers'=>$bank));
}

echo print_r($response, true);
