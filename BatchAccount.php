<?php

require 'vendor/autoload.php';

use Billplz\API;
use Billplz\Connect;

ini_set('display_errors', 'On');
error_reporting(E_ALL);
set_time_limit(0);

echo 'Make sure you set to UTF-8 character encoding on the file! '.PHP_EOL;
echo 'The expected arrangement is: Bank Account Number' . PHP_EOL;
echo PHP_EOL;

$filename = $argv[1];
$api_key = $argv[2];

echo 'Reading file contents...'.PHP_EOL.PHP_EOL;

$filecsv = @file_get_contents($filename);

if (!$filecsv) {
    exit('Failed to read file contents');
}

$array = array_map("str_getcsv", explode("\n", $filecsv));

$bank_account = array();

$index_1 = 0;
$index_2 = 0;

foreach ($array as $data) {
    if ($data[0] === 'Bank Code') {
        continue;
    }

    if (empty($data[0])) {
        break;
    }

    $bank_account[] = $data[1];
}

$connnect = (new Connect($api_key))->detectMode();
$billplz = new API($connnect);

$response = array();

echo 'Sending to Billplz API...'. PHP_EOL.PHP_EOL;

foreach ($bank_account as $bank) {
    $response[] = $billplz->toArray($billplz->getBankAccount($bank));
}

echo 'Writing to csv file...'. PHP_EOL.PHP_EOL;

for ($i = 0; $i<sizeof($response); $i++) {
    if ($response[$i][0] === 200 || $response[$i][0] === '200') {
        $array[$i+1][0] = $response[$i][1]['code'];
        $array[$i+1][1] = $response[$i][1]['acc_no'];
        $array[$i+1][2] = $response[$i][1]['id_no'];
        $array[$i+1][3] = $response[$i][1]['name'];
        $array[$i+1][4] = $response[$i][1]['organization'];
        $array[$i+1][5] = $response[$i][1]['authorization_date'];
        $array[$i+1][6] = $response[$i][1]['status'];
        $array[$i+1][7] = $response[$i][1]['processed_at'];
        $array[$i+1][8] = $response[$i][1]['reject_desc'];
    } else {
        $array[$i+1][6] = $response[$i][1]['error']['type'];
    }
}

$array[0][0] = 'Bank Code';
$array[0][1] = 'Account Number';
$array[0][2] = 'Identity Number';
$array[0][3] = 'Name';
$array[0][4] = 'Organization';
$array[0][5] = 'Authorization Date';
$array[0][6] = 'Status';
$array[0][7] = 'Processed At';
$array[0][8] = 'Reject Desc';

echo 'Generating Success Files...'.PHP_EOL;

$fp = fopen('success_'. $filename, 'w');
foreach ($array as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
echo 'Success...'.PHP_EOL.PHP_EOL;
